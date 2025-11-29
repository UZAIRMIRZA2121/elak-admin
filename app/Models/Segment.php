<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
      use HasFactory;

        protected $table = 'segments';

        protected $fillable = ['client_id', 'name', 'type','validation_date','status'];

    protected $fillable = [
        'type',
        'name',
        'validation_date',
        'status',
        'client_id', // add this
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
