<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حساب قيد المراجعة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- إضافة خط Cairo الاحترافي --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            blue: '#1f3e5e', /* اللون الأزرق المأخوذ من شعارك */
                            gold: '#d99e30', /* اللون الذهبي المأخوذ من شعارك */
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="flex fixed inset-0 justify-center items-center p-4 font-sans bg-slate-50 overflow-hidden">

    {{-- تأثيرات الإضاءة في الخلفية (متناسقة مع ألوان الشعار) --}}
    <div class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-brand-blue/10 rounded-full blur-3xl pointer-events-none">
    </div>
    <div class="absolute bottom-[-10%] left-[-5%] w-96 h-96 bg-brand-gold/15 rounded-full blur-3xl pointer-events-none">
    </div>

    {{-- البطاقة الرئيسية بتأثير Glassmorphism - مبنية بحيث تتقلص بناءً على مساحة الشاشة --}}
    <div
        class="w-full max-w-md bg-white/80 backdrop-blur-xl border border-white/50 rounded-3xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] p-5 sm:p-8 flex flex-col items-center justify-center relative z-10 transition-all duration-300 hover:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.15)] mx-auto">

        {{-- قسم الشعار المركزي --}}
        <div class="flex relative justify-center mb-4 sm:mb-6 w-full">
            {{-- هالة ضوئية خفيفة خلف الشعار للفت الانتباه --}}
            <div class="absolute inset-0 rounded-full blur-xl animate-pulse scale-125 bg-brand-gold/20"></div>

            {{-- الشعار الخاص بك --}}
            <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="شعار الشركة"
                class="object-contain relative z-10 w-24 sm:w-32 h-auto drop-shadow-2xl transition-transform duration-500 hover:scale-105">
        </div>

        {{-- شريط الحالة العلوي النابض --}}
        <div
            class="inline-flex gap-2 items-center px-5 py-2 mb-6 text-sm font-bold rounded-full border shadow-sm text-brand-blue bg-slate-50 border-slate-100">
            <span class="flex relative w-2.5 h-2.5">
                <span
                    class="inline-flex absolute w-full h-full rounded-full opacity-75 animate-ping bg-brand-gold"></span>
                <span class="inline-flex relative w-2.5 h-2.5 rounded-full bg-brand-gold"></span>
            </span>
            حالة الحساب
        </div>

        <h1
            class="mb-2 sm:mb-4 text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight text-brand-blue text-center">
            قيد المراجعة</h1>
        <p class="mb-5 sm:mb-8 text-xs sm:text-sm font-semibold leading-relaxed text-slate-500 text-center px-2">
            مرحباً بك! تم تسجيل حساب شركتك بنجاح. نحن نقوم حالياً بمراجعة البيانات لتفعيل حسابك، يرجى التواصل معنا
            لتسريع عملية التفعيل.
        </p>

        {{-- زر التواصل عبر الواتساب --}}
        <a href="https://wa.me/{{ $adminPhone ?? '' }}?text={{ rawurlencode('مرحباً، قمت بتسجيل حساب جديد في النظام باسم ' . (auth()->user()?->App?->name ?? 'شركتنا') . ' وأرغب بتفعيله.') }}"
            target="_blank"
            class="group flex items-center justify-center gap-2 w-full bg-[#25D366] hover:bg-[#20b958] text-white text-sm sm:text-base font-bold py-3 sm:py-3.5 px-4 rounded-xl transition-all duration-300 active:scale-95 mb-3 shadow-lg shadow-green-500/30">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 transition-transform fill-current group-hover:scale-110"
                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
            </svg>
            تواصل مع الإدارة للتفعيل
        </a>

        {{-- زر تسجيل الخروج --}}
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit"
                class="py-2.5 w-full text-xs sm:text-sm font-bold rounded-xl transition-all duration-200 text-slate-400 hover:text-brand-blue hover:bg-slate-50">
                تسجيل الخروج والعودة لاحقاً
            </button>
        </form>

    </div>
</body>

</html>