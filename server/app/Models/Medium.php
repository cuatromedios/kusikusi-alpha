<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
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
        return "/media/$this->id/thumb/{$this->getTitleAsSlug()}";
    }
    public function getPreviewAttribute()
    {
        return "/media/$this->id/preview/{$this->getTitleAsSlug()}";
    }
    private function getTitleAsSlug() {
        $filename = isset($this['title']) ? Str::slug($this['title']) : 'media';
        $fileformat = isset($this['format']) ? Str::slug($this['format']) : 'bin';
        return "{$filename}.{$fileformat}";
    }

}
