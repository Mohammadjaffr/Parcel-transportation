{{-- خلفية مظلمة (Backdrop) تظهر في الموبايل عند فتح القائمة --}}
<div x-cloak x-show="sidebarToggle" x-transition.opacity
    class="fixed inset-0 z-[9998] bg-slate-900/50 backdrop-blur-sm lg:hidden" @click="sidebarToggle = false"></div>

{{-- السايد بار --}}
<aside
    class="fixed {{ app()->getLocale() == 'ar' ? 'right-0 border-l' : 'left-0 border-r' }} top-0 bottom-0 z-[9999] flex w-72 flex-col bg-white transition-transform duration-300 ease-in-out dark:border-gray-800 dark:bg-boxdark lg:static lg:translate-x-0 font-outfit shadow-2xl border-gray-100 lg:shadow-none"
    :class="sidebarToggle ? 'translate-x-0' : '{{ app()->getLocale() == 'ar' ? 'translate-x-full' : '-translate-x-full' }}'"
    @click.outside="sidebarToggle = false">

    {{-- ==================== 1. منطقة الشعار (Logo Header) ==================== --}}
    <div class="flex justify-between items-center px-6 h-24 border-b border-gray-50 dark:border-gray-800/50 shrink-0"
        :class="{ 'lg:justify-center': sidebarToggle }">

        <a href="{{ auth()->user()->type === 'super_admin' ? route('superadmin.dashboard') : route('dashboard.index') }}"
            class="flex gap-2 justify-center items-center w-full transition-transform active:scale-95">
            {{-- الشعار الكامل --}}
            <img class="object-contain w-auto h-14 transition-all duration-300" :class="{ 'lg:hidden': sidebarToggle }"
                src="@if (auth()->user()?->app?->logo) {{ asset('storage/' . auth()->user()->app->logo) }} @else {{ asset('assets/image/icon_without_bg.png') }} @endif"
                alt="شعار النظام" />

            {{-- الأيقونة المصغرة --}}
            <img class="hidden object-contain w-10 h-10 transition-all duration-300"
                :class="{ 'hidden lg:block': sidebarToggle, 'hidden': !sidebarToggle }"
                src="@if (auth()->user()?->app?->logo) {{ asset('storage/' . auth()->user()->app->logo) }} @else {{ asset('assets/image/icon_without_bg.png') }} @endif"
                alt="أيقونة النظام" />
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
                <h3 class="mb-4 text-xs font-black tracking-widest text-gray-400 uppercase transition-all dark:text-gray-500"
                    :class="{ 'lg:text-center': sidebarToggle }">
                    <span :class="{ 'lg:hidden': sidebarToggle }">القائمة الرئيسية</span>
                    <span class="material-symbols-outlined mx-auto hidden text-[20px]"
                        :class="{ 'hidden lg:block': sidebarToggle, 'hidden': !sidebarToggle }">more_horiz</span>
                </h3>

                <ul class="flex flex-col gap-2">

                    @if(auth()->user()->type === 'super_admin')
                    {{-- ==================== Super Admin Navigation ==================== --}}

                    {{-- لوحة تحكم المشرف --}}
                    <li>
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="window.location.href.includes('/superadmin/dashboard') ?
                                'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                                'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">
                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="window.location.href.includes('/superadmin/dashboard') ?
                                    'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                shield_person
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">لوحة التحكم</span>
                        </a>
                    </li>

                    {{-- إدارة المكاتب (Super Admin) --}}
                    <li>
                        <a href="{{ route('superadmin.offices.index') }}"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="window.location.href.includes('/superadmin/offices') ?
                                'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                                'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">
                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="window.location.href.includes('/superadmin/offices') ?
                                    'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                apartment
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">المكاتب</span>
                        </a>
                    </li>

                    {{-- الباقات --}}
                    <li>
                        <a href="{{ route('superadmin.packages.index') }}"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="window.location.href.includes('/superadmin/packages') ?
                                'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                                'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">
                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="window.location.href.includes('/superadmin/packages') ?
                                    'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                workspace_premium
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الباقات</span>
                        </a>
                    </li>

                    {{-- الاشتراكات --}}
                    <li>
                        <a href="{{ route('superadmin.subscriptions.index') }}"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="window.location.href.includes('/superadmin/subscriptions') ?
                                'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                                'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">
                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="window.location.href.includes('/superadmin/subscriptions') ?
                                    'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                card_membership
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الاشتراكات</span>
                        </a>
                    </li>

                    @else
                    {{-- ==================== Regular Employee Navigation ==================== --}}

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
                            <span :class="{ 'lg:hidden': sidebarToggle }">الصفحة الرئيسية</span>
                        </a>
                    </li>

                    {{-- إدارة الأفراد (تظهر فقط إذا كان أحد الموديولات مفعل) --}}
                    @php
                        $app = auth()->user()->app;
                        $hasPeople = $app && ($app->hasService('Drivers') || (auth()->user()->type != 'user' && $app->hasService('Users')) || $app->hasService('Customers'));
                    @endphp
                    @if($hasPeople)
                    <li x-init="@if (request()->routeIs('drivers.*') || request()->routeIs('users.*') || request()->routeIs('customers.*')) selected = 'People' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'People' ? '' : 'People')"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="{{ request()->routeIs('drivers.*', 'users.*', 'customers.*') ? 'true' : 'false' }} ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="{{ request()->routeIs('drivers.*', 'users.*', 'customers.*') ? 'true' : 'false' }} ? 'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                group
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الأفراد</span>

                            <span
                                class="absolute material-symbols-outlined text-[18px] transition-transform duration-200 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }}"
                                :class="{ 'rotate-180': selected === 'People', 'lg:hidden': sidebarToggle }">
                                expand_more
                            </span>
                        </a>

                        <div x-cloak x-show="selected === 'People'" x-collapse>
                            <div class="relative mt-2 {{ app()->getLocale() == 'ar' ? 'pr-6' : 'pl-6' }}"
                                :class="{ 'lg:hidden': sidebarToggle }">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-9' : 'left-9' }}">
                                </div>

                                <ul class="flex flex-col gap-1 {{ app()->getLocale() == 'ar' ? 'pr-8' : 'pl-8' }} py-1">
                                    
                                    @hasservice('Drivers')
                                    <li>
                                        <a href="{{ route('drivers.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('drivers.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            السائقين
                                        </a>
                                    </li>
                                    @endhasservice

                                    @if(Auth::user()->type != 'user')
                                    @hasservice('Users')
                                        <li>
                                            <a href="{{ route('users.index') }}"
                                                class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                                ادارة المستخدمين
                                            </a>
                                        </li>
                                    @endhasservice
                                    @endif

                                    @hasservice('Customers')
                                    <li>
                                        <a href="{{ route('customers.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('customers.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            العملاء
                                        </a>
                                    </li>
                                    @endhasservice
                                  
                                    
                                </ul>
                            </div>
                        </div>
                    </li>
                    @endif

                    {{-- إدارة الركاب --}}
                    @hasservice('Passengers')
                    <li x-init="@if (request()->routeIs('passengers.*') || request()->routeIs('trips.*')) selected = 'PassengersMenu' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'PassengersMenu' ? '' : 'PassengersMenu')"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="{{ request()->routeIs('passengers.*', 'trips.*') ? 'true' : 'false' }} ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="{{ request()->routeIs('passengers.*', 'trips.*') ? 'true' : 'false' }} ? 'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                airline_seat_recline_normal
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الركاب</span>

                            <span
                                class="absolute material-symbols-outlined text-[18px] transition-transform duration-200 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }}"
                                :class="{ 'rotate-180': selected === 'PassengersMenu', 'lg:hidden': sidebarToggle }">
                                expand_more
                            </span>
                        </a>

                        <div x-cloak x-show="selected === 'PassengersMenu'" x-collapse>
                            <div class="relative mt-2 {{ app()->getLocale() == 'ar' ? 'pr-6' : 'pl-6' }}"
                                :class="{ 'lg:hidden': sidebarToggle }">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-9' : 'left-9' }}">
                                </div>
                                <ul
                                    class="flex flex-col gap-1 {{ app()->getLocale() == 'ar' ? 'pr-8' : 'pl-8' }} py-1">
                                    
                                    <li>
                                        <a href="{{ route('passengers.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('passengers.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الركاب
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('trips.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('trips.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            رحلات الركاب
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </li>
                    @endhasservice
                    

                    {{-- إدارة المكاتب (المكاتب الموثوقة/غير الموثوقة) --}}
                    <li x-init="@if (request()->routeIs('app.*') || request()->routeIs('branch.*') || request()->routeIs('offices.*') || request()->routeIs('offices.unverified.*')) selected = 'Offices' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'Offices' ? '' : 'Offices')"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="{{ request()->routeIs('app.*', 'branch.*', 'offices.*', 'offices.unverified.*') ? 'true' : 'false' }} ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="{{ request()->routeIs('app.*', 'branch.*', 'offices.*', 'offices.unverified.*') ? 'true' : 'false' }} ? 'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                apartment
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">المكاتب</span>

                            <span
                                class="absolute material-symbols-outlined text-[18px] transition-transform duration-200 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }}"
                                :class="{ 'rotate-180': selected === 'Offices', 'lg:hidden': sidebarToggle }">
                                expand_more
                            </span>
                        </a>

                        <div x-cloak x-show="selected === 'Offices'" x-collapse>
                            <div class="relative mt-2 {{ app()->getLocale() == 'ar' ? 'pr-6' : 'pl-6' }}"
                                :class="{ 'lg:hidden': sidebarToggle }">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-9' : 'left-9' }}">
                                </div>
                                <ul
                                    class="flex flex-col gap-1 {{ app()->getLocale() == 'ar' ? 'pr-8' : 'pl-8' }} py-1">
                                    @hasservice('Offices_Verified')
                                    <li>
                                        <a href="{{ route('app.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('app.index') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                    
                                            مكاتب داخل النظام
                                        </a>
                                    </li>
                                    @endhasservice
                                    @hasservice('Offices_Unverified')
                                    <li>
                                        <a href="{{ route('offices.unverified.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('offices.unverified.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            مكاتب خارجية                                        </a>
                                    </li>
                                    @endhasservice
                                </ul>
                            </div>
                        </div>
                    </li>

                    {{-- إدارة الطرود --}}
                    @php
                        $app = auth()->user()->app;
                        $hasShipments = $app && ($app->hasService('Shipment_Out') || $app->hasService('Shipment_In'));
                    @endphp
                    @if($hasShipments)
                    <li x-init="@if (request()->routeIs('shipment.outgoing.*') ||
                            request()->routeIs('shipment.incoming.*') ||
                            request()->routeIs('shipment.index')) selected = 'Shipments' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'Shipments' ? '' : 'Shipments')"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="{{ request()->routeIs('shipment.outgoing.*', 'shipment.incoming.*', 'shipment.index') ? 'true' : 'false' }} ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="{{ request()->routeIs('shipment.outgoing.*', 'shipment.incoming.*', 'shipment.index') ? 'true' : 'false' }} ? 'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                inventory_2
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الطرود</span>

                            <span
                                class="absolute material-symbols-outlined text-[18px] transition-transform duration-200 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }}"
                                :class="{ 'rotate-180': selected === 'Shipments', 'lg:hidden': sidebarToggle }">
                                expand_more
                            </span>
                        </a>

                        <div x-cloak x-show="selected === 'Shipments'" x-collapse>
                            <div class="relative mt-2 {{ app()->getLocale() == 'ar' ? 'pr-6' : 'pl-6' }}"
                                :class="{ 'lg:hidden': sidebarToggle }">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-9' : 'left-9' }}">
                                </div>
                                <ul
                                    class="flex flex-col gap-1 {{ app()->getLocale() == 'ar' ? 'pr-8' : 'pl-8' }} py-1">
                                    
                                    @hasservice('Shipment_Out')
                                    <li>
                                        <a href="{{ route('shipment.outgoing.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('shipment.outgoing.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الطرود المرسلة
                                        </a>
                                    </li>
                                    @endhasservice

                                    @hasservice('Shipment_In')
                                    <li>
                                        <a href="{{ route('shipment.incoming.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('shipment.incoming.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الطرود المستلمة
                                        </a>
                                    </li>
                                    @endhasservice

                                </ul>
                            </div>
                        </div>
                    </li>
                    @endif
                    

                    
                    
                    @php
                        $app = auth()->user()->app;
                        $hasPackages = $app && ($app->hasService('Package_Out') || $app->hasService('Package_In'));
                    @endphp
                    @if($hasPackages)
                    <li x-init="@if (request()->routeIs('shipmentpackage.*') || request()->routeIs('receipts.*')) selected = 'ShipmentsOps' @endif">
                        <a href="#"
                            @click.prevent="selected = (selected === 'ShipmentsOps' ? '' : 'ShipmentsOps')"
                            class="flex relative gap-3 items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 group"
                            :class="{{ request()->routeIs('shipmentpackage.*', 'receipts.*') ? 'true' : 'false' }} ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[22px] transition-colors"
                                :class="{{ request()->routeIs('shipmentpackage.*', 'receipts.*') ? 'true' : 'false' }} ?
                                'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                local_shipping
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الشحنات</span>

                            <span
                                class="absolute material-symbols-outlined text-[18px] transition-transform duration-200 {{ app()->getLocale() == 'ar' ? 'left-4' : 'right-4' }}"
                                :class="{ 'rotate-180': selected === 'ShipmentsOps', 'lg:hidden': sidebarToggle }">
                                expand_more
                            </span>
                        </a>

                        <div x-cloak x-show="selected === 'ShipmentsOps'" x-collapse>
                            <div class="relative mt-2 {{ app()->getLocale() == 'ar' ? 'pr-6' : 'pl-6' }}"
                                :class="{ 'lg:hidden': sidebarToggle }">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-9' : 'left-9' }}">
                                </div>
                                <ul
                                    class="flex flex-col gap-1 {{ app()->getLocale() == 'ar' ? 'pr-8' : 'pl-8' }} py-1">
                                    
                                    @hasservice('Package_Out')
                                    <li>
                                        <a href="{{ route('shipmentpackage.outgoing.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('shipmentpackage.outgoing.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الشحنات المرسلة
                                        </a>
                                    </li>
                                    @endhasservice

                                    @hasservice('Package_In')

                                    <li>
                                        <a href="{{ route('shipmentpackage.incoming.index') }}"
                                            class="relative flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg transition-colors {{ request()->routeIs('shipmentpackage.incoming.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الشحنات المستلمة
                                        </a>
                                    </li>
                                    @endhasservice
                                    

                                </ul>
                            </div>
                        </div>
                    </li>
                    @endif
                    

                    
                    @endif
                </ul>
            </div>
        </nav>
    </div>
</aside>