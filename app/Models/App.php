<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;
    protected $table = 'apps'; // ya jo bhi tumhara table ka naam hai

    protected $fillable = [
        'app_name',
        'app_logo',
        'app_dec',
        'app_type',
        'color_theme',
        'banner',
        'client_id',  // <-- new
    ];
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function colorThemes()
    {
        return $this->hasMany(ColorTheme::class, 'app_id');
    }
    public function banners()
    {
        return $this->hasMany(AppBanner::class, 'app_id');
    }


}