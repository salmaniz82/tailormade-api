<?php

namespace Framework;

class ImageTransformer
{

    public $imageType;
    public $filePath;
    public $imageSourceInfo = array();
    public $typeInDigit;
    public $width_org;
    public $height_org;
    public $image_type;
    public $bits_org;
    public $aspectRatio_W_org;
    public $aspectRatio_H_org;

    public $newImageDimensions;
    public $newWidth;
    public $newHeight;
    public $imageBuild;


    public function __construct()
    {
        // initialize the class;
    }

    public function refinePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('/\/+/', '/', $path);

        return $path;
    }


    public function showAbsPath()
    {
        echo ABSPATH;
    }

    public function loadImage($filePath)
    {
        $path = ABSPATH . "{$filePath}";
        $path = $this->refinePath($path);
        $this->filePath = $path;
        return $this->isFileValid($path);
    }

    public function isFileValid($path)
    {
        if (!file_exists($this->filePath)) {
            return false;
        } else {
            $this->prepareImgInfo();
            return true;
        }
    }

    public function prepareImgInfo()
    {
        $this->imageSourceInfo = getimagesize($this->filePath);
        $this->width_org = $this->imageSourceInfo[0];
        $this->height_org = $this->imageSourceInfo[1];
        $this->typeInDigit = $this->imageSourceInfo[2];
        $this->image_type = $this->imageSourceInfo['mime'];
        $this->bits_org = $this->imageSourceInfo['bits'];
        $this->aspectRatio_W_org = $this->width_org / $this->height_org;
        $this->aspectRatio_H_org = $this->height_org / $this->width_org;
    }


    public function prepareDimension($newWidthRequested, $newHeightRequested, $outputQuality = null)
    {

        $error = 0;

        if ($newHeightRequested != 'auto' && $newWidthRequested != 'auto') {


            $newWidth = (int) $newWidthRequested;
            $newHeight = (int) $newHeightRequested;

            $this->newWidth = $newWidth;
            $this->newHeight = $newHeight;
            $this->newImageDimensions = imagecreatetruecolor($newWidth, $newHeight);
        } else if ($newHeightRequested == 'auto' && $newWidthRequested != 'auto') {
            // auto height

            $newWidth = (int) $newWidthRequested;
            $newHeight = (int) $newWidth / $this->aspectRatio_W_org;
            $this->newWidth = $newWidth;
            $this->newHeight = $newHeight;
            $this->newImageDimensions = imagecreatetruecolor($newWidth, $newHeight);
        } else if ($newHeightRequested != 'auto' && $newWidthRequested == 'auto') {
            // auto width

            $newHeight = (int) $newHeightRequested;
            $newWidth = (int) $newHeight * $this->aspectRatio_W_org;
            $this->newWidth = $newWidth;
            $this->newHeight = $newHeight;
            $this->newImageDimensions = imagecreatetruecolor($newWidth, $newHeight);
        } else {

            $error += 1;
        }

        if ($error == 0) {
            $this->imagePrep();
            $this->resampleImage();
        } else {
            return false;
        }
    }

    public function imagePrep()
    {
        $image_type = $this->image_type;
        $typeDigit = $this->typeInDigit;

        if ($image_type == 'image/png' || strpos($image_type, 'png') ||  $typeDigit == 3) {
            $this->imageBuild =  imagecreatefrompng($this->filePath);
        } else if ($image_type == 'image/jpeg' || strpos($image_type, 'jpg') || $typeDigit == 2) {
            $this->imageBuild =  imagecreatefromjpeg($this->filePath);
        } else if (strpos($image_type, 'bmp') || $typeDigit == 6) {
            $this->imageBuild =  imagecreatefrombmp($this->filePath);
        } else if ($image_type == "image/gif" || strpos($image_type, 'gif') || $typeDigit == 1) {
            $this->imageBuild =  imagecreatefromgif($this->filePath);
        } else if ($image_type == "image/webp" || strpos($image_type, 'webp') || $typeDigit == 5) {
            $this->imageBuild =  imagecreatefromwebp($this->filePath);
        } else {

            return false;
        }
    }

    public function resampleImage()
    {

        imagecopyresampled($this->newImageDimensions, $this->imageBuild, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->width_org, $this->height_org);
    }

    public function reproduceSaveImage(array $args = null)
    {
        // prepare default values
        $quality = (int) (isset($args['quality'])) ? $args['quality'] : 100;
        $baseDirectory = dirname($this->filePath);
        $filename = basename($this->filePath);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $imgDimensionString = "--{$this->newWidth}x{$this->newHeight}";

        /*
        $filename = str_replace('.'.$ext, $imgDimensionString, $filename) . '.'.$ext;
        */



        $filename = $baseDirectory . '/thumbnails/' . $filename;

        switch ($this->typeInDigit) {
            case 1:
                return imagegif($this->newImageDimensions, $filename, $quality);
                break;
            case 2:
                return imagejpeg($this->newImageDimensions, $filename, $quality);
                break;
            case 3:
                $pngQuality = ($quality - 100) / 11.111111;
                $pngQuality = round(abs($pngQuality));
                return imagepng($this->newImageDimensions, $filename, $pngQuality);
                break;
            case 18:

                return imagewebp($this->newImageDimensions, $filename, $quality);
                break;
        }
    }
}
