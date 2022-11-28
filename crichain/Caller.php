<?php

namespace crichain;

use crichain\config\config;
use crichain\utils\Functions;
use crichain\utils\HttpClient;
use crichain\utils\P256EC;
use Ethereum\Abi;
use Exception;
use Template\TransactionBody;
use Template\TransactionInfo;
use Web3\Contracts\Ethabi;
use Web3\Contracts\Types;
use Web3\Utils;

class Caller
{
    /**
     * @var Abi
     */
    private $abi;

    /**
     * @var Ethabi
     */
    private $ethabi;

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
     * @param string $nftType NFT配置，默认为NFT_A
     * @throws Exception
     */
    public function __construct(string $privateKey, string $nftType = 'NFT_A')
    {
        if (!$privateKey) {
            throw new Exception('私钥不能为空');
        }
        $this->privateKey = $privateKey;

        //abi实例
        $fileName = __DIR__ . '/config/' . $nftType . '.json';
        $contractMeta = json_decode(file_get_contents($fileName));
        $this->abi = new Abi($contractMeta->abi);

        $this->ethabi = new Ethabi([
            'address' => new Types\Address,
            'bool' => new Types\Boolean,
            'bytes' => new Types\Bytes,
            'dynamicBytes' => new Types\DynamicBytes,
            'int' => new Types\Integer,
            'string' => new Types\Str,
            'uint' => new Types\Uinteger,
        ]);

        $this->privateKey = $privateKey;
    }

    /**
     * 调用合约
     *
     * @param string $contractAddress 合约地址
     * @param string $method 合约方法名, 详情见config/NFT_A.json
     * @param array $params 合约参数数组 ['xxxxx','xxxx','xxxxx'], 详情见config/NFT_A.json, 参数顺序必须与配置中的一致
     * @param string $operateId 操作ID
     * @return array|mixed
     * @throws Exception
     */
    public function callContract(string $contractAddress, string $method, array $params, string $operateId = "")
    {
        return $this->_callContract($contractAddress, $method, $params, $operateId);
    }

    /**
     * 调用合约
     * 手动，可传入指定nonce
     *
     * @param string $contractAddress 合约地址
     * @param string $method 合约方法名, 详情见config/NFT_A.json
     * @param array $params 合约参数数组 ['xxxxx','xxxx','xxxxx'], 详情见config/NFT_A.json, 参数顺序必须与配置中的一致
     * @param string $operateId 操作ID
     * @param int $nonce
     * @return array|mixed
     * @throws Exception
     */
    public function callContractManual(string $contractAddress, string $method, array $params, string $operateId = "", int $nonce = 0)
    {
        return $this->_callContract($contractAddress, $method, $params, $operateId, $nonce);
    }


    /**
     * @throws Exception
     */
    private function _callContract(string $contractAddress, string $method, array $params, string $operateId = "", int $nonce = 0)
    {
        if (!$contractAddress || !$method) {
            throw new Exception('参数错误');
        }

        //获取合约签名
        $methodSignature = $this->getMethodSignature($method, $params);

        //合约方法类型
        $methodType = $this->abi->getParamDefinition($method)->stateMutability;

        $callParams = [
            'operateId' => $operateId,
            'contractAddress' => $contractAddress,
            'contractCode' => 'NFT_A',
            'functionType' => $methodType,
            'method' => $method,
        ];

        //获取txData
        if ($methodType == 'tx') {
            $txData = $this->getTxDataManual($methodSignature, $contractAddress, $nonce);
            $callParams['data'] = $txData;
        }
        ($methodType == 'view') && $callParams['params'] = $params;
        //var_dump($callParams); die;

        //请求
        return HttpClient::call(config::getCurlConf('callcontract'), true, $callParams, [], true, 10);
    }

    /**
     * 通过私钥获取地址
     * @param $privateKey
     * @return false|string
     */
    static public function getAddressByPrivateKey($privateKey)
    {
        $ec = new P256EC();
        $keyPair = $ec->fromPrivateKey($privateKey);
        return $ec->genAddress($keyPair);
    }

    /**
     * 获取账号信息
     *
     * @param string $address 地址
     * @return array
     * @throws Exception
     */
    static public function getAccountInfo(string $address): array
    {
        if (!$address) {
            throw new Exception('参数错误');
        }
        return HttpClient::call(config::getCurlConf('account'), false, ["address" => $address], [], true);
    }

    /**
     * 获取nonce
     *
     * @throws Exception
     */
    static public function getNonce(string $address): int
    {
        $accountInfo = self::getAccountInfo($address);
        if ($accountInfo['success'] === true && is_array($accountInfo['data']) && isset($accountInfo['data']['nonce'])) {
            return $accountInfo['data']['nonce'];
        }
        throw new Exception("get nonce failed");
    }

    /**
     * 获取交易详情
     *
     * @param string $hash 交易哈希
     * @return array
     * @throws Exception
     */
    static public function transactionInfo(string $hash): array
    {
        if (!$hash) {
            throw new Exception('参数错误');
        }
        return HttpClient::call(config::getCurlConf('transactionInfo'), false, ["hash" => $hash], [], true);
    }

    /**
     * 获取合约方法签名
     *
     * @param $methodName
     * @param $params
     * @return string
     * @throws Exception
     */
    private function getMethodSignature($methodName, $params): string
    {
        $inputs = $this->abi->getParamDefinition($methodName)->inputs;
        $types = array_column($inputs, 'type');
        $encodeParameters = $this->ethabi->encodeParameters($types, $params);
        $encodeParameters = Utils::stripZero($encodeParameters);

        $functionName = $methodName . "(" . implode(',', $types) . ")";
        $methodId = $this->ethabi->encodeFunctionSignature($functionName);
        return $methodId . $encodeParameters;
    }

    /**
     * 生成txData
     *
     * @param $methodSignature
     * @param $contractAddress
     * @return string
     * @throws Exception
     */
    private function getTxData($methodSignature, $contractAddress): string
    {
        return $this->buildTxData($methodSignature, $contractAddress);
    }

    /**
     * 生成txData
     * 手动，可传入nonce
     *
     * @param $methodSignature
     * @param $contractAddress
     * @param $nonce
     * @return string
     * @throws Exception
     */
    private function getTxDataManual($methodSignature, $contractAddress, $nonce) {
        return $this->buildTxData($methodSignature, $contractAddress, $nonce);
    }

    /**
     * 构建交易体
     * User: <eaterlow@gmail.com>
     * Date: 2022/11/28
     * Time: 14:21
     * @param $methodSignature
     * @param $contractAddress
     * @param $nonce
     * @return string
     * @throws Exception
     */
    private function buildTxData($methodSignature, $contractAddress, $nonce): string
    {
        $txBody = new TransactionBody();
        $address = self::getAddressByPrivateKey($this->privateKey);
        $txBody->setAddress(Utils::hexToBin($address));
        $txBody->setNonce(($nonce ?: self::getNonce($address)));
        $txBody->setChainId(config::CHAIN_ID);
        $txBody->setVersion(Utils::hexToBin(Utils::toHex(config::VERSION)));
        $txBody->setTimestamp(Functions::mtime());
        $txBody->setCodeData(Utils::hexToBin($methodSignature));
        $txBody->setRecipient(Utils::hexToBin($contractAddress));

        //ecDataSign
        $ecData = $txBody->serializeToString();
        $ecDataSign = Creator::sign($this->privateKey, $ecData);

        //txInfo
        $txInfo = new TransactionInfo();
        $txInfo->setBody($txBody);
        $txInfo->setSignature(Utils::hexToBin($ecDataSign));

        //txData
        return Utils::toHex($txInfo->serializeToString());
    }
}
