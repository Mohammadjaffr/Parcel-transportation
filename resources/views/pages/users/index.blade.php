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

    <div x-data="userFilter()" @open-create-modal.window="isModalOpen = true" class="pb-24 space-y-6 min-h-screen font-body" dir="rtl">
        
        {{-- Modals --}}
        @include('pages.users.create-user-modal')
        @include('pages.users.edit-user-modal')

        {{-- ================= الإحصائيات (بطاقات الفلترة التفاعلية) ================= --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 md:gap-6">
            
            {{-- إجمالي المستخدمين --}}
            <div @click="statusFilter = 'all'"
                :class="statusFilter === 'all' ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 hover:border-primary/50 dark:border-boxdark-2'"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md dark:border-boxdark-2">
                <div class="flex justify-center items-center w-12 h-12 rounded-xl shadow-inner bg-primary-container dark:bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[24px]">group</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase dark:text-bodydark">
                        إجمالي المستخدمين
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white" 
                        x-text="search === '' && statusFilter === 'all' ? '{{ $users->count() }} من {{ $users->total() ?? 0 }}' : filteredUsers.length"></h4>
                </div>
            </div>

            {{-- حسابات نشطة --}}
            <div @click="statusFilter = 'active'"
                :class="statusFilter === 'active' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-gray-100 hover:border-emerald-300 dark:border-boxdark-2'"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md dark:border-boxdark-2 border-r-emerald-500 dark:border-r-emerald-500">
                <div class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[24px]">verified_user</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-emerald-500 uppercase">
                        حسابات نشطة
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white" x-text="users.filter(u => u.is_banned == 0).length"></h4>
                </div>
            </div>

            {{-- حسابات محظورة --}}
            <div @click="statusFilter = 'banned'"
                :class="statusFilter === 'banned' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-gray-100 hover:border-rose-300 dark:border-boxdark-2'"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md dark:border-boxdark-2 border-r-rose-500 dark:border-r-rose-500">
                <div class="flex justify-center items-center w-12 h-12 text-rose-500 bg-rose-50 rounded-xl dark:bg-rose-500/10">
                    <span class="material-symbols-outlined text-[24px]">block</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-rose-500 uppercase">
                        حسابات محظورة
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white" x-text="users.filter(u => u.is_banned == 1).length"></h4>
                </div>
            </div>
        </div>

        {{-- ================= حاوية الجدول والبحث ================= --}}
        <div class="bg-white dark:bg-boxdark rounded-[2.5rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-hidden transition-colors">
            
            {{-- شريط البحث --}}
            <div class="p-5 w-full bg-white dark:bg-boxdark rounded-t-[2.5rem] border-b border-gray-100 dark:border-boxdark-2">
                <div class="relative rounded-2xl border border-gray-200 transition-all dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">
                    <input type="text" x-model.debounce.300ms="search"
                        placeholder="ابحث بالاسم أو رقم الهاتف..."
                        class="pr-12 pl-4 w-full h-14 text-sm font-medium placeholder-gray-400 bg-transparent rounded-2xl border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">
                    <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                        <span class="material-symbols-outlined text-[24px]">search</span>
                    </div>
                </div>
            </div>

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                <template x-for="user in filteredUsers" :key="user.id">
                    <div class="flex flex-col gap-4 p-5 rounded-2xl border border-gray-100 transition-all bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30 hover:shadow-sm group">
                        
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-12 h-12 text-lg font-black text-white rounded-xl shadow-inner bg-primary"
                                    x-text="user.name ? user.name.charAt(0) : '?'"></div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-black text-on-surface dark:text-white font-headline" x-text="user.name"></span>
                                    <div class="flex gap-1.5 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark" dir="ltr">
                                        <span class="material-symbols-outlined text-[14px]">call</span>
                                        <span x-text="user.phone || 'غير مدخل'"></span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- القائمة المنسدلة (Kebab Menu) للإجراءات --}}
                            <div x-data="{ menuOpen: false }" class="relative">
                                <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false" class="p-2 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                <div x-show="menuOpen" x-transition x-cloak class="overflow-hidden absolute left-0 top-full z-50 mt-2 w-48 bg-white rounded-xl border border-gray-100 shadow-lg dark:bg-boxdark-2 dark:border-boxdark">
                                    <button @click="openEditModal(user); menuOpen = false" class="flex gap-3 items-center px-4 py-3 w-full text-xs font-bold text-gray-700 border-b border-gray-50 transition-colors dark:text-gray-200 hover:bg-surface dark:hover:bg-boxdark dark:border-boxdark">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                        تعديل البيانات
                                    </button>
                                    <a :href="`{{ url('users') }}/${user.id}`" class="flex gap-3 items-center px-4 py-3 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-surface dark:hover:bg-boxdark">
                                        <span class="material-symbols-outlined text-[18px]">analytics</span>
                                        إنتاجية الموظف
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-boxdark">
                            <span :class="user.type === 'admin' ? 'bg-primary-container text-primary dark:bg-primary/10' : 'bg-white border border-gray-100 shadow-sm text-gray-500 dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-300'"
                                class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase"
                                x-text="user.type === 'admin' ? 'مدير نظام' : 'مستخدم'"></span>
                            
                            <span :class="user.is_banned == 0 ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black">
                                <span :class="user.is_banned == 0 ? 'bg-emerald-500' : 'bg-rose-500'" class="w-1.5 h-1.5 rounded-full"></span>
                                <span x-text="user.is_banned == 0 ? 'نشط' : 'محظور'"></span>
                            </span>
                        </div>
                    </div>
                </template>

                <div x-show="filteredUsers.length === 0" x-cloak
                    class="py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <div class="flex flex-col justify-center items-center">
                        <div class="p-4 mb-4 bg-white rounded-full shadow-sm dark:bg-boxdark">
                            <span class="text-[32px] text-gray-300 material-symbols-outlined dark:text-gray-600">search_off</span>
                        </div>
                        <h4 class="text-sm font-black text-on-surface dark:text-white font-headline">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">لم نعثر على مستخدمين يطابقون خيارات البحث.</p>
                    </div>
                </div>
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto px-5 pb-5 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] dark:text-bodydark">
                            <th class="px-6 py-4">المستخدم</th>
                            <th class="px-6 py-4">الهاتف</th>
                            <th class="px-6 py-4 text-center">نوع الحساب</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        <template x-for="user in filteredUsers" :key="user.id">
                            <tr class="rounded-2xl border border-transparent shadow-sm transition-all bg-surface dark:bg-boxdark-2 hover:shadow-md hover:border-gray-200 dark:hover:border-boxdark group">
                                
                                {{-- بيانات المستخدم --}}
                                <td class="px-6 py-5 border-r border-gray-50 border-y dark:border-boxdark-2 first:rounded-r-2xl">
                                    <div class="flex gap-4 items-center">
                                        <div class="flex justify-center items-center w-12 h-12 text-lg font-black text-white rounded-xl shadow-inner bg-primary" x-text="user.name ? user.name.charAt(0) : '?'"></div>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-black text-on-surface dark:text-white font-headline" x-text="user.name"></span>
                                            <div class="flex items-center gap-1.5 text-[11px] font-bold text-gray-500 dark:text-bodydark" dir="ltr">
                                                <i class="fa-brands fa-whatsapp text-emerald-500 text-[14px]"></i>
                                                <span x-text="user.whatsapp_number ?? '-'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- رقم الهاتف --}}
                                <td class="px-6 py-5 border-gray-50 border-y dark:border-boxdark-2">
                                    <div class="flex gap-2 items-center text-gray-600 dark:text-gray-300" dir="ltr">
                                        <span class="material-symbols-outlined text-[18px] text-gray-400">call</span>
                                        <span class="text-sm font-bold" x-text="user.phone || '---'"></span>
                                    </div>
                                </td>

                                {{-- الدور / الصلاحية --}}
                                <td class="px-6 py-5 text-center border-gray-50 border-y dark:border-boxdark-2">
                                    <span :class="user.type === 'admin' ? 'bg-primary-container text-primary dark:bg-primary/10' : 'bg-white border border-gray-100 shadow-sm text-gray-500 dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-300'"
                                        class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase"
                                        x-text="user.type === 'admin' ? 'مدير نظام' : 'مستخدم'"></span>
                                </td>

                                {{-- الحالة --}}
                                <td class="px-6 py-5 text-center border-gray-50 border-y dark:border-boxdark-2">
                                    <span :class="user.is_banned == 0 ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black">
                                        <span :class="user.is_banned == 0 ? 'bg-emerald-500' : 'bg-rose-500'" class="w-1.5 h-1.5 rounded-full"></span>
                                        <span x-text="user.is_banned == 0 ? 'نشط' : 'محظور'"></span>
                                    </span>
                                </td>

                                {{-- الإجراءات --}}
                                <td class="px-6 py-5 text-center border-l border-gray-50 border-y dark:border-boxdark-2 last:rounded-l-2xl">
                                    <div class="flex gap-2 justify-center items-center">
                                        
                                        <button @click="openEditModal(user)" title="تعديل بيانات المستخدم"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-primary-container hover:text-primary hover:border-primary/20 dark:hover:bg-primary/10 dark:hover:text-primary active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>

                                        {{-- 💡 إصلاح خطأ الرابط ($user غير معرّف) باستخدام قالب الحرف العكسي (Backticks) في جافاسكريبت --}}
                                        <a :href="`{{ url('users') }}/${user.id}`" title="إنتاجية الموظف"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 dark:hover:bg-blue-500/10 dark:hover:text-blue-400 active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">analytics</span>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- حالة الفراغ --}}
                        <tr x-show="filteredUsers.length === 0" x-cloak>
                            <td colspan="5" class="py-24 text-center">
                                <div class="flex flex-col justify-center items-center">
                                    <span class="mb-4 text-[40px] text-gray-300 material-symbols-outlined dark:text-gray-600">search_off</span>
                                    <div class="text-sm font-black text-on-surface dark:text-white font-headline">لا توجد نتائج</div>
                                    <div class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">لم نعثر على مستخدمين يطابقون خيارات البحث.</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($users->hasPages())
                <div class="px-6 py-5 border-t border-gray-100 bg-surface/50 dark:bg-boxdark-2/50 dark:border-boxdark-2 rounded-b-[2.5rem]">
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