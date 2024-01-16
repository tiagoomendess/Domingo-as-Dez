<?php

namespace App;

class HoneyPotCatch extends BaseModel
{
    protected $table = 'honey_pot_catches';

    protected $fillable = [
        'ip_address',
        'user_agent',
        'route',
        'query_params',
        'headers',
        'cookies',
        'ip_country',
        'http_method'
    ];


}