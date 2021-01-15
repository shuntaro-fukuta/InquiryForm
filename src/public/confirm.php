<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'config' . DIRECTORY_SEPARATOR . 'init.php');
require_once(LIB_DIR . DIRECTORY_SEPARATOR . 'Validator.php');
require_once(LIB_DIR . DIRECTORY_SEPARATOR . 'functions.php');
require_once(CLASSES_DIR . DIRECTORY_SEPARATOR . 'Forms' . DIRECTORY_SEPARATOR . 'InquiryForm.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['inquiry_form'])) {
        header('HTTP/1.0 400 Bad Request');
        exit;
    }

    $inquiry_form = $_SESSION['inquiry_form'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inquiry_form = new InquiryForm();
    $inquiry_form->setSubject(get_element_from_post_parameters('subject'));
    $inquiry_form->setName(get_element_from_post_parameters('name'));
    $inquiry_form->setEmail(get_element_from_post_parameters('email'));
    $inquiry_form->setTelephoneNumber(get_element_from_post_parameters('telephone_number'));
    $inquiry_form->setInquiry(get_element_from_post_parameters('inquiry'));

    $errors = $inquiry_form->validate();
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['inquiry_form'] = $inquiry_form;
        header('Location: inquiry_form.php');
        exit;
    }

    // 二重サブミット対策のためリダイレクト
    $_SESSION['inquiry_form'] = $inquiry_form;
    header('Location: confirm.php');
    exit;
}

include(VIEW_DIR . DIR_SEP . 'confirm_view.php');
