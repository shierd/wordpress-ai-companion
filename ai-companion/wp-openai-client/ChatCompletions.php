<?php
namespace Dozen\OpenAi;

class ChatCompletions extends Completions {
    /**
     * @var string the api url
     */
    protected $url = 'https://api.openai.com/v1/chat/completions';

    /**
     * handle the request body
     */
    protected function handleRequestBody($request_body) {
        $messages = $this->context == null ? $request_body['messages'] : $this->context->getMessage();

        $body = [
            'model' => $request_body['model'],
            'messages' => $messages
        ];
        $common = $this->getCommonBody($request_body);

        return array_merge($body, $common);
    }

    /**
     * get completions text
     */
    public function getText() {
        $msg = $this->response['choices'][0];
        if (!isset($msg['message'])) {
            throw new \Exception('empty message');
        }
        $text = trim($msg['message']['content']);
        return $text;
    }
}