<?php
namespace Dozen\OpenAi;

class Client {
    /**
     * @var string the api domain
     */
    protected $api;
    /**
     * @var string YOUR_API_KEY https://platform.openai.com/account/api-keys
     */
    protected $apikey;
    /**
     * @var string GPT model
     */
    private $model;
    /**
     * @var Context context
     */
    private $context;

    public function __construct($apikey, $model){
        $this->apikey = $apikey;
        $this->model = $model;
        $this->context = new Context($this->model);
    }

    public function setApi($api) {
        $this->api = $api;
    }

    /**
     * 请求 completions 接口，返回 Completions 对象
     * @param array 接口参数
     * @return Completions
     */
    public function completions($request_body) {
        $model = $request_body['model'];
        $context = $this->context;
        // add message
        $context->addPrompt($request_body['prompt']);
        switch ($model) {
            case 'text-davinci-003':
                if (!isset($request_body['max_tokens'])) {
                    $request_body['max_tokens'] = 1024;
                }
                // create completion
                $completions = new Completions($this->apikey, $this->api);
                $completions->setModel($model);
                $completions->setContext($context);
                $completions->create($request_body);
                break;
            case 'code-davinci-002':
                // Lower temperatures give more precise results.
                if (!isset($request_body['temperature'])) {
                    $request_body['temperature'] = 0;
                }
                // Limit completion size for more precise results or lower latency.
                if (!isset($request_body['max_tokens'])) {
                    $request_body['max_tokens'] = 512;
                }
                // create completion
                $completions = new Completions($this->apikey, $this->api);
                $completions->setModel($model);
                $completions->create($request_body);
                break;
            case 'gpt-3.5-turbo':
                if (!isset($request_body['max_tokens'])) {
                    $request_body['max_tokens'] = 1024;
                }
                // create completion
                $completions = new ChatCompletions($this->apikey, $this->api);
                $completions->setModel($model);
                $completions->setContext($context);
                $completions->create($request_body);
                break;
        }
        // add completion
        $context->addCompletion($completions->getText());
        // var_dump($context->getMessage());
        
        return $completions;
    }

    /**
     * return the context message
     */
    public function getContextMessage() {
        return $this->context->getMessage();
    }

    public function cleanContext() {
        $this->context->cleanMessage();
    }
}