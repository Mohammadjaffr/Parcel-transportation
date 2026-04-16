<style>
    h3 { text-align: center; color: #1e293b; font-size: 16px; margin: 0; padding: 0; }
    .text-center { text-align: center; }
    .bold { font-weight: bold; }
    .border-bottom { border-bottom: 1px dashed #94a3b8; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 4px 2px; font-size: 11px; line-height: 1.5; }
</style>

<h3>شـركـة الشـحـن السـريـع</h3>
<div class="text-center" style="font-size: 10px; color: #64748b;">رقم ضريبي: 123456789</div>
<br>

<h3 style="font-size: 14px; text-decoration: underline;">{{ $title }}</h3>
<br>

<table>
    <tr>
        <td width="35%" class="bold">رقم البوليصة:</td>
        <td width="65%">{{ $bond_number }}</td>
    </tr>
    <tr>
        <td width="35%" class="bold">التاريخ:</td>
        <td width="65%">{{ $date }}</td>
    </tr>
</table>

<table><tr><td class="border-bottom"></td></tr></table> <table>
    <tr>
        <td width="35%" class="bold">اسم العميل:</td>
        <td width="65%">{{ $customer_name }}</td>
    </tr>
    <tr>
        <td width="35%" class="bold">رقم الهاتف:</td>
        <td width="65%" dir="ltr" style="text-align: right;">{{ $customer_phone }}</td>
    </tr>
    <tr>
        <td width="35%" class="bold">محتوى الطرد:</td>
        <td width="65%">{{ $package_type === 'carton' ? 'كرتون' : ($package_type === 'bag' ? 'كيس' : 'أخرى') }}</td>
    </tr>
</table>

<table><tr><td class="border-bottom"></td></tr></table> <table>
    <tr>
        <td width="40%" class="bold">طريقة الدفع:</td>
        <td width="60%">{{ $payment_method }}</td>
    </tr>
    <tr>
        <td width="40%" class="bold">المبلغ:</td>
        <td width="60%" class="bold" style="font-size: 13px;">{{ $amount }} ريال</td>
    </tr>
</table>

<br><br>
<div class="text-center" style="font-size: 10px;">
    أصدرت بواسطة: {{ $creator_name }}<br>
    -- شكراً لاختياركم خدماتنا --
</div>