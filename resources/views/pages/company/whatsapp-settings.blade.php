@extends('layouts.app')

@section('title', 'إعدادات الواتساب (WhatsApp)')
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
        baseUrlInput: '{{ $company->whatsapp_base_url }}',
        instanceIdInput: '{{ $company->whatsapp_instance_id }}',
        instanceTokenInput: '',
        webhookUrlInput: '{{ $company->whatsapp_webhook_url }}',
        timeoutInput: {{ $company->whatsapp_timeout ?? 30 }},
        maskedTokenDisplay: '{{ $maskedToken }}',
        saveFeedbackMessage: '',
        saveFeedbackType: 'success',

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

        async checkStatus() {
            this.isCheckingStatus = true;
            try {
                const res = await fetch('{{ route('app.whatsapp.settings.status') }}', {
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
                const res = await fetch(routeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
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
                const res = await fetch('{{ route('app.whatsapp.settings.qr') }}', {
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
                    body: JSON.stringify({ phone: this.pairingPhone })
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
                        message: this.testMessage
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
    }" class="flex flex-col gap-6 max-w-7xl mx-auto w-full">

        {{-- ================= 1. رأس الصفحة (Page Header Card) ================= --}}
        <div class="px-2 sm:px-4">
            <div class="relative bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 overflow-hidden pt-12 pb-6 px-6 text-center">

                {{-- الشريط اللوني العلوي الزمردي المتناسق --}}
                <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-r from-emerald-500/20 via-emerald-500/10 to-teal-500/5"></div>

                {{-- أزرار التنقل السريعة --}}
                {{-- <div class="absolute top-4 left-4 flex items-center gap-2">
                    <a href="{{ route('app.sms.settings') }}"
                        title="إعدادات الرسائل النصية SMS"
                        class="hidden sm:flex justify-center items-center px-3.5 py-1.5 rounded-full backdrop-blur-md transition-all bg-white/80 hover:bg-white dark:bg-boxdark/80 dark:hover:bg-boxdark text-slate-700 dark:text-gray-200 hover:text-primary text-xs font-bold gap-1.5 shadow-sm border border-slate-100 dark:border-gray-700 active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">sms</span>
                        <span>إعدادات SMS</span>
                    </a>
                    <a href="{{ route('app.settings') }}"
                        title="العودة لإعدادات الشركة"
                        class="flex justify-center items-center px-3.5 py-1.5 rounded-full backdrop-blur-md transition-all bg-white/80 hover:bg-white dark:bg-boxdark/80 dark:hover:bg-boxdark text-slate-700 dark:text-gray-200 hover:text-primary text-xs font-bold gap-1.5 shadow-sm border border-slate-100 dark:border-gray-700 active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        <span>إعدادات الشركة</span>
                    </a>
                </div> --}}

                {{-- أيقونة الهيدر المركزية للواتساب --}}
                <div class="relative mx-auto w-24 h-24 rounded-[1.5rem] bg-white dark:bg-boxdark shadow-lg border-4 border-white dark:border-boxdark p-1 z-10 -mt-16 mb-4">
                    <div class="flex overflow-hidden justify-center items-center w-full h-full rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                        <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">chat</span>
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

                <p class="max-w-2xl mx-auto text-xs sm:text-sm font-medium text-slate-500 dark:text-gray-400 mb-6 leading-relaxed">
                    إدارة بوابة وخدمة WhatsApp الخاصة بـ <span class="font-bold text-slate-700 dark:text-gray-200">{{ $company->name }}</span>، وربط الجلسة ورمز الاقتران واختبار الإرسال المباشر للمكتب بصورة مستقلة.
                </p>

                
            </div>
        </div>

        {{-- تنبيه تفاعلي عند تنفيذ عمليات الجلسة --}}
        <div x-cloak x-show="sessionFeedbackMessage" x-transition.opacity.duration.300ms class="px-2 sm:px-4">
            <div class="p-4 rounded-2xl flex items-start gap-3 border shadow-sm"
                :class="sessionFeedbackType === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800' : 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800'">
                <span class="material-symbols-outlined text-xl shrink-0 mt-0.5" x-text="sessionFeedbackType === 'success' ? 'check_circle' : 'error'"></span>
                <div class="flex-1 text-xs font-bold leading-relaxed" x-text="sessionFeedbackMessage"></div>
                <button type="button" @click="sessionFeedbackMessage = ''" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </div>

        {{-- ================= شبكة الأقسام الرئيسية (Grid) ================= --}}
        <div class="px-2 sm:px-4 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- ================= العمود الأول (اليمين في RTL: الإعدادات وربط الحساب) ================= --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-bl-[5rem] pointer-events-none"></div>

                    <div class="flex items-center gap-3 mb-6 relative">
                        <div class="flex justify-center items-center w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">tune</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-black font-headline text-slate-800 dark:text-white">إعدادات بوابة WhatsApp</h2>
                            <p class="text-xs font-bold text-slate-400 dark:text-gray-400">ضبط معرّف الـ Instance ومفتاح الوصول وعنوان الخادم الخاص بمكتبك</p>
                        </div>
                    </div>

                    
                    <div x-cloak x-show="saveFeedbackMessage" x-transition.opacity.duration.300ms class="mb-4">
                        <div class="p-3.5 rounded-2xl flex items-center gap-2.5 text-xs font-bold"
                            :class="saveFeedbackType === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800' : 'bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800'">
                            <span class="material-symbols-outlined text-lg shrink-0" x-text="saveFeedbackType === 'success' ? 'check_circle' : 'error'"></span>
                            <span class="flex-1 leading-relaxed" x-text="saveFeedbackMessage"></span>
                        </div>
                    </div>

                    <form action="{{ route('app.whatsapp.settings.update') }}" method="POST" @submit.prevent="saveSettings()" class="space-y-5">
                        @csrf
                        @method('PUT')

                    
                        <div>
                            <div class="flex justify-between items-center px-1 mb-1.5">
                                <label for="whatsapp_base_url" class="text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">عنوان API الأساسي (Base URL)</label>
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-gray-800 px-2 py-0.5 rounded-md">اتركه فارغاً للاعتماد على سيرفر النظام الافتراضي</span>
                            </div>
                            <div class="relative flex items-center">
                                <input type="url"
                                    id="whatsapp_base_url"
                                    name="whatsapp_base_url"
                                    x-model="baseUrlInput"
                                    dir="ltr"
                                    placeholder="{{ $effectiveBaseUrl }}"
                                    class="w-full h-12 px-4 pr-11 text-xs sm:text-sm font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-boxdark focus:ring-2 focus:ring-emerald-500/20">
                                <div class="absolute right-3.5 pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">dns</span>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-gray-500 mt-1 px-1">
                                السيرفر النشط الحالي: <code class="font-mono text-emerald-600 dark:text-emerald-400 font-bold" dir="ltr">{{ $effectiveBaseUrl }}</code>
                            </p>
                        </div>

                        
                        <div>
                            <div class="flex justify-between items-center px-1 mb-1.5">
                                <label for="whatsapp_instance_id" class="text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">
                                    معرّف الـ Instance (Instance ID / Name)
                                    <span class="text-rose-500">*</span>
                                </label>
                                <template x-if="instanceIdInput">
                                    <span class="text-[11px] font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md"
                                        x-text="'الحالي: ' + instanceIdInput"></span>
                                </template>
                            </div>
                            <div class="relative flex items-center">
                                <input type="text"
                                    id="whatsapp_instance_id"
                                    name="whatsapp_instance_id"
                                    x-model="instanceIdInput"
                                    dir="ltr"
                                    placeholder="مثال: company_{{ $company->id }}_instance"
                                    class="w-full h-12 px-4 pr-11 text-xs sm:text-sm font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-boxdark focus:ring-2 focus:ring-emerald-500/20">
                                <div class="absolute right-3.5 pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">badge</span>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-gray-500 mt-1 px-1">
                                اسم أو معرّف الجلسة المستقلة الخاصة بمكتبك في سيرفر Evolution API.
                            </p>
                        </div>

                        
                        <div>
                            <div class="flex justify-between items-center px-1 mb-1.5">
                                <label for="whatsapp_instance_token" class="text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">
                                    رمز الوصول السري (Instance Token / Apikey)
                                    <template x-if="!maskedTokenDisplay">
                                        <span class="text-rose-500">*</span>
                                    </template>
                                </label>
                                <template x-if="maskedTokenDisplay">
                                    <span class="text-[11px] font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md border border-emerald-100 dark:border-emerald-800"
                                        x-text="'المحفوظ: ' + maskedTokenDisplay"></span>
                                </template>
                            </div>

                            <div class="relative flex items-center">
                                <input :type="showToken ? 'text' : 'password'"
                                    id="whatsapp_instance_token"
                                    name="whatsapp_instance_token"
                                    x-model="instanceTokenInput"
                                    dir="ltr"
                                    autocomplete="new-password"
                                    placeholder="•••••••••••••••• (محفوظ مسبقاً، اتركه فارغاً للإبقاء عليه)"
                                    class="w-full h-12 px-4 pl-12 pr-11 text-xs sm:text-sm rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-boxdark focus:ring-2 focus:ring-emerald-500/20 font-mono">

                                <div class="absolute right-3.5 pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">key</span>
                                </div>

                                <button type="button" @click="showToken = !showToken"
                                    class="absolute left-3 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-gray-200 transition-colors">
                                    <span class="material-symbols-outlined text-[20px]" x-text="showToken ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>

                            <p class="text-[11px] text-slate-400 dark:text-gray-500 mt-1.5 px-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">lock</span>
                                <span>يتم إرساله في ترويسة <code class="font-mono text-emerald-600 font-bold">Apikey</code>. الرمز مشفر ولن يتم استبداله إذا ترك الحقل فارغاً.</span>
                            </p>
                        </div>

                       
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label for="whatsapp_webhook_url" class="block text-xs font-bold text-slate-600 dark:text-gray-300 font-headline mb-1.5 px-1">
                                    رابط الويب هوك (Webhook URL)
                                    <span class="text-[10px] text-slate-400 font-normal">(اختياري)</span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="url"
                                        id="whatsapp_webhook_url"
                                        name="whatsapp_webhook_url"
                                        x-model="webhookUrlInput"
                                        dir="ltr"
                                        placeholder="https://example.com/api/webhook"
                                        class="w-full h-12 px-4 pr-11 text-xs font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/20">
                                    <div class="absolute right-3.5 pointer-events-none text-slate-400">
                                        <span class="material-symbols-outlined text-[20px]">webhook</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="whatsapp_timeout" class="block text-xs font-bold text-slate-600 dark:text-gray-300 font-headline mb-1.5 px-1">
                                    المهلة الزمنية بالثواني (Timeout)
                                </label>
                                <div class="relative flex items-center">
                                    <input type="number"
                                        id="whatsapp_timeout"
                                        name="whatsapp_timeout"
                                        x-model="timeoutInput"
                                        dir="ltr"
                                        min="5"
                                        max="120"
                                        class="w-full h-12 px-4 pr-11 text-xs font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/20">
                                    <div class="absolute right-3.5 pointer-events-none text-slate-400">
                                        <span class="material-symbols-outlined text-[20px]">timer</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                       
                        <div class="pt-3">
                            <button type="submit"
                                :disabled="isSaving"
                                class="w-full h-12 flex justify-center items-center gap-2 text-sm font-bold text-white rounded-xl shadow-lg shadow-emerald-600/20 bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all disabled:opacity-50 disabled:pointer-events-none font-headline">
                                <template x-if="!isSaving">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[20px]">save</span>
                                        <span>حفظ إعدادات WhatsApp</span>
                                    </span>
                                </template>
                                <template x-if="isSaving">
                                    <span class="flex items-center gap-2">
                                        <span class="w-5 h-5 border-2 border-white rounded-full animate-spin border-t-transparent"></span>
                                        <span>جاري الحفظ...</span>
                                    </span>
                                </template>
                            </button>
                        </div>
                    </form>
                </div> --}}

                {{-- 2. بطاقة ربط الحساب (QR Code أو كود الاقتران عبر الهاتف) --}}
                <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden">

                    {{-- ===== حالة الاتصال الناجح: عرض رسالة التأكيد بدلاً من QR ===== --}}
                    <div x-show="connectionState === 'connected'" x-transition.opacity.duration.400ms x-cloak>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex justify-center items-center w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 shrink-0 shadow-sm">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">verified</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-black font-headline text-slate-800 dark:text-white">حساب WhatsApp مرتبط ✅</h2>
                                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400">الجلسة نشطة ومتصلة بنجاح — لا حاجة لمسح QR</p>
                            </div>
                        </div>

                        <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/20 border border-emerald-200/60 dark:border-emerald-800/60 text-center space-y-3">
                            <div class="flex justify-center">
                                <div class="w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center shadow-inner">
                                    <span class="material-symbols-outlined text-4xl text-emerald-600 dark:text-emerald-400" style="font-variation-settings: 'FILL' 1;">link</span>
                                </div>
                            </div>
                            <h3 class="text-sm font-black font-headline text-emerald-800 dark:text-emerald-300">الاتصال فعّال وجاهز</h3>
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 font-medium leading-relaxed max-w-sm mx-auto">
                                حساب WhatsApp مرتبط ومتصل بالسيرفر بنجاح. يمكنك إرسال الرسائل والإشعارات مباشرة.
                                <br>لإعادة ربط رقم جديد، قم بتسجيل الخروج أولاً من قسم "إدارة الجلسة".
                            </p>
                            <div class="flex justify-center items-center gap-2 pt-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_6px_rgba(16,185,129,0.5)]"></span>
                                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400" x-text="connectionText"></span>
                            </div>
                        </div>
                    </div>

                    {{-- ===== حالة عدم الاتصال: عرض QR Code و كود الاقتران ===== --}}
                    <div x-show="connectionState !== 'connected'" x-transition.opacity.duration.400ms>
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="flex justify-center items-center w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 shrink-0 shadow-sm">
                                    <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">qr_code_scanner</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-black font-headline text-slate-800 dark:text-white">ربط حساب WhatsApp</h2>
                                    <p class="text-xs font-bold text-slate-400 dark:text-gray-400">اقتران رقم هاتف المكتب عبر QR Code أو كود الاقتران الرقمي</p>
                                </div>
                            </div>

                            {{-- تبديل التبويبات --}}
                            <div class="flex items-center p-1 bg-slate-100 dark:bg-gray-800 rounded-xl">
                                <button type="button"
                                    @click="activeLinkTab = 'qr'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all"
                                    :class="activeLinkTab === 'qr' ? 'bg-white dark:bg-boxdark text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                                    رمز QR
                                </button>
                                <button type="button"
                                    @click="activeLinkTab = 'pairing'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all"
                                    :class="activeLinkTab === 'pairing' ? 'bg-white dark:bg-boxdark text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                                    كود الاقتران
                                </button>
                            </div>
                        </div>

                        {{-- تبويب مسح QR Code --}}
                        <div x-show="activeLinkTab === 'qr'" x-transition.opacity class="space-y-4 text-center">
                            <div class="max-w-xs mx-auto p-4 rounded-2xl bg-slate-50 dark:bg-gray-800/50 border border-slate-200/80 dark:border-gray-700 flex flex-col items-center justify-center min-h-[220px]">
                                <template x-if="qrImage">
                                    <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-200">
                                        <img :src="qrImage.startsWith('data:') || qrImage.startsWith('http') ? qrImage : 'data:image/png;base64,' + qrImage"
                                            alt="WhatsApp QR Code"
                                            class="w-48 h-48 object-contain">
                                    </div>
                                </template>

                                <template x-if="!qrImage && !isFetchingQr">
                                    <div class="text-center py-6">
                                        <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-gray-600 mb-2">qr_code_2</span>
                                        <p class="text-xs text-slate-500 dark:text-gray-400 font-medium">
                                            انقر على الزر أدناه لتوليد رمز الاستجابة السريعة (QR Code) الجديد
                                        </p>
                                    </div>
                                </template>

                                <template x-if="isFetchingQr">
                                    <div class="py-8 text-center space-y-2">
                                        <div class="w-8 h-8 border-3 border-emerald-500 rounded-full animate-spin border-t-transparent mx-auto"></div>
                                        <p class="text-xs font-bold text-slate-500">جاري طلب رمز QR من السيرفر...</p>
                                    </div>
                                </template>

                                <template x-if="qrError">
                                    <div class="mt-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 font-medium">
                                        <span x-text="qrError"></span>
                                    </div>
                                </template>
                            </div>

                            <div class="flex justify-center gap-3">
                                <button type="button"
                                    @click="fetchQrCode()"
                                    :disabled="isFetchingQr || !isConfigured"
                                    class="px-5 h-11 flex items-center justify-center gap-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/20 transition-all disabled:opacity-50">
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
                            <p class="text-xs text-slate-500 dark:text-gray-400 leading-relaxed">
                                يمكنك ربط الحساب بدون كاميرا عبر إدخال رقم الهاتف المسجل في واتساب وتوليد كود مكون من 8 خانات لإدخاله في هاتفك:
                            </p>

                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="relative flex-1">
                                    <input type="tel"
                                        x-model="pairingPhone"
                                        dir="ltr"
                                        placeholder="مثال: 771234567 أو 967771234567"
                                        class="w-full h-12 px-4 pr-11 text-xs sm:text-sm font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/20">
                                    <div class="absolute right-3.5 top-3.5 pointer-events-none text-slate-400">
                                        <span class="material-symbols-outlined text-[20px]">phone</span>
                                    </div>
                                </div>

                                <button type="button"
                                    @click="requestPairingCode()"
                                    :disabled="isRequestingPairing || !isConfigured"
                                    class="h-12 px-5 flex items-center justify-center gap-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/20 transition-all disabled:opacity-50 shrink-0 font-headline">
                                    <template x-if="!isRequestingPairing">
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[18px]">pin</span>
                                            <span>طلب كود الاقتران</span>
                                        </span>
                                    </template>
                                    <template x-if="isRequestingPairing">
                                        <span class="flex items-center gap-1.5">
                                            <span class="w-4 h-4 border-2 border-white rounded-full animate-spin border-t-transparent"></span>
                                            <span>جاري الطلب...</span>
                                        </span>
                                    </template>
                                </button>
                            </div>

                            {{-- عرض كود الاقتران الناتج --}}
                            <template x-if="pairingCode">
                                <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
                                    <div>
                                        <span class="text-[11px] font-bold text-emerald-800 dark:text-emerald-300 block mb-0.5">كود الاقتران الخاص بك:</span>
                                        <span class="text-2xl font-black font-mono tracking-widest text-emerald-700 dark:text-emerald-400" x-text="pairingCode"></span>
                                    </div>
                                    <button type="button" @click="copyPairingCode()"
                                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white dark:bg-boxdark text-emerald-700 dark:text-emerald-300 shadow-sm border border-emerald-200 dark:border-emerald-700 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px]" x-text="copiedPairing ? 'check' : 'content_copy'"></span>
                                        <span x-text="copiedPairing ? 'تم النسخ!' : 'نسخ الكود'"></span>
                                    </button>
                                </div>
                            </template>

                            <template x-if="pairingError">
                                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold" x-text="pairingError"></div>
                            </template>
                        </div>
                    </div>

                </div>

            </div>

            {{-- ================= العمود الثاني (اليسار في RTL: حالة الجلسة واختبار الإرسال) ================= --}}
            <div class="lg:col-span-5 space-y-6">

                {{-- 3. بطاقة التحكم وإدارة الجلسة الحية --}}
                <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="flex justify-center items-center w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 shrink-0 shadow-sm">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">sensors</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-black font-headline text-slate-800 dark:text-white">إدارة الجلسة</h2>
                                <p class="text-xs font-bold text-slate-400 dark:text-gray-400">أوامر التحكم بجلسة WhatsApp</p>
                            </div>
                        </div>

                        {{-- زر تحديث الحالة --}}
                        <button type="button"
                            @click="checkStatus()"
                            :disabled="isCheckingStatus || !isConfigured"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-gray-700 text-xs font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-gray-800 flex items-center gap-1 transition-all disabled:opacity-50">
                            <span class="material-symbols-outlined text-[16px]" :class="isCheckingStatus ? 'animate-spin' : ''">refresh</span>
                            <span>فحص</span>
                        </button>
                    </div>

                    {{-- شريط ملخص الحالة الحالية --}}
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-800/50 border border-slate-100 dark:border-gray-700/80 space-y-2 mb-5">
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
                            class="h-11 px-3 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800 flex items-center justify-center gap-1.5 transition-all disabled:opacity-50 font-headline">
                            <span class="material-symbols-outlined text-[18px]">play_arrow</span>
                            <span>بدء الاتصال</span>
                        </button>

                        {{-- 2. إعادة الاتصال --}}
                        <button type="button"
                            @click="triggerSessionAction('reconnect', '{{ route('app.whatsapp.settings.reconnect') }}')"
                            :disabled="isOperatingSession || !isConfigured"
                            class="h-11 px-3 rounded-xl text-xs font-bold bg-sky-50 text-sky-700 hover:bg-sky-100 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800 flex items-center justify-center gap-1.5 transition-all disabled:opacity-50 font-headline">
                            <span class="material-symbols-outlined text-[18px]">sync</span>
                            <span>إعادة الاتصال</span>
                        </button>

                        {{-- 3. فصل الاتصال مؤقتاً --}}
                        <button type="button"
                            @click="triggerSessionAction('disconnect', '{{ route('app.whatsapp.settings.disconnect') }}')"
                            :disabled="isOperatingSession || !isConfigured"
                            class="h-11 px-3 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 hover:bg-amber-100 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800 flex items-center justify-center gap-1.5 transition-all disabled:opacity-50 font-headline">
                            <span class="material-symbols-outlined text-[18px]">pause</span>
                            <span>فصل مؤقت</span>
                        </button>

                        {{-- 4. تسجيل الخروج ومسح الجلسة --}}
                        <button type="button"
                            @click="if(confirm('هل أنت متأكد من تسجيل الخروج ومسح الجلسة لربط رقم جديد؟')) { triggerSessionAction('logout', '{{ route('app.whatsapp.settings.logout') }}') }"
                            :disabled="isOperatingSession || !isConfigured"
                            class="h-11 px-3 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800 flex items-center justify-center gap-1.5 transition-all disabled:opacity-50 font-headline">
                            <span class="material-symbols-outlined text-[18px]">logout</span>
                            <span>تسجيل خروج</span>
                        </button>
                    </div>
                </div>

                {{-- 4. بطاقة اختبار إرسال رسالة تجريبية --}}
                <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-6 relative">
                        <div class="flex justify-center items-center w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">send</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-black font-headline text-slate-800 dark:text-white">اختبار إرسال WhatsApp</h2>
                            <p class="text-xs font-bold text-slate-400 dark:text-gray-400">إرسال رسالة تجريبية فورية للتحقق من الجلسة</p>
                        </div>
                    </div>

                    {{-- تنبيه إذا لم تكن البيانات مكتملة --}}
                    @if(!$isConfigured)
                        <div class="mb-5 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/50 flex items-start gap-3">
                            <span class="material-symbols-outlined text-amber-500 text-xl shrink-0 mt-0.5">warning</span>
                            <div class="text-xs text-amber-800 dark:text-amber-300 font-medium leading-relaxed">
                                <span class="font-bold block mb-0.5">الإعدادات غير مكتملة:</span>
                                يرجى حفظ معرّف الـ Instance والرمز أولاً لتتمكن من إجراء اختبار الإرسال.
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

                            <div class="relative flex items-center">
                                <input type="tel"
                                    x-model="testRecipient"
                                    dir="ltr"
                                    placeholder="مثال: 771234567 أو 967771234567"
                                    class="w-full h-12 px-4 pr-11 text-xs sm:text-sm rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-boxdark focus:ring-2 focus:ring-emerald-500/20 font-mono">

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
                                    class="w-full p-3.5 text-xs sm:text-sm rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-boxdark focus:ring-2 focus:ring-emerald-500/20 leading-relaxed resize-none"></textarea>
                            </div>
                        </div>

                        {{-- زر الإرسال --}}
                        <div class="pt-2">
                            <button type="button"
                                @click="sendTestWhatsApp()"
                                :disabled="isSendingTest || !isConfigured"
                                class="w-full h-12 flex justify-center items-center gap-2 text-sm font-bold text-white rounded-xl shadow-lg shadow-emerald-600/20 bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all disabled:opacity-50 disabled:pointer-events-none font-headline">
                                <template x-if="!isSendingTest">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[20px]">send</span>
                                        <span>إرسال رسالة تجريبية</span>
                                    </span>
                                </template>
                                <template x-if="isSendingTest">
                                    <span class="flex items-center gap-2">
                                        <span class="w-5 h-5 border-2 border-white rounded-full animate-spin border-t-transparent"></span>
                                        <span>جاري محاولة الإرسال...</span>
                                    </span>
                                </template>
                            </button>
                        </div>

                        {{-- رسالة النجاح --}}
                        <div x-cloak x-show="testSuccessMessage" x-transition.opacity.duration.300ms
                            class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-start gap-3">
                            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-xl shrink-0 mt-0.5">check_circle</span>
                            <div class="text-xs text-emerald-800 dark:text-emerald-300 font-bold leading-relaxed" x-text="testSuccessMessage"></div>
                        </div>

                        {{-- رسالة الخطأ --}}
                        <div x-cloak x-show="testErrorMessage" x-transition.opacity.duration.300ms
                            class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 flex items-start gap-3">
                            <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-xl shrink-0 mt-0.5">error</span>
                            <div class="text-xs text-rose-800 dark:text-rose-300 font-bold leading-relaxed" x-text="testErrorMessage"></div>
                        </div>
                    </div>
                </div>

                {{-- 5. بطاقة الإرشادات والمعلومات --}}
                {{-- <div class="bg-gradient-to-br from-slate-50 to-white dark:from-boxdark dark:to-boxdark/80 rounded-[2rem] border border-slate-200/80 dark:border-gray-800 p-6 space-y-4">
                    <div class="flex items-center gap-2.5 text-slate-800 dark:text-white font-headline">
                        <span class="material-symbols-outlined text-emerald-600 text-xl">help</span>
                        <h3 class="text-sm font-black">إرشادات معمارية هامة</h3>
                    </div>

                    <ul class="space-y-2.5 text-xs text-slate-500 dark:text-gray-400 leading-relaxed font-medium">
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600 shrink-0 mt-0.5">check_small</span>
                            <span>كل مكتب يمتلك <strong class="text-slate-700 dark:text-gray-200">Instance مستقل</strong> بالكامل لضمان عدم تداخل الأرقام والرسائل بين المكاتب.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600 shrink-0 mt-0.5">check_small</span>
                            <span>بمجرد مسح رمز QR بنجاح، تظل الجلسة نشطة ومستقرة، ولا يلزم إعادة المسح إلا عند تسجيل الخروج يدوياً.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600 shrink-0 mt-0.5">check_small</span>
                            <span>تأكد من عدم تسجيل خروج الجهاز من تطبيق WhatsApp على الهاتف لضمان استمرار وصول الإشعارات للعملاء.</span>
                        </li>
                    </ul>
                </div> --}}

            </div>

        </div>

    </div>
@endsection
