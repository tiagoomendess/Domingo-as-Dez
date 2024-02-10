<?php

namespace App\Jobs;

use App\DataExport;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDataExports
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $available_formats = ['csv', 'pdf'];

    public function __construct()
    {

    }

    public function handle()
    {
        $startTime = new \DateTime();
        $data_export = $this->getOldestPendingDataExport();
        if (empty($data_export)) {
            return;
        }
        Log::info("Starting ProcessDataExports...");

        $data_export->status = 'completed';
        $data_export->message = 'Data export completed successfully';
        $data_export->save();

        $endTime = new \DateTime();
        $diff = $endTime->diff($startTime);

        Log::info("ProcessDataExports completed successfully in " . $diff->format('%s seconds %F microseconds'));
    }

    private function getOldestPendingDataExport()
    {
        $export = DataExport::where('status', 'pending')->orderBy('id')->first();
        if (!empty($export)) {
            $export->status = 'processing';
            $export->save();
        }

        return $export;
    }
}
