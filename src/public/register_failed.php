<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'config' . DIRECTORY_SEPARATOR . 'init.php');

header('X-FRAME-OPTIONS: SAMEORIGIN');

include(VIEW_DIR . DIR_SEP . 'register_failed_view.php');
