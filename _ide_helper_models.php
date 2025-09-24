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
 * @property string $code
 * @property int $tier_id
 * @property string $source
 * @property string $status
 * @property int $trial_days
 * @property string $expires_at
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property string|null $deleted_at
 * @property int $create_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $createBy
 * @property-read bool $is_expired
 * @property-read bool $is_used
 * @property-read bool $is_valid
 * @property-read \App\Models\Tenant|null $tenant
 * @property-read \App\Models\Tier $tier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode expired()
 * @method static \Database\Factories\ActivationCodeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode forModuleType(string $moduleType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode used()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode valid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereCreateBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereTierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereTrialDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivationCode whereUsedAt($value)
 */
	class ActivationCode extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Service|null $service
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $country_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Country $country
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereUpdatedAt($value)
 */
	class City extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $stripe_id
 * @property string $company_name
 * @property string|null $contact_name
 * @property string $contact_email
 * @property string $contact_phone
 * @property string|null $job_title
 * @property string|null $website
 * @property string $subdomain
 * @property string|null $company_size
 * @property string|null $industry
 * @property int $city_id
 * @property string|null $postal_code
 * @property string|null $address
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location|null $area
 * @property-read \App\Models\Source|null $source
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Database\Factories\ClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client hasExpiredGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client onGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCompanySize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereSubdomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereWebsite($value)
 */
	class Client extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\City> $cities
 * @property-read int|null $cities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereUpdatedAt($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $deals
 * @property-read int|null $deals_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomField query()
 */
	class CustomField extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property int $tier_id
 * @property string $source
 * @property string $status
 * @property int $trial_days
 * @property string $expires_at
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property string $discount_percentage
 * @property string $usage_type
 * @property int|null $max_uses
 * @property string|null $deleted_at
 * @property int $create_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $createBy
 * @property-read bool $is_expired
 * @property-read bool $is_used
 * @property-read bool $is_valid
 * @property-read \App\Models\Tenant|null $tenant
 * @property-read \App\Models\Tier $tier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode forModuleType(string $moduleType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode used()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode valid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereCreateBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereDiscountPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereMaxUses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereTierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereTrialDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereUsageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscountCode whereUsedAt($value)
 */
	class DiscountCode extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string|null $device_type
 * @property string|null $device_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FcmToken whereUserId($value)
 */
	class FcmToken extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Lead> $leads
 * @property-read int|null $leads_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry whereLocales(string $column, array $locales)
 */
	class Industry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property int $subscription_id
 * @property string $amount
 * @property string $status
 * @property string $due_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client|null $client
 * @property-read \App\Models\Subscription|null $subscription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property \App\Enums\ActivationStatus $status
 * @property-read \Kalnoy\Nestedset\Collection<int, Location> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $clients
 * @property-read int|null $clients_count
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

namespace App\Models{
/**
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $key
 * @property string $group
 * @property array<array-key, mixed> $group_label
 * @property int $has_number_field
 * @property array<array-key, mixed>|null $number_field_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $clients
 * @property-read int|null $clients_count
 * @property-read mixed $localized_group_label
 * @property-read mixed $localized_name
 * @property-read mixed $localized_number_field_label
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereGroupLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereHasNumberField($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereNumberFieldLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereUpdatedAt($value)
 */
	class Module extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason query()
 */
	class Reason extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $clients
 * @property-read int|null $clients_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service query()
 */
	class Service extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property array<array-key, mixed>|null $sources
 * @property array<array-key, mixed>|null $departments
 * @property array<array-key, mixed>|null $industries
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDepartments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereIndustries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSources($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read mixed $image_url
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Database\Factories\SourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Source query()
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
 * @property int $id
 * @property int $client_id
 * @property int $tier_id
 * @property \Illuminate\Support\Carbon|null $subscription_start_date
 * @property \Illuminate\Support\Carbon|null $subscription_end_date
 * @property string $subscription_status
 * @property string $activition_method
 * @property string|null $source
 * @property string $auto_renew
 * @property \App\Enums\landlord\PaymentStatus $payment_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Tier $tier
 * @method static \Database\Factories\SubscriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereActivitionMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereAutoRenew($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereSubscriptionEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereSubscriptionStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereSubscriptionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereTierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 */
	class Subscription extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
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
 * @property-read \App\Models\User|null $leader
 * @property-read \App\Models\Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $sales
 * @property-read int|null $sales_count
 * @property-read \App\Models\Source|null $source
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 */
	class Team extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property int|null $client_id
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Stancl\Tenancy\Database\Models\Domain> $domains
 * @property-read int|null $domains_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tier> $tiers
 * @property-read int|null $tiers_count
 * @property-read \App\Models\User|null $user
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 */
	class Tenant extends \Eloquent implements \Stancl\Tenancy\Contracts\TenantWithDatabase, \Spatie\MediaLibrary\HasMedia {}
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

namespace App\Models\Tenant{
/**
 * @property-read \App\Models\Tenant\Lead|null $activeLead
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ContactPhone> $contactPhones
 * @property-read int|null $contact_phones_count
 * @property-read \App\Models\Country|null $country
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Lead> $leads
 * @property-read int|null $leads_count
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
 * @property-read \App\Models\Tenant\Lead|null $activeLead
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ContactPhone> $contactPhones
 * @property-read int|null $contact_phones_count
 * @property-read \App\Models\Country|null $country
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Lead> $leads
 * @property-read int|null $leads_count
 * @property-read \App\Models\Source|null $source
 * @property-read \App\Models\Tenant\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMerge filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMerge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMerge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMerge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMerge withAnyTag($tags)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMerge withTag($tag)
 */
	class ContactMerge extends \Eloquent {}
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
 * @property-read \App\Models\Tenant\Contact|null $contact
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Item> $items
 * @property-read int|null $items_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Stage|null $stage
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
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DealItem query()
 */
	class DealItem extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read mixed $localized_name
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereLocales(string $column, array $locales)
 */
	class Department extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\FormAction> $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\FormField> $fields
 * @property-read int|null $fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\FormSubmission> $submissions
 * @property-read int|null $submissions_count
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
 * @property \App\Enums\ServiceDuration $duration
 * @property \App\Enums\ItemType $type
 * @property \App\Enums\ServiceType $service_type
 * @property-read \App\Models\Tenant\ItemCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Deal> $deals
 * @property-read int|null $deals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\Lead> $opportunities
 * @property-read int|null $opportunities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant\ItemVariant> $variants
 * @property-read int|null $variants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item filter(\App\Abstracts\QueryFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item query()
 */
	class Item extends \Eloquent {}
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
 * @property-read \App\Models\Tenant\Item|null $item
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomField> $customFields
 * @property-read int|null $custom_fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Industry> $industries
 * @property-read int|null $industries_count
 * @property-read \App\Models\Reason|null $reason
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service> $services
 * @property-read int|null $services_count
 * @property-read \App\Models\Stage|null $stage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Stage> $stages
 * @property-read int|null $stages_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod query()
 */
	class PaymentMethod extends \Eloquent {}
}

namespace App\Models\Tenant{
/**
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
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property-read \App\Models\Tenant\Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcmToken> $fcm_tokens
 * @property-read int|null $fcm_tokens_count
 * @property string $image
 * @property-read string $name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePmLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $package_name
 * @property string|null $description
 * @property float $price
 * @property string $duration_unit
 * @property int $duration
 * @property int $refund_period
 * @property int|null $max_users
 * @property int $max_contacts
 * @property int $storage_limit
 * @property string $status
 * @property string $availability
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed $modules
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Stancl\Tenancy\Database\TenantCollection<int, \App\Models\Tenant> $tenant
 * @property-read int|null $tenant_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TierModule> $tier_modules
 * @property-read int|null $tier_modules_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier active()
 * @method static \Database\Factories\TierFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereAvailability($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereDurationUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereMaxContacts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereMaxUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier wherePackageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereRefundPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereStorageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tier withModule(\App\Enums\ModuleType $module)
 */
	class Tier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tier_id
 * @property int $module_id
 * @property int|null $limit_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Module $module
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TierModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TierModule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TierModule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TierModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TierModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TierModule whereLimitValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TierModule whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TierModule whereTierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TierModule whereUpdatedAt($value)
 */
	class TierModule extends \Eloquent {}
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
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FcmToken> $fcm_tokens
 * @property-read int|null $fcm_tokens_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Tenant|null $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePmLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

