@extends('layouts.app')
@section('title', 'إدارة الباقات | المشرف العام')

@section('content')
<div x-data="packagesManager()" class="space-y-6">

    {{-- Toast --}}
    <div x-cloak x-show="toast.show" x-transition class="fixed top-6 left-1/2 z-[9999] -translate-x-1/2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-lg" :class="toast.type==='success'?'bg-emerald-500':'bg-red-500'">
        <span x-text="toast.message"></span>
    </div>

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">إدارة الباقات</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">إنشاء وإدارة باقات الاشتراك</p>
        </div>
        <button @click="showModal = true" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:shadow-md">
            <span class="material-symbols-outlined text-[20px]">add</span> إنشاء باقة جديدة
        </button>
    </div>

    {{-- Packages Grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($packages as $package)
        <div class="group relative overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 transition-all duration-300 hover:shadow-lg dark:bg-gray-800 dark:ring-gray-700"
             id="pkg-{{ $package->id }}">
            {{-- Color top bar --}}
            <div class="h-1.5 transition-colors duration-200"
                 :class="pkgStates[{{ $package->id }}] ? 'bg-gradient-to-r from-primary to-amber-400' : 'bg-gray-300 dark:bg-gray-600'"></div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">{{ $package->name }}</h3>
                    <label class="relative inline-flex h-6 w-11 cursor-pointer items-center rounded-full transition-colors duration-200"
                           :class="pkgStates[{{ $package->id }}] ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'">
                        <input type="checkbox" class="peer sr-only"
                               :checked="pkgStates[{{ $package->id }}]"
                               @change="togglePkgStatus({{ $package->id }}, $event.target.checked)" />
                        <span class="absolute h-4 w-4 rounded-full bg-white shadow transition-all duration-200"
                              :class="pkgStates[{{ $package->id }}] ? 'right-1' : 'left-1'"></span>
                    </label>
                </div>
                <div class="mt-4 flex items-baseline gap-1">
                    <span class="text-3xl font-black text-primary">{{ number_format($package->price, 0) }}</span>
                    <span class="text-sm font-bold text-gray-400">ر.ي / {{ $package->duration_in_days }} يوم</span>
                </div>
                <div class="mt-5 space-y-3">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="material-symbols-outlined text-[18px] text-emerald-500">check_circle</span>
                        <span><b class="text-gray-900 dark:text-white">{{ $package->max_branches }}</b> فروع</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="material-symbols-outlined text-[18px] text-emerald-500">check_circle</span>
                        <span><b class="text-gray-900 dark:text-white">{{ $package->max_drivers }}</b> سائقين</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="material-symbols-outlined text-[18px] text-emerald-500">check_circle</span>
                        <span><b class="text-gray-900 dark:text-white">{{ $package->max_shipments }}</b> شحنات</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="material-symbols-outlined text-[18px] text-emerald-500">check_circle</span>
                        <span><b class="text-gray-900 dark:text-white">{{ $package->max_packages }}</b> حزم</span>
                    </div>
                </div>
                <div class="mt-5 flex items-center gap-2 text-xs font-bold text-gray-400 dark:text-gray-500">
                    <span class="material-symbols-outlined text-[16px]">group</span>
                    {{ $package->subscriptions_count }} اشتراك
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
            <span class="material-symbols-outlined text-[48px] text-gray-300">workspace_premium</span>
            <p class="mt-2 text-sm text-gray-400">لا توجد باقات بعد</p>
        </div>
        @endforelse
    </div>

    {{-- Create Package Modal --}}
    <div x-cloak x-show="showModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div x-show="showModal" x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
        <div x-show="showModal" x-transition class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-gray-900 dark:text-white">إنشاء باقة جديدة</h3>
                <button @click="showModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <form @submit.prevent="createPackage()" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">اسم الباقة</label>
                    <input type="text" x-model="form.name" required class="w-full rounded-xl border-0 bg-gray-50 py-2.5 px-4 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">السعر (ر.ي)</label>
                        <input type="number" x-model="form.price" required min="0" class="w-full rounded-xl border-0 bg-gray-50 py-2.5 px-4 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">المدة (يوم)</label>
                        <input type="number" x-model="form.duration_in_days" required min="1" class="w-full rounded-xl border-0 bg-gray-50 py-2.5 px-4 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">حد الفروع</label>
                        <input type="number" x-model="form.max_branches" required min="1" class="w-full rounded-xl border-0 bg-gray-50 py-2.5 px-4 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">حد السائقين</label>
                        <input type="number" x-model="form.max_drivers" required min="1" class="w-full rounded-xl border-0 bg-gray-50 py-2.5 px-4 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">حد الشحنات</label>
                        <input type="number" x-model="form.max_shipments" required min="1" class="w-full rounded-xl border-0 bg-gray-50 py-2.5 px-4 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">حد الحزم</label>
                        <input type="number" x-model="form.max_packages" required min="1" class="w-full rounded-xl border-0 bg-gray-50 py-2.5 px-4 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="rounded-xl px-5 py-2.5 text-sm font-bold text-gray-700 ring-1 ring-gray-200 transition hover:bg-gray-50 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700">إلغاء</button>
                    <button type="submit" :disabled="loading" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:shadow-md disabled:opacity-50">
                        <span x-show="loading" class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                        حفظ الباقة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function packagesManager() {
    return {
        showModal: false,
        loading: false,
        form: { name: '', price: '', duration_in_days: 30, max_branches: 5, max_drivers: 10, max_shipments: 500, max_packages: 50 },
        toast: { show: false, message: '', type: 'success' },
        pkgStates: {
            @foreach($packages as $package)
                {{ $package->id }}: {{ $package->is_active ? 'true' : 'false' }},
            @endforeach
        },
        showToast(msg, type = 'success') {
            this.toast = { show: true, message: msg, type };
            setTimeout(() => this.toast.show = false, 3000);
        },
        async createPackage() {
            this.loading = true;
            try {
                const res = await fetch('{{ route("superadmin.packages.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });
                const data = await res.json();
                if (data.status === 'success') {
                    this.showToast(data.message);
                    this.showModal = false;
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showToast(Object.values(data.errors || {}).flat().join(', ') || 'خطأ في البيانات', 'error');
                }
            } catch (e) {
                console.error('createPackage error:', e);
                this.showToast('حدث خطأ في الاتصال', 'error');
            }
            this.loading = false;
        },
        async togglePkgStatus(packageId, checked) {
            try {
                const res = await fetch(`/superadmin/packages/${packageId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.status === 'success') {
                    this.pkgStates[packageId] = data.is_active;
                    this.showToast(data.message);
                } else {
                    this.pkgStates[packageId] = !checked;
                    this.showToast('حدث خطأ', 'error');
                }
            } catch (e) {
                console.error('togglePkgStatus error:', e);
                this.pkgStates[packageId] = !checked;
                this.showToast('حدث خطأ في الاتصال', 'error');
            }
        }
    };
}
</script>
@endsection
