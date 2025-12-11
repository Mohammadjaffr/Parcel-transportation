@extends('layouts.app')
@section('title', 'إدارة المستخدمين')
@section('Breadcrumb', 'إدارة المستخدمين')

@section('content')

    <div x-data="userFilter()" class="space-y-5 sm:space-y-6">

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5">

                <div class="flex flex-col sm:flex-row gap-4 items-end">

                    <!-- البحث -->
                    <div class="flex-1 min-w-[250px]">
                        <label class="mb-2 block text-xs text-right">البحث</label>
                        <input type="text" x-model="search" placeholder="أدخل نص البحث..."
                            class="h-10 w-full rounded-lg border px-4 text-sm text-right" />
                    </div>

                    <!-- الحالة -->
                    <div class="min-w-[180px]">
                        <label class="mb-2 block text-xs text-right">الحالة</label>
                        <select x-model="statusFilter" class="h-10 w-full rounded-lg border px-4 text-sm text-right">
                            <option value="all">الكل</option>
                            <option value="active">نشط</option>
                            <option value="inactive">محظور</option>
                        </select>
                    </div>

                    <!-- زر التطبيق -->
                    <button @click="filterNow"
                        class="bg-brand-500 hover:bg-brand-600 h-10 rounded-lg px-6 py-2 text-sm font-medium text-white">
                        تطبيق
                    </button>

                </div>

            </div>

            <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">

                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="max-w-full overflow-x-auto">

                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 sm:px-6 text-right">الاسم</th>
                                    <th class="px-5 py-3 sm:px-6 text-right">رقم الواتساب</th>
                                    <th class="px-5 py-3 sm:px-6 text-right">نوعه</th>
                                    <th class="px-5 py-3 sm:px-6 text-right">الحظر</th>
                                    <th class="px-5 py-3 sm:px-6 text-center">الإجراءات</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">

                                <template x-for="user in filteredUsers" :key="user.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">

                                        <td class="px-5 py-4 sm:px-6">
                                            <span class="block font-medium text-gray-800 dark:text-white"
                                                x-text="user.name"></span>
                                            <span class="block text-gray-500 text-xs"
                                                x-text="user.whatsapp_number ?? '-'"></span>
                                        </td>

                                        <td class="px-5 py-4 sm:px-6 text-gray-600 dark:text-gray-300" x-text="user.phone">
                                        </td>

                                        <td class="px-5 py-4 sm:px-6">
                                            <span
                                                class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium 
                                                     text-success-700 dark:bg-success-500/15 dark:text-success-400"
                                                x-text="user.type === 'admin' ? 'مدير نظام' : 'مستخدم'">
                                            </span>
                                        </td>

                                        <td class="px-5 py-4 sm:px-6">
                                            <span x-show="user.is_banned == 1"
                                                class="rounded-full bg-success-50 text-success-600 px-2 py-0.5 text-theme-xs font-medium">
                                                نشط
                                            </span>
                                            <span x-show="user.is_banned == 0"
                                                class="rounded-full bg-error-50 text-error-600 px-2 py-0.5 text-theme-xs font-medium">
                                                محظور
                                            </span>
                                        </td>

                                        <td class="px-5 py-4 sm:px-6 text-center">
                                            <a :href="'/users/' + user.id"
                                                class="text-brand-500 hover:text-brand-700 text-sm">
                                                عرض
                                            </a>
                                        </td>

                                    </tr>
                                </template>

                                <tr x-show="filteredUsers.length === 0">
                                    <td colspan="6" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                        لا يوجد نتائج مطابقة
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>

    </div>

<script>
function userFilter() {
    return {
        search: "",
        statusFilter: "all",

        users: @json($users->items()),   // ← هنا التصحيح المهم جداً
        filteredUsers: @json($users->items()),

        filterNow() {

            this.filteredUsers = this.users.filter(user => {

                let matchesSearch =
                    this.search === "" ||
                    user.name.includes(this.search) ||
                    (user.phone && user.phone.includes(this.search));

                let matchesStatus =
                    this.statusFilter === "all" ||
                    (this.statusFilter === "active" && user.is_banned == 1) ||
                    (this.statusFilter === "inactive" && user.is_banned == 0);

                return matchesSearch && matchesStatus;
            });
        }
    }
}
</script>


@endsection
