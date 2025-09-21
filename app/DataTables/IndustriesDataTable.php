<?php

namespace App\DataTables;

use App\Models\Industry;
use App\Services\IndustryService;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class IndustriesDataTable extends DataTable
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
            ->addColumn('check_box', function (Industry $industry) {
                return view(
                    'layouts.components._datatable-checkbox',
                    ['name' => "industries[]", 'value' => $industry->id]
                );
            })
            ->addColumn('action', function (Industry $industry) {
                return view(
                    'layouts.dashboard.industry.components._actions',
                    ['model' => $industry, 'url' => route('industries.destroy', $industry->id)]
                );
            })
            ->addColumn('created_at', function (Industry $industry) {
                return $industry->created_at->format('d-m-Y');
            })
            ->orderColumn('created_at', 'created_at $1')
            ->setRowId(content: 'id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(IndustryService $industryService): QueryBuilder
    {
        return  $industryService->datatable(filters: $this->filters, withRelations: $this->withRelations);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('industries-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->dom('Bfrtip')
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

                    searchInput.addClass('form-control border-0').attr('placeholder', 'Search industries...');
                    $('#search-here').replaceWith(searchInput);


                }",
            ]);
    }

    //     <div class="input-group rounded-pill border overflow-hidden">
    //     <span class="input-group-text bg-white border-0">
    //         <i class="fas fa-search text-muted"></i>
    //     </span>
    //     <input type="text" class="form-control border-0" placeholder="Search">
    //     <button class="btn btn-light border-0">
    //         <i class="fas fa-filter text-muted"></i>
    //     </button>
    // </div>
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
        return 'Industries_' . date('YmdHis');
    }

    public function myCustomAction()
    {
        //...your code here.
    }
}
