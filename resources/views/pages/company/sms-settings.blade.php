@extends('layouts.app')

@section('title', 'إعدادات الرسائل النصية SMS')
@section('Breadcrumb', 'إعدادات الرسائل النصية SMS')

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
                this.testErrorMessage = 'حدث خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.';
                this.testSuccessMessage = '';
            } finally {
                this.isSendingTest = false;
            }
        }
    }" class="flex flex-col gap-6 max-w-7xl mx-auto w-full">

        {{-- ================= 1. رأس الصفحة (Page Header Card) ================= --}}
        <div class="px-2 sm:px-4">
            <div class="relative bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 overflow-hidden pt-12 pb-6 px-6 text-center">

                {{-- الشريط اللوني العلوي المتناسق مع صفحة الشركة --}}
                <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-r from-primary/20 via-primary/10 to-primary/5"></div>

                {{-- زر العودة السريعة لصفحة إعدادات الشركة --}}
                <div class="absolute top-4 left-4 flex items-center gap-2">
                    <a href="{{ route('app.settings') }}"
                        title="العودة لإعدادات الشركة"
                        class="flex justify-center items-center px-3.5 py-1.5 rounded-full backdrop-blur-md transition-all bg-white/80 hover:bg-white dark:bg-boxdark/80 dark:hover:bg-boxdark text-slate-700 dark:text-gray-200 hover:text-primary text-xs font-bold gap-1.5 shadow-sm border border-slate-100 dark:border-gray-700 active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        <span>إعدادات الشركة</span>
                    </a>
                </div>

                {{-- أيقونة الهيدر المركزية --}}
                <div class="relative mx-auto w-24 h-24 rounded-[1.5rem] bg-white dark:bg-boxdark shadow-lg border-4 border-white dark:border-boxdark p-1 z-10 -mt-16 mb-4">
                    <div class="flex overflow-hidden justify-center items-center w-full h-full rounded-xl bg-primary/10 text-primary">
                        <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">sms</span>
                    </div>
                </div>

                {{-- عنوان الصفحة --}}
                <h1 class="flex gap-2 justify-center items-center mb-2 text-2xl font-black font-headline text-slate-800 dark:text-white">
                    إعدادات الرسائل النصية SMS
                    <span class="w-2.5 h-2.5 rounded-full {{ $isConfigured ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)] animate-pulse' : 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.6)]' }}"></span>
                </h1>

                <p class="max-w-2xl mx-auto text-xs sm:text-sm font-medium text-slate-500 dark:text-gray-400 mb-6 leading-relaxed">
                    إدارة بوابة إرسال الرسائل النصية القصيرة (SMS) التابعة لـ <span class="font-bold text-slate-700 dark:text-gray-200">{{ $company->name }}</span>، وتكوين مفاتيح الربط وتجربة الإرسال المباشر.
                </p>

                {{-- شارات الحالة والإحصائيات السريعة --}}
                <div class="flex flex-wrap justify-center items-center gap-3 pt-5 border-t border-slate-100 dark:border-gray-800">
                    {{-- حالة التكوين --}}
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold font-headline {{ $isConfigured ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-100 dark:border-amber-800' }}">
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">
                            {{ $isConfigured ? 'verified' : 'pending' }}
                        </span>
                        <span>{{ $isConfigured ? 'البيانات مهيأة ومكتملة' : 'بانتظار تهيئة مفتاح API ورقم المرسل' }}</span>
                    </div>

                    {{-- اسم الشركة التابع لها --}}
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold font-headline bg-slate-50 text-slate-600 dark:bg-gray-800 dark:text-gray-300 border border-slate-100 dark:border-gray-700">
                        <span class="material-symbols-outlined text-[18px]">business</span>
                        <span>المكتب: {{ $company->name }}</span>
                    </div>

                    {{-- البوابة المزودة --}}
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold font-headline bg-primary/10 text-primary border border-primary/20">
                        <span class="material-symbols-outlined text-[18px]">cell_tower</span>
                        <span>مزود الخدمة: HttpSms Gateway</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= شبكة الأقسام الرئيسية (Grid) ================= --}}
        <div class="px-2 sm:px-4 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- ================= 2. إعدادات بوابة SMS (Gateway Settings Form) ================= --}}
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden">
                    
                    {{-- زخرفة علوية خفيفة --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-[5rem] pointer-events-none"></div>

                    {{-- رأس البطاقة --}}
                    <div class="flex items-center gap-3 mb-6 relative">
                        <div class="flex justify-center items-center w-12 h-12 rounded-2xl bg-primary/10 text-primary shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">tune</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-black font-headline text-slate-800 dark:text-white">إعدادات بوابة الرسائل</h2>
                            <p class="text-xs font-bold text-slate-400 dark:text-gray-400">ضبط بيانات مفتاح API ورقم المرسل المعتمد</p>
                        </div>
                    </div>

                    {{-- نموذج الحفظ --}}
                    <form action="{{ route('app.sms.settings.update') }}" method="POST" @submit="isSaving = true" class="space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- 1. عنوان API الأساسي (Base URL) --}}
                        <div>
                            <div class="flex justify-between items-center px-1 mb-1.5">
                                <label class="text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">عنوان API الأساسي (Base URL)</label>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 bg-slate-100 dark:bg-gray-800 px-2 py-0.5 rounded-md">قراءة فقط (افتراضي النظام)</span>
                            </div>
                            <div class="relative flex items-center">
                                <input type="text" value="{{ $baseUrl }}" readonly dir="ltr"
                                    class="w-full h-12 px-4 pr-11 text-xs sm:text-sm font-mono text-slate-500 dark:text-gray-400 bg-slate-100 dark:bg-gray-800/60 rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 outline-none cursor-not-allowed">
                                <div class="absolute right-3.5 pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">dns</span>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-gray-500 mt-1 px-1">
                                المسار الثابت لمزود خدمة SMS المعتمد بالنظام.
                            </p>
                        </div>

                        {{-- 2. مفتاح API Key --}}
                        <div>
                            <div class="flex justify-between items-center px-1 mb-1.5">
                                <label for="sms_api_key" class="text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">
                                    مفتاح الاتصال (API Key)
                                    @if(!$company->sms_api_key)
                                        <span class="text-rose-500">*</span>
                                    @endif
                                </label>
                                @if($maskedKey)
                                    <span class="text-[11px] font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md border border-emerald-100 dark:border-emerald-800">
                                        الحالي: {{ $maskedKey }}
                                    </span>
                                @endif
                            </div>

                            <div class="relative flex items-center">
                                <input :type="showApiKey ? 'text' : 'password'"
                                    id="sms_api_key"
                                    name="sms_api_key"
                                    dir="ltr"
                                    autocomplete="new-password"
                                    placeholder="{{ $company->sms_api_key ? '•••••••••••••••• (محفوظ مسبقاً، اتركه فارغاً للإبقاء عليه)' : 'أدخل مفتاح x-api-key الخاص بحسابك' }}"
                                    class="w-full h-12 px-4 pl-12 pr-11 text-xs sm:text-sm rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-boxdark focus:ring-2 focus:ring-primary/20 font-mono">

                                {{-- أيقونة القفل --}}
                                <div class="absolute right-3.5 pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">key</span>
                                </div>

                                {{-- زر إظهار/إخفاء --}}
                                <button type="button" @click="showApiKey = !showApiKey"
                                    class="absolute left-3 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-gray-200 transition-colors">
                                    <span class="material-symbols-outlined text-[20px]" x-text="showApiKey ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>

                            <p class="text-[11px] text-slate-400 dark:text-gray-500 mt-1.5 px-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">lock</span>
                                <span>يتم إرسال هذا المفتاح في ترويسة <code class="font-mono text-primary font-bold">x-api-key</code>. لا تقلق، المفتاح محمي ولن يتم استبداله إذا تُرك فارغاً.</span>
                            </p>
                        </div>

                        {{-- 3. رقم المرسل (From Phone) --}}
                        <div>
                            <div class="flex justify-between items-center px-1 mb-1.5">
                                <label for="sms_from_phone" class="text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">
                                    رقم هاتف المرسل (From Phone)
                                    <span class="text-rose-500">*</span>
                                </label>
                            </div>

                            <div class="relative flex items-center">
                                <input type="text"
                                    id="sms_from_phone"
                                    name="sms_from_phone"
                                    dir="ltr"
                                    value="{{ old('sms_from_phone', $company->sms_from_phone) }}"
                                    placeholder="مثال: +967779522898 أو 779522898"
                                    class="w-full h-12 px-4 pr-11 text-xs sm:text-sm rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-boxdark focus:ring-2 focus:ring-primary/20 font-mono">

                                <div class="absolute right-3.5 pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">sim_card</span>
                                </div>
                            </div>

                            <p class="text-[11px] text-slate-400 dark:text-gray-500 mt-1.5 px-1">
                                رقم الشريحة المسجلة في تطبيق بوابة الرسائل على الهاتف المتصل بالبوابة.
                            </p>
                        </div>

                        {{-- زر حفظ الإعدادات --}}
                        <div class="pt-3">
                            <button type="submit"
                                :disabled="isSaving"
                                class="w-full h-12 flex justify-center items-center gap-2 text-sm font-bold text-white rounded-xl shadow-lg shadow-primary/20 bg-primary hover:opacity-95 active:scale-95 transition-all disabled:opacity-50 disabled:pointer-events-none font-headline">
                                <template x-if="!isSaving">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[20px]">save</span>
                                        <span>حفظ إعدادات SMS</span>
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
                </div>
            </div>

            {{-- ================= 3. اختبار إرسال رسالة SMS (Test SMS Card) ================= --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm border border-slate-100 dark:border-gray-800 p-6 sm:p-8 relative overflow-hidden">

                    {{-- زخرفة خفيفة --}}
                    <div class="absolute top-0 right-0 w-28 h-28 bg-emerald-500/5 rounded-bl-[4rem] pointer-events-none"></div>

                    {{-- رأس بطاقة الاختبار --}}
                    <div class="flex items-center gap-3 mb-6 relative">
                        <div class="flex justify-center items-center w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">send</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-black font-headline text-slate-800 dark:text-white">اختبار إرسال SMS</h2>
                            <p class="text-xs font-bold text-slate-400 dark:text-gray-400">إرسال رسالة تجريبية للتحقق من الاتصال</p>
                        </div>
                    </div>

                    {{-- تنبيه إذا لم تكن البيانات مكتملة --}}
                    @if(!$isConfigured)
                        <div class="mb-5 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/50 flex items-start gap-3">
                            <span class="material-symbols-outlined text-amber-500 text-xl shrink-0 mt-0.5">warning</span>
                            <div class="text-xs text-amber-800 dark:text-amber-300 font-medium leading-relaxed">
                                <span class="font-bold block mb-0.5">الإعدادات غير مكتملة:</span>
                                يرجى إدخال مفتاح API ورقم المرسل وحفظهما أولاً لتتمكن من إجراء اختبار الإرسال.
                            </div>
                        </div>
                    @endif

                    {{-- حقول الاختبار --}}
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
                                سيتم تهيئة الرقم تلقائياً بإضافة المفتاح الدولي المعتمد بالخدمة (967).
                            </p>
                        </div>

                        {{-- نص الرسالة --}}
                        <div>
                            <div class="flex justify-between items-center px-1 mb-1.5">
                                <label class="text-xs font-bold text-slate-600 dark:text-gray-300 font-headline">
                                    نص الرسالة التجريبية
                                    <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] font-mono text-slate-400" x-text="testMessage.length + ' / 500 حرف'"></span>
                            </div>

                            <div class="relative">
                                <textarea x-model="testMessage" rows="3" maxlength="500"
                                    placeholder="اكتب نص الرسالة التجريبية هنا..."
                                    class="w-full p-3.5 text-xs sm:text-sm rounded-xl border-none ring-1 ring-slate-200 dark:ring-gray-700 bg-slate-50 dark:bg-gray-800/40 text-slate-800 dark:text-white outline-none transition-all focus:bg-white dark:focus:bg-boxdark focus:ring-2 focus:ring-emerald-500/20 leading-relaxed resize-none"></textarea>
                            </div>
                        </div>

                        {{-- زر إرسال الاختبار --}}
                        <div class="pt-2">
                            <button type="button"
                                @click="sendTestSms()"
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

                        {{-- رسالة النجاح التفاعلية --}}
                        <div x-cloak x-show="testSuccessMessage" x-transition.opacity.duration.300ms
                            class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-start gap-3">
                            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-xl shrink-0 mt-0.5">check_circle</span>
                            <div class="text-xs text-emerald-800 dark:text-emerald-300 font-bold leading-relaxed" x-text="testSuccessMessage"></div>
                        </div>

                        {{-- رسالة الخطأ التفاعلية --}}
                        <div x-cloak x-show="testErrorMessage" x-transition.opacity.duration.300ms
                            class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 flex items-start gap-3">
                            <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-xl shrink-0 mt-0.5">error</span>
                            <div class="text-xs text-rose-800 dark:text-rose-300 font-bold leading-relaxed" x-text="testErrorMessage"></div>
                        </div>
                    </div>
                </div>

                {{-- ================= 4. بطاقة المعلومات والإرشادات (Help & Info Card) ================= --}}
                <div class="bg-gradient-to-br from-slate-50 to-white dark:from-boxdark dark:to-boxdark/80 rounded-[2rem] border border-slate-200/80 dark:border-gray-800 p-6 space-y-4">
                    <div class="flex items-center gap-2.5 text-slate-800 dark:text-white font-headline">
                        <span class="material-symbols-outlined text-primary text-xl">help</span>
                        <h3 class="text-sm font-black">إرشادات هامة</h3>
                    </div>

                    <ul class="space-y-2.5 text-xs text-slate-500 dark:text-gray-400 leading-relaxed font-medium">
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-primary shrink-0 mt-0.5">check_small</span>
                            <span>تأكد من نسخ مفتاح <strong class="text-slate-700 dark:text-gray-200">API Key</strong> بدقة من لوحة تحكم المزود دون مسافات إضافية.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-primary shrink-0 mt-0.5">check_small</span>
                            <span>يجب أن يكون هاتف الإرسال متصلاً بالإنترنت وتطبيق البوابة نشطاً والشريحة بها رصيد كافٍ.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-primary shrink-0 mt-0.5">check_small</span>
                            <span>قبول الطلب من البوابة يعني تسليمه لخادم الإرسال، ويعتمد استلام المستلم الفعلي على تغطية الشبكة.</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
@endsection
