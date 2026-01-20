<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class SocialMediaController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $data = SocialMedia::latest()->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Social Link not found', 200);
        }
        return $this->success($data, 'Social Link fetch Successful!', 200);
    }
}
