<?php
namespace Dozen\OpenAi;

class OpenAi {
    /**
     * @var string YOUR_API_KEY https://platform.openai.com/account/api-keys
     */
    protected $apikey;
    /**
     * WordPress 响应
     */
    protected $wpResponse;

    public function __construct($apikey){
        $this->apikey = $apikey;
    }

    /**
     * 请求接口
     * @param string $url 接口地址
     * @param array $body 接口参数
     * @return array 接口返回数据
     */
    protected function request($url, $body) {
        $args = [
			'body' => wp_json_encode($body),
			'headers' => [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->apikey
			],
			'timeout' => 60
		];
        $this->wpResponse = wp_remote_post($url, $args);
        if (is_wp_error($this->wpResponse)) {
            throw new \Exception($this->wpResponse->get_error_message());
        } else if ($this->wpResponse['response']['code'] != 200) {
            throw new \Exception(sprintf("[%s] %s", $this->wpResponse['response']['code'], $this->wpResponse['response']['message']));
        }
        $resp_body = wp_remote_retrieve_body($this->wpResponse);
		$resp_json = json_decode($resp_body, true);
        return $resp_json;
    }

    public function getWPResponse() {
        return $this->wpResponse;
    }
}