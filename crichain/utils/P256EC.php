<?php

namespace crichain\utils;

require __DIR__ . '/../../vendor/autoload.php';

use BN\BN;
use crichain\utils\Crypto;
use Elliptic\EC;
use Exception;

class P256EC {

    /**
     * @var EC
     */
    protected $_ec;

    public function __construct ()
    {
        // select p256
        $this->_ec = new EC('p256');
    }

    /**
     * 生成 key pair
     * 
     * User: <eaterlow@gmail.com>
     * Date: 2022/8/1
     * Time: 17:24
     * @return EC\KeyPair
     */
    public function genKeyPair ()
    {
        return $this->_ec->genKeyPair();
    }

    public function fromPrivateKey ($privateKey)
    {
        $keyPair = $this->_ec->keyFromPrivate($privateKey);
        $priKey = $this->genPrivateKey($keyPair);

        return $this->_ec->keyFromPrivate($priKey);
    }

    public function genPrivateKey ($keyPair)
    {
        return Crypto::convertPrivateKey($keyPair->getPrivate());
    }

    public function genPublicKey ($keyPair)
    {
        return Crypto::convertPublicKey($keyPair->getPublic());
    }

    public function genAddress ($keyPair)
    {
        return Crypto::genAddress($keyPair->getPublic());
    }

    /**
     * @param $keyPair
     * @param $msg string 必须为16进制的字符串
     * @param $publicKey
     * @param $address
     * @return string
     * @throws Exception
     */
    public function genSign ($keyPair, $msg, $publicKey, $address)
    {
        // $newBn = (new BN(Bytes::getbytes($msg), 16))->toArray(16);
        $newBn = (new BN($msg, 16))->toArray(16);

        $msg = hash('sha256', Bytes::tostr($newBn));
        $sign = $keyPair->sign($msg);

        $arrS = $sign->s->toArray("le");
        $arrR = $sign->r->toArray("le");

        $sr = (new BN(array_merge($arrR, $arrS), 16))->toString("hex");

        return $publicKey . $address . $sr;
    }
}
