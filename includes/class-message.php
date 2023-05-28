<?php
namespace BengalStudio;

class Message {
    private $sender_id;
    private $message;
    private $thread_id;
    private $recipients;

    public function __construct($sender_id, $message, $thread_id = null, $recipients = array()) {
        $this->sender_id = $sender_id;
        $this->message = $message;
        $this->thread_id = $thread_id;
        $this->recipients = $recipients;
    }

    public function send() {
        $chat_thread = new Thread($this->thread_id);
        return $chat_thread->add_chat_message($this->sender_id, $this->message, $this->recipients);
    }
}



