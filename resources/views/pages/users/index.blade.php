@extends('layouts.app')
@section('title', 'إدارة المستخدمين')
@section('addButton')
    @include('pages.users.create-user-modal')
    <x-modals.success-modal />
    <x-modals.error-modal />
@endsection

@section('content')

    <div x-data="userFilter()" class="space-y-6 font-outfit" dir="rtl">
        @include('pages.users.edit-user-modal')


        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-6">
            <div @click="statusFilter = 'all'; filterNow()"
                :class="statusFilter === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-gray-100'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">إجمالي
                        المستخدمين</span>
                    <h4 class="text-xl font-black dark:text-white" x-text="users.length"></h4>
                </div>
            </div>

            <div @click="statusFilter = 'active'; filterNow()"
                :class="statusFilter === 'active' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V22" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">نشط
                        حالياً</span>
                    <h4 class="text-xl font-black dark:text-white" x-text="users.filter(u => u.is_banned == 0).length"></h4>
                </div>
            </div>

            <div @click="statusFilter = 'inactive'; filterNow()"
                :class="statusFilter === 'inactive' ? 'border-error-500 ring-2 ring-error-500/20' : 'border-gray-100'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-error-50 dark:bg-error-500/10 text-error-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">حسابات
                        محظورة</span>
                    <h4 class="text-xl font-black dark:text-white" x-text="users.filter(u => u.is_banned == 1).length"></h4>
                </div>
            </div>
        </div>

        <div
            class="w-full bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div class="relative group ">
                <input type="text" x-model="search" @input.debounce.300ms="filterNow"
                    placeholder="ابحث بالاسم أو رقم الهاتف..."
                    class="w-full h-12 pr-11 pl-4 rounded-xl border-none bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all text-sm font-medium dark:text-white placeholder-gray-400">
                <div
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 group-focus-within:text-brand-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="overflow-x-auto px-4 pb-4">
                <table class="w-full border-separate border-spacing-y-3 text-right">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="py-4 px-6">المستخدم</th>
                            <th class="py-4 px-6">الهاتف</th>
                            <th class="py-4 px-6 text-center">نوع الحساب</th>
                            <th class="py-4 px-6 text-center">الحالة</th>
                            <th class="py-4 px-6 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        <template x-for="user in filteredUsers" :key="user.id">
                            <tr
                                class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-md transition-all border border-transparent hover:border-gray-100 dark:hover:border-gray-800">

                                <td class="py-5 px-6 first:rounded-r-2xl border-y border-r dark:border-gray-800/50">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 dark:text-white"
                                            x-text="user.name"></span>
                                        <span class="text-[10px] font-bold text-gray-400"
                                            x-text="user.whatsapp_number ?? '-'"></span>
                                    </div>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50">
                                    <span class="text-sm font-bold text-gray-500 dark:text-gray-400"
                                        x-text="user.phone"></span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    <span
                                        :class="user.type === 'admin' ? 'bg-brand-50 text-brand-500 dark:bg-brand-500/10' : 'bg-gray-50 text-gray-500 dark:bg-gray-700'"
                                        class="px-3 py-1 rounded-lg text-[10px] font-black uppercase"
                                        x-text="user.type === 'admin' ? 'مدير نظام' : 'مستخدم'"></span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    <span
                                        :class="user.is_banned == 0 ? 'bg-success-50 text-success-500' : 'bg-error-50 text-error-500'"
                                        class="px-3 py-1 rounded-lg text-[10px] font-black uppercase">
                                        <span x-text="user.is_banned == 0 ? 'نشط' : 'محظور'"></span>
                                    </span>
                                </td>

                                <td
                                    class="py-5 px-6 last:rounded-l-2xl border-y border-l dark:border-gray-800/50 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- <a :href="'/users/' + user.id"
                                            class="p-2 inline-flex text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a> --}}
                                        <button @click="openEditModal(user.id)" :disabled="isFetching == user.id"
                                            class="inline-flex p-2 text-gray-400 transition-all rounded-lg hover:bg-white hover:text-brand-500 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-brand-400">
                                            <template x-if="isFetching == user.id">
                                                <svg class="animate-spin h-5 w-5 text-brand-500"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </template>
                                            <template x-if="isFetching != user.id">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </template>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredUsers.length === 0">
                            <td colspan="5" class="py-20 text-center">
                                <div class="text-gray-400 italic">لا توجد نتائج تطابق بحثك حالياً..</div>
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
                countries: [
                    { name: 'Yemen', code: 'YE', dial_code: '967' }
                ],
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
                    if (!fullNumber) return { country: this.countries[0], local: '' };

                    // Try to match dial code
                    for (let country of this.countries) {
                        if (fullNumber.startsWith(country.dial_code)) {
                            return {
                                country: country,
                                local: fullNumber.substring(country.dial_code.length)
                            };
                        }
                    }
                    return { country: this.countries[0], local: fullNumber };
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