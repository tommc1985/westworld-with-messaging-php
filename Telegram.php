<?php

class Telegram
{
    public $sender;
    public $receiver;
    public $message;
    public $dispatchTime;
    public $extraInfo;

    public function __construct($time, $sender, $receiver, $msg, $info = null)
    {
        $this->dispatchTime = $time;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->message = $msg;
        $this->extraInfo = $info;

        say(colouredString("Time: {$this->dispatchTime}|Sender: {$this->sender}|Receiver: {$this->receiver}|Message: {$this->message}", null, 'light_gray'));
    }
}