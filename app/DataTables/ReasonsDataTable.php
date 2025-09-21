<?php

namespace App\DataTables;

use App\Models\Reason;
use App\Services\ReasonService;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ReasonsDataTable extends DataTable
{
    protected array $actions = ['myCustomAction'];
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('check_box', function (Reason $reason) {
                return view(
                    'layouts.components._datatable-checkbox',
                    ['name' => "reasons[]", 'value' => $reason->id]
                );
            })
            ->addColumn('action', function (Reason $reason) {
                return view(
                    'layouts.dashboard.reason.components._actions',
                    ['model' => $reason, 'url' => route('reasons.destroy', $reason->id)]
                );
            })
            ->addColumn('created_at', function (Reason $reason) {
                return $reason->created_at->format('d-m-Y');
            })
            ->orderColumn('created_at', 'created_at $1')
            ->addColumn('updated_at', function (Reason $reason) {
                return $reason->updated_at->format('d-m-Y');
            })
            ->orderColumn('updated_at', 'updated_at $1')
            ->setRowId('id');
    }
     /**
     * Get the query source of dataTable.
     */
    public function query(ReasonService $reasonService): QueryBuilder
    {
        return  $reasonService->datatable(filters: $this->filters, withRelations: $this->withRelations);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('reasons-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->parameters([
                'dom' => 'ft<"d-flex justify-content-between align-items-center"ipl>',
                'buttons' => ['myCustomAction'],
                'initComplete' => "function(settings, json) {
                    var searchInput = $('.dataTables_filter input');

                    $('.dataTables_filter label').contents().filter(function() {
                        return this.nodeType === 3;
                    }).remove();

                    $('.dataTables_length label').contents().filter(function () {
                        return this.nodeType === 3;
                    }).each(function () {
                        $(this).replaceWith($(this).text().replace(' entries', ''));
                    });

                    searchInput.addClass('form-control border-0').attr('placeholder', 'Search reasons...');
                    $('#search-here').replaceWith(searchInput);


                }",
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
            // Column::make('id'),
            Column::make('name'),
            Column::make('created_at'),
            Column::make('updated_at'),
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
        return 'reasons' . date('YmdHis');
    }

}
