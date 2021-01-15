<?php

const DIR_SEP = DIRECTORY_SEPARATOR;

const APP_ROOT = DIR_SEP . 'var' . DIR_SEP . 'www' . DIR_SEP . 'html';

const PUBLIC_DIR = APP_ROOT . DIR_SEP . 'src' . DIR_SEP . 'public';
const CONFIG_DIR = APP_ROOT . DIR_SEP . 'config';
const LIB_DIR = APP_ROOT . DIR_SEP . 'src' . DIR_SEP . 'lib';
const CLASSES_DIR = APP_ROOT . DIR_SEP . 'src' . DIR_SEP . 'app' . DIR_SEP . 'classes';
const VIEW_DIR = PUBLIC_DIR . DIR_SEP . 'views';
const HTML_DIR = PUBLIC_DIR . DIR_SEP . 'html';

const LOG_DIR = APP_ROOT . DIR_SEP . 'log';
ini_set('error_log', LOG_DIR . DIR_SEP . 'error.log');
