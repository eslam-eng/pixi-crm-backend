<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;

class CountryCodeController extends Controller
{
    public function __invoke()
    {
        $file = database_path('top_100_country_phone_codes.json');

        $jsonContents = file_get_contents($file);

        $countryCodes = json_decode($jsonContents, true); // decode as associative array

        return ApiResponse::success(data: $countryCodes);
    }
}
