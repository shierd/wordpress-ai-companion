<?php
namespace Dozen\OpenAi;

class Context {
    /**
     * @var string INSTRUCTION
     */
    const INSTRUCTION = "You are ChatGPT, a large language model trained by OpenAI. Answer as concisely as possible. Knowledge cutoff: %s Current date: %s";
    const MESSAGE_KEY = 'wp_openai_client_message_v2';
    /**
     * @var string 上下文内容
     */
    private $content;
    /**
     * @var array 消息队列
     */
    private $message;
    /**
     * @var string user label
     */
    const LABEL_USER = 'user';
    /**
     * @var string ai label
     */
    const LABEL_AI = 'assistant';
    /**
     * @var string system label
     */
    const LABEL_SYS = 'system';
    /**
     * @var string GPT model
     */
    private $model;
    
    public function __construct($model){
        $this->message = [];
        $this->model = $model;
        // 启动 session
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
        // 检查 session 是否有上下文消息
        if (isset($_SESSION[self::MESSAGE_KEY])) {
            $this->message = $_SESSION[self::MESSAGE_KEY];
        }

        $this->init();
    }

    private function init() {
        switch ($this->model) {
            case 'text-davinci-003':
                $preamble = sprintf(self::INSTRUCTION, '2023-03', date('Y-m-d'));
                $this->content = "Instructions: {$preamble} <|endoftext|>\n";
                if ($this->message) {
                    foreach ($this->message as $msg) {
                        $this->content .= $this->getMessageContent($msg['role'], $msg['content']);
                    }
                }
                break;
            case 'code-davinci-002':
                break;
            case 'gpt-3.5-turbo':
                $preamble = sprintf(self::INSTRUCTION, '2023-03', date('Y-m-d'));
                if (empty($this->message)) {
                    $msg = ['role' => self::LABEL_SYS, 'content' => $preamble];
                    array_push($this->message, $msg);
                }
                break;
        }
    }

    private function getMessageContent($label, $content) {
        return "{$label}: " . $content . " <|endoftext|>\n";
    }

    public function addPrompt($prompt) {
        $this->content = $this->content . $this->getMessageContent(self::LABEL_USER, $prompt);
        $this->pushMessage(self::LABEL_USER, $prompt);
    }

    public function addCompletion($completion) {
        $this->content = $this->content . $this->getMessageContent(self::LABEL_AI, $completion);
        $this->pushMessage(self::LABEL_AI, $completion);
    }

    /**
     * 把消息推进消息队列并缓存起来
     */
    private function pushMessage($label, $content) {
        $msg = [
            'role' => $label,
            'content' => $content
        ];
        array_push($this->message, $msg);
        $this->shiftMessage();
        $_SESSION[self::MESSAGE_KEY] = $this->message;
    }

    /**
     * 检查上下文内容是否超过最大 token ，超过则移除一些消息
     * 非严谨处理
     */
    private function shiftMessage() {
        $length = 0;
        foreach ($this->message as $val) {
            $length += strlen($val['content']);
        }
        if (4096 - $length / 2 - 512 - 1024 < 0) {
            array_shift($this->message);
            array_shift($this->message);
        }
    }

    public function getContent() {
        return $this->content;
    }

    public function getMessage() {
        return $this->message;
    }
}