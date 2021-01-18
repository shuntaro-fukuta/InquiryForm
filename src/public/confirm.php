<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'config' . DIRECTORY_SEPARATOR . 'init.php');
require_once(CLASSES_DIR . DIR_SEP . 'Validator.php');
require_once(LIB_DIR . DIR_SEP . 'functions.php');
require_once(CLASSES_DIR . DIR_SEP . 'form' . DIR_SEP . 'InquiryForm.php');

session_start();

if (!isset($_SESSION['csrf_token'])) {
    error403();
}

if (!isset($_SESSION['inquiry_form'])) {
    error400();
}

$inquiry_form = $_SESSION['inquiry_form'];

include(VIEW_DIR . DIR_SEP . 'confirm_view.php');
