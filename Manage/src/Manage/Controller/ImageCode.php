<?php
namespace Manage\Controller;

use Zend\Captcha\Exception;
use Zend\Captcha\Image;
use Zend\Stdlib\ErrorHandler;

class ImageCode extends Image{
    protected function generateImage($id, $word){

        $w     = $this->getWidth();
        $h     = $this->getHeight();
        $fsize = $this->getFontSize();

        $imgFile   = $this->getImgDir() . $id . $this->getSuffix();

        if (empty($this->startImage)) {
            $img = imagecreatetruecolor($w, $h);
        } else {
            // Potential error is change to exception
            ErrorHandler::start();
            $img   = imagecreatefrompng($this->startImage);
            $error = ErrorHandler::stop();
            if (!$img || $error) {
                throw new Exception\ImageNotLoadableException(
                    "Can not load start image '{$this->startImage}'",
                    0,
                    $error
                );
            }
            $w = imagesx($img);
            $h = imagesy($img);
        }

        $textColor = imagecolorallocate($img, 0, 0, 0);
        $bgColor   = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $w-1, $h-1, $bgColor);

        for($i=0; $i<strlen($word); $i++){
            $x = mt_rand(4,6)+$w*$i/4;
            $y = mt_rand(1, $h/4);
            $color = imagecolorallocate($img, mt_rand(0,100), mt_rand(0,150), mt_rand(0,200));
            imagestring($img, $fsize, $x, $y, $word[$i], $color);
        }

        // generate noise
        for ($i=0; $i < $this->dotNoiseLevel; $i++) {
            imagefilledellipse($img, mt_rand(0, $w), mt_rand(0, $h), 2, 2, $textColor);
        }
        for ($i=0; $i < $this->lineNoiseLevel; $i++) {
            $color = imagecolorallocate($img, mt_rand(0,100), mt_rand(0,150), mt_rand(0,200));
            imageline($img, mt_rand(0, $w), mt_rand(0, $h), mt_rand(0, $w), mt_rand(0, $h), $color);
        }

        imagepng($img, $imgFile);
        imagedestroy($img);
    }
}