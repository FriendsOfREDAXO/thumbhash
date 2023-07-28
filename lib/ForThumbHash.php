<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ThumbHash;

use Exception;
use rex;
use rex_addon;
use rex_file;
use rex_media;
use rex_media_cache;
use rex_path;
use rex_sql;

use function in_array;

final class ForThumbHash
{
    /**
     * Get ThumbHash for MediaPool-File.
     * @api
     */
    public static function getThumbHash(string $media_filename): string
    {
        $media = rex_media::get($media_filename);
        if (null === $media) {
            throw new Exception('Media ' . $media_filename . ' not found!');
        }

        if (!in_array(rex_file::extension($media_filename), ForThumbHashMedia::IMAGE_TYPES, true)) {
            throw new Exception('Invalid extension in file ' . $media_filename . '! Valid extensions: ' . implode(', ', ForThumbHashMedia::IMAGE_TYPES));
        }

        $thumbhash = $media->getValue('thumbhash');
        if (null === $thumbhash || '' === $thumbhash) {
            $path = rex_path::media($media_filename);
            $thumbhashdata = ForThumbHashMedia::generateMediaThumbhash($path);
            ForThumbHashMedia::updateMediaThumbhash($media->getId(), $thumbhashdata['thumbhash'], $thumbhashdata['thumbhashimg']);
            $thumbhash = $thumbhashdata['thumbhash'];
            rex_media_cache::delete($media_filename);
        }

        return $thumbhash;
    }

    /**
     * Get ThumbHash-Image for MediaPool-File.
     * @api
     */
    public static function getThumbHashImg(string $media_filename): string
    {
        $media = rex_media::get($media_filename);
        if (null === $media) {
            throw new Exception('Media ' . $media_filename . ' not found!');
        }

        if (!in_array(rex_file::extension($media_filename), ForThumbHashMedia::IMAGE_TYPES, true)) {
            throw new Exception('Invalid extension in file ' . $media_filename . '! Valid extensions: ' . implode(', ', ForThumbHashMedia::IMAGE_TYPES));
        }

        $thumbhashimg = $media->getValue('thumbhashimg');
        if (null === $thumbhashimg || '' === $thumbhashimg) {
            $path = rex_path::media($media_filename);
            $thumbhashdata = ForThumbHashMedia::generateMediaThumbhash($path);
            ForThumbHashMedia::updateMediaThumbhash($media->getId(), $thumbhashdata['thumbhash'], $thumbhashdata['thumbhashimg']);
            $thumbhashimg = $thumbhashdata['thumbhashimg'];
            rex_media_cache::delete($media_filename);
        }

        return $thumbhashimg;
    }

    /**
     * Get ThumbHash for File.
     * @api
     */
    public static function getThumbHashForFile(string $path): string
    {
        if (!file_exists($path)) {
            throw new Exception('File ' . $path . ' not found!');
        }

        if (!in_array(rex_file::extension($path), ForThumbHashMedia::IMAGE_TYPES, true)) {
            throw new Exception('Invalid extension in file ' . $path . '! Valid extensions: ' . implode(', ', ForThumbHashMedia::IMAGE_TYPES));
        }

        $thumbhashdata = ForThumbHashMedia::generateMediaThumbhash($path);
        return $thumbhashdata['thumbhash'];
    }

    /**
     * Get ThumbHash-Image for File.
     * @api
     */
    public static function getThumbHashImgForFile(string $path): string
    {

        if (!file_exists($path)) {
            throw new Exception('File ' . $path . ' not found!');
        }

        if (!in_array(rex_file::extension($path), ForThumbHashMedia::IMAGE_TYPES, true)) {
            throw new Exception('Invalid extension in file ' . $path . '! Valid extensions: ' . implode(', ', ForThumbHashMedia::IMAGE_TYPES));
        }

        $thumbhashdata = ForThumbHashMedia::generateMediaThumbhash($path);
        return $thumbhashdata['thumbhashimg'];
    }

    /**
     * Get the Script-Tag for frontend.
     * @api
     */
    public static function getScript(): string
    {
        return '<script type="module" defer>' . PHP_EOL . rex_file::get(rex_addon::get('thumbhash')->getAssetsUrl('thumbhash_fe.min.js')) . PHP_EOL . '</script>';
    }

    /**
     * Get the Script-Tag for frontend.
     * @api
     */
    public static function getScriptTag(): string
    {
        return '<script type="module" src="' . rex_addon::get('thumbhash')->getAssetsUrl('thumbhash_fe.min.js') . '" defer></script>';
    }

    /**
     * Create all ThumbHash-Data in MediaPool.
     * @return int
     * @api
     */
    public static function createThumbHashes()
    {
        $tcount = 0;

        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('media'));
        $sql->select('*');

        for ($i = 0; $i < $sql->getRows(); ++$i) {
            $filename = $sql->getValue('filename');

            if (null === $filename) {
                $sql->next();
                continue;
            }

            if (in_array(rex_file::extension($filename), \FriendsOfRedaxo\ThumbHash\ForThumbHashMedia::IMAGE_TYPES, true)) {
                rex_media_cache::delete($filename);
                $thumbhash = self::getThumbHash($filename);
                if ('' !== $thumbhash) {
                    ++$tcount;
                }
            }

            $sql->next();
        }

        return $tcount;
    }

    /**
     * Clear all ThumbHash-Data in MediaPool.
     * @return string|bool
     * @api
     */
    public static function clearThumbHashes()
    {
        $sql = rex_sql::factory();
        $sql->setDebug(false);
        $sql->setQuery('UPDATE `' . rex::getTable('media') . "` SET `thumbhash` = '', `thumbhashimg` = ''");

        if (!$sql->hasError()) {
            return true;
        }

        if (null === $sql->getError()) {
            return true;
        }

        return $sql->getError();
    }
}
