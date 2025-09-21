<?php

namespace App\DataTables;

use App\Models\Client;
use App\Services\ClientService;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ClientsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('check_box', function (Client $client) {
                return view(
                    'layouts.components._datatable-checkbox',
                    ['name' => "clients[]", 'value' => $client->id]
                );
            })
            ->addColumn('action', function (Client $client) {
                return view(
                    'layouts.dashboard.client.components._actions',
                    ['model' => $client, 'url' => route('custom-fields.destroy', $client->id)]
                );
            })
            ->addColumn('created_at', function (Client $client) {
                return $client->created_at->format('d-m-Y');
            })
            ->orderColumn('created_at', 'created_at $1')
            ->addColumn('updated_at', function (Client $client) {
                return $client->updated_at->format('d-m-Y');
            })
            ->orderColumn('updated_at', 'updated_at $1')
            ->setRowId('id');
    }
     /**
     * Get the query source of dataTable.
     */
    public function query(ClientService $clientService): QueryBuilder
    {
        return  $clientService->datatable([], []);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('clients-table')
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
            Column::make('phone'),
            Column::make('email'),
            Column::make('address'),
            // Column::make('resource')
            //     ->title('Resource')  // Optional: Add a title for the column
            //     ->searchable(false)  // Adjust this based on your use case
            //     ->orderable(false)    // Adjust this based on your use case
            //     ->addColumn('resource', function (Client $client) {
            //         return $client->resource ? $client->resource->name : '';  // Assuming 'name' is a property of the related 'resource'
            //     }),

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
        return 'clients' . date('YmdHis');
    }
}
