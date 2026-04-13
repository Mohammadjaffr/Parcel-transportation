<nav
    class="fixed bottom-0 left-0 w-full glass-nav shadow-[0_-8px_24px_rgba(36,56,156,0.06)] flex flex-row justify-around items-center px-4 pb-4 pt-4 z-50 rounded-t-3xl bg-white">

    <a href="{{ route('dashboard.index') }}"
        class="flex flex-col items-center justify-center px-4 py-2 transition-all active:scale-90 rounded-2xl {{ Route::is('dashboard.index') ? 'bg-primary-container text-primary' : 'text-slate-400 hover:text-primary' }}">

        <span class="material-symbols-outlined"
            style="font-variation-settings: 'FILL' {{ Route::is('dashboard.index') ? 1 : 0 }};">
            home
        </span>

        <span class="font-headline text-[10px] {{ Route::is('dashboard.index') ? 'font-bold' : 'font-medium' }} mt-1">
            الرئيسية
        </span>
    </a>

    <a href="{{ route('people.index') }}"
        class="flex flex-col items-center justify-center px-4 py-2 transition-all active:scale-90 rounded-2xl {{ Route::is(['people.*', 'drivers.*', 'users.*', 'customers.*']) ? 'bg-primary-container text-primary' : 'text-slate-400 hover:text-primary' }}">

        <span class="material-symbols-outlined"
            style="font-variation-settings: 'FILL' {{ Route::is(['people.*', 'drivers.*', 'users.*', 'customers.*']) ? 1 : 0 }};">
            group
        </span>

        <span
            class="font-headline text-[10px] {{ Route::is(['people.*', 'drivers.*', 'users.*', 'customers.*']) ? 'font-bold' : 'font-medium' }} mt-1">
            الأفراد
        </span>
    </a>

    <a href="{{ route('mobile.office') }}"
        class="flex flex-col items-center justify-center px-4 py-2 transition-all active:scale-90 rounded-2xl {{ Route::is(['mobile.office', 'offices.unverified.index', 'offices.create', 'app.index']) ? 'bg-primary-container text-primary' : 'text-slate-400 hover:text-primary' }}">

        <span class="material-symbols-outlined"
            style="font-variation-settings: 'FILL' {{ Route::is(['mobile.office', 'offices.unverified.index', 'offices.create', 'app.index']) ? 1 : 0 }};">
            apartment
        </span>

        <span
            class="font-headline text-[10px] {{ Route::is(['mobile.office', 'offices.unverified.index', 'offices.create', 'app.index']) ? 'font-bold' : 'font-medium' }} mt-1">
            المكاتب
        </span>
    </a>

    <a href="{{ route('mobile.shipment') }}"
        class="flex flex-col items-center justify-center px-4 py-2 transition-all active:scale-90 rounded-2xl {{ Route::is(['mobile.shipment', 'shipment.*']) ? 'bg-primary-container text-primary' : 'text-slate-400 hover:text-primary' }}">
        <span class="material-symbols-outlined"
            style="font-variation-settings: 'FILL' {{ Route::is(['mobile.shipment', 'shipment.*']) ? 1 : 0 }};">inventory_2</span>
        <span
            class="font-headline text-[10px] {{ Route::is(['mobile.shipment', 'shipment.*']) ? 'font-bold' : 'font-medium' }} mt-1">الطرود</span>
    </a>

    <a href="{{ route('mobile.shipmentpackage.index') }}"
        class="flex flex-col items-center justify-center px-4 py-2 transition-all active:scale-90 rounded-2xl {{ Route::is(['mobile.shipmentpackage.*', 'shipmentpackage.*']) ? 'bg-primary-container text-primary' : 'text-slate-400 hover:text-primary' }}">
        <span class="material-symbols-outlined"
            style="font-variation-settings: 'FILL' {{ Route::is(['mobile.shipmentpackage.*', 'shipmentpackage.*']) ? 1 : 0 }};">local_shipping</span>
        <span
            class="font-headline text-[10px] {{ Route::is(['mobile.shipmentpackage.*', 'shipmentpackage.*']) ? 'font-bold' : 'font-medium' }} mt-1">الشحنات</span>
    </a>
</nav>