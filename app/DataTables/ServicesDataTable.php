<?php

namespace App\DataTables;

use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ServicesDataTable extends DataTable
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
            ->addColumn('check_box', function (Service $service) {
                return view(
                    'layouts.components._datatable-checkbox',
                    ['name' => "service[]", 'value' => $service->id]
                );
            })
            ->addColumn('action', function (Service $service) {
                return view(
                    'layouts.dashboard.service.components._actions',
                    ['model' => $service, 'url' => route('services.destroy', $service->id)] // Use $service->id
                );
            })
            ->addColumn('created_at', function (Service $service) {
                return $service->created_at->format('d-m-Y');
            })
            ->orderColumn('created_at', 'created_at $1')
            ->addColumn('updated_at', function (Service $service) {
                return $service->updated_at->format('d-m-Y');
            })->addColumn('price', function (Service $service) {
                return $service->price ?? '-';
            })
            ->orderColumn('updated_at', 'updated_at $1')
            ->setRowId('id');
    }
    /**
     * Get the query source of dataTable.
     */
    public function query(ServiceService $serviceService): QueryBuilder
    {
        return  $serviceService->datatable(filters: $this->filters, withRelations: $this->withRelations);
    }
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('services-table')
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

                    searchInput.addClass('form-control border-0').attr('placeholder', 'Search Services...');
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
            Column::make('price'),
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
        return 'services_' . date('YmdHis');
    }


}
