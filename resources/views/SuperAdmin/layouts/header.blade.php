<header class="fixed top-0 left-0 z-30 w-full px-4 py-3 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-slate-200/50 lg:w-[calc(100%-18rem)]">
    <div class="flex justify-between items-center w-full h-14">
        
        <div class="flex gap-4 items-center">
            <button @click="sidebarOpen = !sidebarOpen" class="flex justify-center items-center w-10 h-10 rounded-xl transition-colors text-slate-600 hover:bg-slate-100 lg:hidden">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <h1 class="hidden text-lg font-bold font-headline text-slate-800 sm:block">
                @yield('header_title', 'لوحة التحكم')
            </h1>
        </div>

        <div class="flex gap-2 items-center sm:gap-4" x-data="{ profileOpen: false, notifOpen: false }">
            
            <div class="relative">
                <button @click="notifOpen = !notifOpen; profileOpen = false" class="flex relative justify-center items-center w-10 h-10 rounded-full transition-colors text-slate-500 hover:bg-slate-100 hover:text-primary">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="flex absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full">
                        <span class="inline-flex absolute w-full h-full bg-rose-400 rounded-full opacity-75 animate-ping"></span>
                    </span>
                </button>

                <div x-show="notifOpen" @click.outside="notifOpen = false" x-transition x-cloak
                     class="overflow-hidden absolute left-0 z-50 mt-2 w-80 bg-white rounded-2xl border shadow-xl border-slate-100">
                    <div class="flex justify-between items-center p-4 border-b border-slate-50 bg-slate-50/50">
                        <h3 class="font-bold text-slate-800">الإشعارات</h3>
                        <span class="px-2 py-1 text-xs font-bold rounded-lg text-primary bg-primary/10">جديد</span>
                    </div>
                    <div class="p-4 text-sm text-center text-slate-500">
                        لا توجد إشعارات جديدة.
                    </div>
                </div>
            </div>

            <div class="hidden w-px h-6 bg-slate-200 sm:block"></div>

            <div class="relative">
                <button @click="profileOpen = !profileOpen; notifOpen = false" class="flex gap-3 items-center p-1 pr-2 rounded-full border border-transparent transition-all hover:bg-slate-50">
                    <div class="flex hidden flex-col items-end text-right sm:block">
                        <span class="text-xs font-bold text-slate-900">{{ Auth::user()->name ?? 'مدير النظام' }}</span>
                        <span class="text-[10px] text-slate-500">Super Admin</span>
                    </div>
                    <img alt="Profile" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=f79009&color=fff" class="object-cover w-10 h-10 rounded-full border-2 border-white shadow-sm" />
                </button>

                <div x-show="profileOpen" @click.outside="profileOpen = false" x-transition x-cloak
                     class="overflow-hidden absolute left-0 z-50 mt-2 w-56 bg-white rounded-2xl border shadow-xl border-slate-100">
                    <div class="p-4 border-b border-slate-50">
                        <p class="text-sm font-bold text-slate-800">{{ Auth::user()->name ?? 'مدير النظام' }}</p>
                        <p class="text-xs text-slate-500">{{ Auth::user()->email ?? 'admin@system.com' }}</p>
                    </div>
                    <div class="p-2">
                        <a href="#" class="flex gap-2 items-center px-3 py-2 text-sm font-medium rounded-xl transition-colors text-slate-600 hover:bg-slate-50 hover:text-primary">
                            <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                            تعديل الملف الشخصي
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>