<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OfficeConnection;
use App\Notifications\ConnectionRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConnectionController extends Controller
{
    /**
     * إرسال طلب ربط
     */
    public function sendRequest($receiverAppId)
{
    $senderAppId = Auth::user()->app_id;

    // 1. الحماية: منع إرسال طلب لنفس الشركة
    if ($senderAppId == $receiverAppId) {
        return back()->with('error', 'لا يمكنك إرسال طلب لشركتك!');
    }

    // 2. التحقق من وجود طلب سابق (معلق أو مقبول)
    $existingConnection = OfficeConnection::where(function($q) use ($senderAppId, $receiverAppId) {
        $q->where('sender_app_id', $senderAppId)->where('receiver_app_id', $receiverAppId);
    })->orWhere(function($q) use ($senderAppId, $receiverAppId) {
        $q->where('sender_app_id', $receiverAppId)->where('receiver_app_id', $senderAppId);
    })->first();

    if ($existingConnection) {
        if ($existingConnection->status == 'pending') {
            return back()->with('info', 'يوجد طلب معلق بالفعل.');
        }
        if ($existingConnection->status == 'accepted') {
            return back()->with('info', 'أنت متصل بالفعل بهذا المكتب.');
        }
    }

    // 3. إنشاء سجل الربط وتخزينه في متغير $connection (هنا التعديل المهم)
    $connection = OfficeConnection::create([
        'sender_app_id'   => $senderAppId,
        'receiver_app_id' => $receiverAppId,
        'status'          => 'pending',
    ]);

    // 4. إرسال الإشعار وتمرير $connection كمعامل ثاني (هنا التعديل المهم)
    $receiverAdmin = User::where('app_id', $receiverAppId)->where('type', 'admin')->first();
    
    if ($receiverAdmin) {
        // قمنا بتمرير المكتب الحالي، والاتصال الجديد الذي أنشأناه للتو
        $receiverAdmin->notify(new ConnectionRequestNotification(Auth::user()->App, $connection));
    }

    return back()->with([
        'success_title' => 'تم الإرسال!',
        'success_message' => 'تم إرسال طلب الربط بنجاح، بانتظار موافقة الطرف الآخر.'
    ]);
}

    /**
     * قبول الطلب
     */
    public function accept($id)
    {
        $connection = OfficeConnection::findOrFail($id);
        
        // التحقق من الصلاحية
        if ($connection->receiver_app_id !== Auth::user()->app_id) { abort(403); }

        $connection->update(['status' => 'accepted']);

        return back()->with('success', 'تم قبول طلب الربط بنجاح.');
    }

    /**
     * رفض الطلب (حذف السجل ليتمكن من الإرسال مرة أخرى)
     */
    public function reject($id)
    {
        $connection = OfficeConnection::findOrFail($id);
        
        if ($connection->receiver_app_id !== Auth::user()->app_id) { abort(403); }

        // الحذف هو السر هنا، السجل يختفي والزر يرجع "ربط" عند المرسل
        $connection->delete();

        return back()->with('info', 'تم رفض الطلب، يمكن للمكتب المحاولة مرة أخرى.');
    }
}