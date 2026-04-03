<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'whatsapp_number' => ['nullable', 'string'],
            'password' => ['required', 'string', 'min:6'],
            'branch_id' => ['required', 'exists:branches,id']
        ], [
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً لمستخدم آخر.',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
            'branch_id.exists' => 'الفرع المحدد غير موجود في النظام.',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return WebResponseClass::sendValidationError($validator);
        }

        try {

            User::create([
                'app_id' => auth()->user()->app_id,
                'branch_id' => $request->branch_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'whatsapp_number' => $request->whatsapp_number,
                'password' => Hash::make($request->password), // تشفير كلمة المرور
                'type' => 'user', // Default type
                'is_banned' => false,
            ]);

            if ($request->wantsJson()) {
                session()->flash('success', true);
                session()->flash('success_title', 'تمت الإضافة!');
                session()->flash('success_message', 'تم إضافة المستخدم بنجاح.');
                return response()->json(['success' => true]);
            }

            return WebResponseClass::sendResponse('تم الإضافة!', 'تم إضافة المستخدم بنجاح', 'حسناً', 'users.index');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'حدث خطأ في السيرفر'], 500);
            }
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with('branch')->findOrFail($id);

        $users = User::with('branch')
            ->where('branch_code', $user->branch_code)
            ->get();

        return view('pages.users.show', compact('user', 'users'));
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // 3. تحديث البيانات
            $user->update([
                'name'            => $request->name,
                'phone'           => $request->phone,
                'branch_id'       => $request->branch_id,
                'whatsapp_number' => $request->whatsapp_number,
                // تحديث كلمة المرور فقط إذا تم إرسالها
                'password'        => $request->filled('password') ? Hash::make($request->password) : $user->password,
            ]);

            // الفلاش ميسج للنجاح
            session()->flash('success', true);
            session()->flash('success_title', 'تم التحديث!');
            session()->flash('success_message', 'تم تحديث بيانات المستخدم بنجاح.');

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء التحديث'], 500);
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
                return response()->json(['message' => 'لا يمكنك حذف حسابك الشخصي.'], 400);
            }
            return WebResponseClass::sendError('لا يمكنك حذف حسابك الشخصي.', 'خطأ!');
        }

        try {
            $user->delete();

            if ($request->wantsJson()) {
                session()->flash('success', true);
                session()->flash('success_title', 'تم الحذف!');
                session()->flash('success_message', 'تم حذف المستخدم بنجاح.');
                return response()->json(['success' => true]);
            }

            return WebResponseClass::sendResponse('تم الحذف!', 'تم حذف المستخدم بنجاح.', 'حسناً', 'users.index');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'حدث خطأ في السيرفر'], 500);
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
