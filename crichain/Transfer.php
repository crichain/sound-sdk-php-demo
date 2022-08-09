<?php

namespace crichain;

use crichain\config\config;
use crichain\utils\Functions;
use crichain\utils\HttpClient;
use Exception;
use Template\TransactionBody;
use Template\TransactionInfo;
use Web3\Utils;

class Transfer {

    /**
     * 私钥
     *
     * @var
     */
    protected $privateKey;

    /**
     * construct
     *
     * @param string $privateKey 私钥
     * @throws Exception
     */
    public function __construct(string $privateKey)
    {
        if (!$privateKey) {
            throw new Exception('私钥不能为空');
        }
        $this->privateKey = $privateKey;
    }

    /**
     * 转账
     *
     * @param string $to 转入地址
     * @param string $amount 转账金额
     * @return array|mixed
     * @throws Exception
     */
    public function safeTransfer(string $to, string $amount)
    {
        if (!$to) {
            throw new Exception('转入地址不能为空');
        }
        if ($amount < 0) {
            throw new Exception('转账金额必须大于0');
        }

        //转出地址
        $from = Caller::getAddressByPrivateKey($this->privateKey);

        $txBody = new TransactionBody();
        $nonce = Caller::getNonce($from);
        $txBody->setNonce($nonce);
        $txBody->setAddress(Utils::hexToBin($from));
        $txBody->setRecipient(Utils::hexToBin($to));
        $txBody->setChainId(config::CHAIN_ID);
        $txBody->setVersion(Utils::hexToBin(Utils::toHex(config::VERSION)));
        $txBody->setTimestamp(Functions::mtime());
        $txBody->setAmount(Functions::getTransferAmountByFloatNum($amount));

        //ecDataSign
        $ecData = $txBody->serializeToString();
        $ecDataSign = Creator::sign($this->privateKey, $ecData);

        //txInfo
        $txInfo = new TransactionInfo();
        $txInfo->setBody($txBody);
        $txInfo->setSignature(Utils::hexToBin($ecDataSign));

        //txData
        $txData = Utils::toHex($txInfo->serializeToString());

        //发起请求
        return HttpClient::call(config::getCurlConf('transferCric'), true, ["txData" => $txData], [], true, 10);
    }

}
