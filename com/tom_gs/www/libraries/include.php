<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'define.php');
require_once(LIBRARY_ROOT . DS . 'Loader' . DS . 'include.php');

use \com\tom_gs\www\libraries\Loader\AutoLoader;

AutoLoader::getInstance(PACKAGE_ROOT)->load(
    LIBRARY_ROOT . DS,
    array(
        'Exception',
        'Cache',
        'Data',
        'Database',
        'Debug',
        'Language',
    //    'Localer',
    //    'Log',
        'Session',
        'Utility',
    )
);
