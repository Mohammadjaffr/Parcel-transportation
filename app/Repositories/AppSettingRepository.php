<?php

namespace App\Repositories;
use App\Models\app_setting;

class AppSettingRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function getSetting()
    {
        $setting = app_setting::first();
        return $setting ;
    }
}
