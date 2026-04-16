@extends('layouts.app')

@section('title', 'إدارة السائقين')

@section('content')


    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        searchQuery: '',
    
        editDriverData: { id: '', name: '', phone: '', url: '' },
        deleteDriverData: { id: '', name: '', url: '' },
    
        openEditModal(id, name, phone) {
            this.editDriverData = {
                id: id,
                name: name,
                phone: phone,
                url: '{{ route('drivers.index') }}/' + id
            };
            this.showEditModal = true;
        },
    
        openDeleteModal(id, name) {
            this.deleteDriverData = {
                id: id,
                name: name,
                url: '{{ route('drivers.index') }}/' + id
            };
            this.showDeleteModal = true;
        },
    
        closeModals() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.showDeleteModal = false;
        }
    }"
        class="flex relative flex-col gap-6 p-4 pb-24 mx-auto max-w-7xl min-h-screen md:p-6 bg-surface dark:bg-boxdark-2 font-body"
        dir="rtl">

        {{-- ================= Header Section ================= --}}
        <div class="flex flex-col gap-4 justify-between items-start md:flex-row md:items-center">
            <div>
                <h1 class="text-2xl font-black tracking-tight md:text-3xl font-headline text-on-surface dark:text-white">
                    السائقين</h1>
                <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-bodydark">
                    إجمالي <span class="font-black text-primary">{{ $drivers->total() }}</span> سائق مسجل
                </p>
            </div>

            <button type="button" @click="showCreateModal = true"
                class="flex gap-2 justify-center items-center px-5 w-full h-12 text-sm font-bold text-white rounded-2xl shadow-lg transition-all bg-primary hover:bg-primary-hover shadow-primary/30 active:scale-95 md:w-auto">
                <span class="text-[22px] material-symbols-outlined"
                    style="font-variation-settings: 'FILL' 1;">person_add</span>
                <span class="hidden md:inline">إضافة سائق جديد</span>
            </button>
        </div>

        {{-- ================= Search & Data Section ================= --}}
        <div
            class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-hidden transition-colors">

            {{-- Search Bar --}}
            <div class="p-5 bg-white border-b border-gray-100 dark:border-boxdark-2 dark:bg-boxdark">
                <div
                    class="relative w-full rounded-2xl border border-gray-200 transition-all md:w-96 dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">
                    <input type="text" x-model="searchQuery" placeholder="ابحث باسم السائق أو رقم الهاتف..."
                        class="pr-12 pl-12 w-full h-12 text-sm font-medium placeholder-gray-400 bg-transparent rounded-2xl border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">

                    <span
                        class="absolute right-4 top-1/2 text-gray-400 transition-colors -translate-y-1/2 material-symbols-outlined group-focus-within:text-primary">search</span>

                    <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                        class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-gray-100 rounded-xl transition-all -translate-y-1/2 dark:bg-boxdark hover:text-error active:scale-95">
                        <span class="text-[18px] material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                @forelse ($drivers as $driver)
                    <div x-show="searchQuery === '' || '{{ $driver->name }}'.includes(searchQuery) || '{{ $driver->phone }}'.includes(searchQuery)"
                        x-transition
                        class="overflow-hidden relative p-5 rounded-2xl border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30">

                        <div class="flex justify-between items-start mb-4">
                            <div class="flex gap-3 items-center">
                                <div
                                    class="flex justify-center items-center w-12 h-12 text-lg font-black rounded-xl bg-primary-container dark:bg-primary/10 text-primary shrink-0">
                                    @php
                                        $words = explode(' ', $driver->name);
                                        echo mb_substr($words[0] ?? '', 0, 1, 'utf-8') .
                                            (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
                                    @endphp
                                </div>
                                <div class="flex flex-col">
                                    <h3 class="text-sm font-black text-on-surface dark:text-white font-headline">
                                        {{ $driver->name }}</h3>
                                    <div class="flex gap-1.5 items-center mt-1 text-gray-500 dark:text-bodydark">
                                        <span class="material-symbols-outlined text-[14px]">phone_iphone</span>
                                        <span
                                            class="font-mono text-xs font-bold tracking-wider dir-ltr">{{ $driver->phone }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 items-center pt-4 border-t border-gray-100 dark:border-boxdark">
                            <a href="tel:{{ $driver->phone }}"
                                class="flex flex-1 gap-2 justify-center items-center px-3 py-2 text-xs font-bold rounded-xl transition-all bg-primary-container dark:bg-primary/10 text-primary hover:bg-primary/20 active:scale-95">
                                <span class="text-[16px] material-symbols-outlined"
                                    style="font-variation-settings: 'FILL' 1;">call</span>
                                اتصال
                            </a>
                            <a href="https://wa.me/{{ ltrim($driver->phone, '+') }}" target="_blank"
                                class="flex flex-1 gap-2 justify-center items-center px-3 py-2 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl transition-all dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 active:scale-95">

                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>

                                <span>واتساب</span>
                            </a>

                            <button type="button"
                                @click="openEditModal({{ $driver->id }}, {{ json_encode($driver->name) }}, {{ json_encode($driver->phone) }})"
                                class="flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 transition-all dark:bg-boxdark dark:border-boxdark-2 hover:text-primary hover:border-primary/30 active:scale-95 shrink-0">
                                <span class="text-[18px] material-symbols-outlined">edit</span>
                            </button>
                            <button type="button"
                                @click="openDeleteModal({{ $driver->id }}, {{ json_encode($driver->name) }})"
                                class="flex justify-center items-center w-10 h-10 text-rose-500 bg-rose-50 rounded-xl transition-all dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 active:scale-95 shrink-0">
                                <span class="text-[18px] material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col justify-center items-center py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <span
                            class="text-[48px] material-symbols-outlined text-gray-300 dark:text-gray-600 mb-4">no_accounts</span>
                        <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لم نعثر على أي سائقين مسجلين</p>
                    </div>
                @endforelse

                <div x-show="searchQuery !== '' && !Array.from(document.querySelectorAll('.space-y-4 > div[x-show]')).some(el => el.style.display !== 'none')"
                    style="display: none;"
                    class="flex flex-col justify-center items-center py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <span
                        class="text-[48px] material-symbols-outlined text-gray-300 dark:text-gray-600 mb-4">search_off</span>
                    <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا يوجد نتائج تطابق بحثك</p>
                </div>
            </div>

            {{-- ===== Desktop View (Data Table) ===== --}}
            <div class="hidden overflow-x-auto px-6 pb-6 mt-4 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr
                            class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] dark:text-bodydark bg-surface dark:bg-boxdark-2 border-b border-gray-100 dark:border-boxdark">
                            <th class="px-6 py-5">السائق</th>
                            <th class="px-6 py-5">رقم الهاتف</th>
                            <th class="px-6 py-5 text-center">تواصل سريع</th>
                            <th class="px-6 py-5 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @foreach ($drivers as $driver)
                            <tr x-show="searchQuery === '' || '{{ $driver->name }}'.includes(searchQuery) || '{{ $driver->phone }}'.includes(searchQuery)"
                                x-transition
                                class="rounded-2xl border border-transparent shadow-sm transition-all bg-surface dark:bg-boxdark-2 hover:shadow-md hover:border-gray-200 dark:hover:border-boxdark group">

                                <td
                                    class="px-6 py-4 border-r border-gray-50 border-y dark:border-boxdark-2 first:rounded-r-2xl">
                                    <div class="flex gap-4 items-center">
                                        <div
                                            class="flex justify-center items-center w-12 h-12 text-lg font-black rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                                            @php
                                                $words = explode(' ', $driver->name);
                                                echo mb_substr($words[0] ?? '', 0, 1, 'utf-8') .
                                                    (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
                                            @endphp
                                        </div>
                                        <span
                                            class="text-sm font-black text-on-surface dark:text-white font-headline">{{ $driver->name }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-gray-50 border-y dark:border-boxdark-2">
                                    <div class="flex gap-2 items-center text-gray-600 dark:text-gray-300">
                                        <span
                                            class="material-symbols-outlined text-[18px] text-gray-400 dark:text-gray-500">phone_iphone</span>
                                        <span
                                            class="font-mono text-sm font-bold tracking-wider dir-ltr">{{ $driver->phone }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center border-gray-50 border-y dark:border-boxdark-2">
                                    <div class="flex gap-3 justify-center items-center">
                                        <a href="tel:{{ $driver->phone }}" title="اتصال"
                                            class="flex justify-center items-center w-10 h-10 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors dark:bg-boxdark dark:border-boxdark-2 text-primary hover:bg-primary-container dark:hover:bg-primary/10 active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]"
                                                style="font-variation-settings: 'FILL' 1;">call</span>
                                        </a>
                                        <a href="https://wa.me/{{ ltrim($driver->phone, '+') }}" target="_blank"
                                            title="واتساب"
                                            class="flex justify-center items-center w-10 h-10 text-emerald-500 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors dark:bg-boxdark dark:border-boxdark-2 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 active:scale-95">
                                            <i class="fa-brands fa-whatsapp text-[20px]"></i>
                                        </a>
                                    </div>
                                </td>

                                <td
                                    class="px-6 py-4 text-center border-l border-gray-50 border-y dark:border-boxdark-2 last:rounded-l-2xl">
                                    <div class="flex gap-2 justify-center items-center">
                                        <button
                                            @click="openEditModal({{ $driver->id }}, {{ json_encode($driver->name) }}, {{ json_encode($driver->phone) }})"
                                            title="تعديل"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-primary-container hover:text-primary hover:border-primary/20 dark:hover:bg-primary/10 dark:hover:text-primary active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>

                                        <button
                                            @click="openDeleteModal({{ $driver->id }}, {{ json_encode($driver->name) }})"
                                            title="حذف"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-rose-50 hover:text-rose-500 hover:border-rose-200 dark:hover:bg-rose-500/10 dark:hover:text-rose-400 active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($drivers->hasPages())
                <div
                    class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-surface/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    {{ $drivers->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        {{-- ======================== Desktop/Centered Modals ======================== --}}

        {{-- 1. Create Driver Modal --}}
        <div x-show="showCreateModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div
                class="relative w-full max-w-lg bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 md:p-8 pointer-events-auto flex flex-col max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex justify-between items-center pb-4 mb-8 border-b border-gray-50 dark:border-boxdark-2">
                    <h3 class="flex gap-2 items-center text-xl font-black font-headline text-on-surface dark:text-white">
                        <div
                            class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[20px]">person_add</span>
                        </div>
                        إضافة سائق جديد
                    </h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-colors bg-surface dark:bg-boxdark-2 hover:text-error active:scale-95">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('drivers.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">الاسم الكامل <span
                                class="text-error">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">person</span>
                            <input type="text" name="name" required placeholder="مثلاً: أحمد السعيدي"
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                        </div>
                    </div>

                    <div x-data="{
                        open: false,
                        search: '',
                        countries: @js(array_values(config('countries', []))),
                        selectedCountry: null,
                        localPhoneNumber: '',
                        init() {
                            this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                        },
                        get filteredCountries() {
                            if (this.search === '') return this.countries;
                            return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                        }
                    }">
                        <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم الهاتف <span
                                class="text-error">*</span></label>

                        <div class="relative">
                            <input type="hidden" name="phone"
                                :value="(selectedCountry?.dial_code.replace('+', '') || '') + localPhoneNumber">

                            <div
                                class="flex relative items-center rounded-xl ring-1 ring-gray-200 transition-all group bg-surface dark:bg-boxdark-2 focus-within:bg-white dark:focus-within:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40">

                                {{-- Country Selector Button --}}
                                <button type="button" @click="open = !open" @click.away="open = false"
                                    class="flex gap-2 items-center px-4 h-14 bg-white rounded-r-xl border-l border-gray-200 transition-colors dark:bg-boxdark dark:border-boxdark-2 shrink-0 hover:bg-gray-50 dark:hover:bg-boxdark-2">
                                    <template x-if="selectedCountry?.svg">
                                        <svg class="w-6 h-auto rounded-[2px] shadow-sm" viewBox="0 0 36 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg"
                                            x-html="selectedCountry.svg"></svg>
                                    </template>
                                    <span class="text-sm font-bold text-gray-600 dark:text-gray-300 dir-ltr"
                                        x-text="selectedCountry?.dial_code"></span>
                                    <span class="material-symbols-outlined text-[18px] text-gray-400">expand_more</span>
                                </button>

                                {{-- Phone Input --}}
                                <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX" required
                                    inputmode="numeric"
                                    class="flex-1 px-4 w-full h-14 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 text-on-surface dark:text-white dir-ltr">

                                {{-- Dropdown panel --}}
                                <div x-show="open" x-transition x-cloak
                                    class="absolute top-[calc(100%+8px)] right-0 z-50 w-full bg-white dark:bg-boxdark rounded-xl border border-gray-100 dark:border-boxdark-2 shadow-xl overflow-hidden">
                                    <div class="p-2 border-b border-gray-50 dark:border-boxdark-2">
                                        <input type="text" x-model="search" placeholder="ابحث عن الدولة أو الرمز..."
                                            class="px-4 py-2 w-full text-sm rounded-lg transition-colors outline-none bg-surface dark:bg-boxdark-2 dark:text-white focus:bg-white dark:focus:bg-boxdark">
                                    </div>
                                    <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <div @click="selectedCountry = country; open = false; search = ''"
                                                class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary-container dark:hover:bg-boxdark-2">
                                                <svg class="w-5 h-auto rounded-[2px] shadow-sm shrink-0"
                                                    viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                                    x-html="country.svg"></svg>
                                                <span
                                                    class="flex-grow text-sm font-medium text-gray-700 truncate dark:text-gray-200"
                                                    x-text="country.name"></span>
                                                <span class="font-mono text-xs font-bold text-gray-500 shrink-0 dir-ltr"
                                                    x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="flex gap-2 justify-center items-center mt-8 w-full h-14 font-black text-white rounded-xl shadow-lg transition-all bg-primary shadow-primary/30 active:scale-95">
                        <span class="material-symbols-outlined">save</span>
                        حفظ السائق
                    </button>
                </form>
            </div>
        </div>

        {{-- 2. Edit Driver Modal --}}
        <div x-show="showEditModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div
                class="relative w-full max-w-lg bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 md:p-8 pointer-events-auto flex flex-col max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex justify-between items-center pb-4 mb-8 border-b border-gray-50 dark:border-boxdark-2">
                    <h3 class="flex gap-2 items-center text-xl font-black font-headline text-on-surface dark:text-white">
                        <div
                            class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl shadow-sm bg-surface dark:bg-boxdark-2 dark:text-bodydark">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </div>
                        تعديل بيانات السائق
                    </h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-colors bg-surface dark:bg-boxdark-2 hover:text-error active:scale-95">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form :action="editDriverData.url" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">الاسم الكامل <span
                                class="text-error">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">person</span>
                            <input type="text" name="name" x-model="editDriverData.name" required
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                        </div>
                    </div>

                    <div x-data="{
                        open: false,
                        search: '',
                        countries: @js(array_values(config('countries'))),
                        selectedCountry: null,
                        localPhoneNumber: '',
                        init() {
                            this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                            const countryCodes = this.countries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                    
                            this.$watch('editDriverData.phone', newValue => {
                                if (!newValue) {
                                    this.localPhoneNumber = '';
                                    return;
                                }
                                const currentConstructed = (this.selectedCountry?.dial_code.replace('+', '') || '') + this.localPhoneNumber;
                                if (newValue !== currentConstructed) {
                                    let matched = false;
                                    for (let code of countryCodes) {
                                        if (newValue.startsWith(code)) {
                                            this.selectedCountry = this.countries.find(c => c.dial_code.replace('+', '') === code);
                                            this.localPhoneNumber = newValue.substring(code.length);
                                            matched = true;
                                            break;
                                        }
                                    }
                                    if (!matched) {
                                        this.localPhoneNumber = newValue;
                                    }
                                }
                            });
                    
                            this.$watch('localPhoneNumber', value => {
                                editDriverData.phone = (this.selectedCountry?.dial_code.replace('+', '') || '') + value;
                            });
                            this.$watch('selectedCountry', value => {
                                editDriverData.phone = (value?.dial_code.replace('+', '') || '') + this.localPhoneNumber;
                            });
                        },
                        get filteredCountries() {
                            if (this.search === '') return this.countries;
                            return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                        }
                    }">
                        <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم الهاتف <span
                                class="text-error">*</span></label>

                        <div class="relative">
                            <input type="hidden" name="phone" :value="editDriverData.phone">

                            <div
                                class="flex relative items-center rounded-xl ring-1 ring-gray-200 transition-all group bg-surface dark:bg-boxdark-2 focus-within:bg-white dark:focus-within:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40">

                                {{-- Country Selector Button --}}
                                <button type="button" @click="open = !open" @click.away="open = false"
                                    class="flex gap-2 items-center px-4 h-14 bg-white rounded-r-xl border-l border-gray-200 transition-colors dark:bg-boxdark dark:border-boxdark-2 shrink-0 hover:bg-gray-50 dark:hover:bg-boxdark-2">
                                    <template x-if="selectedCountry?.svg">
                                        <svg class="w-6 h-auto rounded-[2px] shadow-sm" viewBox="0 0 36 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg"
                                            x-html="selectedCountry.svg"></svg>
                                    </template>
                                    <span class="text-sm font-bold text-gray-600 dark:text-gray-300 dir-ltr"
                                        x-text="selectedCountry?.dial_code"></span>
                                    <span class="material-symbols-outlined text-[18px] text-gray-400">expand_more</span>
                                </button>

                                {{-- Phone Input --}}
                                <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX" required
                                    inputmode="numeric"
                                    class="flex-1 px-4 w-full h-14 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 text-on-surface dark:text-white dir-ltr">

                                {{-- Dropdown panel --}}
                                <div x-show="open" x-transition x-cloak
                                    class="absolute top-[calc(100%+8px)] right-0 z-50 w-full bg-white dark:bg-boxdark rounded-xl border border-gray-100 dark:border-boxdark-2 shadow-xl overflow-hidden">
                                    <div class="p-2 border-b border-gray-50 dark:border-boxdark-2">
                                        <input type="text" x-model="search" placeholder="ابحث عن الدولة أو الرمز..."
                                            class="px-4 py-2 w-full text-sm rounded-lg transition-colors outline-none bg-surface dark:bg-boxdark-2 dark:text-white focus:bg-white dark:focus:bg-boxdark">
                                    </div>
                                    <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <div @click="selectedCountry = country; open = false; search = ''"
                                                class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary-container dark:hover:bg-boxdark-2">
                                                <svg class="w-5 h-auto rounded-[2px] shadow-sm shrink-0"
                                                    viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                                    x-html="country.svg"></svg>
                                                <span
                                                    class="flex-grow text-sm font-medium text-gray-700 truncate dark:text-gray-200"
                                                    x-text="country.name"></span>
                                                <span class="font-mono text-xs font-bold text-gray-500 shrink-0 dir-ltr"
                                                    x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="flex gap-2 justify-center items-center mt-8 w-full h-14 font-black text-white rounded-xl shadow-lg transition-all bg-primary shadow-primary/30 active:scale-95">
                        <span class="material-symbols-outlined">update</span>
                        حفظ التعديلات
                    </button>
                </form>
            </div>
        </div>

        {{-- 3. Delete Confirmation Modal --}}
        <div x-show="showDeleteModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div
                class="relative w-full max-w-md bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-8 text-center pointer-events-auto flex flex-col">

                {{-- أيقونة التحذير --}}
                <div
                    class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 dark:bg-rose-500/10 text-error rounded-[1.5rem] shadow-sm">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>

                <h3 class="mb-3 text-2xl font-black font-headline text-on-surface dark:text-white">تأكيد الحذف</h3>

                <p class="mb-8 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                    هل أنت متأكد من أنك تريد حذف السائق <br>
                    <span class="text-base font-bold text-on-surface dark:text-white font-headline"
                        x-text="deleteDriverData.name"></span>؟<br>
                    <span class="inline-block mt-2 text-error/80 dark:text-error">لا يمكن التراجع عن هذا الإجراء.</span>
                </p>

                <form :action="deleteDriverData.url" method="POST" class="flex gap-3 w-full">
                    @csrf
                    @method('DELETE')

                    <button type="button" @click="closeModals()"
                        class="flex-1 h-12 text-sm font-bold text-gray-600 rounded-xl transition-all bg-surface dark:bg-boxdark-2 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-boxdark font-headline active:scale-95">
                        تراجع
                    </button>

                    <button type="submit"
                        class="flex-1 h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-error hover:bg-error/90 shadow-error/30 active:scale-95 font-headline">
                        نعم، احذف
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
