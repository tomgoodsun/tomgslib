<?php
function dump($expression)
{
    $debug_info = debug_backtrace();
    com\tom_gs\www\libraries\Debug\ValueDumper::getInstance()->write(
        $expression,
        true,
        isset($debug_info[0]) ? $debug_info[0] : array()
    );
}

function setdump($expression)
{
    $debug_info = debug_backtrace();
    com\tom_gs\www\libraries\Debug\BatchValueDumper::getInstance()->addDumper(
        $expression,
        isset($debug_info[0]) ? $debug_info[0] : array()
    );
}

function dumpall()
{
    com\tom_gs\www\libraries\Debug\BatchValueDumper::getInstance()->write(
        null,
        true
    );
}
