<?php

namespace App\Models\Central;

use App\Enums\Landlord\ActivationStatusEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Traits\Filterable;
use App\Traits\HasFeatureLimits;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
// use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Support\Facades\Artisan;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;



class Tenant extends BaseTenant implements TenantWithDatabase
{
    // use Filterable, HasFeatureLimits, HasUuids, SoftDeletes, UsesLandlordConnection;
    use  Filterable, HasFeatureLimits, HasUuids, HasDatabase, HasDomains;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'status',
        'owner_id',
        'has_used_trial',
        'trial_plan_id',
    ];


    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'owner_id',
        ];
    }

    protected $casts = [
        'status' => ActivationStatusEnum::class,
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // todo return to in when use pivot table for multiple access per user for multiple tenants
    //    public function users()
    //    {
    //        return $this->belongsToMany(User::class, 'tenant_users')
    //            ->withPivot('is_owner')
    //            ->withTimestamps()
    //            ->using(TenantUser::class);
    //
    //    }

    /**
     * Get all subscriptions for this tenant
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscriptionTransition(): HasMany|Tenant
    {
        return $this->hasMany(SubscriptionTransition::class);
    }

    /**
     * Get current active subscription
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->whereIn('status', [SubscriptionStatusEnum::ACTIVE->value, SubscriptionStatusEnum::TRIAL->value]); // or whatever your active status enum/value is
    }

    //    public function activeSubscription(): ?object
    //    {
    //        return $this->subscriptions()
    //            ->whereNotIn('status', SubscriptionStatusEnum::inactive())
    //            ->where('ends_at', '>', now())
    //            ->latest('starts_at')
    //            ->first();
    //    }

    // Get owner user directly through pivot
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
        //        return $this->belongsTo(User::class, 'user_id')
        //            ->join('tenant_users', 'users.id', '=', 'tenant_users.user_id')
        //            ->where('tenant_users.tenant_id', $this->id)
        //            ->where('tenant_users.is_owner', true);
    }

    /**
     * Get current plan
     */
    public function plan()
    {
        return $this->activeSubscription()?->plan;
    }

    /**
     * Get tenant invoices
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // protected static function boot(): void
    // {
    //     parent::boot();

    //     static::creating(function ($tenant) {
    //         // Generate database name if not provided
    //         if (empty($tenant->database)) {
    //             $ulid = (string) Str::ulid(); // e.g., '01HZG8Z8X1CWVRYKX84Z7KT8AZ'
    //             $lastFive = substr($ulid, -5); // e.g., '8AZ'
    //             $tenant->database = 'tenant_' . Str::slug($tenant->name) . '_' . $lastFive;
    //         }
    //     });

    //     static::created(function ($tenant) {
    //         $tenant->makeCurrent();

    //         if (app()->environment('local')) {
    //             // Create the tenant database
    //             static::createDatabase($tenant->database);
    //             // Set the current tenant to the newly created tenant
    //             // Run migrations for the tenant
    //             Artisan::call('migrate:fresh', [
    //                 '--database' => 'tenant',
    //                 '--force' => true,
    //             ]);
    //         } else {
    //             static::cloneFromTemplate($tenant->database);
    //             // 4. Run seeders for tenant
    //             Artisan::call('db:seed', [
    //                 '--class' => 'TenantDatabaseSeeder',
    //                 '--database' => 'tenant',
    //                 '--force' => true,
    //             ]);
    //         }
    //     });
    // }

    // public static function cloneFromTemplate($database_name): bool
    // {
    //     // Create new database
    //     DB::statement("CREATE DATABASE IF NOT EXISTS `$database_name`");

    //     // Get all tables from template
    //     $templateDb = config('database.tenant_template_db');

    //     $tables = DB::select("SHOW TABLES FROM `$templateDb`");

    //     foreach ($tables as $table) {
    //         $tableName = array_values((array) $table)[0];
    //         // Clone table structure and data
    //         DB::statement("CREATE TABLE `$database_name`.`$tableName` LIKE `$templateDb`.`$tableName`");
    //     }

    //     return true;
    // }

    public function latestSubscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany(); // latest active
    }

    public function activeSubscriptions(): Builder|HasMany
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            });
    }

    // public static function createDatabase($database_name): bool
    // {
    //     return DB::statement("CREATE DATABASE IF NOT EXISTS `$database_name`");
    // }

    // public function hasHadTrial()
    // {
    //     return $this->has_used_trial;
    // }

    /**
     * Mark user as having used their free trial
     */
    public function markTrialUsed(?int $planId = null): void
    {
        $this->update([
            'has_used_trial' => true,
            'trial_plan_id' => $planId,
        ]);
    }
}
