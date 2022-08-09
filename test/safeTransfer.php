<?php

require __DIR__ . '/../vendor/autoload.php';

/**
 * 转账
 */

//转出私钥
$privateLey = "";
//转入地址
$to = '';

$caller = new \crichain\Caller($privateLey);
$res = $caller->safeTransfer($to, '0.2');
var_dump($res); die;