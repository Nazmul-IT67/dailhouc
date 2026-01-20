<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    use ApiResponse;

    public function index()
    {

        $data = SystemSetting::where('id', 1)->first();

        if ($data == null) {
            return $this->error([], 'System Settings not found', 200);
        }

        return $this->success($data, 'System Settings fetch Successful!', 200);
    }
}
