<?php
namespace Dozen\OpenAi;

class Client {
    /**
     * @var string YOUR_API_KEY https://platform.openai.com/account/api-keys
     */
    protected $apikey;

    public function __construct($apikey){
        $this->apikey = $apikey;
    }

    /**
     * 请求 completions 接口，返回 Completions 对象
     * @param array 接口参数
     * @return Completions
     */
    public function completions($request_body) {
        $model = $request_body['model'];
        $context = new Context($model);
        switch ($model) {
            case 'text-davinci-003':
                // add message
                $context->addPrompt($request_body['prompt']);
                // create completion
                $completions = new Completions($this->apikey);
                $completions->setModel($model);
                $completions->setContext(new Context($model));
                $completions->create($request_body);
                // add completion
                $context->addCompletion($completions->getText());
                break;
            case 'code-davinci-002':
                break;
            case 'gpt-3.5-turbo':
                // add message
                $context->addPrompt($request_body['prompt']);
                // create completion
                $completions = new ChatCompletions($this->apikey);
                $completions->setModel($model);
                $completions->setContext(new Context($model));
                $completions->create($request_body);
                // add completion
                $context->addCompletion($completions->getText());
                break;
        }
        // var_dump($context->getMessage());
        
        return $completions;
    }
}