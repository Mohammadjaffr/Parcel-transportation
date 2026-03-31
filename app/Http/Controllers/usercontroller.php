<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Classes\WebResponseClass;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * عرض قائمة المستخدمين مع البحث
     */
    public function index(Request $request)
    {
        $query = User::where('type', '!=', 'super_admin');

        // تفعيل ميزة البحث المباشر بالاسم، الهاتف، أو الواتساب
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('whatsapp_number', 'like', "%{$search}%");
            });
        }

        // استخدام withQueryString للحفاظ على كلمة البحث عند التنقل عبر الـ Pagination
        $users = $query->latest()->paginate(10)->withQueryString();

        if ($request->isMobile) {
            return view('mobile.pages.people.users.index', compact('users'));
        }
        
        return view('pages.users.index', compact('users'));
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
        ], [
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً لمستخدم آخر.',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.'
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'whatsapp_number' => $request->whatsapp_number,
                'password' => Hash::make($request->password), // تشفير كلمة المرور
                'type' => 'user', // Default type
                'is_banned' => false,
                'branch_code' => Auth::user()->branch_code,
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
        $user = User::findOrFail($id);
        
        return response()->json($user);
    }

    /**
     * تحديث بيانات المستخدم (يدعم AJAX)
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone,' . $id,
            'whatsapp_number' => 'nullable|string',
            'type' => 'required|in:user,admin,super_admin',
            'password' => 'nullable|string|min:6',
            'is_banned' => 'nullable',
        ], [
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً.'
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = [
                'name' => $request->name,
                'phone' => $request->phone,
                'whatsapp_number' => $request->whatsapp_number,
                'type' => $request->type,
            ];

            // تحديث حالة الحظر إن وجدت
            if ($request->has('is_banned')) {
                $data['is_banned'] = filter_var($request->is_banned, FILTER_VALIDATE_BOOLEAN);
            }

            // تحديث كلمة المرور فقط إذا تم إدخال قيمة جديدة
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            if ($request->wantsJson()) {
                session()->flash('success', true);
                session()->flash('success_title', 'تم التحديث!');
                session()->flash('success_message', 'تم تحديث بيانات المستخدم بنجاح.');
                return response()->json(['success' => true]);
            }

            return WebResponseClass::sendResponse('تم التحديث!', 'تم تحديث بيانات المستخدم بنجاح.', 'حسناً', 'users.index');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'حدث خطأ في السيرفر'], 500);
            }
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