@extends('layouts.app')

@section('title', 'إعدادات الواتساب (WhatsApp) - ' . $branch->name)
@section('Breadcrumb', 'إعدادات الواتساب (WhatsApp)')

@section('content')
    <div x-data="{
        showToken: false,
        isSaving: false,
        isCheckingStatus: false,
        isSendingTest: false,
        isOperatingSession: false,
        isFetchingQr: false,
        isRequestingPairing: false,

        // الفرع المحدد حالياً
        selectedBranchId: {{ $branch->id }},

        // حالة التهيئة والاتصال
        isConfigured: {{ $isConfigured ? 'true' : 'false' }},
        connectionState: 'unknown',
        connectionText: 'جاري فحص الاتصال...',
        lastCheckedTime: '',

        // ربط الحساب (QR / Pairing)
        activeLinkTab: 'qr',
        qrImage: null,
        qrError: '',
        pairingPhone: '',
        pairingCode: null,
        pairingError: '',
        copiedPairing: false,

        // رسالة الاختبار
        testRecipient: '',
        testMessage: 'مرحباً بك! هذه رسالة تجريبية للتحقق من تكامل واتساب بنجاح مع نظام مرسل.',
        testSuccessMessage: '',
        testErrorMessage: '',

        // رسائل عامة
        sessionFeedbackMessage: '',
        sessionFeedbackType: 'success',

        // مدخلات نموذج الإعدادات التفاعلي
        baseUrlInput: '{{ $branch->whatsapp_base_url }}',
        instanceIdInput: '{{ $branch->whatsapp_instance_id }}',
        instanceTokenInput: '',
        webhookUrlInput: '{{ $branch->whatsapp_webhook_url }}',
        timeoutInput: {{ $branch->whatsapp_timeout ?? 30 }},
        maskedTokenDisplay: '{{ $maskedToken }}',
        saveFeedbackMessage: '',
        saveFeedbackType: 'success',

        // تبديل الفرع
        switchBranch(branchId) {
            window.location.href = '{{ route('app.whatsapp.settings') }}?branch_id=' + branchId;
        },

        async saveSettings() {
            this.isSaving = true;
            this.saveFeedbackMessage = '';

            try {
                const res = await fetch('{{ route('app.whatsapp.settings.update') }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        branch_id: this.selectedBranchId,
                        whatsapp_base_url: this.baseUrlInput,
                        whatsapp_instance_id: this.instanceIdInput,
                        whatsapp_instance_token: this.instanceTokenInput,
                        whatsapp_webhook_url: this.webhookUrlInput,
                        whatsapp_timeout: this.timeoutInput
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.saveFeedbackType = 'success';
                    this.saveFeedbackMessage = data.message || 'تم حفظ إعدادات تكامل WhatsApp بنجاح.';
                    this.isConfigured = data.is_configured;
                    if (data.masked_token) {
                        this.maskedTokenDisplay = data.masked_token;
                    }
                    this.instanceTokenInput = '';
                    this.checkStatus();
                } else {
                    this.saveFeedbackType = 'error';
                    this.saveFeedbackMessage = data.error || 'حدث خطأ أثناء حفظ الإعدادات.';
                }
            } catch (e) {
                this.saveFeedbackType = 'error';
                this.saveFeedbackMessage = 'حدث خطأ في الاتصال بالخادم أثناء الحفظ.';
            } finally {
                this.isSaving = false;
            }
        },

        init() {
            if (this.isConfigured) {
                this.checkStatus();
            } else {
                this.connectionState = 'not_configured';
                this.connectionText = 'بانتظار تهيئة بيانات الـ Instance والرمز';
            }
        },

        buildUrl(base) {
            return base + (base.includes('?') ? '&' : '?') + 'branch_id=' + this.selectedBranchId;
        },

        async checkStatus() {
            this.isCheckingStatus = true;
            try {
                const res = await fetch(this.buildUrl('{{ route('app.whatsapp.settings.status') }}'), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.lastCheckedTime = new Date().toLocaleTimeString('ar-YE');

                if (res.ok && data.success) {
                    const st = (data.state || '').toLowerCase();
                    if (st === 'open' || st === 'connected' || data.is_logged_in) {
                        this.connectionState = 'connected';
                        this.connectionText = 'متصل وجاهز للعمل' + (data.account_name ? ' (' + data.account_name + ')' : '');
                        this.qrImage = null;
                        this.qrError = '';
                    } else if (st === 'connecting') {
                        this.connectionState = 'connecting';
                        this.connectionText = 'جاري الاتصال بالسيرفر...';
                    } else {
                        this.connectionState = 'disconnected';
                        this.connectionText = 'غير متصل (يحتاج مسح QR أو اقتران)';
                        if (!this.qrImage && !this.isFetchingQr) {
                            this.fetchQrCode();
                        }
                    }
                } else {
                    this.connectionState = 'error';
                    this.connectionText = data.error || 'تعذر الوصول إلى مزود WhatsApp';
                }
            } catch (e) {
                this.connectionState = 'error';
                this.connectionText = 'خطأ في الاتصال بالخادم الداخلي';
            } finally {
                this.isCheckingStatus = false;
            }
        },

        async triggerSessionAction(actionName, routeUrl) {
            this.isOperatingSession = true;
            this.sessionFeedbackMessage = '';

            try {
                const res = await fetch(this.buildUrl(routeUrl), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ branch_id: this.selectedBranchId })
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    this.sessionFeedbackType = 'success';
                    this.sessionFeedbackMessage = data.message || `تم تنفيذ عملية ${actionName} بنجاح.`;
                    await this.checkStatus();
                    if (actionName === 'connect' && this.connectionState !== 'connected') {
                        this.fetchQrCode();
                    }
                } else {
                    this.sessionFeedbackType = 'error';
                    this.sessionFeedbackMessage = data.error || `فشل تنفيذ عملية ${actionName}.`;
                }
            } catch (e) {
                this.sessionFeedbackType = 'error';
                this.sessionFeedbackMessage = 'حدث خطأ غير متوقع أثناء إرسال الأمر.';
            } finally {
                this.isOperatingSession = false;
            }
        },

        async fetchQrCode() {
            this.isFetchingQr = true;
            this.qrError = '';
            try {
                const res = await fetch(this.buildUrl('{{ route('app.whatsapp.settings.qr') }}'), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (res.ok && data.success && data.qrcode) {
                    this.qrImage = data.qrcode;
                } else if (data.is_connected) {
                    this.qrImage = null;
                    this.connectionState = 'connected';
                    this.connectionText = 'متصل وجاهز للعمل';
                    this.qrError = data.message || 'الجلسة متصلة بالفعل برقم هاتف.';
                } else {
                    this.qrError = data.error || data.message || 'لم يتم إرجاع رمز QR من الخادم.';
                }
            } catch (e) {
                this.qrError = 'فشل جلب رمز QR من الخادم.';
            } finally {
                this.isFetchingQr = false;
            }
        },

        async requestPairingCode() {
            if (!this.pairingPhone) {
                this.pairingError = 'يرجى إدخال رقم الهاتف أولاً.';
                return;
            }

            this.isRequestingPairing = true;
            this.pairingError = '';
            this.pairingCode = null;

            try {
                const res = await fetch('{{ route('app.whatsapp.settings.pair') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ phone: this.pairingPhone, branch_id: this.selectedBranchId })
                });
                const data = await res.json();

                if (res.ok && data.success && data.code) {
                    this.pairingCode = data.code;
                } else {
                    this.pairingError = data.error || 'تعذر توليد كود الاقتران. تأكد من صحة الرقم ومن إعدادات الـ Instance.';
                }
            } catch (e) {
                this.pairingError = 'حدث خطأ في الاتصال بالخادم أثناء طلب كود الاقتران.';
            } finally {
                this.isRequestingPairing = false;
            }
        },

        copyPairingCode() {
            if (!this.pairingCode) return;
            navigator.clipboard.writeText(this.pairingCode);
            this.copiedPairing = true;
            setTimeout(() => { this.copiedPairing = false; }, 2500);
        },

        async sendTestWhatsApp() {
            if (!this.testRecipient) {
                this.testErrorMessage = 'يرجى إدخال رقم هاتف المستلم التجريبي.';
                this.testSuccessMessage = '';
                return;
            }

            if (!this.testMessage) {
                this.testErrorMessage = 'يرجى كتابة نص الرسالة التجريبية.';
                this.testSuccessMessage = '';
                return;
            }

            this.isSendingTest = true;
            this.testSuccessMessage = '';
            this.testErrorMessage = '';

            try {
                const res = await fetch('{{ route('app.whatsapp.settings.test') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        recipient: this.testRecipient,
                        message: this.testMessage,
                        branch_id: this.selectedBranchId
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.testSuccessMessage = data.message || 'تم إرسال الرسالة التجريبية بنجاح عبر واتساب!';
                    this.testErrorMessage = '';
                } else {
                    this.testErrorMessage = data.error || (data.message || 'فشل إرسال الرسالة التجريبية.');
                    this.testSuccessMessage = '';
                }
            } catch (error) {
                this.testErrorMessage = 'حدث خطأ في الاتصال بالخادم. يرجى مراجعة الاتصال والمحاولة ثانية.';
                this.testSuccessMessage = '';
            } finally {
                this.isSendingTest = false;
            }
        }
    }" class="flex flex-col gap-6 mx-auto w-full max-w-7xl">

        {{-- ================= 1. رأس الصفحة (Page Header Card) ================= --}}
        <div class="px-2 sm:px-4">
            <div class="relative bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 overflow-hidden pt-12 pb-6 px-6 text-center">

                {{-- الشريط اللوني العلوي الزمردي المتناسق --}}
                <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-r from-emerald-500/20 via-emerald-500/10 to-teal-500/5"></div>

                {{-- أيقونة الهيدر المركزية للواتساب --}}
                <div class="relative mx-auto w-24 h-24 rounded-[1.5rem] bg-white dark:bg-boxdark shadow-lg border-4 border-white dark:border-boxdark p-1 z-10 -mt-16 mb-4">
                    <div class="flex overflow-hidden justify-center items-center w-full h-full text-emerald-600 bg-emerald-50 rounded-xl dark:bg-emerald-950/50 dark:text-emerald-400">
                        <span class="text-4xl material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">chat</span>
                    </div>
                </div>

                {{-- عنوان الصفحة --}}
                <h1 class="flex gap-2 justify-center items-center mb-2 text-2xl font-black font-headline text-slate-800 dark:text-white">
                    إعدادات تكامل الواتساب (WhatsApp)
                    <span class="w-2.5 h-2.5 rounded-full transition-colors"
                        :class="{
                            'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)] animate-pulse': connectionState === 'connected',
                            'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.6)]': connectionState === 'connecting',
                            'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.6)]': connectionState === 'error',
                            'bg-slate-400': connectionState === 'disconnected' || connectionState === 'not_configured' || connectionState === 'unknown'
                        }"></span>
                </h1>

                <p class="mx-auto mb-4 max-w-2xl text-xs font-medium leading-relaxed sm:text-sm text-slate-500 dark:text-gray-400">
                    إدارة بوابة وخدمة WhatsApp الخاصة بفرع <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $branch->name }}</span>
                    التابع لـ <span class="font-bold text-slate-700 dark:text-gray-200">{{ $company->name }}</span>، وربط الجلسة ورمز الاقتران واختبار الإرسال المباشر بصورة مستقلة لكل فرع.
                </p>

                {{-- ============= اختيار الفرع ============= --}}
                @if($branches->count() > 1)
                <div class="flex justify-center mb-2">
                    <div class="inline-flex relative gap-2 items-center px-4 py-2 rounded-xl border bg-slate-50 dark:bg-gray-800/60 border-slate-200/80 dark:border-gray-700">
                        <span class="material-symbols-outlined text-[18px] text-emerald-600 dark:text-emerald-400">apartment</span>
                        <label class="text-xs font-bold text-slate-600 dark:text-gray-300">الفرع:</label>
                        <select x-model="selectedBranchId"
                            @change="switchBranch(selectedBranchId)"
                            class="pr-6 text-xs font-bold bg-transparent border-none appearance-none cursor-pointer outline-none sm:text-sm text-slate-800 dark:text-white focus:ring-0">
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ $b->id == $branch->id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->city }})
                                    @if($b->hasWhatsAppConfigured()) ✅ @endif
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined text-[16px] text-slate-400 pointer-events-none absolute left-3">expand_more</span>
                    </div>
                </div>
                @endif

            </div>
        </div>

        {{-- تنبيه تفاعلي عند تنفيذ عمليات الجلسة --}}
        <div x-cloak x-show="sessionFeedbackMessage" x-transition.opacity.duration.300ms class="px-2 sm:px-4">
            <div class="flex gap-3 items-start p-4 rounded-2xl border shadow-sm"
                :class="sessionFeedbackType === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800' : 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800'">
                <span class="mt-0.5 text-xl material-symbols-outlined shrink-0" x-text="sessionFeedbackType === 'success' ? 'check_circle' : 'error'"></span>
                <div class="flex-1 text-xs font-bold leading-relaxed" x-text="sessionFeedbackMessage"></div>
                <button type="button" @click="sessionFeedbackMessage = ''" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </div>

        {{-- ================= شبكة الأقسام الرئيسية (Grid) ================= --}}
        <div class="grid grid-cols-1 gap-6 items-start px-2 sm:px-4 lg:grid-cols-12">

            {{-- ================= العمود الأول (اليمين في RTL: الإعدادات وربط الحساب) ================= --}}
            <div class="space-y-6 lg:col-span-7">

                {{-- 2. بطاقة ربط الحساب (QR Code أو كود الاقتران عبر الهاتف) --}}
                <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden">

                    {{-- ===== حالة الاتصال الناجح: عرض رسالة التأكيد بدلاً من QR ===== --}}
                    <div x-show="connectionState === 'connected'" x-transition.opacity.duration.400ms x-cloak>
                        <div class="flex gap-3 items-center mb-6">
                            <div class="flex justify-center items-center w-12 h-12 text-emerald-600 bg-emerald-50 rounded-2xl shadow-sm dark:bg-emerald-950/40 dark:text-emerald-400 shrink-0">
                                <span class="text-2xl material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-black font-headline text-slate-800 dark:text-white">حساب WhatsApp مرتبط ✅</h2>
                                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400">الجلسة نشطة ومتصلة بنجاح لفرع {{ $branch->name }} — لا حاجة لمسح QR</p>
                            </div>
                        </div>

                        <div class="p-5 space-y-3 text-center bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border dark:from-emerald-950/30 dark:to-teal-950/20 border-emerald-200/60 dark:border-emerald-800/60">
                            <div class="flex justify-center">
                                <div class="flex justify-center items-center w-20 h-20 bg-emerald-100 rounded-full shadow-inner dark:bg-emerald-900/50">
                                    <span class="text-4xl text-emerald-600 material-symbols-outlined dark:text-emerald-400" style="font-variation-settings: 'FILL' 1;">link</span>
                                </div>
                            </div>
                            <h3 class="text-sm font-black text-emerald-800 font-headline dark:text-emerald-300">الاتصال فعّال وجاهز</h3>
                            <p class="mx-auto max-w-sm text-xs font-medium leading-relaxed text-emerald-700 dark:text-emerald-400">
                                حساب WhatsApp مرتبط ومتصل بالسيرفر بنجاح لفرع <strong>{{ $branch->name }}</strong>. يمكنك إرسال الرسائل والإشعارات مباشرة.
                                <br>لإعادة ربط رقم جديد، قم بتسجيل الخروج أولاً من قسم "إدارة الجلسة".
                            </p>
                            <div class="flex gap-2 justify-center items-center pt-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_6px_rgba(16,185,129,0.5)]"></span>
                                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400" x-text="connectionText"></span>
                            </div>
                        </div>
                    </div>

                    {{-- ===== حالة عدم الاتصال: عرض QR Code و كود الاقتران ===== --}}
                    <div x-show="connectionState !== 'connected'" x-transition.opacity.duration.400ms>
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-12 h-12 text-teal-600 bg-teal-50 rounded-2xl shadow-sm dark:bg-teal-950/40 dark:text-teal-400 shrink-0">
                                    <span class="text-2xl material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">qr_code_scanner</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-black font-headline text-slate-800 dark:text-white">ربط حساب WhatsApp</h2>
                                    <p class="text-xs font-bold text-slate-400 dark:text-gray-400">اقتران رقم هاتف فرع <strong class="text-emerald-600">{{ $branch->name }}</strong> عبر QR Code أو كود الاقتران الرقمي</p>
                                </div>
                            </div>

                            {{-- تبديل التبويبات --}}
                            <div class="flex items-center p-1 rounded-xl bg-slate-100 dark:bg-gray-800">
                                <button type="button"
                                    @click="activeLinkTab = 'qr'"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all"
                                    :class="activeLinkTab === 'qr' ? 'bg-white dark:bg-boxdark text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                                    رمز QR
                                </button>
                                <button type="button"
                                    @click="activeLinkTab = 'pairing'"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all"
                                    :class="activeLinkTab === 'pairing' ? 'bg-white dark:bg-boxdark text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                                    كود الاقتران
                                </button>
                            </div>
                        </div>

                        {{-- تبويب مسح QR Code --}}
                        <div x-show="activeLinkTab === 'qr'" x-transition.opacity class="space-y-4 text-center">
                            <div class="max-w-xs mx-auto p-4 rounded-2xl bg-slate-50 dark:bg-gray-800/50 border border-slate-200/80 dark:border-gray-700 flex flex-col items-center justify-center min-h-[220px]">
                                <template x-if="qrImage">
                                    <div class="p-3 bg-white rounded-xl border shadow-sm border-slate-200">
                                        <img :src="qrImage.startsWith('data:') || qrImage.startsWith('http') ? qrImage : 'data:image/png;base64,' + qrImage"
                                            alt="WhatsApp QR Code"
                                            class="object-contain w-48 h-48">
                                    </div>
                                </template>

                                <template x-if="!qrImage && !isFetchingQr">
                                    <div class="py-6 text-center">
                                        <span class="mb-2 text-5xl material-symbols-outlined text-slate-300 dark:text-gray-600">qr_code_2</span>
                                        <p class="text-xs font-medium text-slate-500 dark:text-gray-400">
                                            انقر على الزر أدناه لتوليد رمز الاستجابة السريعة (QR Code) الجديد
                                        </p>
                                    </div>
                                </template>

                                <template x-if="isFetchingQr">
                                    <div class="py-8 space-y-2 text-center">
                                        <div class="mx-auto w-8 h-8 rounded-full border-emerald-500 animate-spin border-3 border-t-transparent"></div>
                                        <p class="text-xs font-bold text-slate-500">جاري طلب رمز QR من السيرفر...</p>
                                    </div>
                                </template>

                                <template x-if="qrError">
                                    <div class="p-3 mt-3 text-xs font-medium text-amber-800 bg-amber-50 rounded-xl border border-amber-200 dark:bg-amber-950/40 dark:border-amber-800 dark:text-amber-300">
                                        <span x-text="qrError"></span>
                                    </div>
                                </template>
                            </div>

                            <div class="flex gap-3 justify-center">
                                <button type="button"
                                    @click="fetchQrCode()"
                                    :disabled="isFetchingQr || !isConfigured"
                                    class="flex gap-2 justify-center items-center px-5 h-11 text-xs font-bold text-white bg-emerald-600 rounded-xl shadow-md transition-all hover:bg-emerald-700 shadow-emerald-600/20 disabled:opacity-50">
                                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                                    <span>تحديث وجلب QR Code</span>
                                </button>
                            </div>

                            <p class="text-[11px] text-slate-400">
                                افتح تطبيق WhatsApp على هاتفك > الأجهزة المرتبطة > ربط جهاز > وجّه الكاميرا نحو الرمز أعلاه.
                            </p>
                        </div>

                        {{-- تبويب كود الاقتران الرقمي برقم الهاتف --}}
                        <div x-show="activeLinkTab === 'pairing'" x-transition.opacity class="space-y-4">
                            <p class="text-xs leading-relaxed text-slate-500 dark:text-gray-400">
                                يمكنك ربط الحساب بدون كاميرا عبر إدخال رقم الهاتف المسجل في واتساب وتوليد كود مكون من 8 خانات لإدخاله في هاتفك:
                            </p>

                            <div class="flex flex-col gap-3 sm:flex-row">
                                <div class="relative flex-1">
                                    <input type="tel"
                                        x-model="pairingPhone"
                                        dir="ltr"
                                        placeholder="مثال: 771234567 أو 967771234567"
                                        class="px-4 pr-11 w-full h-12 font-mono text-xs rounded-xl border-none ring-1 outline-none sm:text-sm ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white focus:ring-2 focus:ring-emerald-500/20">
                                    <div class="absolute top-3.5 right-3.5 pointer-events-none text-slate-400">
                                        <span class="material-symbols-outlined text-[20px]">phone</span>
                                    </div>
                                </div>

                                <button type="button"
                                    @click="requestPairingCode()"
                                    :disabled="isRequestingPairing || !isConfigured"
                                    class="flex gap-2 justify-center items-center px-5 h-12 text-xs font-bold text-white bg-emerald-600 rounded-xl shadow-md transition-all hover:bg-emerald-700 shadow-emerald-600/20 disabled:opacity-50 shrink-0 font-headline">
                                    <template x-if="!isRequestingPairing">
                                        <span class="flex gap-1.5 items-center">
                                            <span class="material-symbols-outlined text-[18px]">pin</span>
                                            <span>طلب كود الاقتران</span>
                                        </span>
                                    </template>
                                    <template x-if="isRequestingPairing">
                                        <span class="flex gap-1.5 items-center">
                                            <span class="w-4 h-4 rounded-full border-2 border-white animate-spin border-t-transparent"></span>
                                            <span>جاري الطلب...</span>
                                        </span>
                                    </template>
                                </button>
                            </div>

                            {{-- عرض كود الاقتران الناتج --}}
                            <template x-if="pairingCode">
                                <div class="flex justify-between items-center p-4 bg-emerald-50 rounded-2xl border border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-800">
                                    <div>
                                        <span class="text-[11px] font-bold text-emerald-800 dark:text-emerald-300 block mb-0.5">كود الاقتران الخاص بك:</span>
                                        <span class="font-mono text-2xl font-black tracking-widest text-emerald-700 dark:text-emerald-400" x-text="pairingCode"></span>
                                    </div>
                                    <button type="button" @click="copyPairingCode()"
                                        class="flex gap-1.5 items-center px-3.5 py-2 text-xs font-bold text-emerald-700 bg-white rounded-xl border border-emerald-200 shadow-sm dark:bg-boxdark dark:text-emerald-300 dark:border-emerald-700">
                                        <span class="material-symbols-outlined text-[16px]" x-text="copiedPairing ? 'check' : 'content_copy'"></span>
                                        <span x-text="copiedPairing ? 'تم النسخ!' : 'نسخ الكود'"></span>
                                    </button>
                                </div>
                            </template>

                            <template x-if="pairingError">
                                <div class="p-3 text-xs font-bold text-rose-800 bg-rose-50 rounded-xl border border-rose-200" x-text="pairingError"></div>
                            </template>
                        </div>
                    </div>

                </div>

            </div>

            {{-- ================= العمود الثاني (اليسار في RTL: حالة الجلسة واختبار الإرسال) ================= --}}
            <div class="space-y-6 lg:col-span-5">

                {{-- 3. بطاقة التحكم وإدارة الجلسة الحية --}}
                <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex gap-3 items-center">
                            <div class="flex justify-center items-center w-12 h-12 text-emerald-600 bg-emerald-50 rounded-2xl shadow-sm dark:bg-emerald-950/40 dark:text-emerald-400 shrink-0">
                                <span class="text-2xl material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">sensors</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-black font-headline text-slate-800 dark:text-white">إدارة الجلسة</h2>
                                <p class="text-xs font-bold text-slate-400 dark:text-gray-400">أوامر التحكم بجلسة WhatsApp لفرع {{ $branch->name }}</p>
                            </div>
                        </div>

                        {{-- زر تحديث الحالة --}}
                        <button type="button"
                            @click="checkStatus()"
                            :disabled="isCheckingStatus || !isConfigured"
                            class="flex gap-1 items-center px-3 py-1.5 text-xs font-bold rounded-xl border transition-all border-slate-200 dark:border-gray-700 text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-gray-800 disabled:opacity-50">
                            <span class="material-symbols-outlined text-[16px]" :class="isCheckingStatus ? 'animate-spin' : ''">refresh</span>
                            <span>فحص</span>
                        </button>
                    </div>

                    {{-- شريط ملخص الحالة الحالية --}}
                    <div class="p-4 mb-5 space-y-2 rounded-2xl border bg-slate-50 dark:bg-gray-800/50 border-slate-100 dark:border-gray-700/80">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-500 dark:text-gray-400">حالة الجلسة:</span>
                            <span class="font-bold font-headline"
                                :class="{
                                    'text-emerald-600 dark:text-emerald-400': connectionState === 'connected',
                                    'text-amber-600 dark:text-amber-400': connectionState === 'connecting',
                                    'text-rose-600 dark:text-rose-400': connectionState === 'error',
                                    'text-slate-600 dark:text-gray-400': connectionState === 'disconnected' || connectionState === 'not_configured' || connectionState === 'unknown'
                                }"
                                x-text="connectionText"></span>
                        </div>
                        <template x-if="lastCheckedTime">
                            <div class="flex justify-between items-center text-[10px] text-slate-400">
                                <span>آخر فحص:</span>
                                <span x-text="lastCheckedTime" dir="ltr"></span>
                            </div>
                        </template>
                    </div>

                    {{-- شبكة أزرار التحكم بالجلسة --}}
                    <div class="grid grid-cols-2 gap-3">
                        {{-- 1. بدء الاتصال --}}
                        <button type="button"
                            @click="triggerSessionAction('connect', '{{ route('app.whatsapp.settings.connect') }}')"
                            :disabled="isOperatingSession || !isConfigured"
                            class="flex gap-1.5 justify-center items-center px-3 h-11 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-xl border transition-all hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200/60 dark:border-emerald-800 disabled:opacity-50 font-headline">
                            <span class="material-symbols-outlined text-[18px]">play_arrow</span>
                            <span>بدء الاتصال</span>
                        </button>

                        {{-- 2. إعادة الاتصال --}}
                        <button type="button"
                            @click="triggerSessionAction('reconnect', '{{ route('app.whatsapp.settings.reconnect') }}')"
                            :disabled="isOperatingSession || !isConfigured"
                            class="flex gap-1.5 justify-center items-center px-3 h-11 text-xs font-bold text-sky-700 bg-sky-50 rounded-xl border transition-all hover:bg-sky-100 dark:bg-sky-950/40 dark:text-sky-300 border-sky-200/60 dark:border-sky-800 disabled:opacity-50 font-headline">
                            <span class="material-symbols-outlined text-[18px]">sync</span>
                            <span>إعادة الاتصال</span>
                        </button>

                        {{-- 3. فصل الاتصال مؤقتاً --}}
                        <button type="button"
                            @click="triggerSessionAction('disconnect', '{{ route('app.whatsapp.settings.disconnect') }}')"
                            :disabled="isOperatingSession || !isConfigured"
                            class="flex gap-1.5 justify-center items-center px-3 h-11 text-xs font-bold text-amber-700 bg-amber-50 rounded-xl border transition-all hover:bg-amber-100 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200/60 dark:border-amber-800 disabled:opacity-50 font-headline">
                            <span class="material-symbols-outlined text-[18px]">pause</span>
                            <span>فصل مؤقت</span>
                        </button>

                        {{-- 4. تسجيل الخروج ومسح الجلسة --}}
                        <button type="button"
                            @click="if(confirm('هل أنت متأكد من تسجيل الخروج ومسح الجلسة لربط رقم جديد؟')) { triggerSessionAction('logout', '{{ route('app.whatsapp.settings.logout') }}') }"
                            :disabled="isOperatingSession || !isConfigured"
                            class="flex gap-1.5 justify-center items-center px-3 h-11 text-xs font-bold text-rose-700 bg-rose-50 rounded-xl border transition-all hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200/60 dark:border-rose-800 disabled:opacity-50 font-headline">
                            <span class="material-symbols-outlined text-[18px]">logout</span>
                            <span>تسجيل خروج</span>
                        </button>
                    </div>
                </div>

                {{-- 4. بطاقة اختبار إرسال رسالة تجريبية --}}
                <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden">
                    <div class="flex relative gap-3 items-center mb-6">
                        <div class="flex justify-center items-center w-12 h-12 text-emerald-600 bg-emerald-50 rounded-2xl shadow-sm dark:bg-emerald-950/40 dark:text-emerald-400 shrink-0">
                            <span class="text-2xl material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">send</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-black font-headline text-slate-800 dark:text-white">اختبار إرسال WhatsApp</h2>
                            <p class="text-xs font-bold text-slate-400 dark:text-gray-400">إرسال رسالة تجريبية فورية للتحقق من جلسة فرع {{ $branch->name }}</p>
                        </div>
                    </div>

                    {{-- تنبيه إذا لم تكن البيانات مكتملة --}}
                    @if(!$isConfigured)
                        <div class="flex gap-3 items-start p-4 mb-5 bg-amber-50 rounded-2xl border dark:bg-amber-950/30 border-amber-200/80 dark:border-amber-900/50">
                            <span class="mt-0.5 text-xl text-amber-500 material-symbols-outlined shrink-0">warning</span>
                            <div class="text-xs font-medium leading-relaxed text-amber-800 dark:text-amber-300">
                                <span class="block mb-0.5 font-bold">الإعدادات غير مكتملة:</span>
                                يرجى حفظ معرّف الـ Instance والرمز أولاً لفرع {{ $branch->name }} لتتمكن من إجراء اختبار الإرسال.
                            </div>
                        </div>
                    @endif

                    <div class="space-y-4">
                        {{-- رقم المستلم --}}
                        <div>
                            <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">
                                رقم هاتف المستلم التجريبي
                                <span class="text-rose-500">*</span>
                            </label>

                            <div class="flex relative items-center">
                                <input type="tel"
                                    x-model="testRecipient"
                                    dir="ltr"
                                    placeholder="مثال: 771234567 أو 967771234567"
                                    class="px-4 pr-11 w-full h-12 font-mono text-xs rounded-xl border-none ring-1 transition-all outline-none sm:text-sm ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white focus:bg-white dark:focus:bg-boxdark focus:ring-2 focus:ring-emerald-500/20">

                                <div class="absolute right-3.5 pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">phone_android</span>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-gray-500 mt-1 px-1">
                                يدعم الأرقام اليمنية تلقائياً (967) وكذلك الأرقام الدولية دون تكرار.
                            </p>
                        </div>

                        {{-- نص الرسالة --}}
                        <div>
                            <div class="flex justify-between items-center px-1 mb-1.5">
                                <label class="text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">
                                    نص الرسالة التجريبية
                                    <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] font-mono text-slate-400" x-text="testMessage.length + ' / 1000 حرف'"></span>
                            </div>

                            <div class="relative">
                                <textarea x-model="testMessage" rows="3" maxlength="1000"
                                    placeholder="اكتب نص الرسالة التجريبية هنا..."
                                    class="p-3.5 w-full text-xs leading-relaxed rounded-xl border-none ring-1 transition-all outline-none resize-none sm:text-sm ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white focus:bg-white dark:focus:bg-boxdark focus:ring-2 focus:ring-emerald-500/20"></textarea>
                            </div>
                        </div>

                        {{-- زر الإرسال --}}
                        <div class="pt-2">
                            <button type="button"
                                @click="sendTestWhatsApp()"
                                :disabled="isSendingTest || !isConfigured"
                                class="flex gap-2 justify-center items-center w-full h-12 text-sm font-bold text-white bg-emerald-600 rounded-xl shadow-lg transition-all shadow-emerald-600/20 hover:bg-emerald-700 active:scale-95 disabled:opacity-50 disabled:pointer-events-none font-headline">
                                <template x-if="!isSendingTest">
                                    <span class="flex gap-2 items-center">
                                        <span class="material-symbols-outlined text-[20px]">send</span>
                                        <span>إرسال رسالة تجريبية</span>
                                    </span>
                                </template>
                                <template x-if="isSendingTest">
                                    <span class="flex gap-2 items-center">
                                        <span class="w-5 h-5 rounded-full border-2 border-white animate-spin border-t-transparent"></span>
                                        <span>جاري محاولة الإرسال...</span>
                                    </span>
                                </template>
                            </button>
                        </div>

                        {{-- رسالة النجاح --}}
                        <div x-cloak x-show="testSuccessMessage" x-transition.opacity.duration.300ms
                            class="flex gap-3 items-start p-4 bg-emerald-50 rounded-2xl border border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-800">
                            <span class="mt-0.5 text-xl text-emerald-600 material-symbols-outlined dark:text-emerald-400 shrink-0">check_circle</span>
                            <div class="text-xs font-bold leading-relaxed text-emerald-800 dark:text-emerald-300" x-text="testSuccessMessage"></div>
                        </div>

                        {{-- رسالة الخطأ --}}
                        <div x-cloak x-show="testErrorMessage" x-transition.opacity.duration.300ms
                            class="flex gap-3 items-start p-4 bg-rose-50 rounded-2xl border border-rose-200 dark:bg-rose-950/40 dark:border-rose-800">
                            <span class="mt-0.5 text-xl text-rose-600 material-symbols-outlined dark:text-rose-400 shrink-0">error</span>
                            <div class="text-xs font-bold leading-relaxed text-rose-800 dark:text-rose-300" x-text="testErrorMessage"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
