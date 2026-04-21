<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Models\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            'office_name' => ['required', 'string', 'max:255'],

            'branch_name' => ['required', 'string', 'max:255'],
            'branch_address' => ['required', 'string', 'max:255'],
            'branch_phone' => ['required', 'string', 'max:20'],
            'branch_city' => ['required', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($request) {
            
            $app = App::create([
                'name' =>  $request->office_name, 
                'is_active' => false,
                'terms_and_conditions' => [
                    'نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا.',
                    'نحن غير مسؤولين عن الأشياء الثمينة الممنوع إرسالها في الطرود.',
                    'نحن غير مسؤولين عن بقاء الطرود أكثر من شهر.',
                    'نحن غير مسؤولين عن الحريق وحوادث السير.',
                    'الرجاء التأكد من بيانات السند قبل المغادرة.'
                ],
            ]);

            $branch = Branch::create([
                'app_id' => $app->id,
                'name' => $request->branch_name,
                'address' => $request->branch_address,
                'phone' => $request->branch_phone,
                'is_main' => true, 
                'city' => $request->branch_city, 
            ]);

            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'app_id' => $app->id,
                'branch_id' => $branch->id,
                'type' => 'admin',
            ]);
            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard.index', absolute: false));
    }
}