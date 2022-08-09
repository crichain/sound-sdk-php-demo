#sound/sound-sdk-php-demo

# Install
```
composer require sound/sound-sdk-php-demo dev-master
```
# Usage

### 生成秘钥对、地址
```php
use crichain\Creator;

$keyPair = Creator::keyPair();
```

### 签名
```php
use crichain\Creator;

/**
 * 签名
 * 
 * @param string $privateKey 私钥
 * @param string $msg 签名文本
 * @return string
 * @throws Exception
 */
$sign = Creator::sign($keyPair['privateKey'], '123');
```
### 获取账户信息
```php
use crichain\Caller;

/**
 * 获取账户信息
 * 
 * @param string $address 地址
 * @throws Exception
 */
$accountInfo = Caller::getAccountInfo($keyPair['address']);
```

### 转账CRIC
```php
use crichain\Caller;

/**
 * 实例化Caller类
 * 
 * @param string $privateKey 私钥
 * @param string $nftType NFT配置，默认为NFT_A
 */
$caller = new Caller($keyPair['privateKey']);

/**
 * 转账
 * 
 * @param string $to 转入地址
 * @param string $amount 转账金额
 * @throws Exception
 */
$res = $caller->safeTransfer('转入地址', '0.01');
```

### 调用合约
```php
use crichain\Caller;
use crichain\utils\Functions;

/**
 * 实例化调用合约类
 * 
 * @param string $privateKey 私钥
 * @param string $nftType NFT配置，默认为NFT_A
 */
$caller = new Caller($privateKey);

/**
 * 铸造
 *
 * @param string $contractAddress   合约地址
 * @param string $method  合约方法名:safeMint
 * @param array $params  合约参数数组:['转入地址','tokenId','token图片地址']
 * @param string $operateId  操作ID
 * @return array|mixed
 * @throws Exception
 */
$r = $caller->callContract($contractAddress, 'safeMint',['xxxx','123','https://gfanx.cn/1.jpg'], Functions::createOperateId());
var_dump($r); die;

/**
 * 转移token
 *
 * @param string $contractAddress   合约地址
 * @param string $method  合约方法名:safeTransfer
 * @param array $params  合约参数数组:['转出地址','转入地址','tokenId']
 * @param string $operateId  操作ID
 * @return array|mixed
 * @throws Exception
 */
$r = $caller->callContract($contractAddress, 'safeTransfer',['xxx','xxx','123'],  Functions::createOperateId());
var_dump($r); die;

/**
 * 销毁
 *
 * @param string $contractAddress   合约地址
 * @param string $method  合约方法名:burn
 * @param array $params  合约参数数组:['tokenId']
 * @param string $operateId 操作ID
 * @return array|mixed
 * @throws Exception
 */
$r = $caller->callContract($contractAddress, 'burn',['123'], Functions::createOperateId());
var_dump($r); die;

/**
 * 获取tokenURI
 *
 * @param string $contractAddress   合约地址
 * @param string $method  合约方法名:tokenURI
 * @param array $params  合约参数数组:['tokenId']
 * @return array|mixed
 * @throws Exception
 */
$r = $caller->callContract($contractAddress, 'tokenURI',['123']);
var_dump($r); die;

#注：其他合约方法，见config/NFT_A.json配置文件，调用方法同上。

```

### 获取交易详情
```php
/**
 * 获取交易详情
 *
 * @param string $hash 交易哈希
 * @return array
 * @throws Exception
 */
$r = crichain\Caller::transactionInfo('xxx');
var_dump($r);
```

### 注意事项
```
1.sdk区分测试与正式环境，如需要使用测试环境，请增加环境变量：CRICHAIN_SDK_ENV=test
```