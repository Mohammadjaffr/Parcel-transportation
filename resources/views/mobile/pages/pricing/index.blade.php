@extends('mobile.layouts.app')

@section('content')
    <div class="container mx-auto py-12 px-4 text-center">
        <h1 class="text-3xl font-bold mb-4 text-gray-800">اختر الباقة المناسبة لشركتك</h1>
        <p class="text-gray-600 mb-10">ارتقِ بأعمال الشحن الخاصة بك مع باقاتنا المرنة.</p>

        @if(session('error'))
            <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded mb-6 text-right">
                {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="bg-blue-100 border-r-4 border-blue-500 text-blue-700 p-4 rounded mb-6 text-right">
                {{ session('info') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($packages as $package)
                @php
                    // التحقق إذا كانت هذه الباقة هي المطلوبة حالياً وقيد الانتظار
                    $isPendingThis = $pendingSubscription && $pendingSubscription->package_id == $package->id;

                    $adminPhone = "967781152674";
                    $appName = auth()->user()->App->name ?? 'شركتي';
                    $waMessage = "مرحباً إدارة النظام، قمت بطلب تفعيل باقة ({$package->name}) لشركة ({$appName}). أرجو تفعيل الحساب.";
                    $waLink = "https://wa.me/{$adminPhone}?text=" . urlencode($waMessage);
                @endphp

                <div
                    class="bg-white rounded-2xl shadow-xl p-8 border @if($isPendingThis) border-green-500 @else border-gray-100 @endif transition-all relative overflow-hidden">

                    @if($isPendingThis)
                        <div class="absolute top-0 left-0 bg-green-500 text-white text-xs px-3 py-1 rounded-br-lg font-bold">
                            طلبك الحالي (قيد الانتظار)
                        </div>
                    @endif

                    <h2 class="text-2xl font-bold text-gray-800">{{ $package->name }}</h2>
                    <div class="text-4xl font-black text-blue-600 my-4">
                        {{ $package->price == 0 ? 'مجاناً' : '$' . $package->price }}
                    </div>
                    <p class="text-gray-500 mb-6 text-sm">صالحة لمدة {{ $package->duration_in_days }} يوماً</p>

                    <ul class="text-right text-gray-600 space-y-4 mb-8 border-t border-gray-50 pt-6">
                        <li class="flex items-center justify-end">
                            <span>{{ $package->max_branches ?: 'عدد غير محدود من' }} <strong>الفروع</strong></span>
                            <span class="text-green-500 mr-2">✔</span>
                        </li>
                        <li class="flex items-center justify-end">
                            <span>{{ $package->max_drivers ?: 'عدد غير محدود من' }} <strong>السائقين</strong></span>
                            <span class="text-green-500 mr-2">✔</span>
                        </li>
                        <li class="flex items-center justify-end">
                            <span>{{ $package->max_shipments ?: 'غير محدود' }} <strong>طرد/شهرياً</strong></span>
                            <span class="text-green-500 mr-2">✔</span>
                        </li>
                        <li class="flex items-center justify-end">
                            <span>{{ $package->max_packages ?: 'غير محدود' }} <strong>رحلة مجمعة</strong></span>
                            <span class="text-green-500 mr-2">✔</span>
                        </li>
                    </ul>

                    @if($isPendingThis)
                        <a href="{{ $waLink }}" target="_blank"
                            class="w-full flex items-center justify-center bg-green-500 text-white font-bold py-4 rounded-xl hover:bg-green-600 transition-all shadow-lg">
                            <svg class="w-6 h-6 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.031 0C5.385 0 0 5.388 0 12.036c0 2.124.553 4.195 1.602 6.012L.15 24l6.108-1.602a11.96 11.96 0 0 0 5.772 1.488h.005c6.645 0 12.03-5.388 12.03-12.036C24.065 5.388 18.676 0 12.031 0Zm0 21.896c-1.802 0-3.567-.485-5.115-1.403l-.366-.217-3.8.997.997-3.8-.238-.378a9.96 9.96 0 0 1-1.523-5.267c0-5.503 4.478-9.98 9.98-9.98s9.98 4.477 9.98 9.98-4.478 9.98-9.98 9.98Zm5.474-7.48c-.3-.15-1.776-.877-2.052-.977-.276-.1-.477-.15-.677.15-.2.3-.776.977-.952 1.177-.176.2-.352.226-.652.076-1.32-.656-2.39-1.332-3.328-2.614-.24-.326.242-.303.824-1.468.075-.15.037-.282-.038-.433-.075-.15-.677-1.632-.927-2.235-.245-.588-.495-.508-.677-.518-.175-.008-.376-.01-.576-.01-.2 0-.526.075-.802.375-.276.3-.105 1.152.927 2.53 0 0 1.77 2.7 4.28 3.755.57.24 1.01.385 1.354.492.57.18 1.09.155 1.5.093.46-.07 1.406-.575 1.603-1.13.197-.556.197-1.033.137-1.13-.06-.098-.21-.156-.51-.306Z" />
                            </svg>
                            تأكيد عبر الواتساب
                        </a>
                    @else
                        <form action="{{ route('subscription.request') }}" method="POST">
                            @csrf
                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                            <button type="submit"
                                class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 transition-all shadow-md">
                                @if($pendingSubscription)
                                    تغيير الطلب لهذه الباقة
                                @else
                                    اطلب الاشتراك الآن
                                @endif
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection