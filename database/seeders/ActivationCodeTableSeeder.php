<?php

namespace Database\Seeders;

use App\DTO\Central\ActivationCodeDTO;
use App\Enums\Landlord\ActivationCodeStatusEnum;
use App\Models\Central\ActivationCode;
use App\Models\Central\Plan;
use App\Models\Central\Source;
use App\Models\Central\User;
use App\Services\Central\ActivationCode\ActivationCodeService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActivationCodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activationCodeDTO = new ActivationCodeDTO(
            planId: Plan::query()->inRandomOrder()->first()->id,
            source_id: Source::query()->inRandomOrder()->first()->id,
            validityDays: 30,
            count: 30,
            parts: 3,
            partLength: 3,
            status: ActivationCodeStatusEnum::AVAILABLE->value
        );

        app(ActivationCodeService::class)->generate($activationCodeDTO);

        $activationCodes = ActivationCode::query()->inRandomOrder()->limit(15)->get();
        foreach ($activationCodes as $activationCode) {
            $randomDate = Carbon::now()->subDays(rand(0, 30)) // go back up to 30 days randomly
                ->subMinutes(rand(0, 1440)); // also random minutes within the day

            $activationCode->update([
                'status' => ActivationCodeStatusEnum::USED->value,
                'redeemed_at' => $randomDate->toDateTimeString(),
                'user_id' => User::query()->inRandomOrder()->first()->id,
            ]);
        }
    }
}
