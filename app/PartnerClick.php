<?php

namespace App;

class PartnerClick extends BaseModel
{
    protected $table = 'partner_clicks';

    protected $fillable = [
        'partner_id',
        'page',
    ];

    public function partner()
    {
        return $this->belongsTo('App\Partner');
    }
}
