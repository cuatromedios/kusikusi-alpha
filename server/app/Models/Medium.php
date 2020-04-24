<?php

namespace App\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class Medium extends EntityModel
{
    protected $appends = ['thumb','preview'];
    protected $contentFields = [ "title", "summary"];
    protected $propertiesFields = [ "size", "lang", "format", "length", "exif", "width", "height" ];

    /**
     * @return string Returns a public path to the medium using presets
     */
    public function getThumbAttribute()
    {
        return "/media/$this->id/thumb/{$this->getTitleAsSlug('thumb')}";
    }
    public function getPreviewAttribute()
    {
        return "/media/$this->id/preview/{$this->getTitleAsSlug('preview')}";
    }
    private function getTitleAsSlug($preset) {
        $filename = isset($this['title']) ? Str::slug($this['title']) : 'media';
        $fileformat = Config::get("media.presets.{$preset}.format", false) ??  (isset($this['format']) ? Str::slug($this['format']) : 'bin');
        return "{$filename}.{$fileformat}";
    }

}
