<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ThumbHash;

use Exception;
use rex;
use rex_extension_point;
use rex_file;
use rex_path;
use rex_sql;

use function in_array;
use function is_array;
use function is_int;
use function is_object;
use function is_string;

final class ForThumbHashMedia
{
    /**
     * Valid Image-Types.
     * @var string[]
     */
    public const IMAGE_TYPES = ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'wbmp', 'webp'];

    /**
     * Media Upload/Update processing. Store ThumbHash+ThumbImage in table rex_media.
     * @param rex_extension_point<array<string, string>> $ep
     * @api
     */
    public static function processUploadedMedia(rex_extension_point $ep): void
    {
        $media_filename = $ep->getParam('filename');
        if (!is_string($media_filename)) {
            throw new Exception('Media-FileName is not a string.');
        }

        // Process only valid extensions
        if (!in_array(rex_file::extension($media_filename), self::IMAGE_TYPES, true)) {
            return;
        }

        $media_id = $ep->getParam('id');
        // new media - get the id
        if (!is_int($media_id)) {
            $sql = rex_sql::factory();
            $sql->setDebug(false);
            $sql->setTable(rex::getTable('media'));
            $sql->setWhere(['filename' => $media_filename]);
            $sql->select('id');
            $media_id = $sql->getValue('id');
        }

        $path = rex_path::media($media_filename);
        $thumbhashdata = self::generateMediaThumbhash($path);
        self::updateMediaThumbhash($media_id, $thumbhashdata['thumbhash'], $thumbhashdata['thumbhashimg']);
    }

    /**
     * Generate ThumbHash-Data from image.
     * @return array{thumbhash: string, thumbhashimg: string}
     * @api
     */
    public static function generateMediaThumbhash(string $path): array
    {
        if (!file_exists($path)) {
            throw new Exception('File ' . $path . ' not found.');
        }

        $thumbhash = '';
        $thumbhashimg = '';

        $imagedata = ForThumbHashHelper::getSizeAndPixelsFromFile($path);
        if (false !== $imagedata && is_array($imagedata) && is_int($imagedata[0]) && is_int($imagedata[1]) && is_array($imagedata[2])) {
            $hash = \Thumbhash\Thumbhash::RGBAToHash($imagedata[0], $imagedata[1], $imagedata[2]);
            $thumbhash = \Thumbhash\Thumbhash::convertHashToString($hash);
            $thumbhashimg = \Thumbhash\Thumbhash::toDataURL($hash);
        }

        return ['thumbhash' => $thumbhash, 'thumbhashimg' => $thumbhashimg];
    }

    /**
     * Update ThumbHash in table rex_media.
     * @api
     */
    public static function updateMediaThumbhash(int $id, string $thumbhash, string $thumbhashimg): void
    {
        $sql = rex_sql::factory();
        $sql->setDebug(false);
        $sql->setTable(rex::getTable('media'));
        $sql->setValue('thumbhash', $thumbhash);
        $sql->setValue('thumbhashimg', $thumbhashimg);
        $sql->setWhere(['id' => $id]);
        $sql->update();
    }

    /**
     * Show ThumbHash+ThumbImage in MediaPool-Detail.
     * @param rex_extension_point<array<string, string>> $ep
     * @api
     */
    public static function mediapoolDetailOutput(rex_extension_point $ep): string
    {

        $subject = (string) $ep->getSubject(); /** @phpstan-ignore-line */

        $media_filename = $ep->getParam('filename');
        if (!is_string($media_filename)) {
            throw new Exception('Media-FileName is no string.');
        }

        if (!in_array(rex_file::extension($media_filename), self::IMAGE_TYPES, true)) {
            return $subject;
        }

        if (is_object($ep->getParam('media')) && $ep->getParam('media') instanceof rex_sql) {
            $thumbhash = $ep->getParam('media')->getValue('thumbhash');
            if (null !== $thumbhash && '' !== $thumbhash) {
                $subject .= '<br><p><strong>ThumbHash</strong>:<br>' . $thumbhash . '</p>';
            }

            $thumbhashimg = $ep->getParam('media')->getValue('thumbhashimg');
            if (null !== $thumbhashimg && '' !== $thumbhashimg) {
                $subject .= '<img src="' . $ep->getParam('media')->getValue('thumbhashimg') . '" />';
            }
        }

        return $subject;
    }
}
