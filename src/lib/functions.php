<?php

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function get_element_from_post_parameters(string $elementName) {
    if (!isset($_POST[$elementName]) || $_POST[$elementName] === '') {
        return null;
    }

    return trim($_POST[$elementName]);
}

function mb_trim(string $string) {
    return preg_replace('/\A[\p{Cc}\p{Cf}\p{Z}]++|[\p{Cc}\p{Cf}\p{Z}]++\z/u', '', $string);
}

function is_empty($var) {
    return ($var === null || $var === '' || $var === []);
}

function error400()
{
    header('HTTP/1.0 400 Page Expired');
    include(HTML_DIR . DIR_SEP . 'error' . DIR_SEP . '400.html');
    exit;
}

function error403()
{
    header('HTTP/1.0 403 Forbidden');
    include(HTML_DIR . DIR_SEP . 'error' . DIR_SEP . '403.html');
    exit;
}

function dump($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}
