@php
    $primaryColor = !empty($design['primary_color'])
        ? $design['primary_color']
        : (!empty($primary)
            ? $primary
            : '#059669');
    $branches = $branch_addresses ?? ($company['branch_addresses'] ?? []);
    if (empty($branches) && !empty($company['main_branch'])) {
        $branches = [
            [
                'name' => $company['main_branch']['title'] ?? 'المركز الرئيسي',
                'address' => '',
                'phone' => $company['main_branch']['phones'] ?? ($company['phone'] ?? ''),
            ],
        ];
    }
    $senderBranchDisplay =
        $sender_branch ??
        ($package_sender_branch ?? ($user_branch ?? ($company['main_branch']['title'] ?? 'الفرع الرئيسي')));
    $manifestNumber = $package_number ?? ($trip_number ?? ($bond_number ?? ($manifest_number ?? ($id ?? '---'))));
@endphp

<div class="manifest-header-wrapper"
    style="width: 100%; border-bottom: 1.5px solid #cbd5e1; padding-bottom: 3mm; margin-bottom: 3.5mm;">
    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
        <tr>
            {{-- العمود الأيمن (عرض 42% - بيانات الشركة وجميع الفروع) --}}
            <td style="width: 42%; vertical-align: top; text-align: right; padding-left: 6px;">
                {{-- اسم الشركة عريض وكبير --}}
                <div style="margin-bottom: 3px;">
                    <h1
                        style="font-size: 15pt; font-weight: 900; color: #0f172a; margin: 0; padding: 0; line-height: 1.15;">
                        {{ $company['name'] ?? 'شركة النقل' }}
                    </h1>
                    <h1
                        style="font-size: 10pt; font-weight: 900; color: #0f172a; margin: 0; padding: 0; line-height: 1;">
                        لخدمات النقل البري وشحن الطرود والبضائع </h1>
                </div>

                {{-- قائمة الفروع والعناوين مكدسة ومضغوطة --}}
                <div style="margin-top: 3px; display: flex; flex-direction: column; gap: 2px;">
                    @if (!empty($branches) && count($branches) > 0)
                        @foreach ($branches as $b)
                            <div
                                style="font-size: 7.5pt; font-weight: 700; color: #1e293b; line-height: 1.3; display: inline; align-items: center; gap: 4px; flex-wrap: wrap;">
                                <span
                                    style="background: #0f172a; color: #ffffff; padding: 1px 6px; border-radius: 3px; font-size: 7.5pt; font-weight: 800; white-space: nowrap; flex-shrink: 0;">
                                    {{ $b['name'] ?? '' }}
                                </span>
                                <span style="color: #334155; font-weight: 700; font-size: 12pt;">
                                    {{ $b['address'] ?? '' }}
                                </span>
                                @if (!empty($b['phone']))
                                    <span style="color: #64748b; font-size: 7.5pt; font-weight: 800;">
                                        - هاتف: <span dir="ltr"
                                            style="font-family: sans-serif; color: #0f172a; font-weight: 800;">{{ $b['phone'] }}</span>
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    @elseif(!empty($company['headquarters']))
                        <div style="font-size: 7.5pt; font-weight: 700; color: #1e293b; line-height: 1.3;">
                            <span
                                style="background: #0f172a; color: #ffffff; padding: 1px 6px; border-radius: 3px; font-size: 7.5pt; font-weight: 800;">
                                الإدارة العامة
                            </span>
                            @if (!empty($company['headquarters']['phones']))
                                <span style="color: #475569; margin-right: 4px;"> - هاتف: <span dir="ltr"
                                        style="font-family: sans-serif; font-weight: 800; color: #0f172a;">{{ $company['headquarters']['phones'] }}</span></span>
                            @endif
                        </div>
                    @endif
                </div>


            </td>

            {{-- العمود الأوسط (عرض 20% - الشعار وعنوان الكشف) --}}
            <td style="width: 20%; vertical-align: middle; text-align: center; padding: 0 4px;">
                {{-- شعار الشركة بدون خلفية أو إطار مربع --}}
                @if (!empty($company['logo']))
                    <div style="margin-bottom: 2mm; text-align: center;">
                        <img src="{{ $company['logo'] }}"
                            style="max-width: 48mm; max-height: 26mm; object-fit: contain; margin: 0 auto; display: block;"
                            alt="Logo">
                    </div>
                @else
                    <div style="height: 10mm;"></div>
                @endif

                {{-- عنوان الكشف داخل كبسولة بيضاوية بلون الهوية --}}
                <div style="text-align: center;">
                    <span
                        style="display: inline-block; background-color: {{ $primaryColor }}; color: #ffffff; border-radius: 20px; padding: 3px 16px; font-weight: 900; font-size: 10.5pt; white-space: nowrap; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        {{ $title ?? 'كشف إرسال شحنة' }}
                    </span>
                </div>
            </td>

            {{-- العمود الأيسر (عرض 38% - بيانات السائق/الكشف ورقم الكشف بالأحمر) --}}
            <td style="width: 38%; vertical-align: top; text-align: right; padding-right: 6px;">
                <div
                    style="display: flex; flex-direction: column; gap: 3px; font-size: 8.5pt; color: #1e293b; line-height: 1.35;">
                    {{-- كشف الشحنات / الرحلات الخاصة بسائق --}}
                    @if (isset($driver_name) && $driver_name !== '---')
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dotted #475569; padding-bottom: 2px;font-size: 12pt;">
                            <div>
                                <span style="font-weight: 800; color: #475569;">اسم السائق : </span>
                                <span style="font-weight: 900; color: #0f172a;">{{ $driver_name }}</span>
                            </div>
                            @if (!empty($driver_phone) && $driver_phone !== '---')
                                <div>
                                    <span style="font-weight: 800; color: #475569;">رقمه : </span>
                                    <span dir="ltr"
                                        style="font-family: sans-serif; font-weight: 900; color: #0f172a;">{{ $driver_phone }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- نوع المركبة إن وجد --}}
                    @php
                        $vehType = $driver_vehicle_type ?? ($vehicle_type ?? null);
                    @endphp
                    @if (!empty($vehType) && $vehType !== '---')
                        <div style="border-bottom: 1px dotted #475569; padding-bottom: 2px;font-size: 12pt;">
                            <span style="font-weight: 800; color: #475569;">نوع المركبة : </span>
                            <span style="font-weight: 900; color: #0f172a;">{{ $vehType }}</span>
                        </div>
                    @endif

                    {{-- إجمالي الركاب وعدد الرحلات للكشوفات العامة --}}
                    @if (isset($total_passengers) && isset($total_count))
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dotted #475569; padding-bottom: 2px;font-size: 12pt;">
                            <div>
                                <span style="font-weight: 800; color: #475569;">إجمالي الركاب : </span>
                                <span style="font-weight: 900; color: #0f172a;">{{ $total_passengers }}</span>
                            </div>
                            <div>
                                <span style="font-weight: 800; color: #475569;">عدد المقاعد : </span>
                                <span style="font-weight: 900; color: #0f172a;">{{ $total_count }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- بيانات العميل إذا كان كشف حساب --}}
                    @if (isset($customer) && !empty($customer['name']))
                        <div
                            style="border-bottom: 1px dotted #475569; padding-bottom: 2px; display: flex; justify-content: space-between;font-size: 12pt;">
                            <div>
                                <span style="font-weight: 800; color: #475569;">اسم العميل : </span>
                                <span style="font-weight: 900; color: #0f172a;">{{ $customer['name'] }}</span>
                            </div>
                            @if (!empty($customer['phone']))
                                <div>
                                    <span style="font-weight: 800; color: #475569;">هاتفه : </span>
                                    <span dir="ltr"
                                        style="font-family: sans-serif; font-weight: 900; color: #0f172a;">{{ $customer['phone'] }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- اليوم --}}
                    <div
                        style="font-weight: 800; color: #334155; font-size: 12pt; direction: rtl; text-align: right; line-height: 1.5;">
                        اليوم :
                        <span
                            style="font-weight: 900; color: #0f172a; border-bottom: 1px dotted #475569; padding-bottom: 0.5px; margin-left: 8px;">
                            {{ $day_name ?? now()->locale('ar')->translatedFormat('l') }}
                        </span>

                        التاريخ :
                        <span dir="ltr"
                            style="font-family: sans-serif; font-weight: 900; color: #0f172a; display: inline-block;">
                            {{ trim(str_replace(['م', '  '], ['', ' '], $formatted_date ?? now()->format('Y / m / d'))) }}
                        </span>
                        <span style="font-family: sans-serif; font-weight: 900; color: #0f172a;"> م</span>

                        @if (!empty($formatted_time))
                            <span
                                style="font-size: 8.5pt; color: #64748b; margin-right: 6px; font-family: sans-serif; display: inline-block;"
                                dir="ltr">
                                ({{ $formatted_time }})
                            </span>
                        @endif
                    </div>

                    {{-- السطر الأخير: الموافق في اليمين، ورقم الكشف بالأحمر في أقصى اليسار --}}
                    <div
                        style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 1px;font-size: 12pt;">

                        {{-- شارة الفرع المصدر (في أسفل العمود الأيمن) --}}
                        <div style="margin-top: 4px;font-size: 12pt;">
                            <span
                                style="background: #e2e8f0; border: 1px solid #94a3b8; padding: 2px 16px; border-radius: 4px; font-weight: 900; font-size: 12pt;; display: inline-block; color: #0f172a; white-space: nowrap;">
                                فرع الارسال :{{ $senderBranchDisplay }}
                            </span>
                        </div>

                        <div style="text-align: left;font-size: 12pt;">
                            <span style="font-size: 12pt;; font-weight: 800; color: #475569; margin-left: 3px;">رقم
                                الكشف: </span>
                            <span
                                style="color: #b91c1c; font-size: 14pt; font-weight: 900; font-family: sans-serif; letter-spacing: 0.5px;">{{ $manifestNumber }}</span>
                        </div>

                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
