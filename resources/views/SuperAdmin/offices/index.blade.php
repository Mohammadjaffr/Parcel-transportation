@extends('SuperAdmin.layouts.app')
@section('title', 'إدارة المكاتب | المشرف العام')

@section('content')
<div x-data="tenantsManager()" class="space-y-6">

    {{-- Toast Notification --}}
    <div x-cloak x-show="toast.show" x-transition
         class="fixed top-6 left-1/2 z-[9999] -translate-x-1/2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-lg flex items-center gap-2"
         :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'">
        <span class="material-symbols-outlined" x-text="toast.type === 'success' ? 'check_circle' : 'error'"></span>
        <span x-text="toast.message"></span>
    </div>

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 font-headline">إدارة المكاتب</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">إدارة جميع المكاتب والشركات في المنصة</p>
        </div>
        <a href="{{ route('superadmin.dashboard') }}" class="inline-flex gap-1 items-center w-max text-sm font-bold transition-colors text-primary hover:text-primary-hover">
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span> العودة للرئيسية
        </a>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-3 p-4 bg-white rounded-2xl border shadow-sm sm:flex-row sm:items-center border-slate-100">
        <div class="relative flex-1 w-full">
            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
            <input type="text" x-model="search" placeholder="ابحث باسم المكتب..."
                   class="py-3 pr-12 pl-4 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
        </div>
        <div class="relative w-full sm:w-64 shrink-0">
            <select x-model="statusFilter" class="py-3 pr-4 pl-10 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition appearance-none cursor-pointer bg-slate-50 text-slate-700 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary">
                <option value="all">جميع الحالات</option>
                <option value="active">نشط فقط</option>
                <option value="inactive">معطل فقط</option>
            </select>
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400 pointer-events-none">expand_more</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden bg-white rounded-2xl border shadow-sm border-slate-100">
        <div class="overflow-x-auto scrollbar-hide">
            <table class="w-full min-w-[800px] text-right">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-6 py-4 text-xs font-black tracking-wider uppercase text-slate-500">المكتب</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">الفروع</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">المستخدمين</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">الباقة</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">الحالة</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                @forelse($apps as $app)
                <tr x-show="filterRow('{{ addslashes($app->name ?? '') }}', {{ $app->is_active ? 'true' : 'false' }})"
                    class="transition-colors hover:bg-slate-50/60">
                    <td class="px-6 py-4">
                        <a href="{{ route('superadmin.offices.show', $app->id) }}" class="flex gap-3 items-center group">
                            <div class="flex justify-center items-center w-10 h-10 text-sm font-black text-white rounded-xl shadow-sm shrink-0" style="background:{{ $app->color ?? '#f79009' }}">{{ mb_substr($app->name ?? 'م',0,1) }}</div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold truncate text-slate-900 group-hover:text-primary">{{ $app->name ?? 'بدون اسم' }}</p>
                                <p class="mt-0.5 text-xs truncate text-slate-400">{{ $app->created_at?->format('Y/m/d') ?? 'تاريخ غير متوفر' }}</p></p>
                            </div>
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-center text-slate-700">{{ $app->branches_count }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-center text-slate-700">{{ $app->users_count }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-center text-slate-700">
                        <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs rounded-lg bg-slate-100 text-slate-700">
                            {{ $app->currentSubscription?->package?->name ?? '—' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <label class="inline-flex relative items-center w-11 h-6 rounded-full transition-colors duration-200 cursor-pointer"
                               :class="appStates[{{ $app->id }}] ? 'bg-emerald-500' : 'bg-slate-300'">
                            <input type="checkbox" class="sr-only peer"
                                   :checked="appStates[{{ $app->id }}]"
                                   @change="toggleStatus({{ $app->id }}, $event.target.checked)" />
                            <span class="absolute w-4 h-4 bg-white rounded-full shadow transition-all duration-200"
                                  :class="appStates[{{ $app->id }}] ? 'right-1' : 'left-1'"></span>
                        </label>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex gap-2 justify-center items-center">
                            <a href="{{ route('superadmin.offices.show', $app->id) }}" class="inline-flex gap-1.5 items-center px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-primary/5 text-primary hover:bg-primary hover:text-white">
                                <span class="material-symbols-outlined text-[16px]">visibility</span> التفاصيل
                            </a>
                            <button type="button"
                                    @click="resetPassword({{ $app->id }}, '{{ addslashes($app->name ?? '') }}')"
                                    class="inline-flex gap-1.5 items-center px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-amber-500/10 text-amber-600 hover:bg-amber-500 hover:text-white">
                                <span class="material-symbols-outlined text-[16px]">lock_reset</span> الباسورد
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-16 text-center">
                    <div class="flex flex-col justify-center items-center">
                        <div class="flex justify-center items-center mb-3 w-14 h-14 rounded-full bg-slate-50"><span class="material-symbols-outlined text-[32px] text-slate-400">domain_disabled</span></div>
                        <p class="text-sm font-bold text-slate-500">لا توجد مكاتب</p>
                    </div>
                </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t bg-slate-50/50 border-slate-100">{{ $apps->links() }}</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// The javascript function remains identical to your excellent implementation
function tenantsManager() {
    return {
        search: '', statusFilter: 'all', toast: { show: false, message: '', type: 'success' },
        appStates: { @foreach($apps as $app) {{ $app->id }}: {{ $app->is_active ? 'true' : 'false' }}, @endforeach },
        showToast(msg, type = 'success') { this.toast = { show: true, message: msg, type }; setTimeout(() => this.toast.show = false, 3000); },
        filterRow(name, isActive) {
            const matchSearch = !this.search || name.toLowerCase().includes(this.search.toLowerCase());
            const matchStatus = this.statusFilter === 'all' || (this.statusFilter === 'active' && isActive) || (this.statusFilter === 'inactive' && !isActive);
            return matchSearch && matchStatus;
        },
        async toggleStatus(appId, checked) {
            try {
                const res = await fetch(`/superadmin/offices/${appId}/toggle-status`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
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
                this.appStates[appId] = !checked;
                this.showToast('حدث خطأ في الاتصال', 'error');
            }
        },
        async resetPassword(appId, appName) {
            const password = prompt(`أدخل كلمة المرور الجديدة لمكتب "${appName}" (اترك الحقل فارغاً لتوليد كلمة مرور عشوائية تلقائياً):`);
            if (password === null) return;

            if (password.trim() !== '' && password.length < 6) {
                this.showToast('يجب أن تكون كلمة المرور 6 أحرف على الأقل', 'error');
                return;
            }

            try {
                const res = await fetch(`/superadmin/offices/${appId}/reset-password`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ password: password })
                });
                const data = await res.json();
                if (res.ok && data.status === 'success') {
                    alert(`${data.message}\nكلمة المرور الجديدة: ${data.password}`);
                    this.showToast('تم إعادة تعيين كلمة المرور بنجاح');
                } else {
                    this.showToast(data.message || 'حدث خطأ أثناء إعادة التعيين', 'error');
                }
            } catch (e) {
                this.showToast('حدث خطأ في الاتصال بالخادم', 'error');
            }
        }
    };
}
</script>
@endsection