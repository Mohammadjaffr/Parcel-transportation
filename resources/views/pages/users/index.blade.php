@extends('layouts.app')
@section('title', 'إدارة المستخدمين')
@section('Breadcrumb', 'إدارة المستخدمين')
@section('addButton')
    @include('pages.users.create-user-modal')

@endsection

@section('content')

    <div x-data="userFilter()" class="space-y-6 font-outfit" dir="rtl">
        @include('pages.users.edit-user-modal')


        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 md:gap-6">
            <div @click="statusFilter = 'all'; filterNow()"
                :class="statusFilter === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-gray-100'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 bg-gray-50 rounded-xl dark:bg-gray-800 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي
                        المستخدمين</span>
                    <h4 class="text-xl font-black dark:text-white" x-text="users.length"></h4>
                </div>
            </div>

            <div @click="statusFilter = 'active'; filterNow()"
                :class="statusFilter === 'active' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V22" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">نشط
                        حالياً</span>
                    <h4 class="text-xl font-black dark:text-white" x-text="users.filter(u => u.is_banned == 0).length"></h4>
                </div>
            </div>

            <div @click="statusFilter = 'inactive'; filterNow()"
                :class="statusFilter === 'inactive' ? 'border-error-500 ring-2 ring-error-500/20' : 'border-gray-100'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-error-50 dark:bg-error-500/10 text-error-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">حسابات
                        محظورة</span>
                    <h4 class="text-xl font-black dark:text-white" x-text="users.filter(u => u.is_banned == 1).length"></h4>
                </div>
            </div>
        </div>



        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="w-full bg-white dark:bg-white/[0.03] p-4 rounded-2xl">
                <div class="relative rounded-2xl border ring-2 group border-brand-500 ring-brand-500/20">
                    <input type="text" x-model="search" @input.debounce.300ms="filterNow"
                        placeholder="ابحث بالاسم أو رقم الهاتف..."
                        class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white">
                    <div
                        class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 group-focus-within:text-brand-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                <template x-for="user in filteredUsers" :key="user.id">
                    <div
                        class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800">
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full bg-brand-500 dark:text-brand-300"
                                    x-text="user.name ? user.name.charAt(0) : '?'"></div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white"
                                        x-text="user.name"></span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="user.phone"></span>
                                </div>
                            </div>
                            <button @click="openEditModal(user.id)" :disabled="isFetching == user.id"
                                class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-brand-500 hover:border-brand-200 dark:bg-gray-900 dark:border-gray-800">
                                <template x-if="isFetching == user.id">
                                    <svg class="w-5 h-5 animate-spin text-brand-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </template>
                                <template x-if="isFetching != user.id">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </template>
                            </button>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-800">
                            <span
                                :class="user.type === 'admin' ? 'bg-brand-50 text-brand-500 dark:bg-brand-500/10' :
                                    'bg-gray-50 text-gray-500 dark:bg-gray-700'"
                                class="px-2.5 py-1 rounded-full text-[10px] font-medium"
                                x-text="user.type === 'admin' ? 'مدير نظام' : 'مستخدم'"></span>
                            <span
                                :class="user.is_banned == 0 ? 'bg-success-100 text-success-700' : 'bg-error-100 text-error-700'"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium dark:bg-opacity-10">
                                <span :class="user.is_banned == 0 ? 'bg-success-500' : 'bg-error-500'"
                                    class="w-1.5 h-1.5 rounded-full"></span>
                                <span x-text="user.is_banned == 0 ? 'نشط' : 'محظور'"></span>
                            </span>
                        </div>
                    </div>
                </template>
                <div x-show="filteredUsers.length === 0"
                    class="py-12 text-center rounded-xl border border-gray-100 border-dashed bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800">
                    <div class="flex flex-col justify-center items-center">
                        <div class="p-3 mb-3 bg-white rounded-full shadow-sm dark:bg-gray-800">
                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">لا توجد نتائج تطابق بحثك حالياً..</p>
                    </div>
                </div>
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto px-4 pb-4 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="px-6 py-4">المستخدم</th>
                            <th class="px-6 py-4">الهاتف</th>
                            <th class="px-6 py-4 text-center">نوع الحساب</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        <template x-for="user in filteredUsers" :key="user.id">
                            <tr
                                class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md hover:border-gray-100 dark:hover:border-gray-800">

                                <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-gray-800/50">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 dark:text-white"
                                            x-text="user.name"></span>
                                        <span class="text-[10px] font-bold text-gray-400"
                                            x-text="user.whatsapp_number ?? '-'"></span>
                                    </div>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <span class="text-sm font-bold text-gray-500 dark:text-gray-400"
                                        x-text="user.phone"></span>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span
                                        :class="user.type === 'admin' ? 'bg-brand-50 text-brand-500 dark:bg-brand-500/10' :
                                            'bg-gray-50 text-gray-500 dark:bg-gray-700'"
                                        class="px-3 py-1 rounded-lg text-[10px] font-black uppercase"
                                        x-text="user.type === 'admin' ? 'مدير نظام' : 'مستخدم'"></span>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span
                                        :class="user.is_banned == 0 ? 'bg-success-50 text-success-500' :
                                            'bg-error-50 text-error-500'"
                                        class="px-3 py-1 rounded-lg text-[10px] font-black uppercase">
                                        <span x-text="user.is_banned == 0 ? 'نشط' : 'محظور'"></span>
                                    </span>
                                </td>

                                <td
                                    class="px-6 py-5 text-center border-l last:rounded-l-2xl border-y dark:border-gray-800/50">
                                    <div class="flex gap-2 justify-center items-center">
                                        <button @click="openEditModal(user.id)" :disabled="isFetching == user.id"
                                            class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-brand-500 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-brand-400">
                                            <template x-if="isFetching == user.id">
                                                <svg class="w-5 h-5 animate-spin text-brand-500"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </template>
                                            <template x-if="isFetching != user.id">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </template>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredUsers.length === 0">
                            <td colspan="5" class="py-20 text-center">
                                <div class="italic text-gray-400">لا توجد نتائج تطابق بحثك حالياً..</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($users->hasPages())
                <div class="px-6 pb-6">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function userFilter() {
            return {
                search: "",
                statusFilter: "all",
                // جلب البيانات من Laravel وتحويلها لـ JSON
                users: @json($users->items()),
                filteredUsers: [],
                editModalOpen: false,
                isUpdating: false,
                isFetching: null,
                countries: [{
                    name: 'Yemen',
                    code: 'YE',
                    dial_code: '967'
                }],
                editUser: {
                    id: null,
                    name: '',
                    phone: '',
                    whatsapp_number: '',
                    type: '',
                    phone_local: '',
                    phone_country: null,
                    whatsapp_local: '',
                    whatsapp_country: null,
                    password: ''
                },

                init() {
                    this.filteredUsers = this.users;
                    this.editUser.phone_country = this.countries[0];
                    this.editUser.whatsapp_country = this.countries[0];
                },

                parsePhoneNumber(fullNumber) {
                    if (!fullNumber) return {
                        country: this.countries[0],
                        local: ''
                    };

                    // Try to match dial code
                    for (let country of this.countries) {
                        if (fullNumber.startsWith(country.dial_code)) {
                            return {
                                country: country,
                                local: fullNumber.substring(country.dial_code.length)
                            };
                        }
                    }
                    return {
                        country: this.countries[0],
                        local: fullNumber
                    };
                },

                async openEditModal(userId) {
                    this.isFetching = userId;
                    try {
                        const response = await fetch(`/users/${userId}/edit`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await response.json();

                        // Parse numbers
                        const parsedPhone = this.parsePhoneNumber(data.phone);
                        const parsedWhatsapp = this.parsePhoneNumber(data.whatsapp_number);

                        this.editUser = {
                            ...data,
                            phone_local: parsedPhone.local,
                            phone_country: parsedPhone.country,
                            whatsapp_local: parsedWhatsapp.local,
                            whatsapp_country: parsedWhatsapp.country,
                            password: '' // Clear password field
                        };

                        this.editModalOpen = true;
                    } catch (error) {
                        console.error("Error fetching user data:", error);
                        alert("حدث خطأ أثناء جلب بيانات المستخدم");
                    } finally {
                        this.isFetching = null;
                    }
                },

                filterNow() {
                    this.filteredUsers = this.users.filter(user => {
                        const matchesSearch = this.search === "" ||
                            user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                            (user.phone && user.phone.includes(this.search));

                        const matchesStatus = this.statusFilter === "all" ||
                            (this.statusFilter === "active" && user.is_banned == 0) ||
                            (this.statusFilter === "inactive" && user.is_banned == 1);

                        return matchesSearch && matchesStatus;
                    });
                }
            }
        }
    </script>
@endsection
