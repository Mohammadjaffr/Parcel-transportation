@extends('mobile.layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        searchQuery: '',
        isSubmitting: false,
        errors: {},
    
        editUserData: { id: '', name: '', branch_id: '', phone: '', whatsapp_number: '', type: 'user', is_banned: false, password: '', url: '' },
        createUserData: { name: '', phone: '', whatsapp_number: '', password: '' },
        deleteUserData: { id: '', name: '', url: '' },
    
        // أضفنا branch_id في آخر قائمة المتغيرات المستلمة
        openEditModal(id, name, phone, whatsapp, type, is_banned, branch_id) {
            this.errors = {}; // تصفير الأخطاء السابقة
            this.editUserData = {
                id: id,
                name: name,
                phone: phone,
                // تحويل الرقم إلى نص لضمان تطابقه مع الـ <option value='...'>
                branch_id: branch_id ? String(branch_id) : '',
                whatsapp_number: whatsapp || '',
                type: type,
                is_banned: is_banned,
                password: '',
                url: '{{ route('users.index') }}/' + id
            };
            this.showEditModal = true;
        },
    
        openCreateModal() {
            this.errors = {};
            this.createUserData = { name: '', phone: '', branch_id: '', whatsapp_number: '', password: '' };
            this.showCreateModal = true;
        },
    
        openDeleteModal(id, name) {
            this.deleteUserData = {
                id: id,
                name: name,
                url: '{{ route('users.index') }}/' + id
            };
            this.showDeleteModal = true;
        },
    
        closeModals() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.showDeleteModal = false;
            this.errors = {};
        },
    
        async submitForm(url, method, data) {
            this.isSubmitting = true;
            this.errors = {};
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = result.errors;
                    } else {
                        alert(result.message || 'حدث خطأ غير متوقع.');
                    }
                } else {
                    this.closeModals();
                    window.location.reload();
                }
            } catch (error) {
                alert('حدث خطأ في الاتصال بالخادم.');
            } finally {
                this.isSubmitting = false;
            }
        },
    
        async toggleStatus(id) {
            try {
                const response = await fetch(`/users/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (!result.success) {
                    alert('فشل تغيير الحالة');
                }
            } catch (error) {
                alert('حدث خطأ في الاتصال.');
            }
        }
    }" class="flex relative flex-col gap-6 pb-24 min-h-screen">

        <!-- Header Section -->
        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-2xl font-black tracking-tight font-headline text-slate-800">إدارة المستخدمين</h1>
                <p class="mt-0.5 text-xs font-semibold text-slate-400">
                    إجمالي <span class="font-bold text-primary">{{ $users->total() }}</span> مستخدم
                </p>
            </div>
            <button type="button" @click="openCreateModal()"
                class="flex justify-center items-center w-12 h-12 text-white rounded-2xl shadow-xl transition-all bg-primary shadow-primary/20 active:scale-95">
                <span class="text-2xl material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">person_add</span>
            </button>
        </div>

        <!-- Search Bar Section -->
        <div class="px-2">
            <div class="relative group">
                <span
                    class="absolute right-4 top-1/2 transition-colors -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary">search</span>
                <input type="text" x-model="searchQuery" placeholder="ابحث باسم المستخدم أو رقم الهاتف..."
                    class="w-full h-14 pr-12 pl-12 rounded-[1.25rem] border-none bg-white shadow-sm ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 transition-all font-headline text-sm text-slate-700 outline-none">

                <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                    class="flex absolute left-4 top-1/2 justify-center items-center w-8 h-8 rounded-xl transition-transform -translate-y-1/2 bg-slate-50 text-slate-400 active:scale-95">
                    <span class="text-lg material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        <!-- Users List Grid (تعديل التصميم ليتطابق مع السائقين) -->
        <!-- Users List Grid -->
        <div class="px-2 space-y-4">
            @forelse ($users as $user)
                <!-- جعلنا كل كارد يدير حالته البرمجية بشكل مستقل (Optimistic UI) -->
                <div x-data="{
                    isBanned: {{ $user->is_banned ? 'true' : 'false' }},
                    isLoading: false,
                    async toggleUserStatus() {
                        if (this.isLoading) return;
                
                        this.isLoading = true;
                        let previousState = this.isBanned;
                        this.isBanned = !this.isBanned; // تغيير الشكل فوراً للمستخدم
                
                        try {
                            const response = await fetch(`/users/{{ $user->id }}/toggle-status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });
                            const result = await response.json();
                
                            if (!result.success) {
                                // إذا رفض السيرفر، نعيد الشكل لحالته السابقة
                                this.isBanned = previousState;
                                alert('فشل تغيير الحالة من الخادم.');
                            }
                        } catch (error) {
                            this.isBanned = previousState;
                            alert('حدث خطأ في الاتصال بالخادم.');
                        } finally {
                            this.isLoading = false;
                        }
                    }
                }"
                    x-show="searchQuery === '' || '{{ $user->name }}'.includes(searchQuery) || '{{ $user->phone }}'.includes(searchQuery)"
                    :class="isBanned ? 'opacity-60 bg-slate-50 border-slate-200 grayscale-[0.5]' : 'bg-white border-slate-50'"
                    class="rounded-[1.75rem] p-5 shadow-[0_8px_30px_rgb(0,0,0,0.02)] border relative overflow-hidden transition-all duration-300 active:scale-[0.98]">

                    <!-- علامة مائية تظهر عند الحظر -->
                    <div x-show="isBanned" x-cloak
                        class="flex absolute inset-0 z-0 justify-center items-center pointer-events-none">
                        <span class="text-4xl font-black -rotate-12 select-none text-rose-500/10">محظور</span>
                    </div>

                    <!-- Top Info -->
                    <div class="flex relative z-10 gap-4 items-center mb-4">
                        <div class="flex justify-center items-center w-14 h-14 text-lg font-black rounded-2xl border shadow-inner transition-colors duration-300 shrink-0"
                            :class="isBanned ? 'bg-slate-200 text-slate-400 border-slate-300' :
                                'bg-secondary/10 text-secondary border-secondary/5'">
                            @php
                                $words = explode(' ', $user->name);
                                echo mb_substr($words[0] ?? '', 0, 1, 'utf-8') .
                                    (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
                            @endphp
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex gap-2 items-center mb-1.5">
                                <h3 class="text-base font-bold leading-none truncate transition-all duration-300 font-headline"
                                    :class="isBanned ? 'text-slate-400 line-through decoration-rose-500/50 decoration-2' :
                                        'text-slate-800'">
                                    {{ $user->name }}
                                </h3>
                                @if ($user->type == 'admin')
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-700 rounded-md">مدير</span>
                                @else
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-700 rounded-md">موظف</span>
                                @endif
                            </div>
                            <div class="flex gap-2 items-center text-slate-500">
                                <span class="material-symbols-outlined text-[16px]"
                                    :class="isBanned ? 'text-slate-300' : 'text-slate-400'">phone_iphone</span>
                                <span class="font-mono text-xs font-bold tracking-wider"
                                    :class="isBanned ? 'text-slate-400' : ''">{{ $user->phone }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="mb-4 h-px transition-colors duration-300"
                        :class="isBanned ? 'bg-slate-200' : 'bg-gradient-to-r from-transparent to-transparent via-slate-200'">
                    </div>

                    <!-- Actions -->
                    <div class="flex relative z-10 justify-between items-center">
                        <div class="flex gap-4 items-center">

                            <!-- Toggle Switch المحسّن -->
                            <div @click="toggleUserStatus()" class="flex gap-2 items-center cursor-pointer"
                                :class="isLoading ? 'pointer-events-none opacity-50' : ''">
                                <div class="relative">
                                    <!-- شريط الخلفية -->
                                    <div class="block w-11 h-6 rounded-full shadow-inner transition-colors duration-300"
                                        :class="isBanned ? 'bg-rose-500' : 'bg-emerald-500'"></div>
                                    <!-- الدائرة المتحركة -->
                                    <div class="absolute top-1 right-1 w-4 h-4 bg-white rounded-full shadow-sm transition-transform duration-300 dot"
                                        :class="isBanned ? 'translate-x-0' : '-translate-x-5'"></div>
                                </div>
                                <!-- حالة النص -->
                                <span class="text-xs font-bold transition-colors duration-300 font-headline"
                                    :class="isBanned ? 'text-rose-500' : 'text-emerald-600'"
                                    x-text="isBanned ? 'محظور' : 'نشط'"></span>

                                <!-- مؤشر التحميل أثناء الاتصال بالسيرفر -->
                                <span x-show="isLoading"
                                    class="material-symbols-outlined animate-spin text-[14px] text-slate-400">progress_activity</span>
                            </div>

                        </div>

                        <div class="flex gap-2" :class="isBanned ? 'opacity-50 pointer-events-none' : ''">
                            <a href="https://wa.me/{{ $user->whatsapp_number ?? $user->phone }}"
                                class="flex justify-center items-center w-10 h-10 text-emerald-600 bg-emerald-50 rounded-xl transition-transform active:scale-95">
                                <span class="text-xl material-symbols-outlined"
                                    style="font-variation-settings: 'FILL' 1;">chat</span>
                            </a>
                            <button type="button"
                                @click="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->phone }}', '{{ $user->whatsapp_number }}', '{{ $user->type }}', {{ $user->is_banned ? 'true' : 'false' }}, {{ $user->branch_id ?? 'null' }})"
                                class="flex justify-center items-center w-10 h-10 rounded-xl transition-all bg-slate-100 text-slate-500 hover:bg-secondary/10 hover:text-secondary active:scale-90">
                                <span class="text-xl material-symbols-outlined">edit_square</span>
                            </button>
                            <button type="button"
                                @click="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                class="flex justify-center items-center w-10 h-10 text-red-500 bg-red-50 rounded-xl transition-all hover:bg-red-100 active:scale-90">
                                <span class="text-xl material-symbols-outlined">delete_outline</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 mx-2 shadow-sm">
                    <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-200">
                        <span class="text-6xl material-symbols-outlined">badge</span>
                    </div>
                    <p class="text-lg font-bold font-headline text-slate-400">لم نعثر على أي مستخدمين</p>
                </div>
            @endforelse

            <div x-show="searchQuery !== '' && !Array.from(document.querySelectorAll('.space-y-4 > div[x-show]')).some(el => el.style.display !== 'none')"
                style="display: none;"
                class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 mx-2 shadow-sm">
                <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-200">
                    <span class="text-6xl material-symbols-outlined">search_off</span>
                </div>
                <p class="text-lg font-bold font-headline text-slate-400">لا يوجد نتائج للبحث</p>
            </div>
        </div>

        <div class="px-2 mt-4" x-show="searchQuery === ''">
            {{ $users->links('vendor.pagination.mobile') }}
        </div>

        <!-- ======================== Bottom Sheet Modals ======================== -->

        <!-- Create User Bottom Sheet -->
        <div x-show="showCreateModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()"></div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto">
                <div @click="closeModals()"
                    class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div class="flex justify-between items-center px-2 mb-8">
                    <h3 class="text-xl font-black font-headline text-slate-800">إضافة مستخدم جديد</h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 rounded-xl bg-slate-50 text-slate-400">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form @submit.prevent="submitForm('{{ route('users.store') }}', 'POST', createUserData)"
                    class="px-2 space-y-5">
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">الاسم الكامل <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400"
                                :class="errors.name ? 'text-red-400' : ''">person</span>
                            <input type="text" x-model="createUserData.name" placeholder="اسم الموظف"
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline"
                                :class="errors.name ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                        </div>
                        <template x-if="errors.name">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">تعيين في فرع <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400"
                                :class="errors.branch_id ? 'text-red-400' : ''">store</span>
                            <select x-model="createUserData.branch_id"
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all appearance-none outline-none bg-slate-50 focus:bg-white font-headline"
                                :class="errors.branch_id ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                                <option value="" disabled selected>اختر الفرع...</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }} - {{ $branch->city }}
                                    </option>
                                @endforeach
                            </select>
                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none material-symbols-outlined text-slate-400">expand_more</span>
                        </div>
                        <template x-if="errors.branch_id">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.branch_id[0]"></p>
                        </template>
                    </div>
                    <div x-data="{
                        open: false,
                        search: '',
                        countries: @js(array_values(config('countries'))),
                        selectedCountry: null,
                        localPhoneNumber: '',
                        init() {
                            this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                            
                            this.$watch('localPhoneNumber', value => {
    // أضفنا .this هنا
    this.createUserData.phone = (this.selectedCountry?.dial_code.replace('+', '') || '') + value;
});
                            this.$watch('selectedCountry', value => {
    // أضفنا .this هنا
    this.createUserData.phone = (value?.dial_code.replace('+', '') || '') + this.localPhoneNumber;
});

                            this.$watch('createUserData.phone', value => {
    if (!value) {
        this.localPhoneNumber = '';
    }
});
                        },
                        get filteredCountries() {
                            if (this.search === '') return this.countries;
                            return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                        }
                    }">
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الهاتف <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="flex relative rounded-2xl ring-1 transition-all bg-slate-50 focus-within:bg-white"
                                :class="errors.phone ? 'ring-red-300 focus-within:ring-red-400' : 'ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20'">
                                
                                {{-- Country Selector Button --}}
                                <button type="button" @click="open = !open"
                                    class="flex gap-2 items-center px-4 bg-transparent rounded-r-2xl border-l border-slate-200 shrink-0 hover:bg-slate-100 transition-colors">
                                    <template x-if="selectedCountry">
                                        <svg class="w-5 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                                    </template>
                                    <span class="text-xs font-bold text-slate-600" dir="ltr" x-text="selectedCountry?.dial_code"></span>
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                </button>

                                {{-- Phone Input --}}
                                <input type="tel" x-model="localPhoneNumber" placeholder="7xx xxx xxx"
                                    class="flex-1 px-4 w-full h-14 text-sm text-left bg-transparent border-none outline-none font-headline rounded-l-2xl" dir="ltr">
                                
                            </div>

                            {{-- Dropdown panel --}}
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute top-[calc(100%+4px)] right-0 z-20 w-full max-h-60 bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden"
                                style="display: none;">
                                <div class="p-2 border-b border-slate-50">
                                    <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                        class="px-4 py-2 w-full text-sm outline-none bg-slate-50 rounded-xl font-headline">
                                </div>
                                <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                    <template x-for="country in filteredCountries" :key="country.code">
                                        <div @click="selectedCountry = country; open = false; search = ''"
                                            class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                            <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>
                                            <span class="flex-grow text-sm font-medium text-slate-700 font-headline truncate" x-text="country.name"></span>
                                            <span class="font-mono text-xs font-bold text-slate-500 shrink-0" dir="ltr" x-text="country.dial_code"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <template x-if="errors.phone">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.phone[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">كلمة المرور <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400"
                                :class="errors.password ? 'text-red-400' : ''">lock</span>
                            <input type="password" x-model="createUserData.password" placeholder="6 أحرف على الأقل"
                                class="pr-12 pl-4 w-full h-14 text-sm text-left rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline dir-ltr"
                                :class="errors.password ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                        </div>
                        <template x-if="errors.password">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.password[0]"></p>
                        </template>
                    </div>

                    <button type="submit" :disabled="isSubmitting"
                        class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary font-headline shadow-primary/30 active:scale-95 disabled:opacity-70">
                        <span x-show="!isSubmitting" class="material-symbols-outlined">save</span>
                        <span x-show="isSubmitting"
                            class="animate-spin material-symbols-outlined">progress_activity</span>
                        <span x-text="isSubmitting ? 'جاري الحفظ...' : 'إضافة المستخدم'"></span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Edit User Bottom Sheet -->
        <div x-show="showEditModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()">
            </div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto">
                <div @click="closeModals()"
                    class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div class="flex justify-between items-center px-2 mb-8">
                    <h3 class="text-xl font-black font-headline text-slate-800">تعديل بيانات المستخدم</h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 rounded-xl bg-slate-50 text-slate-400">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form @submit.prevent="submitForm(editUserData.url, 'POST', { ...editUserData, _method: 'PUT' })"
                    class="px-2 space-y-5">

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">الاسم الكامل <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400"
                                :class="errors.name ? 'text-red-400' : ''">person</span>
                            <input type="text" x-model="editUserData.name"
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline"
                                :class="errors.name ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                        </div>
                        <template x-if="errors.name">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">الفرع <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400"
                                :class="errors.branch_id ? 'text-red-400' : ''">store</span>
                            <select x-model="editUserData.branch_id"
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all appearance-none outline-none bg-slate-50 focus:bg-white font-headline"
                                :class="errors.branch_id ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                                <option value="" disabled>اختر الفرع...</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none material-symbols-outlined text-slate-400">expand_more</span>
                        </div>
                        <template x-if="errors.branch_id">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.branch_id[0]"></p>
                        </template>
                    </div>

                    <div x-data="{
                        open: false,
                        search: '',
                        countries: @js(array_values(config('countries'))),
                        selectedCountry: null,
                        localPhoneNumber: '',
                        init() {
                            this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                            const countryCodes = this.countries.map(c => c.dial_code.replace('+', '')).sort((a,b) => b.length - a.length);

                            this.$watch('localPhoneNumber', value => {
                                editUserData.phone = (this.selectedCountry?.dial_code.replace('+', '') || '') + value;
                            });
                            this.$watch('selectedCountry', value => {
                                editUserData.phone = (value?.dial_code.replace('+', '') || '') + this.localPhoneNumber;
                            });

                            // When model property changes from external (User opens modal)
                            this.$watch('editUserData.phone', newValue => {
                                if (!newValue) {
                                    this.localPhoneNumber = '';
                                    return;
                                }
                                const currentConstructed = (this.selectedCountry?.dial_code.replace('+', '') || '') + this.localPhoneNumber;
                                if (newValue !== currentConstructed) {
                                    let matched = false;
                                    for(let code of countryCodes) {
                                        if (newValue.startsWith(code)) {
                                            this.selectedCountry = this.countries.find(c => c.dial_code.replace('+','') === code);
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
                        },
                        get filteredCountries() {
                            if (this.search === '') return this.countries;
                            return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                        }
                    }">
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الهاتف <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="flex relative rounded-2xl ring-1 transition-all bg-slate-50 focus-within:bg-white"
                                :class="errors.phone ? 'ring-red-300 focus-within:ring-red-400' : 'ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20'">
                                
                                {{-- Country Selector Button --}}
                                <button type="button" @click="open = !open"
                                    class="flex gap-2 items-center px-4 bg-transparent rounded-r-2xl border-l border-slate-200 shrink-0 hover:bg-slate-100 transition-colors">
                                    <template x-if="selectedCountry">
                                        <svg class="w-5 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                                    </template>
                                    <span class="text-xs font-bold text-slate-600" dir="ltr" x-text="selectedCountry?.dial_code"></span>
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                </button>

                                {{-- Phone Input --}}
                                <input type="tel" x-model="localPhoneNumber" placeholder="7xx xxx xxx"
                                    class="flex-1 px-4 w-full h-14 text-sm text-left bg-transparent border-none outline-none font-headline rounded-l-2xl" dir="ltr">
                                
                            </div>

                            {{-- Dropdown panel --}}
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute top-[calc(100%+4px)] right-0 z-20 w-full max-h-60 bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden"
                                style="display: none;">
                                <div class="p-2 border-b border-slate-50">
                                    <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                        class="px-4 py-2 w-full text-sm outline-none bg-slate-50 rounded-xl font-headline">
                                </div>
                                <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                    <template x-for="country in filteredCountries" :key="country.code">
                                        <div @click="selectedCountry = country; open = false; search = ''"
                                            class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                            <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>
                                            <span class="flex-grow text-sm font-medium text-slate-700 font-headline truncate" x-text="country.name"></span>
                                            <span class="font-mono text-xs font-bold text-slate-500 shrink-0" dir="ltr" x-text="country.dial_code"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <template x-if="errors.phone">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.phone[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">كلمة المرور الجديدة
                            <span class="text-xs font-normal text-slate-400">(اختياري)</span></label>
                        <div class="relative">
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400"
                                :class="errors.password ? 'text-red-400' : ''">lock_reset</span>
                            <input type="password" x-model="editUserData.password"
                                placeholder="أدخل كلمة المرور الجديدة إذا أردت تغييرها"
                                class="pr-12 pl-4 w-full h-14 text-sm text-left rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline dir-ltr"
                                :class="errors.password ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                        </div>
                        <template x-if="errors.password">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.password[0]"></p>
                        </template>
                    </div>

                    <button type="submit" :disabled="isSubmitting"
                        class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary font-headline shadow-primary/30 active:scale-95 disabled:opacity-70">
                        <span x-show="!isSubmitting" class="material-symbols-outlined">update</span>
                        <span x-show="isSubmitting"
                            class="animate-spin material-symbols-outlined">progress_activity</span>
                        <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ التعديلات'"></span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Bottom Sheet -->
        <div x-show="showDeleteModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()">
            </div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto text-center">
                <div @click="closeModals()"
                    class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div
                    class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-red-50 text-red-500 rounded-[1.5rem]">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>

                <h3 class="mb-3 text-2xl font-black font-headline text-slate-800">تأكيد الحذف</h3>

                <p class="mb-8 text-sm font-semibold leading-relaxed text-slate-500">
                    هل أنت متأكد من أنك تريد حذف المستخدم <br>
                    <span class="text-base font-bold text-slate-800 font-headline"
                        x-text="deleteUserData.name"></span>؟<br>
                    <span class="text-red-500/80">لا يمكن التراجع عن هذا الإجراء.</span>
                </p>

                <form @submit.prevent="submitForm(deleteUserData.url, 'POST', { _method: 'DELETE' })"
                    class="flex gap-3 px-2">
                    <button type="button" @click="closeModals()"
                        class="flex-1 py-4 text-sm font-bold rounded-2xl transition-all text-slate-600 bg-slate-100 hover:bg-slate-200 active:scale-95 font-headline">
                        تراجع
                    </button>

                    <button type="submit" :disabled="isSubmitting"
                        class="flex-1 py-4 text-sm font-bold text-white bg-red-500 rounded-2xl shadow-lg transition-all hover:bg-red-600 shadow-red-500/30 active:scale-95 font-headline">
                        <span x-show="!isSubmitting">نعم، احذف</span>
                        <span x-show="isSubmitting"
                            class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
