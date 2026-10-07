<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/flowbite@4.0.1/dist/flowbite.min.js"></script>

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #0b1121;
        }
        
        .ribbon {
            position: absolute;
            top: 25px;
            left: -35px;
            color: #fff;
            padding: 5px 40px;
            transform: rotate(-45deg);
            font-weight: 800;
            font-size: 0.75rem;
            letter-spacing: 1px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            z-index: 20;
        }
        .ribbon-vip { background: #059669; }
        .ribbon-popular { background: #2563eb; }

        .bg-lines {
            background-image: repeating-linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.015) 0px,
                rgba(255, 255, 255, 0.015) 1px,
                transparent 1px,
                transparent 12px
            );
        }
    </style>

    <title>باقات الاشتراك | FLogistics</title>
</head>
<body class="overflow-x-hidden relative min-h-screen antialiased text-gray-300">
    
    <!-- الخلفية المؤثرة -->
    <div class="fixed inset-0 pointer-events-none bg-lines -z-20"></div>
    <div class="fixed top-[-10%] right-[-5%] w-[500px] h-[500px] bg-blue-900/30 rounded-full mix-blend-screen filter blur-[100px] pointer-events-none -z-10"></div>
    <div class="fixed bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-emerald-900/20 rounded-full mix-blend-screen filter blur-[100px] pointer-events-none -z-10"></div>

    <div class="relative z-10 py-16 md:py-24">
        <div class="container px-4 mx-auto max-w-7xl">
            
            <!-- الهيدر -->
            <div class="flex flex-col gap-4 justify-center items-center mb-16 text-center">
               <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="Logo" class="w-24 h-24 drop-shadow-2xl">
                 <h1 class="text-3xl font-black tracking-tight leading-relaxed text-white md:text-4xl">
                    ارتقِ بأعمال الشحن الخاصة بك
                </h1>
                <p class="max-w-2xl text-lg text-gray-400">باقات مرنة ومصممة خصيصاً لتلبية احتياجاتك المتطورة في عالم اللوجستيات.</p>
            </div>

            <!-- التنبيهات -->
            @if(session('info') || session('error'))
                <div class="flex justify-center items-center mb-10">
                    <div class="inline-flex items-center gap-3 px-6 py-3 text-sm font-semibold rounded-2xl border backdrop-blur-md shadow-xl
                        {{ session('error') ? 'text-red-200 bg-red-900/50 border-red-500/40' : 'text-teal-200 bg-teal-900/50 border-teal-500/40' }}">
                        <span>{{ session('error') ?? session('info') }}</span>
                    </div>
                </div>
            @endif

            <!-- الحاوية الرئيسية للباقات -->
            <div class="grid grid-cols-1 gap-8 items-center mx-auto max-w-6xl md:grid-cols-2 lg:grid-cols-3">
                @foreach($packages as $package)
                    @php
                        $isFree = $package->price == 0;
                        $isVip = str_contains(strtoupper($package->name), 'VIP') || str_contains($package->name, 'كبرى') || $package->price > 200;
                        $isPopular = !$isFree && !$isVip;

                        $isPendingThis = $pendingSubscription && $pendingSubscription->package_id == $package->id;
                        $isActiveThis = $activeSubscription && $activeSubscription->package_id == $package->id;
                        
                        $adminPhone = "967776023837";
                        $appName = auth()->user()->App->name ?? 'شركتي';
                        $waMessage = "مرحباً، قمت بطلب تفعيل باقة ({$package->name}) لشركة ({$appName}). أرجو تفعيل الحساب.";
                        $waLink = "https://wa.me/{$adminPhone}?text=" . urlencode($waMessage);

                        $checkBgClass = $isVip ? 'bg-emerald-500' : ($isFree ? 'bg-gray-500' : 'bg-blue-600');
                    @endphp

                    <!-- بطاقة الباقة -->
                    <div class="relative flex flex-col bg-[#1e293b]/80 backdrop-blur-xl rounded-[2rem] overflow-hidden transition-all duration-300 border shadow-2xl h-full
                        {{ $isPopular ? 'lg:scale-105 border-blue-500/50 shadow-[0_0_40px_-15px_rgba(37,99,235,0.4)] z-10' : 'border-white/10 hover:-translate-y-2' }}
                        {{ $isPendingThis ? 'ring-2 ring-yellow-500/50' : '' }}
                        {{ $isActiveThis ? 'ring-2 ring-emerald-500/50' : '' }}">
                        
                        @if($isVip)
                            <div class="ribbon ribbon-vip">VIP</div>
                        @elseif($isPopular)
                            <div class="ribbon ribbon-popular">الاكثر مبيعاً</div>
                        @endif

                        <div class="flex flex-col p-8 h-full md:p-10">
                            
                            <!-- رأس البطاقة -->
                            <div class="mb-8 text-center">
                                <h2 class="mb-3 text-xl font-bold text-gray-200">{{ $package->name }}</h2>
                                
                                <div class="flex gap-1 justify-center items-end mb-5 h-16">
                                    @if($isFree)
                                        <span class="text-5xl font-black tracking-tight text-white">مجاناً</span>
                                    @else
                                        <span class="text-5xl font-black tracking-tight text-white">{{ number_format($package->price) }}</span>
                                        <span class="mb-1 text-xl font-bold text-gray-400">ر.س</span>
                                    @endif
                                </div>
                                
                                <span class="inline-block px-4 py-1.5 text-xs font-semibold text-gray-300 rounded-full border bg-white/5 border-white/10">
                                    صالحة لمدة {{ $package->duration_in_days }} يوماً
                                </span>
                            </div>

                            <div class="mb-8 w-full h-px bg-gradient-to-r from-transparent to-transparent via-white/20"></div>

                            <!-- المميزات -->
                            <ul class="flex-1 mb-10 space-y-5 text-sm font-medium text-gray-300 md:text-base">
                                <li class="flex justify-between items-center">
                                    <span><strong class="text-white">{{ $package->max_branches ?: 'عدد غير محدود' }}</strong> فروع</span>
                                    <div class="flex justify-center items-center w-5 h-5 rounded-full {{ $checkBgClass }}">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </li>
                                <li class="flex justify-between items-center">
                                    <span><strong class="text-white">{{ $package->max_drivers ?: 'عدد غير محدود' }}</strong> سائقين</span>
                                    <div class="flex justify-center items-center w-5 h-5 rounded-full {{ $checkBgClass }}">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </li>
                                <li class="flex justify-between items-center">
                                    <span><strong class="text-white">{{ $package->max_shipments ?: 'غير محدود' }}</strong> طرد / شهرياً</span>
                                    <div class="flex justify-center items-center w-5 h-5 rounded-full {{ $checkBgClass }}">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </li>
                                <li class="flex justify-between items-center">
                                    <span><strong class="text-white">{{ $package->max_packages ?: 'غير محدود' }}</strong> رحلة مجمعة</span>
                                    <div class="flex justify-center items-center w-5 h-5 rounded-full {{ $checkBgClass }}">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </li>
                            </ul>

                            <!-- الأزرار -->
                            <div class="mt-auto">
                                @if($isActiveThis)
                                    <button disabled class="py-3.5 w-full text-[15px] font-bold rounded-xl bg-emerald-900/30 text-emerald-400 border border-emerald-500/30 cursor-not-allowed">
                                        باقـتـك الحالية
                                    </button>
                                @elseif($isPendingThis)
                                    <div class="flex flex-col gap-2">
                                        <button disabled class="py-3.5 w-full text-[15px] font-bold rounded-xl bg-yellow-900/30 text-yellow-400 border border-yellow-500/30 cursor-not-allowed">
                                            قيد المراجعة...
                                        </button>
                                        <a href="{{ $waLink }}" target="_blank" class="text-sm text-center text-emerald-400 underline hover:text-emerald-300 underline-offset-4">
                                            تواصل عبر الواتساب للتسريع
                                        </a>
                                    </div>
                                @else
                                    <form action="{{ route('subscription.request') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                                        <button type="submit"
                                            class="py-3.5 w-full text-[15px] font-bold rounded-xl transition-all shadow-lg border
                                            {{ $isPopular ? 'bg-[#2563eb] hover:bg-[#1d4ed8] text-white border-blue-500 shadow-blue-900/40' : 'bg-white/10 hover:bg-white/20 text-white border-white/20' }}">
                                            {{ $isFree ? 'ابدأ باقتك المجانية' : ($activeSubscription ? 'ترقية إلى هذه الباقة' : 'اشترك الآن') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>