<?php

namespace App\QueryFilters\Tenant;

use App\Abstracts\QueryFilter;
use Carbon\Carbon;

class TaskFilters extends QueryFilter
{
    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    /**
     * Search tasks by title or description
     */
    public function search($term)
    {
        return $this->builder->where(function ($query) use ($term) {
            $query->where('title', 'LIKE', "%$term%")
                ->orWhere('description', 'LIKE', "%$term%");
        });
    }

    /**
     * Filter by task status
     */
    public function status($term)
    {
        return $this->builder->where('status', $term);
    }

    /**
     * Filter by priority
     */
    public function priority_id($term)
    {
        return $this->builder->where('priority_id', $term);
    }

    /**
     * Filter by assigned user
     */
    public function assigned_to_id($term)
    {
        return $this->builder->where('assigned_to_id', $term);
    }

    /**
     * Filter by due date range
     * Expected format: ['start' => '2024-01-01', 'end' => '2024-12-31']
     */
    public function due_date_range($term)
    {
        if (is_array($term)) {
            if (isset($term['start']) && !empty($term['start'])) {
                $this->builder->where('due_date', '>=', $term['start']);
            }
            if (isset($term['end']) && !empty($term['end'])) {
                $this->builder->where('due_date', '<=', $term['end']);
            }
        } elseif (is_string($term) && !empty($term)) {
            // Handle single date string
            $this->builder->where('due_date', '>=', $term);
        }
        return $this->builder;
    }

    /**
     * Filter by related lead
     */
    public function related_to($term)
    {
        return $this->builder->where('lead_id', $term);
    }

    /**
     * Filter by lead ID (alias for related_to)
     */
    public function lead_id($term)
    {
        return $this->builder->where('lead_id', $term);
    }

    /**
     * Filter by task type
     */
    public function task_type($term)
    {
        return $this->builder->where('task_type_id', $term);
    }

    /**
     * Filter by tags
     */
    public function tags($term)
    {
        if (is_array($term)) {
            return $this->builder->whereJsonContains('tags', $term);
        }
        return $this->builder->whereJsonContains('tags', $term);
    }

    /**
     * Filter by due date (exact match)
     */
    public function due_date($term)
    {
        return $this->builder->where('due_date', $term);
    }

    /**
     * Filter by due time (exact match)
     */
    public function due_time($term)
    {
        return $this->builder->where('due_time', $term);
    }

    /**
     * Filter tasks due today
     */
    public function due_today()
    {
        return $this->builder->where('due_date', Carbon::today());
    }

    /**
     * Filter tasks due this week
     */
    public function due_this_week()
    {
        return $this->builder->whereBetween('due_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Filter tasks due this month
     */
    public function due_this_month()
    {
        return $this->builder->whereBetween('due_date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ]);
    }

    /**
     * Filter overdue tasks
     */
    public function overdue()
    {
        return $this->builder->where('due_date', '<', Carbon::today())
            ->where('status', '!=', 'completed');
    }

    /**
     * Filter by creation date range
     */
    public function created_at_range($term)
    {
        if (is_array($term)) {
            if (isset($term['start']) && !empty($term['start'])) {
                $this->builder->where('created_at', '>=', $term['start']);
            }
            if (isset($term['end']) && !empty($term['end'])) {
                $this->builder->where('created_at', '<=', $term['end']);
            }
        }
        return $this->builder;
    }

    /**
     * Filter by updated date range
     */
    public function updated_at_range($term)
    {
        if (is_array($term)) {
            if (isset($term['start']) && !empty($term['start'])) {
                $this->builder->where('updated_at', '>=', $term['start']);
            }
            if (isset($term['end']) && !empty($term['end'])) {
                $this->builder->where('updated_at', '<=', $term['end']);
            }
        }
        return $this->builder;
    }

    /**
     * Filter by additional notes
     */
    public function additional_notes($term)
    {
        return $this->builder->where('additional_notes', 'LIKE', "%$term%");
    }

    /**
     * Filter by multiple statuses
     */
    public function statuses($term)
    {
        if (is_array($term)) {
            return $this->builder->whereIn('status', $term);
        }
        return $this->builder->where('status', $term);
    }

    /**
     * Filter by multiple priorities
     */
    public function priorities($term)
    {
        if (is_array($term)) {
            return $this->builder->whereIn('priority_id', $term);
        }
        return $this->builder->where('priority_id', $term);
    }

    /**
     * Filter by multiple assigned users
     */
    public function assigned_users($term)
    {
        if (is_array($term)) {
            return $this->builder->whereIn('assigned_to_id', $term);
        }
        return $this->builder->where('assigned_to_id', $term);
    }

    /**
     * Filter by multiple leads
     */
    public function leads($term)
    {
        if (is_array($term)) {
            return $this->builder->whereIn('lead_id', $term);
        }
        return $this->builder->where('lead_id', $term);
    }

    /**
     * Filter by multiple task types
     */
    public function task_types($term)
    {
        if (is_array($term)) {
            return $this->builder->whereIn('task_type_id', $term);
        }
        return $this->builder->where('task_type_id', $term);
    }

    public function start_date($term)
    {
        return $this->builder->where('created_at', '>=', $term);
    }

    public function user_id($term)
    {
        return $this->builder->where('assigned_to_id', $term);
    }

    public function end_date($term)
    {
        return $this->builder->where('created_at', '<=', $term);
    }

    public function team_id($term)
    {
        return $this->builder->whereHas('assignedTo', function ($query) use ($term) {
            $query->where('team_id', $term);
        });
    }
}
