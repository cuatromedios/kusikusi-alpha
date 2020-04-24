<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Medium extends EntityModel
{
    protected $appends = ['small','large'];
    protected $contentFields = [ "title", "welcome", "footer" ];
    protected $propertiesFields = [ "size", "lang", "format", "length", "exif", "width", "height" ];

    /**
     * @param $key
     * @return string Returns a public path to the medium using presets
     */
    public function getSmallAttribute()
    {
        return "/media/$this->id/small/{$this->getTitleAsSlug()}";
    }
    public function getLargeAttribute()
    {
        return "/media/$this->id/large/{$this->getTitleAsSlug()}";
    }
    private function getTitleAsSlug() {
        if (isset($this['title'])) {
            $filename=Str::slug($this['title']);
        } else {
            $filename='media';
        }
        if (isset($this['format'])) {
            $fileformat=Str::slug($this['format']);
        } else {
            $fileformat='bin';
        }
        return "{$filename}.{$fileformat}";
    }
    public static function extensionIsImage($extension) {
        return array_search(strtolower($extension), ['jpeg', 'jpg', 'png', 'gif']) !== false;
    }

}
