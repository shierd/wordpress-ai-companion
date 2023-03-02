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
			'methods' => 'POST',
			'callback' => [$this, 'answer']
		]);
	}

	public function response($code, $msg, $data) {
		$result = [
			'code' => $code,
			'data' => $data,
			'message' => $msg
		];
		return new WP_REST_Response($result);
	}

	public function success($data=[]) {
		return $this->response(600, 'success', $data);
	}

	public function error($msg='error', $data=[]) {
		return new WP_Error( 601, __( $msg, 'text-domain' ), $data );
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
		$data = [
			'model' => $model,
			'prompt' => $message,
			'max_tokens' => 1024
		];

		try {
			$client = new Client($apikey);
			$completions = $client->completions($data);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}

		$data = [];
		$data['text'] = $completions->getText();
		$data['time'] = $completions->getTime();
		$data['temp'] = $completions->getWPResponse();
		
		return $this->success($data);
	}

}
