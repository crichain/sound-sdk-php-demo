<?php
require __DIR__ . '/../vendor/autoload.php';

use crichain\utils\Functions;
use crichain\Caller;

class testCase {

    //获取账户信息
    public function getAccountInfo($address) {
        $accountInfo = Caller::getAccountInfo($address);
        var_dump($accountInfo); die;
    }

    //交易信息
    public function transactionInfo($hash) {
        $transactionInfo = Caller::transactionInfo($hash);
        var_dump($transactionInfo); die;
    }

    //调用合约
    public function callContract($privateKey, $contractAddress, $method, $params) {
        $caller = new Caller($privateKey);
        $r = $caller->callContract($contractAddress, $method,$params,Functions::createOperateId());
        var_dump($r); die;
    }

    //调用合约，手动传入 Nonce
    public function callContractManual($privateKey, $contractAddress, $method, $params, $nonce) {
        $caller = new Caller($privateKey);
        $r = $caller->callContractManual($contractAddress, $method,$params,Functions::createOperateId(), $nonce);
        var_dump($r); die;
    }

}

//测试钱包私钥
$privateKey = '';
//测试钱包地址
$address = '';
//测试合约地址
$contractAddress = '0xce7e273ed4081e6309664734dc7a162e2e20e6cd';

$testCase = new testCase();
//$testCase->getAccountInfo('');
//$testCase->transactionInfo('0xa548710a29ba68a1a76987b4f7038fca75f0a7d8b5f8996411b8250d11ce4c00');
//铸造
//$testCase->callContract($privateKey, $contractAddress,'safeMint', [
//    '61d4c124df65ba081992ff2a8c77c67a8b3cb77c',
//    '2442125423124',
//    'https://gfanx.cn/1111.png'
//    ]
//);

//销毁
//$testCase->callContract($privateKey, $contractAddress,'burn', ['2442125423124']);

//转账
//$testCase->callContract($privateKey, $contractAddress,'safeTransfer', ['61d4c124df65ba081992ff2a8c77c67a8b3cb77c','1c3fc81fa28aee2dc09d02a963b95185d10ec758','2442125423124']);

//tokenURI
//$testCase->callContract($privateKey, $contractAddress,'tokenURI', ['2442125423124']);


//从某个地址转移token到指定地址
//$caller = new \crichain\Caller($privateKey);
//$r = $caller->callContract('', 'safeTransferFrom',['61d4c124df65ba081992ff2a8c77c67a8b3cb77c','1c3fc81fa28aee2dc09d02a963b95185d10ec758','2332125423124'],$operateId);
//var_dump($r); die;

//添加白名单
//$caller = new \crichain\Caller($privateKey);
//$address = '';
//$r = $caller->callContract($contractAddress, 'addWhiteList',[$address],Functions::createOperateId());
//var_dump($r); die;

//获取白名单
//$caller = new \crichain\Caller($privateKey);
//$r = $caller->callContract($contractAddress, 'getWhiteList',[11]);
//var_dump($r); die;


//获取当前token被授权的地址
//$caller = new \crichain\Caller($privateKey);
//$r = $caller->callContract($contractAddress, 'getApproved',[2432125423124]);
//var_dump($r); die;

//isApprovedForAll
//$caller = new \crichain\Caller($privateKey);
//$r = $caller->callContract($contractAddress, 'isApprovedForAll',['61d4c124df65ba081992ff2a8c77c67a8b3cb77c','1c3fc81fa28aee2dc09d02a963b95185d10ec758']);
//var_dump($r); die;



