<div x-show="sidebarToggle" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarToggle = false"
     class="fixed inset-0 z-[999] bg-black/60 backdrop-blur-sm lg:hidden">
</div>

<aside :class="sidebarToggle ? 'translate-x-0' : '{{ app()->getLocale() == 'ar' ? 'translate-x-full' : '-translate-x-full' }}'"
       class="fixed inset-y-0 {{ app()->getLocale() == 'ar' ? 'right-0' : 'left-0' }} z-[1000] flex w-72 flex-col bg-white transition-transform duration-300 ease-in-out dark:bg-boxdark lg:static lg:translate-x-0">
    
    <div class="flex items-center justify-between px-6 py-5.5 lg:py-6.5">
        <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2">
            <img src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" alt="Logo" class="h-10 w-auto" />
            <span class="text-xl font-bold text-black dark:text-white">بساط</span>
        </a>

        <button @click.stop="sidebarToggle = !sidebarToggle" class="block lg:hidden text-gray-500 hover:text-black dark:hover:text-white">
            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 8.175H2.98748L9.36248 1.6875C9.66248 1.3875 9.66248 0.9125 9.36248 0.6125C9.06248 0.3125 8.58748 0.3125 8.28748 0.6125L1.51248 7.5125C1.21248 7.8125 1.21248 8.2875 1.51248 8.5875L8.28748 15.4875C8.43748 15.6375 8.63748 15.7125 8.83748 15.7125C9.03748 15.7125 9.23748 15.6375 9.38748 15.4875C9.68748 15.1875 9.68748 14.7125 9.38748 14.4125L2.98748 7.925H19C19.4125 7.925 19.75 7.5875 19.75 7.175C19.75 6.7625 19.4125 6.425 19 6.425V8.175Z" fill=""/>
            </svg>
        </button>
    </div>

    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
        <nav class="mt-5 px-4 py-4 lg:mt-9 lg:px-6">
            <div>
                <h3 class="mb-4 ml-4 text-sm font-semibold text-bodydark2 uppercase">القائمة الرئيسية</h3>

                <ul class="mb-6 flex flex-col gap-1.5">
                    <li>
                        <a class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-graydark dark:hover:bg-meta-4 {{ request()->routeIs('dashboard.index') ? 'bg-graydark dark:bg-meta-4' : '' }}"
                           href="{{ route('dashboard.index') }}">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M16.1 1.9H1.9C1.1 1.9 0.4 2.6 0.4 3.4V14.6C0.4 15.4 1.1 16.1 1.9 16.1H16.1C16.9 16.1 17.6 15.4 17.6 14.6V3.4C17.6 2.6 16.9 1.9 16.1 1.9ZM16.1 14.6H1.9V3.4H16.1V14.6Z" fill=""/>
                                <path d="M3.7 12.4H6V5.6H3.7V12.4ZM7.9 12.4H10.1V8.2H7.9V12.4ZM12 12.4H14.3V10.1H12V12.4Z" fill=""/>
                            </svg>
                            الرئيسية
                        </a>
                    </li>
                    
                    </ul>
            </div>
        </nav>
    </div>
</aside>