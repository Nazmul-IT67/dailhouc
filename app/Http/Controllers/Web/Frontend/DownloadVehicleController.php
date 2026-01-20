<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use App\Traits\ApiResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadVehicleController extends Controller
{
    public function downloadVehiclePdf($id)
    {
        $vehicle = Vehicle::with([
            'data.condition', 'photos', 'brand', 'model', 'fuel', 'transmission', 'power'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.vehicle', compact('vehicle'));
        return $pdf->download('vehicle-'.$vehicle->id.'.pdf');
    }
}
