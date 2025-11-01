<?php

namespace App\Enums\Landlord;

enum LandlordPermissionsEnum: string
{
    case LIST_ADMINS = 'list_admins';
    case CREATE_ADMIN = 'create_admin';
    case UPDATE_ADMIN = 'update_admin';
    case DELETE_ADMIN = 'delete_admin';

    case LIST_ACTIVATION_CODES = 'list_activation_codes';
    case CREATE_ACTIVATION_CODE = 'create_activation_code';
    case UPDATE_ACTIVATION_CODE = 'update_activation_code';
    case DELETE_ACTIVATION_CODE = 'delete_activation_code';

    case LIST_DISCOUNT_CODES = 'list_discount_codes';
    case CREATE_DISCOUNT_CODE = 'create_discount_code';
    case UPDATE_DISCOUNT_CODE = 'update_discount_code';
    case DELETE_DISCOUNT_CODE = 'delete_discount_code';

    case LIST_CLIENTS = 'list_clients';
    case CREATE_CLIENT = 'create_client';
    case UPDATE_CLIENT = 'update_client';
    case DELETE_CLIENT = 'delete_client';

    case LIST_PACKAGES = 'list_packages';
    case CREATE_PACKAGE = 'create_package';
    case UPDATE_PACKAGE = 'update_package';
    case DELETE_PACKAGE = 'delete_package';

    case LIST_SUBSCRIPTIONS = 'list_subscriptions';
    case CREATE_SUBSCRIPTION = 'create_subscription';

    case LIST_INVOICES = 'list_invoices';

    case SHOW_INVOICE = 'show_invoice';
    case PRINT_INVOICE = 'print_invoice';
    case DOWNLOAD_INVOICE = 'download_invoice';

    /**
     * Get the group name for the permission
     */
    public function getGroup(): string
    {
        return match ($this) {
            self::LIST_ADMINS,
            self::CREATE_ADMIN,
            self::UPDATE_ADMIN,
            self::DELETE_ADMIN, => 'admins',

            self::LIST_ACTIVATION_CODES,
            self::CREATE_ACTIVATION_CODE,
            self::UPDATE_ACTIVATION_CODE,
            self::DELETE_ACTIVATION_CODE, => 'activation_codes',

            self::LIST_DISCOUNT_CODES,
            self::CREATE_DISCOUNT_CODE,
            self::UPDATE_DISCOUNT_CODE,
            self::DELETE_DISCOUNT_CODE, => 'discount_codes',

            self::LIST_CLIENTS,
            self::CREATE_CLIENT,
            self::UPDATE_CLIENT,
            self::DELETE_CLIENT => 'clients',
            self::LIST_PACKAGES,
            self::CREATE_PACKAGE,
            self::UPDATE_PACKAGE,
            self::DELETE_PACKAGE => 'packages',

            self::LIST_SUBSCRIPTIONS,
            self::CREATE_SUBSCRIPTION,
            self::LIST_INVOICES,
            self::SHOW_INVOICE,
            self::PRINT_INVOICE,
            self::DOWNLOAD_INVOICE, => 'subscriptions',
        };
    }

    /**
     * Get all permissions grouped by their group
     */
    public static function grouped(): array
    {
        $grouped = [];
        foreach (self::cases() as $permission) {
            $grouped[$permission->getGroup()][] = [
                'name' => $permission->value,
                'label' => $permission->getLabel(),
            ];
        }

        return $grouped;
    }

    /**
     * Get the translated label for the permission
     */
    public function getLabel(): string
    {
        return __('permissions.' . $this->value);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
