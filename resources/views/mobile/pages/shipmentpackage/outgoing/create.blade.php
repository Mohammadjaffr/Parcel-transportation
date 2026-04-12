@extends('mobile.layouts.app')

@section('title', 'إنشاء شحنة جديدة')

@section('content')
<div class="flex flex-col gap-6 px-4 pb-32 pt-4" 
     x-data="{ 
        selectedParcels: [], 
        totalWeight: 0,
        updateStats() {
            let weight = 0;
            this.selectedParcels.forEach(p => {
                weight += parseFloat(p.weight);
            });
            this.totalWeight = weight.toFixed(2);
        },
        toggleParcel(id, weight) {
            const index = this.selectedParcels.findIndex(p => p.id === id);
            if (index > -1) {
                this.selectedParcels.splice(index, 1);
            } else {
                this.selectedParcels.push({id: id, weight: weight});
            }
            this.updateStats();
        }
     }">

    {{-- الهيدر --}}
    <div class="flex items-center gap-4">
        <a href="" 
           class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-slate-500 active:scale-90 transition-all">
            <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
        </a>
        <div>
            <h1 class="text-xl font-black font-headline text-slate-800">تجميع شحنة</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase">Create New Manifest</p>
        </div>
    </div>

    <form action="" method="POST" id="packageForm">
        @csrf
        
        {{-- حقول الطرود المختارة المخفية --}}
        <template x-for="parcel in selectedParcels" :key="parcel.id">
            <input type="hidden" name="parcel_ids[]" :value="parcel.id">
        </template>

        <div class="space-y-6">
            {{-- بطاقة البيانات الأساسية --}}
            <div class="bg-white p-6 rounded-[2.5rem] border border-slate-50 shadow-sm space-y-5">
                
                {{-- اختيار الوجهة --}}
                <div>
                    <label class="block px-1 mb-2 text-xs font-black text-slate-500 font-headline">وجهة الشحنة (الفرع المستلم)</label>
                    <div class="relative">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">store</span>
                        <select name="receiver_branch_id" required
                                class="w-full h-14 pr-12 pl-4 bg-slate-50 border-none rounded-2xl ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 outline-none text-sm font-bold appearance-none">
                            <option value="" disabled selected>اختر الفرع المستلم...</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- اختيار السائق --}}
                <div>
                    <label class="block px-1 mb-2 text-xs font-black text-slate-500 font-headline">السائق المسؤول</label>
                    <div class="relative">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">person_pin</span>
                        <select name="driver_id" required
                                class="w-full h-14 pr-12 pl-4 bg-slate-50 border-none rounded-2xl ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 outline-none text-sm font-bold appearance-none">
                            <option value="" disabled selected>اختر السائق...</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- رقم التتبع اليدوي (اختياري أو تلقائي) --}}
                <div>
                    <label class="block px-1 mb-2 text-xs font-black text-slate-500 font-headline">رقم تتبع الشحنة</label>
                    <div class="relative">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">tag</span>
                        <input type="text" name="tracking_number" placeholder="اتركه فارغاً للتوليد التلقائي"
                               class="w-full h-14 pr-12 pl-4 bg-slate-50 border-none rounded-2xl ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 outline-none text-sm font-bold uppercase tracking-widest">
                    </div>
                </div>
            </div>

            {{-- قسم اختيار الطرود --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between px-2">
                    <h3 class="font-black text-slate-800 font-headline flex items-center gap-2">
                        الطرود المتاحة
                        <span class="bg-primary/10 text-primary text-[10px] px-2 py-0.5 rounded-full" x-text="selectedParcels.length">0</span>
                    </h3>
                    <button type="button" @click="selectedParcels = []; $el.closest('form').querySelectorAll('input[type=checkbox]').forEach(el => el.checked = false); updateStats()" 
                            class="text-[10px] font-bold text-rose-500 underline underline-offset-4">إلغاء الكل</button>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    @forelse($pendingParcels as $parcel)
                        <label class="relative block group">
                            <input type="checkbox" class="hidden peer" 
                                   @change="toggleParcel({{ $parcel->id }}, {{ $parcel->weight }})">
                            
                            <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm peer-checked:ring-2 peer-checked:ring-primary peer-checked:border-transparent transition-all flex items-center gap-4 active:scale-[0.98]">
                                {{-- أيقونة الطرد --}}
                                <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 peer-checked:bg-primary/10 peer-checked:text-primary shrink-0 transition-colors">
                                    <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                                </div>

                                {{-- بيانات الطرد --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <span class="text-xs font-black text-slate-800 block truncate">{{ $parcel->bond_number }}</span>
                                        <span class="text-[10px] font-bold text-slate-400">{{ $parcel->weight }} كجم</span>
                                    </div>
                                    <span class="text-[10px] font-medium text-slate-500 block truncate">إلى: {{ $parcel->receiverCustomer->name }}</span>
                                </div>

                                {{-- علامة الاختيار --}}
                                <div class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center transition-all peer-checked:bg-primary peer-checked:border-primary">
                                    <span class="material-symbols-outlined text-white text-[16px] hidden peer-checked:block">check</span>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="bg-slate-50 p-10 rounded-[2.5rem] border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400">
                            <span class="material-symbols-outlined text-5xl mb-2 opacity-30">inbox_customize</span>
                            <p class="text-xs font-bold">لا توجد طرود بانتظار الشحن حالياً</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- البار السفلي العائم (Stats & Action) --}}
        <div class="fixed bottom-0 left-0 w-full bg-white/90 backdrop-blur-md border-t border-slate-100 p-4 px-6 z-50 flex items-center justify-between rounded-t-[2rem] shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
            <div class="flex flex-col">
                <span class="text-[10px] font-black text-slate-400 uppercase">إجمالي المختار</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-black text-slate-800" x-text="totalWeight">0</span>
                    <span class="text-[10px] font-bold text-slate-500 uppercase">كجم</span>
                </div>
            </div>

            <button type="submit" :disabled="selectedParcels.length === 0"
                    class="h-14 px-8 bg-primary text-white rounded-2xl font-black text-sm shadow-[0_10px_25px_rgba(36,56,156,0.3)] disabled:bg-slate-200 disabled:shadow-none transition-all active:scale-95 flex items-center gap-2">
                <span>إنشاء الإرسالية</span>
                <span class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center text-[12px]" x-text="selectedParcels.length">0</span>
            </button>
        </div>
    </form>
</div>
@endsection