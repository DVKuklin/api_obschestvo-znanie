<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paragraph extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $fillable = [
        'content',
        'theme',
        'sort',
    ];

    public function themeName() {
        return $this->belongsTo('App\Models\Theme','theme','id');
    }

    protected $table = 'paragraphs';

    public function isFavourite($user_id) {
        return Favourite::where('user_id',$user_id)->where('paragraph_theme_id',$this->attributes['id'])->where('type','paragraph')->exists();
    }

}
