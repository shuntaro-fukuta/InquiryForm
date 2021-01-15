<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'config' . DIRECTORY_SEPARATOR . 'init.php');
require_once(LIB_DIR . DIR_SEP . 'functions.php');
require_once(CLASSES_DIR . DIRECTORY_SEPARATOR . 'Forms' . DIRECTORY_SEPARATOR . 'InquiryForm.php');

session_start();

$inquiry_form = null;
if (isset($_SESSION['inquiry_form'])) {
    $inquiry_form = $_SESSION['inquiry_form'];
    unset($_SESSION['inquiry_form']);
}

$errors = [];
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}

include(VIEW_DIR . DIR_SEP . 'inquiry_form_view.php');
