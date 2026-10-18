{{-- ======================== Global Success Modal ======================== --}}
<div x-data="{ isSuccessModalOpen: @if (session('success') || session('success_message') || session('success_title')) true @else false @endif }">
    
    {{-- نستخدم x-teleport لضمان ظهور المودال فوق كل العناصر الأخرى --}}
    <template x-teleport="body">
        <div x-cloak x-show="isSuccessModalOpen" 
             class="fixed inset-0 z-[999999] flex items-center justify-center p-4 overflow-y-auto sm:p-6"
             @keydown.escape.window="isSuccessModalOpen = false">
             
            {{-- Backdrop (الخلفية المعتمة مع تأثير بلور) --}}
            <div x-show="isSuccessModalOpen"
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 w-full h-full backdrop-blur-sm bg-gray-900/40" 
                 @click="isSuccessModalOpen = false">
            </div>

            {{-- Modal Panel (النافذة الرئيسية) --}}
            <div x-show="isSuccessModalOpen" 
                 x-transition:enter="transition ease-out duration-400"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                 class="relative p-8 w-full max-w-sm text-center bg-white rounded-3xl shadow-2xl dark:bg-boxdark" dir="rtl">
                
                {{-- الأيقونة الجمالية --}}
                <div class="flex justify-center items-center mb-6">
                    <div class="flex relative justify-center items-center w-24 h-24 bg-green-50 rounded-full dark:bg-green-500/10">
                        {{-- الخلفية الزخرفية --}}
                        <svg class="absolute text-green-100 dark:text-green-500/20 w-[90px] h-[90px] animate-[spin_10s_linear_infinite]" viewBox="0 0 90 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M34.364 6.85053C38.6205 -2.28351 51.3795 -2.28351 55.636 6.85053C58.0129 11.951 63.5594 14.6722 68.9556 13.3853C78.6192 11.0807 86.5743 21.2433 82.2185 30.3287C79.7862 35.402 81.1561 41.5165 85.5082 45.0122C93.3019 51.2725 90.4628 63.9451 80.7747 66.1403C75.3648 67.3661 71.5265 72.2695 71.5572 77.9156C71.6123 88.0265 60.1169 93.6664 52.3918 87.3184C48.0781 83.7737 41.9219 83.7737 37.6082 87.3184C29.8831 93.6664 18.3877 88.0266 18.4428 77.9156C18.4735 72.2695 14.6352 67.3661 9.22531 66.1403C-0.462787 63.9451 -3.30193 51.2725 4.49185 45.0122C8.84391 41.5165 10.2138 35.402 7.78151 30.3287C3.42572 21.2433 11.3808 11.0807 21.0444 13.3853C26.4406 14.6722 31.9871 11.951 34.364 6.85053Z" fill="currentColor" />
                        </svg>
                        
                        {{-- أيقونة الصح --}}
                        <svg class="relative z-10 w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>

                {{-- النصوص --}}
                <h4 class="mb-2 text-2xl font-black text-gray-900 dark:text-white">
                    {{ session('success_title') ?? 'نجاح!' }}
                </h4>
                
                <p class="px-4 mb-8 text-sm font-medium leading-relaxed text-gray-500 dark:text-gray-400">
                    {{ session('success_message') ?? 'تمت العملية بنجاح.' }}
                </p>

                @if (session('whatsapp_redirect_url'))
                    <a href="{{ session('whatsapp_redirect_url') }}" target="_blank"
                        class="flex items-center justify-center gap-2 mb-3 py-3.5 w-full text-sm font-bold text-white bg-emerald-500 rounded-xl transition-all duration-200 hover:bg-emerald-600 hover:shadow-lg hover:shadow-emerald-500/30 active:scale-95">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                        </svg>
                        <span>فتح محادثة واتساب مع الوسيط</span>
                    </a>
                @endif

                {{-- زر الإغلاق --}}
                <button @click="isSuccessModalOpen = false" type="button"
                        class="py-3.5 w-full text-sm font-bold text-white bg-orange-400 rounded-xl transition-all duration-200 hover:bg-orange-500 hover:shadow-lg hover:shadow-orange-500/30 active:scale-95 focus:outline-none">
                    {{ session('success_buttonText') ?? 'حسناً' }}
                </button>
            </div>
        </div>
    </template>
</div>