<?php

namespace App\Exports;

use App\Services\Tenant\Users\UserService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    protected $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    public function collection()
    {
        $userService = app(UserService::class);
        $query = $userService->getQuery();

        // If user_id or city_id included, eager load relations
        if (in_array('team_id', $this->columns)) {
            $query->with('team');
        }

        if (in_array('department_id', $this->columns)) {
            $query->with('department');
        }

        $users = $query->select($this->columns)->get();

        return $users->map(function ($user) {
            $row = [];
            foreach ($this->columns as $column) {
                if ($column === 'team_id') {
                    $row['team'] = $user->team?->title; 
                } elseif ($column === 'department_id') {
                    $row['department'] = $user->department?->name;
                } elseif ($column === 'is_active') {
                    $row['is_active'] = $user->is_active ? 'Yes' : 'No';
                } elseif ($column === 'created_at') {
                    $row['created_at'] = $user->created_at->toDateString();
                } else {
                    $row[$column] = $user->$column;
                }
            }
            return $row;
        });
    }

    public function headings(): array
    {
        return array_map(function ($column) {
            if ($column === 'team_id') {
                return 'team';
            } elseif ($column === 'department_id') {
                return 'department';
            }
            return ucfirst(str_replace('_', ' ', $column));
        }, $this->columns);
    }
}
