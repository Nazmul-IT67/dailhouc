<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all();
        return view('backend.pages.dashboard'); // blade file: resources/views/dashboard.blade.php
    }
}
