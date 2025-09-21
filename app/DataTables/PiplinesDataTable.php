<?php

namespace App\DataTables;

use App\Models\Pipline;
use App\Services\PiplineService;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PiplinesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('check_box', function (Pipline $pipline) {
                return view(
                    'layouts.components._datatable-checkbox',
                    ['name' => "piplines[]", 'value' => $pipline->id]
                );
            })
            ->addColumn('action', function (Pipline $pipline) {
                return view(
                    'layouts.dashboard.pipline.components._actions',
                    ['model' => $pipline, 'url' => route('piplines.destroy', $pipline->id)]
                );
            })
            ->addColumn('created_at', function (Pipline $iipline) {
                return $iipline->created_at->format('d-m-Y');
            })
            ->addColumn('stages', function (Pipline $pipline) {
                return $pipline->stages->pluck('name')->join(' , ');
            })
            ->orderColumn('created_at', 'created_at $1')
            ->setRowId(content: 'id');
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(PiplineService $piplineService): QueryBuilder
    {
        return  $piplineService->datatable([], ['stages']);
    }


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('piplines-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('check_box')->title('<label class="custom-control custom-checkbox custom-control-md">
            <input type="checkbox" class="custom-control-input checkAll">
            <span class="custom-control-label custom-control-label-md  tx-17"></span></label>')->searchable(false)->orderable(false),
            Column::make('id'),
            Column::make('name'),
            Column::make('stages')->title(__('Stages')),
            Column::make('created_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Piplines_' . date('YmdHis');
    }
}
