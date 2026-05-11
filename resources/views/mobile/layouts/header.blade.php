<header
    class="fixed top-0 w-full z-50 glass-nav border-b border-slate-200/10 shadow-[0_8px_24px_rgba(36,56,156,0.06)] px-4 py-3 flex flex-row items-center justify-between bg-white/80 backdrop-blur-md"
    x-data="{ profileOpen: false, notifOpen: false }">

    {{-- ================= الجزء الأول: قائمة الملف الشخصي ================= --}}
    <div class="relative">
        <button @click="profileOpen = !profileOpen; notifOpen = false"
            class="flex gap-3 items-center p-1 pr-2 text-right rounded-full border border-transparent transition-all hover:bg-slate-50 active:scale-95 hover:border-slate-100">

            <div class="overflow-hidden relative w-11 h-11 rounded-full border-2 shadow-sm border-primary/20 shrink-0">
                <img alt="{{ Auth::user()->name }}" class="object-cover w-full h-full"
                    src="{{ Auth::user()->cached_app_logo }}" />
            </div>

            <div class="flex flex-col min-w-0">
                <span class="text-[10px] font-bold text-slate-400 mb-0.5">مرحباً بك 👋</span>
                <div class="flex gap-1 items-center">
                    <span
                        class="font-headline font-bold text-sm text-slate-900 tracking-tight truncate max-w-[120px] xs:max-w-[150px]">
                        {{ Auth::user()->name }}
                    </span>
                    <span class="material-symbols-outlined text-slate-400 text-[16px] transition-transform duration-300"
                        :class="profileOpen ? 'rotate-180' : ''">expand_more</span>
                </div>
            </div>
        </button>

        <div x-show="profileOpen" @click.outside="profileOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2" x-cloak
            class="absolute top-full right-0 mt-3 w-72 bg-white/95 backdrop-blur-2xl rounded-[2rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-slate-100 overflow-hidden z-50">

            <div class="p-5 bg-gradient-to-br to-transparent border-b from-primary/10 via-primary/5 border-primary/10">
                <div class="flex gap-3 items-center mb-3">
                    <div
                        class="flex justify-center items-center w-10 h-10 bg-white rounded-xl border shadow-sm text-primary border-primary/10 shrink-0">
                        <span class="text-2xl material-symbols-outlined"
                            style="font-variation-settings: 'FILL' 1;">domain</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black font-headline text-slate-800">
                            {{ Auth::user()->cached_app_name }}
                        </h4>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5 flex items-center gap-1">
                            {{ Auth::user()->branch?->name }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-2 items-center p-2 rounded-xl border border-white bg-white/60">
                    @if (Auth::user()->type === 'admin')
                        <span
                            class="bg-primary text-white text-[10px] px-2 py-1 rounded-lg font-bold shadow-sm shadow-primary/20">مدير
                            النظام</span>
                    @else
                        <span
                            class="bg-slate-700 text-white text-[10px] px-2 py-1 rounded-lg font-bold shadow-sm shadow-slate-700/20">موظف</span>
                    @endif
                    <span class="text-xs font-bold truncate text-slate-600">{{ Auth::user()->Branch?->name }}</span>
                </div>
            </div>

            <div class="flex flex-col gap-1 p-2">
                @if (Auth::user()->type === 'admin')
                    <a href="{{ route('app.settings') }}"
                        class="flex gap-3 items-center p-3 text-sm font-bold rounded-2xl transition-colors hover:bg-slate-50 text-slate-600 hover:text-primary font-headline active:scale-95">
                        <span class="material-symbols-outlined text-[20px] bg-slate-100 p-1.5 rounded-lg">settings</span>
                        إعدادات الشركة
                    </a>
                @endif

                <div class="mx-2 my-1 h-px bg-slate-100"></div>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="flex gap-3 items-center p-3 w-full text-sm font-bold rounded-2xl transition-colors hover:bg-red-50 text-slate-600 hover:text-red-600 font-headline active:scale-95">
                        <span
                            class="material-symbols-outlined text-[20px] bg-red-50 text-red-500 p-1.5 rounded-lg">logout</span>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= الجزء الثاني: قائمة الإشعارات ================= --}}
    @hasservice('Notifications')
    <div class="flex gap-1 items-center sm:gap-2">
        <div class="relative">
            <button @click="notifOpen = !notifOpen; profileOpen = false"
                class="flex relative z-10 justify-center items-center w-10 h-10 bg-transparent rounded-full transition-colors duration-200 hover:bg-slate-50 active:scale-90 text-slate-500 hover:text-primary">
                <span class="material-symbols-outlined" data-icon="notifications">notifications</span>

                @if (auth()->user()->unreadNotifications->count() > 0)
                    <span
                        class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-black rounded-full border-2 border-white flex items-center justify-center animate-bounce shadow-sm z-10 pointer-events-none">
                        {{ auth()->user()->unreadNotifications->count() > 99 ? '99+' : auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </button>

            <div x-show="notifOpen" @click.outside="notifOpen = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2" x-cloak
                class="fixed top-[70px] inset-x-4 md:absolute md:top-full md:left-0 md:right-auto md:mt-3 md:w-80 bg-white/95 backdrop-blur-2xl border border-slate-100 rounded-[2rem] shadow-[0_30px_60px_-15px_rgba(0,0,0,0.15)] z-[100] flex flex-col overflow-hidden">

                <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100/50 bg-slate-50/30">
                    <div class="flex gap-2 items-center">
                        <h3 class="text-lg font-bold font-headline text-slate-800">الإشعارات</h3>
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <span class="bg-rose-50 text-rose-500 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ auth()->user()->unreadNotifications->count() }} جديد
                            </span>
                        @endif
                    </div>
                    <form action="" method="GET">
                        <button type="submit"
                            class="text-xs font-bold transition-opacity text-primary hover:opacity-80">تحديد
                            كمقروء</button>
                    </form>
                </div>

                <div class="max-h-[340px] overflow-y-auto overscroll-contain flex flex-col scrollbar-hide">
                    @php
                        $allNotifications = auth()->user()->notifications()->take(15)->get();
                        $unreadNotifications = $allNotifications->whereNull('read_at');
                        $readNotifications = $allNotifications->whereNotNull('read_at');

                        if ($unreadNotifications->count() >= 3) {
                            $displayNotifications = $unreadNotifications;
                        } else {
                            $neededReadCount = 3 - $unreadNotifications->count();
                            $displayNotifications = $unreadNotifications->concat(
                                $readNotifications->take($neededReadCount),
                            );
                        }
                    @endphp

                    @forelse($displayNotifications as $notification)
                        @php
                            $type = $notification->data['type'] ?? '';
                            $isUnread = $notification->unread();

                            // استخدام الرابط الجديد 'url' مع الاحتفاظ بالتوافقية مع 'action_url' القديم
                            $actionUrl = $notification->data['action_url'] ?? '#';
                            if ($actionUrl !== '#') {
                                $actionUrl .=
                                    (parse_url($actionUrl, PHP_URL_QUERY) ? '&' : '?') .
                                    'notify_id=' .
                                    $notification->id;
                            }
                        @endphp

                        <div
                            class="flex flex-col border-b border-slate-50 relative group {{ $isUnread ? 'bg-primary/[0.02]' : 'hover:bg-slate-50' }} transition-colors">

                            @if ($isUnread)
                                <div class="absolute right-0 top-3 bottom-3 w-1 rounded-l-full bg-primary"></div>
                            @endif

                            <div class="flex gap-4 items-start p-4">
                                {{-- 💡 تم إضافة النوع package_received هنا للألوان --}}
                                <div class="w-11 h-11 shrink-0 rounded-2xl flex items-center justify-center shadow-sm border transition-transform group-hover:scale-105
                                    @if ($type == 'connection_request') bg-blue-50 text-blue-500 border-blue-100
                                    @elseif($type == 'connection_accepted') bg-emerald-50 text-emerald-500 border-emerald-100
                                    @elseif($type == 'connection_rejected') bg-rose-50 text-rose-500 border-rose-100
                                    @elseif($type == 'shipment_dispatched') bg-amber-50 text-amber-500 border-amber-100
                                    @elseif($type == 'admin_new_shipment') bg-purple-50 text-purple-600 border-purple-100
                                    @elseif($type == 'admin_new_manifest') bg-teal-50 text-teal-600 border-teal-100
                                    @elseif($type == 'new_shipment') bg-indigo-50 text-indigo-500 border-indigo-100
                                    @elseif($type == 'admin_status_updated') bg-cyan-50 text-cyan-600 border-cyan-100
                                    @elseif($type == 'incoming_package') bg-orange-50 text-orange-600 border-orange-100
                                    @elseif($type == 'package_received') bg-emerald-50 text-emerald-600 border-emerald-100
                                    @else bg-slate-100 text-slate-500 border-slate-200 @endif">

                                    {{-- 💡 تم إضافة الأيقونة الخاصة بـ package_received --}}
                                    <span class="material-symbols-outlined text-[22px]">
                                        @if ($type == 'connection_request')
                                            person_add
                                        @elseif($type == 'connection_accepted')
                                            handshake
                                        @elseif($type == 'connection_rejected')
                                            block
                                        @elseif($type == 'shipment_dispatched')
                                            local_shipping
                                        @elseif($type == 'admin_new_shipment')
                                            add_box
                                        @elseif($type == 'admin_new_manifest')
                                            all_inbox
                                        @elseif($type == 'new_shipment')
                                            unarchive
                                        @elseif($type == 'admin_status_updated')
                                            update
                                        @elseif($type == 'incoming_package')
                                            local_shipping
                                        @elseif($type == 'package_received')
                                            inventory_2
                                        @else
                                            notifications
                                        @endif
                                    </span>
                                </div>

                                <div class="flex relative flex-col gap-1 w-full text-right">
                                    <a href="{{ $actionUrl }}" class="flex flex-col w-full">
                                        <div class="flex gap-2 justify-between items-start w-full">
                                            {{-- 💡 تم إضافة العنوان الخاص بـ package_received --}}
                                            <p
                                                class="text-sm font-headline font-bold {{ $isUnread ? 'text-slate-800' : 'text-slate-600' }}">
                                                @if ($type == 'connection_request')
                                                    طلب ربط جديد
                                                @elseif($type == 'connection_accepted')
                                                    تم قبول طلبك 🎉
                                                @elseif($type == 'connection_rejected')
                                                    تم رفض طلبك
                                                @elseif($type == 'shipment_dispatched')
                                                    طرد في الطريق
                                                @elseif($type == 'admin_new_shipment')
                                                    طرد جديد (الإدارة)
                                                @elseif($type == 'admin_new_manifest')
                                                    إرسال شحنة جديده(الإدارة)
                                                @elseif($type == 'new_shipment')
                                                    طرد وارد 📦
                                                @elseif($type == 'admin_status_updated')
                                                    تحديث حالة 🔄
                                                @elseif($type == 'incoming_package')
                                                    إرسالية في الطريق
                                                @elseif($type == 'package_received')
                                                    تم الاستلام بالمستودع ✅
                                                @else
                                                    إشعار
                                                @endif
                                            </p>
                                            <span class="text-[10px] text-slate-400 font-bold whitespace-nowrap mt-0.5">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                            {{ $notification->data['message'] }}
                                        </p>
                                    </a>

                                    {{-- أزرار القبول والرفض لطلبات الربط --}}
                                    @if ($type == 'connection_request' && $isUnread)
                                        <div class="flex relative z-10 gap-2 mt-3">
                                            <form
                                                action="{{ route('connections.accept', $notification->data['connection_id'] ?? 0) }}?notify_id={{ $notification->id }}"
                                                method="POST" class="flex-1">
                                                @csrf
                                                <button
                                                    class="w-full py-2 bg-primary text-white text-[10px] font-bold rounded-xl active:scale-95 transition-all shadow-sm shadow-primary/20">قبول</button>
                                            </form>

                                            <form
                                                action="{{ route('connections.reject', $notification->data['connection_id'] ?? 0) }}?notify_id={{ $notification->id }}"
                                                method="POST" class="flex-1">
                                                @csrf
                                                <button
                                                    class="w-full py-2 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-xl active:scale-95 transition-all">رفض</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col justify-center items-center py-12 text-slate-400">
                            <span class="mb-2 text-5xl opacity-20 material-symbols-outlined">notifications_off</span>
                            <p class="text-xs font-bold">لا توجد إشعارات حالياً</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endhasservice
</header>