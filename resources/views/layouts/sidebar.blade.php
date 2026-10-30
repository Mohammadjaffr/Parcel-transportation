{{-- خلفية مظلمة (Backdrop) تظهر في الموبايل عند فتح القائمة --}}
<div x-cloak x-show="sidebarToggle" x-transition.opacity
    class="fixed inset-0 z-[9998] bg-slate-900/50 backdrop-blur-sm lg:hidden" @click="sidebarToggle = false"></div>

{{-- السايد بار --}}
<aside
    class="fixed {{ app()->getLocale() == 'ar' ? 'right-0 border-l' : 'left-0 border-r' }} top-0 bottom-0 z-[9999] flex w-64 flex-col bg-white transition-transform duration-300 ease-in-out dark:border-gray-800 dark:bg-boxdark lg:static lg:translate-x-0 font-outfit shadow-2xl border-gray-100 lg:shadow-none"
    :class="sidebarToggle ? 'translate-x-0' : '{{ app()->getLocale() == 'ar' ? 'translate-x-full' : '-translate-x-full' }}'"
    @click.outside="sidebarToggle = false">

    {{-- ==================== 1. منطقة الشعار (Logo Header) ==================== --}}
    <div class="flex justify-between items-center px-4 h-16 border-b border-gray-50 dark:border-gray-800/50 shrink-0"
        :class="{ 'lg:justify-center': sidebarToggle }">

        <a href="{{ auth()->user()->type === 'super_admin' ? route('superadmin.dashboard') : route('dashboard.index') }}"
            class="flex gap-2 justify-center items-center w-full transition-transform active:scale-95">
            {{-- الشعار الكامل --}}
            <img class="object-contain w-auto h-10 transition-all duration-300" :class="{ 'lg:hidden': sidebarToggle }"
                src="@if (auth()->user()?->app?->logo) {{ asset('storage/' . auth()->user()->app->logo) }} @else {{ asset('assets/image/icon_without_bg.png') }} @endif"
                alt="شعار النظام" />

            {{-- الأيقونة المصغرة --}}
            <img class="hidden object-contain w-8 h-8 transition-all duration-300"
                :class="{ 'hidden lg:block': sidebarToggle, 'hidden': !sidebarToggle }"
                src="@if (auth()->user()?->app?->logo) {{ asset('storage/' . auth()->user()->app->logo) }} @else {{ asset('assets/image/icon_without_bg.png') }} @endif"
                alt="أيقونة النظام" />
        </a>

        {{-- زر الإغلاق في شاشات الموبايل --}}
        <button @click.stop="sidebarToggle = false"
            class="flex justify-center items-center w-7 h-7 text-gray-500 bg-gray-50 rounded-lg transition-colors hover:bg-primary/10 hover:text-primary lg:hidden dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-primary/20">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>

    {{-- ==================== 2. القائمة الملاحية (Navigation) ==================== --}}
    <div class="flex overflow-y-auto flex-col duration-300 ease-linear custom-scrollbar">
        <nav class="px-3 py-2.5" x-data="{ selected: '' }">

            <div>
                <h3 class="mb-2 px-2 text-[11px] font-black tracking-widest text-gray-400 uppercase transition-all dark:text-gray-500"
                    :class="{ 'lg:text-center': sidebarToggle }">
                    <span :class="{ 'lg:hidden': sidebarToggle }">القائمة الرئيسية</span>
                    <span class="material-symbols-outlined mx-auto hidden text-[18px]"
                        :class="{ 'hidden lg:block': sidebarToggle, 'hidden': !sidebarToggle }">more_horiz</span>
                </h3>

                <ul class="flex flex-col gap-1">

                    @if(auth()->user()->type === 'super_admin')
                    {{-- ==================== Super Admin Navigation ==================== --}}

                    {{-- لوحة تحكم المشرف --}}
                    <li>
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="flex relative gap-2.5 items-center px-3 py-2 text-xs font-bold rounded-xl transition-all duration-200 group"
                            :class="window.location.href.includes('/superadmin/dashboard') ?
                                'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                                'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">
                            <span class="material-symbols-outlined text-[20px] transition-colors"
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
                            class="flex relative gap-2.5 items-center px-3 py-2 text-xs font-bold rounded-xl transition-all duration-200 group"
                            :class="window.location.href.includes('/superadmin/offices') ?
                                'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                                'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">
                            <span class="material-symbols-outlined text-[20px] transition-colors"
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
                            class="flex relative gap-2.5 items-center px-3 py-2 text-xs font-bold rounded-xl transition-all duration-200 group"
                            :class="window.location.href.includes('/superadmin/packages') ?
                                'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                                'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">
                            <span class="material-symbols-outlined text-[20px] transition-colors"
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
                            class="flex relative gap-2.5 items-center px-3 py-2 text-xs font-bold rounded-xl transition-all duration-200 group"
                            :class="window.location.href.includes('/superadmin/subscriptions') ?
                                'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                                'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">
                            <span class="material-symbols-outlined text-[20px] transition-colors"
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
                            class="flex relative gap-2.5 items-center px-3 py-1.5 text-xs font-bold rounded-xl transition-all duration-200 group"
                            :class="window.location.href.includes('{{ route('dashboard.index') }}') ?
                                'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                                'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[19px] transition-colors"
                                :class="window.location.href.includes('{{ route('dashboard.index') }}') ?
                                    'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                grid_view
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الصفحة الرئيسية</span>
                        </a>
                    </li>

                    {{-- 1. إدارة الأفراد (تظهر فقط إذا كان أحد الموديولات مفعل) --}}
                    @php
                        $app = auth()->user()->app;
                        $hasPeople = $app && ($app->hasService('Drivers') || (auth()->user()->type != 'user' && $app->hasService('Users')) || $app->hasService('Customers'));
                    @endphp
                    @if($hasPeople)
                    <li x-init="@if (request()->routeIs('drivers.*') || request()->routeIs('users.*') || request()->routeIs('customers.*')) selected = 'People' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'People' ? '' : 'People')"
                            class="flex relative gap-2.5 items-center px-3 py-1.5 text-xs font-bold rounded-xl transition-all duration-200 group"
                            :class="{{ request()->routeIs('drivers.*', 'users.*', 'customers.*') ? 'true' : 'false' }} ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[19px] transition-colors"
                                :class="{{ request()->routeIs('drivers.*', 'users.*', 'customers.*') ? 'true' : 'false' }} ? 'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                group
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الأفراد</span>

                            <span
                                class="absolute material-symbols-outlined text-[16px] transition-transform duration-200 {{ app()->getLocale() == 'ar' ? 'left-3' : 'right-3' }}"
                                :class="{ 'rotate-180': selected === 'People', 'lg:hidden': sidebarToggle }">
                                expand_more
                            </span>
                        </a>

                        <div x-cloak x-show="selected === 'People'" x-collapse>
                            <div class="relative mt-0.5 {{ app()->getLocale() == 'ar' ? 'pr-4' : 'pl-4' }}"
                                :class="{ 'lg:hidden': sidebarToggle }">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-6' : 'left-6' }}">
                                </div>

                                <ul class="flex flex-col gap-0.5 {{ app()->getLocale() == 'ar' ? 'pr-5' : 'pl-5' }} py-0.5">
                                    
                                    @hasservice('Drivers')
                                    <li>
                                        <a href="{{ route('drivers.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('drivers.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            السائقين
                                        </a>
                                    </li>
                                    @endhasservice

                                    @if(Auth::user()->type === 'admin' || Auth::user()->is_branch_admin)
                                    @hasservice('Users')
                                        <li>
                                            <a href="{{ route('users.index') }}"
                                                class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                                ادارة المستخدمين
                                            </a>
                                        </li>
                                    @endhasservice
                                    @endif

                                    @hasservice('Customers')
                                    <li>
                                        <a href="{{ route('customers.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('customers.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            العملاء
                                        </a>
                                    </li>
                                    @endhasservice
                                  
                                    
                                </ul>
                            </div>
                        </div>
                    </li>
                    @endif
                    
                    {{-- 2. إدارة المكاتب (المكاتب الموثوقة/غير الموثوقة) --}}
                    <li x-init="@if (request()->routeIs('app.*') || request()->routeIs('branch.*') || request()->routeIs('offices.*') || request()->routeIs('offices.unverified.*')) selected = 'Offices' @endif">
                        <a href="#" @click.prevent="selected = (selected === 'Offices' ? '' : 'Offices')"
                            class="flex relative gap-2.5 items-center px-3 py-1.5 text-xs font-bold rounded-xl transition-all duration-200 group"
                            :class="{{ request()->routeIs('app.*', 'branch.*', 'offices.*', 'offices.unverified.*') ? 'true' : 'false' }} ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 hover:bg-gray-50 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800'">

                            <span class="material-symbols-outlined text-[19px] transition-colors"
                                :class="{{ request()->routeIs('app.*', 'branch.*', 'offices.*', 'offices.unverified.*') ? 'true' : 'false' }} ? 'text-primary dark:text-primary' : 'text-gray-400 group-hover:text-primary'">
                                apartment
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">المكاتب</span>

                            <span
                                class="absolute material-symbols-outlined text-[16px] transition-transform duration-200 {{ app()->getLocale() == 'ar' ? 'left-3' : 'right-3' }}"
                                :class="{ 'rotate-180': selected === 'Offices', 'lg:hidden': sidebarToggle }">
                                expand_more
                            </span>
                        </a>

                        <div x-cloak x-show="selected === 'Offices'" x-collapse>
                            <div class="relative mt-0.5 {{ app()->getLocale() == 'ar' ? 'pr-4' : 'pl-4' }}"
                                :class="{ 'lg:hidden': sidebarToggle }">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-6' : 'left-6' }}">
                                </div>
                                <ul
                                    class="flex flex-col gap-0.5 {{ app()->getLocale() == 'ar' ? 'pr-5' : 'pl-5' }} py-0.5">
                                    @hasservice('Offices_Verified')
                                    <li>
                                        <a href="{{ route('app.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('app.index') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                    
                                            مكاتب داخل النظام
                                        </a>
                                    </li>
                                    @endhasservice
                                    @hasservice('Offices_Unverified')
                                    <li>
                                        <a href="{{ route('offices.unverified.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('offices.unverified.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            مكاتب خارجية                                        </a>
                                    </li>
                                    @endhasservice
                                </ul>
                            </div>
                        </div>
                    </li>

                    {{-- 3. إدارة الطرود --}}
                    @php
                        $app = auth()->user()->app;
                        $hasShipments = $app && ($app->hasService('Shipment_Out') || $app->hasService('Shipment_In'));
                    @endphp
                    @if($hasShipments)
                    <li>
                        <div
                            class="flex relative gap-2.5 items-center px-3 py-1.5 text-xs font-bold rounded-xl transition-all duration-200"
                            :class="{{ request()->routeIs('shipment.outgoing.*', 'shipment.incoming.*', 'shipment.quickScan', 'shipment.index') ? 'true' : 'false' }} ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 dark:text-gray-400'">

                            <span class="material-symbols-outlined text-[19px] transition-colors"
                                :class="{{ request()->routeIs('shipment.outgoing.*', 'shipment.incoming.*', 'shipment.quickScan', 'shipment.index') ? 'true' : 'false' }} ? 'text-primary dark:text-primary' : 'text-gray-400'">
                                inventory_2
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الطرود</span>
                        </div>

                        <div>
                            <div class="relative mt-0.5 {{ app()->getLocale() == 'ar' ? 'pr-4' : 'pl-4' }}"
                                :class="{ 'lg:hidden': sidebarToggle }">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-6' : 'left-6' }}">
                                </div>
                                <ul
                                    class="flex flex-col gap-0.5 {{ app()->getLocale() == 'ar' ? 'pr-5' : 'pl-5' }} py-0.5">
                                    
                                    @hasservice('Shipment_Out')
                                    <li>
                                        <a href="{{ route('shipment.outgoing.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('shipment.outgoing.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الطرود المرسلة
                                        </a>
                                    </li>
                                    @endhasservice

                                    @hasservice('Shipment_In')
                                    <li>
                                        <a href="{{ route('shipment.incoming.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('shipment.incoming.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الطرود المستلمة
                                        </a>
                                    </li>
                                 
                                    @endhasservice

                                </ul>
                            </div>
                        </div>
                    </li>
                    @endif
                    
                    {{-- 4. الشحنات --}}
                    @php
                        $app = auth()->user()->app;
                        $hasPackages = $app && ($app->hasService('Package_Out') || $app->hasService('Package_In'));
                    @endphp
                    @if($hasPackages)
                    <li>
                        <div
                            class="flex relative gap-2.5 items-center px-3 py-1.5 text-xs font-bold rounded-xl transition-all duration-200"
                            :class="{{ request()->routeIs('shipmentpackage.*', 'receipts.*') ? 'true' : 'false' }} ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 dark:text-gray-400'">

                            <span class="material-symbols-outlined text-[19px] transition-colors"
                                :class="{{ request()->routeIs('shipmentpackage.*', 'receipts.*') ? 'true' : 'false' }} ?
                                'text-primary dark:text-primary' : 'text-gray-400'">
                                local_shipping
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الشحنات</span>
                        </div>

                        <div>
                            <div class="relative mt-0.5 {{ app()->getLocale() == 'ar' ? 'pr-4' : 'pl-4' }}"
                                :class="{ 'lg:hidden': sidebarToggle }">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-6' : 'left-6' }}">
                                </div>
                                <ul
                                    class="flex flex-col gap-0.5 {{ app()->getLocale() == 'ar' ? 'pr-5' : 'pl-5' }} py-0.5">
                                    
                                    @hasservice('Package_Out')
                                    <li>
                                        <a href="{{ route('shipmentpackage.outgoing.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('shipmentpackage.outgoing.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الشحنات المرسلة
                                        </a>
                                    </li>
                                    @endhasservice

                                    @hasservice('Package_In')

                                    <li>
                                        <a href="{{ route('shipmentpackage.incoming.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('shipmentpackage.incoming.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الشحنات المستلمة
                                        </a>
                                    </li>
                                    {{-- <li>
                                        <a href="{{ route('shipment.quickScan') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('shipment.quickScan') ? 'text-primary bg-primary/10 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            <span class="material-symbols-outlined text-[18px]">barcode_scanner</span>
                                            استلام سريع (باركود)
                                        </a>
                                    </li> --}}
                                    @endhasservice
                                    

                                </ul>
                            </div>
                        </div>
                    </li>
                    @endif

                    {{-- 5. إدارة الركاب --}}
                    @hasservice('Passengers')
                    <li>
                        <div
                            class="flex relative gap-2.5 items-center px-3 py-1.5 text-xs font-bold rounded-xl transition-all duration-200"
                            :class="{{ request()->routeIs('passengers.*', 'trips.*') ? 'true' : 'false' }} ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 dark:text-gray-400'">

                            <span class="material-symbols-outlined text-[19px] transition-colors"
                                :class="{{ request()->routeIs('passengers.*', 'trips.*') ? 'true' : 'false' }} ? 'text-primary dark:text-primary' : 'text-gray-400'">
                                airline_seat_recline_normal
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الركاب</span>
                        </div>

                        <div>
                            <div class="relative mt-0.5 {{ app()->getLocale() == 'ar' ? 'pr-4' : 'pl-4' }}"
                                :class="{ 'lg:hidden': sidebarToggle }">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-6' : 'left-6' }}">
                                </div>
                                <ul
                                    class="flex flex-col gap-0.5 {{ app()->getLocale() == 'ar' ? 'pr-5' : 'pl-5' }} py-0.5">
                                    
                                    <li>
                                        <a href="{{ route('passengers.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('passengers.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            الركاب
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('trips.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('trips.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            رحلات الركاب
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </li>
                    @endhasservice

                    {{-- 6. الصندوق المالي --}}
                    <li>
                        <div
                            class="flex relative gap-2.5 items-center px-3 py-1.5 text-xs font-bold rounded-xl transition-all duration-200"
                            :class="{{ request()->routeIs('cash.*', 'cash-categories.*', 'closings.*') ? 'true' : 'false' }} ?
                            'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white' :
                            'text-gray-600 dark:text-gray-400'">

                            <span class="material-symbols-outlined text-[19px] transition-colors"
                                :class="{{ request()->routeIs('cash.*', 'cash-categories.*', 'closings.*') ? 'true' : 'false' }} ? 'text-primary dark:text-primary' : 'text-gray-400'">
                                account_balance_wallet
                            </span>
                            <span :class="{ 'lg:hidden': sidebarToggle }">الصندوق المالي</span>
                        </div>

                        <div>
                            <div class="relative mt-0.5 {{ app()->getLocale() == 'ar' ? 'pr-4' : 'pl-4' }}"
                                :class="{ 'lg:hidden': sidebarToggle }">
                                <div
                                    class="absolute top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700 {{ app()->getLocale() == 'ar' ? 'right-6' : 'left-6' }}">
                                </div>
                                <ul class="flex flex-col gap-0.5 {{ app()->getLocale() == 'ar' ? 'pr-5' : 'pl-5' }} py-0.5">
                                    {{-- رابط دفتر الصندوق --}}
                                    <li>
                                        <a href="{{ route('cash.ledger.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('cash.ledger.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            دفتر الحسابات
                                        </a>
                                    </li>

                                    {{-- رابط فئات الصندوق --}}
                                    @if (in_array(auth()->user()->type, ['admin', 'super_admin']))
                                    <li>
                                        <a href="{{ route('cash-categories.index') }}"
                                            class="relative flex items-center gap-2 px-2.5 py-1 text-xs font-bold rounded-lg transition-colors {{ request()->routeIs('cash-categories.*') ? 'text-primary bg-primary/5 dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-primary hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800' }}">
                                            فئات الحسابات
                                        </a>
                                    </li>
                                    @endif

                                </ul>
                            </div>
                        </div>
                    </li>
                    @endif    
                </ul>
            </div>
        </nav>
    </div>
</aside>