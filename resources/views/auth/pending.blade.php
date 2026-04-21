<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حساب قيد المراجعة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans">

    <div class="max-w-md w-full bg-white rounded-[2rem] shadow-xl p-8 text-center relative overflow-hidden">
        
        {{-- تأثير جمالي في الخلفية --}}
        <div class="absolute top-0 left-0 right-0 h-2 bg-amber-500"></div>

        {{-- أيقونة الانتظار --}}
        <div class="w-24 h-24 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-5xl">hourglass_top</span>
        </div>

        <h1 class="text-2xl font-black text-slate-800 mb-2">حسابك قيد المراجعة</h1>
        <p class="text-sm text-slate-500 leading-relaxed mb-8">
            مرحباً بك! تم تسجيل حساب شركتك بنجاح، ولكنه يحتاج إلى تفعيل من قبل الإدارة لتتمكن من استخدام النظام. يرجى التواصل معنا لتفعيل حسابك في أسرع وقت.
        </p>

        {{-- زر التواصل عبر الواتساب --}}
        <a href="https://wa.me/{{ $adminPhone }}?text={{ urlencode('مرحباً، قمت بتسجيل حساب جديد في النظام باسم ' . auth()->user()->App->name . ' وأرغب بتفعيله.') }}" 
           target="_blank"
           class="flex items-center justify-center gap-2 w-full bg-[#25D366] hover:bg-[#20b958] text-white font-bold py-4 rounded-xl transition-all active:scale-95 mb-4 shadow-lg shadow-green-500/30">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
            </svg>
            تواصل مع الإدارة للتفعيل
        </a>

        {{-- زر تسجيل الخروج --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-bold text-slate-400 hover:text-rose-500 transition-colors">
                تسجيل الخروج والعودة لاحقاً
            </button>
        </form>

    </div>
</body>
</html>