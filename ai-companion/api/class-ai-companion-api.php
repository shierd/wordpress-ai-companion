<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.shierd.com
 * @since      1.0.0
 *
 * @package    Ai_Companion
 * @subpackage Ai_Companion/public
 */

use Dozen\OpenAi\Client;
use Dozen\OpenAi\Context;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ai_Companion
 * @subpackage Ai_Companion/public
 * @author     Your Name <email@example.com>
 */
class Ai_Companion_Api {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $spe = 0;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function registerRoutes() {
		register_rest_route('ai_companion', '/answer', [
			[
				'methods' => 'POST',
				'callback' => [$this, 'answer']
			],
			[
				'methods' => 'GET',
				'callback' => [$this, 'answer']
			]
		]);
		register_rest_route('ai_companion', '/messages', [
			'methods' => 'GET',
			'callback' => [$this, 'messages']
		]);
		register_rest_route('ai_companion', '/clean', [
			'methods' => 'POST',
			'callback' => [$this, 'clean']
		]);
	}

	private function response($code, $msg, $data) {
		$result = [
			'code' => $code,
			'data' => $data,
			'message' => $msg
		];
		return new WP_REST_Response($result);
	}

	private function success($data=[]) {
		return $this->response(600, 'success', $data);
	}

	private function error($msg='error', $data=[]) {
		return new WP_Error( 601, __( $msg, 'text-domain' ), $data );
	}

	public function stream($event) {
		$msg = $this->handleText($event['choices'][0]['delta']['content'] ?? '');
		$done = $event['done'] ?? 0;
		$data = json_encode(['done' => $done, 'text' => $msg]);
		echo "event: msg\n";
		echo "data: {$data}\n";
		echo "\n";
        ob_flush();
        flush();
	}

	public function answer($request) {
		$params = $request->get_params();
		$message = $params['message'];
		if (empty($message)) {
			return $this->error('Param message is empty');
		}

		$option = get_option(Ai_Companion_OPTION_KEY);
		$apikey = $option['openai_api_key'] ?? '';
		if (empty($apikey)) {
			return $this->error('API_KEY not set');
		}
		$model = $option['model'] ?? 'text-davinci-003';
		$api_address = $option['api_address'] ?? null;
		$stream = empty($option['stream']) ? false : true;
		$data = [
			'model' => $model,
			'prompt' => $message,
			'stream' => $stream
		];

		try {
			$client = new Client($apikey, $model);
			$client->setApi($api_address);
			if ($stream) {
				header("Cache-Control: no-cache");
				header("Content-Type: text/event-stream");
				header('X-Accel-Buffering: no');
				$client->setStreamHandler(array($this, 'stream'));
				$client->completions($data);
				exit;
			}
			$completions = $client->completions($data);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}

		$data = [];
		$data['text'] = $this->handleText($completions->getText());
		// Codex model use code highlight by default
		if ($model == 'code-davinci-002') {
			$data['text'] = "<pre><code>{$data['text']}</code></pre>";
		}
		$data['time'] = $completions->getTime();
		// $data['temp'] = $completions->getWPResponse();
		
		return $this->success($data);
	}

	private function handleText($text) {
		// escape html
		$text = htmlspecialchars($text);
		// format code, support highlight
		$text = preg_replace_callback("/```(\w*)\s?\n?/", function ($matches) {
			$this->spe++;
			if (!empty($matches[1]) || (empty($matches[1]) && $this->spe % 2 == 1)) {
				$lang = empty($matches[1]) ? 'plaintext' : $matches[1];
				return "<pre><code class=\"language-{$lang}\">";
			}
			if ($matches[0] === "``` \n" || (empty($matches[1]) && $this->spe % 2 == 0)) {
				return "</code></pre>\n";
			}
		}, $text);
		return $text;
	}

	public function messages($request) {
		$option = get_option(Ai_Companion_OPTION_KEY);
		$model = $option['model'] ?? 'text-davinci-003';
		$apikey = $option['openai_api_key'] ?? '';
		if (empty($apikey)) {
			return $this->error('API_KEY not set');
		}

		$client = new Client($apikey, $model);
		$message = $client->getContextMessage();
		$list = [];
		foreach ($message as $msg) {
			if ($msg['role'] == Context::LABEL_SYS) {
				continue;
			}
			$msg['content'] = $this->handleText($msg['content']);
			$list[] = $msg;
		}

		return $this->success([
			'list' => $list
		]);
	}

	public function clean($request) {
		$option = get_option(Ai_Companion_OPTION_KEY);
		$model = $option['model'] ?? 'text-davinci-003';
		$apikey = $option['openai_api_key'] ?? '';
		if (empty($apikey)) {
			return $this->error('API_KEY not set');
		}

		$client = new Client($apikey, $model);
		$client->cleanContext();

		return $this->success();
	}
}
