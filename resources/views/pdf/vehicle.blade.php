<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
            color: #2c3e50;
        }

        .price {
            color: #e74c3c;
            font-size: 18px;
            font-weight: bold;
        }

        .main-img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }

        .section-title {
            background: #2c3e50;
            color: #fff;
            padding: 6px 10px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table td {
            padding: 6px 8px;
            border: 1px solid #f1f1f1;
            vertical-align: top;
            word-wrap: break-word;
        }

        .label {
            font-weight: bold;
            color: #555;
            width: 35%;
            display: inline-block;
        }

        .value {
            color: #000;
            width: 60%;
            display: inline-block;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #777;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            background: #eee;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="title">{{ $vehicle->brand->name ?? '' }} {{ $vehicle->model->name ?? '' }}</div>
        <div class="price">{{ $vehicle->currency->symbol ?? '' }} {{ number_format($vehicle->price, 2) }}</div>
        <p style="margin: 5px 0;">{{ $vehicle->category->name ?? '' }} | {{ $vehicle->milage ?? 0 }} km |
            {{ $vehicle->first_registration ?? '' }}</p>
    </div>

    @if ($vehicle->photos && $vehicle->photos->first())
        <img src="{{ public_path($vehicle->photos->first()->image_url) }}" class="main-img">
    @endif

    <div class="section-title">Vehicle Specifications</div>
    <table>
        <tr>
            <td><span class="label">Condition:</span> <span
                    class="value">{{ $vehicle->data->condition->name ?? 'N/A' }}</span></td>
            <td><span class="label">Body Type:</span> <span
                    class="value">{{ $vehicle->body_type->title ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Fuel Type:</span> <span class="value">{{ $vehicle->fuel->title ?? 'N/A' }}</span>
            </td>
            <td><span class="label">Transmission:</span> <span
                    class="value">{{ $vehicle->transmission->title ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Exterior Color:</span> <span
                    class="value">{{ $vehicle->data->bodyColor->name ?? 'N/A' }} @if ($vehicle->data->metalic)
                        (Metalic)
                    @endif
                </span></td>
            <td><span class="label">Interior Color:</span> <span
                    class="value">{{ $vehicle->data->interiorColor->name ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Upholstery:</span> <span
                    class="value">{{ $vehicle->data->upholstery->name ?? 'N/A' }}</span></td>
            <td><span class="label">Seats / Doors:</span> <span
                    class="value">{{ $vehicle->data->numOfSeats->name ?? 'N/A' }} /
                    {{ $vehicle->data->numOfDoor->name ?? 'N/A' }}</span></td>
        </tr>
    </table>

    <div class="section-header section-title">Engine & Performance</div>
    <table>
        <tr>
            <td><span class="label">Engine Size:</span> <span
                    class="value">{{ $vehicle->engine_displacement ?? ($vehicle->engineAndEnvironment->engine_size ?? 'N/A') }}</span>
            </td>
            <td><span class="label">Power:</span> <span class="value">{{ $vehicle->power->title ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td><span class="label">Drive Type:</span> <span
                    class="value">{{ $vehicle->engineAndEnvironment->driverType->name ?? 'N/A' }}</span></td>
            <td><span class="label">Cylinders:</span> <span
                    class="value">{{ $vehicle->engineAndEnvironment->cylinders->name ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Emission Class:</span> <span
                    class="value">{{ $vehicle->engineAndEnvironment->emissionClass->name ?? 'N/A' }}</span></td>
            <td><span class="label">CO2 Emissions:</span> <span
                    class="value">{{ $vehicle->engineAndEnvironment->co2_emissions ?? 'N/A' }} g/km</span></td>
        </tr>
        <tr>
            <td><span class="label">Fuel Cons. (Comb):</span> <span
                    class="value">{{ $vehicle->engineAndEnvironment->fuel_consumption_combined ?? 'N/A' }}
                    L/100km</span></td>
            <td><span class="label">Kerb Weight:</span> <span
                    class="value">{{ $vehicle->engineAndEnvironment->kerb_weight ?? 'N/A' }} kg</span></td>
        </tr>
    </table>

    <div class="section-header section-title">Maintenance & History</div>
    <table>
        <tr>
            <td><span class="label">Service History:</span> <span
                    class="value">{{ $vehicle->conditionAndMaintenance->service_history ? 'Full' : 'N/A' }}</span>
            </td>
            <td><span class="label">Previous Owners:</span> <span
                    class="value">{{ $vehicle->data->previousOwner->name ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Non-Smoker:</span> <span
                    class="value">{{ $vehicle->conditionAndMaintenance->non_smoker_car ? 'Yes' : 'No' }}</span></td>
            <td><span class="label">Damaged:</span> <span
                    class="value">{{ $vehicle->conditionAndMaintenance->damaged_vehicle ? 'Yes' : 'No' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Technical Insp.:</span> <span
                    class="value">{{ $vehicle->conditionAndMaintenance->technical_inspection_valid_until ?? 'N/A' }}</span>
            </td>
            <td><span class="label">Guarantee:</span> <span
                    class="value">{{ $vehicle->conditionAndMaintenance->guarantee ? 'Yes' : 'No' }}</span></td>
        </tr>
    </table>

    <div class="section-header section-title">Seller Description</div>
    <div style="padding: 10px; border: 1px solid #f1f1f1; min-height: 60px;">
        {!! nl2br(e($vehicle->description)) !!}
    </div>

    <div class="section-header section-title">Seller Contact</div>
    <table>
        <tr>
            <td><span class="label">Seller Name:</span> <span
                    class="value">{{ $vehicle->contact_info['name'] ?? 'N/A' }}</span></td>
            <td><span class="label">Location:</span> <span
                    class="value">{{ $vehicle->contact_info['city_id'] ?? '' }},
                    {{ $vehicle->contact_info['street_details'] ?? '' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Phone:</span> <span
                    class="value">{{ $vehicle->contact_info['phone'] ?? 'N/A' }}</span></td>
            <td><span class="label">WhatsApp:</span> <span
                    class="value">{{ $vehicle->contact_info['whatsapp_number'] ?? 'N/A' }}</span></td>
        </tr>
    </table>

    <div class="footer">
        Generated by <strong>AutoMoto54</strong> on {{ date('d M, Y') }} | www.automoto54.com
    </div>

</body>

</html>
