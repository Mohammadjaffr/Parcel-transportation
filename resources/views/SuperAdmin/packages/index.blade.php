@extends('SuperAdmin.layouts.app')

@section('title', 'إدارة الباقات | المشرف العام')

@section('content')
<div x-data="packagesManager()" class="space-y-6">

    {{-- Toast --}}
    <div x-cloak
         x-show="toast.show"
         x-transition
         class="fixed top-6 left-1/2 z-[9999] flex -translate-x-1/2 items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-lg"
         :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'">
        <span class="material-symbols-outlined" x-text="toast.type === 'success' ? 'check_circle' : 'error'"></span>
        <span x-text="toast.message"></span>
    </div>

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 font-headline">إدارة الباقات</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">
                إنشاء وإدارة باقات الاشتراك والمميزات
            </p>
        </div>

        <button type="button"
                @click="showModal = true"
                class="inline-flex gap-2 justify-center items-center px-5 py-3 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-primary shadow-primary/30 hover:bg-primary-hover hover:shadow-primary/40">
            <span class="material-symbols-outlined text-[20px]">add</span>
            إنشاء باقة جديدة
        </button>
    </div>

    {{-- Packages Grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($packages as $package)
            <div class="overflow-hidden relative bg-white rounded-2xl border shadow-sm transition-all duration-300 border-slate-100 hover:shadow-xl hover:-translate-y-1 group"
                 id="pkg-{{ $package->id }}">

                {{-- Color top bar --}}
                <div class="h-1.5 transition-colors duration-200"
                     :class="pkgStates[{{ $package->id }}] ? 'bg-gradient-to-r from-primary to-amber-400' : 'bg-slate-200'">
                </div>

                <div class="p-6">
                    <div class="flex gap-3 justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 font-headline">
                                {{ $package->name }}
                            </h3>

                            <button type="button"
                                    @click="openEditPackage({
                                        id: {{ $package->id }},
                                        name: @js($package->name),
                                        price: @js($package->price),
                                        duration_in_days: @js($package->duration_in_days),
                                        max_branches: @js($package->max_branches),
                                        max_drivers: @js($package->max_drivers),
                                        max_shipments: @js($package->max_shipments),
                                        max_packages: @js($package->max_packages)
                                    })"
                                    class="inline-flex gap-1.5 items-center mt-2 text-xs font-bold transition-colors text-primary hover:text-primary-hover">
                                <span class="material-symbols-outlined text-[16px]">edit_square</span>
                                تعديل الباقة
                            </button>
                        </div>

                        <label class="inline-flex relative items-center w-11 h-6 rounded-full transition-colors duration-200 cursor-pointer shrink-0"
                               :class="pkgStates[{{ $package->id }}] ? 'bg-emerald-500' : 'bg-slate-300'">
                            <input type="checkbox"
                                   class="sr-only peer"
                                   :checked="pkgStates[{{ $package->id }}]"
                                   @change="togglePkgStatus({{ $package->id }}, $event.target.checked)" />

                            <span class="absolute w-4 h-4 bg-white rounded-full shadow transition-all duration-200"
                                  :class="pkgStates[{{ $package->id }}] ? 'right-1' : 'left-1'"></span>
                        </label>
                    </div>

                    <div class="flex gap-1 items-baseline pb-5 border-b border-slate-100">
                        <span class="text-4xl font-black text-primary font-headline">
                            {{ number_format($package->price, 0) }}
                        </span>
                        <span class="text-sm font-bold text-slate-400">
                            ر.ي / {{ $package->duration_in_days }} يوم
                        </span>
                    </div>

                    <div class="pt-5 mt-2 space-y-4">
                        <div class="flex gap-3 items-center text-sm text-slate-600">
                            <div class="flex justify-center items-center w-6 h-6 text-emerald-500 bg-emerald-50 rounded-full shrink-0">
                                <span class="material-symbols-outlined text-[14px]">done</span>
                            </div>
                            <span><b class="text-slate-900">{{ $package->max_branches }}</b> فروع كحد أقصى</span>
                        </div>

                        <div class="flex gap-3 items-center text-sm text-slate-600">
                            <div class="flex justify-center items-center w-6 h-6 text-emerald-500 bg-emerald-50 rounded-full shrink-0">
                                <span class="material-symbols-outlined text-[14px]">done</span>
                            </div>
                            <span><b class="text-slate-900">{{ $package->max_drivers }}</b> سائقين</span>
                        </div>

                        <div class="flex gap-3 items-center text-sm text-slate-600">
                            <div class="flex justify-center items-center w-6 h-6 text-emerald-500 bg-emerald-50 rounded-full shrink-0">
                                <span class="material-symbols-outlined text-[14px]">done</span>
                            </div>
                            <span><b class="text-slate-900">{{ $package->max_shipments }}</b> شحنات</span>
                        </div>

                        <div class="flex gap-3 items-center text-sm text-slate-600">
                            <div class="flex justify-center items-center w-6 h-6 text-emerald-500 bg-emerald-50 rounded-full shrink-0">
                                <span class="material-symbols-outlined text-[14px]">done</span>
                            </div>
                            <span><b class="text-slate-900">{{ $package->max_packages }}</b> حزم</span>
                        </div>
                    </div>

                    <div class="flex gap-2 justify-center items-center p-3 mt-6 text-xs font-bold rounded-xl bg-slate-50 text-slate-500">
                        <span class="material-symbols-outlined text-[18px]">group</span>
                        المشتركين: {{ $package->subscriptions_count }}
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col col-span-full justify-center items-center p-12 text-center bg-white rounded-2xl border shadow-sm border-slate-100">
                <div class="flex justify-center items-center mb-4 w-16 h-16 rounded-full bg-slate-50">
                    <span class="material-symbols-outlined text-[36px] text-slate-300">workspace_premium</span>
                </div>
                <p class="text-sm font-bold text-slate-500">
                    لا توجد باقات مضافة بعد
                </p>
            </div>
        @endforelse
    </div>

    {{-- Create Package Modal --}}
    <div x-cloak x-show="showModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6">
        <div x-show="showModal"
             x-transition.opacity
             class="absolute inset-0 backdrop-blur-sm bg-slate-900/60"
             @click="showModal = false"></div>

        <div x-show="showModal"
             x-transition
             class="relative w-full max-w-xl bg-white rounded-3xl shadow-2xl"
             style="max-height: 90vh; overflow-y: auto;">

            <div class="flex justify-between items-center p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-lg font-black text-slate-900 font-headline">
                    إنشاء باقة جديدة
                </h3>

                <button @click="showModal = false"
                        type="button"
                        class="flex justify-center items-center w-8 h-8 bg-white rounded-full border transition-colors text-slate-400 hover:text-slate-700 hover:bg-slate-50 border-slate-200">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form @submit.prevent="createPackage()" class="p-6 space-y-5">
                <div>
                    <label class="block mb-2 text-sm font-bold text-slate-700">اسم الباقة</label>
                    <input type="text"
                           x-model="form.name"
                           required
                           class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary"
                           placeholder="مثال: الباقة الذهبية" />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">السعر (ر.ي)</label>
                        <input type="number"
                               x-model="form.price"
                               required
                               min="0"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">المدة (أيام)</label>
                        <input type="number"
                               x-model="form.duration_in_days"
                               required
                               min="1"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">الحد الأقصى للفروع</label>
                        <input type="number"
                               x-model="form.max_branches"
                               required
                               min="1"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">الحد الأقصى للسائقين</label>
                        <input type="number"
                               x-model="form.max_drivers"
                               required
                               min="1"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">الحد الأقصى للشحنات</label>
                        <input type="number"
                               x-model="form.max_shipments"
                               required
                               min="1"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">الحد الأقصى للحزم</label>
                        <input type="number"
                               x-model="form.max_packages"
                               required
                               min="1"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>
                </div>

                <div class="flex gap-3 justify-end items-center pt-6 mt-6 border-t border-slate-100">
                    <button type="button"
                            @click="showModal = false"
                            class="px-5 py-3 text-sm font-bold bg-white rounded-xl border transition text-slate-700 border-slate-200 hover:bg-slate-50">
                        إلغاء
                    </button>

                    <button type="submit"
                            :disabled="loading"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-white transition-all shadow-lg rounded-xl bg-primary shadow-primary/30 hover:bg-primary-hover min-w-[140px] disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="loading" class="w-5 h-5 rounded-full border-2 border-white animate-spin border-t-transparent"></span>
                        <span x-show="!loading">حفظ الباقة</span>
                        <span x-show="loading">جاري الحفظ...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Package Modal --}}
    <div x-cloak x-show="editModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6">
        <div x-show="editModal"
             x-transition.opacity
             class="absolute inset-0 backdrop-blur-sm bg-slate-900/60"
             @click="editModal = false"></div>

        <div x-show="editModal"
             x-transition
             class="relative w-full max-w-xl bg-white rounded-3xl shadow-2xl"
             style="max-height: 90vh; overflow-y: auto;">

            <div class="flex justify-between items-center p-6 border-b border-slate-100 bg-slate-50/50">
                <div>
                    <h3 class="text-lg font-black text-slate-900 font-headline">
                        تعديل الباقة
                    </h3>
                    <p class="mt-1 text-xs font-bold text-slate-500">
                        سيتم تطبيق التعديل على الباقة، والاشتراكات الحالية ستظل مرتبطة بنفس الباقة.
                    </p>
                </div>

                <button @click="editModal = false"
                        type="button"
                        class="flex justify-center items-center w-8 h-8 bg-white rounded-full border transition-colors text-slate-400 hover:text-slate-700 hover:bg-slate-50 border-slate-200">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form @submit.prevent="updatePackage()" class="p-6 space-y-5">
                <div>
                    <label class="block mb-2 text-sm font-bold text-slate-700">اسم الباقة</label>
                    <input type="text"
                           x-model="editForm.name"
                           required
                           class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary"
                           placeholder="مثال: الباقة الذهبية" />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">السعر</label>
                        <input type="number"
                               x-model="editForm.price"
                               required
                               min="0"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">المدة بالأيام</label>
                        <input type="number"
                               x-model="editForm.duration_in_days"
                               required
                               min="1"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">عدد الفروع</label>
                        <input type="number"
                               x-model="editForm.max_branches"
                               required
                               min="1"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">عدد السائقين</label>
                        <input type="number"
                               x-model="editForm.max_drivers"
                               required
                               min="1"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">عدد الشحنات</label>
                        <input type="number"
                               x-model="editForm.max_shipments"
                               required
                               min="1"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">عدد الحزم</label>
                        <input type="number"
                               x-model="editForm.max_packages"
                               required
                               min="1"
                               class="px-4 py-3 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition bg-slate-50 text-slate-900 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
                    </div>
                </div>

                <div class="flex gap-3 justify-end items-center pt-6 mt-6 border-t border-slate-100">
                    <button type="button"
                            @click="editModal = false"
                            class="px-5 py-3 text-sm font-bold bg-white rounded-xl border transition-colors text-slate-600 border-slate-200 hover:bg-slate-50">
                        إلغاء
                    </button>

                    <button type="submit"
                            :disabled="loading"
                            class="inline-flex gap-2 justify-center items-center px-5 py-3 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-primary shadow-primary/30 hover:bg-primary-hover disabled:opacity-60 disabled:cursor-not-allowed">
                        <span x-show="loading" class="w-5 h-5 rounded-full border-2 border-white animate-spin border-t-transparent"></span>
                        <span x-show="!loading">حفظ التعديلات</span>
                        <span x-show="loading">جاري الحفظ...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
function packagesManager() {
    return {
        showModal: false,
        editModal: false,
        loading: false,

        toast: {
            show: false,
            message: '',
            type: 'success'
        },

        pkgStates: {
            @foreach($packages as $package)
                {{ $package->id }}: {{ $package->is_active ? 'true' : 'false' }},
            @endforeach
        },

        form: {
            name: '',
            price: '',
            duration_in_days: 30,
            max_branches: 1,
            max_drivers: 1,
            max_shipments: 100,
            max_packages: 10,
        },

        editForm: {
            id: null,
            name: '',
            price: '',
            duration_in_days: 30,
            max_branches: 1,
            max_drivers: 1,
            max_shipments: 100,
            max_packages: 10,
        },

        showToast(message, type = 'success') {
            this.toast = {
                show: true,
                message: message,
                type: type
            };

            setTimeout(() => {
                this.toast.show = false;
            }, 3000);
        },

        resetForm() {
            this.form = {
                name: '',
                price: '',
                duration_in_days: 30,
                max_branches: 1,
                max_drivers: 1,
                max_shipments: 100,
                max_packages: 10,
            };
        },

        openEditPackage(packageData) {
            this.editForm = {
                id: packageData.id,
                name: packageData.name,
                price: packageData.price,
                duration_in_days: packageData.duration_in_days,
                max_branches: packageData.max_branches,
                max_drivers: packageData.max_drivers,
                max_shipments: packageData.max_shipments,
                max_packages: packageData.max_packages,
            };

            this.editModal = true;
        },

        async createPackage() {
            if (this.loading) return;

            this.loading = true;

            try {
                const res = await fetch(`{{ route('superadmin.packages.store') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await res.json();

                if (!res.ok || data.status !== 'success') {
                    this.showToast(data.message || 'تعذر إنشاء الباقة', 'error');
                    return;
                }

                this.showToast(data.message || 'تم إنشاء الباقة بنجاح');
                this.showModal = false;
                this.resetForm();

                setTimeout(() => location.reload(), 700);

            } catch (e) {
                console.error('createPackage error:', e);
                this.showToast('حدث خطأ أثناء الاتصال بالخادم', 'error');
            } finally {
                this.loading = false;
            }
        },

        async updatePackage() {
            if (this.loading) return;

            if (!this.editForm.id) {
                this.showToast('لم يتم تحديد الباقة', 'error');
                return;
            }

            this.loading = true;

            try {
                const res = await fetch(`/superadmin/packages/${this.editForm.id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: this.editForm.name,
                        price: this.editForm.price,
                        duration_in_days: this.editForm.duration_in_days,
                        max_branches: this.editForm.max_branches,
                        max_drivers: this.editForm.max_drivers,
                        max_shipments: this.editForm.max_shipments,
                        max_packages: this.editForm.max_packages,
                    }),
                });

                const data = await res.json();

                if (!res.ok || data.status !== 'success') {
                    this.showToast(data.message || 'تعذر تعديل الباقة', 'error');
                    return;
                }

                this.showToast(data.message || 'تم تعديل الباقة بنجاح');
                this.editModal = false;

                setTimeout(() => location.reload(), 700);

            } catch (e) {
                console.error('updatePackage error:', e);
                this.showToast('حدث خطأ أثناء الاتصال بالخادم', 'error');
            } finally {
                this.loading = false;
            }
        },

        async togglePkgStatus(packageId, checked) {
            const oldValue = this.pkgStates[packageId];
            this.pkgStates[packageId] = checked;

            try {
                const res = await fetch(`/superadmin/packages/${packageId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                const data = await res.json();

                if (!res.ok || data.status !== 'success') {
                    this.pkgStates[packageId] = oldValue;
                    this.showToast(data.message || 'تعذر تحديث حالة الباقة', 'error');
                    return;
                }

                this.pkgStates[packageId] = data.is_active;
                this.showToast(data.message);

            } catch (e) {
                console.error('togglePkgStatus error:', e);
                this.pkgStates[packageId] = oldValue;
                this.showToast('حدث خطأ أثناء تحديث حالة الباقة', 'error');
            }
        },
    };
}
</script>
@endsection