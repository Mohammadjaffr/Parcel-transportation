<header class="fixed top-0 w-full z-50 glass-nav border-b border-slate-200/10 shadow-[0_8px_24px_rgba(36,56,156,0.06)] px-6 py-4 flex flex-row items-center justify-between">
    <div class="flex items-center gap-4">
        <div class="relative w-12 h-12 rounded-full overflow-hidden border-2 border-primary/10 active:scale-95 transition-transform duration-200">
            <img alt="محمد السعدي" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCkwZIq82EuZQzF_YUiLAdjNE4SpUl2HXkg6yFUwxAOHukJ6iY2h4Rsb6khB0QvxsS3mkgPpKZHyiUT-nYbCKeSWSHhNDVugtBY6jJk0Dkh5ny0Hc5fk6eCZk2dJ9Z8xpBYJkCcaPzSKh5zXd2GeV9ZQuuHSLsVRuHhTtfEj_LwMzDxA6JtACCV0qZYq9cWjKbEOHZSHWZTmOXhdqjSoJaUXsyXklN9d1FwsPGToUjVKEC01BEZ4j-rrVQcS1BNkJeiNP7JuTbHkNo"/>
        </div>
        <div class="flex flex-col">
            <span class="font-headline font-bold text-lg text-slate-900 tracking-tight leading-tight">{{ Auth::user()->name }}</span>
            <div class="flex items-center gap-1 text-on-surface-variant">
                <span class="material-symbols-outlined text-[16px] text-primary" data-icon="location_on">location_on</span>
                <span class="text-xs font-medium">عدن، اليمن</span>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2">
    

    <div class="relative">
        <button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 transition-colors active:scale-90 duration-200 text-slate-500">
            <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
        </button>
        <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full border-2 border-white"></span>
    </div>

    <form method="POST" action="{{ route('logout') }}" id="logout-form">
        @csrf
        <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-red-50 text-slate-500 hover:text-red-600 transition-colors active:scale-90 duration-200" title="تسجيل الخروج">
            <span class="material-symbols-outlined" data-icon="logout">logout</span>
        </button>
    </form>
</div>
</header>