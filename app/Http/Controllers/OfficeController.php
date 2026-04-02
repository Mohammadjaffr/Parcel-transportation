<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index(Request $request) {
        if ($request->isMobile) {
            return view('mobile.pages.office.index');
        }

        return view('pages.drivers.index');
    }

    public function unverifiedIndex(Request $request){
    $query = Office::query();
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->orWhere('city', 'like', "%{$search}%");
        });
    }
    $offices = $query->latest()->paginate(10)->withQueryString();
        if ($request->isMobile) {
            return view('mobile.pages.office.unverified.index',compact('offices'));
        }
        return view('pages.office.unverified.index',compact('offices'));
    }
}
