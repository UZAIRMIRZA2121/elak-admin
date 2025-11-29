<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;  // 👈 yahan Model ke jagah Authenticatable use karo
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'email',
        'password',
        'logo',
        'cover',
        'type',
        'status',
        'remember',
    ];

    protected $hidden = [
        'password',
        'remember_token', // 👈 ye bhi add karo
    ];
    public function apps()
    {
        return $this->hasMany(App::class, 'client_id');
    }
    public function segments()
    {
        return $this->hasMany(Segment::class, 'client_id', 'id');
    }


}