<?php

namespace App\Services\Report;

use App\Models\Tenant\User;
use App\Models\Tenant\Report;
use Illuminate\Support\Facades\Auth;

class ReportPermissionService
{
    /**
     * Check if user can view reports
     */
    public function canViewReports(User $user = null): bool
    {
        $user = $user ?? Auth::user();
        return $user->hasPermissionTo('view-reports');
    }

    /**
     * Check if user can create reports
     */
    public function canCreateReports(User $user = null): bool
    {
        $user = $user ?? Auth::user();
        return $user->hasPermissionTo('create-reports');
    }

    /**
     * Check if user can edit reports
     */
    public function canEditReports(User $user = null): bool
    {
        $user = $user ?? Auth::user();
        return $user->hasPermissionTo('edit-reports');
    }

    /**
     * Check if user can delete reports
     */
    public function canDeleteReports(User $user = null): bool
    {
        $user = $user ?? Auth::user();
        return $user->hasPermissionTo('delete-reports');
    }

    /**
     * Check if user can execute reports
     */
    public function canExecuteReports(User $user = null): bool
    {
        $user = $user ?? Auth::user();
        return $user->hasPermissionTo('execute-reports');
    }

    /**
     * Check if user can view specific report
     */
    public function canViewReport(Report $report, User $user = null): bool
    {
        $user = $user ?? Auth::user();

        // Check basic view permission
        if (!$this->canViewReports($user)) {
            return false;
        }

        // Check report-specific permissions
        if ($report->permissions && isset($report->permissions['view'])) {
            return $this->checkRolePermissions($user, $report->permissions['view']);
        }

        return true;
    }

    /**
     * Check if user can edit specific report
     */
    public function canEditReport(Report $report, User $user = null): bool
    {
        $user = $user ?? Auth::user();

        // Check basic edit permission
        if (!$this->canEditReports($user)) {
            return false;
        }

        // Check if user is the creator
        if ($report->created_by_id === $user->id) {
            return true;
        }

        // Check report-specific permissions
        if ($report->permissions && isset($report->permissions['edit'])) {
            return $this->checkRolePermissions($user, $report->permissions['edit']);
        }

        return false;
    }

    /**
     * Check if user can execute specific report
     */
    public function canExecuteReport(Report $report, User $user = null): bool
    {
        $user = $user ?? Auth::user();

        // Check basic execute permission
        if (!$this->canExecuteReports($user)) {
            return false;
        }

        // Check report-specific permissions
        if ($report->permissions && isset($report->permissions['execute'])) {
            return $this->checkRolePermissions($user, $report->permissions['execute']);
        }

        return true;
    }

    /**
     * Check if user can view specific report category
     */
    public function canViewReportCategory(string $category, User $user = null): bool
    {
        $user = $user ?? Auth::user();

        $permissionMap = [
            'sales_performance' => 'view-sales-reports',
            'lead_management' => 'view-lead-reports',
            'team_performance' => 'view-team-reports',
            'task_completion' => 'view-task-reports',
            'revenue_analysis' => 'view-revenue-reports',
            'opportunity_pipeline' => 'view-pipeline-reports',
            'call_activity' => 'view-call-reports',
            'contact_management' => 'view-contact-reports',
            'product_performance' => 'view-product-reports',
            'forecasting' => 'view-forecasting-reports',
        ];

        $permission = $permissionMap[$category] ?? 'view-reports';
        return $user->hasPermissionTo($permission);
    }

    /**
     * Get reports accessible to user
     */
    public function getAccessibleReports(User $user = null): \Illuminate\Database\Eloquent\Builder
    {
        $user = $user ?? Auth::user();

        $query = Report::query();

        // If user doesn't have view-reports permission, return empty
        if (!$this->canViewReports($user)) {
            return $query->whereRaw('1 = 0');
        }

        // Filter by report-specific permissions
        $query->where(function ($q) use ($user) {
            // Reports with no specific permissions
            $q->whereNull('permissions')
                ->orWhereJsonLength('permissions', 0);

            // Reports where user has permission
            $q->orWhere(function ($subQ) use ($user) {
                $subQ->whereJsonContains('permissions->view', $user->getRoleNames()->toArray());
            });

            // Reports created by user
            $q->orWhere('created_by_id', $user->id);
        });

        return $query;
    }

    /**
     * Check role permissions for report
     */
    protected function checkRolePermissions(User $user, array $allowedRoles): bool
    {
        $userRoles = $user->getRoleNames()->toArray();
        return !empty(array_intersect($userRoles, $allowedRoles));
    }

    /**
     * Get default permissions for report categories
     */
    public function getDefaultPermissions(): array
    {
        return [
            'sales_performance' => [
                'view' => ['admin', 'manager', 'sales'],
                'edit' => ['admin', 'manager'],
                'execute' => ['admin', 'manager', 'sales'],
            ],
            'lead_management' => [
                'view' => ['admin', 'manager', 'sales'],
                'edit' => ['admin', 'manager'],
                'execute' => ['admin', 'manager', 'sales'],
            ],
            'team_performance' => [
                'view' => ['admin', 'manager'],
                'edit' => ['admin'],
                'execute' => ['admin', 'manager'],
            ],
            'task_completion' => [
                'view' => ['admin', 'manager', 'sales', 'support'],
                'edit' => ['admin', 'manager'],
                'execute' => ['admin', 'manager', 'sales', 'support'],
            ],
            'revenue_analysis' => [
                'view' => ['admin', 'manager'],
                'edit' => ['admin'],
                'execute' => ['admin', 'manager'],
            ],
            'opportunity_pipeline' => [
                'view' => ['admin', 'manager', 'sales'],
                'edit' => ['admin', 'manager'],
                'execute' => ['admin', 'manager', 'sales'],
            ],
            'call_activity' => [
                'view' => ['admin', 'manager', 'sales'],
                'edit' => ['admin', 'manager'],
                'execute' => ['admin', 'manager', 'sales'],
            ],
            'contact_management' => [
                'view' => ['admin', 'manager', 'sales', 'support'],
                'edit' => ['admin', 'manager'],
                'execute' => ['admin', 'manager', 'sales', 'support'],
            ],
            'product_performance' => [
                'view' => ['admin', 'manager'],
                'edit' => ['admin'],
                'execute' => ['admin', 'manager'],
            ],
            'forecasting' => [
                'view' => ['admin', 'manager'],
                'edit' => ['admin'],
                'execute' => ['admin', 'manager'],
            ],
        ];
    }

    /**
     * Set default permissions for a report
     */
    public function setDefaultPermissions(Report $report): void
    {
        $defaultPermissions = $this->getDefaultPermissions();
        $categoryPermissions = $defaultPermissions[$report->category] ?? [];

        if (!empty($categoryPermissions)) {
            $report->update(['permissions' => $categoryPermissions]);
        }
    }
}
