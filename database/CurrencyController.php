<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{
    public function __invoke()
    {
        $file = database_path('currencies.json');

        $jsonContents = file_get_contents($file);

        $countryCodes = json_decode($jsonContents, true); // decode as associative array

        return ApiResponse::success(data: $countryCodes);
    }
}
