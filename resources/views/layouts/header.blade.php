<header x-data="{ menuToggle: false, notifOpen: false }"
    class="flex sticky top-0 z-40 w-full bg-white border-b transition-colors duration-300 dark:border-gray-800 dark:bg-boxdark">

    <div class="flex flex-col justify-between items-center grow lg:flex-row lg:px-6">

        <div
            class="flex justify-between items-center px-3 py-3 w-full border-b border-gray-200 lg:w-auto lg:border-b-0 lg:px-0 lg:py-4 dark:border-gray-800">

            <div class="flex gap-2 items-center sm:gap-4">
                <button @click.stop="sidebarToggle = !sidebarToggle"
                    class="z-[99999] flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-all hover:bg-gray-100 lg:h-11 lg:w-11 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800"
                    :class="sidebarToggle ? 'bg-gray-100 dark:bg-gray-800' : ''">

                    <svg class="hidden fill-current lg:block" width="18" height="14" viewBox="0 0 16 12">
                        <path
                            d="M0.58 1C0.58.58.91.25 1.33.25h13.33c.41 0 .75.33.75.75s-.33.75-.75.75H1.33C.91 1.75.58 1.41.58 1zM0.58 11c0-.41.33-.75.75-.75h13.33c.41 0 .75.33.75.75s-.33.75-.75.75H1.33c-.41 0-.75-.33-.75-.75zM1.33 5.25c-.41 0-.75.33-.75.75s.33.75.75.75h6.66c.41 0 .75-.33.75-.75s-.33-.75-.75-.75H1.33z" />
                    </svg>
                    <svg :class="sidebarToggle ? 'hidden' : 'block lg:hidden'" class="fill-current" width="24"
                        height="24">
                        <path
                            d="M3.25 6c0-.41.33-.75.75-.75h16c.41 0 .75.33.75.75s-.33.75-.75.75H4c-.41 0-.75-.33-.75-.75zM3.25 18c0-.41.33-.75.75-.75h16c.41 0 .75.33.75.75s-.33.75-.75.75H4c-.41 0-.75-.33-.75-.75zM4 11.25c-.41 0-.75.33-.75.75s.33.75.75.75h8c.41 0 .75-.33.75-.75s-.33-.75-.75-.75H4z" />
                    </svg>
                    <svg :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fill-current" width="24"
                        height="24">
                        <path
                            d="M6.22 7.28c-.29-.29-.29-.76 0-1.06.29-.29.77-.29 1.06 0L12 10.94l4.72-4.72c.29-.29.77-.29 1.06 0 .29.29.29.77 0 1.06L13.06 12l4.72 4.72c.29.29.29.77 0 1.06-.29.29-.77.29-1.06 0L12 13.06l-4.72 4.72c-.29.29-.77.29-1.06 0-.29-.29-.29-.77 0-1.06L10.94 12 6.22 7.28z" />
                    </svg>
                </button>

                <a href="{{ url('/') }}" class="block lg:hidden">
                    <img src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" alt="Logo"
                        class="w-auto h-10">
                </a>
            </div>

            <button @click.stop="menuToggle = !menuToggle"
                class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-lg hover:bg-gray-100 lg:hidden dark:text-gray-400 dark:hover:bg-gray-800"
                :class="menuToggle ? 'bg-gray-100 dark:bg-gray-800 text-primary' : ''">
                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24">
                    <circle cx="6" cy="12" r="1.5" />
                    <circle cx="12" cy="12" r="1.5" />
                    <circle cx="18" cy="12" r="1.5" />
                </svg>
            </button>
        </div>

        <div :class="menuToggle ? 'flex' : 'hidden'"
            class="flex-col gap-4 items-center px-5 py-4 w-full bg-white border-b border-gray-100 shadow-sm lg:flex lg:w-auto lg:flex-row lg:border-none lg:bg-transparent lg:px-0 lg:py-0 lg:shadow-none dark:border-gray-800 dark:bg-transparent">

            <div class="flex gap-3 items-center">

                <div x-data="backupManager()">
                    <template x-teleport="body">
                        <div x-show="toastVisible" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="translate-y-[-20px] opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            class="fixed right-5 top-5 z-[10000] flex items-center gap-3 rounded-xl border border-primary/20 bg-white/90 p-4 shadow-2xl backdrop-blur-md dark:bg-boxdark/90">
                            <div class="flex justify-center items-center w-8 h-8 rounded-full bg-primary/10">
                                <span class="text-xl material-symbols-outlined text-primary">check_circle</span>
                            </div>
                            <span class="text-sm font-bold text-gray-800 dark:text-white" x-text="toastMessage"></span>
                        </div>
                    </template>


                </div>

                {{-- <div class="relative">
                    <button @click="notifOpen = !notifOpen; dropdownOpen = false"
                        class="flex relative justify-center items-center w-11 h-11 text-gray-500 rounded-full border border-gray-200 transition-all hover:bg-gray-100 hover:text-primary dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                        <span class="material-symbols-outlined text-[22px]">notifications</span>
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute top-0 right-0 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-black rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center animate-bounce shadow-sm z-10 pointer-events-none">
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
                        class="fixed top-[70px] inset-x-4 lg:absolute lg:top-full {{ app()->getLocale() == 'ar' ? 'lg:left-0' : 'lg:right-0' }} lg:inset-x-auto lg:mt-3 lg:w-80 bg-white/95 backdrop-blur-2xl border border-slate-100 rounded-[2rem] shadow-[0_30px_60px_-15px_rgba(0,0,0,0.15)] z-[100] flex flex-col overflow-hidden dark:bg-boxdark/95 dark:border-gray-800">

                        <div
                            class="flex justify-between items-center px-5 py-4 border-b border-slate-100/50 bg-slate-50/30 dark:border-gray-800 dark:bg-gray-900/30">
                            <div class="flex gap-2 items-center">
                                <h3 class="text-lg font-bold font-headline text-slate-800 dark:text-white">الإشعارات
                                </h3>
                                @if (auth()->user()->unreadNotifications->count() > 0)
                                    <span
                                        class="bg-rose-50 text-rose-500 text-[10px] font-bold px-2 py-0.5 rounded-full dark:bg-rose-500/10">
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
                            @forelse(auth()->user()->notifications->take(15) as $notification)
                                @php
                                    $type = $notification->data['type'] ?? '';
                                    $isUnread = $notification->unread();

                                    $actionUrl = $notification->data['action_url'] ?? '#';
                                    if ($actionUrl !== '#') {
                                        $actionUrl .=
                                            (parse_url($actionUrl, PHP_URL_QUERY) ? '&' : '?') .
                                            'notify_id=' .
                                            $notification->id;
                                    }
                                @endphp

                                <div
                                    class="flex flex-col border-b border-slate-50 dark:border-gray-800/50 relative group {{ $isUnread ? 'bg-primary/[0.02] dark:bg-primary/[0.05]' : 'hover:bg-slate-50 dark:hover:bg-gray-800/50' }} transition-colors">

                                    @if ($isUnread)
                                        <div class="absolute right-0 top-3 bottom-3 w-1 rounded-l-full bg-primary">
                                        </div>
                                    @endif

                                    <div class="flex gap-4 items-start p-4">
                                        <div
                                            class="w-11 h-11 shrink-0 rounded-2xl flex items-center justify-center shadow-sm border transition-transform group-hover:scale-105
                                            @if ($type == 'connection_request') bg-blue-50 text-blue-500 border-blue-100 dark:bg-blue-500/10 dark:border-blue-500/20
                                            @elseif($type == 'connection_accepted') bg-emerald-50 text-emerald-500 border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20
                                            @elseif($type == 'connection_rejected') bg-rose-50 text-rose-500 border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20
                                            @elseif($type == 'shipment_dispatched') bg-amber-50 text-amber-500 border-amber-100 dark:bg-amber-500/10 dark:border-amber-500/20
                                            @elseif($type == 'admin_new_shipment') bg-purple-50 text-purple-600 border-purple-100 dark:bg-purple-500/10 dark:border-purple-500/20
                                            @elseif($type == 'new_shipment') bg-indigo-50 text-indigo-500 border-indigo-100 dark:bg-indigo-500/10 dark:border-indigo-500/20
                                            @elseif($type == 'admin_status_updated') bg-cyan-50 text-cyan-600 border-cyan-100 dark:bg-cyan-500/10 dark:border-cyan-500/20
                                            @else bg-slate-100 text-slate-500 border-slate-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 @endif">

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
                                                @elseif($type == 'new_shipment')
                                                    unarchive
                                                @elseif($type == 'admin_status_updated')
                                                    {{ $notification->data['icon'] ?? 'update' }}
                                                @else
                                                    notifications
                                                @endif
                                            </span>
                                        </div>

                                        <div class="flex relative flex-col gap-1 w-full text-right">
                                            <a href="{{ $actionUrl }}" class="flex flex-col w-full">
                                                <div class="flex gap-2 justify-between items-start w-full">
                                                    <p
                                                        class="text-sm font-headline font-bold {{ $isUnread ? 'text-slate-800 dark:text-white' : 'text-slate-600 dark:text-gray-300' }}">
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
                                                        @elseif($type == 'new_shipment')
                                                            طرد وارد 📦
                                                        @elseif($type == 'admin_status_updated')
                                                            تحديث حالة 🔄
                                                        @else
                                                            إشعار
                                                        @endif
                                                    </p>
                                                    <span
                                                        class="text-[10px] text-slate-400 font-bold whitespace-nowrap mt-0.5">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <p
                                                    class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-gray-400">
                                                    {{ $notification->data['message'] }}
                                                </p>
                                            </a>

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
                                                            class="w-full py-2 bg-slate-100 text-slate-600 dark:bg-gray-800 dark:text-gray-300 text-[10px] font-bold rounded-xl active:scale-95 transition-all">رفض</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="flex flex-col justify-center items-center py-12 text-slate-400 dark:text-gray-500">
                                    <span
                                        class="mb-2 text-5xl opacity-20 material-symbols-outlined">notifications_off</span>
                                    <p class="text-xs font-bold">لا توجد إشعارات حالياً</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div> --}}
                <div class="flex gap-1 items-center sm:gap-2">
                    <div class="relative">
                        <button @click="notifOpen = !notifOpen; profileOpen = false"
                            class="flex relative justify-center items-center w-11 h-11 text-gray-500 rounded-full border border-gray-200 transition-all hover:bg-gray-100 hover:text-primary dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                            <span class="material-symbols-outlined" data-icon="notifications">notifications</span>

                            {{-- تحديث هنا: عرض الرقم بدلاً من النقطة --}}
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

                            <div
                                class="flex justify-between items-center px-5 py-4 border-b border-slate-100/50 bg-slate-50/30">
                                <div class="flex gap-2 items-center">
                                    <h3 class="text-lg font-bold font-headline text-slate-800">الإشعارات</h3>
                                    @if (auth()->user()->unreadNotifications->count() > 0)
                                        <span
                                            class="bg-rose-50 text-rose-500 text-[10px] font-bold px-2 py-0.5 rounded-full">
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
                                    // 1. جلب أحدث 15 إشعار للتعامل معها
                                    $allNotifications = auth()->user()->notifications()->take(15)->get();

                                    // 2. فصل الإشعارات غير المقروءة والمقروءة
                                    $unreadNotifications = $allNotifications->whereNull('read_at');
                                    $readNotifications = $allNotifications->whereNotNull('read_at');

                                    // 3. اللوجيك الذكي للعدد:
                                    if ($unreadNotifications->count() >= 3) {
                                        // إذا كان هناك 3 أو أكثر غير مقروءة، نعرضها جميعاً
                                        $displayNotifications = $unreadNotifications;
                                    } else {
                                        // إذا كان غير المقروء أقل من 3 (مثلاً 1)، نحسب كم نحتاج لنصل إلى 3 (نحتاج 2)
                                        $neededReadCount = 3 - $unreadNotifications->count();

                                        // ندمج غير المقروء مع العدد المطلوب من المقروء
                                        $displayNotifications = $unreadNotifications->concat(
                                            $readNotifications->take($neededReadCount),
                                        );
                                    }
                                @endphp

                                @forelse($displayNotifications as $notification)
                                    @php
                                        $type = $notification->data['type'] ?? '';
                                        $isUnread = $notification->unread();

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
                                            <div class="absolute right-0 top-3 bottom-3 w-1 rounded-l-full bg-primary">
                                            </div>
                                        @endif

                                        <div class="flex gap-4 items-start p-4">
                                            {{-- تحديد ألوان وخلفية الأيقونة حسب نوع الإشعار --}}
                                            <div
                                                class="w-11 h-11 shrink-0 rounded-2xl flex items-center justify-center shadow-sm border transition-transform group-hover:scale-105
                @if ($type == 'connection_request') bg-blue-50 text-blue-500 border-blue-100
                @elseif($type == 'connection_accepted') bg-emerald-50 text-emerald-500 border-emerald-100
                @elseif($type == 'connection_rejected') bg-rose-50 text-rose-500 border-rose-100
                @elseif($type == 'shipment_dispatched') bg-amber-50 text-amber-500 border-amber-100
                @elseif($type == 'admin_new_shipment') bg-purple-50 text-purple-600 border-purple-100
                @elseif($type == 'admin_new_manifest') bg-teal-50 text-teal-600 border-teal-100
                @elseif($type == 'new_shipment') bg-indigo-50 text-indigo-500 border-indigo-100
                @elseif($type == 'admin_status_updated') bg-cyan-50 text-cyan-600 border-cyan-100
                @else bg-slate-100 text-slate-500 border-slate-200 @endif">

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
                                                        {{ $notification->data['icon'] ?? 'update' }}
                                                    @else
                                                        notifications
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="flex relative flex-col gap-1 w-full text-right">
                                                <a href="{{ $actionUrl }}" class="flex flex-col w-full">
                                                    <div class="flex gap-2 justify-between items-start w-full">
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
                                                            @else
                                                                إشعار
                                                            @endif
                                                        </p>
                                                        <span
                                                            class="text-[10px] text-slate-400 font-bold whitespace-nowrap mt-0.5">
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
                                        <span
                                            class="mb-2 text-5xl opacity-20 material-symbols-outlined">notifications_off</span>
                                        <p class="text-xs font-bold">لا توجد إشعارات حالياً</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden w-px h-8 bg-gray-200 lg:block dark:bg-gray-700"></div>
                <span class="font-medium text-gray-600 text-theme-sm dark:text-bodydark">
                    {{ Auth::user()->branch?->name }}
                </span>
            </div>

            <div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
                <button @click.prevent="dropdownOpen = !dropdownOpen; notifOpen = false"
                    class="flex gap-2 items-center p-1 rounded-lg transition-colors group hover:bg-gray-50 dark:hover:bg-white/5">

                    <div
                        class="flex justify-center items-center w-10 h-10 text-gray-400 bg-gray-50 rounded-full border border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                        <span class="material-symbols-outlined">person</span>
                    </div>


                    <div class="hidden text-right lg:block">
                        <p class="font-semibold text-gray-800 text-theme-sm dark:text-white">{{ Auth::user()->name }}
                        </p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ Auth::user()->phone }}</p>
                    </div>

                    <svg :class="dropdownOpen && 'rotate-180'"
                        class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-cloak x-show="dropdownOpen" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    class="absolute {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} mt-3 w-64 origin-top-right rounded-2xl border border-gray-200 bg-white p-2 shadow-xl dark:border-gray-800 dark:bg-boxdark">

                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ Auth::user()->email ?? Auth::user()->phone }}</p>
                    </div>
                    @if (Auth::user()->type === 'admin')
                        <a href="{{ route('app.settings') }}"
                            class="flex gap-3 items-center p-3 text-sm font-bold rounded-2xl transition-colors hover:bg-slate-50 text-slate-600 hover:text-primary font-headline active:scale-95">
                            <span
                                class="material-symbols-outlined text-[20px] bg-slate-100 p-1.5 rounded-lg">settings</span>
                            إعدادات الشركة
                        </a>
                    @endif

                    <div class="p-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex gap-3 items-center px-4 py-2.5 w-full text-sm font-medium text-red-500 rounded-xl transition-colors hover:bg-red-50 dark:hover:bg-red-500/10">
                                <span class="text-xl text-red-500 material-symbols-outlined">logout</span>
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>

<script>
    function backupManager() {
        return {
            isBackingUp: false,
            toastVisible: false,
            toastMessage: '',
            async performBackup() {
                if (this.isBackingUp) return;
                this.isBackingUp = true;
                try {
                    const response = await fetch('{{ route('backup.upload') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    });
                    const data = await response.json();
                    this.toastMessage = data.status ? 'تم رفع النسخة الاحتياطية بنجاح' :
                        'فشل رفع النسخة الاحتياطية';
                } catch (error) {
                    this.toastMessage = 'حدث خطأ في الاتصال';
                } finally {
                    this.isBackingUp = false;
                    this.toastVisible = true;
                    setTimeout(() => this.toastVisible = false, 4000);
                }
            }
        }
    }
</script>
