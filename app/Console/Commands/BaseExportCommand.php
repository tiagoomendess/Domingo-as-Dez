<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

abstract class BaseExportCommand extends Command
{
    protected function getRangeDateFromInput($date_type): Carbon {
        $time = $date_type == 'start' ? '00:00:00' : '23:59:59';
        do {
            $date_str = $this->ask("What is the $date_type date? (ex: 2023-10-18)");
            try {
                $date = Carbon::createFromFormat('Y-m-d H:i:s', "$date_str $time");
            } catch (\Exception $e) {
                $date = null;
                $this->error("Invalid date '$date_str' provided. Please try again.");
                continue;
            }

        } while (!$date);

        return $date;
    }

    protected function getWantedFiletype($allowed_types = []): string {
        do {
            $chosen = $this->ask('File type ' . implode(', ', $allowed_types) . '?');
            if (!in_array($chosen, $allowed_types)) {
                $this->error('Invalid file type "' . $chosen . '" provided. Please try again.');
            }

        } while (!in_array($chosen, $allowed_types));

        return $chosen;
    }
}
