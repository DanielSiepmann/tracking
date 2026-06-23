<?php

declare(strict_types=1);

(static function (string $tableName = 'pages'): void {
    $GLOBALS['TCA'][$tableName]['ctrl']['defaultAllowedRecordTypes'][] = 'tx_tracking_pageview';
    $GLOBALS['TCA'][$tableName]['ctrl']['defaultAllowedRecordTypes'][] = 'tx_tracking_recordview';
})();
