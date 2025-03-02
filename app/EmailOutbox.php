<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailOutbox extends Model
{
    protected $table = 'email_outbox';

    protected $fillable = [
        'from', 'to', 'cc', 'bcc', 'subject', 'body', 'headers', 'attachments',
    ];
}
