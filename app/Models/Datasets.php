<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Datasets extends Model
{
    protected $table = 'datasets';
    protected $primaryKey = 'id_dataset';

    protected $fillable = [
        'admin_id',
        'features',
        'label',
    ];

    protected $casts = [
        'features' => 'array',
        'created_at' => 'datetime',
    ];
}
