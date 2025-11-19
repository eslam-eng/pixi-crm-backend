<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    // Dashboard Permissions
    case VIEW_ADMIN_DASHBOARD = 'view-admin-dashboard';
    case VIEW_MANAGER_DASHBOARD = 'view-manager-dashboard';
    case VIEW_AGENT_DASHBOARD = 'view-agent-dashboard';

    // Lead Management
    case VIEW_LEADS = 'view-leads';
    case CREATE_LEADS = 'create-leads';
    case EDIT_LEADS = 'edit-leads';
    case DELETE_LEADS = 'delete-leads';
    case KANBAN_LEADS = 'kanban-leads';
    case CHANGE_STAGE = 'change-stage';
    case GET_ACTIVITIES_LIST = 'get-activities-list';

    // Contact Management
    case VIEW_CONTACTS = 'view-contacts';
    case CREATE_CONTACTS = 'create-contacts';
    case EDIT_CONTACTS = 'edit-contacts';
    case DELETE_CONTACTS = 'delete-contacts';
    case MERGE_CONTACTS = 'merge-contacts';

    // Form Management
    case VIEW_FORMS = 'view-forms';
    case CREATE_FORMS = 'create-forms';
    case EDIT_FORMS = 'edit-forms';
    case DELETE_FORMS = 'delete-forms';
    case TOGGLE_FORMS = 'toggle-forms';

    // Deal Management
    case VIEW_DEALS = 'view-deals';
    case CREATE_DEALS = 'create-deals';
    case EDIT_DEALS = 'edit-deals';
    case DELETE_DEALS = 'delete-deals';
    case CHANGE_APPROVAL_STATUS = 'change-approval-status';

    // Task Management
    case VIEW_TASKS = 'view-tasks';
    case CREATE_TASKS = 'create-tasks';
    case EDIT_TASKS = 'edit-tasks';
    case DELETE_TASKS = 'delete-tasks';

    // Item Management
    case VIEW_ITEMS = 'view-items';
    case CREATE_ITEMS = 'create-items';
    case EDIT_ITEMS = 'edit-items';
    case DELETE_ITEMS = 'delete-items';

    // Attendance Management
    case VIEW_ATTENDANCE = 'view-attendance';
    case CREATE_ATTENDANCE = 'create-attendance';

    // Settings
    case MANAGE_SETTINGS = 'manage-settings';

    /**
     * Get the group name for the permission
     */
    public function getGroup(): string
    {
        return match ($this) {
            self::VIEW_ADMIN_DASHBOARD,
            self::VIEW_MANAGER_DASHBOARD,
            self::VIEW_AGENT_DASHBOARD => 'dashboard',

            self::VIEW_LEADS,
            self::CREATE_LEADS,
            self::EDIT_LEADS,
            self::DELETE_LEADS,
            self::KANBAN_LEADS,
            self::CHANGE_STAGE,
            self::GET_ACTIVITIES_LIST => 'leads',

            self::VIEW_CONTACTS,
            self::CREATE_CONTACTS,
            self::EDIT_CONTACTS,
            self::DELETE_CONTACTS,
            self::MERGE_CONTACTS => 'contacts',

            self::VIEW_FORMS,
            self::CREATE_FORMS,
            self::EDIT_FORMS,
            self::DELETE_FORMS,
            self::TOGGLE_FORMS => 'forms',

            self::VIEW_DEALS,
            self::CREATE_DEALS,
            self::EDIT_DEALS,
            self::DELETE_DEALS,
            self::CHANGE_APPROVAL_STATUS => 'deals',

            self::VIEW_TASKS,
            self::CREATE_TASKS,
            self::EDIT_TASKS,
            self::DELETE_TASKS => 'tasks',

            self::VIEW_ITEMS,
            self::CREATE_ITEMS,
            self::EDIT_ITEMS,
            self::DELETE_ITEMS => 'items',

            self::VIEW_ATTENDANCE,
            self::CREATE_ATTENDANCE => 'attendance',

            self::MANAGE_SETTINGS => 'settings',
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
        return __('app.permissions.' . $this->value, $this->value);
    }

    /**
     * Get the description for the permission
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::VIEW_ADMIN_DASHBOARD => 'View admin dashboard',
            self::VIEW_MANAGER_DASHBOARD => 'View manager dashboard',
            self::VIEW_AGENT_DASHBOARD => 'View agent dashboard',
            
            self::VIEW_LEADS => 'View all leads',
            self::CREATE_LEADS => 'Create new leads',
            self::EDIT_LEADS => 'Edit all leads',
            self::DELETE_LEADS => 'Delete leads',
            self::KANBAN_LEADS => 'Kanban leads',
            self::CHANGE_STAGE => 'Change stage',
            self::GET_ACTIVITIES_LIST => 'Get activities list',
            
            self::VIEW_CONTACTS => 'View contacts',
            self::CREATE_CONTACTS => 'Create contacts',
            self::EDIT_CONTACTS => 'Edit contacts',
            self::DELETE_CONTACTS => 'Delete contacts',
            self::MERGE_CONTACTS => 'Merge contacts',
            
            self::VIEW_FORMS => 'View forms',
            self::CREATE_FORMS => 'Create forms',
            self::EDIT_FORMS => 'Edit forms',
            self::DELETE_FORMS => 'Delete forms',
            self::TOGGLE_FORMS => 'Toggle forms',
            
            self::VIEW_DEALS => 'View deals',
            self::CREATE_DEALS => 'Create deals',
            self::EDIT_DEALS => 'Edit deals',
            self::DELETE_DEALS => 'Delete deals',
            self::CHANGE_APPROVAL_STATUS => 'Change approval status',
            
            self::VIEW_TASKS => 'View tasks',
            self::CREATE_TASKS => 'Create tasks',
            self::EDIT_TASKS => 'Edit tasks',
            self::DELETE_TASKS => 'Delete tasks',
            
            self::VIEW_ITEMS => 'View items',
            self::CREATE_ITEMS => 'Create items',
            self::EDIT_ITEMS => 'Edit items',
            self::DELETE_ITEMS => 'Delete items',
            
            self::VIEW_ATTENDANCE => 'View attendance',
            self::CREATE_ATTENDANCE => 'Create attendance',
            
            self::MANAGE_SETTINGS => 'Manage system settings',
        };
    }

    /**
     * Get all permission values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get permissions by group
     */
    public static function getByGroup(string $group): array
    {
        return array_filter(
            self::cases(),
            fn($permission) => $permission->getGroup() === $group
        );
    }
}

