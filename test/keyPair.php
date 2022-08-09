<?php

use crichain\utils\P256EC;

require __DIR__ . '/../vendor/autoload.php';

//生成秘钥对、地址
$ec = new P256EC();
$keyPair = $ec->genKeyPair();
$privateKey = $ec->genPrivateKey($keyPair);
$publicKey = $ec->genPublicKey($keyPair);
$address = $ec->genAddress($keyPair);

echo 'Private Key: ', $privateKey . "\n";
echo 'Public Key: ', $publicKey . "\n";
echo 'address: ', $address . "\n\n";


//通过私钥获得keyPair
$keyPair = $ec->fromPrivateKey($privateKey);
$parsePublicKey = $ec->genPublicKey($keyPair);
$parseAddress = $ec->genAddress($keyPair);

echo '解析后的 Public Key: ' , $parsePublicKey . "\n";
echo '解析后的 address: ', $parseAddress . "\n\n";

//生成签名
$msg = "123123";
$signature = $ec->genSign($keyPair, $msg, $parsePublicKey, $parseAddress);
echo 'signature: ', $signature . "\n";
