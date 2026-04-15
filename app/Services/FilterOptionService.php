<?php

namespace App\Services;

use App\Repositories\FilterOptionRepository;
use Illuminate\Http\Request;

class FilterOptionService
{
    public function __construct(protected FilterOptionRepository $repo)
    {
    }

    /**
     * Return all filter options with cascading counts.
     *
     * Cascade order:
     *   category → brand → model → sub_model → body_type → fuel → transmission → colors
     *
     * Each step uses all previously selected filters as context for counting,
     * but excludes its own filter so all options remain visible (count = 0 if none match).
     */
    public function getOptions(Request $request): array
    {
        return [
            'total_vehicles'  => $this->repo->getTotalCount($request),
            'categories'      => $this->repo->getCategories($request),
            'brands'          => $this->repo->getBrands($request),
            'models'          => $this->repo->getModels($request),
            'sub_models'      => $this->repo->getSubModels($request),
            'body_types'      => $this->repo->getBodyTypes($request),
            'fuels'           => $this->repo->getFuels($request),
            'transmissions'   => $this->repo->getTransmissions($request),
            'body_colors'     => $this->repo->getBodyColors($request),
            'interior_colors' => $this->repo->getInteriorColors($request),
            'bed_type_id'     => $this->repo->getBedType($request),
            'upholstery_id'   => $this->repo->getUpholstery($request),
            'equipment_ids'   => $this->repo->getEquipment($request),
            'seller_type_id'  => $this->repo->getSeller($request),
        ];
    }
}
