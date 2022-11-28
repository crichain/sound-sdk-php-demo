<?php

require __DIR__ . '/../vendor/autoload.php';

/**
 * 转账
 */

//转出私钥
$privateLey = "";
//转入地址
$to = '';

$transfer = new \crichain\Transfer($privateLey);
$res = $transfer->safeTransfer($to, '0.2');
//$res = $transfer->safeTransfer($to, '0.2', 10); // 手动传入 nonce
var_dump($res); die;

