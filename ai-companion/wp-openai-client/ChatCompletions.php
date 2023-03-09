<?php
namespace Dozen\OpenAi;

class ChatCompletions extends Completions {
    /**
     * @var string the api url
     */
    protected $url = '/v1/chat/completions';

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
        $k = $this->stream ? 'delta' : 'message';
        if (!isset($msg[$k])) {
            throw new \Exception('empty message');
        }
        $text = trim($msg[$k]['content']);
        return $text;
    }
}