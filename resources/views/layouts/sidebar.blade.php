<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : 'translate-x-full'"
  class="sidebar fixed right-0 top-0 z-40 flex h-screen w-[290px] flex-col overflow-y-hidden border-l border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black duration-300 ease-in-out transition-transform lg:static lg:translate-x-0"
  @click.outside="sidebarToggle = false">


  <!-- SIDEBAR HEADER -->
  <div :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    class="flex items-center gap-2 pt-8 sidebar-header pb-3">
    <a href="#">
      <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
        <img class="dark:hidden w-12 h-12" src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" alt="Logo" />
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

          <svg :class="sidebarToggle ? 'lg:block hidden' : 'hidden'" class="mx-auto fill-current menu-group-icon"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
              fill="" />
          </svg>
        </h3>

        <ul class="flex flex-col gap-4 mb-6">
          <!-- Menu Item Dashboard -->
          <li>
            <a href="{{ route('dashboard.index') }}" class="menu-item group" :class="window.location.href.includes('{{ route('dashboard.index') }}') ?
                                'menu-item-active' : 'menu-item-inactive'">
              <svg :class="window.location.href.includes('{{ route('dashboard.index') }}') ?
                                'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24"
                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
              :class="window.location.href.includes('{{ route('users.index') }}') ? 'menu-item-active' : 'menu-item-inactive'">
              <svg
                :class="window.location.href.includes('{{ route('users.index') }}') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
          <li>
            <a href="{{ route('drivers.index') }}" @click="selected = (selected === 'Profile' ? '':'Profile')"
              class="menu-item group"
              :class="window.location.href.includes('{{ route('drivers.index') }}') ? 'menu-item-active' : 'menu-item-inactive'">
              <svg
                :class="window.location.href.includes('{{ route('drivers.index') }}') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                fill="#dc6803" width="30" height="30" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg">
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
                  <title>bus</title>
                  <rect x="27" y="11" width="2" height="4"></rect>
                  <rect x="3" y="11" width="2" height="4"></rect>
                  <rect x="20" y="20" width="2" height="2"></rect>
                  <rect x="10" y="20" width="2" height="2"></rect>
                  <path
                    d="M21,4H11A5.0059,5.0059,0,0,0,6,9V23a2.0023,2.0023,0,0,0,2,2v3h2V25H22v3h2V25a2.0027,2.0027,0,0,0,2-2V9A5.0059,5.0059,0,0,0,21,4Zm3,6,.0009,6H8V10ZM11,6H21a2.995,2.995,0,0,1,2.8157,2H8.1843A2.995,2.995,0,0,1,11,6ZM8,23V18H24.0012l.0008,5Z"
                    transform="translate(0 0)"></path>
                  <rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32"
                    height="32">
                  </rect>
                </g>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                إدارة السائقين
              </span>
            </a>
          </li>
          <!-- Menu Item Profile -->

          <!-- Menu Item Forms -->
          <li>
            <a href="{{ route('request.index') }}" @click="selected = (selected === 'Profile' ? '':'Profile')"
              class="menu-item group"
              :class="window.location.href.includes('{{ route('request.index') }}') ? 'menu-item-active' : 'menu-item-inactive'">

              <svg
                :class="window.location.href.includes('{{ route('request.index') }}') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                fill="#dc6803" width="30" height="30" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg">
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
                  <rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32"
                    height="32">
                  </rect>
                </g>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                إدارة الطلبات
              </span>
            </a>
          </li>
          <!-- Menu Item Forms -->

          <!-- Menu Item Tables -->
          {{-- <li>
            <a href="{{ route('Coupon.index') }}" @click="selected = (selected === 'Profile' ? '':'Profile')"
              class="menu-item group"
              :class="window.location.href.includes('{{ route('Coupon.index') }}') ? 'menu-item-active' : 'menu-item-inactive'">

              <svg fill="currentColor" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M20 6H4c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-1 10H5c-.55 0-1-.45-1-1V9c0-.55.45-1 1-1h14c.55 0 1 .45 1 1v6c0 .55-.45 1-1 1z" />
                <circle cx="8.5" cy="11.5" r="1.5" />
                <circle cx="15.5" cy="11.5" r="1.5" />
                <path d="M8 15h8v1.5c0 .83-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5V15z" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                كوبونات الخصم
              </span>
            </a>
          </li> --}}
          <!-- Menu Item Tables -->
          {{-- <li>
            <a href="{{ route('Vehicle.index') }}" @click="selected = (selected === 'Profile' ? '':'Profile')"
              class="menu-item group"
              :class="window.location.href.includes('{{ route('Vehicle.index') }}') ? 'menu-item-active' : 'menu-item-inactive'">

              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                  <path
                    d="M22.875 8.625L21.75 9L21 10.5L19.1776 4.4253C18.9238 3.57933 18.1452 3 17.2619 3H6.73806C5.85484 3 5.0762 3.57934 4.82241 4.4253L3 10.5L2.25 9L1.125 8.625"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path
                    d="M1.5 19.468V21C1.5 21.3978 1.65804 21.7794 1.93934 22.0607C2.22064 22.342 2.60218 22.5 3 22.5C3.39782 22.5 3.77936 22.342 4.06066 22.0607C4.34196 21.7794 4.5 21.3978 4.5 21V19.555"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path
                    d="M22.5 19.468V21C22.5 21.3978 22.342 21.7794 22.0607 22.0607C21.7794 22.342 21.3978 22.5 21 22.5C20.6022 22.5 20.2206 22.342 19.9393 22.0607C19.658 21.7794 19.5 21.3978 19.5 21V19.5766"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path
                    d="M2.75 10.5H12H21.25C21.7804 10.5 22.2891 10.7299 22.6642 11.139C23.0393 11.5482 23.25 12.1032 23.25 12.6818V18.4091C23.25 18.6984 23.1446 18.9759 22.9571 19.1805C22.7696 19.3851 22.5152 19.5 22.25 19.5H1.75C1.48478 19.5 1.23043 19.3851 1.04289 19.1805C0.855357 18.9759 0.75 18.6984 0.75 18.4091V12.6818C0.75 12.1032 0.960714 11.5482 1.33579 11.139C1.71086 10.7299 2.21957 10.5 2.75 10.5Z"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M0.75 14.25H5L6.125 16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                  <path d="M14.25 16.5H9.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                  <path d="M23.25 14.25H19L17.875 16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                </g>
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                إدارة المركبات
              </span>
            </a>
          </li> --}}

          <!-- Menu Item Pages -->
          {{-- <li>
            <a href="{{ route('systems.index') }}" @click="selected = (selected === 'Profile' ? '':'Profile')"
              class="menu-item group"
              :class="window.location.href.includes('{{ route('systems.index') }}') ? 'menu-item-active' : 'menu-item-inactive'">

              <svg
                :class="window.location.href.includes('{{ route('systems.index') }}') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                  stroke-width="0.32"></g>
                <g id="SVGRepo_iconCarrier">
                  <path
                    d="M11.078035,0 C11.3724371,0 11.6350302,0.183056678 11.7339285,0.457230801 L12.4396543,2.41370379 C12.6930786,2.47650967 12.9108093,2.54030436 13.0942558,2.60592238 C13.2952001,2.67779926 13.5540423,2.78741449 13.8746747,2.93586046 L15.5184936,2.06596774 C15.794148,1.92009416 16.1343396,1.97375545 16.3504064,2.19719235 L17.7960017,3.69209722 C17.98787,3.8905102 18.0422042,4.18265582 17.9342767,4.43557836 L17.162857,6.24336136 C17.2913496,6.47797752 17.3939318,6.67854463 17.4711247,6.84658462 C17.5553742,7.02998593 17.6588292,7.28241713 17.7829588,7.60671302 L19.580333,8.37623211 C19.8497082,8.4915611 20.0170118,8.76152727 19.9986241,9.05119919 L19.8669066,11.126215 C19.8494975,11.4004703 19.668825,11.6382294 19.4072566,11.7310995 L17.7049419,12.3355083 C17.6562729,12.5705675 17.6053791,12.772402 17.551477,12.9424906 C17.4883274,13.1417599 17.389065,13.3979932 17.2526724,13.7155635 L18.1084613,15.6068822 C18.2316885,15.879218 18.1635045,16.1990386 17.9395819,16.3990196 L16.3138559,17.8509252 C16.0957241,18.0457347 15.7773686,18.084139 15.5182641,17.9469002 L13.8421792,17.0591354 C13.5477413,17.2124998 13.2783109,17.3348831 13.0324612,17.4263047 C12.8127426,17.5080092 12.5685018,17.5992352 12.2997543,17.6999771 L11.6499659,19.5002547 C11.5529743,19.7689756 11.2984077,19.9507082 11.0099151,19.9571805 L9.10927684,20 C8.81300029,20.0064684 8.54492708,19.8269822 8.44118272,19.5525044 L7.6751041,17.5256808 C7.3232066,17.4026533 7.06612795,17.3070148 6.89898146,17.2366139 C6.74058527,17.1698987 6.53544077,17.0722593 6.28058727,16.9426265 L4.38190658,17.7549437 C4.12577479,17.8645252 3.82821583,17.812324 3.62584584,17.6223069 L2.22106797,16.3032781 C2.00593124,16.1012735 1.94386481,15.7866753 2.0664447,15.519534 L2.88322205,13.7395109 C2.76017205,13.4979963 2.65957084,13.2838441 2.58116863,13.0959926 C2.49857708,12.8981035 2.39862152,12.628586 2.28009144,12.2848943 L0.491710371,11.7412063 C0.190471763,11.6496264 -0.0110085834,11.3694211 0.000465944291,11.0580168 L0.071249487,9.13703967 C0.0806273232,8.88253689 0.2313194,8.65393727 0.463026654,8.5427144 L2.34096885,7.64127468 C2.42759746,7.32228988 2.50386559,7.07419005 2.57165467,6.89274074 C2.63878533,6.7130538 2.74293335,6.47740464 2.8854431,6.18143219 L2.06997455,4.45996626 C1.94312228,4.19217928 2.00336838,3.87420996 2.21965377,3.66998322 L3.62443163,2.34352648 C3.82439931,2.15470767 4.11840992,2.10075785 4.37358276,2.20606012 L6.27210909,2.98952564 C6.4823493,2.85093927 6.67247185,2.73658268 6.84371422,2.6461118 C7.04934485,2.53747289 7.3225817,2.42318736 7.66837893,2.29976859 L8.32789678,0.458652854 C8.42637398,0.183743082 8.68933933,0 8.98430143,0 L11.078035,0 Z M10.5870708,1.37705446 L9.47640909,1.37705446 L8.86948809,3.07134033 C8.79801391,3.27086827 8.63769115,3.42680114 8.43474473,3.49417973 C7.99915007,3.63879796 7.68401915,3.76347045 7.50006723,3.86065605 C7.30479682,3.96382144 7.05649639,4.12077045 6.75923344,4.33037214 C6.56321164,4.46858816 6.30888789,4.49688254 6.08670302,4.40519344 L4.25716409,3.65019697 L3.54351334,4.32405928 L4.2874951,5.89461557 C4.37724721,6.08408357 4.375001,6.30362324 4.2813906,6.49125715 C4.08134734,6.89222649 3.94589193,7.18842613 3.87809235,7.36990353 C3.8103478,7.55123365 3.71917424,7.8615808 3.60874921,8.29154274 C3.55823748,8.48822027 3.42255355,8.65285199 3.23793163,8.74147321 L1.44780841,9.60075854 L1.41171745,10.5802222 L3.03130231,11.0725942 C3.24698647,11.1381646 3.41716675,11.3030041 3.48790571,11.5148681 C3.64831871,11.9953067 3.7761118,12.3491471 3.86857839,12.5706968 C3.95934025,12.788162 4.09417527,13.0648832 4.27239921,13.3979523 C4.37321509,13.5863595 4.38009307,13.810311 4.29102495,14.0044194 L3.53951667,15.6421999 L4.25118794,16.3104301 L6.03251181,15.5483219 C6.22679544,15.465201 6.44877026,15.4739754 6.6357229,15.5721659 C7.00102332,15.7640277 7.27322021,15.8974495 7.44479999,15.9697176 C7.61953128,16.0433131 7.9548478,16.1653796 8.44065086,16.3314709 C8.63693275,16.3985778 8.7926767,16.548807 8.86538101,16.7411617 L9.57250939,18.6120199 L10.4993142,18.591227 L11.0961476,16.9376646 C11.1646975,16.747743 11.3140075,16.5970812 11.5046571,16.5254566 C11.8958011,16.3785088 12.2416349,16.2491584 12.5421977,16.1373911 C12.8039897,16.0400412 13.1223034,15.8867935 13.4934399,15.6776981 C13.7015148,15.5604703 13.9559143,15.5571841 14.1670155,15.6689972 L15.7439676,16.5042547 L16.6305025,15.7125069 L15.8561342,14.0011301 C15.7756691,13.8232999 15.7751619,13.6200892 15.8547382,13.4418685 C16.0370606,13.0335353 16.160351,12.7267619 16.2224704,12.5307434 C16.2832183,12.3390526 16.3456807,12.0622192 16.407072,11.7055024 C16.4492368,11.4605022 16.6216614,11.2569821 16.8582214,11.1729911 L18.5056035,10.5880863 L18.5779926,9.44770174 L16.9575114,8.75391676 C16.7827815,8.67910873 16.6466533,8.53682007 16.5808114,8.36016907 C16.4198441,7.92830106 16.2929314,7.61166725 16.2030747,7.41605965 C16.1143251,7.22286224 15.9738341,6.95669259 15.7830295,6.62172026 C15.677285,6.43607749 15.6644194,6.21248859 15.7481863,6.01618508 L16.4733058,4.31690418 L15.7093237,3.52686246 L14.2211258,4.31440213 C14.0242762,4.41857298 13.7888454,4.4230994 13.5880489,4.32657373 C13.1546705,4.11824271 12.829188,3.97550384 12.6204677,3.90084548 C12.4166956,3.82795707 12.1292655,3.75057315 11.7626377,3.671335 C11.5278825,3.62059807 11.3361711,3.45377367 11.2554479,3.22998614 L10.5870708,1.37705446 Z M10.0237083,5.64149212 C12.459442,5.64149212 14.4339942,7.59383168 14.4339942,10.0021646 C14.4339942,12.4104975 12.459442,14.362837 10.0237083,14.362837 C7.58797468,14.362837 5.61342244,12.4104975 5.61342244,10.0021646 C5.61342244,7.59383168 7.58797468,5.64149212 10.0237083,5.64149212 Z M10.0237083,7.01854658 C8.35715373,7.01854658 7.00614429,8.35435786 7.00614429,10.0021646 C7.00614429,11.6499713 8.35715373,12.9857826 10.0237083,12.9857826 C11.6902629,12.9857826 13.0412723,11.6499713 13.0412723,10.0021646 C13.0412723,8.35435786 11.6902629,7.01854658 10.0237083,7.01854658 Z"
                    fill="currentColor" />
                </g>
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                اعدادات النظام
              </span>
            </a>
          </li> --}}
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