<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full lg:translate-x-0'"
    class="fixed {{ app()->getLocale() == 'ar' ? 'right-0 border-l' : 'left-0 border-r' }} top-0 z-[9999] flex h-screen w-72 flex-col overflow-y-hidden border-gray-100 bg-white duration-300 ease-linear dark:border-gray-800 dark:bg-boxdark lg:static font-outfit shadow-sm"
    @click.outside="sidebarToggle = false">

    {{-- ==================== 1. منطقة الشعار (Logo Header) ==================== --}}
    <div class="flex justify-between items-center px-6 h-24 border-b border-gray-50 dark:border-gray-800/50 shrink-0"
        :class="sidebarToggle ? 'lg:justify-center' : ''">

        <a href="{{ route('dashboard.index') }}"
            class="flex gap-2 justify-center items-center w-full transition-transform active:scale-95">
            {{-- الشعار الكامل (يظهر عندما يكون الشريط مفتوحاً) --}}
            <img class="object-contain w-auto h-14 transition-all duration-300"
                :class="sidebarToggle ? 'lg:hidden' : 'block'"
                src="@if(auth()->user()?->app?->logo) {{ asset('storage/' . auth()->user()->app->logo) }} @else {{ asset('assets/image/icon_without_bg.png') }} @endif" alt="شعار النظام" />

            {{-- الأيقونة المصغرة (تظهر عندما يكون الشريط مغلقاً) --}}
            <img class="hidden object-contain w-10 h-10 transition-all duration-300"
                :class="sidebarToggle ? 'lg:block' : 'hidden'"
                src="@if(auth()->user()?->app?->logo) {{ asset('storage/' . auth()->user()->app->logo) }} @else {{ asset('assets/image/icon_without_bg.png') }} @endif" alt="أيقونة النظام" />
        </a>

        {{-- زر الإغلاق في شاشات الموبايل --}}
        <button @click.stop="sidebarToggle = false"
            class="flex justify-center items-center w-8 h-8 text-gray-500 bg-gray-50 rounded-xl transition-colors hover:bg-primary/10 hover:text-primary lg:hidden dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-primary/20">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
    </div>

    {{-- ==================== 2. القائمة الملاحية (Navigation) ==================== --}}
    <div class="flex overflow-y-auto flex-col duration-300 ease-linear custom-scrollbar">
        <nav class="px-4 py-6" x-data="{ selected: '' }">

            <div>
                <h3 class="mb-4 text-xs font-black tracking-widest text-gray-400 uppercase dark:text-gray-500"
                    :class="sidebarToggle ? 'text-center' : ''">
                    <span :class="sidebarToggle ? 'lg:hidden' : 'block'">القائمة الرئيسية</span>
                    <span class="material-symbols-outlined mx-auto hidden text-[20px]"
                        :class="sidebarToggle ? 'lg:block' : 'hidden'">more_horiz</span>
                </h3>

                <ul class="flex flex-col gap-2">

                    {{-- لوحة التحكم --}}
                    <li>
                        <a href="{{ route('dashboard.index') }}"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="window.location.href.includes('{{ route('dashboard.index') }}') ?
                                'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                                'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="window.location.href.includes('{{ route('dashboard.index') }}') ?
                                    'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                grid_view
                            </span>
                            <span :class="sidebarToggle ? 'lg:hidden' : 'block'">الصفحة الرئيسية</span>
                        </a>
                    </li>

                    {{-- إدارة الأفراد (قائمة منسدلة) --}}
                    <li x-init="@if (request()->routeIs('drivers.*') || request()->routeIs('users.*') || request()->routeIs('customers.*')) selected = 'People' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'People' ? '' : 'People')"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="(selected === 'People' ||
                                {{ request()->routeIs('drivers.*', 'users.*', 'customers.*') ? 'true' : 'false' }}) ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="(selected === 'People' ||
                                    {{ request()->routeIs('drivers.*', 'users.*', 'customers.*') ? 'true' : 'false' }}
                                    ) ? 'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                group
                            </span>
                            <span :class="sidebarToggle ? 'lg:hidden' : 'block'">إدارة الأفراد</span>

                            <span
                                class="absolute material-symbols-outlined text-[18px] transition-transform duration-200 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }}"
                                :class="[selected === 'People' ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '']">
                                expand_more
                            </span>
                        </a>

                        {{-- القائمة الفرعية (بخط هرمي) --}}
                        <div x-cloak x-show="selected === 'People'" x-collapse>
                            <div class="relative mt-2 {{ app()->getLocale() == 'ar' ? 'pr-6' : 'pl-6' }}"
                                :class="sidebarToggle ? 'lg:hidden' : 'block'">
                                {{-- الخط العمودي --}}
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-9' : 'left-9' }}">
                                </div>

                                <ul
                                    class="flex flex-col gap-1 {{ app()->getLocale() == 'ar' ? 'pr-8' : 'pl-8' }} py-1">
                                    <li>
                                        <a href="{{ route('drivers.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('drivers.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            السائقين
                                        </a>
                                    </li>
                                    @if (Auth::user()->type != 'user')
                                        <li>
                                            <a href="{{ route('users.index') }}"
                                                class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                                ادارة المستخدمين
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{ route('customers.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('customers.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            العملاء
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>

                    {{-- إدارة المكاتب (قائمة منسدلة) --}}
                    <li x-init="@if (request()->routeIs('branch.*') || request()->routeIs('offices.*') || request()->routeIs('offices.unverified.*')) selected = 'Offices' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'Offices' ? '' : 'Offices')"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="(selected === 'Offices' ||
                                {{ request()->routeIs('branch.*', 'offices.*', 'offices.unverified.*') ? 'true' : 'false' }}
                                ) ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="(selected === 'Offices' ||
                                    {{ request()->routeIs('branch.*', 'offices.*', 'offices.unverified.*') ? 'true' : 'false' }}
                                    ) ? 'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                apartment
                            </span>
                            <span :class="sidebarToggle ? 'lg:hidden' : 'block'">إدارة المكاتب</span>

                            <span
                                class="absolute material-symbols-outlined text-[18px] transition-transform duration-200 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }}"
                                :class="[selected === 'Offices' ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '']">
                                expand_more
                            </span>
                        </a>

                        <div x-cloak x-show="selected === 'Offices'" x-collapse>
                            <div class="relative mt-2 {{ app()->getLocale() == 'ar' ? 'pr-6' : 'pl-6' }}"
                                :class="sidebarToggle ? 'lg:hidden' : 'block'">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-9' : 'left-9' }}">
                                </div>
                                <ul
                                    class="flex flex-col gap-1 {{ app()->getLocale() == 'ar' ? 'pr-8' : 'pl-8' }} py-1">
                                    <li>
                                        <a href="{{ route('app.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('offices.index') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            المكاتب الموثوقة
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('offices.unverified.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('offices.unverified.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            المكاتب غير الموثوقة
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>

                    {{-- إدارة الطرود --}}
                    {{-- إدارة الطرود (قائمة منسدلة) --}}
                    <li x-init="@if (request()->routeIs('shipment.outgoing.*') || request()->routeIs('shipment.incoming.*') || request()->routeIs('shipment.index')) selected = 'Shipments' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'Shipments' ? '' : 'Shipments')"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="(selected === 'Shipments' ||
                                {{ request()->routeIs('shipment.outgoing.*', 'shipment.incoming.*', 'shipment.index') ? 'true' : 'false' }}) ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="(selected === 'Shipments' ||
                                    {{ request()->routeIs('shipment.outgoing.*', 'shipment.incoming.*', 'shipment.index') ? 'true' : 'false' }}) ?
                                'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                inventory_2
                            </span>
                            <span :class="sidebarToggle ? 'lg:hidden' : 'block'">إدارة الطرود</span>

                            <span
                                class="absolute material-symbols-outlined text-[18px] transition-transform duration-200 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }}"
                                :class="[selected === 'Shipments' ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '']">
                                expand_more
                            </span>
                        </a>

                        {{-- القائمة الفرعية --}}
                        <div x-cloak x-show="selected === 'Shipments'" x-collapse>
                            <div class="relative mt-2 {{ app()->getLocale() == 'ar' ? 'pr-6' : 'pl-6' }}"
                                :class="sidebarToggle ? 'lg:hidden' : 'block'">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-9' : 'left-9' }}">
                                </div>
                                <ul
                                    class="flex flex-col gap-1 {{ app()->getLocale() == 'ar' ? 'pr-8' : 'pl-8' }} py-1">
                                    <li>
                                        <a href="{{ route('shipment.outgoing.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('shipment.outgoing.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الطرود الصادرة
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('shipment.incoming.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('shipment.incoming.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الطرود الواردة
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>

                    {{-- حركة الشحنات (قائمة منسدلة) --}}
                    <li x-init="@if (request()->routeIs('shipmentpackage.*') || request()->routeIs('receipts.*')) selected = 'ShipmentsOps' @endif">
                        <a href="#"
                            @click.prevent="selected = (selected === 'ShipmentsOps' ? '' : 'ShipmentsOps')"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="(selected === 'ShipmentsOps' ||
                                {{ request()->routeIs('shipmentpackage.*', 'receipts.*') ? 'true' : 'false' }}) ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="(selected === 'ShipmentsOps' ||
                                    {{ request()->routeIs('shipmentpackage.*', 'receipts.*') ? 'true' : 'false' }}) ?
                                'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                local_shipping
                            </span>
                            <span :class="sidebarToggle ? 'lg:hidden' : 'block'">حركة الشحنات</span>

                            <span
                                class="absolute material-symbols-outlined text-[18px] transition-transform duration-200 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }}"
                                :class="[selected === 'ShipmentsOps' ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '']">
                                expand_more
                            </span>
                        </a>

                        <div x-cloak x-show="selected === 'ShipmentsOps'" x-collapse>
                            <div class="relative mt-2 {{ app()->getLocale() == 'ar' ? 'pr-6' : 'pl-6' }}"
                                :class="sidebarToggle ? 'lg:hidden' : 'block'">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-9' : 'left-9' }}">
                                </div>
                                <ul
                                    class="flex flex-col gap-1 {{ app()->getLocale() == 'ar' ? 'pr-8' : 'pl-8' }} py-1">
                                    <li>
                                        <a href="{{ route('shipmentpackage.outgoing.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('shipmentpackage.outgoing.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الشحنات المرسلة
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('shipmentpackage.incoming.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('shipmentpackage.incoming.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الشحنات المستلمة
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>
        </nav>
    </div>
</aside>
