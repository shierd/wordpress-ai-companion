<?php
namespace Dozen\OpenAi;

class Client {
    /**
     * @var string YOUR_API_KEY https://platform.openai.com/account/api-keys
     */
    protected $apikey;

    public function __construct($apikey){
        $this->apikey = $apikey;
    }

    /**
     * 请求 completions 接口，返回 Completions 对象
     * @param array 接口参数
     * @return Completions
     */
    public function completions($request_body) {
        $completions = new Completions($this->apikey);
        $completions->create($request_body);
        return $completions;
    }
}