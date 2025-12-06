<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    protected $table = 'apps';

    protected $fillable = [
        'app_name',
        'app_logo',
        'app_dec',
        'app_type',
        'color_theme',
        'banner',
        'client_id',
    ];

    // App belongs to one client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }


    public function banners()
    {
        return $this->hasMany(AppBanner::class, 'app_id', 'id'); // make sure app_banners table has app_id
    }

    public function themes()
    {
        return $this->belongsToMany(
            ColorTheme::class, // related model
            'app_themes',      // pivot table
            'app_id',          // foreign key on pivot table for this model
            'theme_id'         // foreign key on pivot table for related model
        )->with('colorCodes'); // eager load colorCodes
    }

}
