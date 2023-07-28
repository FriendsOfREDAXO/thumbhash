<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ThumbHash;

use rex;
use rex_sql;

if (rex::isBackend()) {
    $sql = rex_sql::factory();
    $sql->setDebug(true);
    $sql->setQuery('ALTER TABLE `' . rex::getTable('media') . '` DROP `thumbhash`, DROP `thumbhashimg`;');
}
