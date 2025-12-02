<?php

namespace App\Http\Controllers\API;

use App\Models\Rating;
use Illuminate\Http\Request;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\RatingRepository;
use Illuminate\Validation\Rule;

class RatingController extends Controller
{

    public function __construct(private RatingRepository $ratingRepository)
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
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
    public function show(Rating $rating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rating $rating)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating)
    {
        //
    }

    public function upsertRating(Request $request)
    {
        $fields=$request->validate([
            'driver_id' =>['required',Rule::exists('drivers', 'id')],
            'rating' =>['required','integer','min:1','max:5'], 
        ], [
            'driver_id.required' => 'يجب اختيار السائق.',
            'driver_id.exists' => 'السائق غير موجودة.',
            'rating.required' => 'يجب إدخال التقييم.',
            'rating.integer' => 'يجب أن يكون التقييم رقمًا صحيحًا.',
            'rating.min' => 'يجب أن يكون التقييم على الأقل 1.',
            'rating.max' => 'يجب أن لا يتجاوز التقييم 5.',
        ]);
        $fields['user_id'] = auth('sanctum')->id();
        $rating=$this->ratingRepository->getByUserIdAndDriverId($fields['user_id'], $fields['driver_id']);
        if($rating){
            $rating=$this->ratingRepository->update($fields, $rating->id);
            return ApiResponseClass::sendResponse($rating, 'تم تحديث التقييم بنجاح.');
        }
        $rating = $this->ratingRepository->store($fields);
        return ApiResponseClass::sendResponse($rating, 'تم إنشاء التقييم بنجاح.');
    }
}
