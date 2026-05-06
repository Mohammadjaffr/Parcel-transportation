@extends('layouts.app')
@section('title', 'إدارة المكاتب | المشرف العام')

@section('content')
<div x-data="tenantsManager()" class="space-y-6">

    {{-- Toast Notification --}}
    <div x-cloak x-show="toast.show" x-transition
         class="fixed top-6 left-1/2 z-[9999] -translate-x-1/2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-lg"
         :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'">
        <span x-text="toast.message"></span>
    </div>

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">إدارة المكاتب</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">إدارة جميع المكاتب والمستأجرين في المنصة</p>
        </div>
        <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-1 text-sm font-bold text-primary hover:underline">
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span> العودة للوحة التحكم
        </a>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[20px] text-gray-400">search</span>
            <input type="text" x-model="search" placeholder="ابحث باسم المكتب..."
                   class="w-full rounded-xl border-0 bg-gray-50 py-2.5 pr-10 pl-4 text-sm font-medium text-gray-900 ring-1 ring-gray-200 transition focus:bg-white focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600 dark:focus:ring-primary" />
        </div>
        <select x-model="statusFilter" class="rounded-xl border-0 bg-gray-50 py-2.5 px-4 text-sm font-bold text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600">
            <option value="all">جميع الحالات</option>
            <option value="active">نشط فقط</option>
            <option value="inactive">معطل فقط</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead><tr class="bg-gray-50/50 dark:bg-gray-900/30">
                    <th class="px-6 py-3 text-right text-xs font-black uppercase text-gray-500 dark:text-gray-400">المكتب</th>
                    <th class="px-6 py-3 text-center text-xs font-black uppercase text-gray-500 dark:text-gray-400">الفروع</th>
                    <th class="px-6 py-3 text-center text-xs font-black uppercase text-gray-500 dark:text-gray-400">المستخدمين</th>
                    <th class="px-6 py-3 text-center text-xs font-black uppercase text-gray-500 dark:text-gray-400">الباقة</th>
                    <th class="px-6 py-3 text-center text-xs font-black uppercase text-gray-500 dark:text-gray-400">الحالة</th>
                    <th class="px-6 py-3 text-center text-xs font-black uppercase text-gray-500 dark:text-gray-400">الإجراءات</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                @forelse($apps as $app)
                <tr x-show="filterRow('{{ addslashes($app->name ?? '') }}', {{ $app->is_active ? 'true' : 'false' }})"
                    class="transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-700/20">
                    <td class="px-6 py-4">
                        <a href="{{ route('superadmin.offices.show', $app->id) }}" class="flex items-center gap-3 group">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl text-white text-sm font-black" style="background:{{ $app->color ?? '#6366f1' }}">{{ mb_substr($app->name ?? 'م',0,1) }}</div>
                            <div>
                                <p class="text-sm font-bold text-gray-900 group-hover:text-primary dark:text-white">{{ $app->name ?? 'بدون اسم' }}</p>
                                <p class="text-xs text-gray-400">{{ $app->created_at->format('Y/m/d') }}</p>
                            </div>
                        </a>
                    </td>
                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-700 dark:text-gray-300">{{ $app->branches_count }}</td>
                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-700 dark:text-gray-300">{{ $app->users_count }}</td>
                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-700 dark:text-gray-300">{{ $app->currentSubscription?->package?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        <label class="relative inline-flex h-6 w-11 cursor-pointer items-center rounded-full transition-colors duration-200"
                               :class="appStates[{{ $app->id }}] ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'">
                            <input type="checkbox" class="peer sr-only"
                                   :checked="appStates[{{ $app->id }}]"
                                   @change="toggleStatus({{ $app->id }}, $event.target.checked)" />
                            <span class="absolute h-4 w-4 rounded-full bg-white shadow transition-all duration-200"
                                  :class="appStates[{{ $app->id }}] ? 'right-1' : 'left-1'"></span>
                        </label>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('superadmin.offices.show', $app->id) }}" class="inline-flex items-center gap-1 rounded-lg bg-primary/5 px-3 py-1.5 text-xs font-bold text-primary transition hover:bg-primary/10">
                            <span class="material-symbols-outlined text-[16px]">visibility</span> التفاصيل
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center"><span class="material-symbols-outlined text-[48px] text-gray-300">apartment</span><p class="mt-2 text-sm text-gray-400">لا توجد مكاتب</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-6 py-4 dark:border-gray-700">{{ $apps->links() }}</div>
    </div>
</div>
@endsection

@section('script')
<script>
function tenantsManager() {
    return {
        search: '',
        statusFilter: 'all',
        toast: { show: false, message: '', type: 'success' },
        appStates: {
            @foreach($apps as $app)
                {{ $app->id }}: {{ $app->is_active ? 'true' : 'false' }},
            @endforeach
        },
        showToast(msg, type = 'success') {
            this.toast = { show: true, message: msg, type };
            setTimeout(() => this.toast.show = false, 3000);
        },
        filterRow(name, isActive) {
            const matchSearch = !this.search || name.toLowerCase().includes(this.search.toLowerCase());
            const matchStatus = this.statusFilter === 'all'
                || (this.statusFilter === 'active' && isActive)
                || (this.statusFilter === 'inactive' && !isActive);
            return matchSearch && matchStatus;
        },
        async toggleStatus(appId, checked) {
            try {
                const res = await fetch(`/superadmin/offices/${appId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.status === 'success') {
                    this.appStates[appId] = data.is_active;
                    this.showToast(data.message);
                } else {
                    this.appStates[appId] = !checked;
                    this.showToast('حدث خطأ', 'error');
                }
            } catch (e) {
                console.error('toggleStatus error:', e);
                this.appStates[appId] = !checked;
                this.showToast('حدث خطأ في الاتصال', 'error');
            }
        }
    };
}
</script>
@endsection
