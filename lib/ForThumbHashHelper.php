<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ThumbHash;

use GdImage;

final class ForThumbHashHelper
{
    /** @var int */

    private const MAX_IMAGE_SIZE = 100;

    /**
     * Get width+height and pixels from image (string) aspect ratio. Resized to maximum size 100px.
     * @api
     */
    public static function getSizeAndPixels(string $content): mixed
    {
        $source = imagecreatefromstring($content);

        $pixels = [];

        if (false !== $source) {
            $width = imagesx($source);
            $height = imagesy($source);

            $imgRatio = $width / $height;

            // dump($width, $height, $imgRatio);

            if ($imgRatio > 1) {
                $newwidth = self::MAX_IMAGE_SIZE;
                $newheight = (int) (self::MAX_IMAGE_SIZE / $imgRatio);
            } else {
                $newwidth = (int) (self::MAX_IMAGE_SIZE * $imgRatio);
                $newheight = self::MAX_IMAGE_SIZE;
            }

            // dump($newwidth, $newheight);

            $thumb = imagecreatetruecolor($newwidth, $newheight);
            if (false !== $thumb) {
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                $width = $newwidth;
                $height = $newheight;

                $pixels = self::getPixels($thumb, $width, $height);
            }

            return [$width, $height, $pixels];
        }

        return false;
    }

    /**
     * Get width+height and pixels from image (filename) aspect ratio. Resized to maximum size 100px.
     * @api
     */
    public static function getSizeAndPixelsFromFile(string $filename): mixed
    {
        $content = file_get_contents($filename);
        if (false !== $content) {
            return self::getSizeAndPixels($content);
        }

        return false;
    }

    /**
     * Get pixels from image.
     * @api
     */
    public static function getPixels(GdImage $thumb, int $width, int $height): mixed
    {
        $pixels = [];
        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                $color_index = imagecolorat($thumb, $x, $y);
                if (false !== $color_index) {
                    $color = imagecolorsforindex($thumb, $color_index);
                    $alpha = 255 - ceil($color['alpha'] * (255 / 127)); // GD only supports 7-bit alpha channel
                    $pixels[] = $color['red'];
                    $pixels[] = $color['green'];
                    $pixels[] = $color['blue'];
                    $pixels[] = $alpha;
                }
            }
        }

        return $pixels;
    }
}
