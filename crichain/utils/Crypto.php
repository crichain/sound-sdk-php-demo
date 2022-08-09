<?php

namespace crichain\utils;

use BN\BN;
use Exception;

/**
 * User: matzoh
 * Date: 2022/8/1
 * Time: 14:45
 */

class Crypto {

    /**
     * @throws Exception
     */
    public static function convertPrivateKey ($bn)
    {
        return (new BN($bn->toArray("le"), 16))->toString("hex");
    }

    /**
     * @throws Exception
     */
    public static function convertPublicKey ($publicBn)
    {
        // x and y reverse and merge
        $arrX = $publicBn->getX()->toArray("le");
        $arrY = $publicBn->getY()->toArray("le");
        // to hex

        return (new BN(array_merge($arrX, $arrY), 16))->toString("hex");
    }

    public static function genAddress ($publicBn)
    {
        // x and y reverse and merge
        $arrX = $publicBn->getX()->toArray("le");
        $arrY = $publicBn->getY()->toArray("le");

        $public = array_merge($arrX, $arrY);

        return substr(hash('sha256', Bytes::tostr($public)), 0, 40);
    }
}

//function convertPrivateKey ($bn) {
////    $bn->toArray("le");
//    return (new BN($bn->toArray("le"), 16))->toString("hex");
//}
