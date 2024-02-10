<?php

namespace App;

class DataExport extends BaseModel
{
    protected $fillable = [
        'name',
        'model',
        'fields',
        'query',
        'order_by',
        'order_direction',
        'format',
        'status',
        'message',
        'file_path',
        'same_file',
        'user_id',
    ];

    protected $guarded = [];

    protected $hidden = [];
}
