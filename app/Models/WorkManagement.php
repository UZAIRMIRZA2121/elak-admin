<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkManagement extends Model
{
    use HasFactory;
       protected $table = 'work_managements';
        protected $fillable = [
            'voucher_id',
            'guid_title',
            'sections',
            'status',
        ];

        protected $casts = [
        'sections' => 'array',
    ];

    }
