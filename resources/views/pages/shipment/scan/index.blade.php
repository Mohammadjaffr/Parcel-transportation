@extends('layouts.app')

@section('title', 'استلام الشحنات بالباركود')

@section('Breadcrumb', 'الطرود المستلمة / استلام بالباركود')

@section('content')

<div
    x-data="shipmentBarcodeScanner()"
    x-init="init()"
    @keydown.escape.window="closeCamera()"
    class="pb-24 min-h-screen font-body lg:pb-12"
    dir="rtl"
>

    {{-- ========================================================= --}}
    {{-- Header --}}
    {{-- ========================================================= --}}

    <div class="mx-auto w-full max-w-7xl">

        <div
            class="flex flex-col gap-4 justify-between items-start mb-6  md:flex-row md:items-center"
        >

            <div>

                <h1
                    class="text-2xl font-black  md:text-3xl font-headline text-on-surface dark:text-white"
                >
                    استلام الشحنات بالباركود
                </h1>

                <p
                    class="mt-1 text-sm font-bold text-gray-500  dark:text-bodydark"
                >
                    امسح باركود سند الطرد لتسجيل وصوله إلى الفرع
                </p>

            </div>


            {{-- رجوع إلى الطرود الواردة --}}
            <a
                href="{{ route('shipment.incoming.index') }}"
                class="flex gap-2 items-center px-5 h-11 text-xs font-black text-gray-700 bg-white rounded-xl border border-gray-100 shadow-sm transition-all  dark:bg-boxdark dark:text-white dark:border-boxdark-2 hover:border-primary/40 hover:text-primary active:scale-95"
            >

                <span class="material-symbols-outlined text-[18px]">
                    arrow_forward
                </span>

                رجوع

            </a>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- Main --}}
    {{-- ========================================================= --}}

    <div
        class="grid grid-cols-1 gap-6 mx-auto max-w-7xl  lg:grid-cols-12"
    >


        {{-- ===================================================== --}}
        {{-- Scanner Card --}}
        {{-- ===================================================== --}}

        <div class="lg:col-span-5">

            <div
                class="
                    relative
                    overflow-hidden
                    p-6 md:p-8

                    bg-white
                    dark:bg-boxdark

                    rounded-[2rem]

                    border
                    border-gray-100
                    dark:border-boxdark-2

                    shadow-sm
                "
            >

                {{-- Decoration --}}
                <div
                    class="
                        absolute
                        top-0 right-0
                        w-40 h-40
                        rounded-bl-[120px]
                        bg-primary/5
                        dark:bg-primary/10
                        pointer-events-none
                    "
                ></div>


                <div class="relative z-10">


                    {{-- Header Scanner --}}

                    <div class="flex gap-4 items-center mb-8">

                        <div
                            class="flex justify-center items-center w-14 h-14 rounded-2xl  bg-primary/10 text-primary shrink-0"
                        >

                            <span class="material-symbols-outlined text-[30px]">
                                barcode_scanner
                            </span>

                        </div>


                        <div>

                            <h2
                                class="text-xl font-black  font-headline text-on-surface dark:text-white"
                            >
                                قارئ السند
                            </h2>

                            <p
                                class="mt-1 text-xs font-bold text-gray-500  dark:text-bodydark"
                            >
                                Barcode / إدخال يدوي
                            </p>

                        </div>

                    </div>



                    {{-- ================================================= --}}
                    {{-- Barcode Input --}}
                    {{-- ================================================= --}}

                    <div>

                        <label
                            class="block mb-2 text-xs font-bold text-gray-600  dark:text-gray-300"
                        >
                            رقم السند
                        </label>


                        <div class="relative">

                            <span
                                class="
                                    absolute
                                    right-4
                                    top-1/2
                                    -translate-y-1/2
                                    material-symbols-outlined
                                    text-[22px]
                                    text-gray-400
                                "
                            >
                                barcode
                            </span>


                            <input
                                x-ref="scanInput"

                                type="text"

                                x-model="bondNumber"

                                @keydown.enter.prevent="receiveShipment()"

                                @input="clearMessagesOnly()"

                                autocomplete="off"

                                autocapitalize="characters"

                                spellcheck="false"

                                placeholder="SNA-260919001"

                                class="pr-12 pl-12 w-full h-16 font-mono text-lg font-black text-center uppercase rounded-2xl border border-gray-200 transition-all outline-none  bg-surface text-on-surface dark:bg-boxdark-2 dark:border-boxdark dark:text-white focus:bg-white dark:focus:bg-boxdark focus:border-primary focus:ring-4 focus:ring-primary/10"

                                dir="ltr"
                            >


                            {{-- Clear --}}

                            <button
                                x-show="bondNumber.length > 0"
                                x-cloak

                                type="button"

                                @click="resetScanner()"

                                class="flex absolute left-3 top-1/2 justify-center items-center w-9 h-9 text-gray-400 rounded-xl transition-all -translate-y-1/2  hover:text-error hover:bg-red-50"
                            >

                                <span class="material-symbols-outlined text-[18px]">
                                    close
                                </span>

                            </button>

                        </div>


                        <p
                            class="
                                mt-2
                                px-1
                                text-[10px]
                                font-bold
                                text-gray-400
                            "
                        >
                            Scanner USB يكتب رقم السند تلقائياً ثم ينفذ الاستلام عند Enter.
                        </p>

                    </div>



                    {{-- ================================================= --}}
                    {{-- Receive Button --}}
                    {{-- ================================================= --}}

                    <button
                        type="button"

                        @click="receiveShipment()"

                        :disabled="loading || !bondNumber.trim()"

                        class="
                            flex
                            gap-2
                            justify-center
                            items-center

                            mt-5

                            w-full
                            h-14

                            text-sm
                            font-black
                            text-white

                            rounded-2xl

                            bg-primary
                            hover:bg-primary-hover

                            shadow-lg
                            shadow-primary/20

                            transition-all

                            active:scale-[0.98]

                            disabled:opacity-50
                            disabled:cursor-not-allowed
                            disabled:shadow-none
                        "
                    >

                        <span
                            x-show="!loading"
                            class="material-symbols-outlined text-[21px]"
                        >
                            inventory
                        </span>


                        <span
                            x-show="loading"
                            x-cloak
                            class="
                                material-symbols-outlined
                                text-[21px]
                                animate-spin
                            "
                        >
                            progress_activity
                        </span>


                        <span x-show="!loading">
                            تسجيل استلام الشحنة
                        </span>


                        <span x-show="loading" x-cloak>
                            جاري تسجيل الاستلام...
                        </span>

                    </button>



                    {{-- Divider --}}

                    <div class="flex gap-4 items-center my-6">

                        <div
                            class="flex-1 h-px bg-gray-100  dark:bg-boxdark-2"
                        ></div>

                        <span
                            class="
                                text-[10px]
                                font-bold
                                text-gray-400
                            "
                        >
                            أو
                        </span>

                        <div
                            class="flex-1 h-px bg-gray-100  dark:bg-boxdark-2"
                        ></div>

                    </div>



                    {{-- ================================================= --}}
                    {{-- Camera --}}
                    {{-- ================================================= --}}

                    <button
                        type="button"

                        @click="openCamera()"

                        class="
                            flex
                            gap-3
                            justify-center
                            items-center

                            w-full
                            h-14

                            text-sm
                            font-black

                            rounded-2xl

                            border
                            border-primary/20

                            bg-primary/10
                            text-primary

                            hover:bg-primary
                            hover:text-white

                            transition-all

                            active:scale-[0.98]
                        "
                    >

                        <span class="material-symbols-outlined text-[24px]">
                            photo_camera
                        </span>

                        مسح باستخدام كاميرا الهاتف

                    </button>



                    {{-- USB Status --}}

                    <div
                        class="flex gap-3 items-center p-4 mt-5 rounded-2xl border border-emerald-100  bg-emerald-50/70 dark:bg-emerald-500/10 dark:border-emerald-500/20"
                    >

                        <div
                            class="flex justify-center items-center w-9 h-9 text-emerald-600 bg-emerald-100 rounded-xl  dark:bg-emerald-500/20"
                        >

                            <span class="material-symbols-outlined text-[20px]">
                                usb
                            </span>

                        </div>


                        <div>

                            <p
                                class="text-xs font-black text-emerald-700  dark:text-emerald-400"
                            >
                                Scanner USB جاهز
                            </p>

                            <p
                                class="
                                    mt-0.5
                                    text-[10px]
                                    font-bold
                                    text-emerald-600/70
                                "
                            >
                                لا يحتاج إلى تعريف أو إعداد إضافي
                            </p>

                        </div>

                    </div>


                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- Result --}}
        {{-- ===================================================== --}}

        <div class="lg:col-span-7">


            {{-- ================================================= --}}
            {{-- Initial State --}}
            {{-- ================================================= --}}

            <div
                x-show="!shipment && !errorMessage && !loading"

                class="
                    flex
                    flex-col
                    justify-center
                    items-center

                    min-h-[500px]

                    p-8

                    text-center

                    bg-white
                    dark:bg-boxdark

                    rounded-[2rem]

                    border
                    border-dashed
                    border-gray-200
                    dark:border-boxdark-2

                    shadow-sm
                "
            >

                <div
                    class="
                        flex
                        justify-center
                        items-center

                        mb-6

                        w-24
                        h-24

                        rounded-[2rem]

                        bg-slate-50
                        dark:bg-boxdark-2

                        text-slate-300
                        dark:text-bodydark
                    "
                >

                    <span class="material-symbols-outlined text-[50px]">
                        qr_code_scanner
                    </span>

                </div>


                <h3
                    class="text-xl font-black  font-headline text-slate-700 dark:text-white"
                >
                    جاهز للاستلام
                </h3>


                <p
                    class="mt-2 max-w-md text-sm font-medium leading-7  text-slate-400 dark:text-bodydark"
                >
                    امسح الباركود الموجود فوق الطرد لتسجيل وصول الشحنة إلى فرعك.
                </p>


                <div
                    class="px-4 py-2 mt-5 font-mono text-xs font-black rounded-xl  bg-slate-50 dark:bg-boxdark-2 text-slate-500"
                    dir="ltr"
                >
                    SNA-260919001
                </div>

            </div>



            {{-- ================================================= --}}
            {{-- Loading --}}
            {{-- ================================================= --}}

            <div
                x-show="loading"
                x-cloak

                class="
                    flex
                    flex-col
                    justify-center
                    items-center

                    min-h-[500px]

                    bg-white
                    dark:bg-boxdark

                    rounded-[2rem]

                    border
                    border-gray-100
                    dark:border-boxdark-2

                    shadow-sm
                "
            >

                <div
                    class="w-14 h-14 rounded-full border-4 animate-spin  border-primary/20 border-t-primary"
                ></div>


                <p
                    class="mt-5 text-sm font-black text-gray-500"
                >
                    جاري تسجيل استلام الشحنة...
                </p>

            </div>



            {{-- ================================================= --}}
            {{-- Error --}}
            {{-- ================================================= --}}

            <div
                x-show="errorMessage && !loading"
                x-cloak

                class="
                    flex
                    flex-col
                    justify-center
                    items-center

                    min-h-[500px]

                    p-8

                    text-center

                    bg-white
                    dark:bg-boxdark

                    rounded-[2rem]

                    border
                    border-red-100
                    dark:border-red-500/20

                    shadow-sm
                "
            >

                <div
                    class="
                        flex
                        justify-center
                        items-center

                        mb-5

                        w-20
                        h-20

                        rounded-[1.5rem]

                        bg-red-50
                        dark:bg-red-500/10

                        text-red-500
                    "
                >

                    <span class="material-symbols-outlined text-[42px]">
                        error
                    </span>

                </div>


                <h3
                    class="text-xl font-black text-red-600"
                >
                    تعذر تسجيل الاستلام
                </h3>


                <p
                    class="mt-3 max-w-lg text-sm font-bold leading-7 text-gray-500  dark:text-bodydark"
                    x-text="errorMessage"
                ></p>


                <button
                    type="button"

                    @click="resetScanner()"

                    class="flex gap-2 justify-center items-center px-6 mt-6 h-12 text-sm font-black text-red-600 bg-red-50 rounded-xl transition-all  hover:bg-red-500 hover:text-white"
                >

                    <span class="material-symbols-outlined text-[19px]">
                        refresh
                    </span>

                    مسح سند آخر

                </button>

            </div>



            {{-- ================================================= --}}
            {{-- Success --}}
            {{-- ================================================= --}}

            <div
                x-show="shipment && !loading"
                x-cloak

                class="
                    overflow-hidden

                    bg-white
                    dark:bg-boxdark

                    rounded-[2rem]

                    border
                    border-emerald-100
                    dark:border-emerald-500/20

                    shadow-sm
                "
            >


                {{-- Success Header --}}

                <div
                    class="flex flex-col gap-4 justify-between items-start p-6 border-b border-gray-100  dark:border-boxdark-2 md:flex-row md:items-center"
                >

                    <div class="flex gap-4 items-center">

                        <div
                            class="flex justify-center items-center w-14 h-14 text-emerald-500 bg-emerald-50 rounded-2xl  dark:bg-emerald-500/10"
                        >

                            <span class="material-symbols-outlined text-[30px]">
                                check_circle
                            </span>

                        </div>


                        <div>

                            <p
                                class="
                                    text-[10px]
                                    font-black
                                    tracking-wider
                                    text-emerald-500
                                "
                            >
                                ✓ تم استلام الشحنة بنجاح
                            </p>


                            <h2
                                class="mt-1 font-mono text-xl font-black  text-slate-800 dark:text-white"

                                x-text="shipment?.bond_number"

                                dir="ltr"
                            ></h2>

                        </div>

                    </div>



                    {{-- Status --}}

                    <span
                        class="inline-flex gap-2 items-center px-4 py-2 text-xs font-black text-emerald-600 bg-emerald-50 rounded-xl ring-1 ring-inset  ring-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400"
                    >

                        <span class="material-symbols-outlined text-[17px]">
                            inventory_2
                        </span>

                        <span
                            x-text="
                                shipment?.status_label
                                || 'مستلمة في الفرع'
                            "
                        ></span>

                    </span>

                </div>



                {{-- ================================================= --}}
                {{-- Route --}}
                {{-- ================================================= --}}

                <div
                    class="flex justify-between items-center p-4 mx-6 mt-6 rounded-2xl  bg-slate-50 dark:bg-boxdark-2"
                >

                    <div class="text-right">

                        <p
                            class="
                                text-[9px]
                                font-bold
                                text-gray-400
                            "
                        >
                            من
                        </p>

                        <p
                            class="mt-1 text-sm font-black  text-slate-800 dark:text-white"
                            x-text="shipment?.sender_branch"
                        ></p>

                    </div>


                    <div
                        class="flex justify-center items-center w-10 h-10 bg-white rounded-full border border-gray-100  dark:bg-boxdark dark:border-boxdark text-primary"
                    >

                        <span
                            class="
                                material-symbols-outlined
                                text-[20px]
                                rtl:rotate-180
                            "
                        >
                            arrow_forward
                        </span>

                    </div>


                    <div class="text-left">

                        <p
                            class="
                                text-[9px]
                                font-bold
                                text-gray-400
                            "
                        >
                            إلى
                        </p>

                        <p
                            class="mt-1 text-sm font-black  text-primary"
                            x-text="shipment?.receiver_branch"
                        ></p>

                    </div>

                </div>



                {{-- ================================================= --}}
                {{-- Sender / Receiver --}}
                {{-- ================================================= --}}

                <div
                    class="grid grid-cols-1 gap-4 p-6  md:grid-cols-2"
                >


                    {{-- Sender --}}

                    <div
                        class="overflow-hidden relative p-5 rounded-2xl border border-emerald-100  bg-emerald-50/50 dark:bg-emerald-500/5 dark:border-emerald-500/20"
                    >

                        <div
                            class="absolute top-0 right-0 w-1 h-full bg-emerald-500"
                        ></div>


                        <div
                            class="flex gap-3 items-center mb-5"
                        >

                            <div
                                class="flex justify-center items-center w-9 h-9 text-emerald-600 bg-emerald-100 rounded-xl"
                            >

                                <span class="material-symbols-outlined text-[20px]">
                                    person
                                </span>

                            </div>


                            <div>

                                <p
                                    class="
                                        text-[10px]
                                        font-black
                                        text-emerald-600
                                    "
                                >
                                    المُرسل
                                </p>

                                <p
                                    class="text-sm font-black  text-slate-800 dark:text-white"
                                    x-text="shipment?.sender?.name"
                                ></p>

                            </div>

                        </div>


                        <div>

                            <p
                                class="
                                    text-[9px]
                                    font-bold
                                    text-gray-400
                                "
                            >
                                رقم الهاتف
                            </p>

                            <p
                                class="mt-0.5 text-sm font-black text-gray-700  dark:text-gray-200"
                                x-text="shipment?.sender?.phone"
                                dir="ltr"
                            ></p>

                        </div>

                    </div>



                    {{-- Receiver --}}

                    <div
                        class="overflow-hidden relative p-5 rounded-2xl border border-blue-100  bg-blue-50/50 dark:bg-blue-500/5 dark:border-blue-500/20"
                    >

                        <div
                            class="absolute top-0 right-0 w-1 h-full bg-blue-500"
                        ></div>


                        <div
                            class="flex gap-3 items-center mb-5"
                        >

                            <div
                                class="flex justify-center items-center w-9 h-9 text-blue-600 bg-blue-100 rounded-xl"
                            >

                                <span class="material-symbols-outlined text-[20px]">
                                    person_pin_circle
                                </span>

                            </div>


                            <div>

                                <p
                                    class="
                                        text-[10px]
                                        font-black
                                        text-blue-600
                                    "
                                >
                                    المُستلم
                                </p>

                                <p
                                    class="text-sm font-black  text-slate-800 dark:text-white"
                                    x-text="shipment?.receiver?.name"
                                ></p>

                            </div>

                        </div>


                        <div>

                            <p
                                class="
                                    text-[9px]
                                    font-bold
                                    text-gray-400
                                "
                            >
                                رقم الهاتف
                            </p>

                            <p
                                class="mt-0.5 text-sm font-black text-gray-700  dark:text-gray-200"
                                x-text="shipment?.receiver?.phone"
                                dir="ltr"
                            ></p>

                        </div>

                    </div>

                </div>



                {{-- Package Type --}}

                <div class="px-6 pb-6">

                    <div
                        class="flex gap-4 items-center p-4 rounded-2xl  bg-slate-50 dark:bg-boxdark-2"
                    >

                        <div
                            class="flex justify-center items-center w-10 h-10 rounded-xl  bg-primary/10 text-primary"
                        >

                            <span class="material-symbols-outlined text-[21px]">
                                inventory_2
                            </span>

                        </div>


                        <div>

                            <p
                                class="
                                    text-[9px]
                                    font-bold
                                    text-gray-400
                                "
                            >
                                نوع الشحنة
                            </p>

                            <p
                                class="mt-0.5 text-sm font-black  text-slate-800 dark:text-white"
                                x-text="shipment?.package_type || 'طرد'"
                            ></p>

                        </div>

                    </div>

                </div>



                {{-- ================================================= --}}
                {{-- Actions --}}
                {{-- ================================================= --}}

                <div
                    class="flex flex-col gap-3 p-6 border-t border-gray-100  bg-slate-50/70 dark:bg-boxdark-2/50 dark:border-boxdark-2 sm:flex-row"
                >


                    <a
                        :href="shipment?.details_url || '#'"
                        class="
                            flex
                            flex-1
                            gap-2
                            justify-center
                            items-center

                            h-12

                            text-sm
                            font-black
                            text-white

                            rounded-xl

                            bg-primary
                            hover:bg-primary-hover

                            shadow-md
                            shadow-primary/20

                            transition-all

                            active:scale-[0.98]
                        "
                    >

                        <span class="material-symbols-outlined text-[20px]">
                            visibility
                        </span>

                        عرض تفاصيل الشحنة

                    </a>



                    <button
                        type="button"

                        @click="resetScanner()"

                        class="
                            flex
                            flex-1
                            gap-2
                            justify-center
                            items-center

                            h-12

                            text-sm
                            font-black

                            rounded-xl

                            border
                            border-gray-200

                            bg-white
                            text-gray-700

                            dark:bg-boxdark
                            dark:border-boxdark
                            dark:text-white

                            transition-all

                            hover:border-primary/40
                            hover:text-primary

                            active:scale-[0.98]
                        "
                    >

                        <span class="material-symbols-outlined text-[20px]">
                            barcode_scanner
                        </span>

                        مسح سند آخر

                    </button>

                </div>

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- Camera Modal --}}
    {{-- ========================================================= --}}

    <div
        x-show="cameraOpen"
        x-cloak

        class="
            fixed
            inset-0
            z-[99999]

            flex
            items-end
            justify-center

            sm:items-center
            sm:p-4
        "
    >


        {{-- Overlay --}}

        <div
            x-show="cameraOpen"

            x-transition.opacity.duration.300ms

            @click="closeCamera()"

            class="fixed inset-0 backdrop-blur-sm  bg-slate-900/70"
        ></div>



        {{-- Modal --}}

        <div
            x-show="cameraOpen"

            x-transition:enter="
                transition ease-out duration-300
            "

            x-transition:enter-start="
                opacity-0
                translate-y-full
                sm:translate-y-4
                sm:scale-95
            "

            x-transition:enter-end="
                opacity-100
                translate-y-0
                sm:scale-100
            "

            x-transition:leave="
                transition ease-in duration-200
            "

            x-transition:leave-start="
                opacity-100
                translate-y-0
                sm:scale-100
            "

            x-transition:leave-end="
                opacity-0
                translate-y-full
                sm:translate-y-4
                sm:scale-95
            "

            class="
                relative

                w-full
                max-w-lg

                p-6

                bg-white
                dark:bg-boxdark

                rounded-t-[2.5rem]
                sm:rounded-[2rem]

                shadow-2xl
            "
        >


            {{-- Mobile handle --}}

            <div
                class="mx-auto mb-6 w-12 h-1.5 rounded-full  bg-slate-200 sm:hidden"
            ></div>



            {{-- Header --}}

            <div
                class="flex justify-between items-start mb-5"
            >

                <div class="flex gap-3 items-center">

                    <div
                        class="flex justify-center items-center w-11 h-11 rounded-xl  bg-primary/10 text-primary"
                    >

                        <span class="material-symbols-outlined text-[24px]">
                            photo_camera
                        </span>

                    </div>


                    <div>

                        <h3
                            class="text-lg font-black  font-headline text-slate-800 dark:text-white"
                        >
                            مسح باركود الشحنة
                        </h3>

                        <p
                            class="
                                mt-0.5
                                text-[10px]
                                font-bold
                                text-gray-400
                            "
                        >
                            وجّه الكاميرا نحو الباركود الموجود على السند
                        </p>

                    </div>

                </div>



                <button
                    type="button"

                    @click="closeCamera()"

                    class="flex justify-center items-center w-9 h-9 rounded-xl  bg-slate-100 text-slate-500 dark:bg-boxdark-2 dark:text-bodydark hover:text-error"
                >

                    <span class="material-symbols-outlined text-[20px]">
                        close
                    </span>

                </button>

            </div>



            {{-- Camera --}}

            <div
                class="
                    relative
                    overflow-hidden

                    min-h-[260px]

                    rounded-[1.5rem]

                    bg-black

                    border-4
                    border-slate-100
                    dark:border-boxdark-2
                "
            >

                <div
                    id="shipment-camera-reader"

                    class="
                        overflow-hidden
                        w-full
                        min-h-[260px]
                        bg-black
                    "
                ></div>



                {{-- Scan frame --}}

                <div
                    class="flex absolute inset-0 justify-center items-center pointer-events-none"
                >

                    <div
                        class="
                            relative

                            w-[82%]
                            h-28

                            rounded-2xl

                            border-2
                            border-white/80

                            shadow-[0_0_0_9999px_rgba(0,0,0,0.25)]
                        "
                    >

                        <div
                            class="
                                absolute

                                left-3
                                right-3
                                top-1/2

                                h-0.5

                                bg-red-500

                                shadow-[0_0_12px_rgba(239,68,68,0.9)]

                                animate-pulse
                            "
                        ></div>

                    </div>

                </div>

            </div>



            {{-- Camera Error --}}

            <div
                x-show="cameraError"
                x-cloak

                class="p-3 mt-4 text-xs font-bold text-red-600 bg-red-50 rounded-xl border border-red-100  dark:bg-red-500/10 dark:border-red-500/20"

                x-text="cameraError"
            ></div>



            <p
                class="mt-4 text-xs font-bold text-center text-gray-400"
            >
                ضع خطوط الباركود داخل الإطار حتى تتم قراءته تلقائياً
            </p>



            <button
                type="button"

                @click="closeCamera()"

                class="
                    flex
                    gap-2
                    justify-center
                    items-center

                    mt-5

                    w-full
                    h-12

                    text-sm
                    font-black

                    rounded-xl

                    bg-slate-100
                    text-slate-600

                    dark:bg-boxdark-2
                    dark:text-gray-300

                    transition-all

                    active:scale-[0.98]
                "
            >

                <span class="material-symbols-outlined text-[18px]">
                    close
                </span>

                إلغاء المسح

            </button>


        </div>

    </div>


</div>

@endsection



@section('script')

{{-- ============================================================= --}}
{{-- Barcode Camera Library --}}
{{-- ============================================================= --}}

<script
    src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js">
</script>



<script>

    function shipmentBarcodeScanner() {

        return {

            /*
            |--------------------------------------------------------------------------
            | State
            |--------------------------------------------------------------------------
            */

            bondNumber: '',

            shipment: null,

            loading: false,

            errorMessage: '',

            cameraOpen: false,

            cameraError: '',

            qrScanner: null,

            cameraProcessing: false,

            lastScannedCode: '',

            lastScannedAt: 0,



            /*
            |--------------------------------------------------------------------------
            | Init
            |--------------------------------------------------------------------------
            */

            init() {

                this.$nextTick(() => {

                    this.focusScannerInput();

                });

            },



            /*
            |--------------------------------------------------------------------------
            | Focus
            |--------------------------------------------------------------------------
            */

            focusScannerInput() {

                setTimeout(() => {

                    if (
                        this.$refs.scanInput
                        &&
                        !this.cameraOpen
                    ) {

                        this.$refs.scanInput.focus();

                    }

                }, 150);

            },



            /*
            |--------------------------------------------------------------------------
            | Normalize barcode
            |--------------------------------------------------------------------------
            */

            normalizeBarcode(value) {

                return String(value || '')
                    .trim()
                    .replace(/\r/g, '')
                    .replace(/\n/g, '')
                    .replace(/\t/g, '')
                    .toUpperCase();

            },



            /*
            |--------------------------------------------------------------------------
            | Receive Shipment
            |--------------------------------------------------------------------------
            */

            async receiveShipment() {

                if (this.loading) {
                    return;
                }


                const code =
                    this.normalizeBarcode(
                        this.bondNumber
                    );


                if (!code) {

                    this.errorMessage =
                        'يرجى مسح الباركود أو كتابة رقم السند.';

                    this.focusScannerInput();

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | منع المسح المكرر السريع
                |--------------------------------------------------------------------------
                */

                const now = Date.now();


                if (
                    this.lastScannedCode === code
                    &&
                    now - this.lastScannedAt < 1200
                ) {

                    return;

                }


                this.lastScannedCode =
                    code;


                this.lastScannedAt =
                    now;


                this.bondNumber =
                    code;


                this.loading =
                    true;


                this.shipment =
                    null;


                this.errorMessage =
                    '';


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | IMPORTANT:
                    | هذا هو Route الاستلام وليس Route البحث القديم
                    |--------------------------------------------------------------------------
                    */

                    const response = await fetch(

                        '{{ route('shipment.incoming.scan.receive') }}',

                        {

                            method: 'POST',

                            headers: {

                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    '{{ csrf_token() }}',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                            },

                            body: JSON.stringify({

                                bond_number:
                                    code,

                            }),

                        }

                    );


                    /*
                    |--------------------------------------------------------------------------
                    | محاولة قراءة JSON
                    |--------------------------------------------------------------------------
                    */

                    let result = null;


                    try {

                        result =
                            await response.json();

                    }
                    catch (jsonError) {

                        throw new Error(
                            'استجابة الخادم غير صحيحة. تحقق من سجل Laravel.'
                        );

                    }



                    /*
                    |--------------------------------------------------------------------------
                    | Validation errors
                    |--------------------------------------------------------------------------
                    */

                    if (
                        response.status === 422
                        &&
                        result.errors
                    ) {

                        const messages =
                            Object.values(
                                result.errors
                            ).flat();


                        throw new Error(
                            messages[0]
                            || 'البيانات المدخلة غير صحيحة.'
                        );

                    }



                    /*
                    |--------------------------------------------------------------------------
                    | أي خطأ آخر
                    |--------------------------------------------------------------------------
                    */

                    if (!response.ok) {

                        throw new Error(
                            result.message
                            || 'تعذر تسجيل استلام الشحنة.'
                        );

                    }



                    /*
                    |--------------------------------------------------------------------------
                    | Success
                    |--------------------------------------------------------------------------
                    */

                    this.shipment =
                        result.shipment;


                    this.errorMessage =
                        '';


                    /*
                    |--------------------------------------------------------------------------
                    | صوت + اهتزاز
                    |--------------------------------------------------------------------------
                    */

                    this.successFeedback();


                    /*
                    |--------------------------------------------------------------------------
                    | إفراغ حقل المسح استعداداً للطرد التالي
                    |--------------------------------------------------------------------------
                    |
                    | بيانات shipment ستبقى ظاهرة على اليسار.
                    |--------------------------------------------------------------------------
                    */

                    this.bondNumber =
                        '';


                    this.lastScannedCode =
                        '';


                    this.lastScannedAt =
                        0;

                }
                catch (error) {

                    console.error(
                        'Shipment receive error:',
                        error
                    );


                    this.shipment =
                        null;


                    this.errorMessage =
                        error.message
                        ||
                        'حدث خطأ أثناء تسجيل استلام الشحنة.';

                }
                finally {

                    this.loading =
                        false;


                    this.focusScannerInput();

                }

            },



            /*
            |--------------------------------------------------------------------------
            | Open Camera
            |--------------------------------------------------------------------------
            */

            async openCamera() {

                this.cameraError =
                    '';


                this.cameraProcessing =
                    false;


                this.cameraOpen =
                    true;


                await this.$nextTick();



                /*
                |--------------------------------------------------------------------------
                | تحقق من المكتبة
                |--------------------------------------------------------------------------
                */

                if (
                    typeof Html5Qrcode ===
                    'undefined'
                ) {

                    this.cameraError =
                        'تعذر تحميل مكتبة قراءة الباركود. تحقق من اتصال الإنترنت.';

                    return;

                }


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | إنشاء Scanner
                    |--------------------------------------------------------------------------
                    */

                    this.qrScanner =
                        new Html5Qrcode(

                            'shipment-camera-reader',

                            {

                                formatsToSupport: [

                                    Html5QrcodeSupportedFormats
                                        .CODE_128,

                                ],

                                verbose: false,

                            }

                        );



                    /*
                    |--------------------------------------------------------------------------
                    | تشغيل الكاميرا الخلفية
                    |--------------------------------------------------------------------------
                    */

                    await this.qrScanner.start(

                        {
                            facingMode:
                                'environment',
                        },

                        {

                            fps: 10,

                            qrbox: {
                                width: 280,
                                height: 110,
                            },

                            aspectRatio:
                                1.777778,

                        },



                        /*
                        |--------------------------------------------------------------------------
                        | تم العثور على Barcode
                        |--------------------------------------------------------------------------
                        */

                        async (
                            decodedText,
                            decodedResult
                        ) => {

                            if (
                                this.cameraProcessing
                            ) {

                                return;

                            }


                            this.cameraProcessing =
                                true;


                            const code =
                                this.normalizeBarcode(
                                    decodedText
                                );


                            if (!code) {

                                this.cameraProcessing =
                                    false;

                                return;

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | وضع الرقم
                            |--------------------------------------------------------------------------
                            */

                            this.bondNumber =
                                code;


                            /*
                            |--------------------------------------------------------------------------
                            | إيقاف الكاميرا
                            |--------------------------------------------------------------------------
                            */

                            await this.stopCamera();


                            this.cameraOpen =
                                false;


                            /*
                            |--------------------------------------------------------------------------
                            | تنفيذ الاستلام مباشرة
                            |--------------------------------------------------------------------------
                            */

                            await this.receiveShipment();


                            this.cameraProcessing =
                                false;

                        },



                        /*
                        |--------------------------------------------------------------------------
                        | أخطاء Frames العادية
                        |--------------------------------------------------------------------------
                        */

                        () => {

                            // لا نظهر خطأ في كل Frame.

                        }

                    );

                }
                catch (error) {

                    console.error(
                        'Camera error:',
                        error
                    );


                    this.cameraError =
                        this.cameraErrorMessage(
                            error
                        );

                }

            },



            /*
            |--------------------------------------------------------------------------
            | Stop Camera
            |--------------------------------------------------------------------------
            */

            async stopCamera() {

                if (!this.qrScanner) {

                    return;

                }


                try {

                    await this.qrScanner.stop();

                }
                catch (error) {

                    // الكاميرا قد تكون متوقفة بالفعل.

                }


                try {

                    this.qrScanner.clear();

                }
                catch (error) {

                    // Ignore clear error.

                }


                this.qrScanner =
                    null;

            },



            /*
            |--------------------------------------------------------------------------
            | Close Camera
            |--------------------------------------------------------------------------
            */

            async closeCamera() {

                await this.stopCamera();


                this.cameraOpen =
                    false;


                this.cameraError =
                    '';


                this.cameraProcessing =
                    false;


                this.focusScannerInput();

            },



            /*
            |--------------------------------------------------------------------------
            | Camera Error Message
            |--------------------------------------------------------------------------
            */

            cameraErrorMessage(error) {

                const message =
                    String(
                        error?.message
                        ||
                        error
                        ||
                        ''
                    );


                if (
                    message.includes(
                        'Permission'
                    )
                    ||
                    message.includes(
                        'NotAllowedError'
                    )
                ) {

                    return 'تم رفض إذن الكاميرا. اسمح للموقع باستخدام الكاميرا ثم حاول مرة أخرى.';

                }


                if (
                    message.includes(
                        'NotFoundError'
                    )
                ) {

                    return 'لم يتم العثور على كاميرا في هذا الجهاز.';

                }


                if (
                    message.includes(
                        'NotReadableError'
                    )
                ) {

                    return 'الكاميرا مستخدمة بواسطة برنامج آخر. أغلق البرنامج ثم حاول مرة أخرى.';

                }


                if (
                    !window.isSecureContext
                ) {

                    return 'الكاميرا تحتاج إلى اتصال HTTPS عند تشغيل النظام على السيرفر.';

                }


                return 'تعذر تشغيل الكاميرا. تأكد من منح إذن الكاميرا ثم حاول مرة أخرى.';

            },



            /*
            |--------------------------------------------------------------------------
            | Success Feedback
            |--------------------------------------------------------------------------
            */

            successFeedback() {

                /*
                |--------------------------------------------------------------------------
                | اهتزاز الهاتف
                |--------------------------------------------------------------------------
                */

                if (
                    navigator.vibrate
                ) {

                    navigator.vibrate(
                        [100, 50, 100]
                    );

                }



                /*
                |--------------------------------------------------------------------------
                | صوت نجاح
                |--------------------------------------------------------------------------
                */

                try {

                    const AudioContext =
                        window.AudioContext
                        ||
                        window.webkitAudioContext;


                    if (!AudioContext) {
                        return;
                    }


                    const audioContext =
                        new AudioContext();


                    const oscillator =
                        audioContext
                            .createOscillator();


                    const gain =
                        audioContext
                            .createGain();


                    oscillator.connect(
                        gain
                    );


                    gain.connect(
                        audioContext.destination
                    );


                    oscillator.type =
                        'sine';


                    oscillator.frequency.value =
                        900;


                    gain.gain.value =
                        0.05;


                    oscillator.start();


                    oscillator.stop(
                        audioContext.currentTime
                        +
                        0.10
                    );

                }
                catch (error) {

                    // الصوت اختياري.

                }

            },



            /*
            |--------------------------------------------------------------------------
            | Clear Error
            |--------------------------------------------------------------------------
            */

            clearMessagesOnly() {

                this.errorMessage =
                    '';

            },



            /*
            |--------------------------------------------------------------------------
            | Reset
            |--------------------------------------------------------------------------
            */

            resetScanner() {

                this.bondNumber =
                    '';


                this.shipment =
                    null;


                this.errorMessage =
                    '';


                this.lastScannedCode =
                    '';


                this.lastScannedAt =
                    0;


                this.focusScannerInput();

            },

        };

    }

</script>

@endsection