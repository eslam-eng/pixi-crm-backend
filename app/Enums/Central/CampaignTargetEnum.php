<?php

namespace App\Enums\Central;

enum CampaignTargetEnum: int
{
    case ALL_CUSTOMERS = 1;

    case SPECIFIC_SEGMENT = 2;

    case SPECIFIC_CUSTOMERS = 3;

}
