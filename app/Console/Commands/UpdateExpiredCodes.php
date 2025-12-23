<?php

namespace App\Console\Commands;

use App\Models\Central\ActivationCode;
use App\Models\Central\DiscountCode;
use App\Enums\Landlord\ActivationCodeStatusEnum;
use Illuminate\Console\Command;

class UpdateExpiredCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codes:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark activation codes and discount codes as expired if they reached their expiration date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        // Expire ActivationCodes
        $expiredActivationCodesCount = ActivationCode::query()
            ->where('status', ActivationCodeStatusEnum::AVAILABLE->value)
            ->where('expired_at', '<=', $now)
            ->update(['status' => ActivationCodeStatusEnum::EXPIRED->value]);

        $this->info("Expired {$expiredActivationCodesCount} activation codes.");

        // Expire DiscountCodes
        $expiredDiscountCodesCount = DiscountCode::query()
            ->where('status', ActivationCodeStatusEnum::AVAILABLE->value)
            ->where('expires_at', '<=', $now)
            ->update(['status' => ActivationCodeStatusEnum::EXPIRED->value]);

        $this->info("Expired {$expiredDiscountCodesCount} discount codes.");
    }
}
