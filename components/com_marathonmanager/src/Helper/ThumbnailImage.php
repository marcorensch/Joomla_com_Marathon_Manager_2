<?php

namespace NXD\Component\MarathonManager\Site\Helper;

use Joomla\CMS\Filesystem\Path;

class ThumbnailImage
{
    private $source;

    public function __construct($sourceImagePath)
    {
        $this->source = $sourceImagePath;
    }

    // Check if DG Library exists
    public static function checkGDLibrary(): bool
    {
        return extension_loaded('gd') && function_exists('gd_info');
    }

    public function createThumbnailIfNotExist($destImagePath, $thumbWidth, $quality): bool
    {
        if (!file_exists($destImagePath)) {
            $this->createFolderStructure($destImagePath);
            return $this->createThumbnail($destImagePath, $thumbWidth, $quality);
        }
        return true;
    }

    public function checkOriginal()
    {
        return file_exists($this->source);
    }

    public function createThumbnail($destImagePath, $thumbWidth, $quality): bool
    {
        try {
            $sourceImage = $this->createSourceImage($this->source);
            $orgWidth = imagesx($sourceImage);
            $orgHeight = imagesy($sourceImage);
            $thumbHeight = floor($orgHeight * ($thumbWidth / $orgWidth));
            $destImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
            imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $orgWidth, $orgHeight);
            imagejpeg($destImage, $destImagePath);
            imagedestroy($sourceImage);
            imagedestroy($destImage);
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return false;
    }

    // Create the GD library source Image based on the filetype
    private function createSourceImage($sourceImagePath) : ?\GdImage
    {
        error_log('Create Source Image: ' . $sourceImagePath);
        $sourceImagePath = Path::clean($sourceImagePath);

        return match (exif_imagetype($sourceImagePath)) {
            // gif
            1 => imageCreateFromGif($sourceImagePath),
            // jpg
            2 => imageCreateFromJpeg($sourceImagePath),
            // png
            3 => imageCreateFromPng($sourceImagePath),
            // bmp
            6 => imageCreateFromBmp($sourceImagePath),
            // not defined
            default => null,
        };
    }

    // Create the folderstructure for the thumbnail image if not exists
    private function createFolderStructure($destImagePath)
    {
        $destImagePath = Path::clean($destImagePath);

        // Get the File Path without the filename the PHP 8 native way
        $destImageFolder = dirname($destImagePath);
        if (!file_exists($destImageFolder)) {
            mkdir($destImageFolder, 0777, true);
        }
    }
}
