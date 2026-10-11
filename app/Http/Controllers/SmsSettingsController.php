<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\App;
use App\Services\HttpSmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SmsSettingsController extends Controller
{
    protected HttpSmsService $smsService;

    public function __construct(HttpSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * عرض صفحة إعدادات الرسائل النصية SMS للمكتب / الشركة الحالية
     */
    public function index(Request $request): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $company = $user?->app;

        if (!$company) {
            return redirect()->route('dashboard.index')
                ->with('error', 'تعذر العثور على بيانات المكتب أو الشركة التابعة لحسابك.');
        }

        $baseUrl = config('services.httpsms.base_url', 'https://abdaa.tiyar.cc/v1');
        $isConfigured = $company->hasSmsConfigured();
        $maskedKey = $company->masked_sms_api_key;

        $viewData = compact('company', 'baseUrl', 'isConfigured', 'maskedKey');

        if ($request->isMobile) {
            return view('mobile.pages.company.sms-settings', $viewData);
        }

        return view('pages.company.sms-settings', $viewData);
    }

    /**
     * حفظ وتحديث إعدادات بوابة SMS للشركة الحالية
     */
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $company = $user?->app;

        if (!$company) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'تعذر تحديد الشركة الحالية.'], 403);
            }
            return WebResponseClass::sendError('تعذر تحديد الشركة الحالية.');
        }

        $validator = Validator::make($request->all(), [
            'sms_api_key'    => 'nullable|string|max:500',
            'sms_from_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\s\-()]+$/'],
        ], [
            'sms_api_key.max'       => 'طول مفتاح API يتجاوز الحد المسموح به.',
            'sms_from_phone.max'    => 'رقم الهاتف المرسل يتجاوز الحد المسموح به.',
            'sms_from_phone.regex'  => 'صيغة رقم المرسل غير صحيحة، يجب أن يحتوي على أرقام فقط.',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $dataToUpdate = [
                'sms_from_phone' => $request->filled('sms_from_phone') ? trim($request->input('sms_from_phone')) : null,
            ];

            // الحفاظ على المفتاح القديم إذا تُرك الحقل فارغاً أو محتوياً على رموز القناع
            if ($request->filled('sms_api_key')) {
                $rawKey = trim($request->input('sms_api_key'));
                if (!preg_match('/^[•*]+$/u', $rawKey)) {
                    $dataToUpdate['sms_api_key'] = $rawKey;
                }
            }

            $company->update($dataToUpdate);

            if ($request->wantsJson()) {
                return response()->json([
                    'success'       => true,
                    'message'       => 'تم حفظ إعدادات بوابة الرسائل النصية بنجاح.',
                    'is_configured' => $company->fresh()->hasSmsConfigured(),
                    'masked_key'    => $company->fresh()->masked_sms_api_key,
                ]);
            }

            return WebResponseClass::sendResponse(
                'تم الحفظ بنجاح!',
                'تم حفظ إعدادات بوابة الرسائل النصية (SMS) بنجاح.',
                'حسناً',
                'app.sms.settings'
            );
        } catch (\Throwable $e) {
            Log::error("Failed to update SMS settings for Company ID {$company->id}: " . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'حدث خطأ أثناء حفظ الإعدادات.'], 500);
            }
            return WebResponseClass::sendExceptionError($e, 'حدث خطأ أثناء حفظ الإعدادات!');
        }
    }

    /**
     * إرسال رسالة نصية تجريبية للتحقق من إعدادات البوابة
     */
    public function sendTest(Request $request): RedirectResponse|JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $company = $user?->app;

        if (!$company) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'تعذر تحديد الشركة الحالية.'], 403);
            }
            return WebResponseClass::sendError('تعذر تحديد الشركة الحالية.');
        }

        $validator = Validator::make($request->all(), [
            'recipient' => ['required', 'string', 'min:8', 'max:25', 'regex:/^[0-9+\s\-()]+$/'],
            'message'   => ['required', 'string', 'min:2', 'max:500'],
        ], [
            'recipient.required' => 'يرجى إدخال رقم هاتف المستلم التجريبي.',
            'recipient.min'      => 'رقم هاتف المستلم قصير جداً.',
            'recipient.max'      => 'رقم هاتف المستلم يتجاوز الحد المسموح به.',
            'recipient.regex'    => 'صيغة رقم هاتف المستلم غير صحيحة.',
            'message.required'   => 'يرجى كتابة نص الرسالة التجريبية.',
            'message.min'        => 'نص الرسالة قصير جداً.',
            'message.max'        => 'يجب ألا يتجاوز نص الرسالة 500 حرف.',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return WebResponseClass::sendValidationError($validator);
        }

        $apiKey = $company->sms_api_key;
        $fromPhone = $company->sms_from_phone;

        if (empty($apiKey) || empty($fromPhone)) {
            $msg = 'يرجى ضبط وحفظ مفتاح API ورقم المرسل أولاً قبل إجراء اختبار الإرسال.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $msg], 422);
            }
            return WebResponseClass::sendError($msg, 'الإعدادات غير مكتملة');
        }

        $recipient = trim($request->input('recipient'));
        $content = trim($request->input('message'));

        // استدعاء خدمة HttpSmsService المعتمدة كما هي دون تغيير
        $result = $this->smsService->send($recipient, $content, $apiKey, $fromPhone);

        if (!empty($result['success']) && $result['success'] === true) {
            $successMsg = "تم إرسال الرسالة التجريبية بنجاح وقبولها من بوابة SMS إلى الرقم {$recipient}.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMsg,
                    'data'    => $result['data'] ?? null,
                ]);
            }

            return WebResponseClass::sendResponse(
                'نجاح الإرسال!',
                $successMsg,
                'حسناً',
                'app.sms.settings'
            );
        }

        // عند حدوث خطأ من البوابة، نسجل في السجل بدون كشف المفتاح ونظهر رسالة ودية
        $rawError = $result['error'] ?? 'فشل الاتصال بمزود الخدمة';
        Log::error("SMS Gateway Test Failed for Company ID {$company->id}: {$rawError}");

        $userFriendlyMsg = 'فشل إرسال الرسالة التجريبية. يرجى التحقق من صحة مفتاح API ورقم المرسل وتوفر رصيد لدى المزود.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error'   => $userFriendlyMsg,
            ], 400);
        }

        return WebResponseClass::sendError($userFriendlyMsg, 'فشل الإرسال');
    }
}
