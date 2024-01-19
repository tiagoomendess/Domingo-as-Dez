<?php

namespace App\Mail;

use App\InfoReport;

use Illuminate\Mail\Mailable;

class InfoReportNotification extends Mailable
{
    /** @var InfoReport */
    private $infoReport;

    public function __construct($infoReport)
    {
        $this->infoReport = $infoReport;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('custom.site_email'), config('app.name'))
            ->subject("Foi enviada uma nova informação para o site #" . $this->infoReport->code)
            ->view('emails.info_report')
            ->with(['info_report' => $this->infoReport]);
    }
}
