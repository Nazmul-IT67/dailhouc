<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function uploadImage($file, $folder)
{
    if (!$file->isValid()) {
        return null;
    }

    $imageName = Str::slug(time()) . rand() . '.' . $file->extension();
    $path      = public_path('uploads/' . $folder);
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
    $file->move($path, $imageName);
    return 'uploads/' . $folder . '/' . $imageName;
}
if (! function_exists('convertPrice')) {
    function convertPrice($price, $fromCurrencyCode, $toCurrencyCode)
    {
        $fromCurrency = \App\Models\Currency::where('code', $fromCurrencyCode)->first();
        $toCurrency = \App\Models\Currency::where('code', $toCurrencyCode)->first();

        if (!$fromCurrency || !$toCurrency) {
            return $price;
        }

        // Base → Target conversion
        $usdPrice = $price / $fromCurrency->exchange_rate;
        return $usdPrice * $toCurrency->exchange_rate;
    }
}
