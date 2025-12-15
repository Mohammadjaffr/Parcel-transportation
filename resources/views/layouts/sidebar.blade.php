<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : 'translate-x-full'"
    class="sidebar fixed right-0 top-0 z-40 flex h-screen w-[290px] flex-col overflow-y-hidden border-l border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black duration-300 ease-in-out transition-transform lg:static lg:translate-x-0"
    @click.outside="sidebarToggle = false">


    <!-- SIDEBAR HEADER -->
    <div :class="sidebarToggle ? 'justify-center' : 'justify-between'"
        class="flex items-center gap-2 pt-8 sidebar-header pb-3">
        <a href="#">
            <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
                <img class="dark:hidden w-40 h-40" src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}"
                    alt="Logo" />
                {{-- Dark logo --}}
                <img class="hidden dark:block w-12 h-12" src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}"
                    alt="Logo" />
            </span>

            <img class="logo-icon w-12 h-12" :class="sidebarToggle ? 'lg:block' : 'hidden'"
                src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" alt="Logo" />
        </a>
    </div>
    <!-- SIDEBAR HEADER -->

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <!-- Sidebar Menu -->
        <nav x-data="{ selected: $persist('Dashboard') }">
            <!-- Menu Group -->
            <div>
                <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
                    <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
                        القائمة الرئيسية
                    </span>

                    <svg :class="sidebarToggle ? 'lg:block hidden' : 'hidden'"
                        class="mx-auto fill-current menu-group-icon" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                            fill="" />
                    </svg>
                </h3>

                <ul class="flex flex-col gap-4 mb-6">
                    <!-- Menu Item Dashboard -->
                    <li>
                        <a href="{{ route('dashboard.index') }}" class="menu-item group"
                            :class="window.location.href.includes('{{ route('dashboard.index') }}') ?
                                'menu-item-active' : 'menu-item-inactive'">
                            <svg :class="window.location.href.includes('{{ route('dashboard.index') }}') ?
                                'menu-item-icon-active' : 'menu-item-icon-inactive'"
                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z"
                                    fill="" />
                            </svg>

                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                                الصفحة الرئيسية
                            </span>
                        </a>

                        <!-- Dropdown Menu Start -->
                        {{-- <div class="overflow-hidden transform translate"
              :class="(selected === 'Dashboard') ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
                <li>
                  <a href="index.html" class="menu-dropdown-item group"
                    :class="page === 'ecommerce' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    eCommerce
                  </a>
                </li>
              </ul>
            </div> --}}
                        <!-- Dropdown Menu End -->

                    </li>
                    <!-- Menu Item Dashboard -->

                    <!-- Menu Item Calendar -->
                    <li>
                        <a href="{{ route('users.index') }}" @click="selected = (selected === 'users' ? '':'users')"
                            class="menu-item group"
                            :class="window.location.href.includes('{{ route('users.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">
                            <svg :class="window.location.href.includes('{{ route('users.index') }}') ? 'menu-item-icon-active' :
                                'menu-item-icon-inactive'"
                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9991 7.25C10.8847 7.25 9.98126 8.15342 9.98126 9.26784C9.98126 10.3823 10.8847 11.2857 11.9991 11.2857C13.1135 11.2857 14.0169 10.3823 14.0169 9.26784C14.0169 8.15342 13.1135 7.25 11.9991 7.25ZM8.48126 9.26784C8.48126 7.32499 10.0563 5.75 11.9991 5.75C13.9419 5.75 15.5169 7.32499 15.5169 9.26784C15.5169 11.2107 13.9419 12.7857 11.9991 12.7857C10.0563 12.7857 8.48126 11.2107 8.48126 9.26784Z"
                                    fill="" />
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                                ادارة المستخدمين
                            </span>
                        </a>
                    </li>
                    <!-- Menu Item Calendar -->

                    <!-- Menu Item Profile -->
                    {{-- <li>
                        <a href="{{ route('branch.index') }}"
                            @click="selected = (selected === 'Profile' ? '':'Profile')" class="menu-item group"
                            :class="window.location.href.includes('{{ route('branch.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">
                            <svg :class="window.location.href.includes('{{ route('branch.index') }}') ? 'menu-item-icon-active' :
                                'menu-item-icon-inactive'"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                                إدارة الافرع
                            </span>
                        </a>
                    </li> --}}
                    <!-- Menu Item Profile -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Requests' ? '' : 'Requests')"
                            class="menu-item group flex items-center relative"
                            :class="(selected === 'Requests') || window.location.href.includes(
                                '{{ route('request.index') }}') || window.location.href.includes(
                                '{{ route('shipments.selectCustomer') }}') ? 'menu-item-active' : 'menu-item-inactive'">

                            <!-- السهم المطلق في أقصى اليسار -->
                            <svg class="absolute left-4 transition-transform duration-300"
                                :class="(selected === 'Requests') ? 'rotate-180' : ''" width="20" height="20"
                                viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <!-- الأيقونة مع هامش لترك مساحة للسهم -->
                            <svg :class="(selected === 'Requests') || window.location.href.includes(
                                    '{{ route('request.index') }}') || window.location.href.includes(
                                    '{{ route('shipments.selectCustomer') }}') ? 'menu-item-icon-active' :
                                'menu-item-icon-inactive'"
                                class="ml-10" fill="#dc6803" width="30" height="30" viewBox="0 0 32 32"
                                id="icon" xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <defs>
                                        <style>
                                            .cls-1 {
                                                fill: none;
                                            }
                                        </style>
                                    </defs>
                                    <title>request</title>
                                    <path d="M22,22v6H6V4H16V2H6A2,2,0,0,0,4,4V28a2,2,0,0,0,2,2H22a2,2,0,0,0,2-2V22Z"
                                        transform="translate(0)"></path>
                                    <path
                                        d="M29.54,5.76l-3.3-3.3a1.6,1.6,0,0,0-2.24,0l-14,14V22h5.53l14-14a1.6,1.6,0,0,0,0-2.24ZM14.7,20H12V17.3l9.44-9.45,2.71,2.71ZM25.56,9.15,22.85,6.44l2.27-2.27,2.71,2.71Z"
                                        transform="translate(0)"></path>
                                    <rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;"
                                        class="cls-1" width="32" height="32">
                                    </rect>
                                </g>
                            </svg>

                            <!-- النص -->
                            <span class="menu-item-text ml-2 flex-1" :class="sidebarToggle ? 'lg:hidden' : ''">
                                إدارة الرسائل
                            </span>
                        </a>
                        <!-- Dropdown Menu Start -->
                        <div class="overflow-hidden transform translate"
                            :class="(selected === 'Requests') ? 'block' : 'hidden'">
                            <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
                                <li>
                                    <a href="{{ route('request.create') }}" class="menu-dropdown-item group"
                                        :class="window.location.href.includes('{{ route('request.index') }}') ?
                                            'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        رسائل عامة
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('shipments.selectCustomer') }}" class="menu-dropdown-item group"
                                        :class="window.location.href.includes('{{ route('shipments.selectCustomer') }}') ?
                                            'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                                        رسائل خاصة
                                    </a>
                                </li>

                            </ul>
                        </div>
                        <!-- Dropdown Menu End -->
                    </li>
                    <!-- Menu Item Forms -->
                    {{-- <li>
                        <a href="{{ route('request.index') }}"
                            @click="selected = (selected === 'Profile' ? '':'Profile')" class="menu-item group"
                            :class="window.location.href.includes('{{ route('request.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

                            <svg :class="window.location.href.includes('{{ route('request.index') }}') ? 'menu-item-icon-active' :
                                'menu-item-icon-inactive'"
                                fill="#dc6803" width="30" height="30" viewBox="0 0 32 32" id="icon"
                                xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <defs>
                                        <style>
                                            .cls-1 {
                                                fill: none;
                                            }
                                        </style>
                                    </defs>
                                    <title>request</title>
                                    <path d="M22,22v6H6V4H16V2H6A2,2,0,0,0,4,4V28a2,2,0,0,0,2,2H22a2,2,0,0,0,2-2V22Z"
                                        transform="translate(0)"></path>
                                    <path
                                        d="M29.54,5.76l-3.3-3.3a1.6,1.6,0,0,0-2.24,0l-14,14V22h5.53l14-14a1.6,1.6,0,0,0,0-2.24ZM14.7,20H12V17.3l9.44-9.45,2.71,2.71ZM25.56,9.15,22.85,6.44l2.27-2.27,2.71,2.71Z"
                                        transform="translate(0)"></path>
                                    <rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;"
                                        class="cls-1" width="32" height="32">
                                    </rect>
                                </g>
                            </svg>
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                                إدارة الطرود
                            </span>
                        </a>
                    </li> --}}
                    <!-- Menu Item Forms -->

                    <!-- Menu Item Tables -->
                    <li>
                        <a href="{{ route('request.adminlog') }}"
                            @click="selected = (selected === 'Profile' ? '':'Profile')" class="menu-item group"
                            :class="window.location.href.includes('{{ route('request.adminlog') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

                            <svg fill="currentColor" width="24" height="24" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20 6H4c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-1 10H5c-.55 0-1-.45-1-1V9c0-.55.45-1 1-1h14c.55 0 1 .45 1 1v6c0 .55-.45 1-1 1z" />
                                <circle cx="8.5" cy="11.5" r="1.5" />
                                <circle cx="15.5" cy="11.5" r="1.5" />
                                <path d="M8 15h8v1.5c0 .83-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5V15z" />
                            </svg>

                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                                عرض السجلات </span>
                        </a>
                    </li>
                    <!-- Menu Item Tables -->
                    <li>
                        <a href="{{ route('drivers.index') }}"
                            @click="selected = (selected === 'Profile' ? '':'Profile')" class="menu-item group"
                            :class="window.location.href.includes('{{ route('drivers.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path
                                        d="M22.875 8.625L21.75 9L21 10.5L19.1776 4.4253C18.9238 3.57933 18.1452 3 17.2619 3H6.73806C5.85484 3 5.0762 3.57934 4.82241 4.4253L3 10.5L2.25 9L1.125 8.625"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M1.5 19.468V21C1.5 21.3978 1.65804 21.7794 1.93934 22.0607C2.22064 22.342 2.60218 22.5 3 22.5C3.39782 22.5 3.77936 22.342 4.06066 22.0607C4.34196 21.7794 4.5 21.3978 4.5 21V19.555"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M22.5 19.468V21C22.5 21.3978 22.342 21.7794 22.0607 22.0607C21.7794 22.342 21.3978 22.5 21 22.5C20.6022 22.5 20.2206 22.342 19.9393 22.0607C19.658 21.7794 19.5 21.3978 19.5 21V19.5766"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M2.75 10.5H12H21.25C21.7804 10.5 22.2891 10.7299 22.6642 11.139C23.0393 11.5482 23.25 12.1032 23.25 12.6818V18.4091C23.25 18.6984 23.1446 18.9759 22.9571 19.1805C22.7696 19.3851 22.5152 19.5 22.25 19.5H1.75C1.48478 19.5 1.23043 19.3851 1.04289 19.1805C0.855357 18.9759 0.75 18.6984 0.75 18.4091V12.6818C0.75 12.1032 0.960714 11.5482 1.33579 11.139C1.71086 10.7299 2.21957 10.5 2.75 10.5Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path d="M0.75 14.25H5L6.125 16.5" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M14.25 16.5H9.75" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M23.25 14.25H19L17.875 16.5" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </g>
                            </svg>

                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                                ادارة السائقين

                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('finance.branches.index') }}"
                            @click="selected = (selected === 'Profile' ? '':'Profile')" class="menu-item group"
                            :class="window.location.href.includes('{{ route('finance.branches.index') }}') ?
                                'menu-item-active' :
                                'menu-item-inactive'">
                            <svg :class="window.location.href.includes('{{ route('finance.branches.index') }}') ?
                                'menu-item-icon-active' : 'menu-item-icon-inactive'"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>

                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                                ادارة المالية

                            </span>
                        </a>
                    </li>

                    <!-- Menu Item Pages -->
                    <li>
                        <a href="{{ route('reports.index') }}"
                            @click="selected = (selected === 'Profile' ? '':'Profile')" class="menu-item group"
                            :class="window.location.href.includes('{{ route('reports.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

                            <svg :class="window.location.href.includes('{{ route('reports.index') }}') ? 'menu-item-icon-active' :
                                'menu-item-icon-inactive'"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>

                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                                إلتقارير
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customers.index') }}"
                            @click="selected = (selected === 'Profile' ? '':'Profile')" class="menu-item group"
                            :class="window.location.href.includes('{{ route('customers.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

                            <svg :class="window.location.href.includes('{{ route('customers.index') }}') ? 'menu-item-icon-active' :
                                'menu-item-icon-inactive'"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" width="24" height="24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>

                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                                العملاء
                            </span>
                        </a>
                    </li>
                    <!-- Menu Item Pages -->
                </ul>
            </div>

            <!-- Others Group -->
            {{-- <div>
        <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            others
          </span>

          <svg :class="sidebarToggle ? 'lg:block hidden' : 'hidden'" class="mx-auto fill-current menu-group-icon"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
              fill="" />
          </svg>
        </h3>

        <ul class="flex flex-col gap-4 mb-6">
          <!-- Menu Item Charts -->
          <li>
            <a href="#" @click.prevent="selected = (selected === 'Charts' ? '':'Charts')" class="menu-item group"
              :class="(selected === 'Charts') || (page === 'lineChart' || page === 'barChart' || page === 'pieChart') ? 'menu-item-active' : 'menu-item-inactive'">
              <svg
                :class="(selected === 'Charts') || (page === 'lineChart' || page === 'barChart' || page === 'pieChart') ? 'menu-item-icon-active'  :'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M12 2C11.5858 2 11.25 2.33579 11.25 2.75V12C11.25 12.4142 11.5858 12.75 12 12.75H21.25C21.6642 12.75 22 12.4142 22 12C22 6.47715 17.5228 2 12 2ZM12.75 11.25V3.53263C13.2645 3.57761 13.7659 3.66843 14.25 3.80098V3.80099C15.6929 4.19606 16.9827 4.96184 18.0104 5.98959C19.0382 7.01734 19.8039 8.30707 20.199 9.75C20.3316 10.2341 20.4224 10.7355 20.4674 11.25H12.75ZM2 12C2 7.25083 5.31065 3.27489 9.75 2.25415V3.80099C6.14748 4.78734 3.5 8.0845 3.5 12C3.5 16.6944 7.30558 20.5 12 20.5C15.9155 20.5 19.2127 17.8525 20.199 14.25H21.7459C20.7251 18.6894 16.7492 22 12 22C6.47715 22 2 17.5229 2 12Z"
                  fill="" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Charts
              </span>

              <svg class="menu-item-arrow absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current"
                :class="[(selected === 'Charts') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="overflow-hidden transform translate" :class="(selected === 'Charts') ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
                <li>
                  <a href="line-chart.html" class="menu-dropdown-item group"
                    :class="page === 'lineChart' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Line Chart
                  </a>
                </li>
                <li>
                  <a href="bar-chart.html" class="menu-dropdown-item group"
                    :class="page === 'barChart' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Bar Chart
                  </a>
                </li>
              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>
          <!-- Menu Item Charts -->

          <!-- Menu Item Ui Elements -->
          <li>
            <a href="#" @click.prevent="selected = (selected === 'UIElements' ? '':'UIElements')"
              class="menu-item group"
              :class="(selected === 'UIElements') || (page === 'alerts' || page === 'avatars' || page === 'badge' || page === 'buttons' || page === 'buttonsGroup' || page === 'cards'|| page === 'carousel' || page === 'dropdowns' || page === 'images' || page === 'list' || page === 'modals' || page === 'videos') ? 'menu-item-active' : 'menu-item-inactive'">
              <svg
                :class="(selected === 'UIElements') || (page === 'alerts' || page === 'avatars' || page === 'badge' || page === 'breadcrumb' || page === 'buttons' || page === 'buttonsGroup' || page === 'cards'|| page === 'carousel' || page === 'dropdowns' || page === 'images' || page === 'list' || page === 'modals' || page === 'notifications' || page === 'popovers' || page === 'progress' || page === 'spinners' || page === 'tooltips' || page === 'videos') ? 'menu-item-icon-active'  :'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M11.665 3.75618C11.8762 3.65061 12.1247 3.65061 12.3358 3.75618L18.7807 6.97853L12.3358 10.2009C12.1247 10.3064 11.8762 10.3064 11.665 10.2009L5.22014 6.97853L11.665 3.75618ZM4.29297 8.19199V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0365V11.6512C11.1631 11.6205 11.0777 11.5843 10.9942 11.5425L4.29297 8.19199ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19199L13.0066 11.5425C12.9229 11.5844 12.8372 11.6207 12.75 11.6515V20.037ZM13.0066 2.41453C12.3732 2.09783 11.6277 2.09783 10.9942 2.41453L4.03676 5.89316C3.27449 6.27429 2.79297 7.05339 2.79297 7.90563V16.0946C2.79297 16.9468 3.27448 17.7259 4.03676 18.1071L10.9942 21.5857L11.3296 20.9149L10.9942 21.5857C11.6277 21.9024 12.3732 21.9024 13.0066 21.5857L19.9641 18.1071C20.7264 17.7259 21.2079 16.9468 21.2079 16.0946V7.90563C21.2079 7.05339 20.7264 6.27429 19.9641 5.89316L13.0066 2.41453Z"
                  fill="" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                UI Elements
              </span>

              <svg class="menu-item-arrow absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current"
                :class="[(selected === 'UIElements') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="overflow-hidden transform translate" :class="(selected === 'UIElements') ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
                <li>
                  <a href="alerts.html" class="menu-dropdown-item group"
                    :class="page === 'alerts' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Alerts
                  </a>
                </li>
                <li>
                  <a href="avatars.html" class="menu-dropdown-item group"
                    :class="page === 'avatars' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Avatars
                  </a>
                </li>
                <li>
                  <a href="badge.html" class="menu-dropdown-item group"
                    :class="page === 'badge' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Badges
                  </a>
                </li>
                <li>
                  <a href="buttons.html" class="menu-dropdown-item group"
                    :class="page === 'buttons' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Buttons
                  </a>
                </li>
                <li>
                  <a href="images.html" class="menu-dropdown-item group"
                    :class="page === 'images' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Images
                  </a>
                </li>
                <li>
                  <a href="videos.html" class="menu-dropdown-item group"
                    :class="page === 'videos' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Videos
                  </a>
                </li>
              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>
          <!-- Menu Item Ui Elements -->

          <!-- Menu Item Authentication -->
          <li>
            <a href="#" @click.prevent="selected = (selected === 'Authentication' ? '':'Authentication')"
              class="menu-item group"
              :class="(selected === 'Authentication') || (page === 'basicChart' || page === 'advancedChart') ? 'menu-item-active' : 'menu-item-inactive'">
              <svg
                :class="(selected === 'Authentication') || (page === 'basicChart' || page === 'advancedChart') ? 'menu-item-icon-active'  :'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M14 2.75C14 2.33579 14.3358 2 14.75 2C15.1642 2 15.5 2.33579 15.5 2.75V5.73291L17.75 5.73291H19C19.4142 5.73291 19.75 6.0687 19.75 6.48291C19.75 6.89712 19.4142 7.23291 19 7.23291H18.5L18.5 12.2329C18.5 15.5691 15.9866 18.3183 12.75 18.6901V21.25C12.75 21.6642 12.4142 22 12 22C11.5858 22 11.25 21.6642 11.25 21.25V18.6901C8.01342 18.3183 5.5 15.5691 5.5 12.2329L5.5 7.23291H5C4.58579 7.23291 4.25 6.89712 4.25 6.48291C4.25 6.0687 4.58579 5.73291 5 5.73291L6.25 5.73291L8.5 5.73291L8.5 2.75C8.5 2.33579 8.83579 2 9.25 2C9.66421 2 10 2.33579 10 2.75L10 5.73291L14 5.73291V2.75ZM7 7.23291L7 12.2329C7 14.9943 9.23858 17.2329 12 17.2329C14.7614 17.2329 17 14.9943 17 12.2329L17 7.23291L7 7.23291Z"
                  fill="" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Authentication
              </span>

              <svg class="menu-item-arrow absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current"
                :class="[(selected === 'Authentication') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="overflow-hidden transform translate"
              :class="(selected === 'Authentication') ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
                <li>
                  <a href="signin.html" class="menu-dropdown-item group"
                    :class="page === 'signin' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Sign In
                  </a>
                </li>
                <li>
                  <a href="signup.html" class="menu-dropdown-item group"
                    :class="page === 'signup' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Sign Up
                  </a>
                </li>
              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>
          <!-- Menu Item Authentication -->
        </ul>
      </div> --}}
        </nav>
        <!-- Sidebar Menu -->
    </div>
</aside>
