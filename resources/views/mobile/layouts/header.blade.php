<header
    class="fixed top-0 w-full z-50 glass-nav border-b border-slate-200/10 shadow-[0_8px_24px_rgba(36,56,156,0.06)] px-6 py-4 flex flex-row items-center justify-between">
    <div class="flex items-center gap-4">
        <div
            class="relative w-12 h-12 rounded-full overflow-hidden border-2 border-primary/10 active:scale-95 transition-transform duration-200">
            <img alt="محمد السعدي" class="w-full h-full object-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCkwZIq82EuZQzF_YUiLAdjNE4SpUl2HXkg6yFUwxAOHukJ6iY2h4Rsb6khB0QvxsS3mkgPpKZHyiUT-nYbCKeSWSHhNDVugtBY6jJk0Dkh5ny0Hc5fk6eCZk2dJ9Z8xpBYJkCcaPzSKh5zXd2GeV9ZQuuHSLsVRuHhTtfEj_LwMzDxA6JtACCV0qZYq9cWjKbEOHZSHWZTmOXhdqjSoJaUXsyXklN9d1FwsPGToUjVKEC01BEZ4j-rrVQcS1BNkJeiNP7JuTbHkNo" />
        </div>
        <div class="flex flex-col">
            <span
                class="font-headline font-bold text-lg text-slate-900 tracking-tight leading-tight">{{ Auth::user()->name }}</span>
            <div class="flex items-center gap-1 text-on-surface-variant">
                <span class="material-symbols-outlined text-[16px] text-primary"
                    data-icon="location_on">location_on</span>
                <span class="text-xs font-medium">عدن، اليمن</span>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2" id="header-actions">

        <div class="relative">
            <button onclick="toggleNotifications()" id="notif-btn"
                class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 transition-colors active:scale-90 duration-200 text-slate-500 relative z-10">
                <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
            </button>
            <span
                class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full border-2 border-white animate-pulse z-10 pointer-events-none"></span>

            <div id="notif-dropdown"
                class="fixed top-16 inset-x-4 bg-white/95 backdrop-blur-2xl border border-white/50 rounded-3xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.15)] transition-all duration-300 origin-top opacity-0 invisible scale-95 translate-y-2 z-[100] flex flex-col overflow-hidden">

                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100/50 bg-slate-50/30">
                    <div class="flex items-center gap-2">
                        <h3 class="font-headline font-bold text-lg text-on-surface">الإشعارات</h3>
                        <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">2
                            جديد</span>
                    </div>
                    <button class="text-xs text-primary font-medium hover:text-primary-hover transition-colors">تحديد
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
                                <p class="text-sm font-headline font-bold text-on-surface">طرد جديد قيد الانتظار</p>
                                <span class="text-[10px] text-primary font-medium whitespace-nowrap mt-0.5">الآن</span>
                            </div>
                            <p class="text-xs text-on-surface-variant line-clamp-2 leading-relaxed">تمت إضافة طرد جديد
                                برقم #TRK-8493 بواسطة العميل أحمد، يرجى تعيين سائق.</p>
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
                                <p class="text-sm font-headline font-medium text-slate-700">تم توصيل الشحنة بنجاح</p>
                                <span class="text-[10px] text-slate-400 whitespace-nowrap mt-0.5">منذ ساعتين</span>
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">تم تسليم الشحنة للعميل في
                                الرياض وتم تحديث الحالة في النظام.</p>
                        </div>
                    </a>

                </div>

                <div class="p-3 bg-white/80 border-t border-slate-100/50">
                    <a href="#"
                        class="flex items-center justify-center w-full py-2.5 bg-slate-50 hover:bg-slate-100 text-primary text-sm font-headline font-bold rounded-xl transition-all active:scale-95">
                        عرض كل الإشعارات
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <button type="submit"
                class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-red-50 text-slate-500 hover:text-red-600 transition-colors active:scale-90 duration-200"
                title="تسجيل الخروج">
                <span class="material-symbols-outlined" data-icon="logout">logout</span>
            </button>
        </form>
    </div>

    

    
</header>