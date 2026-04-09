<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full lg:translate-x-0'"
       class="fixed {{ app()->getLocale() == 'ar' ? 'right-0 border-l' : 'left-0 border-r' }} top-0 z-[9999] flex h-screen w-72 flex-col overflow-y-hidden border-gray-200 bg-white duration-300 ease-linear dark:border-gray-800 dark:bg-boxdark lg:static"
       @click.outside="sidebarToggle = false">

    <div class="flex justify-between items-center px-6 py-5 lg:py-6" :class="sidebarToggle ? 'lg:justify-center' : ''">
        
        <a href="{{ route('dashboard.index') }}" class="flex items-center shrink-0">
            <img class="object-contain w-auto h-10 transition-all duration-300" 
                 :class="sidebarToggle ? 'lg:hidden' : 'block'"
                 src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" 
                 alt="Logo" />

            <img class="hidden object-contain w-10 h-10 transition-all duration-300" 
                 :class="sidebarToggle ? 'lg:block' : 'hidden'"
                 src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" 
                 alt="Icon" />
        </a>

        <button @click.stop="sidebarToggle = false"
                class="flex justify-center items-center w-9 h-9 text-gray-500 bg-gray-100 rounded-lg transition-colors hover:bg-gray-200 hover:text-gray-700 lg:hidden dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    
    <div class="flex overflow-y-auto flex-col duration-300 ease-linear no-scrollbar">
        <nav class="px-4 py-4 lg:px-6" x-data="{ selected: '' }">
            
            <div>
                <h3 class="mb-4 text-xs font-semibold text-gray-400 uppercase dark:text-bodydark2" 
                    :class="sidebarToggle ? 'text-center' : ''">
                    <span :class="sidebarToggle ? 'lg:hidden' : 'block'">القائمة الرئيسية</span>
                    <span class="material-symbols-outlined mx-auto hidden text-[20px]" :class="sidebarToggle ? 'lg:block' : 'hidden'">more_horiz</span>
                </h3>

                <ul class="flex flex-col gap-1.5 mb-6">
                    
                    <li>
                        <a href="{{ route('dashboard.index') }}" 
                           class="flex relative gap-2.5 items-center px-4 py-2.5 font-medium rounded-lg transition-all duration-300 ease-in-out group"
                           :class="window.location.href.includes('{{ route('dashboard.index') }}') ? 
                                  'bg-primary/10 text-primary dark:bg-meta-4 dark:text-white' : 
                                  'text-gray-600 hover:bg-gray-100 dark:text-bodydark1 dark:hover:bg-meta-4'">
                            
                            <span class="material-symbols-outlined text-[22px] transition-colors duration-300"
                                  :class="window.location.href.includes('{{ route('dashboard.index') }}') ? 'text-primary dark:text-white' : 'text-gray-500 group-hover:text-gray-700 dark:text-bodydark2 dark:group-hover:text-white'">
                                grid_view
                            </span>
                            <span :class="sidebarToggle ? 'lg:hidden' : 'block'">الصفحة الرئيسية</span>
                        </a>
                    </li>

                    <li x-init="@if(request()->routeIs('drivers.*') || request()->routeIs('users.*') || request()->routeIs('customers.*')) selected = 'People' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'People' ? '' : 'People')"
                           class="flex relative gap-2.5 items-center px-4 py-2.5 font-medium rounded-lg transition-all duration-300 ease-in-out group"
                           :class="(selected === 'People' || {{ request()->routeIs('drivers.*', 'users.*', 'customers.*') ? 'true' : 'false' }}) ? 
                                  'bg-primary/10 text-primary dark:bg-meta-4 dark:text-white' : 
                                  'text-gray-600 hover:bg-gray-100 dark:text-bodydark1 dark:hover:bg-meta-4'">
                            
                            <span class="material-symbols-outlined text-[22px] transition-colors duration-300"
                                  :class="(selected === 'People' || {{ request()->routeIs('drivers.*', 'users.*', 'customers.*') ? 'true' : 'false' }}) ? 'text-primary dark:text-white' : 'text-gray-500 group-hover:text-gray-700 dark:text-bodydark2 dark:group-hover:text-white'">
                                group
                            </span>
                            <span :class="sidebarToggle ? 'lg:hidden' : 'block'">إدارة الأفراد</span>

                            <svg class="absolute {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} top-1/2 -translate-y-1/2 fill-current transition-transform duration-200"
                                 :class="[selected === 'People' ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '']"
                                 width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.41107 6.9107C4.73651 6.58527 5.26414 6.58527 5.58958 6.9107L10.0003 11.3214L14.4111 6.91071C14.7365 6.58527 15.2641 6.58527 15.5896 6.91071C15.915 7.23614 15.915 7.76378 15.5896 8.08922L10.5896 13.0892C10.2641 13.4147 9.73651 13.4147 9.41107 13.0892L4.41107 8.08922C4.08563 7.76378 4.08563 7.23614 4.41107 6.9107Z"/>
                            </svg>
                        </a>

                        <div x-cloak x-show="selected === 'People'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="overflow-hidden">
                            <ul class="mt-2 flex flex-col gap-2 {{ app()->getLocale() == 'ar' ? 'pr-11' : 'pl-11' }}" :class="sidebarToggle ? 'lg:hidden' : 'flex'">
                                <li>
                                    <a href="{{ route('drivers.index') }}" 
                                       class="group relative flex items-center gap-2.5 rounded-md px-2 py-1.5 font-medium transition-colors duration-300 {{ request()->routeIs('drivers.*') ? 'text-primary dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-bodydark2 dark:hover:text-white' }}">
                                        السائقين
                                    </a>
                                </li>
                                @if (Auth::user()->type != 'user')
                                <li>
                                    <a href="{{ route('users.index') }}" 
                                       class="group relative flex items-center gap-2.5 rounded-md px-2 py-1.5 font-medium transition-colors duration-300 {{ request()->routeIs('users.*') ? 'text-primary dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-bodydark2 dark:hover:text-white' }}">
                                        ادارة المستخدمين
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a href="{{ route('customers.index') }}" 
                                       class="group relative flex items-center gap-2.5 rounded-md px-2 py-1.5 font-medium transition-colors duration-300 {{ request()->routeIs('customers.*') ? 'text-primary dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-bodydark2 dark:hover:text-white' }}">
                                        العملاء
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li x-init="@if(request()->routeIs('branch.*') || request()->routeIs('offices.unverified.*')) selected = 'Offices' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'Offices' ? '' : 'Offices')"
                           class="flex relative gap-2.5 items-center px-4 py-2.5 font-medium rounded-lg transition-all duration-300 ease-in-out group"
                           :class="(selected === 'Offices' || {{ request()->routeIs('branch.*', 'offices.unverified.*') ? 'true' : 'false' }}) ? 
                                  'bg-primary/10 text-primary dark:bg-meta-4 dark:text-white' : 
                                  'text-gray-600 hover:bg-gray-100 dark:text-bodydark1 dark:hover:bg-meta-4'">
                            
                            <span class="material-symbols-outlined text-[22px] transition-colors duration-300"
                                  :class="(selected === 'Offices' || {{ request()->routeIs('branch.*', 'offices.unverified.*') ? 'true' : 'false' }}) ? 'text-primary dark:text-white' : 'text-gray-500 group-hover:text-gray-700 dark:text-bodydark2 dark:group-hover:text-white'">
                                apartment
                            </span>
                            <span :class="sidebarToggle ? 'lg:hidden' : 'block'">إدارة المكاتب</span>

                            <svg class="absolute {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} top-1/2 -translate-y-1/2 fill-current transition-transform duration-200"
                                 :class="[selected === 'Offices' ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '']"
                                 width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.41107 6.9107C4.73651 6.58527 5.26414 6.58527 5.58958 6.9107L10.0003 11.3214L14.4111 6.91071C14.7365 6.58527 15.2641 6.58527 15.5896 6.91071C15.915 7.23614 15.915 7.76378 15.5896 8.08922L10.5896 13.0892C10.2641 13.4147 9.73651 13.4147 9.41107 13.0892L4.41107 8.08922C4.08563 7.76378 4.08563 7.23614 4.41107 6.9107Z"/>
                            </svg>
                        </a>

                        <div x-cloak x-show="selected === 'Offices'" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="overflow-hidden">
                            <ul class="mt-2 flex flex-col gap-2 {{ app()->getLocale() == 'ar' ? 'pr-11' : 'pl-11' }}" :class="sidebarToggle ? 'lg:hidden' : 'flex'">
                                <li>
                                    <a href="{{ route('offices.index') }}" 
                                       class="group relative flex items-center gap-2.5 rounded-md px-2 py-1.5 font-medium transition-colors duration-300 {{ request()->routeIs('offices.*') ? 'text-primary dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-bodydark2 dark:hover:text-white' }}">
                                        المكاتب الموثوقة
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('offices.unverified.index') }}" 
                                       class="group relative flex items-center gap-2.5 rounded-md px-2 py-1.5 font-medium transition-colors duration-300 {{ request()->routeIs('offices.unverified.index') ? 'text-primary dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-bodydark2 dark:hover:text-white' }}">
                                        المكاتب غير الموثوقة
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="{{ route('shipment.index') }}" 
                           class="flex relative gap-2.5 items-center px-4 py-2.5 font-medium rounded-lg transition-all duration-300 ease-in-out group"
                           :class="request()->routeIs('shipment.index') ? 
                                  'bg-primary/10 text-primary dark:bg-meta-4 dark:text-white' : 
                                  'text-gray-600 hover:bg-gray-100 dark:text-bodydark1 dark:hover:bg-meta-4'">
                            
                            <span class="material-symbols-outlined text-[22px] transition-colors duration-300"
                                  :class="request()->routeIs('shipment.index') ? 'text-primary dark:text-white' : 'text-gray-500 group-hover:text-gray-700 dark:text-bodydark2 dark:group-hover:text-white'">
                                inventory_2
                            </span>
                            <span :class="sidebarToggle ? 'lg:hidden' : 'block'">إدارة الطرود</span>
                        </a>
                    </li>

                    <li x-init="@if(request()->routeIs('shipmentpackage.*') || request()->routeIs('receipts.*')) selected = 'ShipmentsOps' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'ShipmentsOps' ? '' : 'ShipmentsOps')"
                           class="flex relative gap-2.5 items-center px-4 py-2.5 font-medium rounded-lg transition-all duration-300 ease-in-out group"
                           :class="(selected === 'ShipmentsOps' || {{ request()->routeIs('shipmentpackage.*', 'receipts.*') ? 'true' : 'false' }}) ? 
                                  'bg-primary/10 text-primary dark:bg-meta-4 dark:text-white' : 
                                  'text-gray-600 hover:bg-gray-100 dark:text-bodydark1 dark:hover:bg-meta-4'">
                            
                            <span class="material-symbols-outlined text-[22px] transition-colors duration-300"
                                  :class="(selected === 'ShipmentsOps' || {{ request()->routeIs('shipmentpackage.*', 'receipts.*') ? 'true' : 'false' }}) ? 'text-primary dark:text-white' : 'text-gray-500 group-hover:text-gray-700 dark:text-bodydark2 dark:group-hover:text-white'">
                                local_shipping
                            </span>
                            <span :class="sidebarToggle ? 'lg:hidden' : 'block'">حركة الشحنات</span>

                            <svg class="absolute {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }} top-1/2 -translate-y-1/2 fill-current transition-transform duration-200"
                                 :class="[selected === 'ShipmentsOps' ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '']"
                                 width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.41107 6.9107C4.73651 6.58527 5.26414 6.58527 5.58958 6.9107L10.0003 11.3214L14.4111 6.91071C14.7365 6.58527 15.2641 6.58527 15.5896 6.91071C15.915 7.23614 15.915 7.76378 15.5896 8.08922L10.5896 13.0892C10.2641 13.4147 9.73651 13.4147 9.41107 13.0892L4.41107 8.08922C4.08563 7.76378 4.08563 7.23614 4.41107 6.9107Z"/>
                            </svg>
                        </a>

                        <div x-cloak x-show="selected === 'ShipmentsOps'" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="overflow-hidden">
                            <ul class="mt-2 flex flex-col gap-2 {{ app()->getLocale() == 'ar' ? 'pr-11' : 'pl-11' }}" :class="sidebarToggle ? 'lg:hidden' : 'flex'">
                                <li>
                                    <a href="{{ route('shipmentpackage.index') }}" 
                                       class="group relative flex items-center gap-2.5 rounded-md px-2 py-1.5 font-medium transition-colors duration-300 {{ request()->routeIs('shipmentpackage.*') ? 'text-primary dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-bodydark2 dark:hover:text-white' }}">
                                        الشحنات المرسلة
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('receipts.index') }}" 
                                       class="group relative flex items-center gap-2.5 rounded-md px-2 py-1.5 font-medium transition-colors duration-300 {{ request()->routeIs('receipts.*') ? 'text-primary dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-bodydark2 dark:hover:text-white' }}">
                                        الشحنات المستلمة
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                </ul>
            </div>
        </nav>
    </div>
</aside>