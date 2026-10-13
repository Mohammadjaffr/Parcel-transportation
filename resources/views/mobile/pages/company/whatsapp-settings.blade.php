@extends('mobile.layouts.app')

@section('title', 'إعدادات الواتساب (WhatsApp) - ' . $branch->name)

@section('content')
    <div x-data="{
        showToken: false,
        isSaving: false,
        isCheckingStatus: false,
        isSendingTest: false,
        isOperatingSession: false,
        isFetchingQr: false,
        isRequestingPairing: false,

        selectedBranchId: {{ $branch->id }},

        isConfigured: {{ $isConfigured ? 'true' : 'false' }},
        connectionState: 'unknown',
        connectionText: 'جاري فحص الاتصال...',

        activeLinkTab: 'qr',
        qrImage: null,
        qrError: '',
        pairingPhone: '',
        pairingCode: null,
        pairingError: '',
        copiedPairing: false,

        testRecipient: '',
        testMessage: 'رسالة تجريبية لاختبار ربط واتساب بنجاح من نظام مرسل.',
        testSuccessMessage: '',
        testErrorMessage: '',
        sessionFeedback: '',
        sessionFeedbackType: 'success',

        baseUrlInput: '{{ $branch->whatsapp_base_url }}',
        instanceIdInput: '{{ $branch->whatsapp_instance_id }}',
        instanceTokenInput: '',
        webhookUrlInput: '{{ $branch->whatsapp_webhook_url }}',
        timeoutInput: {{ $branch->whatsapp_timeout ?? 30 }},
        maskedTokenDisplay: '{{ $maskedToken }}',
        saveFeedbackMsg: '',
        saveFeedbackType: 'success',

        buildUrl(base) {
            return base + (base.includes('?') ? '&' : '?') + 'branch_id=' + this.selectedBranchId;
        },

        switchBranch(branchId) {
            window.location.href = '{{ route('app.whatsapp.settings') }}?branch_id=' + branchId;
        },

        async saveSettings() {
            this.isSaving = true;
            this.saveFeedbackMsg = '';
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
                        whatsapp_timeout: this.timeoutInput,
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.saveFeedbackType = 'success';
                    this.saveFeedbackMsg = data.message || 'تم الحفظ بنجاح.';
                    this.isConfigured = data.is_configured;
                    if (data.masked_token) {
                        this.maskedTokenDisplay = data.masked_token;
                    }
                    this.instanceTokenInput = '';
                    this.checkStatus();
                } else {
                    this.saveFeedbackType = 'error';
                    this.saveFeedbackMsg = data.error || 'فشل حفظ الإعدادات.';
                }
            } catch (e) {
                this.saveFeedbackType = 'error';
                this.saveFeedbackMsg = 'خطأ في الاتصال بالخادم.';
            } finally {
                this.isSaving = false;
            }
        },

        init() {
            if (this.isConfigured) {
                this.checkStatus();
            } else {
                this.connectionState = 'not_configured';
                this.connectionText = 'بانتظار تهيئة البيانات';
            }
        },

        async checkStatus() {
            this.isCheckingStatus = true;
            try {
                const res = await fetch(this.buildUrl('{{ route('app.whatsapp.settings.status') }}'), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    const st = (data.state || '').toLowerCase();
                    if (st === 'open' || st === 'connected' || data.is_logged_in) {
                        this.connectionState = 'connected';
                        this.connectionText = 'متصل بنجاح' + (data.account_name ? ' (' + data.account_name + ')' : '');
                        this.qrImage = null;
                        this.qrError = '';
                    } else if (st === 'connecting') {
                        this.connectionState = 'connecting';
                        this.connectionText = 'جاري الاتصال...';
                    } else {
                        this.connectionState = 'disconnected';
                        this.connectionText = 'غير متصل';
                        if (!this.qrImage && !this.isFetchingQr) {
                            this.fetchQrCode();
                        }
                    }
                } else {
                    this.connectionState = 'error';
                    this.connectionText = data.error || 'خطأ في الاتصال';
                }
            } catch (e) {
                this.connectionState = 'error';
                this.connectionText = 'تعذر الوصول للسيرفر';
            } finally {
                this.isCheckingStatus = false;
            }
        },

        async triggerSessionAction(actionName, routeUrl) {
            this.isOperatingSession = true;
            this.sessionFeedback = '';
            try {
                const res = await fetch(routeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        branch_id: this.selectedBranchId
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.sessionFeedbackType = 'success';
                    this.sessionFeedback = data.message || `تمت العملية بنجاح.`;
                    await this.checkStatus();
                    if (actionName === 'connect') {
                        this.fetchQrCode();
                    }
                } else {
                    this.sessionFeedbackType = 'error';
                    this.sessionFeedback = data.error || `فشل التنفيذ.`;
                }
            } catch (e) {
                this.sessionFeedbackType = 'error';
                this.sessionFeedback = 'حدث خطأ في الاتصال.';
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
                    this.connectionText = 'متصل بنجاح';
                    this.qrError = data.message || 'الجلسة متصلة بالفعل.';
                } else {
                    this.qrError = data.error || data.message || 'لم يتم إرجاع رمز QR.';
                }
            } catch (e) {
                this.qrError = 'فشل جلب الرمز.';
            } finally {
                this.isFetchingQr = false;
            }
        },

        async requestPairingCode() {
            if (!this.pairingPhone) return;
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
                    body: JSON.stringify({
                        branch_id: this.selectedBranchId,
                        phone: this.pairingPhone
                    })
                });
                const data = await res.json();
                if (res.ok && data.success && data.code) {
                    this.pairingCode = data.code;
                } else {
                    this.pairingError = data.error || 'تعذر توليد كود الاقتران.';
                }
            } catch (e) {
                this.pairingError = 'خطأ في الاتصال بالخادم.';
            } finally {
                this.isRequestingPairing = false;
            }
        },

        copyPairingCode() {
            if (!this.pairingCode) return;
            navigator.clipboard.writeText(this.pairingCode);
            this.copiedPairing = true;
            setTimeout(() => { this.copiedPairing = false; }, 2000);
        },

        async sendTestWhatsApp() {
            if (!this.testRecipient || !this.testMessage) return;
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
                        branch_id: this.selectedBranchId,
                        recipient: this.testRecipient,
                        message: this.testMessage
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.testSuccessMessage = data.message || 'تم إرسال الرسالة بنجاح!';
                } else {
                    this.testErrorMessage = data.error || 'فشل إرسال الرسالة التجريبية.';
                }
            } catch (e) {
                this.testErrorMessage = 'حدث خطأ في الاتصال بالخادم.';
            } finally {
                this.isSendingTest = false;
            }
        }
    }" class="flex flex-col gap-5 px-3 py-4 pb-28 min-h-screen">

        {{-- رأس الصفحة مع زر الرجوع --}}
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center gap-3">
                <button onclick="history.back()"
                    class="flex justify-center items-center w-10 h-10 bg-white dark:bg-boxdark rounded-2xl shadow-sm border border-slate-100 dark:border-gray-800 text-slate-500 active:scale-90 transition-transform">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </button>
                <div>
                    <h1 class="text-base font-black font-headline text-slate-800 dark:text-white">إعدادات WhatsApp</h1>
                    <p class="text-[11px] font-bold text-slate-400">بوابة المراسلة والربط المباشر للفرع</p>
                </div>
            </div>

            <button type="button" @click="checkStatus()"
                class="flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white dark:bg-boxdark border border-slate-100 dark:border-gray-800 text-xs font-bold text-slate-600 dark:text-gray-300 active:scale-95">
                <span class="material-symbols-outlined text-[16px]" :class="isCheckingStatus ? 'animate-spin' : ''">refresh</span>
                <span>فحص</span>
            </button>
        </div>

        {{-- محدد الفرع إذا وجد أكثر من فرع --}}
        @if(isset($branches) && $branches->count() > 1)
            <div class="bg-white dark:bg-boxdark rounded-2xl p-3 shadow-sm border border-slate-100 dark:border-gray-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-gray-300">
                    <span class="material-symbols-outlined text-emerald-600 text-[18px]">store</span>
                    <span>اختر الفرع:</span>
                </div>
                <select @change="switchBranch($event.target.value)" class="text-xs font-bold py-1.5 px-3 rounded-xl bg-slate-50 dark:bg-gray-800 border-none ring-1 ring-slate-200 dark:ring-gray-700 text-slate-800 dark:text-white outline-none">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $b->id === $branch->id ? 'selected' : '' }}>
                            {{ $b->name }} {{ $b->is_main ? '(الرئيسي)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- تنبيه الجلسة --}}
        <div x-cloak x-show="sessionFeedback" class="p-3 rounded-2xl text-xs font-bold flex items-center gap-2"
            :class="sessionFeedbackType === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200'">
            <span class="material-symbols-outlined text-base" x-text="sessionFeedbackType === 'success' ? 'check_circle' : 'error'"></span>
            <span class="flex-1" x-text="sessionFeedback"></span>
        </div>

        {{-- بطاقة الهيدر وحالة الجلسة --}}
        <div class="relative bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-5 text-center overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-r from-emerald-500/20 to-teal-500/5"></div>

            <div class="relative mx-auto w-16 h-16 rounded-2xl bg-white dark:bg-boxdark shadow-md border-2 border-white dark:border-boxdark p-1 z-10 -mt-2 mb-3">
                <div class="flex justify-center items-center w-full h-full rounded-xl bg-emerald-50 text-emerald-600">
                    <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">chat</span>
                </div>
            </div>

            <h2 class="text-base font-black font-headline text-slate-800 dark:text-white mb-1">
                {{ $branch->name }}
            </h2>
            <p class="text-xs font-semibold text-slate-400 mb-2">
                {{ $company->name }}
            </p>

            <div class="flex flex-wrap justify-center items-center gap-2 mt-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-bold font-headline"
                    :class="{
                        'bg-emerald-50 text-emerald-700 border border-emerald-100': connectionState === 'connected',
                        'bg-amber-50 text-amber-700 border border-amber-100': connectionState === 'connecting',
                        'bg-rose-50 text-rose-700 border border-rose-100': connectionState === 'error',
                        'bg-slate-100 text-slate-600 border border-slate-200': connectionState === 'disconnected' || connectionState === 'not_configured'
                    }">
                    <span class="w-2 h-2 rounded-full"
                        :class="{
                            'bg-emerald-500 animate-pulse': connectionState === 'connected',
                            'bg-amber-500': connectionState === 'connecting',
                            'bg-rose-500': connectionState === 'error',
                            'bg-slate-400': connectionState === 'disconnected' || connectionState === 'not_configured'
                        }"></span>
                    <span x-text="connectionText"></span>
                </span>
            </div>
        </div>

        {{-- بطاقة إعدادات API --}}
        <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-5 space-y-4">
            <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100 dark:border-gray-800">
                <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600">
                    <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">tune</span>
                </div>
                <div>
                    <h3 class="text-sm font-black font-headline text-slate-800 dark:text-white">إعدادات الـ Instance للفرع</h3>
                    <p class="text-[10px] font-bold text-slate-400">بيانات الربط مع خادم WhatsApp لهذا الفرع</p>
                </div>
            </div>

            <div x-cloak x-show="saveFeedbackMsg" class="p-3 rounded-2xl text-xs font-bold flex items-center gap-2"
                :class="saveFeedbackType === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200'">
                <span class="material-symbols-outlined text-base" x-text="saveFeedbackType === 'success' ? 'check_circle' : 'error'"></span>
                <span class="flex-1" x-text="saveFeedbackMsg"></span>
            </div>

            <form action="{{ route('app.whatsapp.settings.update') }}" method="POST" @submit.prevent="saveSettings()" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Base URL --}}
                <div>
                    <label class="block px-1 mb-1 text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">عنوان API الأساسي</label>
                    <input type="url" name="whatsapp_base_url" x-model="baseUrlInput" dir="ltr"
                        placeholder="{{ $effectiveBaseUrl }}"
                        class="w-full h-11 px-3 text-xs font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none">
                </div>

                {{-- Instance ID --}}
                <div>
                    <label class="block px-1 mb-1 text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">معرّف الـ Instance</label>
                    <input type="text" name="whatsapp_instance_id" x-model="instanceIdInput" dir="ltr"
                        placeholder="معرّف الجلسة"
                        class="w-full h-11 px-3 text-xs font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none">
                </div>

                {{-- Instance Token --}}
                <div>
                    <div class="flex justify-between items-center px-1 mb-1">
                        <label class="text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">الرمز السري (Token)</label>
                        <template x-if="maskedTokenDisplay">
                            <span class="text-[10px] font-mono text-emerald-600" x-text="maskedTokenDisplay"></span>
                        </template>
                    </div>
                    <div class="relative flex items-center">
                        <input :type="showToken ? 'text' : 'password'"
                            name="whatsapp_instance_token"
                            x-model="instanceTokenInput"
                            dir="ltr"
                            placeholder="•••••••••••••••• (محفوظ مسبقاً)"
                            class="w-full h-11 px-3 pl-10 text-xs font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none">
                        <button type="button" @click="showToken = !showToken" class="absolute left-2.5 p-1 text-slate-400">
                            <span class="material-symbols-outlined text-[18px]" x-text="showToken ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                </div>

                {{-- Webhook URL --}}
                <div>
                    <label class="block px-1 mb-1 text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">رابط الويب هوك (Webhook URL)</label>
                    <input type="url" name="whatsapp_webhook_url" x-model="webhookUrlInput" dir="ltr"
                        placeholder="https://yourdomain.com/api/whatsapp/webhook"
                        class="w-full h-11 px-3 text-xs font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none">
                </div>

                {{-- زر الحفظ --}}
                <button type="submit" :disabled="isSaving"
                    class="w-full h-11 flex justify-center items-center gap-2 text-xs font-bold text-white rounded-xl shadow-md bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all disabled:opacity-50 font-headline">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span x-text="isSaving ? 'جاري الحفظ...' : 'حفظ الإعدادات'"></span>
                </button>
            </form>
        </div>

        {{-- بطاقة التحكم بالجلسة --}}
        <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-5 space-y-3">
            <h3 class="text-xs font-bold text-slate-700 dark:text-gray-200 font-headline">أوامر التحكم بجلسة الفرع</h3>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="triggerSessionAction('connect', '{{ route('app.whatsapp.settings.connect') }}')"
                    class="h-10 px-2 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 active:scale-95">
                    بدء الاتصال
                </button>
                <button type="button" @click="triggerSessionAction('reconnect', '{{ route('app.whatsapp.settings.reconnect') }}')"
                    class="h-10 px-2 rounded-xl text-xs font-bold bg-sky-50 text-sky-700 active:scale-95">
                    إعادة الاتصال
                </button>
                <button type="button" @click="triggerSessionAction('disconnect', '{{ route('app.whatsapp.settings.disconnect') }}')"
                    class="h-10 px-2 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 active:scale-95">
                    فصل مؤقت
                </button>
                <button type="button" @click="if(confirm('تسجيل الخروج ومسح الجلسة؟')) { triggerSessionAction('logout', '{{ route('app.whatsapp.settings.logout') }}') }"
                    class="h-10 px-2 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 active:scale-95">
                    تسجيل خروج
                </button>
            </div>
        </div>

        {{-- بطاقة مسح QR / كود الاقتران --}}
        <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-5 space-y-3">
            <div class="flex justify-between items-center pb-2 border-b border-slate-100 dark:border-gray-800">
                <h3 class="text-xs font-bold text-slate-700 dark:text-gray-200 font-headline">ربط الحساب للفرع</h3>
                <div class="flex gap-1 text-[11px] font-bold">
                    <button type="button" @click="activeLinkTab = 'qr'" :class="activeLinkTab === 'qr' ? 'text-emerald-600 font-black' : 'text-slate-400'">رمز QR</button>
                    <span class="text-slate-300">|</span>
                    <button type="button" @click="activeLinkTab = 'pairing'" :class="activeLinkTab === 'pairing' ? 'text-emerald-600 font-black' : 'text-slate-400'">كود الاقتران</button>
                </div>
            </div>

            <div x-show="activeLinkTab === 'qr'" class="text-center space-y-3">
                <template x-if="qrImage">
                    <div class="mx-auto w-40 h-40 bg-white p-2 rounded-xl border border-slate-200">
                        <img :src="qrImage.startsWith('data:') || qrImage.startsWith('http') ? qrImage : 'data:image/png;base64,' + qrImage" alt="QR Code" class="w-full h-full object-contain">
                    </div>
                </template>
                <button type="button" @click="fetchQrCode()" :disabled="isFetchingQr"
                    class="px-4 h-9 rounded-xl text-xs font-bold bg-emerald-600 text-white active:scale-95">
                    <span x-text="isFetchingQr ? 'جاري التحميل...' : 'جلب رمز QR'"></span>
                </button>
                <div x-show="qrError" class="text-xs text-rose-500 font-medium" x-text="qrError"></div>
            </div>

            <div x-show="activeLinkTab === 'pairing'" class="space-y-3">
                <div class="flex gap-2">
                    <input type="tel" x-model="pairingPhone" dir="ltr" placeholder="رقم الهاتف: 771234567"
                        class="flex-1 h-10 px-3 text-xs font-mono rounded-xl ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800 text-slate-800 dark:text-white outline-none">
                    <button type="button" @click="requestPairingCode()" :disabled="isRequestingPairing"
                        class="px-3 h-10 rounded-xl text-xs font-bold bg-emerald-600 text-white shrink-0 active:scale-95">
                        طلب الكود
                    </button>
                </div>
                <template x-if="pairingCode">
                    <div class="p-3 bg-emerald-50 rounded-xl text-center border border-emerald-200 flex justify-between items-center">
                        <span class="text-base font-black font-mono tracking-widest text-emerald-700" x-text="pairingCode"></span>
                        <button type="button" @click="copyPairingCode()" class="text-xs text-emerald-800 font-bold">
                            <span x-text="copiedPairing ? 'تم النسخ!' : 'نسخ'"></span>
                        </button>
                    </div>
                </template>
                <div x-show="pairingError" class="text-xs text-rose-500 font-medium" x-text="pairingError"></div>
            </div>
        </div>

        {{-- بطاقة الرسالة التجريبية --}}
        <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-5 space-y-3">
            <h3 class="text-xs font-bold text-slate-700 dark:text-gray-200 font-headline">رسالة تجريبية</h3>
            <div class="space-y-3">
                <input type="tel" x-model="testRecipient" dir="ltr" placeholder="رقم هاتف المستلم (771234567)"
                    class="w-full h-10 px-3 text-xs font-mono rounded-xl ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800 text-slate-800 dark:text-white outline-none">
                <textarea x-model="testMessage" rows="2" placeholder="نص الرسالة..."
                    class="w-full p-2.5 text-xs rounded-xl ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800 text-slate-800 dark:text-white outline-none resize-none"></textarea>
                
                <button type="button" @click="sendTestWhatsApp()" :disabled="isSendingTest || !isConfigured"
                    class="w-full h-10 rounded-xl text-xs font-bold bg-emerald-600 text-white flex justify-center items-center gap-1.5 active:scale-95 disabled:opacity-50 font-headline">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span x-text="isSendingTest ? 'جاري الإرسال...' : 'إرسال رسالة تجريبية'"></span>
                </button>

                <div x-show="testSuccessMessage" class="p-2.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold" x-text="testSuccessMessage"></div>
                <div x-show="testErrorMessage" class="p-2.5 rounded-xl bg-rose-50 text-rose-800 text-xs font-bold" x-text="testErrorMessage"></div>
            </div>
        </div>

    </div>
@endsection
