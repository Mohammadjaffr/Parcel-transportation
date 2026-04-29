<x-guest-layout>
    {{-- يمكنك تمرير العنوان للـ layout إذا كان يدعم ذلك --}}
    <x-slot name="title">
        التحقق من رقم الهاتف
    </x-slot>

    <div class="flex flex-col justify-center items-center px-4 min-h-screen bg-slate-50">
        
        {{-- البطاقة الرئيسية --}}
        <div class="w-full max-w-md bg-white rounded-[2rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] border border-slate-100 p-8">
            
            {{-- الأيقونة والعنوان --}}
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[32px]">mark_email_read</span>
                </div>
                <h1 class="text-2xl font-black text-slate-800 font-headline mb-2">التحقق من رقم الهاتف</h1>
                <p class="text-sm font-bold text-slate-500">
                    لقد أرسلنا كود التحقق المكون من 6 أرقام إلى الواتساب الخاص بالرقم:
                    <br>
                    <span class="inline-block mt-2 font-mono text-primary bg-primary/5 px-3 py-1 rounded-lg dir-ltr">
                        {{ session('verify_phone') }}
                    </span>
                </p>
            </div>

            {{-- عرض الأخطاء إن وجدت --}}
            @if($errors->has('otp') || $errors->has('phone'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-xl flex items-start gap-3">
                    <span class="material-symbols-outlined text-rose-500">error</span>
                    <div class="flex flex-col mt-0.5">
                        @if($errors->has('otp')) <p class="text-xs font-bold text-rose-600">{{ $errors->first('otp') }}</p> @endif
                        @if($errors->has('phone')) <p class="text-xs font-bold text-rose-600">{{ $errors->first('phone') }}</p> @endif
                    </div>
                </div>
            @endif

            {{-- رسائل النجاح (مثل إعادة الإرسال) --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-start gap-3">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    <p class="text-xs font-bold text-emerald-600 mt-0.5">{{ session('success') }}</p>
                </div>
            @endif

            {{-- ================= فورمة إدخال الـ OTP الأساسية ================= --}}
            <form action="{{ route('otp.verify') }}" method="POST" id="otp-form">
                @csrf
                <input type="hidden" name="phone" value="{{ session('verify_phone') }}">
                
                {{-- حقل إدخال مخفي يجمع الأرقام الستة --}}
                <input type="hidden" name="otp" id="final-otp">

                {{-- مربعات الإدخال التفاعلية باستخدام Alpine.js --}}
                <div x-data="otpInput()" class="flex justify-between gap-2 mb-8 dir-ltr">
                    <template x-for="(i, index) in length" :key="index">
                        <input type="text" maxlength="1"
                            x-ref="`field_${index}`"
                            @input="handleInput($event, index)"
                            @keydown.backspace="handleBackspace($event, index)"
                            @paste="handlePaste($event)"
                            class="w-12 h-14 text-center text-xl font-black font-mono text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                        >
                    </template>
                </div>

                <button type="button" onclick="submitOtp()"
                    class="w-full h-14 bg-primary text-white rounded-2xl font-black shadow-[0_8px_20px_rgba(var(--color-primary),0.2)] hover:bg-primary/90 active:scale-[0.98] transition-all">
                    تأكيد الكود
                </button>
            </form>

            {{-- فاصل --}}
            <div class="flex items-center gap-4 my-8">
                <div class="h-px bg-slate-100 flex-1"></div>
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-wider">أو</span>
                <div class="h-px bg-slate-100 flex-1"></div>
            </div>

            {{-- ================= قسم إعادة الإرسال ================= --}}
            @php
                $throttleKey = 'resend-otp:' . session('verify_phone');
                $secondsRemaining = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            @endphp

            <div x-data="{ 
                    seconds: {{ $secondsRemaining > 0 ? $secondsRemaining : 0 }},
                    get formattedTime() {
                        let m = Math.floor(this.seconds / 60);
                        let s = this.seconds % 60;
                        return m + ':' + (s < 10 ? '0' : '') + s;
                    },
                    init() {
                        if (this.seconds > 0) {
                            let interval = setInterval(() => {
                                this.seconds--;
                                if (this.seconds <= 0) {
                                    clearInterval(interval);
                                }
                            }, 1000);
                        }
                    }
                }" 
                class="text-center flex flex-col items-center gap-3">
                
                <p class="text-xs font-bold text-slate-500">لم يصلك الكود؟</p>

                <form action="" method="POST">
                    @csrf
                    
                    {{-- الزر يظهر فقط إذا انتهى الوقت --}}
                    <button type="submit" x-show="seconds === 0" x-cloak
                        class="text-sm font-black text-primary hover:text-primary/80 transition-colors underline underline-offset-4 active:scale-95">
                        إعادة إرسال الكود الآن
                    </button>

                    {{-- رسالة الانتظار تظهر والوقت يعمل --}}
                    <div x-show="seconds > 0" x-cloak
                        class="flex items-center gap-2 px-4 py-2.5 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="material-symbols-outlined text-[16px] text-primary animate-pulse">hourglass_empty</span>
                        <span class="text-xs font-bold text-slate-500">
                            طلب كود جديد متاح بعد <span x-text="formattedTime" class="font-mono text-primary font-black dir-ltr inline-block w-10 text-center"></span>
                        </span>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- سكريبت لتشغيل مربعات الـ OTP --}}
    <script>
        function otpInput() {
            return {
                length: 6,
                handleInput(e, index) {
                    let value = e.target.value;
                    if (!/^[0-9]+$/.test(value)) {
                        e.target.value = '';
                        return;
                    }
                    if (value.length === 1 && index < this.length - 1) {
                        this.$refs[`field_${index + 1}`].focus();
                    }
                },
                handleBackspace(e, index) {
                    if (e.target.value === '' && index > 0) {
                        this.$refs[`field_${index - 1}`].focus();
                    }
                },
                handlePaste(e) {
                    e.preventDefault();
                    let pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, this.length);
                    if (pastedData) {
                        for (let i = 0; i < pastedData.length; i++) {
                            this.$refs[`field_${i}`].value = pastedData[i];
                        }
                        if (pastedData.length < this.length) {
                            this.$refs[`field_${pastedData.length}`].focus();
                        } else {
                            this.$refs[`field_${this.length - 1}`].focus();
                        }
                    }
                }
            }
        }

        function submitOtp() {
            let otpString = '';
            const inputs = document.querySelectorAll('input[x-ref^="field_"]');
            inputs.forEach(input => {
                otpString += input.value;
            });
            
            document.getElementById('final-otp').value = otpString;
            document.getElementById('otp-form').submit();
        }
    </script>
</x-guest-layout>