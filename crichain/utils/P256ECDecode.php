<?php
/**
 * User: matzoh
 * Date: 2022/8/1
 * Time: 17:51
 */

namespace crichain\utils;

use Elliptic\EC;

class P256ECDecode
{
    /**
     * @var EC
     */
    protected $_ec;

    public function __construct ()
    {
        // select p256
        $this->_ec = new EC('p256');
    }

    public function fromPrivateKey ($privateKey)
    {
        $keyPair = $this->_ec->keyFromPrivate($privateKey);
        $priKey = $this->getPrivateKey($keyPair);

        return $this->_ec->keyFromPrivate($priKey);
    }

    public function getPrivateKey ($keyPair)
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
}
