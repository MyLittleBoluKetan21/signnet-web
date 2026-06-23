<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $table = 'system_logs';
    protected $primaryKey = 'id_log';

    public $timestamps = false; 

    protected $fillable = [
        'admin_id',
        'activity',
        'created_at',
    ];
}
