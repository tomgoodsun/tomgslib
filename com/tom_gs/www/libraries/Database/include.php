<?php
require_once(__DIR__ . DS . 'Driver' . DS . 'include.php');
require_once(__DIR__ . DS . 'Exception' . DS . 'include.php');
require_once(__DIR__ . DS . 'Result' . DS . 'include.php');
require_once(__DIR__ . DS . 'Table' . DS . 'include.php');
require_once(__DIR__ . DS . 'Utility' . DS . 'include.php');

\com\tom_gs\www\libraries\Loader\AutoLoader::getInstance()->register(
    '\com\tom_gs\www\libraries\Database'
);
