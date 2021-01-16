<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'config' . DIRECTORY_SEPARATOR . 'init.php');
require_once(CONFIG_DIR . DIRECTORY_SEPARATOR . 'database_config.php');
require_once(LIB_DIR . DIR_SEP . 'functions.php');
require_once(CLASSES_DIR . DIRECTORY_SEPARATOR . 'Forms' . DIRECTORY_SEPARATOR . 'InquiryForm.php');

require_once(LIB_DIR . DIR_SEP . 'PHPMailer' . DIR_SEP . 'src' . DIR_SEP . 'PHPMailer.php');
require_once(LIB_DIR . DIR_SEP . 'PHPMailer' . DIR_SEP . 'src' . DIR_SEP . 'Exception.php');
require_once(LIB_DIR . DIR_SEP . 'PHPMailer' . DIR_SEP . 'src' . DIR_SEP . 'SMTP.php');

session_start();
if (!isset($_SESSION['inquiry_form'])) {
    header('HTTP/1.0 400 Bad Request');
    include(HTML_DIR . DIR_SEP . 'error' . DIR_SEP . '400.html');
    exit;
}

$inquiry_form = $_SESSION['inquiry_form'];
$is_inserted = false;
try {
    if (!defined('DATABASE')) throw new LogicException('const DATABASE must be defined in database_config.php');
    if (!defined('DATABASE_HOST')) throw new LogicException('const DATABASE_HOST must be defined in database_config.php');
    if (!defined('DATABASE_NAME')) throw new LogicException('const DATABASE_NAME must be defined in database_config.php');
    if (!defined('DATABASE_USER')) throw new LogicException('const DATABASE_USER must be defined in database_config.php');
    if (!defined('DATABASE_ENCODING')) throw new LogicException('const DATABASE_ENCODING must be defined in database_config.php');

    $pdo = new PDO(
        DATABASE . ':dbname=' . DATABASE_NAME . ';host=' . DATABASE_HOST . ';charset=' . DATABASE_ENCODING,
        DATABASE_USER,
        DATABASE_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $pstmt = $pdo->prepare('
        INSERT INTO inquiries (
            subject,
            name,
            email,
            telephone_number,
            inquiry
        ) VALUES (
            :subject,
            :name,
            :email,
            :telephone_number,
            :inquiry
        );
    ');

    $is_inserted = $pstmt->execute([
        ':subject' => $inquiry_form->getSubject(),
        ':name' => $inquiry_form->getName(),
        ':email' => $inquiry_form->getEmail(),
        ':telephone_number' => $inquiry_form->getTelephoneNumber(),
        ':inquiry' => $inquiry_form->getInquiry(),
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
