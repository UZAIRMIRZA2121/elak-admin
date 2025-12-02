<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppTheme extends Model
{
    use HasFactory;

    protected $table = 'app_themes';

    protected $fillable = [
        'app_id',
        'theme_id',
    ];

    // Relations
    public function app()
    {
        return $this->belongsTo(App::class, 'app_id');
    }

    public function theme()
    {
        return $this->belongsTo(ColorTheme::class, 'theme_id');
    }
}
