<?php
namespace Dozen\OpenAi;

class OpenAi {
    /**
     * @var string the api domain
     */
    protected $api = 'https://api.openai.com';
    /**
     * @var string YOUR_API_KEY https://platform.openai.com/account/api-keys
     */
    protected $apikey;
    /**
     * WordPress response
     */
    protected $wpResponse;

    public function __construct($apikey, $api=null){
        $this->apikey = $apikey;
        if ($api) {
            $this->api = $api;
        }
    }

    /**
     * request api
     * @param string $uri api path
     * @param array $body api param
     * @return array
     */
    protected function request($uri, $body) {
        $url = $this->api . $uri;
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
            // var_dump($this->wpResponse);
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