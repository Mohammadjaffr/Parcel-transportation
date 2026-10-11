@extends('mobile.layouts.app')

@section('title', 'إعدادات الرسائل النصية SMS')

@section('content')
    <div x-data="{
        showApiKey: false,
        isSaving: false,
        isSendingTest: false,
        testRecipient: '',
        testMessage: 'رسالة تجريبية لاختبار بوابة SMS بنجاح من نظام مرسل.',
        testSuccessMessage: '',
        testErrorMessage: '',
        isConfigured: {{ $isConfigured ? 'true' : 'false' }},

        async sendTestSms() {
            if (!this.testRecipient) {
                this.testErrorMessage = 'يرجى إدخال رقم هاتف المستلم.';
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
                const response = await fetch('{{ route('app.sms.settings.test') }}', {
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

                const data = await response.json();

                if (response.ok && data.success) {
                    this.testSuccessMessage = data.message || 'تم إرسال الرسالة التجريبية بنجاح!';
                    this.testErrorMessage = '';
                } else {
                    this.testErrorMessage = data.error || (data.message || 'فشل إرسال الرسالة التجريبية.');
                    this.testSuccessMessage = '';
                }
            } catch (error) {
                this.testErrorMessage = 'حدث خطأ في الاتصال بالخادم. يرجى المحاولة لاحقاً.';
                this.testSuccessMessage = '';
            } finally {
                this.isSendingTest = false;
            }
        }
    }" class="flex flex-col gap-5 px-3 py-4 pb-28 min-h-screen">

        {{-- رأس الصفحة مع زر الرجوع --}}
        <div class="flex items-center gap-3 px-1">
            <button onclick="history.back()"
                class="flex justify-center items-center w-10 h-10 bg-white dark:bg-boxdark rounded-2xl shadow-sm border border-slate-100 dark:border-gray-800 text-slate-500 active:scale-90 transition-transform">
                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
            </button>
            <div>
                <h1 class="text-lg font-black font-headline text-slate-800 dark:text-white">إعدادات رسائل SMS</h1>
                <p class="text-[11px] font-bold text-slate-400 dark:text-gray-400">بوابة إرسال الرسائل القصيرة</p>
            </div>
        </div>

        {{-- بطاقة الهيدر وحالة البوابة --}}
        <div class="relative bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-5 text-center overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-r from-primary/20 to-primary/5"></div>

            <div class="relative mx-auto w-16 h-16 rounded-2xl bg-white dark:bg-boxdark shadow-md border-2 border-white dark:border-boxdark p-1 z-10 -mt-2 mb-3">
                <div class="flex justify-center items-center w-full h-full rounded-xl bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">sms</span>
                </div>
            </div>

            <h2 class="text-base font-black font-headline text-slate-800 dark:text-white mb-1">
                {{ $company->name }}
            </h2>

            <div class="flex justify-center items-center mt-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold font-headline {{ $isConfigured ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-100 dark:border-amber-800' }}">
                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">
                        {{ $isConfigured ? 'verified' : 'pending' }}
                    </span>
                    {{ $isConfigured ? 'البيانات مهيأة ومكتملة' : 'بانتظار ضبط البيانات' }}
                </span>
            </div>
        </div>

        {{-- بطاقة إعدادات البوابة --}}
        <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-5 space-y-4">
            <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100 dark:border-gray-800">
                <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">tune</span>
                </div>
                <div>
                    <h3 class="text-sm font-black font-headline text-slate-800 dark:text-white">بيانات البوابة</h3>
                    <p class="text-[10px] font-bold text-slate-400">مفتاح API ورقم الهاتف المرسل</p>
                </div>
            </div>

            <form action="{{ route('app.sms.settings.update') }}" method="POST" @submit="isSaving = true" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Base URL --}}
                <div>
                    <label class="block px-1 mb-1 text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">عنوان API الأساسي</label>
                    <input type="text" value="{{ $baseUrl }}" readonly dir="ltr"
                        class="w-full h-11 px-3 text-xs font-mono text-slate-500 bg-slate-100 dark:bg-gray-800/60 rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 outline-none">
                </div>

                {{-- API Key --}}
                <div>
                    <div class="flex justify-between items-center px-1 mb-1">
                        <label for="m_sms_api_key" class="text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">مفتاح API</label>
                        @if($maskedKey)
                            <span class="text-[10px] font-mono font-bold text-emerald-600">{{ $maskedKey }}</span>
                        @endif
                    </div>
                    <div class="relative flex items-center">
                        <input :type="showApiKey ? 'text' : 'password'"
                            id="m_sms_api_key"
                            name="sms_api_key"
                            dir="ltr"
                            placeholder="{{ $company->sms_api_key ? '•••••••• (محفوظ مسبقاً)' : 'أدخل مفتاح API' }}"
                            class="w-full h-11 px-3 pl-10 text-xs font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-primary/20">

                        <button type="button" @click="showApiKey = !showApiKey"
                            class="absolute left-2.5 p-1 text-slate-400">
                            <span class="material-symbols-outlined text-[18px]" x-text="showApiKey ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1 px-1">اتركه فارغاً للاحتفاظ بالمفتاح الحالي.</p>
                </div>

                {{-- From Phone --}}
                <div>
                    <label for="m_sms_from_phone" class="block px-1 mb-1 text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">رقم هاتف المرسل</label>
                    <input type="text"
                        id="m_sms_from_phone"
                        name="sms_from_phone"
                        dir="ltr"
                        value="{{ old('sms_from_phone', $company->sms_from_phone) }}"
                        placeholder="+967779522898"
                        class="w-full h-11 px-3 text-xs font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-primary/20">
                </div>

                {{-- زر الحفظ --}}
                <button type="submit"
                    :disabled="isSaving"
                    class="w-full h-11 flex justify-center items-center gap-2 text-xs font-bold text-white rounded-xl shadow-md bg-primary hover:opacity-95 active:scale-95 transition-all disabled:opacity-50 font-headline">
                    <span x-show="!isSaving" class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>حفظ الإعدادات</span>
                    </span>
                    <span x-show="isSaving" class="w-4 h-4 border-2 border-white rounded-full animate-spin border-t-transparent"></span>
                </button>
            </form>
        </div>

        {{-- بطاقة اختبار الإرسال --}}
        <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-5 space-y-4">
            <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100 dark:border-gray-800">
                <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400">
                    <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">send</span>
                </div>
                <div>
                    <h3 class="text-sm font-black font-headline text-slate-800 dark:text-white">اختبار إرسال رسالة</h3>
                    <p class="text-[10px] font-bold text-slate-400">تجربة الإرسال المباشر</p>
                </div>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block px-1 mb-1 text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">رقم المستلم</label>
                    <input type="tel"
                        x-model="testRecipient"
                        dir="ltr"
                        placeholder="771234567"
                        class="w-full h-11 px-3 text-xs font-mono rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div>
                    <label class="block px-1 mb-1 text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">نص الرسالة</label>
                    <textarea x-model="testMessage" rows="2" maxlength="500"
                        class="w-full p-3 text-xs rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/20 resize-none"></textarea>
                </div>

                <button type="button"
                    @click="sendTestSms()"
                    :disabled="isSendingTest || !isConfigured"
                    class="w-full h-11 flex justify-center items-center gap-2 text-xs font-bold text-white rounded-xl shadow-md bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all disabled:opacity-50 font-headline">
                    <span x-show="!isSendingTest" class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span>إرسال تجريبي</span>
                    </span>
                    <span x-show="isSendingTest" class="w-4 h-4 border-2 border-white rounded-full animate-spin border-t-transparent"></span>
                </button>

                <div x-cloak x-show="testSuccessMessage"
                    class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 font-bold"
                    x-text="testSuccessMessage"></div>

                <div x-cloak x-show="testErrorMessage"
                    class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-800 dark:text-rose-300 font-bold"
                    x-text="testErrorMessage"></div>
            </div>
        </div>

    </div>
@endsection
