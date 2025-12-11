<?php

namespace App\Http\Controllers;

use App\Models\User;
use GPBMetadata\Google\Api\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentBranch = auth()->user()->branch_id;

        $users = User::with('branch')
            ->where('branch_id', $currentBranch)
            ->paginate(10);

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with('branch')->findOrFail($id);

        // جلب كل المستخدمين في نفس الفرع
        $users = User::with('branch')
            ->where('branch_id', $user->branch_id)
            ->get();

        // تمرير user + users للواجهة
        return view('pages.users.show', compact('user', 'users'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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
