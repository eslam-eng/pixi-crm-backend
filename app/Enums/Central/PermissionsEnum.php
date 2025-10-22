<?php

namespace App\Enums\Central;

enum PermissionsEnum: string
{
    // Contact Permissions
    case LIST_CONTACTS = 'list_contacts';
    case CREATE_CONTACT = 'create_contact';
    case VIEW_CONTACT = 'view_contact';
    case EDIT_CONTACT = 'edit_contact';
    case DELETE_CONTACT = 'delete_contact';
    case EXPORT_CONTACT = 'export_contact';
    case IMPORT_CONTACT = 'import_contact';

    // Order Permissions
    case LIST_ORDERS = 'list_orders';
    case CREATE_ORDER = 'create_order';
    case VIEW_ORDER = 'view_order';
    case EDIT_ORDER = 'edit_order';
    case DELETE_ORDER = 'delete_order';

    // Product Permissions
    case LIST_PRODUCTS = 'list_products';
    case CREATE_PRODUCT = 'create_product';
    case VIEW_PRODUCT = 'view_product';
    case EDIT_PRODUCT = 'edit_product';
    case DELETE_PRODUCT = 'delete_product';

    // Role Permissions
    case LIST_ROLES = 'list_roles';
    case CREATE_ROLE = 'create_role';
    case VIEW_ROLE = 'view_role';
    case EDIT_ROLE = 'edit_role';
    case DELETE_ROLE = 'delete_role';

    /**
     * Get the group name for the permission
     */
    public function getGroup(): string
    {
        return match ($this) {
            self::LIST_CONTACTS,
            self::CREATE_CONTACT,
            self::VIEW_CONTACT,
            self::EDIT_CONTACT,
            self::DELETE_CONTACT,
            self::EXPORT_CONTACT,
            self::IMPORT_CONTACT => 'contacts',

            self::LIST_ORDERS,
            self::CREATE_ORDER,
            self::VIEW_ORDER,
            self::EDIT_ORDER,
            self::DELETE_ORDER => 'orders',

            self::LIST_PRODUCTS,
            self::CREATE_PRODUCT,
            self::VIEW_PRODUCT,
            self::EDIT_PRODUCT,
            self::DELETE_PRODUCT => 'products',

            self::LIST_ROLES,
            self::CREATE_ROLE,
            self::VIEW_ROLE,
            self::EDIT_ROLE,
            self::DELETE_ROLE => 'roles',
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
        return __('permissions.'.$this->value);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
