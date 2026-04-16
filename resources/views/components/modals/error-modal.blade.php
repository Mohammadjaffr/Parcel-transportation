{{-- ================= مودال الخطأ الذكي ================= --}}
<div x-data="{ isErrorModalOpen: {{ (session()->has('error') || session()->has('error_message') || (isset($errors) && $errors->any())) ? 'true' : 'false' }} }">
    <div x-show="isErrorModalOpen" style="display: none;"
        class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        {{-- الخلفية المظللة --}}
        <div class="fixed inset-0 w-full h-full backdrop-blur-sm bg-slate-900/40" @click="isErrorModalOpen = false">
        </div>

        {{-- المودال --}}
        <div x-show="isErrorModalOpen" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-8"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-8"
            class="relative w-full max-w-[calc(100vw-2rem)] sm:max-w-md bg-white rounded-[2rem] shadow-2xl overflow-hidden p-6 sm:p-8 border border-slate-100">

            <div class="text-center">
                <div class="flex relative z-10 justify-center items-center mb-6">
                    <svg class="text-rose-100 w-[80px] h-[80px] sm:w-[90px] sm:h-[90px]" viewBox="0 0 90 90"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M34.364 6.85053C38.6205 -2.28351 51.3795 -2.28351 55.636 6.85053C58.0129 11.951 63.5594 14.6722 68.9556 13.3853C78.6192 11.0807 86.5743 21.2433 82.2185 30.3287C79.7862 35.402 81.1561 41.5165 85.5082 45.0122C93.3019 51.2725 90.4628 63.9451 80.7747 66.1403C75.3648 67.3661 71.5265 72.2695 71.5572 77.9156C71.6123 88.0265 60.1169 93.6664 52.3918 87.3184C48.0781 83.7737 41.9219 83.7737 37.6082 87.3184C29.8831 93.6664 18.3877 88.0266 18.4428 77.9156C18.4735 72.2695 14.6352 67.3661 9.22531 66.1403C-0.462787 63.9451 -3.30193 51.2725 4.49185 45.0122C8.84391 41.5165 10.2138 35.402 7.78151 30.3287C3.42572 21.2433 11.3808 11.0807 21.0444 13.3853C26.4406 14.6722 31.9871 11.951 34.364 6.85053Z"
                            fill="currentColor" />
                    </svg>

                    <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                        <svg class="w-8 h-8 text-rose-500 sm:w-10 sm:h-10" viewBox="0 0 38 38" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M9.62684 11.7496C9.04105 11.1638 9.04105 10.2141 9.62684 9.6283C10.2126 9.04252 11.1624 9.04252 11.7482 9.6283L18.9985 16.8786L26.2485 9.62851C26.8343 9.04273 27.7841 9.04273 28.3699 9.62851C28.9556 10.2143 28.9556 11.164 28.3699 11.7498L21.1198 18.9999L28.3699 26.25C28.9556 26.8358 28.9556 27.7855 28.3699 28.3713C27.7841 28.9571 26.8343 28.9571 26.2485 28.3713L18.9985 21.1212L11.7482 28.3715C11.1624 28.9573 10.2126 28.9573 9.62684 28.3715C9.04105 27.7857 9.04105 26.836 9.62684 26.2502L16.8771 18.9999L9.62684 11.7496Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                </div>

                <h4 class="mb-2 text-xl font-bold sm:text-2xl font-headline text-slate-800">
                    {{ is_string(session('error_title')) ? session('error_title') : 'تنبيه!' }}
                </h4>
                
                {{-- 💡 الفلتر الذكي لاستخراج رسالة الخطأ الحقيقية --}}
                <p class="px-2 text-sm font-medium leading-relaxed sm:text-base text-slate-500 sm:px-4">
                    @php
                        $finalErrorMessage = 'حدث خطأ ما، يرجى مراجعة البيانات والمحاولة مرة أخرى.';
                        
                        if (session()->has('error_message') && is_string(session('error_message'))) {
                            $finalErrorMessage = session('error_message');
                        } elseif (session()->has('error') && is_string(session('error'))) {
                            $finalErrorMessage = session('error');
                        } elseif (isset($errors) && $errors->any()) {
                            // التقاط أول خطأ من الفاليديشن (مثلاً: حقل الاسم مطلوب)
                            $finalErrorMessage = $errors->first();
                        }
                    @endphp
                    
                    {{ $finalErrorMessage }}
                </p>

                <div class="flex flex-col gap-3 mt-8 sm:mt-10">
                    <button @click="isErrorModalOpen = false" type="button"
                        class="w-full sm:w-auto sm:min-w-[140px] sm:mx-auto py-3.5 sm:py-3 px-6 text-sm sm:text-base font-bold font-headline text-white rounded-2xl bg-rose-500 hover:bg-rose-600 active:scale-95 transition-all shadow-lg shadow-rose-500/20">
                        {{ is_string(session('error_buttonText')) ? session('error_buttonText') : 'حسناً' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>