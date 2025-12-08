<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Services\AdminLoggerService;


class BranchController extends Controller
{
    /* ========== 1- عرض جميع الفروع ========== */
    public function index()
    {
        $branches = Branch::latest()->paginate(10);
        return view('pages.branch.index', compact('branches'));
    }

    /* ========== 2- صفحة إنشاء فرع ========== */
    public function create()
    {
        return view('pages.branch.create');
    }

    /* ========== 3- تخزين فرع جديد ========== */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'phone'  => 'required|string|max:50',
        ], [
            'name.required'   => 'حقل اسم الفرع مطلوب.',
            'region.required' => 'حقل المنطقة مطلوب.',
            'phone.required'  => 'حقل الهاتف مطلوب.',
        ]);

        if ($validator->fails()) {
            return $this->ValidationError($validator);
        }

        try {
            Branch::create($validator->validated());
            AdminLoggerService::log('إنشاء فرع', 'Branch', "تم إنشاء الفرع بنجاح");


            return $this->SuccessBacktoIndex(
                'تمت الإضافة!',
                'تم إضافة الفرع بنجاح.'
            );
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }

    /* ========== 4- عرض تفاصيل فرع واحد ========== */
    public function show($id)
    {
        $branch = Branch::findOrFail($id);
        return view('pages.branch.show', compact('branch'));
    }

    /* ========== 5- صفحة تعديل الفرع ========== */
    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return view('pages.branch.edit', compact('branch'));
    }

    /* ========== 6- تحديث الفرع ========== */
    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'phone'  => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->ValidationError($validator);
        }

        try {
            $branch->update($validator->validated());
            AdminLoggerService::log('تحديث فرع', 'Branch', "تم تحديث بيانات الفرع بنجاح");

            return $this->SuccessBacktoIndex(
                'تم التحديث!',
                'تم تحديث بيانات الفرع بنجاح.'
            );
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }

    /* ========== 7- حذف الفرع ========== */
    public function destroy($id)
    {
        try {
            Branch::findOrFail($id)->delete();
            AdminLoggerService::log('حذف فرع', 'Branch', "تم حذف الفرع بنجاح");


            return $this->SuccessBacktoIndex(
                'تم الحذف!',
                'تم حذف الفرع بنجاح.'
            );
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }


    /* ========== دوال إعادة الاستخدام ========== */
    private function ValidationError($validator)
    {
        return redirect()->back()
            ->withErrors($validator)
            ->with('error', true)
            ->with('error_title', 'حدث خطأ!')
            ->with('error_message', $validator->errors()->first())
            ->with('error_buttonText', 'حسناً')
            ->withInput();
    }

    private function SuccessBacktoIndex($title, $msg)
    {
        return redirect()->route('branch.index')
            ->with('success', true)
            ->with('success_title', $title)
            ->with('success_message', $msg)
            ->with('success_buttonText', 'حسناً');
    }

    private function ExceptionError($e)
    {
        return redirect()->back()
            ->with('error', true)
            ->with('error_title', 'خطأ غير متوقع!')
            ->with('error_message', $e->getMessage())
            ->with('error_buttonText', 'حسناً');
    }
}
