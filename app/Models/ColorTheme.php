<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColorTheme extends Model
{
    use HasFactory;

    protected $table = "color_themes";

    protected $fillable = [
        "color_name",
        "color_code",
        "color_gradient",
        "color_type",
        "status",
        "app_id", // NEW
    ];

    public function app()
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
