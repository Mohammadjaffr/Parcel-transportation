<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use App\Classes\WebResponseClass;
use App\Services\AdminLoggerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class OfficeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMobile) {
            return view('mobile.pages.office.index');
        }

        return view('pages.office.index');
    }

    public function unverifiedIndex(Request $request)
    {
        $query = Office::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        $offices = $query->latest()->paginate(10)->withQueryString();
        if ($request->isMobile) {
            return view('mobile.pages.office.unverified.index', compact('offices'));
        }
        return view('pages.office.unverified.index', compact('offices'));
    }

    public function create(Request $request)
    {   if ($request->isMobile) {
            return view('mobile.pages.office.unverified.create');
        }
        return view('pages.office.unverified.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255', 
        'phone' => 'nullable|string|max:20', 
        'branches' => 'required|array|min:1',
        'branches.*.name' => 'required|string|max:255',
        'branches.*.city' => 'required|string|max:255',
        'branches.*.phone' => 'nullable|string|max:20',
        'branches.*.address' => 'nullable|string',
    ]);

    try {
        DB::transaction(function () use ($request) {
            $user = auth()->user();

            $office = Office::create([
                'app_id'     => $user->app_id,
                'created_by' => $user->id,
                'name'       => $request->name,
                'phone'      => $request->phone,
                'address'    => $request->address,
            ]);

            foreach ($request->branches as $branchData) {
                $office->branches()->create([
                    'name'    => $branchData['name'],
                    'city'    => $branchData['city'],
                    'phone'   => $branchData['phone'],
                    'address' => $branchData['address'],
                ]);
            }
        });

        session()->flash('success_title', 'تم الحفظ!');
        session()->flash('success_message', 'تم إضافة المكتب وفروعه بنجاح.');

        return redirect()->route('offices.unverified.index');

    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
    }
}

    public function edit($id)
    {
        $office = Office::with('branches')->findOrFail($id);
        return view('mobile.pages.office.unverified.edit', compact('office'));
    }
    public function update(Request $request, $id)
    {
        $office = Office::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'branches' => 'required|array|min:1',
            'branches.*.name' => 'required|string|max:255',
            'branches.*.city' => 'required|string|max:100',
        ]);

        try {
            DB::transaction(function () use ($request, $office) {
                $office->update(['name' => $request->name]);
                $office->branches()->delete();
                foreach ($request->branches as $branchData) {
                    $office->branches()->create([
                        'name'    => $branchData['name'],
                        'city'    => $branchData['city'],
                        'phone'   => $branchData['phone'],
                        'address' => $branchData['address'],
                    ]);
                }
            });

            return redirect()->route('offices.unverified.index')->with('success', 'تم تحديث البيانات بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'حدث خطأ أثناء التحديث');
        }
    }

    public function destroy(Office $office)
    {
       try {
            $office->delete();
            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف المكتب بنجاح.',
                'حسناً'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
