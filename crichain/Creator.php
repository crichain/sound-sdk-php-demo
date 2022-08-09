<?php

namespace crichain;

use crichain\utils\P256EC;
use Exception;
use Web3\Utils;

class Creator
{

    /**
     * 生成秘钥对
     *
     * @return array
     */
    static public function keyPair(): array
    {
        $ec = new P256EC();
        $keyPair = $ec->genKeyPair();
        $privateKey = $ec->genPrivateKey($keyPair);
        $publicKey = $ec->genPublicKey($keyPair);
        $address = $ec->genAddress($keyPair);
        return [
            'privateKey' => $privateKey,
            'publicKey' => $publicKey,
            'address' => $address,
        ];
    }

    /**
     * 签名
     *
     * @param string $privateKey 私钥
     * @param string $msg 签名文本
     * @return string
     * @throws Exception
     */
    static public function sign(string $privateKey, string $msg): string
    {
        if (!Utils::isHex($msg)) {
            $msg = Utils::toHex($msg);
        }
        $ec = new P256EC();
        $keyPair = $ec->fromPrivateKey($privateKey);
        $parsePublicKey = $ec->genPublicKey($keyPair);
        $parseAddress = $ec->genAddress($keyPair);
        return $ec->genSign($keyPair, $msg, $parsePublicKey, $parseAddress);
    }

}
