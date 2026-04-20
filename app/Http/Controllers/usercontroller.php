<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShipmentPackage;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * عرض قائمة المستخدمين مع البحث
     */
    public function index(Request $request)
    {
        $query = User::where('type', '!=', 'admin')->where('app_id', auth()->user()->app_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('whatsapp_number', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $branches = Branch::where('app_id', auth()->user()->app_id)->get();

        if ($request->isMobile) {
            return view('mobile.pages.people.users.index', compact('users', 'branches'));
        }

        return view('pages.users.index', compact('users', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * إضافة مستخدم جديد (يدعم AJAX)
     */
    public function store(Request $request)
{
    // 1. التحقق من البيانات
    $validator = Validator::make($request->all(), [
        'name' => ['required', 'string', 'max:255'],
        'phone' => ['required', 'string', 'unique:users,phone'],
        'password' => ['required', 'string', 'min:6'],
        'branch_id' => ['required', 'exists:branches,id']
    ], [
        'phone.unique' => 'رقم الهاتف مسجل مسبقاً لمستخدم آخر.',
        'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
        'branch_id.exists' => 'الفرع المحدد غير موجود في النظام.',
    ]);

    if ($validator->fails()) {
        // إذا كان الطلب AJAX/JSON نرسل الأخطاء بصيغة JSON مع كود 422
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        return WebResponseClass::sendValidationError($validator);
    }

    try {
        DB::beginTransaction(); // أفضل ممارسة عند الإضافة

        User::create([
            'app_id'    => auth()->user()->app_id,
            'branch_id' => $request->branch_id,
            'name'      => $request->name,
            'phone'     => $request->phone,
            'whatsapp_number' => $request->whatsapp_number,
            'password'  => Hash::make($request->password),
            'type'      => 'user',
            'is_banned' => false,
        ]);

        DB::commit();

        // 2. الرد في حالة النجاح
        if ($request->expectsJson()) {
            return response()->json([
                'status'  => 'success',
                'title'   => 'تمت الإضافة!',
                'message' => 'تم إضافة المستخدم بنجاح إلى النظام.',
                'redirect' => route('users.index') // نرسل الرابط لتقوم الجافاسكريبت بالتحويل إذا أردت
            ], 200);
        }

        return WebResponseClass::sendResponse('تم الإضافة!', 'تم إضافة المستخدم بنجاح', 'حسناً', 'users.index');

    } catch (\Exception $e) {
        DB::rollBack();
        
        if ($request->expectsJson()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()
            ], 500);
        }
        return WebResponseClass::sendExceptionError($e);
    }
}

    /**
     * Display the specified resource.
     */
    // معتمد
    public function show(Request $request, $id)
    {
        $user = User::with('branch')->findOrFail($id);
        $period = $request->query('period', 'all');
        $dateFilter = function ($query) use ($period) {
            if ($period === 'today') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($period === 'week') {
                $query->where('created_at', '>=', Carbon::now()->startOfWeek());
            } elseif ($period === 'month') {
                $query->where('created_at', '>=', Carbon::now()->startOfMonth());
            }
            // إذا كان 'all' لن يتم إضافة أي شرط
        };

       $manifestsCount = ShipmentPackage::where('created_by', $user->id)->where($dateFilter)->count();
        $shipmentsCount = Shipment::where('created_by', $user->id)->where($dateFilter)->count();
        $customersCount = Customer::where('created_by', $user->id)->where($dateFilter)->count();
        $recentManifests = ShipmentPackage::with('driver')->where('created_by', $user->id)->latest()->take(5)->get();

        if ($request->isMobile) {
            return view('mobile.pages.people.users.show', compact('user', 'manifestsCount', 'shipmentsCount', 'customersCount', 'recentManifests', 'period'));
        }
        
        return view('pages.users.show', compact('user', 'manifestsCount', 'shipmentsCount','customersCount', 'recentManifests', 'period'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $query = User::query();

        if (auth()->check() && auth()->user()->type !== 'super_admin') {
            $query->where('app_id', auth()->user()->app_id);
        }

        // 2. جلب المستخدم مع تفاصيل علاقته بالفرع (إن وجدت)
        $user = $query->with('branch')->findOrFail($id);

        return response()->json($user);
    }

    /**
     * تحديث بيانات المستخدم (يدعم AJAX)
     */
    public function update(Request $request, string $id)
    {
        // 1. جلب المستخدم مع التأكد من عزل البيانات (SaaS)
        $query = User::query();
        if (auth()->user()->type !== 'super_admin') {
            $query->where('app_id', auth()->user()->app_id);
        }
        $user = $query->findOrFail($id);

        // 2. التحقق من البيانات (تأكد من مطابقة الأسماء تماماً)
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|unique:users,phone,' . $id,
            'branch_id' => 'required', // سنكتفي بـ required هنا لأننا سنفحص الشركة يدوياً أدناه
        ], [
            'name.required'      => 'يرجى إدخال الاسم.',
            'phone.unique'       => 'رقم الهاتف مسجل مسبقاً.',
            'branch_id.required' => 'يرجى تحديد الفرع.',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            // 3. تحديث البيانات
            $user->update([
                'name'            => $request->name,
                'phone'           => $request->phone,
                'branch_id'       => $request->branch_id,
                'whatsapp_number' => $request->whatsapp_number,
                'is_banned'       => $request->is_banned ?? 0,
                // تحديث كلمة المرور فقط إذا تم إرسالها
                'password'        => $request->filled('password') ? Hash::make($request->password) : $user->password,
            ]);

            // الفلاش ميسج للنجاح
            session()->flash('success', true);
            session()->flash('success_title', 'تم التحديث!');
            session()->flash('success_message', 'تم تحديث بيانات المستخدم بنجاح.');

 return WebResponseClass::sendResponse('تم الإضافة!', 'تم تحديث بيانات المستخدم بنجاح', 'حسناً', 'users.index');        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * حذف مستخدم (يدعم AJAX)
     */
    public function destroy(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // حماية: منع المستخدم من حذف نفسه
        if (auth()->id() == $user->id) {
            if ($request->wantsJson()) {
                return WebResponseClass::sendError('لا يمكنك حذف حسابك الشخصي.', 'خطأ!');
            }
            return WebResponseClass::sendError('لا يمكنك حذف حسابك الشخصي.', 'خطأ!');
        }

        try {
            $user->delete();

            if ($request->wantsJson()) {
                session()->flash('success', true);
                session()->flash('success_title', 'تم الحذف!');
                session()->flash('success_message', 'تم حذف المستخدم بنجاح.');
                return WebResponseClass::sendResponse('تم الحذف!', 'تم حذف المستخدم بنجاح.', 'حسناً', 'users.index');
            }

            return WebResponseClass::sendResponse('تم الحذف!', 'تم حذف المستخدم بنجاح.', 'حسناً', 'users.index');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return WebResponseClass::sendExceptionError($e);
            }
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * تبديل حالة حظر المستخدم
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        $user->is_banned = !$user->is_banned;
        $user->save();

        return response()->json([
            'success' => true,
            'status'  => $user->is_banned
        ]);
    }
}
