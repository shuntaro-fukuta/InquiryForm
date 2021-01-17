<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'config' . DIRECTORY_SEPARATOR . 'init.php');
require_once(LIB_DIR . DIR_SEP . 'functions.php');
require_once(CLASSES_DIR . DIRECTORY_SEPARATOR . 'Forms' . DIRECTORY_SEPARATOR . 'InquiryForm.php');
require_once(LIB_DIR . DIR_SEP . 'db' . DIR_SEP . 'Database.php');
require_once(LIB_DIR . DIR_SEP . 'db' . DIR_SEP . 'DBConfig.php');

require_once(LIB_DIR . DIR_SEP . 'mail' . DIR_SEP . 'Mailer.php');
require_once(LIB_DIR . DIR_SEP . 'mail' . DIR_SEP . 'config' . DIR_SEP . 'MailAuthConfig.php');
require_once(LIB_DIR . DIR_SEP . 'mail' . DIR_SEP . 'config' . DIR_SEP . 'MailHeaderConfig.php');
require_once(LIB_DIR . DIR_SEP . 'PHPMailer' . DIR_SEP . 'src' . DIR_SEP . 'PHPMailer.php');
require_once(LIB_DIR . DIR_SEP . 'PHPMailer' . DIR_SEP . 'src' . DIR_SEP . 'Exception.php');
require_once(LIB_DIR . DIR_SEP . 'PHPMailer' . DIR_SEP . 'src' . DIR_SEP . 'SMTP.php');

header('X-FRAME-OPTIONS: SAMEORIGIN');

session_start();
$post_csrf_token = $_POST['csrf_token'] ?? null;
$session_csrf_token = $_SESSION['csrf_token'] ?? null;
if (is_null($post_csrf_token) || is_null($session_csrf_token) || $post_csrf_token !== $session_csrf_token) {
    error403();
}

if (!isset($_SESSION['inquiry_form'])) {
    error400();
}

$inquiry_form = $_SESSION['inquiry_form'];
$is_inserted = false;
try {
    $db_config = new DBConfig();
    $database = new Database($db_config);
    $is_inserted = $database->insert('inquiries', [
        'subject' => $inquiry_form->getSubject(),
        'name' => $inquiry_form->getName(),
        'email' => $inquiry_form->getEmail(),
        'telephone_number' => $inquiry_form->getTelephoneNumber(),
        'inquiry' => $inquiry_form->getInquiry(),
    ]);

    if ($is_inserted) {
        $user_mail = create_user_mail($inquiry_form);
        $user_mail->send();

        $admin_mail = create_admin_mail($inquiry_form);
        $admin_mail->send();
    }
} catch (PDOException $e) {
    $is_inserted = false;
    error_log($e->getMessage(), 0);
}

$_SESSION = [];
$location = $is_inserted ? 'register_success.php' : 'register_failed.php';
header('Location: ' . $location);

function create_admin_mail(InquiryForm $inquiry_form) {
    $mail_auth_config = new MailAuthConfig('admin_inquiry');

    $mail_header_config = new MailHeaderConfig('admin_inquiry');
    $mail_header_config->setTo($mail_header_config->getTo());

    $mailer = new Mailer($mail_auth_config, $mail_header_config);

    $template = Mailer::getMailTemplate('admin_inquiry.php');
    $inquiry_sent_time = new DateTime();
    $mailer->setBody(sprintf($template,
        $inquiry_sent_time->format('Y年m月d日 H時i分s秒'),
        InquiryForm::SUBJECT[$inquiry_form->getSubject()],
        $inquiry_form->getName(),
        $inquiry_form->getTelephoneNumber(),
        $inquiry_form->getEmail(),
        $inquiry_form->getInquiry()
    ));

    return $mailer;
}

function create_user_mail(InquiryForm $inquiry_form) {
    $mail_auth_config = new MailAuthConfig('user_inquiry');

    $mail_header_config = new MailHeaderConfig('user_inquiry');
    $mail_header_config->setTo($inquiry_form->getEmail());

    $mailer = new Mailer($mail_auth_config, $mail_header_config);

    $template = Mailer::getMailTemplate('user_inquiry.php');
    $inquiry_sent_time = new DateTime();
    $mailer->setBody(sprintf($template,
        $inquiry_sent_time->format('Y年m月d日 H時i分s秒'),
        InquiryForm::SUBJECT[$inquiry_form->getSubject()],
        $inquiry_form->getName(),
        $inquiry_form->getTelephoneNumber(),
        $inquiry_form->getEmail(),
        $inquiry_form->getInquiry()
    ));

    return $mailer;
}
