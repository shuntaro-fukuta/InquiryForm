<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'config' . DIRECTORY_SEPARATOR . 'init.php');
require_once(LIB_DIR . DIRECTORY_SEPARATOR . 'Validator.php');
require_once(LIB_DIR . DIRECTORY_SEPARATOR . 'functions.php');
require_once(CLASSES_DIR . DIRECTORY_SEPARATOR . 'Forms' . DIRECTORY_SEPARATOR . 'InquiryForm.php');

session_start();

if (!isset($_SESSION['inquiry_form'])) {
    // TODO: registerのほうと共通化
    header('HTTP/1.0 400 Bad Request');
    include(HTML_DIR . DIR_SEP . 'error' . DIR_SEP . '400.html');
    exit;
}

$inquiry_form = $_SESSION['inquiry_form'];

include(VIEW_DIR . DIR_SEP . 'confirm_view.php');
