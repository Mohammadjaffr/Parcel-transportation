<header x-data="{ menuToggle: false }" class="sticky top-0 z-40 flex w-full border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="flex grow items-center justify-between px-4 py-3">
        
        <div class="flex items-center gap-3">
            <button class="text-gray-500 dark:text-gray-400" @click.stop="sidebarToggle = !sidebarToggle">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h12M4 18h16" />
                </svg>
            </button>
            
            <div class="flex flex-col">
                <span class="text-xs font-bold text-orange-500">{{ Auth::user()->branch->name }}</span>
                <span class="text-[10px] text-gray-400 leading-none">لوحة التحكم</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            
            <div x-data="backupSystem()">
                <button @click="performBackup()" :disabled="isBackingUp" 
                        class="p-2 rounded-full bg-gray-50 dark:bg-gray-800 text-gray-500 hover:text-orange-500 transition-colors">
                    <template x-if="!isBackingUp">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                    </template>
                    <template x-if="isBackingUp">
                        <svg class="w-5 h-5 animate-spin text-orange-500" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </template>
                </button>
            </div>

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-1">
                    <div class="w-8 h-8 rounded-full bg-orange-100 border border-orange-200 flex items-center justify-center text-orange-600 font-bold text-xs">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </button>
                
                <div x-show="open" @click.outside="open = false" 
                     class="absolute left-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-2 z-50">
                    <div class="px-4 py-2 border-b border-gray-50 dark:border-gray-700">
                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50">الملف الشخصي</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-right px-4 py-2 text-sm text-red-500 hover:bg-red-50">تسجيل الخروج</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header> 