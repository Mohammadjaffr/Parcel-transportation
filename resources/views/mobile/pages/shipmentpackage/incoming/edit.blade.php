@extends('mobile.layouts.app')

@section('title', 'تعديل الإرسالية #' . $package->id)

@section('content')
<div x-data="{
        isSubmitting: false,
        showModal: false,
        itemIndexToRemove: null,
        items: [
            @foreach($package->shipments as $shipment)
            {
                id: '{{ $shipment->id }}',
                bond_number: '{{ $shipment->code }}',
                receiver_name: '{{ $shipment->receiverCustomer->name ?? '' }}',
                receiver_phone: '{{ $shipment->receiverCustomer->phone ?? '' }}',
                package_type: '{{ $shipment->package_type }}',
                payment_status: '{{ $shipment->payment_method == 'prepaid' ? 'paid' : 'unpaid' }}',
                amount: '{{ $shipment->total_amount }}'
            },
            @endforeach
        ],
        addItem() {
            this.items.push({ id: '', bond_number: '', receiver_name: '', receiver_phone: '', package_type: 'كرتون', payment_status: 'unpaid', amount: '' });
        },
        confirmRemove(index) {
            this.itemIndexToRemove = index;
            this.showModal = true;
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
                if (this.activeItem === index) {
                    this.activeItem = Math.max(0, index - 1);
                } else if (this.activeItem > index) {
                    this.activeItem--;
                }
            } else {
                alert('يجب أن تحتوي الإرسالية على طرد واحد على الأقل.');
            }
        },
        submitForm() {
            if(this.items.length === 0) { alert('يجب إضافة طرد واحد على الأقل.'); return; }
            this.isSubmitting = true;
            document.getElementById('editPackageMobileForm').submit();
        }
    }" class="pb-24 font-body" dir="rtl">
    
    {{-- هيدر الموبايل --}}
    <div class="flex gap-3 items-center p-4 bg-white border-b border-gray-100 dark:bg-boxdark dark:border-boxdark-2">
        <a href="{{ route('shipmentpackage.incoming.show', $package->id) }}" class="flex justify-center items-center w-10 h-10 text-gray-500 bg-gray-50 rounded-xl active:scale-95 dark:bg-boxdark-2">
            <span class="material-symbols-outlined">arrow_forward</span>
        </a>
        <h1 class="text-lg font-black text-on-surface dark:text-white font-headline">تعديل الإرسالية</h1>
    </div>

    <form id="editPackageMobileForm" action="{{ route('shipmentpackage.incoming.update', $package->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="p-4 space-y-6">
            {{-- بطاقة بيانات الرحلة --}}
            <div class="p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                <h3 class="mb-4 text-sm font-black text-primary font-headline">بيانات الرحلة الأساسية</h3>
                <div class="space-y-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-500">رقم التتبع</label>
                        <input type="text" name="id" value="{{ old('id', $package->id) }}" required class="px-4 h-12 text-sm bg-gray-50 rounded-xl border border-gray-200 focus:border-primary dark:bg-boxdark-2 dark:border-boxdark dark:text-white">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-500">فرع الزاجل المرسل</label>
                        <select name="sender_office_branch_id" required class="px-4 h-12 text-sm bg-gray-50 rounded-xl border border-gray-200 focus:border-primary dark:bg-boxdark-2 dark:border-boxdark dark:text-white">
                            @foreach($offices as $office)
                                <optgroup label="{{ $office->name }}">
                                    @foreach($office->branches as $branch)
                                        <option value="{{ $branch->id }}" {{ (old('sender_office_branch_id', $package->sender_office_branch_id) == $branch->id) ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-500">اسم السائق</label>
                        <input type="text" name="driver_name" value="{{ old('driver_name', $package->driver->name ?? '') }}" required class="px-4 h-12 text-sm bg-gray-50 rounded-xl border border-gray-200 focus:border-primary dark:bg-boxdark-2 dark:border-boxdark dark:text-white">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="flex justify-between text-xs font-bold text-gray-500">رقم هاتف السائق</label>
                        <input type="tel" name="driver_phone" value="{{ old('driver_phone', $package->driver->phone ?? '') }}" required dir="ltr" class="px-4 h-12 text-sm text-right bg-gray-50 rounded-xl border border-gray-200 focus:border-primary dark:bg-boxdark-2 dark:border-boxdark dark:text-white">
                    </div>
                </div>
            </div>

            {{-- الطرود --}}
            <div class="flex justify-between items-center px-1">
                <h3 class="text-base font-black text-on-surface font-headline dark:text-white">الطرود المضمنة</h3>
                <button type="button" @click="addItem" class="flex gap-1 items-center text-xs font-black text-primary">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span> إضافة
                </button>
            </div>

            <div class="space-y-4">
               <template x-for="(item, index) in items" :key="index">
                    <div class="relative p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                        <input type="hidden" :name="'items['+index+'][id]'" x-model="item.id">
                        
                        {{-- تمت إضافة x-show هنا لإخفاء الزر عن الطرد الأول (الذي يحمل الفهرس 0) --}}
                        <button type="button" x-show="index > 0" @click="confirmRemove(index)" class="flex absolute top-4 left-4 justify-center items-center w-8 h-8 text-rose-500 bg-rose-50 rounded-lg active:scale-90 dark:bg-rose-500/10">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                        
                        <div class="pr-8 mb-4 text-sm font-black text-primary">طرد #<span x-text="index + 1"></span></div>
                        <div class="space-y-3">
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-gray-400">رقم السند</label>
                                <input type="text" :name="'items['+index+'][bond_number]'" x-model="item.bond_number" required class="px-3 h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 dark:bg-boxdark-2 dark:border-boxdark dark:text-white">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-gray-400">اسم المستلم</label>
                                <input type="text" :name="'items['+index+'][receiver_name]'" x-model="item.receiver_name" required class="px-3 h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 dark:bg-boxdark-2 dark:border-boxdark dark:text-white">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-gray-400">هاتف المستلم</label>
                                <input type="tel" :name="'items['+index+'][receiver_phone]'" x-model="item.receiver_phone" required dir="ltr" class="px-3 h-11 text-sm text-right bg-gray-50 rounded-xl border border-gray-200 dark:bg-boxdark-2 dark:border-boxdark dark:text-white">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- الموديل (Modal) --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div @click.outside="showModal = false" class="p-6 w-full max-w-sm bg-white rounded-3xl shadow-2xl">
                <div class="flex justify-center mb-4 text-rose-500"><span class="text-4xl material-symbols-outlined">delete_forever</span></div>
                <h3 class="mb-2 text-lg font-black text-center text-slate-800">تأكيد الحذف</h3>
                <p class="mb-6 text-sm font-bold text-center text-slate-500">هل أنت متأكد من حذف هذا الطرد؟</p>
                <div class="flex gap-3">
                    <button type="button" @click="showModal = false" class="flex-1 py-3 text-sm font-black rounded-xl text-slate-600 bg-slate-100">إلغاء</button>
                    <button type="button" @click="executeRemove()" class="flex-1 py-3 text-sm font-black text-white bg-rose-500 rounded-xl">نعم، احذفه</button>
                </div>
            </div>
        </div>

        {{-- الشريط السفلي للحفظ --}}
        <div class="fixed right-0 bottom-0 left-0 z-50 p-4 bg-white border-t border-gray-100 dark:bg-boxdark dark:border-boxdark-2 pb-safe">
            <button type="button" @click="submitForm" :disabled="isSubmitting" class="flex justify-center items-center w-full h-14 text-base font-black text-white bg-emerald-500 rounded-2xl shadow-lg transition-transform active:scale-95 shadow-emerald-500/20">
                <span x-show="!isSubmitting">حفظ التعديلات</span>
                <span x-show="isSubmitting" class="flex gap-2 items-center"><span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> جاري الحفظ...</span>
            </button>
        </div>
    </form>
</div>
@endsection