<div x-show="sidebarOpen" 
     x-transition.opacity.duration.300ms
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 backdrop-blur-sm bg-slate-900/50 lg:hidden" x-cloak></div>

<aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
       class="flex fixed top-0 right-0 z-50 flex-col w-72 h-screen bg-white border-l shadow-2xl transition-transform duration-300 ease-in-out lg:shadow-none border-slate-200/60">
    
    <div class="flex justify-center items-center px-6 h-20 border-b shrink-0 border-slate-100">
        <a href="{{ route('superadmin.dashboard') }}" class="flex gap-3 items-center group">
            <div class="flex justify-center items-center w-10 h-10 text-white bg-gradient-to-br rounded-xl shadow-lg transition-transform from-primary to-primary-hover shadow-primary/30 group-hover:scale-105">
                <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="" class="w-10 h-10 rounded-full">
            </div>
            <span class="text-xl font-black font-headline text-slate-800">سوبر أدمن</span>
        </a>
    </div>

    <nav class="overflow-y-auto flex-1 px-4 py-6 space-y-1.5 scrollbar-hide">
        
        <a href="{{ route('superadmin.dashboard') }}" 
           class="flex gap-3 items-center px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('superadmin.dashboard') ? 'font-bold bg-primary-container text-primary shadow-sm' : 'font-medium text-slate-600 hover:bg-slate-50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' {{ request()->routeIs('superadmin.dashboard') ? '1' : '0' }};">dashboard</span>
            الرئيسية
        </a>

        <div class="pt-5 pb-2">
            <span class="px-4 text-xs font-bold tracking-wider uppercase text-slate-400">إدارة النظام</span>
        </div>

        <a href="{{ route('superadmin.offices.index') }}" 
           class="flex gap-3 items-center px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('superadmin.offices.*') ? 'font-bold bg-primary-container text-primary shadow-sm' : 'font-medium text-slate-600 hover:bg-slate-50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' {{ request()->routeIs('superadmin.offices.*') ? '1' : '0' }};">domain</span>
            المكاتب والشركات 
        </a>

        <a href="{{ route('superadmin.packages.index') }}" 
           class="flex gap-3 items-center px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('superadmin.packages.*') ? 'font-bold bg-primary-container text-primary shadow-sm' : 'font-medium text-slate-600 hover:bg-slate-50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' {{ request()->routeIs('superadmin.packages.*') ? '1' : '0' }};">inventory_2</span>
            الباقات 
        </a>

        <a href="{{ route('superadmin.subscriptions.index') }}" 
           class="flex gap-3 items-center px-4 py-3 text-sm rounded-xl transition-colors {{ request()->routeIs('superadmin.subscriptions.*') ? 'font-bold bg-primary-container text-primary shadow-sm' : 'font-medium text-slate-600 hover:bg-slate-50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' {{ request()->routeIs('superadmin.subscriptions.*') ? '1' : '0' }};">workspace_premium</span>
            الاشتراكات  
        </a>

        <div class="pt-5 pb-2">
            <span class="px-4 text-xs font-bold tracking-wider uppercase text-slate-400">أخرى</span>
        </div>

        <a href="#" class="flex gap-3 items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors text-slate-600 hover:bg-slate-50 hover:text-primary">
            <span class="material-symbols-outlined text-[22px]">settings</span>
            الإعدادات العامة
        </a>
    </nav>

    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex gap-2 justify-center items-center px-4 py-3 w-full text-sm font-bold text-red-600 bg-red-50 rounded-xl transition-all hover:bg-red-600 hover:text-white hover:shadow-md">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                تسجيل الخروج
            </button>
        </form>
    </div>
</aside>