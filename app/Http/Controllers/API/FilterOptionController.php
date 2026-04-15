<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\FilterOptionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterOptionController extends Controller
{
    use ApiResponse;
    public function __construct(protected FilterOptionService $filterOptionService)
    {
    }

    /**
     * GET /api/vehicles/filter-options
     *
     * Query params (all optional, cascading):
     *   ?category_id=1
     *   ?category_id=1&brand_id=2
     *   ?category_id=1&brand_id=2&model_id=5
     *   ?category_id=1&brand_id=2&model_id=5&body_color_id[]=3&body_color_id[]=4
     */
    public function index(Request $request): JsonResponse
    {
        $options = $this->filterOptionService->getOptions($request);

        return $this->success($options, 'Filter options fetched successfully', 200);
    }
}
