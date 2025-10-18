<?php

namespace App\Services\Report;

use App\Models\Tenant\Report;
use App\Models\Tenant\ReportExecution;
use App\Mail\ReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ReportEmailService
{
    /**
     * Send report email to recipients
     */
    public function sendReportEmail(Report $report, ReportExecution $execution, string $filePath): bool
    {
        try {
            if (empty($report->recipients)) {
                return false;
            }

            $fileContent = Storage::disk('public')->get($filePath);
            $fileName = basename($filePath);
            $mimeType = $this->getMimeType($filePath);

            foreach ($report->recipients as $recipient) {
                Mail::to($recipient)->send(new ReportMail(
                    $report,
                    $execution,
                    $fileName,
                    $fileContent,
                    $mimeType
                ));
            }

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send report email for report {$report->id}", [
                'report_name' => $report->name,
                'recipients' => $report->recipients,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send test email for report configuration
     */
    public function sendTestEmail(Report $report, string $recipient): bool
    {
        try {
            $testExecution = new ReportExecution([
                'report_id' => $report->id,
                'status' => 'completed',
                'started_at' => Carbon::now(),
                'completed_at' => Carbon::now(),
                'execution_time' => 0,
                'records_processed' => 0,
            ]);

            Mail::to($recipient)->send(new ReportMail(
                $report,
                $testExecution,
                'test_report.pdf',
                'This is a test email to verify report email configuration.',
                'text/plain'
            ));

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send test email for report {$report->id}", [
                'report_name' => $report->name,
                'recipient' => $recipient,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get MIME type for file
     */
    private function getMimeType(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'pdf':
                return 'application/pdf';
            case 'xlsx':
            case 'xls':
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            case 'csv':
                return 'text/csv';
            default:
                return 'application/octet-stream';
        }
    }

    /**
     * Validate email recipients
     */
    public function validateRecipients(array $recipients): array
    {
        $validRecipients = [];
        $invalidRecipients = [];

        foreach ($recipients as $recipient) {
            if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                $validRecipients[] = $recipient;
            } else {
                $invalidRecipients[] = $recipient;
            }
        }

        return [
            'valid' => $validRecipients,
            'invalid' => $invalidRecipients,
        ];
    }

    /**
     * Get email delivery status for a report execution
     */
    public function getEmailDeliveryStatus(ReportExecution $execution): array
    {
        // This would integrate with your email tracking system
        // For now, returning basic status
        return [
            'sent_at' => $execution->completed_at,
            'recipients_count' => count($execution->report->recipients ?? []),
            'status' => 'sent', // sent, failed, pending
        ];
    }
}
