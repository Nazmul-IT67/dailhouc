<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class BlogController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $data = Blog::all();

        if ($data == null) {
            return $this->error([], 'Blogs not found', 200);
        }

        return $this->success($data, 'Blogs fetch Successful!', 200);
    }
}
