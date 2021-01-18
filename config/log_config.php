<?php

const LOG_DIR = APP_ROOT . DIR_SEP . 'log';
const LOG_FILENAME = 'error.log';

if (!file_exists(LOG_DIR . DIR_SEP . LOG_FILENAME)) {
    touch(LOG_DIR . DIR_SEP . LOG_FILENAME);
    chmod(LOG_DIR . DIR_SEP . LOG_FILENAME, 0755);
}

ini_set('error_log', LOG_DIR . DIR_SEP . LOG_FILENAME);
