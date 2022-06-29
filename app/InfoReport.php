<?php

namespace App;

class InfoReport extends SearchableModel
{
    protected $table = 'info_reports';

    public const SEARCH_FIELDS = [
        'code' => [
            'name' => 'code',
            'type' => 'string',
            'trans' => 'Código',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|min:9|max:9|string'
        ],
        'status' => [
            'name' => 'status',
            'type' => 'enum',
            'trans' => 'Estado',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|in:sent,seen,used,archived,deleted',
            'enumItems' => [
                [
                    'name' => 'Enviada',
                    'value' => 'sent'
                ],
                [
                    'name' => 'Vista',
                    'value' => 'seen'
                ],
                [
                    'name' => 'Usada',
                    'value' => 'used'
                ],
                [
                    'name' => 'Arquivada',
                    'value' => 'archived'
                ],
                [
                    'name' => 'Apagada',
                    'value' => 'deleted'
                ],
            ]
        ],
        'created_at' => [
            'name' => 'created_at',
            'type' => 'date',
            'trans' => 'Data de Criação',
            'allowSearch' => false
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'type' => 'date',
            'trans' => 'Ultima Atualização',
            'allowSearch' => false
        ]
    ];

    protected $fillable = [
        'id',
        'code',
        'user_id',
        'status',
        'content',
        'source',
        'updated_at',
        'created_at',
    ];

    public const ALLOWED_STATUS = ['sent', 'seen', 'used', 'archived', 'deleted'];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
