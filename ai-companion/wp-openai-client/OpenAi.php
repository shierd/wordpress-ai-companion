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
    /**
     * @var bool Whether to use stream mode.
     */
    protected $stream;
    /**
     * @var callable stream callback function
     */
    protected $streamCallback;
    /**
     * @var string stream message
     */
    protected $streamMessage = '';
    /**
     * @var string stream content
     */
    protected $streamContent = '';
    /**
     * @var array stream event array
     */
    protected $streamEvent = [];
    /**
     * @var string stream line buffer
     */
    protected $streamLineBuffer = '';

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

    /**
     * Making API requests using stream mode.
     * @param string $uri api path
     * @param array $body api param
     */
    protected function stream($uri, $body) {
        $url = $this->api . $uri;
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apikey
        ];
        $data = wp_json_encode($body);
        $type = 'POST';
        $options = [];
        $options['transport'] = 'Requests_Transport_fsockopen';
        $hooks = new \WP_HTTP_Requests_Hooks( $url, $body );
        $hooks->register('request.progress', array($this, 'streamProgress'));
        $options['hooks'] = $hooks;
        $options['timeout'] = 20;

        $requests_response = \Requests::request( $url, $headers, $data, $type, $options );

        // Convert the response into an array.
        $http_response = new \WP_HTTP_Requests_Response( $requests_response );
        $response      = $http_response->to_array();

        // Add the original object to the array.
        $response['http_response'] = $http_response;

        $this->wpResponse = $response;

		$resp_json = $this->streamEvent[0] ?? null;
        $resp_json['choices'][0]['delta']['content'] = $this->streamMessage;
        return $resp_json;
    }

    /**
     * Processing stream progress.
     * @param string $block
     * @param int $size
     * @param int $max_bytes
     */
    public function streamProgress($block, $size, $max_bytes) {
        $this->streamContent .= $block;
        // push stream event to $streamEvent
        $events = $this->retrieveStreamEvent($this->streamContent);
        foreach ($events as $event) {
            $text = $event['choices'][0]['delta']['content'] ?? '';
            $this->streamMessage .= $text;
            array_push($this->streamEvent, $event);
            $this->streamLineBuffer .= $text;
            $lp = mb_strpos($this->streamLineBuffer, "\n");
            if (strpos($this->streamLineBuffer, '`') === 0 && $lp === false) {
                continue;
            } else if (strpos($this->streamLineBuffer, '`') === 0 && $lp !== false) {
                $line = mb_substr($this->streamLineBuffer, 0, $lp+1);
                $event['choices'][0]['delta']['content'] = $line;
            }
            while($lp !== false) {
                $this->streamLineBuffer = mb_substr($this->streamLineBuffer, $lp+1);
                $lp = mb_strpos($this->streamLineBuffer, "\n");
            }
            call_user_func($this->streamCallback, $event);
        }
    }

    /**
     * retrieve event from stream content
     * @param string &$content stream content
     */
    private function retrieveStreamEvent(&$content) {
        $content = preg_replace_callback('/\s?\n[a-z0-9]{3,4}\s?\n/', function ($matches) {
            return '';
        }, $content);
        $lines = explode("\n", $content);
        $events = [];
        foreach ($lines as $line) {
            if (!empty($line)) {
                $l = trim($line);
                if (strpos($l, 'data:') === 0) {
                    $l = trim(substr($l, 5));
                }
                if ($l == '[DONE]') {
                    $event = ['done' => 1];
                } else {
                    $event = json_decode($l, true);
                }
                if (is_array($event)) {
                    $events[] = $event;
                }
            }
            if (strpos($content, $line."\n") === 0) {
                $len = strlen($line)+1;
                $content = substr($content, $len);
            }
        }
        return $events;
    }

    public function getWPResponse() {
        return $this->wpResponse;
    }

    /**
     * stream setting
     * @param callable $callback stream callback function
     */
    public function setStream($callback) {
        $this->stream = true;
        $this->streamCallback = $callback;
    }
}