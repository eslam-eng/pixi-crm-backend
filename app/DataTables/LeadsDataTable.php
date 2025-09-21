<?php

namespace App\DataTables;

use App\Models\Lead;
use App\Services\leadService;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class LeadsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('check_box', function (Lead $lead) {
                return view(
                    'layouts.components._datatable-checkbox',
                    ['name' => "leads[]", 'value' => $lead->id]
                );
            })
            ->addColumn('contact_name', function (Lead $lead) {
                return $lead->contact ? $lead->contact->name : 'N/A'; // Fetch contact name
            })
            ->addColumn('status', function (Lead $lead) {
                return ucfirst($lead->status); // Format status
            })
            ->addColumn('stage', function (Lead $lead) {
                return $lead->stages->last() ? $lead->stages->last()->name : 'N/A'; // Get the latest stage
            })
            ->addColumn('action', function (Lead $lead) {
                return view(
                    'layouts.dashboard.lead.components._actions',
                    ['model' => $lead, 'url' => route('leads.destroy', $lead->id)]
                );
            })
            ->addColumn('created_at', function (Lead $lead) {
                return $lead->created_at->format('d-m-Y');
            })
            ->orderColumn('created_at', 'created_at $1')
            ->addColumn('updated_at', function (Lead $lead) {
                return $lead->updated_at->format('d-m-Y');
            })
            ->orderColumn('updated_at', 'updated_at $1')
            ->setRowId('id');
    }
     /**
     * Get the query source of dataTable.
     */
    public function query(LeadService $leadService): QueryBuilder
    {
        return $leadService->datatable([], [ 'contact', 'stages' ]);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('leads-table')
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
            Column::make('contact_name')->title('Contact Name'), // Display contact name
            Column::make('status')->title('Status'), // Show status
            Column::make('stage')->title('Current Stage'), // Show the latest stage
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
        return 'leads' . date('YmdHis');
    }
}
