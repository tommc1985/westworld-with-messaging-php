<?php

class MessageDispatcher
{
    const SEND_MSG_IMMEDIATELY = 0;
    const NO_ADDITIONAL_INFO   = 0;
    const SENDER_ID_IRRELEVANT = -1;

    public static $instance;

    /**
     * Constructor
     */
    public function __construct() {
        $this->entityMap = [];
    }

    /**
     * Get Instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Discharge
     * @param  int   $pReceiver
     * @param  Telegram &$msg
     */
    public function discharge($pReceiver, Telegram &$msg)
    {
        if (!$pReceiver->handleMessage($msg)) {
            say(colouredString("Message not handled"), 'red');
        }
    }

    /**
     * Dispatch Message
     * @param  [type] $delay     [description]
     * @param  [type] $sender    [description]
     * @param  [type] $receiver  [description]
     * @param  [type] $msg       [description]
     * @param  [type] $AdditionalInfo [description]
     */
    public function dispatchMessage($delay, $sender, $receiver, $msg, $additionalInfo)
    {
        $pReceiver = EntityManager::getInstance()->getEntityFromID($receiver);

        //make sure the receiver is valid
        if (empty($pReceiver)) {
            say(colouredString("Warning! No Receiver with ID of {$receiver} found", null, 'red'));
            return;
        }

        //create the telegram
        $telegram = new Telegram(0, $sender, $receiver, $msg, $additionalInfo);

        //if there is no delay, route telegram immediately
        if ($delay <= 0) {
            if(defined('SHOW_MESSAGING_INFO')) {
                say(colouredString("Telegram dispatched at time: " . time() . " by {$sender} for {$receiver}. Msg is '{$msg}'", null, 'light_gray'));
            }

            //send the telegram to the recipient
            $this->discharge($pReceiver, $telegram);
        } else { //else calculate the time when the telegram should be dispatched
            $currentTime = time();

            $telegram->dispatchTime = $currentTime + $delay;

            //and put it in the queue
            PriorityQueue::getInstance()->insert($telegram);

            if(defined('SHOW_MESSAGING_INFO')) {
                say(colouredString("Delayed telegram from {$sender} recorded at time " . time() . " for {$receiver}. Msg is '$msg'", null, 'light_gray'));
            }
        }
    }

    public function dispatchDelayedMessages()
    {
        //first get current time
        $currentTime = time();

        $priorityQueue = PriorityQueue::getInstance();

        //now peek at the queue to see if any telegrams need dispatching.
        //remove all telegrams from the front of the queue that have gone
        //past their sell by date
        while(!$priorityQueue->isEmpty() &&
             ($priorityQueue->begin()->dispatchTime < $currentTime) &&
             ($priorityQueue->begin()->dispatchTime > 0)) {
        //read the telegram from the front of the queue
        $telegram = $priorityQueue->shift();

        //find the recipient
        $pReceiver = EntityManager::getInstance()->getEntityFromID($telegram->receiver);

        if(defined('SHOW_MESSAGING_INFO')) {
            say(colouredString("Queued telegram ready for dispatch: Sent to " . $pReceiver->getId() . ". Msg is '{$telegram->message}'", null, 'light_gray'));
        }

            //send the telegram to the recipient
            $this->discharge($pReceiver, $telegram);
        }
    }
}