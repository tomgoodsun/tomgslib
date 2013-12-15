<?php
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('LIBRARY_ROOT', __DIR__);
define('PACKAGE_ROOT', realpath(LIBRARY_ROOT . str_repeat(DS . '..', 4)));
