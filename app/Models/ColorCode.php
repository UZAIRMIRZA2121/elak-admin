<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColorCode extends Model
{
    use HasFactory;
    protected $fillable = [
        'color_theme_id',
        'color_name',
        'color_code',
        'color_type',
        'color_gradient',
        'status'
    ];

    public function theme()
    {
        return $this->belongsTo(ColorTheme::class, 'color_theme_id');
    }
}
