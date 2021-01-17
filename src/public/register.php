<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'config' . DIRECTORY_SEPARATOR . 'init.php');
require_once(LIB_DIR . DIR_SEP . 'functions.php');
require_once(CLASSES_DIR . DIRECTORY_SEPARATOR . 'Forms' . DIRECTORY_SEPARATOR . 'InquiryForm.php');
require_once(LIB_DIR . DIR_SEP . 'db' . DIR_SEP . 'Database.php');
require_once(LIB_DIR . DIR_SEP . 'db' . DIR_SEP . 'DBConfig.php');

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
        $user_mail = create_user_mail_from_inquiry_form($inquiry_form);
        $user_mail->send();

        $admin_mail = create_admin_mail_from_inquiry_form($inquiry_form);
        $admin_mail->send();
    }
} catch (PDOException $e) {
    $is_inserted = false;
    error_log($e->getMessage(), 0);
}

$_SESSION = [];
$location = $is_inserted ? 'register_success.php' : 'register_failed.php';
header('Location: ' . $location);
exit;

function create_user_mail_from_inquiry_form(InquiryForm $inquiry_form)
{
    $user_mail = new PHPMailer(true);

    $mail_config = parse_ini_file(CONFIG_DIR . DIR_SEP . 'mail.ini', true)['user'];
    $user_mail->isSMTP();
    $user_mail->Host = $mail_config['host'];
    $user_mail->SMTPAuth = true;
    $user_mail->Username = $mail_config['username'];
    $user_mail->Password = $mail_config['password'];
    $user_mail->SMTPSecure = $mail_config['smtp_secure'];
    $user_mail->Port = $mail_config['port'];
    $user_mail->CharSet = $mail_config['charset'];

    $template = $mail_config['template'];
    $inquiry_sent_time = new DateTime();
    $body = sprintf($template,
       $inquiry_sent_time->format('Y年m月d日 H時i分s秒'),
       InquiryForm::SUBJECT[$inquiry_form->getSubject()],
       $inquiry_form->getName(),
       $inquiry_form->getTelephoneNumber(),
       $inquiry_form->getEmail(),
       $inquiry_form->getInquiry()
    );

    $user_mail->setFrom($mail_config['from']);
    $user_mail->addAddress($inquiry_form->getEmail());
    $user_mail->addReplyTo($mail_config['reply_to']);
    $user_mail->Sender = $mail_config['return_path'];

    $user_mail->Subject =$mail_config['subject'];
    $user_mail->Body = $body;

    return $user_mail;
}

function create_admin_mail_from_inquiry_form(InquiryForm $inquiry_form)
{
    $admin_mail = new PHPMailer(true);

    $mail_config = parse_ini_file(CONFIG_DIR . DIR_SEP . 'mail.ini', true)['admin'];
    $admin_mail->isSMTP();
    $admin_mail->Host = $mail_config['host'];
    $admin_mail->SMTPAuth = true;
    $admin_mail->Username = $mail_config['username'];
    $admin_mail->Password = $mail_config['password'];
    $admin_mail->SMTPSecure = $mail_config['smtp_secure'];
    $admin_mail->Port = $mail_config['port'];
    $admin_mail->CharSet = $mail_config['charset'];

    $template = $mail_config['template'];
    $inquiry_sent_time = new DateTime();
    $body = sprintf($template,
       $inquiry_sent_time->format('Y年m月d日 H時i分s秒'),
       InquiryForm::SUBJECT[$inquiry_form->getSubject()],
       $inquiry_form->getName(),
       $inquiry_form->getTelephoneNumber(),
       $inquiry_form->getEmail(),
       $inquiry_form->getInquiry()
    );

    $admin_mail->setFrom($mail_config['from']);
    $admin_mail->addAddress($mail_config['admin_email']);
    $admin_mail->Sender = $mail_config['return_path'];

    $admin_mail->Subject =$mail_config['subject'];
    $admin_mail->Body = $body;

    return $admin_mail;
}
