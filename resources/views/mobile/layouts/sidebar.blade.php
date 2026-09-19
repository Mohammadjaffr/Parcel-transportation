<nav x-data="{ showMoreMenu: false }"
    class="fixed bottom-0 left-0 w-full glass-nav shadow-[0_-8px_24px_rgba(36,56,156,0.06)] z-50 rounded-t-3xl bg-white select-none">

    <!-- Floating Menu Overlay -->
    <div x-show="showMoreMenu" 
         @click="showMoreMenu = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm" 
         style="display: none; z-index: -1;">
    </div>

    <!-- More Menu Content -->
    <div x-show="showMoreMenu" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-12"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-12"
         class="absolute bottom-full left-4 right-4 mb-2 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 flex flex-col gap-1 overflow-hidden" 
         style="display: none; z-index: -1;">
         
        <a href="{{ route('people.index') }}"
            class="flex items-center gap-4 px-4 py-3 transition-colors rounded-xl {{ Route::is(['people.*', 'drivers.*', 'users.*', 'customers.*']) ? 'bg-primary-container/50 text-primary' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-[24px]"
                style="font-variation-settings: 'FILL' {{ Route::is(['people.*', 'drivers.*', 'users.*', 'customers.*']) ? 1 : 0 }};">
                group
            </span>
            <span class="font-headline text-[13px] {{ Route::is(['people.*', 'drivers.*', 'users.*', 'customers.*']) ? 'font-bold' : 'font-medium' }}">
                الأفراد
            </span>
        </a>

        <a href="{{ route('mobile.office') }}"
            class="flex items-center gap-4 px-4 py-3 transition-colors rounded-xl {{ Route::is(['mobile.office', 'offices.unverified.index', 'offices.create', 'app.index']) ? 'bg-primary-container/50 text-primary' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-[24px]"
                style="font-variation-settings: 'FILL' {{ Route::is(['mobile.office', 'offices.unverified.index', 'offices.create', 'app.index']) ? 1 : 0 }};">
                apartment
            </span>
            <span class="font-headline text-[13px] {{ Route::is(['mobile.office', 'offices.unverified.index', 'offices.create', 'app.index']) ? 'font-bold' : 'font-medium' }}">
                المكاتب
            </span>
        </a>

        <a href="{{ route('mobile.cash.index') }}"
            class="flex items-center gap-4 px-4 py-3 transition-colors rounded-xl {{ Route::is(['mobile.cash.index', 'cash.ledger.*', 'cash-categories.*']) ? 'bg-primary-container/50 text-primary' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-[24px]"
                style="font-variation-settings: 'FILL' {{ Route::is(['mobile.cash.index', 'cash.ledger.*', 'cash-categories.*']) ? 1 : 0 }};">
                account_balance_wallet
            </span>
            <span class="font-headline text-[13px] {{ Route::is(['mobile.cash.index', 'cash.ledger.*', 'cash-categories.*']) ? 'font-bold' : 'font-medium' }}">
                الصندوق المالي
            </span>
        </a>

        <a href="{{ route('shipment.quickScan') }}"
            class="flex items-center gap-4 px-4 py-3 transition-colors rounded-xl {{ Route::is('shipment.quickScan') ? 'bg-[#fb6514]/10 text-[#fb6514]' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="material-symbols-outlined text-[24px] text-[#fb6514]">
                barcode_scanner
            </span>
            <span class="font-headline text-[13px] font-bold text-[#fb6514]">
                الاستلام السريع (باركود)
            </span>
        </a>
    </div>

    <!-- Main Navigation Bar -->
    <div class="flex flex-row justify-around items-center px-1 sm:px-2 pb-4 pt-4 bg-white rounded-t-3xl relative z-10 w-full overflow-x-auto">
        <a href="{{ route('dashboard.index') }}"
            class="flex flex-col items-center justify-center px-1 py-2 xs:px-2 transition-all active:scale-90 rounded-2xl {{ Route::is('dashboard.index') ? 'bg-primary-container text-primary' : 'text-slate-400 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[20px] xs:text-[24px]"
                style="font-variation-settings: 'FILL' {{ Route::is('dashboard.index') ? 1 : 0 }};">
                home
            </span>
            <span class="font-headline text-[9px] xs:text-[10px] {{ Route::is('dashboard.index') ? 'font-bold' : 'font-medium' }} mt-1 whitespace-nowrap">
                الرئيسية
            </span>
        </a>

        <a href="{{ route('mobile.shipment') }}"
            class="flex flex-col items-center justify-center px-1 py-2 xs:px-2 transition-all active:scale-90 rounded-2xl {{ Route::is(['mobile.shipment', 'shipment.*']) ? 'bg-primary-container text-primary' : 'text-slate-400 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[20px] xs:text-[24px]"
                style="font-variation-settings: 'FILL' {{ Route::is(['mobile.shipment', 'shipment.*']) ? 1 : 0 }};">inventory_2</span>
            <span class="font-headline text-[9px] xs:text-[10px] {{ Route::is(['mobile.shipment', 'shipment.*']) ? 'font-bold' : 'font-medium' }} mt-1 whitespace-nowrap">
                الطرود
            </span>
        </a>

        <a href="{{ route('mobile.shipmentpackage.index') }}"
            class="flex flex-col items-center justify-center px-1 py-2 xs:px-2 transition-all active:scale-90 rounded-2xl {{ Route::is(['mobile.shipmentpackage.*', 'shipmentpackage.*']) ? 'bg-primary-container text-primary' : 'text-slate-400 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[20px] xs:text-[24px]"
                style="font-variation-settings: 'FILL' {{ Route::is(['mobile.shipmentpackage.*', 'shipmentpackage.*']) ? 1 : 0 }};">local_shipping</span>
            <span class="font-headline text-[9px] xs:text-[10px] {{ Route::is(['mobile.shipmentpackage.*', 'shipmentpackage.*']) ? 'font-bold' : 'font-medium' }} mt-1 whitespace-nowrap">
                الشحنات
            </span>
        </a>

        @hasservice('Passengers')
        <a href="{{ route('mobile.passenger') }}"
            class="flex flex-col items-center justify-center px-1 py-2 xs:px-2 transition-all active:scale-90 rounded-2xl {{ Route::is(['passengers.*', 'mobile.passenger', 'trips.*']) ? 'bg-primary-container text-primary' : 'text-slate-400 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[20px] xs:text-[24px]"
                style="font-variation-settings: 'FILL' {{ Route::is(['passengers.*', 'mobile.passenger', 'trips.*']) ? 1 : 0 }};">
                airline_seat_recline_normal
            </span>
            <span class="font-headline text-[9px] xs:text-[10px] {{ Route::is(['passengers.*', 'mobile.passenger', 'trips.*']) ? 'font-bold' : 'font-medium' }} mt-1 whitespace-nowrap">
                الركاب
            </span>
        </a>
        @endhasservice

        <!-- More Toggle Button -->
        <button @click="showMoreMenu = !showMoreMenu"
            class="flex flex-col items-center justify-center px-1 py-2 xs:px-2 transition-all active:scale-90 rounded-2xl text-slate-400 hover:text-primary outline-none"
            :class="showMoreMenu ? 'text-primary' : ''">
            <span class="material-symbols-outlined text-[20px] xs:text-[24px]"
                :style="showMoreMenu ? 'font-variation-settings: \'FILL\' 1;' : 'font-variation-settings: \'FILL\' 0;'">
                more_horiz
            </span>
            <span class="font-headline text-[9px] xs:text-[10px] font-medium mt-1 whitespace-nowrap"
                :class="showMoreMenu ? 'font-bold' : ''">
                المزيد
            </span>
        </button>
    </div>

</nav>
