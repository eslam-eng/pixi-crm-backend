<?php

namespace App\DataTables;

use App\Enums\ActivationStatus;
use App\Enums\LocationType;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class LocationsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('check_box', function (Location $location) {
                return view(
                    'layouts.components._datatable-checkbox',
                    ['name' => "locations[]", 'value' => $location->id]
                );
            })
            ->addColumn('status', function (Location $location) {
                $badgeClass = $location->status === ActivationStatus::ACTIVE ? 'badge-success-transparent' : 'badge-danger-transparent';
                return '<span class="badge ' . $badgeClass . '">' . $location->status->label() . '</span>';
            })
            ->orderColumn('status', 'status $1')
            ->addColumn('created_at', function (Location $location) {
                return $location->created_at->format('d-m-Y');
            })
            ->orderColumn('created_at', 'created_at $1')
            ->addColumn('type', function (Location $location) {
                return LocationType::From($location->depth)->label();
                // return $location->depth;
            })
            ->addColumn('action', function (Location $location) {
                $result = $location->depth;

                $path = match ($result) {
                0 => 'locations.countries.edit',
                1 => 'locations.governorates.edit',
                2 => 'locations.cities.edit',
                default => 'error',
                };
                return view(
                    'layouts.dashboard.location.components._actions',
                    ['model' => $location, 'path' => $path ,'url' => route('locations.destroy', $location->id)]
                );
            })
            ->rawColumns(['status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(LocationService $locationService): QueryBuilder
    {
        return  $locationService->datatable([], [])->withDepth();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('locations-table')
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
            Column::make('id'),
            Column::make('title'),
            Column::make('status'),
            Column::make('type')->orderable(false),   
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
        return 'Locations_' . date('YmdHis');
    }
}
