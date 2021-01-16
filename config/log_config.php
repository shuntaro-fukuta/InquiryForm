<?php

const LOG_DIR = APP_ROOT . DIR_SEP . 'log';

const LOG_FILENAME = 'error.log';

ini_set('error_log', LOG_DIR . DIR_SEP . LOG_FILENAME);
