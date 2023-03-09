<?php
namespace Dozen\OpenAi;

class Completions extends OpenAi {
    /**
     * @var string the api url
     */
    protected $url = '/v1/completions';
    /**
     * @var array 接口响应数据
     */
    protected $response;
    /**
     * @var string model used
     */
    protected $model;
    /**
     * @var Context context
     */
    protected $context;

    public function setModel($model) {
        $this->model = $model;
    }

    public function getModel() {
        return $this->model;
    }

    public function setContext($context) {
        $this->context = $context;
    }

    public function getResponse() {
        return $this->response;
    }
    
    /**
     * 请求接口，获取响应信息
     */
    public function create($request_body) {
        $request_body = $this->handleRequestBody($request_body);
        
        if (!empty($request_body['stream'])) {
            $this->response = $this->stream($this->url, $request_body);
        } else {
            $this->response = $this->request($this->url, $request_body);
        }
        
        return $this->response;
    }

    /**
     * handle the request body
     */
    protected function handleRequestBody($request_body) {
        $request_body['prompt'] = $this->context == null ? $request_body['prompt'] : $this->context->getContent();

        if (!isset($request_body['prompt']) || !is_string($request_body['prompt']) || $request_body['prompt'] == '') {
            throw new \Exception("prompt empty");
        }

        return $request_body;
    }

    /**
     * return api common request body
     */
    protected function getCommonBody($request_body) {
        $body = [];
        if (isset($request_body['max_tokens'])) {
            $body['max_tokens'] = $request_body['max_tokens'];
        }
        if (isset($request_body['stream'])) {
            $body['stream'] = $request_body['stream'];
        }
        return $body;
    }

    /**
     * get completions text
     */
    public function getText() {
        $text = trim($this->response['choices'][0]['text']);
        $pos = strpos($text, Context::LABEL_AI);
        if ($pos === 0) {
            $text = substr($text, strlen(Context::LABEL_AI)+1);
        }
        $pos = strpos($text, "ChatGPT");
        if ($pos === 0) {
            $text = substr($text, strlen("ChatGPT")+1);
        }
        return trim($text);
    }

    /**
     * 获取回复时间
     */
    public function getTime() {
        return $this->response['created'];
    }
}