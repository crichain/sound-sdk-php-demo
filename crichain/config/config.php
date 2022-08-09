<?php

namespace crichain\config;

class config
{
    //合约节点ID
    const CHAIN_ID = 168;
    //合约版本
    const VERSION = 'V.2022';

    //合约接口域名
    const DOMAIN_TEST = 'http://test.open-api.crichain.cn'; //测试环境
    const DOMAIN = 'http://openapi.crichain.cn';  //正式环境

    //合约接口地址
    const CURL_CONF = [
        'transferCric' => '/chain/transferCric.json',
        'account' => '/chain/account.json',
        'transactionInfo' => '/chain/transaction.json',
        'callcontract' => '/chain/callcontract.json',
    ];

    /**
     * 获取curl
     *
     * @param $type
     * @return string
     */
    static public function getCurlConf($type) {
        $env = getenv('CRICHAIN_SDK_ENV');
        $domain = $env == 'test' ? config::DOMAIN_TEST : config::DOMAIN;
        return $domain . config::CURL_CONF[$type];
    }
}
