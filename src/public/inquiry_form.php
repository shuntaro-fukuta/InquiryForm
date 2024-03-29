<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'config' . DIRECTORY_SEPARATOR . 'init.php');
require_once(LIB_DIR . DIR_SEP . 'functions.php');
require_once(CLASSES_DIR . DIR_SEP . 'form' . DIR_SEP . 'InquiryForm.php');
require_once(CLASSES_DIR . DIR_SEP . 'db' . DIR_SEP . 'DBConfig.php');
require_once(CLASSES_DIR . DIR_SEP . 'mail' . DIR_SEP . 'Mailer.php');
require_once(CLASSES_DIR . DIR_SEP . 'mail' . DIR_SEP . 'MailAuthConfig.php');
require_once(CLASSES_DIR . DIR_SEP . 'mail' . DIR_SEP . 'MailHeaderConfig.php');

header('X-FRAME-OPTIONS: SAMEORIGIN');

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$inquiry_form = null;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['inquiry_form'])) {
        $inquiry_form = $_SESSION['inquiry_form'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inquiry_form = new InquiryForm();
    $inquiry_form->setSubject(get_element_from_post_parameters('subject'));
    $inquiry_form->setName(get_element_from_post_parameters('name'));
    $inquiry_form->setEmail(get_element_from_post_parameters('email'));
    $inquiry_form->setTelephoneNumber(get_element_from_post_parameters('telephone_number'));
    $inquiry_form->setInquiry(get_element_from_post_parameters('inquiry'));

    $errors = $inquiry_form->validate();

    if (empty($errors)) {
        $_SESSION['inquiry_form'] = $inquiry_form;
        header('Location: confirm.php');
        exit;
    }
}

include(VIEW_DIR . DIR_SEP . 'inquiry_form_view.php');
