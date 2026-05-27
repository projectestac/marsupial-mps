<?php

// Load libraries.
include_once '../../../config.php';

// Truncate log table.
if (!delete_records('log')) {
    log_to_file("Action 4-1 KO!");
} else {
    log_to_file("Action 4-1 OK!");
}

// Truncate sessions table.
if (!delete_records('sessions')) {
    log_to_file("Action 4-2 KO!");
} else {
    log_to_file("Action 4-2 OK!");
}

function log_to_file($info): void
{
    global $CFG;

    $directorio_log = $CFG->dataroot . '/cronlog';

    if (!is_dir($directorio_log) && !mkdir($directorio_log) && !is_dir($directorio_log)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $directorio_log));
    }

    if ($handle = @fopen($directorio_log . '/cron.log', "a")) {
        $content = "\r\n" . date("Y-m-d H:i:s") . ' - Success: ' . $info;

        @fwrite($handle, $content);
        @fclose($handle);
    }
}
