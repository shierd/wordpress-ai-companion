<?php
namespace Dozen\OpenAi;

class Completions extends OpenAi {
    /**
     * @var string 接口地址
     */
    const API_URL = 'https://api.openai.com/v1/completions';
    /**
     * @var array 接口响应数据
     */
    private $response;
    
    /**
     * 请求接口，获取响应信息
     */
    public function create($request_body) {
        if (!isset($request_body['prompt']) || !is_string($request_body['prompt']) || $request_body['prompt'] == '') {
            return null;
        }
        // 创建上下文
        $context = new Context();
        // 添加消息
        $context->addPrompt($request_body['prompt']);
        $request_body['prompt'] = $context->getContent();
        $this->response = $this->request(self::API_URL, $request_body);
        // 添加回复
        $context->addCompletion($this->getText());
        return $this->response;
    }

    /**
     * 返回响应信息
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * 获取回复文本
     */
    public function getText() {
        $text = trim($this->response['choices'][0]['text']);
        $pos = strpos($text, Context::instance()->getAiLabel());
        if ($pos === 0) {
            $text = substr($text, strlen(Context::instance()->getAiLabel())+1);
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