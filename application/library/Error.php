<?php
/**
 * [错误处理函数]
 * @param  [type] $errno   [description]
 * @param  [type] $errstr  [description]
 * @param  [type] $errfile [description]
 * @param  [type] $errline [description]
 * @param  [type] $context [description]
 * @return [type]          [description]
 */
function errorHandler($exception)
{
    (new Raven())->LogException($exception);
    return true;
}
