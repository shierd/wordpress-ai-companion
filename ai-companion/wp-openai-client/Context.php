<?php
namespace Dozen\OpenAi;

class Context {
    /**
     * @var string 前言
     */
    const PREAMBLE = "Instructions:\nYou are %s, a large language model trained by OpenAI.\nYou answer as concisely as possible for each response (e.g. don't be verbose).\nIt is very important that you answer as concisely as possible, so please remember this.\nIf you are generating a list, do not have too many items. Keep the number of items short.\nCurrent time: %s<|endoftext|>";
    const MESSAGE_KEY = 'wp_openai_client_message';
    /**
     * @var string 上下文内容
     */
    private $content;
    /**
     * @var array 消息队列
     */
    private $message;
    /**
     * @var string 用户名字
     */
    private $userLabel = 'User';
    /**
     * @var string AI名字
     */
    private $aiLabel = 'ChatGPT';
    /**
     * @var static 单例
     */
    private static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    
    public function __construct(){
        $this->message = [];
        $preamble = sprintf(self::PREAMBLE, $this->aiLabel, date('Y-m-d H:i:s'));
        $this->content = "{$preamble}";

        // 启动 session
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
        // 检查 session 是否有上下文消息
        if (isset($_SESSION[self::MESSAGE_KEY])) {
            $this->message = $_SESSION[self::MESSAGE_KEY];
            foreach ($this->message as $msg) {
                $this->content .= $this->getMessageContent($msg['label'], $msg['content']);
            }
        }
    }

    private function getMessageContent($label, $content) {
        return "\n\n{$label}:\n" . $content . '<|endoftext|>';
    }

    public function addPrompt($prompt) {
        $this->content = $this->content . $this->getMessageContent($this->userLabel, $prompt);
        $this->pushMessage($this->userLabel, $prompt);
    }

    public function addCompletion($completion) {
        $this->content = $this->content . $this->getMessageContent($this->aiLabel, $completion);
        $this->pushMessage($this->aiLabel, $completion);
    }

    /**
     * 把消息推进消息队列并缓存起来
     */
    private function pushMessage($label, $content) {
        $msg = [
            'label' => $label,
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
        $length = strlen($this->content);
        if (4096 - $length / 2 - 512 - 1024 < 0) {
            array_shift($this->message);
            array_shift($this->message);
        }
    }

    public function getContent() {
        return $this->content;
    }

    public function getUserLabel() {
        return $this->userLabel;
    }

    public function getAiLabel() {
        return $this->aiLabel;
    }
}