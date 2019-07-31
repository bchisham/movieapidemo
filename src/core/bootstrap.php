<?php

const APP_DIR_ROOT = '/var/www/moviedb';
const APP_DIR_INI = '/var/www/moviedb/ini';
const APP_DIR_CORE = '/var/www/moviedb/core';

require_once APP_DIR_CORE . DIRECTORY_SEPARATOR . 'autoloader.php';


autoloader::quickPath(realpath(__DIR__));

config::get();