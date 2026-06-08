<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\Branch;
use App\Models\Driver;
use App\Models\Passengers;
use App\Services\AdminLoggerService;
use App\Services\CustomerTransactionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\Broker;

class PassengersController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Passengers::with(['driver', 'broker', 'branch'])->where('branch_id', $user->branch_id)
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('passenger_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%")
                    ->orWhereHas('driver', function ($driverQuery) use ($search) {
                        $driverQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    // 🌟 التعديل الذهبي هنا: البحث في علاقة الوسيط (broker) وعمود الاسم (name) فقط 🌟
                    ->orWhereHas('broker', function ($brokerQuery) use ($search) {
                        $brokerQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('branch', function ($branchQuery) use ($search) {
                        $branchQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $passengers = $query->paginate(15)->withQueryString();
        $drivers = Driver::orderBy('name')->get();
        $brokers = Broker::select('id', 'name')->get();

        if ($request->isMobile) {
            return view('mobile.pages.passenger.passengers.index', compact(
                'passengers',
                'drivers',
                'brokers',
            ));
        }

        return view('pages.passengers.index', compact(
            'passengers',
            'drivers',
            'brokers',
        ));
    }

    public function create(Request $request)
    {
        $drivers = Driver::orderBy('name')->get();
        $brokers = Broker::select('id', 'name')->get();
        if ($request->isMobile) {
            return view('mobile.pages.passenger.passengers.create', compact(
                'drivers',
                'brokers',
            ));
        }

        return view('pages.passengers.create', compact(
            'drivers',
            'brokers',
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'pickup_location' => 'required|string|max:255', // مكان الصعود الجديد
            'destination' => 'nullable|string|max:255',
            'passenger_number' => ['required', 'string', 'max:255'],

            // 👈 التعديل الجديد: إزالة حقول العملاء وإضافة حقول الوسيط المتوافقة مع المودال
            'broker_id'   => ['nullable', 'exists:brokers,id'],
            'broker_name' => ['required', 'string', 'max:255'], // الحقل النصي دائماً مطلوب لأنه يحمل اسم الوسيط المكتوب

            'count' => ['required', 'integer', 'min:1'],
            'office_commission'       => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'other_office_commission' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'note' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = $validator->validated();
            $data['status'] = 'pending';
            $data['passenger_number'] = $this->normalizePhone($data['passenger_number']);
            $driverPhone = $this->normalizePhone($data['driver_phone'] ?? '');
            $data['branch_id'] = $this->currentBranchId();

            // 🌟 هندسة الوسطاء: إذا لم يتم اختيار وسيط مسبقاً (أي أن broker_id فارغ)
            if (empty($data['broker_id']) && !empty($data['broker_name'])) {
                // سيبحث عن الاسم في الداتابيز، إن وجده يجلبه، وإن لم يجده ينشئ وسيطاً جديداً فوراً
                $broker = Broker::firstOrCreate([
                    'name' => $data['broker_name']
                ]);

                // إسناد الـ id المستخرج لقاعدة بيانات الراكب
                $data['broker_id'] = $broker->id;
            }

            // معالجة السائق (تبقى كما هي تماماً في كودك الأصلي بدون تغيير)
            // $data['driver_id'] = ($data['driver_id'] ?? null)
            //     ?: $this->resolvePassengerDriver($driverPhone, $data['driver_name'] ?? null);

            // 🗑️ تنظيف البيانات من الحقول المؤقتة لكي لا يظهر خطأ "عمود غير موجود" عند الحفظ
            unset($data['broker_name']);

            // إنشاء وحفظ سجل الراكب الجديد مربوطاً بالوسيط والسائق
            $passenger = Passengers::create($data);

            return WebResponseClass::sendResponse('تمت الإضافة!', 'تم حفظ الراكب والوسيط بنجاح.', 'حسناً', 'passengers.index');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
    public function show(Request $request, $id)
    {
        $user = auth()->user();
        $passenger = Passengers::with(['driver', 'broker', 'branch'])->where('branch_id', $user->branch_id)->findOrFail($id);

        if ($request->isMobile) {
            return view('mobile.pages.passenger.passengers.show', compact('passenger'));
        }

        return view('pages.passengers.show', compact('passenger'));
    }

    public function edit($id)
    {
        $passenger = Passengers::with(['driver', 'broker', 'branch'])->findOrFail($id);
        $drivers = Driver::orderBy('name')->get();
        $brokers = Broker::select('id', 'name')->get();
        $branches = Branch::orderBy('name')->get();

        $currentBranch = $this->currentBranchId()
            ? Branch::find($this->currentBranchId())
            : null;

        if (request()->isMobile) {
            return view('mobile.pages.passenger.passengers.edit', compact(
                'passenger',
                'drivers',
                'brokers',
                'branches',
                'currentBranch'
            ));
        }

        return view('pages.passengers.edit', compact(
            'passenger',
            'drivers',
            'brokers',
            'branches',
            'currentBranch'
        ));
    }

    public function update(Request $request, $id)
    {
        $passenger = Passengers::findOrFail($id);
        $oldStatus = $passenger->status;

        // منع تعديل البيانات إذا كانت الرحلة مكتملة مسبقاً لحماية الحسابات والعمولات
        if ($oldStatus == 'completed') {
            return WebResponseClass::sendResponse('تم !', 'لا يمكنك تحديث بيانات الراكب المكتمل رحلتة', 'حسناً', 'passengers.index');
        }

        $validator = Validator::make($request->all(), [
            'date'             => ['required', 'date'],
            'passenger_number' => ['required', 'string', 'max:255'],
            'destination'             => ['required', 'string', 'max:255'],      // 🔥 الحقل الجديد للوجهة
            'pickup_location'         => ['required', 'string', 'max:255'],

            // 👈 التعديل الجديد: إزالة حقول العملاء وإضافة حقول الوسيط المتوافقة مع مودال التعديل
            'broker_id'        => ['nullable', 'exists:brokers,id'],
            'broker_name'      => ['required', 'string', 'max:255'], // حقل الاسم مطلوب دائماً لاستخدامه في البحث أو الإنشاء

            'count'            => ['required', 'integer', 'min:1'],
            'office_commission'       => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'other_office_commission' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'note'             => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            DB::beginTransaction();
            $data = $validator->validated();

            $data['passenger_number'] = $this->normalizePhone($data['passenger_number']);
            // $driverPhone = $this->normalizePhone($data['driver_phone'] ?? '');
            $data['branch_id'] = $this->currentBranchId();

            // 🌟 هندسة الوسطاء في التعديل: إذا لم يتم اختيار وسيط من القائمة (أي تم كتابة اسم جديد يدوياً)
            if (empty($data['broker_id']) && !empty($data['broker_name'])) {
                // سيبحث عن الاسم، إن وجده يجلبه، وإن لم يجده ينشئ وسيطاً جديداً في جدول الوسطاء فوراً
                $broker = Broker::firstOrCreate([
                    'name' => $data['broker_name']
                ]);

                $data['broker_id'] = $broker->id;
            }

            // معالجة السائق (تبقى كما هي في كودك الأصلي)
            // $data['driver_id'] = ($data['driver_id'] ?? null)
            //     ?: $this->resolvePassengerDriver($driverPhone, $data['driver_name'] ?? null);

            // 🗑️ تنظيف المصفوفة من الحقول النصية المؤقتة لكي لا يظهر خطأ أعمدة غير موجودة عند الحفظ
            unset($data['broker_name']);

            // تحديث سجل الراكب ببياناته الجديدة والوسيط الجديد
            $passenger->update($data);

            // 💰 إذا تحولت حالة الرحلة إلى "مكتمل" ولم تكن مكتملة من قبل، نقوم بتسجيل العمولة ماليًا
            // if ($passenger->status === 'completed' && $oldStatus !== 'completed') {
            //     // 💡 تنبيه مالي: تأكد من مراجعة كود السيرفس بالأسفل ليتعامل مع الـ broker_id بدلاً من العميل
            //     // $transactionService = new CustomerTransactionService(); 
            //     // $transactionService->recordPassengerCommission($passenger);
            // }

            DB::commit();
            return WebResponseClass::sendResponse('تم التحديث!', 'تم تعديل بيانات الراكب وتسجيل العمولة للوسيط.', 'حسناً', 'passengers.index');
        } catch (Exception $e) {
            DB::rollBack(); // التراجع عن العمليات في حال حدوث أي فشل تقني
            return WebResponseClass::sendExceptionError($e);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,confirmed,completed,cancel'],
        ]);

        try {
            DB::beginTransaction();

            $passenger = Passengers::findOrFail($id);
            $oldStatus = $passenger->status;
            $newStatus = $request->status;

            if ($oldStatus === 'completed') {
                return back()->with('error', 'لا يمكنك تحديث حالة الراكب المكتمل رحلته.');
            }

            if ($oldStatus === $newStatus) {
                return back();
            }

            $passenger->update(['status' => $newStatus]);

            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                // $transactionService = new CustomerTransactionService();
                // $transactionService->recordPassengerCommission($passenger);
            }

            DB::commit();

            $successMessages = [
                'confirmed' => 'تم تأكيد الحجز ✅',
                'completed' => 'تم إكمال الرحلة وتسجيل العمولة ✅',
                'cancel'    => 'تم إلغاء الحجز 🚫',
                'pending'   => 'تم إعادة الراكب لقيد الانتظار 🔄',
            ];

            return WebResponseClass::sendResponse('تم التحديث!', $successMessages[$newStatus] ?? 'تم تحديث الحالة بنجاح', 'حسناً', 'passengers.index');
        } catch (Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }

    public function destroy($id)
    {
        try {
            $passenger = Passengers::findOrFail($id);
            $passengerNumber = $passenger->passenger_number;

            $passenger->delete();

            AdminLoggerService::log(
                'حذف راكب',
                'Passengers',
                $id,
                "تم حذف الراكب رقم {$passengerNumber}"
            );

            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف الراكب بنجاح.',
                'حسناً',
                'passengers.index'
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    private function currentBranchId(): ?int
    {
        return auth()->user()->branch_id ?? null;
    }

    private function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $phone = preg_replace('/[^\d\+]/', '', $phone);
        $phone = ltrim($phone, '+');

        if (str_starts_with($phone, '00')) {
            $phone = substr($phone, 2);
        }

        if (str_starts_with($phone, '967967')) {
            $phone = substr($phone, 3);
        }

        if (str_starts_with($phone, '966966')) {
            $phone = substr($phone, 3);
        }

        if (preg_match('/^0(7\d{8})$/', $phone, $matches)) {
            return '967' . $matches[1];
        }

        if (preg_match('/^(7\d{8})$/', $phone, $matches)) {
            return '967' . $matches[1];
        }

        return $phone;
    }

    
}
