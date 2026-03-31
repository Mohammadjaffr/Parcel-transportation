@extends('mobile.layouts.app')

@section('title', 'إدارة العملاء')

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
    
        editCustomerData: { id: '', name: '', phone: '', whatsapp_number: '', url: '' },
        createCustomerData: { name: '', phone: '', whatsapp_number: '' },
        deleteCustomerData: { id: '', name: '', url: '' },
    
        openEditModal(id, name, phone, whatsapp) {
            this.errors = {};
            this.editCustomerData = {
                id: id,
                name: name,
                phone: phone,
                whatsapp_number: whatsapp || '',
                url: '{{ route('customers.index') }}/' + id
            };
            this.showEditModal = true;
        },
    
        openCreateModal() {
            this.errors = {};
            this.createCustomerData = { name: '', phone: '', whatsapp_number: '' };
            this.showCreateModal = true;
        },
    
        openDeleteModal(id, name) {
            this.deleteCustomerData = {
                id: id,
                name: name,
                url: '{{ route('customers.index') }}/' + id
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
                console.error('Error:', error);
                alert('حدث خطأ في الاتصال بالخادم.');
            } finally {
                this.isSubmitting = false;
            }
        }
    }" class="flex relative flex-col gap-6 pb-24 min-h-screen">

        <!-- Header Section -->
        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-2xl font-black tracking-tight font-headline text-slate-800">العملاء</h1>
                <p class="mt-0.5 text-xs font-semibold text-slate-400">
                    إجمالي <span class="text-primary">{{ $customers->total() }}</span> عميل مسجل
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
                <input type="text" x-model="searchQuery" placeholder="ابحث باسم العميل أو رقم الهاتف..."
                    class="w-full h-14 pr-12 pl-12 rounded-[1.25rem] border-none bg-white shadow-sm ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 transition-all font-headline text-sm text-slate-700 outline-none">

                <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                    class="flex absolute left-4 top-1/2 justify-center items-center w-8 h-8 rounded-xl transition-transform -translate-y-1/2 bg-slate-50 text-slate-400 active:scale-95">
                    <span class="text-lg material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        <!-- Customer List Grid -->
        <div class="px-2 space-y-4">
            @forelse ($customers as $customer)
                @php
                    $balance = ($customer->debit_sum ?? 0) - ($customer->credit_sum ?? 0);
                @endphp
                <div x-show="searchQuery === '' || '{{ $customer->name }}'.includes(searchQuery) || '{{ $customer->phone }}'.includes(searchQuery)"
                    class="bg-white rounded-[1.75rem] p-5 shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-slate-50 relative overflow-hidden active:scale-[0.98] transition-all">

                    <!-- Top Info -->
                    <div class="flex relative z-10 gap-4 items-center mb-4">
                        <div
                            class="flex justify-center items-center w-14 h-14 text-lg font-black bg-gradient-to-br rounded-2xl border shadow-inner from-primary/10 to-primary/5 text-primary font-headline border-primary/5 shrink-0">
                            @php
                                $words = explode(' ', $customer->name);
                                $first = mb_substr($words[0] ?? '', 0, 1, 'utf-8');
                                $second = isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '';
                                echo $first . $second;
                            @endphp
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="mb-1.5 text-base font-bold leading-none truncate font-headline text-slate-800">
                                {{ $customer->name }}</h3>
                            <div class="flex gap-2 items-center text-slate-500">
                                <span class="material-symbols-outlined text-[16px] text-primary/60">phone_iphone</span>
                                <span class="font-mono text-xs font-bold tracking-wider">{{ $customer->phone }}</span>
                            </div>
                        </div>
                        <button type="button"
                            @click="openEditModal({{ $customer->id }}, {{ json_encode($customer->name) }}, {{ json_encode($customer->phone) }}, {{ json_encode($customer->whatsapp_number) }})"
                            class="flex justify-center items-center w-10 h-10 rounded-xl transition-all bg-slate-50 text-slate-400 hover:bg-primary/5 hover:text-primary active:scale-90">
                            <span class="text-xl material-symbols-outlined">edit_square</span>
                        </button>
                    </div>

                    <!-- Financial Stats Badge -->
                    <div class="flex gap-2 p-3 mb-4 rounded-2xl border bg-slate-50 border-slate-100/50">
                        <div class="flex-1 text-center">
                            <span class="block text-[10px] font-bold text-slate-400 mb-1">الشحنات</span>
                            <span
                                class="block text-sm font-black text-slate-700">{{ $customer->shipments_count ?? 0 }}</span>
                        </div>
                        <div class="w-px bg-slate-200/60"></div>
                        <div class="flex-1 text-center">
                            <span class="block text-[10px] font-bold text-slate-400 mb-1">المدفوع</span>
                            <span
                                class="block text-sm font-black text-emerald-600">{{ number_format($customer->credit_sum ?? 0, 0) }}</span>
                        </div>
                        <div class="w-px bg-slate-200/60"></div>
                        <div class="flex-1 text-center">
                            <span class="block text-[10px] font-bold text-slate-400 mb-1">عليه</span>
                            <span
                                class="block text-sm font-black {{ $balance > 0 ? 'text-rose-500' : 'text-slate-700' }}">{{ number_format($balance, 0) }}</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="mb-4 h-px bg-gradient-to-r from-transparent to-transparent via-slate-100"></div>

                    <!-- Actions -->
                    <div class="flex relative z-10 justify-between items-center">
                        <div class="flex gap-2.5">
                            <a href="{{ route('customers.show', $customer->id) }}"
                                class="flex gap-2 items-center px-4 py-2 text-xs font-bold text-white rounded-xl border shadow-md transition-transform bg-primary font-headline active:scale-95 shadow-primary/20">
                                <span class="text-sm material-symbols-outlined"
                                    style="font-variation-settings: 'FILL' 1;">visibility</span>
                                الملف
                            </a>
                            <a href="https://wa.me/{{ $customer->whatsapp_number ?? $customer->phone }}"
                                class="flex gap-2 items-center px-4 py-2 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl border transition-transform font-headline active:scale-95 border-emerald-100/50">
                                <span class="text-sm material-symbols-outlined"
                                    style="font-variation-settings: 'FILL' 1;">chat</span>
                                واتساب
                            </a>
                        </div>

                        <button type="button"
                            @click="openDeleteModal({{ $customer->id }}, {{ json_encode($customer->name) }})"
                            class="flex justify-center items-center w-10 h-10 text-red-500 bg-red-50 rounded-xl transition-all hover:bg-red-100 active:scale-90">
                            <span class="text-xl material-symbols-outlined">delete_outline</span>
                        </button>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div
                    class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 mx-2 shadow-sm">
                    <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-200">
                        <span class="text-6xl material-symbols-outlined">group_off</span>
                    </div>
                    <p class="text-lg font-bold font-headline text-slate-400">لم نعثر على أي عملاء</p>
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
            {{ $customers->links('vendor.pagination.mobile') }}
        </div>

        <!-- ======================== Bottom Sheet Modals ======================== -->

        <!-- Create Customer Bottom Sheet -->
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
                    <h3 class="text-xl font-black font-headline text-slate-800">إضافة عميل جديد</h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 rounded-xl bg-slate-50 text-slate-400">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form @submit.prevent="submitForm('{{ route('customers.store') }}', 'POST', createCustomerData)"
                    class="px-2 space-y-5">
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">الاسم الكامل <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined"
                                :class="errors.name ? 'text-red-400' : 'text-slate-400'">person</span>
                            <input type="text" x-model="createCustomerData.name" placeholder="مثلاً: محمد عبدالله"
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline"
                                :class="errors.name ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                        </div>
                        <template x-if="errors.name">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الهاتف <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined"
                                :class="errors.phone ? 'text-red-400' : 'text-slate-400'">phone</span>
                            <input type="tel" x-model="createCustomerData.phone" placeholder="+967 7xx xxx xxx"
                                class="pr-12 pl-4 w-full h-14 text-sm text-left rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline dir-ltr"
                                :class="errors.phone ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                        </div>
                        <template x-if="errors.phone">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.phone[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الواتساب <span
                                class="text-xs font-normal text-slate-400">(اختياري)</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined"
                                :class="errors.whatsapp_number ? 'text-red-400' : 'text-emerald-500/50'">chat</span>
                            <input type="tel" x-model="createCustomerData.whatsapp_number"
                                placeholder="نفس رقم الهاتف إذا ترك فارغاً"
                                class="pr-12 pl-4 w-full h-14 text-sm text-left rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline dir-ltr"
                                :class="errors.whatsapp_number ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-emerald-500/20'">
                        </div>
                        <template x-if="errors.whatsapp_number">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.whatsapp_number[0]"></p>
                        </template>
                    </div>

                    <button type="submit" :disabled="isSubmitting"
                        class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary font-headline shadow-primary/30 active:scale-95 disabled:opacity-70">
                        <span x-show="!isSubmitting" class="material-symbols-outlined">save</span>
                        <span x-show="isSubmitting"
                            class="animate-spin material-symbols-outlined">progress_activity</span>
                        <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ بيانات العميل'"></span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Edit Customer Bottom Sheet -->
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
                    <h3 class="text-xl font-black font-headline text-slate-800">تعديل بيانات العميل</h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 rounded-xl bg-slate-50 text-slate-400">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form @submit.prevent="submitForm(editCustomerData.url, 'POST', { ...editCustomerData, _method: 'PUT' })"
                    class="px-2 space-y-5">
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">الاسم الكامل <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined"
                                :class="errors.name ? 'text-red-400' : 'text-slate-400'">person</span>
                            <input type="text" x-model="editCustomerData.name"
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline"
                                :class="errors.name ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                        </div>
                        <template x-if="errors.name">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الهاتف <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined"
                                :class="errors.phone ? 'text-red-400' : 'text-slate-400'">phone</span>
                            <input type="tel" x-model="editCustomerData.phone"
                                class="pr-12 pl-4 w-full h-14 text-sm text-left rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline dir-ltr"
                                :class="errors.phone ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                        </div>
                        <template x-if="errors.phone">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.phone[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الواتساب <span
                                class="text-xs font-normal text-slate-400">(اختياري)</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined"
                                :class="errors.whatsapp_number ? 'text-red-400' : 'text-emerald-500/50'">chat</span>
                            <input type="tel" x-model="editCustomerData.whatsapp_number"
                                class="pr-12 pl-4 w-full h-14 text-sm text-left rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline dir-ltr"
                                :class="errors.whatsapp_number ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-emerald-500/20'">
                        </div>
                        <template x-if="errors.whatsapp_number">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.whatsapp_number[0]"></p>
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
                    هل أنت متأكد من أنك تريد حذف العميل <br>
                    <span class="text-base font-bold text-slate-800 font-headline"
                        x-text="deleteCustomerData.name"></span>؟<br>
                    <span class="text-red-500/80">لا يمكن حذف عميل لديه حركات مالية.</span>
                </p>

                <!-- الإرسال عبر AJAX للتحقق من خطأ "لا يمكن الحذف بسبب الحركات المالية" بدون Refresh -->
                <form @submit.prevent="submitForm(deleteCustomerData.url, 'POST', { _method: 'DELETE' })"
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
