@php
    $primaryColor = !empty($design['primary_color']) ? $design['primary_color'] : (!empty($primary) ? $primary : '#059669');
    $branches = $branch_addresses ?? ($company['branch_addresses'] ?? []);
    if (empty($branches) && !empty($company['main_branch'])) {
        $branches = [
            [
                'name'    => $company['main_branch']['title'] ?? 'المركز الرئيسي',
                'address' => '',
                'phone'   => $company['main_branch']['phones'] ?? ($company['phone'] ?? ''),
            ]
        ];
    }
    $branchFirst = $branches[0] ?? null;
    $otherBranches = array_slice($branches, 1);
    $senderBranchDisplay = $sender_branch ?? ($user_branch ?? ($company['main_branch']['title'] ?? 'الفرع الرئيسي'));
    $bondNumDisplay = $bond_number ?? ($transaction_id ?? ($reference_number ?? '---'));

    // توليد رمز QR إذا لم يكن ممرراً
    if (!isset($qrCodeSvg)) {
        $qrCodeSvg = null;
        try {
            if (class_exists(\Milon\Barcode\Facades\DNS2DFacade::class) && !empty($bondNumDisplay) && $bondNumDisplay !== '---') {
                $qrCodeSvg = \Milon\Barcode\Facades\DNS2DFacade::getBarcodeSVG(
                    (string) ($tracking_code ?? $bondNumDisplay),
                    'QRCODE',
                    2.0,
                    2.0,
                    'black',
                    false
                );
            }
        } catch (\Throwable $e) {
            $qrCodeSvg = null;
        }
    }
@endphp

<table class="header-table" style="width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 1.5mm;">
    <tr>
        {{-- العمود الأيمن (عرض 38% - محاذاة لليمين) --}}
        <td style="width: 38%; text-align: right; vertical-align: top; padding: 0 0 0 4px;">
            {{-- اسم الشركة بخط عريض وواضح --}}
            <div style="margin-bottom: 2px;">
                <h1 style="color: {{ $primaryColor }}; font-size: 14pt; font-weight: 900; line-height: 1.15; margin: 0; padding: 0;">
                    {{ $company['name'] ?? 'شركة النقل' }}
                </h1>
            </div>

            {{-- الفرع الأول (المركز الرئيسي أو أول فرع) --}}
            @if(!empty($branchFirst))
                <div style="margin-top: 2px; line-height: 1.25;">
                    <div style="font-size: 7.5pt; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 4px; flex-wrap: wrap;">
                        <span style="background: {{ $primaryColor }}; color: #ffffff; padding: 1px 6px; border-radius: 3px; font-size: 7.5pt; font-weight: 800; display: inline-block; white-space: nowrap;">
                            {{ $branchFirst['name'] ?? 'الفرع الرئيسي' }}
                        </span>
                        @if(!empty($branchFirst['address']))
                            <span style="color: #334155; font-size: 7.5pt; font-weight: 700;">
                                {{ $branchFirst['address'] }}
                            </span>
                        @endif
                    </div>
                    @if(!empty($branchFirst['phone']))
                        <div style="font-size: 7.5pt; color: #475569; margin-top: 1.5px; line-height: 1.2;">
                            <span style="font-weight: 800; color: #334155;">للتواصل : </span>
                            <span dir="ltr" style="font-family: sans-serif; font-weight: 800; color: #0f172a;">{{ $branchFirst['phone'] }}</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- شارة الفرع المصدر (أسفل العمود الأيمن) --}}
            <div style="margin-top: 3px;">
                <span style="border: 1.5px solid #1e293b; background: #f1f5f9; border-radius: 14px; padding: 2px 18px; font-weight: 900; font-size: 9pt; display: inline-block; color: #0f172a; white-space: nowrap;">
                    فرع {{ $senderBranchDisplay }}
                </span>
            </div>
        </td>

        {{-- العمود الأوسط (عرض 24% - محاذاة للوسط) --}}
        <td style="width: 24%; text-align: center; vertical-align: top; padding: 0 4px;">
            {{-- شعار الشركة بحجم متناسق --}}
            @if(!empty($company['logo']))
                <div style="margin-bottom: 1.5mm; text-align: center;">
                    <img src="{{ $company['logo'] }}"
                        style="max-width: 34mm; max-height: 15mm; object-fit: contain; margin: 0 auto; display: block;"
                        alt="Logo">
                </div>
            @else
                <div style="height: 11mm;"></div>
            @endif

            {{-- عنوان السند في كبسولة بيضاوية ممتلئة بلون الهوية --}}
            <div style="text-align: center;">
                <span style="display: inline-block; background-color: {{ $primaryColor }}; color: #ffffff; border-radius: 25px; padding: 3px 16px; font-size: 11pt; font-weight: 900; white-space: nowrap; box-shadow: 0 1px 3px rgba(0,0,0,0.12);">
                    {{ $title ?? 'سند إرسال طرد' }}
                </span>
            </div>
        </td>

        {{-- العمود الأيسر (عرض 38% - محاذاة لليمين مع رمز QR في أقصى اليسار) --}}
        <td style="width: 38%; vertical-align: top; text-align: left; padding: 0 4px 0 0;">
            <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                <tr>
                    {{-- البيانات النصية (التاريخ والفروع الإضافية ورقم السند) --}}
                    <td style="text-align: right; vertical-align: top; padding-left: 4px;">
                        {{-- اليوم والتاريخ --}}
                        <div style="font-size: 8pt; font-weight: 800; color: #1e293b; margin-bottom: 1.5px; line-height: 1.3;">
                            اليوم : <span style="font-weight: 900; border-bottom: 1px dotted #64748b; padding-bottom: 0.5px;">{{ $day_name ?? now()->locale('ar')->translatedFormat('l') }}</span>
                        </div>
                        <div style="font-size: 8pt; font-weight: 800; color: #1e293b; margin-bottom: 2px; line-height: 1.3;">
                            الموافق : <span dir="ltr" style="font-family: sans-serif; font-weight: 800;">{{ $formatted_date ?? (now()->format('Y / m / d') . ' م') }}</span>
                        </div>

                        {{-- بقية الفروع إن وجدت --}}
                        @if(!empty($otherBranches) && count($otherBranches) > 0)
                            @foreach(array_slice($otherBranches, 0, 1) as $branchItem)
                                <div style="margin-top: 1.5px; line-height: 1.25;">
                                    <div style="font-size: 7.5pt; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 4px; flex-wrap: wrap;">
                                        <span style="background: {{ $primaryColor }}; color: #ffffff; padding: 1px 5px; border-radius: 3px; font-size: 7pt; font-weight: 800; display: inline-block; white-space: nowrap;">
                                            {{ $branchItem['name'] ?? '' }}
                                        </span>
                                        @if(!empty($branchItem['address']))
                                            <span style="color: #334155; font-size: 7pt; font-weight: 700;">
                                                {{ $branchItem['address'] }}
                                            </span>
                                        @endif
                                    </div>
                                    @if(!empty($branchItem['phone']))
                                        <div style="font-size: 7pt; color: #475569; margin-top: 1px; line-height: 1.15;">
                                            <span style="font-weight: 800; color: #334155;">للتواصل : </span>
                                            <span dir="ltr" style="font-family: sans-serif; font-weight: 800; color: #0f172a;">{{ $branchItem['phone'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @elseif(!empty($company['other_phones']))
                            <div style="font-size: 7pt; font-weight: bold; color: #64748b; line-height: 1.2; margin-top: 1px;">
                                للتواصل : <span dir="ltr" style="font-family: sans-serif; color: #0f172a; font-weight: 800;">{{ $company['other_phones'] }}</span>
                            </div>
                        @endif

                        {{-- رقم السند أسفل العمود الأيسر --}}
                        <div style="margin-top: 3px; line-height: 1.2;">
                            <span style="font-size: 8pt; font-weight: 800; color: #475569;">رقم السند : </span>
                            <span style="color: #b91c1c; font-size: 13pt; font-weight: 900; font-family: sans-serif; letter-spacing: 0.5px;">{{ $bondNumDisplay }}</span>
                        </div>
                    </td>

                    {{-- رمز QR Code في أقصى الزاوية العلوية اليسرى --}}
                    @if(!empty($qrCodeSvg))
                        <td style="width: 22mm; text-align: left; vertical-align: top; padding: 0;">
                            <div style="display: inline-block; padding: 2px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.06); line-height: 0;">
                                {!! $qrCodeSvg !!}
                            </div>
                        </td>
                    @endif
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="header-divider" style="height: 1.5px; background-color: {{ $primaryColor }}; margin-bottom: 2mm; width: 100%;"></div>
