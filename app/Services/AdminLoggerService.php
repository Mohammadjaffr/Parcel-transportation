<?php

namespace App\Services;

use App\Models\AdminActivity;
use Illuminate\Http\Request;

class AdminLoggerService
{
    public static function log($action,$model = null,$modelId = null,$description = null) {
        // $adminId =  auth('sanctum')->id();
          $adminId = auth()->id();
        if (!$adminId) {
            return null;
        }

        return AdminActivity::create([
            'user_id' => $adminId,
            'action' => $action,
            'model_name' => $model,
            'description' => $description . " (ID: $modelId)",
            // 'model_id' => $modelId, // Column missing in DB
        ]);
    }


}