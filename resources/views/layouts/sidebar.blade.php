<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : 'translate-x-full'"
  class="sidebar fixed right-0 top-0 z-40 flex h-screen w-[290px] flex-col overflow-y-hidden border-l border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black duration-300 ease-in-out transition-transform lg:static lg:translate-x-0"
  @click.outside="sidebarToggle = false">


  <!-- SIDEBAR HEADER -->
  <div :class="sidebarToggle ? 'justify-center' : 'justify-between'" class="flex gap-2 items-center sidebar-header">
    <a href="#">
      <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
        <img width="150" height="150" class="h-auto dark:hidden"
          src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" alt="Logo" />
        {{-- Dark logo --}}
        <img width="200" height="200" class="hidden dark:block"
          src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" alt="Logo" />
      </span>

      <img class="w-12 h-12 logo-icon" :class="sidebarToggle ? 'lg:block' : 'hidden'"
        src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" alt="Logo" />
    </a>
  </div>
  <!-- SIDEBAR HEADER -->

  <div class="flex overflow-y-auto flex-col duration-300 ease-linear no-scrollbar">
    <!-- Sidebar Menu -->
    <nav x-data="{ selected: $persist('Dashboard') }">
      <!-- Menu Group -->
      <div>
        <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
          {{-- <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            القائمة الرئيسية
          </span> --}}

          <svg :class="sidebarToggle ? 'lg:block hidden' : 'hidden'" class="mx-auto fill-current menu-group-icon"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
            stroke="currentColor" stroke-width="1.5">
            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
              d="M3 6a3 3 0 013-3h2.25a3 3 0 013 3v2.25a3 3 0 01-3 3H6a3 3 0 01-3-3V6zm9.75 0a3 3 0 013-3H18a3 3 0 013 3v2.25a3 3 0 01-3 3h-2.25a3 3 0 01-3-3V6zM3 15.75a3 3 0 013-3h2.25a3 3 0 013 3V18a3 3 0 01-3 3H6a3 3 0 01-3-3v-2.25zm9.75 0a3 3 0 013-3H18a3 3 0 013 3V18a3 3 0 01-3 3h-2.25a3 3 0 01-3-3v-2.25z"
              clip-rule="evenodd" />
          </svg>
        </h3>

        <ul class="flex flex-col gap-4 mb-6">
          <!-- Menu Item Dashboard -->
          <li>
            <a href="{{ route('dashboard.index') }}" class="menu-item group" :class="window.location.href.includes('{{ route('dashboard.index') }}') ?
                                'menu-item-active' : 'menu-item-inactive'">
              <svg :class="window.location.href.includes('{{ route('dashboard.index') }}') ?
                                'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24" height="24"
                viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                  d="M3 6a3 3 0 013-3h2.25a3 3 0 013 3v2.25a3 3 0 01-3 3H6a3 3 0 01-3-3V6zm9.75 0a3 3 0 013-3H18a3 3 0 013 3v2.25a3 3 0 01-3 3h-2.25a3 3 0 01-3-3V6zM3 15.75a3 3 0 013-3h2.25a3 3 0 013 3V18a3 3 0 01-3 3H6a3 3 0 01-3-3v-2.25zm9.75 0a3 3 0 013-3H18a3 3 0 013 3V18a3 3 0 01-3 3h-2.25a3 3 0 01-3-3v-2.25z"
                  clip-rule="evenodd" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                الصفحة الرئيسية
              </span>
            </a>

            <!-- Dropdown Menu Start -->
            {{-- <div class="overflow-hidden transform translate"
              :class="(selected === 'Dashboard') ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 pl-9 mt-2 menu-dropdown">
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
          {{-- تاب إدارة الأفراد مع قائمة فرعية --}}
          <li
            x-init="@if(request()->routeIs('drivers.*') || request()->routeIs('users.*') || request()->routeIs('customers.*')) selected = 'People' @endif">
            <a href="#" @click.prevent="selected = (selected === 'People' ? '' : 'People')"
              class="menu-item group {{ request()->routeIs('drivers.*') || request()->routeIs('users.*') || request()->routeIs('customers.*') ? 'menu-item-active' : 'menu-item-inactive' }}">

              {{-- أيقونة الأفراد --}}
              <svg
                class="{{ request()->routeIs('drivers.*') || request()->routeIs('users.*') || request()->routeIs('customers.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"
                width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                  d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.372.568A12.696 12.696 0 0112 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 01-.372-.568 6.787 6.787 0 011.019-4.38z"
                  clip-rule="evenodd" />
                <path
                  d="M5.082 14.254a8.287 8.287 0 00-1.308 5.135 9.687 9.687 0 01-1.764-.44l-.115-.04a.563.563 0 01-.373-.487l-.01-.121a3.75 3.75 0 013.57-4.047zM20.226 19.389a8.287 8.287 0 00-1.308-5.135 3.75 3.75 0 013.57 4.047l-.01.121a.563.563 0 01-.373.486l-.115.04c-.567.2-1.156.349-1.764.441z" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                إدارة الأفراد
              </span>

              {{-- سهم القائمة الفرعية --}}
              <svg style="left: 10px; right: auto;"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current menu-item-arrow"
                :class="[(selected === 'People') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            {{-- القائمة الفرعية --}}
            <div class="overflow-hidden transform translate" :class="(selected === 'People') ? 'block' : 'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 pr-9 mt-2 menu-dropdown">
                {{-- السائقين --}}
                <li>
                  <a href="{{ route('drivers.index') }}"
                    class="menu-dropdown-item group {{ request()->routeIs('drivers.*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' }}">
                    السائقين
                  </a>
                </li>
                {{-- ادارة المستخدمين --}}
                @if (Auth::user()->type != 'user')
                  <li>
                    <a href="{{ route('users.index') }}"
                      class="menu-dropdown-item group {{ request()->routeIs('users.*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' }}">
                      ادارة المستخدمين
                    </a>
                  </li>
                @endif
                {{-- العملاء --}}
                <li>
                  <a href="{{ route('customers.index') }}"
                    class="menu-dropdown-item group {{ request()->routeIs('customers.*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' }}">
                    العملاء
                  </a>
                </li>
              </ul>
            </div>
          </li>

          <!-- Menu Item branch -->

          <li>
            <a href="{{ route('branch.index') }}" @click="selected = (selected === 'Profile' ? '':'Profile')"
              class="menu-item group" :class="window.location.href.includes('{{ route('branch.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">
              <svg :class="window.location.href.includes('{{ route('branch.index') }}') ? 'menu-item-icon-active' :
                                'menu-item-icon-inactive'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                fill="currentColor" width="24" height="24">
                <path
                  d="M19.006 3.705a.75.75 0 00-.512-1.41L6 6.838V3a.75.75 0 00-.75-.75h-1.5A.75.75 0 003 3v4.93l-1.006.365a.75.75 0 00.512 1.41l16.5-6z" />
                <path fill-rule="evenodd"
                  d="M3.019 11.115L18 5.667V9.09l4.006 1.456a.75.75 0 11-.512 1.41l-.494-.18v8.475h.75a.75.75 0 010 1.5H2.25a.75.75 0 010-1.5H3v-9.129l.019-.007zM18 20.25v-9.565l1.5.545v9.02H18zm-9-6a.75.75 0 00-.75.75v4.5c0 .414.336.75.75.75h3a.75.75 0 00.75-.75V15a.75.75 0 00-.75-.75H9z"
                  clip-rule="evenodd" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                إدارة المكاتب
              </span>
            </a>
          </li>


          <!-- Menu Item Forms -->
          <li>
            <a href="{{ route('shipment.index') }}"
              class="menu-item group {{ request()->routeIs('shipment.index') ? 'menu-item-active' : 'menu-item-inactive' }}">

              {{-- أيقونة قائمة الطرود - clipboard list --}}
              <svg
                class="{{ request()->routeIs('shipment.index') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"
                width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                  d="M7.502 6h7.128A3.375 3.375 0 0118 9.375v9.375a3 3 0 003-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 00-.673-.05A3 3 0 0015 1.5h-1.5a3 3 0 00-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6zM13.5 3A1.5 1.5 0 0012 4.5h4.5A1.5 1.5 0 0015 3h-1.5z"
                  clip-rule="evenodd" />
                <path fill-rule="evenodd"
                  d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 013 20.625V9.375zm9.586 4.594a.75.75 0 00-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 00-1.06 1.06l1.5 1.5a.75.75 0 001.116-.062l3-3.75z"
                  clip-rule="evenodd" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                إدارة الطرود
              </span>
            </a>
          </li>

          {{-- تاب الشحنات مع قائمة فرعية --}}
          <li
            x-init="@if(request()->routeIs('shipmentpackage.*') || request()->routeIs('receipts.*')) selected = 'Shipments' @endif">
            <a href="#" @click.prevent="selected = (selected === 'Shipments' ? '' : 'Shipments')"
              class="menu-item group {{ request()->routeIs('shipmentpackage.*') || request()->routeIs('receipts.*') ? 'menu-item-active' : 'menu-item-inactive' }}">

              {{-- أيقونة الشحنات --}}
              <svg
                class="{{ request()->routeIs('shipmentpackage.*') || request()->routeIs('receipts.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"
                width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h12V6.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM13.5 15h-12v2.625c0 1.035.84 1.875 1.875 1.875h.375a3 3 0 116 0h3a.75.75 0 00.75-.75V15z" />
                <path
                  d="M8.25 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0zM15.75 6.75a.75.75 0 00-.75.75v11.25c0 .087.015.17.042.248a3 3 0 015.958.464c.853-.175 1.522-.935 1.464-1.883a18.659 18.659 0 00-3.732-10.104 1.837 1.837 0 00-1.47-.725H15.75z" />
                <path d="M19.5 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                إدارة الشحنات
              </span>

              {{-- سهم القائمة الفرعية --}}
              <svg style="left: 10px; right: auto;"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current menu-item-arrow"
                :class="[(selected === 'Shipments') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            {{-- القائمة الفرعية --}}
            <div class="overflow-hidden transform translate" :class="(selected === 'Shipments') ? 'block' : 'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 pr-9 mt-2 menu-dropdown">
                {{-- ارسال الشحنات --}}
                <li>
                  <a href="{{ route('shipmentpackage.index') }}"
                    class="menu-dropdown-item group {{ request()->routeIs('shipmentpackage.*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' }}">
                    الشحنات المرسله
                  </a>
                </li>
                {{-- استلام شحنات --}}
                <li>
                  <a href="{{ route('receipts.index') }}"
                    class="menu-dropdown-item group {{ request()->routeIs('receipts.*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' }}">
                    الشحنات المستلمه
                  </a>
                </li>
              </ul>
            </div>
          </li>
          @if (Auth::user()->type != 'user')
            <li>
              <a href="{{ route('transactions.index') }}"
                class="menu-item group {{ request()->routeIs('transactions.*') && !request()->routeIs('transaction-categories.*') ? 'menu-item-active' : 'menu-item-inactive' }}">

                {{-- أيقونة تدل على النقدية --}}
                <svg
                  class="{{ request()->routeIs('transactions.*') && !request()->routeIs('transaction-categories.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"
                  width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 7.5a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" />
                  <path fill-rule="evenodd"
                    d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3h17.25c1.035 0 1.875.84 1.875 1.875v9.75c0 1.036-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 011.5 14.625v-9.75zM8.25 9.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM18.75 9a.75.75 0 00-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 00.75-.75V9.75a.75.75 0 00-.75-.75h-.008zM4.5 9.75A.75.75 0 015.25 9h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75V9.75z"
                    clip-rule="evenodd" />
                  <path
                    d="M2.25 18a.75.75 0 000 1.5c5.4 0 10.63.722 15.6 2.075 1.19.324 2.4-.558 2.4-1.82V18.75a.75.75 0 00-.75-.75H2.25z" />
                </svg>

                <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                  الصندوق المالي
                </span>
              </a>
            </li>
          @endif
          @if (Auth::user()->type != 'user')
            <li>
              <a href="{{ route('transaction-categories.index') }}"
                class="menu-item group {{ request()->routeIs('transaction-categories.*') ? 'menu-item-active' : 'menu-item-inactive' }}">

                {{-- أيقونة الإعدادات --}}
                <svg
                  class="{{ request()->routeIs('transaction-categories.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"
                  width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd"
                    d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 00-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 00-2.282.819l-.922 1.597a1.875 1.875 0 00.432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 000 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 00-.432 2.385l.922 1.597a1.875 1.875 0 002.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 002.28-.819l.923-1.597a1.875 1.875 0 00-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 000-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 00-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 00-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 00-1.85-1.567h-1.843zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"
                    clip-rule="evenodd" />
                </svg>

                <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                  إعدادات الفئات
                </span>
              </a>
            </li>
          @endif

          @if (Auth::user()->type != 'user')
            {{-- رابط الإقفال اليومي --}}
            <li>
              <a href="{{ route('closings.index') }}"
                class="menu-item group {{ request()->routeIs('closings.*') ? 'menu-item-active' : 'menu-item-inactive' }}">

                {{-- Icon --}}
                <svg class="{{ request()->routeIs('closings.*') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"
                  width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd"
                    d="M7.502 6h7.128A3.375 3.375 0 0118 9.375v9.375a3 3 0 003-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 00-.673-.05A3 3 0 0015 1.5h-1.5a3 3 0 00-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6zM13.5 3A1.5 1.5 0 0012 4.5h4.5A1.5 1.5 0 0015 3h-1.5z"
                    clip-rule="evenodd" />
                  <path fill-rule="evenodd"
                    d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 013 20.625V9.375zM6 12a.75.75 0 01.75-.75h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75V12zm2.25 0a.75.75 0 01.75-.75h3.75a.75.75 0 010 1.5H9a.75.75 0 01-.75-.75zM6 15a.75.75 0 01.75-.75h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75V15zm2.25 0a.75.75 0 01.75-.75h3.75a.75.75 0 010 1.5H9a.75.75 0 01-.75-.75zM6 18a.75.75 0 01.75-.75h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75V18zm2.25 0a.75.75 0 01.75-.75h3.75a.75.75 0 010 1.5H9a.75.75 0 01-.75-.75z"
                    clip-rule="evenodd" />
                </svg>

                <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                  سجل الإقفال اليومي
                </span>
              </a>
            </li>
          @endif

          <li>
            <button @click="window.dispatchEvent(new CustomEvent('open-transaction-modal'))"
              class="w-full text-right menu-item group menu-item-inactive">
              <svg class="menu-item-icon-inactive group-hover:menu-item-icon-active" width="24" height="24"
                viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd"
                  d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"
                  clip-rule="evenodd" />
              </svg>
              <span class="menu-item-text">إضافة سندات</span>
            </button>
          </li>
          @if (Auth::user()->type != 'user')
            <li>
              <button @click="window.dispatchEvent(new CustomEvent('open-closing-modal'))"
                class="w-full text-right menu-item group menu-item-inactive">
                <svg class="menu-item-icon-inactive group-hover:menu-item-icon-active" width="24" height="24"
                  viewBox="0 0 24 24" fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z"
                    clip-rule="evenodd" />
                </svg>
                <span class="menu-item-text">إضافة إقفال</span>
              </button>
            </li>
          @endif
          <!-- Menu Item Forms -->


          <!-- Menu Item Tables -->
          {{-- <li>
            <a href="{{ route('shipment.adminlog') }}" @click="selected = (selected === 'Profile' ? '':'Profile')"
              class="menu-item group" :class="window.location.href.includes('{{ route('shipment.adminlog') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

              <svg fill="currentColor" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M20 6H4c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-1 10H5c-.55 0-1-.45-1-1V9c0-.55.45-1 1-1h14c.55 0 1 .45 1 1v6c0 .55-.45 1-1 1z" />
                <circle cx="8.5" cy="11.5" r="1.5" />
                <circle cx="15.5" cy="11.5" r="1.5" />
                <path d="M8 15h8v1.5c0 .83-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5V15z" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                عرض السجلات </span>
            </a>
          </li> --}}
          <!-- Menu Item Tables -->
          {{-- <li>
            <a href="{{ route('drivers.index') }}" @click="selected = (selected === 'Profile' ? '':'Profile')"
              class="menu-item group" :class="window.location.href.includes('{{ route('drivers.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

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
                ادارة السائقين

              </span>
            </a>
          </li> --}}
          {{-- <li>
            <a href="{{ route('finance.branches.index') }}" @click="selected = (selected === 'Profile' ? '':'Profile')"
              class="menu-item group" :class="window.location.href.includes('{{ route('finance.branches.index') }}') ?
                                'menu-item-active' :
                                'menu-item-inactive'">
              <svg :class="window.location.href.includes('{{ route('finance.branches.index') }}') ?
                                'menu-item-icon-active' : 'menu-item-icon-inactive'" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
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
            <a href="{{ route('reports.index') }}" @click="selected = (selected === 'Profile' ? '':'Profile')"
              class="menu-item group" :class="window.location.href.includes('{{ route('reports.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

              <svg :class="window.location.href.includes('{{ route('reports.index') }}') ? 'menu-item-icon-active' :
                                'menu-item-icon-inactive'" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="24" height="24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                إلتقارير
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

              <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current menu-item-arrow"
                :class="[(selected === 'Charts') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="overflow-hidden transform translate" :class="(selected === 'Charts') ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 pl-9 mt-2 menu-dropdown">
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

              <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current menu-item-arrow"
                :class="[(selected === 'UIElements') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="overflow-hidden transform translate" :class="(selected === 'UIElements') ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 pl-9 mt-2 menu-dropdown">
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

              <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current menu-item-arrow"
                :class="[(selected === 'Authentication') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="overflow-hidden transform translate"
              :class="(selected === 'Authentication') ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 pl-9 mt-2 menu-dropdown">
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