<?php

namespace App\DTO\Report;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;

class ReportExecutionDTO extends BaseDTO
{
    public function __construct(
        public int $report_id,
        public ?int $executed_by_id = null,
        public string $status = 'pending',
        public ?string $started_at = null,
        public ?string $completed_at = null,
        public ?int $execution_time = null,
        public ?int $records_processed = null,
        public ?string $file_path = null,
        public ?int $file_size = null,
        public ?string $error_message = null,
        public ?array $parameters = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            report_id: $request->input('report_id'),
            executed_by_id: $request->input('executed_by_id'),
            status: $request->input('status', 'pending'),
            started_at: $request->input('started_at'),
            completed_at: $request->input('completed_at'),
            execution_time: $request->input('execution_time'),
            records_processed: $request->input('records_processed'),
            file_path: $request->input('file_path'),
            file_size: $request->input('file_size'),
            error_message: $request->input('error_message'),
            parameters: $request->input('parameters'),
        );
    }

    public function toArray(): array
    {
        return [
            'report_id' => $this->report_id,
            'executed_by_id' => $this->executed_by_id,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'execution_time' => $this->execution_time,
            'records_processed' => $this->records_processed,
            'file_path' => $this->file_path,
            'file_size' => $this->file_size,
            'error_message' => $this->error_message,
            'parameters' => $this->parameters,
        ];
    }

    /**
     * Mark execution as started
     */
    public function markAsStarted(): self
    {
        $this->status = 'running';
        $this->started_at = now()->toDateTimeString();
        return $this;
    }

    /**
     * Mark execution as completed
     */
    public function markAsCompleted(int $recordsProcessed = null, string $filePath = null, int $fileSize = null): self
    {
        $this->status = 'completed';
        $this->completed_at = now()->toDateTimeString();
        $this->records_processed = $recordsProcessed;
        $this->file_path = $filePath;
        $this->file_size = $fileSize;

        if ($this->started_at) {
            $this->execution_time = now()->diffInSeconds($this->started_at);
        }

        return $this;
    }

    /**
     * Mark execution as failed
     */
    public function markAsFailed(string $errorMessage = null): self
    {
        $this->status = 'failed';
        $this->completed_at = now()->toDateTimeString();
        $this->error_message = $errorMessage;

        if ($this->started_at) {
            $this->execution_time = now()->diffInSeconds($this->started_at);
        }

        return $this;
    }
}
