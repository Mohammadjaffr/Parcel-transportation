<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\App;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class WhatsAppSettingsController extends Controller
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * التحقق من توفر بيانات الشركة / المكتب للمستخدم الحالي
     */
    protected function getCompany(): ?App
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user?->app;
    }

    /**
     * عرض صفحة إعدادات WhatsApp للمكتب / الشركة الحالية
     */
    public function index(Request $request): View|RedirectResponse
    {
        $company = $this->getCompany();

        if (!$company) {
            return redirect()->route('dashboard.index')
                ->with('error', 'تعذر العثور على بيانات المكتب أو الشركة التابعة لحسابك.');
        }

        $service = $this->whatsAppService->forApp($company);
        $isConfigured = $company->hasWhatsAppConfigured();
        $maskedToken = $company->masked_whatsapp_token;
        $effectiveBaseUrl = $company->whatsapp_base_url;
        $effectiveTimeout = $company->whatsapp_timeout;

        $viewData = compact('company', 'isConfigured', 'maskedToken', 'effectiveBaseUrl', 'effectiveTimeout');

        if ($request->isMobile) {
            return view('mobile.pages.company.whatsapp-settings', $viewData);
        }

        return view('pages.company.whatsapp-settings', $viewData);
    }

    /**
     * حفظ وتحديث إعدادات ربط WhatsApp للشركة الحالية
     */
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $company = $this->getCompany();

        if (!$company) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'تعذر تحديد الشركة الحالية.'], 403);
            }
            return WebResponseClass::sendError('تعذر تحديد الشركة الحالية.');
        }

        $validator = Validator::make($request->all(), [
            'whatsapp_base_url'       => ['nullable', 'url', 'max:500'],
            'whatsapp_instance_id'    => ['nullable', 'string', 'max:150'],
            'whatsapp_instance_token' => ['nullable', 'string', 'max:1000'],
            'whatsapp_webhook_url'    => ['nullable', 'url', 'max:500'],
            'whatsapp_timeout'        => ['nullable', 'integer', 'min:5', 'max:120'],
        ], [
            'whatsapp_base_url.url'    => 'صيغة عنوان API الأساسي غير صحيحة، يجب أن يبدأ بـ http:// أو https://',
            'whatsapp_base_url.max'    => 'عنوان API الأساسي يتجاوز الحد المسموح به.',
            'whatsapp_instance_id.max' => 'معرف الـ Instance يتجاوز الحد المسموح به.',
            'whatsapp_webhook_url.url' => 'صيغة رابط Webhook غير صحيحة، يجب أن يبدأ بـ http:// أو https://',
            'whatsapp_timeout.integer' => 'قيمة المهلة الزمنية يجب أن تكون رقماً صحيحاً بالثواني.',
            'whatsapp_timeout.min'     => 'الحد الأدنى للمهلة الزمنية هو 5 ثوانٍ.',
            'whatsapp_timeout.max'     => 'الحد الأقصى للمهلة الزمنية هو 120 ثانية.',
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
                'whatsapp_base_url'    => $request->filled('whatsapp_base_url') ? trim($request->input('whatsapp_base_url')) : null,
                'whatsapp_instance_id' => $request->filled('whatsapp_instance_id') ? trim($request->input('whatsapp_instance_id')) : null,
                'whatsapp_webhook_url' => $request->filled('whatsapp_webhook_url') ? trim($request->input('whatsapp_webhook_url')) : null,
                'whatsapp_timeout'     => $request->filled('whatsapp_timeout') ? (int) $request->input('whatsapp_timeout') : 30,
            ];

            // الحفاظ على التوكن القديم إذا تُرك فارغاً أو محتوياً على رموز القناع
            if ($request->filled('whatsapp_instance_token')) {
                $rawToken = trim($request->input('whatsapp_instance_token'));
                if (!preg_match('/^[•*]+$/u', $rawToken)) {
                    $dataToUpdate['whatsapp_api_key'] = $rawToken;
                }
            }

            $company->update($dataToUpdate);
            $company->refresh();

            if ($request->wantsJson()) {
                return response()->json([
                    'success'       => true,
                    'message'       => 'تم حفظ إعدادات تكامل WhatsApp بنجاح.',
                    'is_configured' => $company->hasWhatsAppConfigured(),
                    'masked_token'  => $company->masked_whatsapp_token,
                ]);
            }

            return WebResponseClass::sendResponse(
                'تم الحفظ بنجاح!',
                'تم حفظ إعدادات WhatsApp للمكتب بنجاح.',
                'حسناً',
                'app.whatsapp.settings'
            );
        } catch (\Throwable $e) {
            Log::error("Failed to update WhatsApp settings for Company ID {$company->id}: " . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'حدث خطأ أثناء حفظ الإعدادات.'], 500);
            }
            return WebResponseClass::sendExceptionError($e, 'حدث خطأ أثناء حفظ الإعدادات!');
        }
    }

    /**
     * فحص حالة اتصال WhatsApp بالـ Instance الخاص بالمكتب
     */
    public function status(Request $request): JsonResponse
    {
        $company = $this->getCompany();

        if (!$company) {
            return response()->json(['success' => false, 'error' => 'تعذر تحديد الشركة الحالية.'], 403);
        }

        if (!$company->hasWhatsAppConfigured()) {
            return response()->json([
                'success'       => false,
                'is_configured' => false,
                'message'       => 'إعدادات WhatsApp غير مكتملة للمكتب.',
                'state'         => 'disconnected',
            ]);
        }

        $service = $this->whatsAppService->forApp($company);
        $result = $service->getStatus();

        if (!empty($result['error'])) {
            return response()->json([
                'success' => false,
                'error'   => $result['message'] ?? 'فشل الاتصال بخدمة WhatsApp',
                'state'   => 'error',
                'raw'     => $result,
            ], 400);
        }

        // استخلاص حالة الاتصال الشائعة من Evolution API ومختلف بوابات WhatsApp
        $isLoggedIn = data_get($result, 'data.LoggedIn') ?? data_get($result, 'LoggedIn');
        $isConnected = data_get($result, 'data.Connected') ?? data_get($result, 'Connected');
        $accountName = data_get($result, 'data.Name') ?? data_get($result, 'Name');

        if ($isLoggedIn === true) {
            $state = 'connected';
        } elseif ($isLoggedIn === false && $isConnected === true) {
            $state = 'disconnected'; // متصل بالسوكت ولكن لم يسجل الدخول (بانتظار مسح QR)
        } else {
            $state = data_get($result, 'instance.state')
                ?? data_get($result, 'state')
                ?? data_get($result, 'status')
                ?? 'disconnected';
        }

        return response()->json([
            'success'      => true,
            'state'        => $state,
            'is_logged_in' => (bool)$isLoggedIn,
            'account_name' => $accountName,
            'data'         => $result,
        ]);
    }

    /**
     * بدء الاتصال بالإنستانس
     */
    public function connect(Request $request): JsonResponse
    {
        $company = $this->getCompany();

        if (!$company) {
            return response()->json(['success' => false, 'error' => 'تعذر تحديد الشركة الحالية.'], 403);
        }

        if (!$company->hasWhatsAppConfigured()) {
            return response()->json(['success' => false, 'error' => 'يرجى حفظ إعدادات الـ Instance ومفتاح الوصول أولاً.'], 422);
        }

        $service = $this->whatsAppService->forApp($company);
        $webhook = $request->input('webhook_url') ?: $company->whatsapp_webhook_url;
        $result = $service->connect($webhook);

        if (!empty($result['error'])) {
            return response()->json([
                'success' => false,
                'error'   => $result['message'] ?? 'فشل بدء الاتصال',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلب بدء الاتصال بنجاح.',
            'data'    => $result,
        ]);
    }

    /**
     * جلب رمز QR Code لربط الحساب
     */
    public function qrCode(Request $request): JsonResponse
    {
        $company = $this->getCompany();

        if (!$company) {
            return response()->json(['success' => false, 'error' => 'تعذر تحديد الشركة الحالية.'], 403);
        }

        if (!$company->hasWhatsAppConfigured()) {
            return response()->json(['success' => false, 'error' => 'يرجى حفظ إعدادات الـ Instance ومفتاح الوصول أولاً.'], 422);
        }

        $service = $this->whatsAppService->forApp($company);
        $result = $service->getQrCode();

        if (!empty($result['error'])) {
            return response()->json([
                'success' => false,
                'error'   => $result['message'] ?? 'فشل توليد رمز QR',
            ], 400);
        }

        // استخراج كود الـ QR أو Base64 بمختلف البنى المحتملة لمزودات WhatsApp
        $qrcode = data_get($result, 'data.qrcode')
            ?? data_get($result, 'data.base64')
            ?? data_get($result, 'data.qrcode.base64')
            ?? data_get($result, 'qrcode.base64')
            ?? data_get($result, 'qrcode')
            ?? data_get($result, 'base64')
            ?? data_get($result, 'data.code')
            ?? data_get($result, 'code');

        $isLoggedIn = data_get($result, 'data.LoggedIn') ?? data_get($result, 'LoggedIn');
        $instanceState = strtolower(data_get($result, 'instance.state') ?? data_get($result, 'state') ?? '');

        // إذا كانت الجلسة مسجلة الدخول بالفعل
        if (!$qrcode && ($isLoggedIn === true || in_array($instanceState, ['open', 'connected']))) {
            return response()->json([
                'success'      => true,
                'is_connected' => true,
                'qrcode'       => null,
                'message'      => 'جلسة WhatsApp متصلة بالفعل برقم هاتف جاهز، لا حاجة لمسح رمز QR.',
                'data'         => $result,
            ]);
        }

        if (!$qrcode) {
            return response()->json([
                'success' => false,
                'qrcode'  => null,
                'error'   => 'لم يتم إرجاع رمز QR من الخادم. قد تكون الجلسة قيد المعالجة أو متصلة بالفعل.',
                'data'    => $result,
            ], 400);
        }

        return response()->json([
            'success' => true,
            'qrcode'  => $qrcode,
            'data'    => $result,
        ]);
    }

    /**
     * طلب كود الاقتران السريع عبر رقم الهاتف
     */
    public function pairingCode(Request $request): JsonResponse
    {
        $company = $this->getCompany();

        if (!$company) {
            return response()->json(['success' => false, 'error' => 'تعذر تحديد الشركة الحالية.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'min:8', 'max:25', 'regex:/^[0-9+\s\-()]+$/'],
        ], [
            'phone.required' => 'يرجى إدخال رقم الهاتف لطلب كود الاقتران.',
            'phone.regex'    => 'صيغة رقم الهاتف غير صحيحة.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()], 422);
        }

        if (!$company->hasWhatsAppConfigured()) {
            return response()->json(['success' => false, 'error' => 'يرجى حفظ إعدادات الـ Instance ومفتاح الوصول أولاً.'], 422);
        }

        $service = $this->whatsAppService->forApp($company);
        $phone = trim($request->input('phone'));
        $result = $service->getPairingCode($phone);

        if (!empty($result['error'])) {
            return response()->json([
                'success' => false,
                'error'   => $result['message'] ?? 'فشل طلب كود الاقتران',
            ], 400);
        }

        $code = data_get($result, 'data.PairingCode')
            ?? data_get($result, 'data.pairingCode')
            ?? data_get($result, 'data.pairing_code')
            ?? data_get($result, 'data.code')
            ?? data_get($result, 'PairingCode')
            ?? data_get($result, 'pairingCode')
            ?? data_get($result, 'pairing_code')
            ?? data_get($result, 'code');

        if (!$code) {
            return response()->json([
                'success' => false,
                'error'   => data_get($result, 'message') ?? 'لم يتم استلام كود الاقتران من الخادم.',
                'data'    => $result,
            ], 400);
        }

        return response()->json([
            'success' => true,
            'code'    => $code,
            'data'    => $result,
        ]);
    }

    /**
     * إعادة الاتصال
     */
    public function reconnect(Request $request): JsonResponse
    {
        $company = $this->getCompany();

        if (!$company) {
            return response()->json(['success' => false, 'error' => 'تعذر تحديد الشركة الحالية.'], 403);
        }

        $service = $this->whatsAppService->forApp($company);
        $result = $service->reconnect();

        if (!empty($result['error'])) {
            return response()->json(['success' => false, 'error' => $result['message'] ?? 'فشل إعادة الاتصال'], 400);
        }

        return response()->json(['success' => true, 'message' => 'تم طلب إعادة الاتصال بنجاح.', 'data' => $result]);
    }

    /**
     * فصل الاتصال مؤقتاً
     */
    public function disconnect(Request $request): JsonResponse
    {
        $company = $this->getCompany();

        if (!$company) {
            return response()->json(['success' => false, 'error' => 'تعذر تحديد الشركة الحالية.'], 403);
        }

        $service = $this->whatsAppService->forApp($company);
        $result = $service->disconnect();

        if (!empty($result['error'])) {
            return response()->json(['success' => false, 'error' => $result['message'] ?? 'فشل فصل الاتصال'], 400);
        }

        return response()->json(['success' => true, 'message' => 'تم فصل الاتصال بنجاح.', 'data' => $result]);
    }

    /**
     * تسجيل الخروج ومسح الجلسة
     */
    public function logout(Request $request): JsonResponse
    {
        $company = $this->getCompany();

        if (!$company) {
            return response()->json(['success' => false, 'error' => 'تعذر تحديد الشركة الحالية.'], 403);
        }

        $service = $this->whatsAppService->forApp($company);
        $result = $service->logout();

        if (!empty($result['error'])) {
            return response()->json(['success' => false, 'error' => $result['message'] ?? 'فشل تسجيل الخروج'], 400);
        }

        return response()->json(['success' => true, 'message' => 'تم تسجيل الخروج ومسح الجلسة بنجاح.', 'data' => $result]);
    }

    /**
     * إرسال رسالة نصية تجريبية للتحقق من الاتصال
     */
    public function sendTest(Request $request): RedirectResponse|JsonResponse
    {
        $company = $this->getCompany();

        if (!$company) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'تعذر تحديد الشركة الحالية.'], 403);
            }
            return WebResponseClass::sendError('تعذر تحديد الشركة الحالية.');
        }

        $validator = Validator::make($request->all(), [
            'recipient' => ['required', 'string', 'min:8', 'max:25', 'regex:/^[0-9+\s\-()]+$/'],
            'message'   => ['required', 'string', 'min:2', 'max:1000'],
        ], [
            'recipient.required' => 'يرجى إدخال رقم هاتف المستلم التجريبي.',
            'recipient.min'      => 'رقم هاتف المستلم قصير جداً.',
            'recipient.max'      => 'رقم هاتف المستلم يتجاوز الحد المسموح به.',
            'recipient.regex'    => 'صيغة رقم هاتف المستلم غير صحيحة.',
            'message.required'   => 'يرجى كتابة نص الرسالة التجريبية.',
            'message.min'        => 'نص الرسالة قصير جداً.',
            'message.max'        => 'يجب ألا يتجاوز نص الرسالة 1000 حرف.',
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

        if (!$company->hasWhatsAppConfigured()) {
            $msg = 'يرجى ضبط وحفظ عنوان API ومفتاح الـ Instance أولاً قبل إجراء اختبار الإرسال.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $msg], 422);
            }
            return WebResponseClass::sendError($msg, 'الإعدادات غير مكتملة');
        }

        $recipient = trim($request->input('recipient'));
        $content   = trim($request->input('message'));

        $service = $this->whatsAppService->forApp($company);
        $result = $service->sendText($recipient, $content);

        // التحقق من نجاح العملية حسب استجابة المزود
        $isSuccess = empty($result['error']) && (!isset($result['status']) || (int) $result['status'] < 400);

        if ($isSuccess) {
            $formattedNumber = $service->formatNumber($recipient);
            $successMsg = "تم إرسال الرسالة التجريبية بنجاح إلى الرقم {$formattedNumber} عبر خادم WhatsApp.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMsg,
                    'data'    => $result,
                ]);
            }

            return WebResponseClass::sendResponse(
                'نجاح الإرسال!',
                $successMsg,
                'حسناً',
                'app.whatsapp.settings'
            );
        }

        // تسجيل الخطأ بأمان دون إظهار المفاتيح
        $rawError = $result['message'] ?? 'فشل الاتصال بمزود الخدمة';
        Log::error("WhatsApp Gateway Test Failed for Company ID {$company->id}: {$rawError}");

        $userFriendlyMsg = 'فشل إرسال الرسالة التجريبية. يرجى التأكد من حالة اتصال الـ Instance وصحة الرمز والإنترنت.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error'   => $userFriendlyMsg,
                'details' => $rawError,
            ], 400);
        }

        return WebResponseClass::sendError($userFriendlyMsg, 'فشل الإرسال');
    }
}
