<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    // public $timestamps = true;

    protected $fillable = [
        'name',
        'url',
        'sort',
        'section',
        'image',
        'description',
        'emoji',
        'heading_image',
        'heading_mobile_image',
    ];

    public function setImageAttribute($value) {
        $attribute_name = "image";
        $disk = "public_storage";
        $destination_path = "images/themes";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function setEmojiAttribute($value) {
        $attribute_name = "emoji";
        $disk = "public_storage";
        $destination_path = "images/themes";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function setHeadingImageAttribute($value) {
        $attribute_name = "heading_image";
        $disk = "public_storage";
        $destination_path = "images/themes/heading_images";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function setHeadingMobileImageAttribute($value) {
        $attribute_name = "heading_mobile_image";
        $disk = "public_storage";
        $destination_path = "images/themes/heading_images";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function sectionName() {
        return $this->belongsTo('App\Models\Section','section','id');
    }

    public function isFavourite($user_id) {
        return Favourite::where('user_id',$user_id)->where('paragraph_theme_id',$this->attributes['id'])->where('type','theme')->exists();
    }
}
