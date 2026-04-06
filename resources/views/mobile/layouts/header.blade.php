<header
    class="fixed top-0 w-full z-50 glass-nav border-b border-slate-200/10 shadow-[0_8px_24px_rgba(36,56,156,0.06)] px-4 py-3 flex flex-row items-center justify-between bg-white/80 backdrop-blur-md"
    x-data="{ profileOpen: false, notifOpen: false }">

    <div class="relative">
        <button @click="profileOpen = !profileOpen; notifOpen = false"
            class="flex items-center gap-3 p-1 pr-2 rounded-full hover:bg-slate-50 transition-all active:scale-95 text-right border border-transparent hover:border-slate-100">

            <div class="relative w-11 h-11 rounded-full overflow-hidden border-2 border-primary/20 shadow-sm shrink-0">
                <img alt="{{ Auth::user()->name }}" class="w-full h-full object-cover"
                    src="{{ Auth::user()->App?->logo ? asset('storage/' . Auth::user()->App->logo) : asset('assets/image/icon_4K.png') }}" />
            </div>

            <div class="flex flex-col min-w-0">
                <span class="text-[10px] font-bold text-slate-400 mb-0.5">مرحباً بك 👋</span>
                <div class="flex items-center gap-1">
                    <span
                        class="font-headline font-bold text-sm text-slate-900 tracking-tight truncate max-w-[120px] xs:max-w-[150px]">
                        {{ Auth::user()->name }}
                    </span>
                    <span class="material-symbols-outlined text-slate-400 text-[16px] transition-transform duration-300"
                        :class="profileOpen ? 'rotate-180' : ''">expand_more</span>
                </div>
            </div>
        </button>

        <div x-show="profileOpen" @click.outside="profileOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2" x-cloak
            class="absolute top-full right-0 mt-3 w-72 bg-white/95 backdrop-blur-2xl rounded-[2rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-slate-100 overflow-hidden z-50">

            <div class="p-5 bg-gradient-to-br from-primary/10 via-primary/5 to-transparent border-b border-primary/10">
                <div class="flex gap-3 items-center mb-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-white text-primary flex items-center justify-center shadow-sm border border-primary/10 shrink-0">
                        <span class="material-symbols-outlined text-2xl"
                            style="font-variation-settings: 'FILL' 1;">domain</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black font-headline text-slate-800">
                            {{ Auth::user()->App?->name ?? 'اسم الشركة' }}
                        </h4>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5 flex items-center gap-1">
                            {{ Auth::user()->branch?->name }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 bg-white/60 p-2 rounded-xl border border-white">
                    @if(Auth::user()->type === 'admin')
                        <span
                            class="bg-primary text-white text-[10px] px-2 py-1 rounded-lg font-bold shadow-sm shadow-primary/20">مدير
                            النظام</span>
                    @else
                        <span
                            class="bg-slate-700 text-white text-[10px] px-2 py-1 rounded-lg font-bold shadow-sm shadow-slate-700/20">موظف</span>
                    @endif
                    <span class="text-xs font-bold text-slate-600 truncate">{{ Auth::user()->Branch?->name }}</span>
                </div>
            </div>

            <div class="p-2 flex flex-col gap-1">
                <a href="#"
                    class="flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-50 text-slate-600 hover:text-primary transition-colors font-headline font-bold text-sm active:scale-95">
                    <span class="material-symbols-outlined text-[20px] bg-slate-100 p-1.5 rounded-lg">person</span>
                    حسابي الشخصي
                </a>

                @if(Auth::user()->type === 'admin')
                    <a href="{{ route('app.settings') }}"
                        class="flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-50 text-slate-600 hover:text-primary transition-colors font-headline font-bold text-sm active:scale-95">
                        <span class="material-symbols-outlined text-[20px] bg-slate-100 p-1.5 rounded-lg">settings</span>
                        إعدادات الشركة
                    </a>
                @endif

                <div class="h-px bg-slate-100 my-1 mx-2"></div>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 p-3 rounded-2xl hover:bg-red-50 text-slate-600 hover:text-red-600 transition-colors font-headline font-bold text-sm active:scale-95">
                        <span
                            class="material-symbols-outlined text-[20px] bg-red-50 text-red-500 p-1.5 rounded-lg">logout</span>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-1 sm:gap-2">

        <div class="relative">
            <button @click="notifOpen = !notifOpen; profileOpen = false"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-transparent hover:bg-slate-50 transition-colors active:scale-90 duration-200 text-slate-500 hover:text-primary relative z-10">
                <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
            </button>
            <span
                class="absolute top-2 right-2.5 w-2 h-2 bg-rose-500 rounded-full border border-white animate-pulse z-10 pointer-events-none"></span>

            <div x-show="notifOpen" @click.outside="notifOpen = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2" x-cloak
                class="fixed top-[70px] inset-x-4 md:absolute md:top-full md:left-0 md:right-auto md:mt-3 md:w-80 bg-white/95 backdrop-blur-2xl border border-slate-100 rounded-[2rem] shadow-[0_30px_60px_-15px_rgba(0,0,0,0.15)] z-[100] flex flex-col overflow-hidden">

                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100/50 bg-slate-50/30">
                    <div class="flex items-center gap-2">
                        <h3 class="font-headline font-bold text-lg text-on-surface">الإشعارات</h3>
                        <span class="bg-rose-50 text-rose-500 text-[10px] font-bold px-2 py-0.5 rounded-full">2
                            جديد</span>
                    </div>
                    <button class="text-xs text-primary font-bold hover:opacity-80 transition-opacity">تحديد
                        كمقروء</button>
                </div>

                <div class="max-h-[340px] overflow-y-auto overscroll-contain flex flex-col scrollbar-hide">
                    <a href="#"
                        class="flex items-start gap-4 p-4 bg-primary/[0.03] hover:bg-primary/[0.06] transition-colors border-b border-slate-50 relative group">
                        <div class="absolute right-0 top-3 bottom-3 w-1 bg-primary rounded-l-full"></div>
                        <div
                            class="w-11 h-11 shrink-0 bg-white text-primary rounded-2xl flex items-center justify-center shadow-sm border border-primary/10 group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-[22px]"
                                style="font-variation-settings: 'FILL' 1;">inventory_2</span>
                        </div>
                        <div class="flex flex-col gap-1 w-full">
                            <div class="flex justify-between items-start w-full">
                                <p class="text-sm font-headline font-bold text-slate-800">طرد جديد قيد الانتظار</p>
                                <span class="text-[10px] text-primary font-bold whitespace-nowrap mt-0.5">الآن</span>
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">تمت إضافة طرد جديد برقم
                                #TRK-8493 بواسطة العميل أحمد، يرجى تعيين سائق.</p>
                        </div>
                    </a>

                    <a href="#"
                        class="flex items-start gap-4 p-4 hover:bg-slate-50 transition-colors border-b border-slate-50 group">
                        <div
                            class="w-11 h-11 shrink-0 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                        </div>
                        <div class="flex flex-col gap-1 w-full">
                            <div class="flex justify-between items-start w-full">
                                <p class="text-sm font-headline font-bold text-slate-700">تم توصيل الشحنة بنجاح</p>
                                <span class="text-[10px] text-slate-400 font-bold whitespace-nowrap mt-0.5">منذ
                                    ساعتين</span>
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">تم تسليم الشحنة للعميل في
                                الرياض وتم تحديث الحالة في النظام.</p>
                        </div>
                    </a>
                </div>

                <div class="p-3 bg-white/80 border-t border-slate-100/50">
                    <a href="#"
                        class="flex items-center justify-center w-full py-3 bg-slate-50 hover:bg-slate-100 text-primary text-sm font-headline font-bold rounded-xl transition-all active:scale-95">
                        عرض كل الإشعارات
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>