
<div x-data="{ isSuccessModalOpen: @if (session('success') || session('success_message') || session('success_title')) true @else false @endif }">
    <!-- الغلاف الخارجي (يعمل في كل الشاشات لتوسيط العنصر) -->
    <div x-show="isSuccessModalOpen" style="display: none;"
        class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <!-- الخلفية الضبابية (Backdrop) -->
        <div class="fixed inset-0 w-full h-full backdrop-blur-sm bg-slate-900/40" @click="isSuccessModalOpen = false">
        </div>

        <!-- صندوق المودل (Responsive Box) -->
        <!-- استخدام w-full للجوال، و sm:max-w-md للكمبيوتر ليثبت عند حجم معين -->
        <div x-show="isSuccessModalOpen" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-8"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-8"
            class="relative w-full max-w-[calc(100vw-2rem)] sm:max-w-md bg-white rounded-[2rem] shadow-2xl overflow-hidden p-6 sm:p-8 border border-slate-100">

            <div class="text-center">
                <!-- الأيقونة (تصغر قليلاً في الجوال وتكبر في الكمبيوتر) -->
                <div class="flex relative z-10 justify-center items-center mb-6">
                    <svg class="text-emerald-100 w-[80px] h-[80px] sm:w-[90px] sm:h-[90px]" viewBox="0 0 90 90"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M34.364 6.85053C38.6205 -2.28351 51.3795 -2.28351 55.636 6.85053C58.0129 11.951 63.5594 14.6722 68.9556 13.3853C78.6192 11.0807 86.5743 21.2433 82.2185 30.3287C79.7862 35.402 81.1561 41.5165 85.5082 45.0122C93.3019 51.2725 90.4628 63.9451 80.7747 66.1403C75.3648 67.3661 71.5265 72.2695 71.5572 77.9156C71.6123 88.0265 60.1169 93.6664 52.3918 87.3184C48.0781 83.7737 41.9219 83.7737 37.6082 87.3184C29.8831 93.6664 18.3877 88.0266 18.4428 77.9156C18.4735 72.2695 14.6352 67.3661 9.22531 66.1403C-0.462787 63.9451 -3.30193 51.2725 4.49185 45.0122C8.84391 41.5165 10.2138 35.402 7.78151 30.3287C3.42572 21.2433 11.3808 11.0807 21.0444 13.3853C26.4406 14.6722 31.9871 11.951 34.364 6.85053Z"
                            fill="currentColor" />
                    </svg>

                    <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                        <svg class="w-8 h-8 text-emerald-500 sm:w-10 sm:h-10" viewBox="0 0 38 38" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M5.9375 19.0004C5.9375 11.7854 11.7864 5.93652 19.0014 5.93652C26.2164 5.93652 32.0653 11.7854 32.0653 19.0004C32.0653 26.2154 26.2164 32.0643 19.0014 32.0643C11.7864 32.0643 5.9375 26.2154 5.9375 19.0004ZM19.0014 2.93652C10.1296 2.93652 2.9375 10.1286 2.9375 19.0004C2.9375 27.8723 10.1296 35.0643 19.0014 35.0643C27.8733 35.0643 35.0653 27.8723 35.0653 19.0004C35.0653 10.1286 27.8733 2.93652 19.0014 2.93652ZM24.7855 17.0575C25.3713 16.4717 25.3713 15.522 24.7855 14.9362C24.1997 14.3504 23.25 14.3504 22.6642 14.9362L17.7177 19.8827L15.3387 17.5037C14.7529 16.9179 13.8031 16.9179 13.2173 17.5037C12.6316 18.0894 12.6316 19.0392 13.2173 19.625L16.657 23.0647C16.9383 23.346 17.3199 23.504 17.7177 23.504C18.1155 23.504 18.4971 23.346 18.7784 23.0647L24.7855 17.0575Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                </div>

                <!-- النصوص -->
                <h4 class="mb-2 text-xl font-bold sm:text-2xl font-headline text-slate-800">
                    {{ session('success_title') ?? 'نجاح!' }}
                </h4>
                <p class="px-2 text-sm font-medium leading-relaxed sm:text-base text-slate-500 sm:px-4">
                    {{ session('success_message') ?? 'تمت العملية بنجاح.' }}
                </p>

                <!-- زر الإغلاق -->
                <div class="flex flex-col gap-3 mt-8 sm:mt-10">
                    @if (session('whatsapp_redirect_url'))
                        <a href="{{ session('whatsapp_redirect_url') }}" target="_blank"
                            class="flex items-center justify-center gap-2 w-full sm:w-auto sm:min-w-[140px] sm:mx-auto py-3.5 sm:py-3 px-6 text-sm sm:text-base font-bold font-headline text-white rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all shadow-lg shadow-emerald-600/20">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                            </svg>
                            <span>فتح محادثة واتساب مع الوسيط</span>
                        </a>
                    @endif
                    <button @click="isSuccessModalOpen = false" type="button"
                        class="w-full sm:w-auto sm:min-w-[140px] sm:mx-auto py-3.5 sm:py-3 px-6 text-sm sm:text-base font-bold font-headline text-white rounded-2xl bg-emerald-500 hover:bg-emerald-600 active:scale-95 transition-all shadow-lg shadow-emerald-500/20">
                        {{ session('success_buttonText') ?? 'حسناً' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
