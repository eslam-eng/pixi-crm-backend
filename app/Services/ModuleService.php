<?php

namespace App\Services;

use App\Models\Module;


class ModuleService extends BaseService
{
    public function __construct(
        public Module  $model,
    ) {}

    public function getModel(): Module
    {
        return $this->model;
    }

    public function getTableName(): String
    {
        return $this->getModel()->getTable();
    }

    public function getGroupedModules()
    {

        return $this->getModel()->all()->groupBy('group')->map(function ($groupModules) {
            return $groupModules->map(function ($module) {
                return [
                    'id' => $module->id,
                    'name' => $module->localized_name,
                    'key' => $module->key,
                    'group' => $module->group,
                    'group_label' => $module->localized_group_label,
                    'has_number_field' => $module->has_number_field,
                    'number_field_label' => $module->localized_number_field_label
                ];
            });
        });
        
    }

}
