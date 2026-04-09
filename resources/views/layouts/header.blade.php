<header x-data="{ menuToggle: false }" 
        class="flex sticky top-0 z-40 w-full bg-white border-b transition-colors duration-300 dark:border-gray-800 dark:bg-boxdark">
    
    <div class="flex flex-col justify-between items-center grow lg:flex-row lg:px-6">
        
        <div class="flex justify-between items-center px-3 py-3 w-full border-b border-gray-200 lg:w-auto lg:border-b-0 lg:px-0 lg:py-4 dark:border-gray-800">
            
            <div class="flex gap-2 items-center sm:gap-4">
                <button @click.stop="sidebarToggle = !sidebarToggle"
                        class="z-[99999] flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-all hover:bg-gray-100 lg:h-11 lg:w-11 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800"
                        :class="sidebarToggle ? 'bg-gray-100 dark:bg-gray-800' : ''">
                    
                    <svg class="hidden fill-current lg:block" width="18" height="14" viewBox="0 0 16 12">
                        <path d="M0.58 1C0.58.58.91.25 1.33.25h13.33c.41 0 .75.33.75.75s-.33.75-.75.75H1.33C.91 1.75.58 1.41.58 1zM0.58 11c0-.41.33-.75.75-.75h13.33c.41 0 .75.33.75.75s-.33.75-.75.75H1.33c-.41 0-.75-.33-.75-.75zM1.33 5.25c-.41 0-.75.33-.75.75s.33.75.75.75h6.66c.41 0 .75-.33.75-.75s-.33-.75-.75-.75H1.33z"/>
                    </svg>
                    <svg :class="sidebarToggle ? 'hidden' : 'block lg:hidden'" class="fill-current" width="24" height="24">
                        <path d="M3.25 6c0-.41.33-.75.75-.75h16c.41 0 .75.33.75.75s-.33.75-.75.75H4c-.41 0-.75-.33-.75-.75zM3.25 18c0-.41.33-.75.75-.75h16c.41 0 .75.33.75.75s-.33.75-.75.75H4c-.41 0-.75-.33-.75-.75zM4 11.25c-.41 0-.75.33-.75.75s.33.75.75.75h8c.41 0 .75-.33.75-.75s-.33-.75-.75-.75H4z"/>
                    </svg>
                    <svg :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fill-current" width="24" height="24">
                        <path d="M6.22 7.28c-.29-.29-.29-.76 0-1.06.29-.29.77-.29 1.06 0L12 10.94l4.72-4.72c.29-.29.77-.29 1.06 0 .29.29.29.77 0 1.06L13.06 12l4.72 4.72c.29.29.29.77 0 1.06-.29.29-.77.29-1.06 0L12 13.06l-4.72 4.72c-.29.29-.77.29-1.06 0-.29-.29-.29-.77 0-1.06L10.94 12 6.22 7.28z"/>
                    </svg>
                </button>

                <a href="{{ url('/') }}" class="block lg:hidden">
                    <img src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" alt="Logo" class="w-auto h-10">
                </a>
            </div>

            <button @click.stop="menuToggle = !menuToggle"
                    class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-lg hover:bg-gray-100 lg:hidden dark:text-gray-400 dark:hover:bg-gray-800"
                    :class="menuToggle ? 'bg-gray-100 dark:bg-gray-800 text-primary' : ''">
                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24">
                    <circle cx="6" cy="12" r="1.5" />
                    <circle cx="12" cy="12" r="1.5" />
                    <circle cx="18" cy="12" r="1.5" />
                </svg>
            </button>
        </div>

        <div :class="menuToggle ? 'flex' : 'hidden'"
             class="flex-col gap-4 items-center px-5 py-4 w-full bg-white border-b border-gray-100 shadow-sm lg:flex lg:w-auto lg:flex-row lg:border-none lg:bg-transparent lg:px-0 lg:py-0 lg:shadow-none dark:border-gray-800 dark:bg-transparent">
            
            <div class="flex gap-3 items-center">
                
                <div x-data="backupManager()">
                    <template x-teleport="body">
                        <div x-show="toastVisible" 
                             x-transition:enter="transition ease-out duration-300" 
                             x-transition:enter-start="translate-y-[-20px] opacity-0"
                             x-transition:enter-end="translate-y-0 opacity-100"
                             class="fixed right-5 top-5 z-[10000] flex items-center gap-3 rounded-xl border border-primary/20 bg-white/90 p-4 shadow-2xl backdrop-blur-md dark:bg-boxdark/90">
                            <div class="flex justify-center items-center w-8 h-8 rounded-full bg-primary/10">
                                <span class="text-xl material-symbols-outlined text-primary">check_circle</span>
                            </div>
                            <span class="text-sm font-bold text-gray-800 dark:text-white" x-text="toastMessage"></span>
                        </div>
                    </template>

                    <button @click="performBackup()" :disabled="isBackingUp"
                            class="flex relative justify-center items-center w-11 h-11 text-gray-500 rounded-full border border-gray-200 transition-all hover:bg-gray-100 hover:text-primary disabled:opacity-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800"
                            title="نسخ احتياطي">
                        <span x-show="!isBackingUp" class="material-symbols-outlined text-[22px]">cloud_upload</span>
                        <svg x-show="isBackingUp" class="w-5 h-5 animate-spin text-primary" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>

                <div class="hidden w-px h-8 bg-gray-200 lg:block dark:bg-gray-700"></div>
                <span class="font-medium text-gray-600 text-theme-sm dark:text-bodydark">
                    {{ Auth::user()->branch?->name }}
                </span>
            </div>

            <div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
                <button @click.prevent="dropdownOpen = !dropdownOpen" 
                        class="flex gap-2 items-center p-1 rounded-lg transition-colors group hover:bg-gray-50 dark:hover:bg-white/5">
                    
                    <div class="flex justify-center items-center w-10 h-10 text-gray-400 bg-gray-50 rounded-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                        <span class="material-symbols-outlined">person</span>
                    </div>

                    <div class="hidden text-right lg:block">
                        <p class="font-semibold text-gray-800 text-theme-sm dark:text-white">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ Auth::user()->phone }}</p>
                    </div>

                    <svg :class="dropdownOpen && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-cloak x-show="dropdownOpen"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     class="absolute {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} mt-3 w-64 origin-top-right rounded-2xl border border-gray-200 bg-white p-2 shadow-xl dark:border-gray-800 dark:bg-boxdark">
                    
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email ?? Auth::user()->phone }}</p>
                    </div>

                    <div class="p-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-sm font-medium text-red-500 rounded-xl transition-colors hover:bg-red-50 dark:hover:bg-red-500/10">
                                <span class="text-xl text-red-500 material-symbols-outlined">logout</span>
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>

<script>
    function backupManager() {
        return {
            isBackingUp: false,
            toastVisible: false,
            toastMessage: '',
            async performBackup() {
                if (this.isBackingUp) return;
                this.isBackingUp = true;
                try {
                    const response = await fetch('{{ route('backup.upload') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    });
                    const data = await response.json();
                    this.toastMessage = data.status ? 'تم رفع النسخة الاحتياطية بنجاح' : 'فشل رفع النسخة الاحتياطية';
                } catch (error) {
                    this.toastMessage = 'حدث خطأ في الاتصال';
                } finally {
                    this.isBackingUp = false;
                    this.toastVisible = true;
                    setTimeout(() => this.toastVisible = false, 4000);
                }
            }
        }
    }
</script>