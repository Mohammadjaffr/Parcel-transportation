@extends('layouts.app')

@section('content')
    <div class="py-16 font-sans bg-gray-50" dir="rtl">
        <div class="container px-4 mx-auto max-w-7xl text-center">
            
            <div class="mb-16">
                <h1 class="mb-4 text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-800 to-blue-500 md:text-5xl">
                    اختر الباقة المناسبة لشركتك
                </h1>
                <p class="text-lg text-gray-500">ارتقِ بأعمال الشحن الخاصة بك مع باقاتنا المرنة والمصممة لتلبية احتياجاتك.</p>
            </div>

            @if(session('error'))
                <div class="flex items-center p-4 mx-auto mb-6 max-w-3xl text-right text-red-800 bg-red-50 rounded-xl border border-red-200 shadow-sm">
                    <svg class="ml-3 w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="flex items-center p-4 mx-auto mb-6 max-w-3xl text-right text-blue-800 bg-blue-50 rounded-xl border border-blue-200 shadow-sm">
                    <svg class="ml-3 w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-8 mx-auto max-w-6xl md:grid-cols-2 lg:grid-cols-3">
                @foreach($packages as $package)
                    @php
                        // التحقق إذا كانت هذه الباقة هي المطلوبة حالياً وقيد الانتظار
                        $isPendingThis = $pendingSubscription && $pendingSubscription->package_id == $package->id;

                        $adminPhone = "967781152674";
                        $appName = auth()->user()->App->name ?? 'شركتي';
                        $waMessage = "مرحباً إدارة النظام، قمت بطلب تفعيل باقة ({$package->name}) لشركة ({$appName}). أرجو تفعيل الحساب.";
                        $waLink = "https://wa.me/{$adminPhone}?text=" . urlencode($waMessage);
                    @endphp

                    <div class="relative flex flex-col p-8 bg-white border border-gray-100 shadow-xl transition-all duration-300 rounded-3xl hover:shadow-2xl hover:-translate-y-2 {{ $isPendingThis ? 'ring-4 ring-green-400/50 border-green-500' : '' }}">
                        
                        @if($isPendingThis)
                            <div class="absolute top-0 right-0 px-4 py-1.5 text-sm font-bold text-white bg-green-500 rounded-tr-3xl rounded-bl-xl">
                                طلبك الحالي (قيد الانتظار)
                            </div>
                        @endif

                        <div class="pt-4 mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">{{ $package->name }}</h2>
                            <div class="flex gap-1 justify-center items-baseline mt-4">
                                <span class="text-5xl font-black text-gray-900">
                                    {{ $package->price == 0 ? 'مجاناً' : '$' . $package->price }}
                                </span>
                            </div>
                            <p class="inline-block px-4 py-1 mt-2 text-sm font-medium text-gray-500 bg-gray-100 rounded-full">
                                صالحة لمدة {{ $package->duration_in_days }} يوماً
                            </p>
                        </div>

                        <div class="mb-6 w-full h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>

                        <ul class="flex-1 mb-8 space-y-4 text-right text-gray-700">
                            <li class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-6 h-6 text-blue-600 bg-blue-100 rounded-full shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span>{{ $package->max_branches ?: 'عدد غير محدود من' }} <strong>الفروع</strong></span>
                            </li>
                            <li class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-6 h-6 text-blue-600 bg-blue-100 rounded-full shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span>{{ $package->max_drivers ?: 'عدد غير محدود من' }} <strong>السائقين</strong></span>
                            </li>
                            <li class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-6 h-6 text-blue-600 bg-blue-100 rounded-full shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span>{{ $package->max_shipments ?: 'غير محدود' }} <strong>طرد / شهرياً</strong></span>
                            </li>
                            <li class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-6 h-6 text-blue-600 bg-blue-100 rounded-full shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span>{{ $package->max_packages ?: 'غير محدود' }} <strong>رحلة مجمعة</strong></span>
                            </li>
                        </ul>

                        <div class="mt-auto">
                            @if($isPendingThis)
                                <a href="{{ $waLink }}" target="_blank"
                                    class="flex justify-center items-center py-3.5 w-full font-bold text-white bg-[#25D366] rounded-xl shadow-lg transition-all hover:bg-[#1EBE5A] hover:shadow-xl hover:-translate-y-0.5 gap-2">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 0C5.385 0 0 5.388 0 12.036c0 2.124.553 4.195 1.602 6.012L.15 24l6.108-1.602a11.96 11.96 0 0 0 5.772 1.488h.005c6.645 0 12.03-5.388 12.03-12.036C24.065 5.388 18.676 0 12.031 0Zm0 21.896c-1.802 0-3.567-.485-5.115-1.403l-.366-.217-3.8.997.997-3.8-.238-.378a9.96 9.96 0 0 1-1.523-5.267c0-5.503 4.478-9.98 9.98-9.98s9.98 4.477 9.98 9.98-4.478 9.98-9.98 9.98Zm5.474-7.48c-.3-.15-1.776-.877-2.052-.977-.276-.1-.477-.15-.677.15-.2.3-.776.977-.952 1.177-.176.2-.352.226-.652.076-1.32-.656-2.39-1.332-3.328-2.614-.24-.326.242-.303.824-1.468.075-.15.037-.282-.038-.433-.075-.15-.677-1.632-.927-2.235-.245-.588-.495-.508-.677-.518-.175-.008-.376-.01-.576-.01-.2 0-.526.075-.802.375-.276.3-.105 1.152.927 2.53 0 0 1.77 2.7 4.28 3.755.57.24 1.01.385 1.354.492.57.18 1.09.155 1.5.093.46-.07 1.406-.575 1.603-1.13.197-.556.197-1.033.137-1.13-.06-.098-.21-.156-.51-.306Z" />
                                    </svg>
                                    تأكيد عبر الواتساب
                                </a>
                            @else
                                <form action="{{ route('subscription.request') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                                    <button type="submit"
                                        class="py-3.5 w-full font-bold text-white bg-blue-600 rounded-xl shadow-md transition-all hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5">
                                        @if($pendingSubscription)
                                            تغيير الطلب لهذه الباقة
                                        @else
                                            اطلب الاشتراك الآن
                                        @endif
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection