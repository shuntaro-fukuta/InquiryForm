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

// TODO: 全角スペース trim
function mb_trim(string $string) {
    return '';
}
