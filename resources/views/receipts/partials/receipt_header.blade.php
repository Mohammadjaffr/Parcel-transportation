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
    $branchFirst = $branches[0] ?? null;
    $otherBranches = array_slice($branches, 1);
    $senderBranchDisplay = $sender_branch ?? ($user_branch ?? ($company['main_branch']['title'] ?? 'الفرع الرئيسي'));
    $bondNumDisplay = $bond_number ?? ($transaction_id ?? ($reference_number ?? '---'));

    // توليد رمز QR إذا لم يكن ممرراً
    if (empty($qrCodeSvg)) {
        $qrCodeSvg = null;
        try {
            $codeToEncode = !empty($tracking_code) && $tracking_code !== '---'
                ? $tracking_code
                : (!empty($bondNumDisplay) && $bondNumDisplay !== '---' ? $bondNumDisplay : null);

            if (
                class_exists(\Milon\Barcode\Facades\DNS2DFacade::class) &&
                !empty($codeToEncode)
            ) {
                $qrCodeSvg = \Milon\Barcode\Facades\DNS2DFacade::getBarcodeSVG(
                    (string) $codeToEncode,
                    'QRCODE',
                    3.0,
                    3.0,
                    'black',
                    false,
                );
            }
        } catch (\Throwable $e) {
            $qrCodeSvg = null;
        }
    }

    // معالجة كود الـ SVG لإضافة viewBox تلقائياً وجعله متجاوباً ليملأ المربع بنسبة 100%
    if (!empty($qrCodeSvg) && is_string($qrCodeSvg)) {
        $qrCodeSvg = preg_replace('/<\?xml.*?\?>/is', '', $qrCodeSvg);
        $qrCodeSvg = preg_replace('/<!DOCTYPE.*?>/is', '', $qrCodeSvg);
        $qrCodeSvg = trim($qrCodeSvg);

        $qrCodeSvg = preg_replace_callback('/<svg\b([^>]*)>/i', function ($matches) {
            $attrs = $matches[1];
            $w = null;
            $h = null;
            if (preg_match('/width="(\d+(?:\.\d+)?)"/i', $attrs, $m)) $w = $m[1];
            if (preg_match('/height="(\d+(?:\.\d+)?)"/i', $attrs, $m)) $h = $m[1];
            
            $attrs = preg_replace('/\b(width|height)="[^"]*"/i', '', $attrs);
            $attrs = trim($attrs);
            
            $viewBox = (!str_contains($attrs, 'viewBox') && $w && $h) ? "viewBox=\"0 0 {$w} {$h}\"" : "";
            return "<svg {$viewBox} width=\"100%\" height=\"100%\" preserveAspectRatio=\"xMidYMid meet\" style=\"width: 100%; height: 100%; display: block;\" {$attrs}>";
        }, $qrCodeSvg, 1);
    }
@endphp

<style>
    .qr-box-container {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .qr-box-container svg,
    .qr-box-container img {
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
        max-height: 100% !important;
        display: block;
        object-fit: contain;
    }
</style>

{{-- الجدول الرئيسي مع خاصية height: 1px للصف لتمكين تمدد الأعمدة الثلاثة من الأعلى للأسفل بنسبة 100% --}}
<table class="header-table" style="width: 100%; height: 1px; border-collapse: collapse; table-layout: fixed; border-bottom: 2px solid {{ $primaryColor }}; margin-bottom: 2mm;">
    <tr style="height: 1px;">
        {{-- العمود الأيمن (عرض 39% - يمتد من الأعلى للأسفل) --}}
        <td style="width: 39%; height: 100%; text-align: right; vertical-align: top; padding: 0 0 4px 4px;">
            <div style="display: flex; flex-direction: column; justify-content: space-between; height: 100%; box-sizing: border-box;">
                {{-- اسم الشركة --}}
                <div style="margin-bottom: 2px;">
                    <h1 style="color: {{ $primaryColor }}; font-size: 13.5pt; font-weight: 900; line-height: 1.05; margin: 0; padding: 0;">
                        {{ $company['name'] ?? 'شركة النقل' }}
                    </h1>
                    <h2 style="font-size: 8.5pt; font-weight: 900; color: #0f172a; margin: 1px 0 0 0; padding: 0; line-height: 1.15;">
                        لخدمات النقل البري وشحن الطرود والبضائع
                    </h2>
                </div>

                {{-- الفرع الأول (المركز الرئيسي) --}}
                @if (!empty($branchFirst))
                    <div style="margin-top: 2px; line-height: 1.2; direction: rtl;">
                        <span style="background: {{ $primaryColor }}; color: #ffffff; padding: 1px 5px; border-radius: 3px; font-size: 8pt; font-weight: 800; display: inline-block; vertical-align: middle; margin-left: 3px;">
                            {{ $branchFirst['name'] ?? 'الفرع الرئيسي' }}
                        </span>
                        @if (!empty($branchFirst['address']))
                            <span style="color: #1e293b; font-size: 8pt; font-weight: 700; vertical-align: middle;">
                                {{ $branchFirst['address'] }}
                            </span>
                        @endif
                        @if (!empty($branchFirst['phone']))
                            <span style="font-size: 8pt; color: #334155; vertical-align: middle;">
                                <span style="font-weight: 800; color: #0f172a;"> للتواصل: </span>
                                <span dir="ltr" style="font-family: sans-serif; font-weight: 800; color: #0f172a;">{{ $branchFirst['phone'] }}</span>
                            </span>
                        @endif
                    </div>
                @endif

                {{-- بقية الفروع --}}
                @if (!empty($otherBranches) && count($otherBranches) > 0)
                    @foreach (array_slice($otherBranches, 0, 1) as $branchItem)
                        <div style="margin-top: 2px; line-height: 1.2; direction: rtl;">
                            <span style="background: {{ $primaryColor }}; color: #ffffff; padding: 1px 5px; border-radius: 3px; font-size: 8pt; font-weight: 800; display: inline-block; vertical-align: middle; margin-left: 3px;">
                                {{ $branchItem['name'] ?? '' }}
                            </span>
                            @if (!empty($branchItem['address']))
                                <span style="color: #1e293b; font-size: 8pt; font-weight: 700; vertical-align: middle;">
                                    {{ $branchItem['address'] }}
                                </span>
                            @endif
                            @if (!empty($branchItem['phone']))
                                <span style="font-size: 8pt; color: #334155; vertical-align: middle;">
                                    <span style="font-weight: 800; color: #0f172a;"> للتواصل: </span>
                                    <span dir="ltr" style="font-family: sans-serif; font-weight: 800; color: #0f172a;">{{ $branchItem['phone'] }}</span>
                                </span>
                            @endif
                        </div>
                    @endforeach
                @elseif(!empty($company['other_phones']))
                    <div style="font-size: 8pt; font-weight: bold; color: #475569; line-height: 1.2; margin-top: 2px;">
                        للتواصل : <span dir="ltr" style="font-family: sans-serif; color: #0f172a; font-weight: 800;">{{ $company['other_phones'] }}</span>
                    </div>
                @endif
            </div>
        </td>

        {{-- العمود الأوسط (عرض 22% - الشعار في الأعلى والكبسولة في الأسفل فوق الخط الأخضر) --}}
        <td style="width: 22%; height: 100%; text-align: center; vertical-align: top; padding: 0 4px 4px 4px;">
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: space-between; height: 100%; box-sizing: border-box;">
                {{-- شعار الشركة --}}
                @if (!empty($company['logo']))
                    <div style="margin-bottom: 1.5mm; text-align: center; width: 100%;">
                        <img src="{{ $company['logo'] }}"
                            style="max-width: 46mm; max-height: 24mm; width: auto; height: auto; object-fit: contain; margin: 0 auto; display: block;"
                            alt="Logo">
                            
                             {{-- عنوان السند يرتكز مباشرة فوق الخط الأخضر --}}
                <div style="text-align: center; line-height: 1; width: 100%;margin-top: 20px ">
                    <span style="display: inline-block; background-color: {{ $primaryColor }}; color: #ffffff; border-radius: 25px; padding: 2.5px 18px; font-size: 11pt; font-weight: 900; white-space: nowrap; box-shadow: 0 1px 2px rgba(0,0,0,0.12);">
                        {{ $title ?? 'سند إرسال طرد' }}
                    </span>
                </div>
                    </div>
                    
                @else
                    <div style="height: 14mm;"></div>
                @endif

               
            </div>
        </td>

        {{-- العمود الأيسر (عرض 39% - يبدأ من أعلى الترويسة وينتهي عند الخط الأخضر بدون أي فراغ علوي) --}}
        <td style="width: 39%; height: 100%; vertical-align: top; padding: 0 0 4px 0;" dir="rtl">
            <div style="display: flex; align-items: stretch; justify-content: flex-end; gap: 10px; width: 100%; height: 100%; box-sizing: border-box;">
                
                {{-- البيانات النصية موزعة من أعلى نقطة لأخفض نقطة بانتظام --}}
                <div style="flex: 0 1 auto; min-width: 0; text-align: right; direction: rtl; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                    {{-- فرع الإرسال (في الأعلى تماماً بموازاة اسم الشركة) --}}
                    <div style="font-size: 13.5pt; font-weight: 900; color: #0f172a; line-height: 1.1; white-space: nowrap;">
                        فرع الارسال {{ $senderBranchDisplay }}
                    </div>

                    {{-- اليوم --}}
                    <div style="font-size: 9.5pt; font-weight: 800; color: #0f172a; line-height: 1.2;">
                        اليوم : <span style="font-weight: 900; border-bottom: 1.5px dotted #475569; padding-bottom: 0.5px;">{{ $day_name ?? now()->locale('ar')->translatedFormat('l') }}</span>
                    </div>

                    {{-- التاريخ --}}
                    <div style="font-size: 9.5pt; font-weight: 800; color: #0f172a; line-height: 1.2; white-space: nowrap;">
                        <span>التاريخ : </span>
                        <span dir="ltr" style="font-family: sans-serif; font-weight: 900; color: #0f172a; display: inline-block;">
                            {{ trim(str_replace(['م', '  '], ['', ' '], $formatted_date ?? now()->format('Y / m / d'))) }}
                        </span>
                        <span style="font-weight: 900;"> م</span>
                    </div>

                    {{-- الوقت --}}
                    @if (!empty($formatted_time))
                        <div style="font-size: 9pt; font-weight: 800; color: #1e293b; line-height: 1.2;">
                            <span>الوقت : </span>
                            <span dir="ltr" style="font-family: sans-serif; font-weight: 900; color: #0f172a; display: inline-block;">
                                {{ $formatted_time }}
                            </span>
                        </div>
                    @endif

                    {{-- رقم السند (في الأسفل تماماً بموازاة كبسولة سند الإرسال والخط الأخضر) --}}
                    <div style="line-height: 1;">
                        <span style="font-size: 9.5pt; font-weight: 900; color: #1e293b;">رقم السند : </span>
                        <span style="color: #b91c1c; font-size: 14.5pt; font-weight: 900; font-family: sans-serif; letter-spacing: 0.5px; vertical-align: middle;">{{ $bondNumDisplay }}</span>
                    </div>
                </div>

                {{-- رمز QR Code يمتد بارتفاع متناسق مع النصوص من الأعلى للأسفل --}}
                @if (!empty($qrCodeSvg))
                    <div class="qr-box-container"
                        style="flex-shrink: 0; width: 28mm; height: 28mm; align-self: center; padding: 2.5px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.06); box-sizing: border-box; display: flex; align-items: center; justify-content: center;">
                        {!! $qrCodeSvg !!}
                    </div>
                @endif

            </div>
        </td>
    </tr>
</table>