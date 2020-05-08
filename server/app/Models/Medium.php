<?php

namespace App\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Mimey\MimeTypes;

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
    public static function getProperties($file) {
        $typeOfFile = gettype($file) === 'object' ? Str::afterLast(get_class($file), '\\') : (gettype($file) === 'string' ? 'path' : 'unknown');
        if ($typeOfFile === 'UploadedFile') {
            $format = strtolower($file->getClientOriginalExtension() ? $file->getClientOriginalExtension() : $file->guessClientExtension());
            $mimeType = $file->getClientMimeType();
            $originalName = $file->getClientOriginalName();
            $size = $file->getSize();
        } else if ($typeOfFile === 'path') {
            $format = strtolower(Str::afterLast($file, '.'));
            $mimes = new MimeTypes;
            $mimeType =  $mimes->getMimeType($format);
            $originalName = Str::afterLast($file, '/');
            $size = null;
        } else {
            $format = 'bin';
            $mimeType = 'application/octet-stream';
            $originalName = 'file.bin';
            $size = null;
        }
        $format = $format == 'jpeg' ? 'jpg': $format;
        $properties = [
            'format' => $format,
            'mimeType' => $mimeType,
            'originalName' => $originalName,
            'size' => $size,
            'isWebImage' => array_search(strtolower($format), config('media.formats.webImages', ['jpeg', 'jpg', 'png', 'gif'])) !== false,
            'isImage' => array_search(strtolower($format), config('media.formats.images', ['jpeg', 'jpg', 'png', 'gif', 'tif', 'tiff', 'iff', 'bmp', 'psd'])) !== false,
            'isAudio' => array_search(strtolower($format), config('media.formats.audios', ['mp3', 'wav', 'aiff', 'aac', 'oga', 'pcm', 'flac'])) !== false,
            'isWebAudio' => array_search(strtolower($format), config('media.formats.webAudios', ['mp3', 'oga'])) !== false,
            'isVideo' => array_search(strtolower($format), config('media.formats.videos', ['mov', 'mp4', 'qt', 'avi', 'mpe', 'mpeg', 'ogg', 'm4p', 'm4v', 'flv', 'wmv'])) !== false,
            'isWebVideo' => array_search(strtolower($format), config('media.formats.webVideos', ['webm', 'mp4', 'ogg', 'm4p', 'm4v'])) !== false,
            'isDocument' => array_search(strtolower($format), config('media.formats.documents', ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'htm', 'html', 'txt', 'rtf', 'csv', 'pps', 'ppsx', 'odf', 'key', 'pages', 'numbers'])) !== false
        ];
        $properties['type'] = $properties['isImage'] ? 'image' : ($properties['isAudio'] ? 'audio' : ($properties['isVideo'] ? 'video' : ($properties['isDocument'] ? 'document' : 'file')));
        if ($properties['isImage']) {
            if ($typeOfFile === 'UploadedFile') {
                $properties['exif'] = Image::make($file->getRealPath())->exif();
            } else if ($typeOfFile === 'path') {
                $properties['exif'] = Image::make($file)->exif();
            }
            if (isset($properties['exif']['COMPUTED']['Width'])) {
                $properties['width'] = $properties['exif']['COMPUTED']['Width'];
                $properties['height'] = $properties['exif']['COMPUTED']['Height'];
            }
        } else {
            $properties['exif'] = null;
            $properties['width'] = null;
            $properties['height'] = null;
        }
        return $properties;
    }

}
