<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'paragraph_theme_id',
        'time_delta',
    ];
}
