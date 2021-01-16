<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'config' . DIRECTORY_SEPARATOR . 'init.php');
require_once(CONFIG_DIR . DIRECTORY_SEPARATOR . 'database_config.php');
require_once(LIB_DIR . DIR_SEP . 'functions.php');
require_once(CLASSES_DIR . DIRECTORY_SEPARATOR . 'Forms' . DIRECTORY_SEPARATOR . 'InquiryForm.php');

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
} catch (PDOException $e) {
    $is_inserted = false;
    error_log($e->getMessage(), 0);
}

$_SESSION = [];
$location = $is_inserted ? 'register_success.php' : 'register_failed.php';
header('Location: ' . $location);
exit;
