<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ThumbHash;

use rex;
use rex_extension;

if (rex::isBackend()) {
    $class = ForThumbHashMedia::class;

    // Save ThumbHash+ThumbImage in table `rex_media` (columns `thumbhash` + `thumbhashimg`)
    rex_extension::register('MEDIA_ADDED', [$class, 'processUploadedMedia'], rex_extension::LATE);
    rex_extension::register('MEDIA_UPDATED', [$class, 'processUploadedMedia'], rex_extension::LATE);

    // Show ThumbHash+ThumbImage in MediaPool-Detail
    rex_extension::register('MEDIA_DETAIL_SIDEBAR', [$class, 'mediapoolDetailOutput']);
}
