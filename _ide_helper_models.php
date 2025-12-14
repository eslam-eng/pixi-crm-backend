<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $phone
 * @property string|null $locale
 * @property \App\Enums\Landlord\ActivationStatusEnum $is_active
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $deleted_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\AdminFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Admin withoutRole($roles, $guard = null)
 */
	class Admin extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Central{
/**
 * @property string $id
 * @property string $code
 * @property int $source_id
 * @property int $validity_days
 * @property string $status
 * @property int $plan_id
 * @property string|null $expired_at
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $redeemed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Central\Plan $plan
 * @property-read \App\Models\Central\Source $source
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\SourcePayoutItem> $sourcePayoutItems
 * @property-read int|null $source_payout_items_count
 * @property-read \App\Models\Central\Subscription|null $subscription
 * @property-read \App\Models\Central\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereRedeemedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereValidityDays($value)
 */
	class ActivationCode extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property string $discount_code
 * @property int $plan_id
 * @property string $discount_type
 * @property float $discount_percentage
 * @property int|null $users_limit
 * @property int|null $usage_limit
 * @property string|null $expires_at
 * @property \App\Enums\Landlord\ActivationStatusEnum $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Central\Plan $plan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\DiscountCodeUsage> $usages
 * @property-read int|null $usages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereDiscountCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereDiscountPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereUsersLimit($value)
 */
	class DiscountCode extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property-read \App\Models\Central\DiscountCode|null $discountCode
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCodeUsage filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCodeUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCodeUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCodeUsage query()
 */
	class DiscountCodeUsage extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property string $slug
 * @property array<array-key, mixed> $name
 * @property int $group is features for limits or for modules values from enum Feature Group
 * @property array<array-key, mixed>|null $description
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $active_slug
 * @property-read \App\Models\Central\FeaturePlan|\App\Models\Central\FeatureSubscription|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\Subscription> $featureSubscriptions
 * @property-read int|null $feature_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\Plan> $plans
 * @property-read int|null $plans_count
 * @property-read mixed $translations
 * @method static \Database\Factories\Central\FeatureFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereActiveSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature withoutTrashed()
 */
	class Feature extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property int $plan_id
 * @property int $feature_id
 * @property string $value
 * @property int $is_unlimited
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Central\Feature $feature
 * @property-read \App\Models\Central\Plan $plan
 * @method static \Database\Factories\Central\FeaturePlanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeaturePlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeaturePlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeaturePlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeaturePlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeaturePlan whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeaturePlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeaturePlan whereIsUnlimited($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeaturePlan wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeaturePlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeaturePlan whereValue($value)
 */
	class FeaturePlan extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property string $subscription_id
 * @property int $feature_id
 * @property string $slug
 * @property string $name
 * @property string $group
 * @property string $value
 * @property int $usage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Central\FeaturePlan|null $feature
 * @property-read \App\Models\Central\Subscription $subscription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription whereUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSubscription whereValue($value)
 */
	class FeatureSubscription extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property string $id
 * @property string $invoice_number
 * @property string $tenant_id
 * @property string|null $subscription_id
 * @property numeric $subtotal
 * @property numeric $tax_amount
 * @property numeric $discount_percentage
 * @property numeric $total
 * @property string $currency
 * @property \App\Enums\Landlord\InvoiceStatusEnum $status
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property array<array-key, mixed>|null $metadata
 * @property \App\Enums\Landlord\PaymentMethodEnum|null $payment_method
 * @property string|null $payment_reference
 * @property array<array-key, mixed>|null $billing_address
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\DiscountCodeUsage> $discountCodeUsage
 * @property-read int|null $discount_code_usage_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\InvoiceItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Central\Subscription|null $subscription
 * @property-read \App\Models\Central\Tenant $tenant
 * @property-read \App\Models\Central\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBillingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDiscountPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaymentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property string $invoice_id
 * @property string|null $description
 * @property int $quantity
 * @property numeric $unit_price
 * @property numeric $total
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $period_start
 * @property string|null $period_end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Central\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem wherePeriodEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem wherePeriodStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InvoiceItem whereUpdatedAt($value)
 */
	class InvoiceItem extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property-read \App\Models\Central\Invoice|null $invoice
 * @property-read \App\Models\Central\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 */
	class Payment extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property string|null $group
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutRole($roles, $guard = null)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property array<array-key, mixed>|null $description
 * @property string $currency_code
 * @property numeric|null $monthly_price
 * @property numeric|null $annual_price
 * @property numeric|null $lifetime_price
 * @property \App\Enums\Landlord\ActivationStatusEnum $is_active
 * @property int $sort_order
 * @property int $trial_days
 * @property int $refund_days
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Central\FeaturePlan|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\Feature> $addonFeatures
 * @property-read int|null $addon_features_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\Feature> $features
 * @property-read int|null $features_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\Feature> $limitFeatures
 * @property-read int|null $limit_features_count
 * @property-read mixed $translations
 * @method static \Database\Factories\Central\PlanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan trial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereAnnualPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereLifetimePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMonthlyPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereRefundDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereTrialDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan withoutTrashed()
 */
	class Plan extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Platform query()
 */
	class Platform extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Enums\Landlord\ActivationStatusEnum $is_active
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Admin> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 */
	class Role extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property-read \App\Models\Central\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLogin filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLogin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLogin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLogin query()
 */
	class SocialLogin extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property string $name
 * @property bool $is_active
 * @property numeric $payout_percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\SourcePayoutBatch> $payoutBatches
 * @property-read int|null $payout_batches_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source wherePayoutPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereUpdatedAt($value)
 */
	class Source extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property int $plan_id
 * @property int $source_id
 * @property string $total_amount
 * @property string $period_start
 * @property string $period_end
 * @property string|null $collected_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\SourcePayoutItem> $payoutItems
 * @property-read int|null $payout_items_count
 * @property-read \App\Models\Central\Plan $plan
 * @property-read \App\Models\Central\Source $source
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch whereCollectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch wherePeriodEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch wherePeriodStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutBatch whereUpdatedAt($value)
 */
	class SourcePayoutBatch extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property int $source_payout_batch_id
 * @property string $activation_code_id
 * @property string $payout_amount
 * @property string|null $collected_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Central\ActivationCode $activationCode
 * @property-read \App\Models\Central\SourcePayoutBatch|null $payoutBatch
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutItem filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutItem whereActivationCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutItem whereCollectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutItem wherePayoutAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutItem whereSourcePayoutBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SourcePayoutItem whereUpdatedAt($value)
 */
	class SourcePayoutItem extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property string $id
 * @property string $subscription_number
 * @property int $plan_id
 * @property \App\Enums\Landlord\SubscriptionStatusEnum $status active,canceled,expired,..
 * @property string $starts_at
 * @property string|null $ends_at
 * @property string|null $trial_ends_at
 * @property string|null $cancelled_at
 * @property string $amount
 * @property string $currency
 * @property bool $auto_renew
 * @property \App\Enums\Landlord\SubscriptionBillingCycleEnum|null $billing_cycle
 * @property array<array-key, mixed> $plan_snapshot
 * @property int $monthly_credit_tokens
 * @property string $tenant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $activation_code_id
 * @property-read mixed $days_left
 * @property-read mixed $ends_at_formatted
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\FeatureSubscription> $featureSubscriptions
 * @property-read int|null $feature_subscriptions_count
 * @property-read \App\Models\Central\FeatureSubscription|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\Feature> $features
 * @property-read int|null $features_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \App\Models\Central\Plan $plan
 * @property-read mixed $plan_name
 * @property-read mixed $starts_at_formatted
 * @property-read \App\Models\Central\Tenant $tenant
 * @property-read mixed $trial_ends_at_formatted
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription active(int $tenantId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereActivationCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereAutoRenew($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereBillingCycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereMonthlyCreditTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePlanSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereSubscriptionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property-read \App\Models\Central\Plan|null $fromPlan
 * @property-read \App\Models\Central\Plan|null $toPlan
 * @property-read \App\Models\Central\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionTransition filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionTransition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionTransition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionTransition query()
 */
	class SubscriptionTransition extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property string $id
 * @property string $name
 * @property array<array-key, mixed>|null $data
 * @property \App\Enums\Landlord\ActivationStatusEnum $status
 * @property int|null $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $has_used_trial
 * @property string|null $trial_plan_id
 * @property-read \App\Models\Central\Subscription|null $activeSubscription
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Stancl\Tenancy\Database\Models\Domain> $domains
 * @property-read int|null $domains_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \App\Models\Central\Subscription|null $latestSubscription
 * @property-read \App\Models\Central\User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\SubscriptionTransition> $subscriptionTransition
 * @property-read int|null $subscription_transition_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereHasUsedTrial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereTrialPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 */
	class Tenant extends \Eloquent implements \Stancl\Tenancy\Contracts\TenantWithDatabase {}
}

namespace App\Models\Central{
/**
 * @property-read \App\Models\Central\Platform|null $platform
 * @property-read \App\Models\Central\Tenant|null $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantPlatform filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantPlatform newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantPlatform newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantPlatform query()
 */
	class TenantPlatform extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantUser query()
 */
	class TenantUser extends \Eloquent {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Enums\Landlord\SupportedLocalesEnum $locale
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Central\SocialLogin> $socialLogins
 * @property-read int|null $social_logins_count
 * @property-read \App\Models\Central\Tenant|null $tenant
 * @property-read \Stancl\Tenancy\Database\TenantCollection<int, \App\Models\Central\Tenant> $tenants
 * @property-read int|null $tenants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Central{
/**
 * @property int $id
 * @property string $email
 * @property string $code
 * @property string $type
 * @property int $attempts
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereUpdatedAt($value)
 */
	class VerificationCode extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Country|null $country
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City query()
 */
	class City extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\City> $cities
 * @property-read int|null $cities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $is_active
 * @property string $payout_percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read mixed $image_url
 * @method static \Database\Factories\SourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source wherePayoutPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source whereUpdatedAt($value)
 */
	class Source extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Lead> $leads
 * @property-read int|null $leads_count
 * @property-read \App\Models\Tenant\Pipeline|null $pipeline
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stage filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stage query()
 */
	class Stage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Tenant\Reminder|null $reminder
 * @property-read \App\Models\Tenant\Task|null $task
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReminder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReminder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskReminder query()
 */
	class TaskReminder extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property array<array-key, mixed>|null $data
 * @property int $status
 * @property int|null $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $has_used_trial
 * @property string|null $trial_plan_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Stancl\Tenancy\Database\Models\Domain> $domains
 * @property-read int|null $domains_count
 * @property-read \App\Models\User|null $user
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereHasUsedTrial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereTrialPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 */
	class Tenant extends \Eloquent implements \Stancl\Tenancy\Contracts\TenantWithDatabase, \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Model $causer
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read \Illuminate\Database\Eloquent\Model $subject
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity forBatch(string $batchUuid)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity forEvent(string $event)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity hasBatch()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity inLog(...$logNames)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity visibleFor($user_id)
 */
	class Activity extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property mixed $value
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppSetting query()
 */
	class AppSetting extends \Eloquent {}
}

namespace App\Models\Tenant\Attendance{
/**
 * @property-read \App\Models\Tenant\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceDay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceDay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendanceDay query()
 */
	class AttendanceDay extends \Eloquent {}
}

namespace App\Models\Tenant\Attendance{
/**
 * @property-read \App\Models\Tenant\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendancePunch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendancePunch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AttendancePunch query()
 */
	class AttendancePunch extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationAction active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationAction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationAction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationAction ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationAction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationAction whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationAction whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationAction whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationAction whereLocales(string $column, array $locales)
 */
	class AutomationAction extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\AutomationStepsImplement|null $automationStepsImplement
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationDelay dueInMinutes($minutes = 5)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationDelay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationDelay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationDelay pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationDelay processed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationDelay query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationDelay readyToExecute()
 */
	class AutomationDelay extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\User|null $triggeredBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationLog byStatus(string $status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationLog byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationLog failed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationLog forEntity(string $entityType, int $entityId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationLog successful()
 */
	class AutomationLog extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\AutomationWorkflow|null $automationWorkflow
 * @property-read \App\Models\Tenant\AutomationWorkflowStep|null $automationWorkflowStep
 * @property-read \App\Models\Tenant\AutomationDelay|null $delay
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $triggerable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationStepsImplement forTriggerable($type, $id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationStepsImplement implemented()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationStepsImplement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationStepsImplement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationStepsImplement ofType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationStepsImplement ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationStepsImplement pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationStepsImplement query()
 */
	class AutomationStepsImplement extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\AutomationTriggerField> $fields
 * @property-read int|null $fields_count
 * @property-read mixed $translations
 * @property-read \App\Models\Tenant\AutomationWorkflow|null $workflow
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTrigger active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTrigger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTrigger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTrigger ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTrigger query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTrigger whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTrigger whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTrigger whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTrigger whereLocales(string $column, array $locales)
 */
	class AutomationTrigger extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property int $id
 * @property int $automation_trigger_id
 * @property string $field_name
 * @property string $field_type
 * @property string $field_label
 * @property string $field_category
 * @property bool $is_relationship
 * @property string|null $description
 * @property string|null $example_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tenant\AutomationTrigger|null $automationTrigger
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField directFields()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField relationshipFields()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereAutomationTriggerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereExampleValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereFieldCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereFieldLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereFieldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereIsRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationTriggerField whereUpdatedAt($value)
 */
	class AutomationTriggerField extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\AutomationTrigger|null $automationTrigger
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\AutomationWorkflowStep> $steps
 * @property-read int|null $steps_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflow active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflow query()
 */
	class AutomationWorkflow extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\AutomationWorkflowStepAction|null $action
 * @property-read \App\Models\Tenant\AutomationWorkflow|null $automationWorkflow
 * @property-read \App\Models\Tenant\AutomationWorkflowStepCondition|null $condition
 * @property-read \App\Models\Tenant\AutomationWorkflowStepDelay|null $delay
 * @property-read mixed $step_data
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStep ofType($type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStep ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStep query()
 */
	class AutomationWorkflowStep extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\AutomationAction|null $automationAction
 * @property-read \App\Models\Tenant\AutomationWorkflowStep|null $automationWorkflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStepAction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStepAction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStepAction query()
 */
	class AutomationWorkflowStepAction extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\AutomationWorkflowStep|null $automationWorkflowStep
 * @property-read \App\Models\Tenant\AutomationTriggerField|null $field
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStepCondition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStepCondition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStepCondition query()
 */
	class AutomationWorkflowStepCondition extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\AutomationWorkflowStep|null $automationWorkflowStep
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStepDelay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStepDelay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutomationWorkflowStepDelay query()
 */
	class AutomationWorkflowStepDelay extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ChairAchievement> $achievements
 * @property-read int|null $achievements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ChairTarget> $activeTargets
 * @property-read int|null $active_targets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $deals
 * @property-read int|null $deals_count
 * @property-read string $display_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ChairTarget> $monthlyTargets
 * @property-read int|null $monthly_targets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ChairTarget> $quarterlyTargets
 * @property-read int|null $quarterly_targets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ChairTarget> $targets
 * @property-read int|null $targets_count
 * @property-read \App\Models\Tenant\Team|null $team
 * @property-read \App\Models\Tenant\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chair active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chair activeAt($date)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chair filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chair individual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chair newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chair newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chair query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chair withTeam()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chair withoutTeam()
 */
	class Chair extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property \App\Enums\PeriodType $period_type
 * @property-read \App\Models\Tenant\Chair|null $chair
 * @property-read float $difference
 * @property-read string $period_string
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairAchievement achieved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairAchievement forPeriod(int $year, int $periodNumber)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairAchievement forYear(int $year)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairAchievement monthly()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairAchievement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairAchievement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairAchievement notAchieved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairAchievement ofType(\App\Enums\PeriodType $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairAchievement quarterly()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairAchievement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairAchievement yearly()
 */
	class ChairAchievement extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property \App\Enums\PeriodType $period_type
 * @property-read \App\Models\Tenant\Chair|null $chair
 * @property-read string $period_string
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairTarget active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairTarget forDate($date)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairTarget forPeriod(int $year, int $periodNumber)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairTarget forYear(int $year)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairTarget monthly()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairTarget ofType(\App\Enums\PeriodType $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairTarget quarterly()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairTarget query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChairTarget yearly()
 */
	class ChairTarget extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\Lead|null $activeLead
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ContactMerge> $contactMerges
 * @property-read int|null $contact_merges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ContactPhone> $contactPhones
 * @property-read int|null $contact_phones_count
 * @property-read \App\Models\Country|null $country
 * @property-read \App\Models\Tenant\Department|null $department
 * @property-read mixed $name
 * @property-read \App\Models\Tenant\ContactPhone|null $phone
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Lead> $leads
 * @property-read int|null $leads_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Source|null $source
 * @property-read \App\Models\Tenant\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact withAnyTag($tags)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact withTag($tag)
 */
	class Contact extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property \App\Enums\MergeContactType $merge_status
 * @property \App\Enums\IdenticalContactType $identical_contact_type
 * @property-read \App\Models\Tenant\Contact|null $contact
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ContactMergePhone> $contactMergePhones
 * @property-read int|null $contact_merge_phones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMerge filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMerge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMerge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMerge query()
 */
	class ContactMerge extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\ContactMerge|null $contactMerge
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMergePhone filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMergePhone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMergePhone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMergePhone query()
 */
	class ContactMergePhone extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\Contact|null $contact
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactPhone filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactPhone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactPhone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactPhone query()
 */
	class ContactPhone extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $deals
 * @property-read int|null $deals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Lead> $leads
 * @property-read int|null $leads_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField query()
 */
	class CustomField extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\User|null $assigned_to
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\DealAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\Tenant\User|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\DealItem> $deal_items
 * @property-read int|null $deal_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Item> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Tenant\Lead|null $lead
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\DealPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ItemVariant> $variants
 * @property-read int|null $variants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deal filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deal query()
 */
	class Deal extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\Deal|null $deal
 * @property-read string|null $file_url
 * @property-read string|null $preview_url
 * @property-read string|null $thumbnail_url
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Media|null $mediaFile
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealAttachment query()
 */
	class DealAttachment extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\Deal|null $deal
 * @property-read \App\Models\Tenant\Item|null $item
 * @property-read \App\Models\Tenant\DealItemSubscription|null $subscription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItem query()
 */
	class DealItem extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property \App\Enums\BillingCycleEnum $billing_cycle
 * @property-read \App\Models\Tenant\DealItem|null $dealItem
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItemSubscription active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItemSubscription byBillingCycle(\App\Enums\BillingCycleEnum $cycle)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItemSubscription expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItemSubscription filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItemSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItemSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItemSubscription query()
 */
	class DealItemSubscription extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\Deal|null $deal
 * @property-read \App\Models\Tenant\PaymentMethod|null $payment_method
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealPayment query()
 */
	class DealPayment extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\Deal|null $deal
 * @property-read \App\Models\Tenant\ItemVariant|null $variant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealVariant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealVariant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealVariant query()
 */
	class DealVariant extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $localized_name
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 */
	class Department extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\FormField> $conditionalFields
 * @property-read int|null $conditional_fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\FormField> $fields
 * @property-read int|null $fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\FormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\FormField> $unconditionalFields
 * @property-read int|null $unconditional_fields_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form query()
 */
	class Form extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\Form|null $form
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormAction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormAction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormAction query()
 */
	class FormAction extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read FormField|null $dependsOn
 * @property-read \App\Models\Tenant\Form|null $form
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormField query()
 */
	class FormField extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\Form|null $form
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission query()
 */
	class FormSubmission extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property \App\Enums\PlatformEnum $platform
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\IntegratedFormFieldMapping> $fieldMappings
 * @property-read int|null $field_mappings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegratedForm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegratedForm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegratedForm query()
 */
	class IntegratedForm extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\IntegratedForm|null $formMapping
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegratedFormFieldMapping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegratedFormFieldMapping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IntegratedFormFieldMapping query()
 */
	class IntegratedFormFieldMapping extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property \App\Enums\IntegrationStatusEnum $status
 * @property \App\Enums\PlatformEnum $platform
 * @property-read int|null $token_expires_in
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integration query()
 */
	class Integration extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\ItemCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $deals
 * @property-read int|null $deals_count
 * @property-read mixed $is_product
 * @property-read mixed $is_service
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $itemable
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Lead> $opportunities
 * @property-read int|null $opportunities_count
 * @property-read \App\Models\Tenant\Product|null $product
 * @property-read \App\Models\Tenant\Service|null $service
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ItemVariant> $variants
 * @property-read int|null $variants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item query()
 */
	class Item extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ItemAttributeValue> $values
 * @property-read int|null $values_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttribute query()
 */
	class ItemAttribute extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\ItemAttribute|null $attribute
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ItemVariant> $variants
 * @property-read int|null $variants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttributeValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttributeValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemAttributeValue query()
 */
	class ItemAttributeValue extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ItemCategory> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Item> $items
 * @property-read int|null $items_count
 * @property-read ItemCategory|null $parent
 * @method static \Database\Factories\Tenant\ItemCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCategory filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCategory ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemCategory roots()
 */
	class ItemCategory extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Item> $items
 * @property-read int|null $items_count
 * @method static \Database\Factories\Tenant\ItemStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemStatus filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemStatus query()
 */
	class ItemStatus extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ItemAttributeValue> $attributeValues
 * @property-read int|null $attribute_values_count
 * @property-read \App\Models\Tenant\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVariant filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVariant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVariant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVariant ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemVariant query()
 */
	class ItemVariant extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property \App\Enums\OpportunityStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Tenant\Contact|null $contact
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\CustomField> $customFields
 * @property-read int|null $custom_fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Item> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Tenant\LossReason|null $reason
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Service> $services
 * @property-read int|null $services_count
 * @property-read \App\Models\Stage|null $stage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Stage> $stages
 * @property-read int|null $stages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \App\Models\Tenant\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ItemVariant> $variants
 * @property-read int|null $variants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead query()
 */
	class Lead extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\Tenant{
/**
 * @property \App\Enums\ActivationStatus $status
 * @property-read \Kalnoy\Nestedset\Collection<int, Location> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read Location|null $parent
 * @property-write mixed $parent_id
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location active()
 * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location ancestorsAndSelf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location ancestorsOf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location applyNestedSetScope(?string $table = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location cities()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location countErrors()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location countries()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location d()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location defaultOrder(string $dir = 'asc')
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location descendantsAndSelf($id, array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location descendantsOf($id, array $columns = [], $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location fixSubtree($root)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location fixTree($root = null)
 * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location getNodeData($id, $required = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location getPlainNodeData($id, $required = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location getTotalErrors()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location governorates()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location hasChildren()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location hasParent()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location isBroken()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location leaves(array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location makeGap(int $cut, int $height)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location moveNode($key, $position)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location newModelQuery()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location newQuery()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location orWhereAncestorOf(bool $id, bool $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location orWhereDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location orWhereNodeBetween($values)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location orWhereNotDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location query()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location rebuildSubtree($root, array $data, $delete = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location rebuildTree(array $data, $delete = false, $root = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location reversed()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location root(array $columns = [])
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location whereAncestorOf($id, $andSelf = false, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location whereAncestorOrSelf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location whereIsAfter($id, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location whereIsBefore($id, $boolean = 'and')
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location whereIsLeaf()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location whereIsRoot()
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location whereNodeBetween($values, $boolean = 'and', $not = false, $query = null)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location whereNotDescendantOf($id)
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location withDepth(string $as = 'depth')
 * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Location withoutRoot()
 */
	class Location extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\Pipeline|null $pipeline
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LossReason filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LossReason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LossReason newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LossReason query()
 */
	class LossReason extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $deals
 * @property-read int|null $deals_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod query()
 */
	class PaymentMethod extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Stage|null $firstStage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\LossReason> $lossReasons
 * @property-read int|null $loss_reasons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Stage> $stages
 * @property-read int|null $stages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pipeline filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pipeline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pipeline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pipeline query()
 */
	class Pipeline extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\PriorityColor|null $color
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Priority default()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Priority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Priority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Priority ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Priority query()
 */
	class Priority extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Priority> $priorities
 * @property-read int|null $priorities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriorityColor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriorityColor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriorityColor ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PriorityColor query()
 */
	class PriorityColor extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\ItemCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $deals
 * @property-read int|null $deals_count
 * @property-read \App\Models\Tenant\Item|null $item
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Lead> $opportunities
 * @property-read int|null $opportunities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ItemVariant> $variants
 * @property-read int|null $variants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 */
	class Product extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read string $display_name
 * @property-read mixed $localized_name
 * @property-read int $total_minutes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TaskReminder> $taskReminders
 * @property-read int|null $task_reminders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder default()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder whereLocales(string $column, array $locales)
 */
	class Reminder extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ReportExecution> $executions
 * @property-read int|null $executions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ReportExecution> $latestExecution
 * @property-read int|null $latest_execution_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report scheduled()
 */
	class Report extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\User|null $executedBy
 * @property-read int $duration
 * @property-read \App\Models\Tenant\Report|null $report
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportExecution failed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportExecution filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportExecution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportExecution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportExecution query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportExecution running()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportExecution successful()
 */
	class ReportExecution extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property \App\Enums\ServiceType $service_type
 * @property \App\Enums\ServiceDuration $duration
 * @property-read \App\Models\Tenant\ItemCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $deals
 * @property-read int|null $deals_count
 * @property-read \App\Models\Tenant\Item|null $item
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Lead> $opportunities
 * @property-read int|null $opportunities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ItemVariant> $variants
 * @property-read int|null $variants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service query()
 */
	class Service extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\Deal|null $deal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionDetail query()
 */
	class SubscriptionDetail extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\User> $followers
 * @property-read int|null $followers_count
 * @property-read \App\Models\Tenant\Lead|null $lead
 * @property-read \App\Models\Tenant\Priority|null $priority
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Reminder> $reminders
 * @property-read int|null $reminders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TaskReminder> $taskReminders
 * @property-read int|null $task_reminders_count
 * @property-read \App\Models\Tenant\TaskType|null $taskType
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 */
	class Task extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskType query()
 */
	class TaskType extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property int $id
 * @property string $title
 * @property int|null $leader_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Chair> $activeChairs
 * @property-read int|null $active_chairs_count
 * @property-read \App\Models\Tenant\Chair|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\User> $activeUsers
 * @property-read int|null $active_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Chair> $chairs
 * @property-read int|null $chairs_count
 * @property-read \App\Models\Tenant\Chair|null $currentChair
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $deals
 * @property-read int|null $deals_count
 * @property-read \App\Models\Tenant\User|null $leader
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\User> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\User> $sales
 * @property-read int|null $sales_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ChairTarget> $year_total
 * @property-read int|null $year_total_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereLeaderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team withTargets()
 */
	class Team extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template email()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template whatsapp()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template withoutTrashed()
 */
	class Template extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tenant\Chair|null $activeChair
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Chair> $activeChairs
 * @property-read int|null $active_chairs_count
 * @property-read \App\Models\Tenant\Chair|null $activeIndividualChair
 * @property-read \App\Models\Tenant\Chair|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Team> $activeTeams
 * @property-read int|null $active_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $assignedDeals
 * @property-read int|null $assigned_deals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $chairDeals
 * @property-read int|null $chair_deals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Chair> $chairs
 * @property-read int|null $chairs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $deals
 * @property-read int|null $deals_count
 * @property-read \App\Models\Tenant\Department|null $department
 * @property string $image
 * @property-read string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Chair> $individualChairs
 * @property-read int|null $individual_chairs_count
 * @property-read \App\Models\Tenant\Attendance\AttendancePunch|null $latestAttendancePunch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Lead> $leads
 * @property-read int|null $leads_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\UserTarget> $targets
 * @property-read int|null $targets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \App\Models\Tenant\Team|null $team
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Chair> $teamChairs
 * @property-read int|null $team_chairs_count
 * @property-read \App\Models\Tenant\Team|null $teamManager
 * @property-read \App\Models\Central\Tenant|null $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTarget forMonth($date)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserTarget query()
 */
	class UserTarget extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $name
 * @property-read \App\Models\Tenant|null $tenant
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

