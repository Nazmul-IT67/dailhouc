<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use Illuminate\Http\Request;

class DynamicPageController extends Controller
{
    // Get all active pages
    public function index()
    {
        $pages = DynamicPage::where('status', 'active')->get();

        return response()->json([
            'success' => true,
            'data' => $pages
        ]);
    }

    // Get single page by slug
    public function show($slug)
    {
        $page = DynamicPage::where('page_slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    }
}
