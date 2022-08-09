<?php

namespace crichain\utils;

use Exception;

class HttpClient
{

    /**
     * curl发送http请求
     * @param string $url 请求的url
     * @param bool $isPost 是否为post请求
     * @param array $data 请求参数
     * @param array $header 请求头 说明：应这样格式设置请求头才生效 ['Authorization:0f5fc4730e21048eae936e2eb99de548']
     * @param bool $isJson 是否为json请求，默认为Content-Type:application/x-www-form-urlencoded
     * @param int $timeOut 超时时间 单位秒，0则永不超时
     * @return mixed
     * @throws Exception
     */
    static public function call(string $url, bool $isPost = true, array $data = [], array $header = [], bool $isJson = false, int $timeOut = 0): array
    {
        if (empty($url)) {
            throw new Exception("url is required.");
        }

        //初始化curl
        $curl = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => '',
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => $timeOut,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $header
        ];
        //post和get特殊处理
        if ($isPost) {
            // 设置POST请求
            $options[CURLOPT_POST] = true;

            if ($isJson && $data) {
                //json处理
                $data = json_encode($data);
                $header = array_merge($header, ['Content-Type: application/json']);
                //设置头信息
                $options[CURLOPT_HTTPHEADER] = $header;

                //如果是json字符串的方式，不能用http_build_query函数
                $options[CURLOPT_POSTFIELDS] = $data;
            } else {
                //x-www-form-urlencoded处理
                //如果是数组的方式,要加http_build_query，不加的话，遇到二维数组会报错。
                $options[CURLOPT_POSTFIELDS] = http_build_query($data);
            }
        } else {
            // GET
            $options[CURLOPT_CUSTOMREQUEST] = 'GET';
            if (strpos($url, '?') === false && !empty($data) && is_array($data)) {
                $params_arr = [];
                foreach ($data as $k => $v) {
                    $params_arr[] = $k . '=' . $v;
                }
                $params_string = implode('&', $params_arr);
                $options[CURLOPT_URL] = $url . '?' . $params_string;
            }
        }
        //header
        $options[CURLOPT_HTTPHEADER][] = 'x-request-id:' . Functions::create_uuid();
        curl_setopt_array($curl, $options);
        // 执行请求
        $response = curl_exec($curl);
        $logger = Logger::getLogger();
        $logger->info('curl', [
            'url' => $url,
            'header' => $options[CURLOPT_HTTPHEADER],
            'params' => is_array($data) ? $data : json_decode($data, true),
            'rep' => json_decode($response, true),
        ]);
        $err = curl_error($curl);
        if ($err) {
            $err = curl_error($curl);
            curl_close($curl);
            throw new Exception("curl err: " . $err);
        }
        //关闭请求
        curl_close($curl);
        $result = json_decode($response, true);
        if (!is_array($result)) {
            throw new Exception("err: ". $response);
        }
        return $result;
    }
}
