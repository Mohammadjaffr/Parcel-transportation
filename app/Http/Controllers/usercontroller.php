<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Classes\WebResponseClass;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::where('type', '!=', 'super_admin')->paginate(10);
        if ($request->isMobile) {
            return view('mobile.pages.people.users.index',compact('users'));
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'whatsapp_number' => ['nullable', 'string'],
            'password' => ['required', 'string', 'min:6'],
        ]);
       
        User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'whatsapp_number' => $request->whatsapp_number,
            'password' => $request->password,
            'type' => 'user', // Default type
            'is_banned' => false,
            'branch_code' => Auth::user()->branch_code,
        ]);

        return WebResponseClass::sendResponse('تم الإضافة!', 'تم إضافة المستخدم بنجاح', 'حسناً', 'users.index');
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone,' . $id,
            'whatsapp_number' => 'nullable|string',
            'type' => 'required|in:user,admin,super_admin',
            'password' => 'nullable|string|min:6',
            'is_banned' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'whatsapp_number' => $request->whatsapp_number,
            'type' => $request->type,
            'is_banned' => $request->has('is_banned') ? (bool) $request->is_banned : false,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
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