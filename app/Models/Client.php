<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
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
        'status',
        'remember',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Each client has ONE app
    public function app()
    {
        return $this->hasOne(App::class, 'client_id', 'id'); // client_id is foreign key in apps table
    }

    // Client can have multiple segments
    public function segments()
    {
        return $this->hasMany(Segment::class, 'client_id', 'id');
    }
}
