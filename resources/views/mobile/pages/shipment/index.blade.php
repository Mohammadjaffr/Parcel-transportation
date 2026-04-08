@extends('mobile.layouts.app')

@section('title', 'إدارة الطرود')

@section('content')
<div class="flex flex-col gap-6 px-2">
    <div class="mb-2">
        <h1 class="text-2xl font-headline font-bold text-on-surface">إدارة الطرود</h1>
        <p class="text-on-surface-variant text-sm">اختر نوع الطرود التي تريد عرضها</p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        
        <a href="{{ route('shipment.index') }}" class="flex items-center gap-5 p-6 bg-surface-container-lowest rounded-3xl shadow-sm border border-slate-100 active:scale-95 transition-all group">
            <div class="w-14 h-14 bg-primary/10 text-primary rounded-2xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">unarchive</span>
            </div>
            <div class="flex flex-col">
                <span class="font-headline font-bold text-lg text-on-surface">الطرود المرسلة</span>
                <span class="text-xs text-on-surface-variant">متابعة الطرود الصادرة من مكتبك</span>
            </div>
            <span class="material-symbols-outlined mr-auto text-slate-300 transition-transform group-hover:-translate-x-1">chevron_left</span>
        </a>

        <a href="#" class="flex items-center gap-5 p-6 bg-surface-container-lowest rounded-3xl shadow-sm border border-slate-100 active:scale-95 transition-all group">
            <div class="w-14 h-14 bg-secondary-container/10 text-secondary rounded-2xl flex items-center justify-center group-hover:bg-secondary-container group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">archive</span>
            </div>
            <div class="flex flex-col">
                <span class="font-headline font-bold text-lg text-on-surface">الطرود المستلمة</span>
                <span class="text-xs text-on-surface-variant">إدارة الطرود الواردة إلى مكتبك</span>
            </div>
            <span class="material-symbols-outlined mr-auto text-slate-300 transition-transform group-hover:-translate-x-1">chevron_left</span>
        </a>

    </div>
</div>
@endsection