@extends('layouts.app')
@section('title', 'إدارة المستخدمين')
@section('Breadcrumb', 'إدارة المستخدمين')

@section('addButton')
    <button @click="$dispatch('open-create-modal')"
        class="inline-flex gap-2 items-center px-4 py-2 text-sm font-semibold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95">
        <span class="material-symbols-outlined text-[20px]">add</span>
        إضافة مستخدم جديد
    </button>
@endsection

@section('content')

    <div x-data="userFilter()" @open-create-modal.window="isModalOpen = true" class="space-y-6 font-outfit" dir="rtl">
        {{-- Modals --}}
        @include('pages.users.create-user-modal')
        @include('pages.users.edit-user-modal')

        {{-- الإحصائيات (بطاقات الفلترة التفاعلية) --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 md:gap-6">
            
            {{-- إجمالي المستخدمين --}}
            <div @click="statusFilter = 'all'"
                :class="statusFilter === 'all' ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 hover:border-primary/50 dark:border-gray-800'"
                class="relative flex flex-col items-start justify-between p-5 transition-all bg-white border cursor-pointer rounded-2xl dark:bg-white/[0.03] hover:shadow-md shadow-theme-sm dark:border-gray-800">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl shadow-inner bg-primary/10 dark:bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[22px]">group</span>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        إجمالي المستخدمين
                    </span>
                    <h4 class="text-xl font-black dark:text-white" 
                        x-text="search === '' && statusFilter === 'all' ? '{{ $users->count() }} من {{ $users->total() ?? 0 }}' : filteredUsers.length"></h4>
                </div>
            </div>

            {{-- حسابات نشطة --}}
            <div @click="statusFilter = 'active'"
                :class="statusFilter === 'active' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100 hover:border-success-300 dark:border-gray-800'"
                class="relative flex flex-col items-start justify-between p-5 transition-all bg-white border cursor-pointer rounded-2xl dark:bg-white/[0.03] hover:shadow-md shadow-theme-sm dark:border-gray-800 border-r-4 border-r-success-500">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <span class="material-symbols-outlined text-[22px]">verified_user</span>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest uppercase text-success-500 text-theme-xs">
                        حسابات نشطة
                    </span>
                    <h4 class="text-xl font-black dark:text-white" x-text="users.filter(u => u.is_banned == 0).length"></h4>
                </div>
            </div>

            {{-- حسابات محظورة --}}
            <div @click="statusFilter = 'banned'"
                :class="statusFilter === 'banned' ? 'border-red-500 ring-2 ring-red-500/20' : 'border-gray-100 hover:border-red-300 dark:border-gray-800'"
                class="relative flex flex-col items-start justify-between p-5 transition-all bg-white border cursor-pointer rounded-2xl dark:bg-white/[0.03] hover:shadow-md shadow-theme-sm dark:border-gray-800 border-r-4 border-r-red-500">
                <div class="flex justify-center items-center w-10 h-10 text-red-500 bg-red-50 rounded-xl dark:bg-red-500/10">
                    <span class="material-symbols-outlined text-[22px]">block</span>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-red-500 uppercase text-theme-xs">
                        حسابات محظورة
                    </span>
                    <h4 class="text-xl font-black dark:text-white" x-text="users.filter(u => u.is_banned == 1).length"></h4>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-boxdark rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden transition-colors">
            
            {{-- شريط البحث --}}
            <div class="p-4 w-full bg-white rounded-2xl border-b border-gray-100 dark:bg-transparent dark:border-gray-800">
                <div class="relative rounded-2xl border border-gray-200 transition-all group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:border-gray-700">
                    <input type="text" x-model.debounce.300ms="search"
                        placeholder="ابحث بالاسم أو رقم الهاتف (في هذه الصفحة)..."
                        class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 rounded-2xl border-none transition-all outline-none bg-gray-50/50 dark:bg-gray-900 focus:ring-0 dark:text-white">
                    <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                        <span class="material-symbols-outlined text-[22px]">search</span>
                    </div>
                </div>
            </div>

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                <template x-for="user in filteredUsers" :key="user.id">
                    <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 transition-all bg-gray-50/50 dark:bg-gray-800/50 dark:border-gray-700 hover:border-primary/30 hover:shadow-sm">
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full shadow-sm bg-primary"
                                    x-text="user.name ? user.name.charAt(0) : '?'"></div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="user.name"></span>
                                    <div class="flex gap-1 items-center mt-0.5 text-xs text-gray-500 dark:text-gray-400" dir="ltr">
                                        <span class="material-symbols-outlined text-[14px]">call</span>
                                        <span x-text="user.phone || 'غير مدخل'"></span>
                                    </div>
                                </div>
                            </div>
                            <button @click="openEditModal(user)"
                                class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-primary hover:border-primary/30 dark:bg-gray-900 dark:border-gray-800">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-800">
                            <span :class="user.type === 'admin' ? 'bg-primary/10 text-primary dark:bg-primary/10' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300'"
                                class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase"
                                x-text="user.type === 'admin' ? 'مدير نظام' : 'مستخدم'"></span>
                            
                            <span :class="user.is_banned == 0 ? 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' : 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400'"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold">
                                <span :class="user.is_banned == 0 ? 'bg-success-500' : 'bg-red-500'" class="w-1.5 h-1.5 rounded-full"></span>
                                <span x-text="user.is_banned == 0 ? 'نشط' : 'محظور'"></span>
                            </span>
                        </div>
                    </div>
                </template>

                <div x-show="filteredUsers.length === 0" x-cloak
                    class="py-12 text-center rounded-xl border border-gray-100 border-dashed bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-700">
                    <div class="flex flex-col justify-center items-center">
                        <div class="p-3 mb-3 bg-white rounded-full shadow-sm dark:bg-gray-900">
                            <span class="text-3xl text-gray-400 material-symbols-outlined">search_off</span>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">لا توجد نتائج تطابق بحثك أو تصفيتك في هذه الصفحة.</p>
                    </div>
                </div>
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto px-4 pb-4 mt-4 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] dark:text-bodydark2">
                            <th class="px-6 py-4">المستخدم</th>
                            <th class="px-6 py-4">الهاتف</th>
                            <th class="px-6 py-4 text-center">نوع الحساب</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        <template x-for="user in filteredUsers" :key="user.id">
                            <tr class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900/50 hover:shadow-md hover:border-primary/30 dark:hover:border-primary/30">
                                
                                <td class="px-6 py-5 border-r border-gray-100 border-y dark:border-gray-800 first:rounded-r-2xl">
                                    <div class="flex gap-3 items-center">
                                        <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full shadow-sm bg-primary" x-text="user.name ? user.name.charAt(0) : '?'"></div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-900 dark:text-white" x-text="user.name"></span>
                                            <div class="flex items-center gap-1 mt-0.5 text-[10px] font-bold text-gray-400" dir="ltr">
                                                <i class="fa-brands fa-whatsapp text-success-500"></i>
                                                <span x-text="user.whatsapp_number ?? '-'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5 border-gray-100 border-y dark:border-gray-800">
                                    <div class="flex gap-2 items-center text-gray-600 dark:text-gray-400" dir="ltr">
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">call</span>
                                        <span class="text-sm font-bold" x-text="user.phone || '---'"></span>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center border-gray-100 border-y dark:border-gray-800">
                                    <span :class="user.type === 'admin' ? 'bg-primary/10 text-primary dark:bg-primary/10' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300'"
                                        class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase"
                                        x-text="user.type === 'admin' ? 'مدير نظام' : 'مستخدم'"></span>
                                </td>

                                <td class="px-6 py-5 text-center border-gray-100 border-y dark:border-gray-800">
                                    <span :class="user.is_banned == 0 ? 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' : 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase">
                                        <span :class="user.is_banned == 0 ? 'bg-success-500' : 'bg-red-500'" class="w-1.5 h-1.5 rounded-full"></span>
                                        <span x-text="user.is_banned == 0 ? 'نشط' : 'محظور'"></span>
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center border-l border-gray-100 border-y dark:border-gray-800 last:rounded-l-2xl">
                                    <div class="flex gap-2 justify-center items-center">
                                        <button @click="openEditModal(user)" title="تعديل بيانات المستخدم"
                                            class="inline-flex p-2 text-gray-400 bg-gray-50 rounded-lg transition-all dark:bg-gray-800 hover:bg-primary/10 hover:text-primary dark:hover:bg-primary/20 disabled:opacity-50">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredUsers.length === 0" x-cloak>
                            <td colspan="5" class="py-20 text-center">
                                <div class="flex flex-col justify-center items-center">
                                    <span class="mb-2 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">search_off</span>
                                    <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">لا توجد نتائج تطابق بحثك أو تصفيتك في هذه الصفحة.</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($users->hasPages())
                <div class="px-6 pt-4 pb-6 mt-4 border-t border-gray-100 dark:border-gray-800">
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
                users: @json($users->items()),
                isModalOpen: false,
                editModalOpen: false,
                
                editUser: {
                    id: null,
                    name: '',
                    phone: '',
                    whatsapp_number: '',
                    type: '',
                    branch_id: '',
                    is_banned: 0,
                    url: ''
                },

                get filteredUsers() {
                    let result = this.users;

                    if (this.statusFilter !== 'all') {
                        const targetBanned = this.statusFilter === 'banned' ? 1 : 0;
                        result = result.filter(u => u.is_banned == targetBanned);
                    }

                    if (this.search.trim() !== "") {
                        const searchTerm = this.search.toLowerCase().trim();
                        result = result.filter(u => {
                            const n = (u.name || "").toLowerCase().includes(searchTerm);
                            const p = (u.phone || "").includes(searchTerm);
                            return n || p;
                        });
                    }

                    return result;
                },

                openEditModal(user) {
                    this.editUser = {
                        id: user.id,
                        name: user.name,
                        phone: user.phone || '',
                        whatsapp_number: user.whatsapp_number || '',
                        type: user.type,
                        branch_id: user.branch_id || '',
                        is_banned: user.is_banned,
                        url: '{{ url("users") }}/' + user.id
                    };
                    this.editModalOpen = true;
                }
            }
        }
    </script>
@endsection