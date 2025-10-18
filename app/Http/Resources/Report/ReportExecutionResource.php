<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportExecutionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'report_id' => $this->report_id,
            'executed_by' => [
                'id' => $this->executedBy?->id,
                'name' => $this->executedBy?->name,
                'email' => $this->executedBy?->email,
            ],
            'status' => $this->status,
            'started_at' => $this->started_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'execution_time' => $this->execution_time,
            'records_processed' => $this->records_processed,
            'file_path' => $this->file_path,
            'file_size' => $this->file_size,
            'error_message' => $this->error_message,
            'parameters' => $this->parameters,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
