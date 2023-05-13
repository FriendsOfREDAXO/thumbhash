<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ThumbHash;

use rex;
use rex_sql_column;
use rex_sql_table;

// Add column `thumbhash` + `thumbhashimg` to table `rex_media`
rex_sql_table::get(rex::getTable('media'))
    ->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('thumbhash', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('thumbhashimg', 'text', true))
    ->ensure();
