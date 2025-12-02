<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Classes\ApiResponseClass;
use App\Services\FirebaseService;
use App\Http\Controllers\Controller;
use App\Repositories\RequestRepository;
use App\Repositories\ServiceRepository;
use App\Services\DriverLocationService;
use App\Services\PriceCalculationService;
use App\Repositories\AppSettingRepository;
use App\Repositories\DiscountCodeRepository;
use App\Services\CommissionCalculationService;

class RequestController extends Controller
{
    public function __construct(
        private RequestRepository $requestRepository, 
        private ServiceRepository $serviceRepository,
        private PriceCalculationService $priceCalculationService,
        private DiscountCodeRepository $discountCodeRepository,
        private DriverLocationService $driverLocationService,
        private AppSettingRepository $appSettingRepository,
        private FirebaseService $firebaseService
)
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ip = $request->header('CF-Connecting-IP');
        return $ip;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['required', Rule::exists('services', 'id')],
            'discount_code' => ['nullable', Rule::exists('discount_codes', 'code')],
            'start_latitude' => 'required|numeric',
            'start_longitude' => 'required|numeric',
            'start_address' => 'required|string|max:255',
            'end_latitude' => 'required|numeric',
            'end_longitude' => 'required|numeric',
            'end_address' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:0',
            'payment_method'=>['required',Rule::in(['cash','deposit'])],
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            // إضافة معرف المستخدم المصادق عليه
            $validated['user_id'] = auth('sanctum')->id();
            // حساب السعر الأصلي والنهائي مع تطبيق الخصم إذا وجد
            if(isset($validated['discount_code'])){
                if($coupon=$this->discountCodeRepository->getDiscountCodeByCode($validated['discount_code'])){
                    if(!$this->discountCodeRepository->isCouponActive($coupon)){
                        return ApiResponseClass::sendError('كود الخصم غير نشط.');
                    }
                    if(!$this->discountCodeRepository->hasUsageLimitAvailable($coupon)){
                        return ApiResponseClass::sendError('تم الوصول إلى الحد الأقصى لاستخدام كود الخصم.');
                    }
                    $result=$this->priceCalculationService->calculatePriceWithDiscount($validated['service_id'], $validated['distance_km'],$coupon);
                }
                else{
                    return ApiResponseClass::sendError('كود الخصم غير صالح.');
                }
            }
            else{
                $result=$this->priceCalculationService->calculateBasePrice($validated['service_id'], $validated['distance_km']);
            }
            $validated['original_price']=$result['original_price'];
            $validated['final_price']=$result['final_price'];
            if($result['discount_applied'] && isset($validated['discount_code'])){
                
                $coupon = $this->discountCodeRepository->getDiscountCodeByCode($validated['discount_code']);
                $validated['discount_code_id'] = $coupon->id;
                $validated['discount_amount'] = $result['discount_amount'];
                // زيادة عدد الاستخدامات لكود الخصم
                $this->discountCodeRepository->incrementUsage($coupon);
            }
            else{
                $validated['discount_amount']=0;
            }
            unset($validated['discount_code']);
            // حساب عمولة التطبيق
            $appCommissionAmount = $this->priceCalculationService->calculateCommission($validated['final_price']);
            $validated['app_commission_amount'] = $appCommissionAmount;

            // تخزين الطلب
            $requestModel = $this->requestRepository->store($validated);

            $appSettings = $this->appSettingRepository->getSetting();
            if ($appSettings && $appSettings->auto_assign_to_drivers) {
                // جلب أقرب السائقين الاقرب للزبون            
                $nearestDrivers =$this->driverLocationService->getNearestDrivers($validated['start_latitude'], $validated['start_longitude'], 8,20);
                if ($nearestDrivers === null) {
                    // لا يوجد سائقين متاحين في المنطقة
                }
                $title = 'طلب جديد';
                $body = 'يوجد طلب جديد في منطقتك، اضغط لقبول الطلب';
                $data = [
                    'request_id' => $requestModel->id,
                    'start_latitude' => $validated['start_latitude'],
                    'start_longitude' => $validated['start_longitude'],
                    'start_address' => $validated['start_address'],
                ];
                foreach ($nearestDrivers as $driver) {
                    if (isset($driver['device_token']) && !empty($driver['device_token'])) {
                        $this->firebaseService->sendNotification(
                            $driver['device_token'],
                            $title,
                            $body,
                            $data
                        );
                    }
                }
            }else {
                // النظام اليدوي - إرسال الطلب للداشبورد
            }
            return ApiResponseClass::sendResponse($requestModel, 'Request created successfully.');
        } catch (Exception $e) {
            return apiResponseClass::sendError('Failed to create request.'.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function calculatePrice(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['required', Rule::exists('services', 'id')],
            'distance_km' =>['required','numeric','min:0'],
            'code'=>['nullable','string']
        ]);
        try {
            if(isset($validated['code'])){
                if($coupon=$this->discountCodeRepository->getDiscountCodeByCode($validated['code'])){
                    if(!$this->discountCodeRepository->isCouponActive($coupon)){
                        return ApiResponseClass::sendError('كود الخصم غير نشط.');
                    }
                    if(!$this->discountCodeRepository->hasUsageLimitAvailable($coupon)){
                        return ApiResponseClass::sendError('تم الوصول إلى الحد الأقصى لاستخدام كود الخصم.');
                    }
                    $result=$this->priceCalculationService->calculatePriceWithDiscount($validated['service_id'], $validated['distance_km'],$coupon);
                }
                else{
                    return ApiResponseClass::sendError('كود الخصم غير صالح.');
                }
            }
            else{
                $result=$this->priceCalculationService->calculateBasePrice($validated['service_id'], $validated['distance_km']);
            }
            return ApiResponseClass::sendResponse($result, 'تم حساب السعر بنجاح.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('خطأ في حساب السعر.'.$e->getMessage());
        }
        
    }
}
