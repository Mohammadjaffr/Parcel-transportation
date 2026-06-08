@extends('mobile.layouts.app')

@section('title', 'إدارة الركاب والرحلات')

@section('content')
<div class="flex flex-col gap-6 px-2">
    <div class="mb-2">
        <h1 class="text-2xl font-headline font-bold text-on-surface">إدارة الركاب والرحلات</h1>
        <p class="text-on-surface-variant text-sm">اختر القسم الذي تريد إدارته ومتابعته</p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        {{-- قسم إدارة الركاب --}}
        @hasservice('Passengers')
        <a href="{{ route('passengers.index') }}" class="flex items-center gap-5 p-6 bg-surface-container-lowest rounded-3xl shadow-sm border border-slate-100 active:scale-95 transition-all group">
            <div class="w-14 h-14 bg-primary/10 text-primary rounded-2xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">airline_seat_recline_normal</span>
            </div>
            <div class="flex flex-col">
                <span class="font-headline font-bold text-lg text-on-surface">إدارة الركاب</span>
                <span class="text-xs text-on-surface-variant">متابعة الحجوزات، إضافة وتعديل بيانات الركاب</span>
            </div>
            <span class="material-symbols-outlined mr-auto text-slate-300 transition-transform group-hover:-translate-x-1">chevron_left</span>
        </a>
        @endhasservice

        {{-- قسم إدارة الرحلات --}}
        {{-- @hasservice('Trips') --}}
        <a href="{{ route('trips.index') }}" class="flex items-center gap-5 p-6 bg-surface-container-lowest rounded-3xl shadow-sm border border-slate-100 active:scale-95 transition-all group">
            <div class="w-14 h-14 bg-secondary-container/30 text-secondary rounded-2xl flex items-center justify-center group-hover:bg-secondary group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">commute</span>
            </div>
            <div class="flex flex-col">
                <span class="font-headline font-bold text-lg text-on-surface">إدارة الرحلات</span>
                <span class="text-xs text-on-surface-variant">متابعة الرحلات اليومية، خطوط السير ومواعيد الانطلاق</span>
            </div>
            <span class="material-symbols-outlined mr-auto text-slate-300 transition-transform group-hover:-translate-x-1">chevron_left</span>
        </a>
        {{-- @endhasservice --}}
    </div>
</div>
@endsection