<?php

/*
 * File: SimpleImage.php
 * Author: Simon Jarvis
 * Copyright: 2006 Simon Jarvis
 * Date: 08/11/06
 * Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
 * 
 * This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation; either version 2 
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details: 
 * http://www.gnu.org/licenses/gpl.html
 *
 */

class SimpleImage {

    var $image;
    var $image_type;
    var $mask;

    function load($filename) {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if ($this->image_type == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
        }
    }

    function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 95, $permissions = null) {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image, $filename);
        }
        if ($permissions != null) {
            chmod($filename, $permissions);
        }
    }

    function output($image_type = IMAGETYPE_JPEG) {
        if ($image_type == IMAGETYPE_JPEG) {
            imagealphablending($this->image, false);
            imagesavealpha($this->image, true);
            imagejpeg($this->image, null, 100);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagealphablending($this->image, false);
            imagesavealpha($this->image, true);
            imagegif($this->image);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagealphablending($this->image, false);
            imagesavealpha($this->image, true);
            imagepng($this->image);
        }
        imagedestroy($this->image);
        @imagedestroy($this->mask);
    }

    function getWidth() {
        return imagesx($this->image);
    }

    function getHeight() {
        return imagesy($this->image);
    }

    function resizeToHeight($height) {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    function scale($scale) {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }

    function resize($width, $height) {
        $new_image = imagecreatetruecolor($width, $height);
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
        imagefilledrectangle($new_image, 0, 0, $this->getWidth(), $this->getHeight(), $transparent);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

    public function crop($newWidth, $newHeight, $x, $y) {
        $crop = $this->image;
        $new_image = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($new_image, $crop, 0, 0, $x, $y, $newWidth, $newHeight, $newWidth, $newHeight);
        $this->image = $new_image;
    }

    public function rotate($graus) {
        $this->image = imagerotate($this->image, $graus, 0);
    }

    public function brilho($escala = 0) {
        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $escala);
    }

    public function contraste($escala = 0) {
        imagefilter($this->image, IMG_FILTER_CONTRAST, $escala);
    }

    public function colorir($r = 0, $g = 0, $b = 0) {
        imagefilter($this->image, IMG_FILTER_COLORIZE, $r, $g, $b);
    }

    public function suavizar($escala = 0) {
        imagefilter($this->image, IMG_FILTER_SMOOTH, $escala);
    }

    public function desfocar() {
        $emboss = array(array(1, 1, 1), array(1, 0, 1), array(1, 1, 1));
        imageconvolution($this->image, $emboss, 8, 0);
        //imagefilter($this->image, IMG_FILTER_GAUSSIAN_BLUR);
    }

    public function negativa() {
        imagefilter($this->image, IMG_FILTER_NEGATE);
    }

    public function escala_de_cinza() {
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
    }

    public function sepia() {
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        imagefilter($this->image, IMG_FILTER_COLORIZE, 100, 45, 30);
    }
    
    public function hipster() {
        //imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, -5);
        imagefilter($this->image, IMG_FILTER_CONTRAST, -5);
        imagefilter($this->image, IMG_FILTER_COLORIZE, 60, 40, -25);
    }

    public function envelhecida() {

        $this->mask = imagecreatefrompng('imgs/mascaras/mask-linhas.png');
        $tempPic = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        imagecopyresampled($tempPic, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
        imagecopyresampled($tempPic, $this->mask, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
        $this->image = $tempPic;


        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        imagefilter($this->image, IMG_FILTER_COLORIZE, 100, 45, 0);

    }

    public function borda($borda){
        if($borda=="borda1") $this->borda1();
        if($borda=="borda2") $this->borda2();
        if($borda=="borda3") $this->borda3();
    }
    private function borda1() {
        $this->mask = imagecreatefrompng('imgs/mascaras/border-mask.png');
        $tempPic = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        imagecopyresampled($tempPic, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
        imagecopyresampled($tempPic, $this->mask, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
        $this->image = $tempPic;
    }

    private function borda2() {
        $this->mask = imagecreatefrompng('imgs/mascaras/border-mask2.png');
        $tempPic = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        $bg = imagecolorallocate($tempPic, 255, 255, 255);
        imagefill($tempPic, 0, 0, $bg);
        imagecopyresampled($tempPic, $this->image, 10, 10, 0, 0, $this->getWidth() - 20, $this->getHeight() - 20, $this->getWidth(), $this->getHeight());
        imagecopyresampled($tempPic, $this->mask, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
        $this->image = $tempPic;
    }

    private function borda3() {
        $this->mask = imagecreatefrompng('imgs/mascaras/border-mask3.png');
        $tempPic = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        $bg = imagecolorallocate($tempPic, 255, 255, 255);
        imagefill($tempPic, 0, 0, $bg);
        imagecopyresampled($tempPic, $this->image, 10, 10, 0, 0, $this->getWidth() - 20, $this->getHeight() - 20, $this->getWidth(), $this->getHeight());
        imagecopyresampled($tempPic, $this->mask, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
        $this->image = $tempPic;
    }

}

class Pixel {

    function Pixel($r, $g, $b) {
        $this->r = ($r > 255) ? 255 : (($r < 0) ? 0 : (int) ($r));
        $this->g = ($g > 255) ? 255 : (($g < 0) ? 0 : (int) ($g));
        $this->b = ($b > 255) ? 255 : (($b < 0) ? 0 : (int) ($b));
    }

}

class Image_PixelOperations {

    function pixelOperation(
    $input_image, $w, $h, $operation_callback, $factor = false
    ) {

        $image = $input_image;
        $new_image = imagecreatetruecolor($w, $h);

        if ($operation_callback == 'contrast') {
            $average_luminance = $this->getAverageLuminance($image);
        } else {
            $average_luminance = false;
        }

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {

                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $pixel = new Pixel($r, $g, $b);
                $pixel = call_user_func(
                        $operation_callback, $pixel, $factor, $average_luminance
                );

                $color = imagecolorallocate(
                        $image, $pixel->r, $pixel->g, $pixel->b
                );
                imagesetpixel($new_image, $x, $y, $color);
            }
        }
        return $new_image;
        //imagepng($new_image, $output_image);
    }

    function addNoise($pixel, $factor) {
        $random = mt_rand(-$factor, $factor);
        return new Pixel(
                        $pixel->r + $random,
                        $pixel->g + $random,
                        $pixel->b + $random
        );
    }

    function adjustBrightness($pixel, $factor) {

        return new Pixel(
                        $pixel->r + $factor,
                        $pixel->g + $factor,
                        $pixel->b + $factor
        );
    }

    function swapColors($pixel, $factor) {

        switch ($factor) {
            case 'rbg':
                return new Pixel(
                                $pixel->r,
                                $pixel->b,
                                $pixel->g
                );
                break;
            case 'bgr':
                return new Pixel(
                                $pixel->b,
                                $pixel->g,
                                $pixel->r
                );
                break;
            case 'brg':
                return new Pixel(
                                $pixel->b,
                                $pixel->r,
                                $pixel->g
                );
                break;
            case 'gbr':
                return new Pixel(
                                $pixel->g,
                                $pixel->b,
                                $pixel->r
                );
                break;
            case 'grb':
                return new Pixel(
                                $pixel->g,
                                $pixel->r,
                                $pixel->b
                );
                break;
            default:
                return $pixel;
        }
    }

    function removeColor($pixel, $factor) {

        if ($factor == 'r') {
            $pixel->r = 0;
        }
        if ($factor == 'g') {
            $pixel->g = 0;
        }
        if ($factor == 'b') {
            $pixel->b = 0;
        }
        if ($factor == 'rb' || $factor == 'br') {
            $pixel->r = 0;
            $pixel->b = 0;
        }
        if ($factor == 'rg' || $factor == 'gr') {
            $pixel->r = 0;
            $pixel->g = 0;
        }
        if ($factor == 'bg' || $factor == 'gb') {
            $pixel->b = 0;
            $pixel->g = 0;
        }

        return $pixel;
    }

    function maxColor($pixel, $factor) {

        if ($factor == 'r') {
            $pixel->r = 255;
        }
        if ($factor == 'g') {
            $pixel->g = 255;
        }
        if ($factor == 'b') {
            $pixel->b = 255;
        }
        if ($factor == 'rb' || $factor == 'br') {
            $pixel->r = 255;
            $pixel->b = 255;
        }
        if ($factor == 'rg' || $factor == 'gr') {
            $pixel->r = 255;
            $pixel->g = 255;
        }
        if ($factor == 'bg' || $factor == 'gb') {
            $pixel->b = 255;
            $pixel->g = 255;
        }

        return $pixel;
    }

    function negative($pixel) {
        return new Pixel(
                        255 - $pixel->g,
                        255 - $pixel->r,
                        255 - $pixel->b
        );
    }

    function greyscale($pixel) {

        $pixel_average = ($pixel->r + $pixel->g + $pixel->b) / 3;

        return new Pixel(
                        $pixel_average,
                        $pixel_average,
                        $pixel_average
        );
    }

    function blackAndWhite($pixel, $factor) {
        $pixel_total = ($pixel->r + $pixel->g + $pixel->b);

        if ($pixel_total > (((255 + $factor) / 2) * 3)) {
            // white
            $pixel->r = 255;
            $pixel->g = 255;
            $pixel->b = 255;
        } else {
            $pixel->r = 0;
            $pixel->g = 0;
            $pixel->b = 0;
        }

        return $pixel;
    }

    function clip($pixel, $factor) {
        if ($pixel->r > 255 - $factor) {
            $pixel->r = 255;
        }
        if ($pixel->r < $factor) {
            $pixel->r = 0;
        }
        if ($pixel->g > 255 - $factor) {
            $pixel->g = 255;
        }
        if ($pixel->g < $factor) {
            $pixel->g = 0;
        }
        if ($pixel->b > 255 - $factor) {
            $pixel->b = 255;
        }
        if ($pixel->b < $factor) {
            $pixel->b = 0;
        }

        return $pixel;
    }

    function getAverageLuminance($image) {

        $luminance_running_sum = 0;

        $x_dimension = imagesx($image);
        $y_dimension = imagesy($image);

        for ($x = 0; $x < $x_dimension; $x++) {
            for ($y = 0; $y < $y_dimension; $y++) {

                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $luminance_running_sum += (0.30 * $r) + (0.59 * $g) + (0.11 * $b);
            }
        }

        $total_pixels = $x_dimension * $y_dimension;

        return $luminance_running_sum / $total_pixels;
    }

    function contrast($pixel, $factor, $average_luminance) {

        return new Pixel(
                        $pixel->r * $factor + (1 - $factor) * $average_luminance,
                        $pixel->g * $factor + (1 - $factor) * $average_luminance,
                        $pixel->b * $factor + (1 - $factor) * $average_luminance
        );
    }

    function saltAndPepper($pixel, $factor) {

        $black = (int) ($factor / 2 + 1);
        $white = (int) ($factor / 2 - 1);

        $random = mt_rand(0, $factor);

        $new_channel = false;

        if ($random == $black) {
            $new_channel = 0;
        }
        if ($random == $white) {
            $new_channel = 255;
        }

        if (is_int($new_channel)) {

            return new Pixel($new_channel, $new_channel, $new_channel);
        } else {
            return $pixel;
        }
    }

    function gamma($pixel, $factor) {

        return new Pixel(
                        pow($pixel->r / 255, $factor) * 255,
                        pow($pixel->g / 255, $factor) * 255,
                        pow($pixel->b / 255, $factor) * 255
        );
    }

    /**
     * @todo Revise the logic, there is something not quite right here
     *
     */
    function snap($pixel, $factor) {

        $proximity = $factor[1];
        $snap_to = $factor[0];

        $close = (int) (255 * $proximity / 100);


        if ($pixel->r + $close >= 255 && $pixel->g + $close >= 255 && $pixel->b + $close >= 255) {
            return $pixel; //skip whitish
        }


        $rgb = array('r', 'g', 'b');

        foreach ($rgb AS $channel) {
            if ($pixel->{$channel} < $snap_to->{$channel} && ($pixel->{$channel} + $close) > $snap_to->{$channel}) {
                $pixel->{$channel} = $snap_to->{$channel};
            }
            if ($pixel->{$channel} > $snap_to->{$channel} && ($pixel->{$channel} - $close) < $snap_to->{$channel}) {
                $pixel->{$channel} = $snap_to->{$channel};
            }
        }

        return $pixel;
    }

    /**
     *
     * @todo finish this method
     */
    function substitute($pixel, $factor) {

        $find_pixel = $factor[0];
        $replace_with_pixel = $factor[1];
        $proximity = $factor[2];

        $close = (int) (255 * $proximity / 100);

        // to do

        return $pixel;
    }

    function randomizeOperation($pixel, $factor) {

        $operations = array(
            'clip',
            'blackAndWhite',
            'greyscale',
            'negative',
            'maxColor',
            'removeColor',
            'swapColors',
            //'adjustBrightness', 
            'addNoise',
            //'contrast', //removed, too heavy for this purpose
            'saltAndPepper',
            'gamma',
        );
        $key = array_rand($operations);
        $operation = $operations[$key];

        switch ($operation) {
            case 'clip':
                $factor = mt_rand(0, 255);
                break;
            case 'maxColor':
            case 'removeColor':
                $factors = array('r', 'g', 'b', 'rg', 'rb', 'gb');
                $factor = $factors[array_rand($factors)];
                break;
            case 'swapColors':
                $factors = array('rbg', 'gbr', 'grb', 'bgr', 'brg');
                $factor = $factors[array_rand($factors)];
                break;
            case 'adjustBrightness':
                $factor = mt_rand(0, 255);
                break;
            case 'addNoise':
                $factor = mt_rand(0, 255);
                break;
            case 'saltAndPepper':
                $factor = mt_rand(0, 255);
                break;
            case 'gamma':
                $factor = mt_rand(0.5, 5);
                break;
            default:
                $factor = false;
                break;
        }

        return call_user_func(array($this, $operation), $pixel, $factor);
    }

}

?>
