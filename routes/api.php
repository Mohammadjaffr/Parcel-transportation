<?php

use App\Http\Controllers\API\AdminSubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['admin.key'])->prefix('admin')->group(function () {
    Route::get('/subscriptions/pending', [AdminSubscriptionController::class, 'pendingRequests']); 
    Route::post('/subscriptions/{id}/activate', [AdminSubscriptionController::class, 'activate']);
});
  

