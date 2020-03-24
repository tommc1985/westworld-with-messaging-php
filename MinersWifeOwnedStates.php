<?php

class WifesGlobalState extends State
{
    public static $instance;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
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
     * Enter
     * @param  BaseGameEntity $wife
     */
    public function enter($wife)
    {

    }

    /**
     * Execute
     * @param  BaseGameEntity $wife
     */
    public function execute($wife)
    {
        //1 in 10 chance of needing the bathroom (provided she is not already in the bathroom)
        if ((randomFloat() < 0.1) &&
           !$wife->getFSM()->isInState(VisitBathroom::getInstance())) {
                $wife->GetFSM()->ChangeState(VisitBathroom::getInstance());
        }
    }

    /**
     * Exit
     * @param  BaseGameEntity $wife
     */
    public function exit($wife)
    {

    }

    /**
     * On message
     * @param  BaseGameEntity $wife
     * @param  Telegram $message
     */
    public function onMessage($wife, $message)
    {
        switch($message->message) {
            case MessageTypes::Msg_HiHoneyImHome:
                say(colouredString("Message handled by " . EntityNames::getNameOfEntity($wife->getId()) . " at time: " . date("h:i:s"), 'light_gray'));

                say(EntityNames::getNameOfEntity($wife->getId()) . ": Hi honey. Let me make you some of mah fine country stew");

                $wife->getFSM()->changeState(CookStew::getInstance());

            return true;
        }

        return false;
    }
}




class DoHouseWork extends State
{
    public static $instance;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
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
     * Enter
     * @param  BaseGameEntity $wife
     */
    public function enter($wife)
    {

    }

    /**
     * Execute
     * @param  BaseGameEntity $wife
     */
    public function execute($wife)
    {
        switch(mt_rand(0,2)) {
            case 0:
                say(EntityNames::getNameOfEntity($wife->getId()) . ": Moppin' the floor");
            break;
            case 1:
                say(EntityNames::getNameOfEntity($wife->getId()) . ": Washin' the dishes");
            break;
            case 2:
                say(EntityNames::getNameOfEntity($wife->getId()) . ": Makin' the bed");
            break;
        }
    }

    /**
     * Exit
     * @param  BaseGameEntity $wife
     */
    public function exit($wife)
    {

    }

    /**
     * On message
     * @param  BaseGameEntity $wife
     * @param  Telegram $message
     */
    public function onMessage($wife, $message)
    {
        return false;
    }
}





class VisitBathroom extends State
{
    public static $instance;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
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
     * Enter
     * @param  BaseGameEntity $wife
     */
    public function enter($wife)
    {
        say(EntityNames::getNameOfEntity($wife->getId()) . ": Walkin' to the can. Need to powda mah pretty li'lle nose");
    }

    /**
     * Execute
     * @param  BaseGameEntity $wife
     */
    public function execute($wife)
    {
        say(EntityNames::getNameOfEntity($wife->getId()) . ": Ahhhhhh! Sweet relief!");

        $wife->getFSM()->revertToPreviousState();
    }

    /**
     * Exit
     * @param  BaseGameEntity $wife
     */
    public function exit($wife)
    {
        say(EntityNames::getNameOfEntity($wife->getId()) . ": Leavin' the Jon");
    }

    /**
     * On message
     * @param  BaseGameEntity $wife
     * @param  Telegram $message
     */
    public function onMessage($wife, $message)
    {
        return false;
    }
}





class CookStew extends State
{
    public static $instance;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
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
     * Enter
     * @param  BaseGameEntity $wife
     */
    public function enter($wife)
    {
        //if not already cooking put the stew in the oven
        if (!$wife->isCooking()) {
            say(EntityNames::getNameOfEntity($wife->getId()) . ": Putting the stew in the oven");

            //send a delayed message myself so that I know when to take the stew out of the oven
            MessageDispatcher::getInstance()->dispatchMessage(
                1.5,                            //time delay
                $wife->getId(),                 //sender ID
                $wife->getId(),                 //receiver ID
                MessageTypes::Msg_StewReady,    //msg
                MessageDispatcher::NO_ADDITIONAL_INFO
            );

            $wife->setCooking(true);
        }
    }

    /**
     * Execute
     * @param  BaseGameEntity $wife
     */
    public function execute($wife)
    {
        say(EntityNames::getNameOfEntity($wife->getId()) . ": Fussin' over food");
    }

    /**
     * Exit
     * @param  BaseGameEntity $wife
     */
    public function exit($wife)
    {
        say(EntityNames::getNameOfEntity($wife->getId()) . ": Puttin' the stew on the table");
    }

    /**
     * On message
     * @param  BaseGameEntity $wife
     * @param  Telegram $message
     */
    public function onMessage($wife, $message)
    {
        switch($message->message) {
            case MessageTypes::Msg_StewReady:
                say(colouredString("Message handled by " . EntityNames::getNameOfEntity($wife->getId()) . " at time: " . date("h:i:s"), 'light_gray'));

                say(EntityNames::getNameOfEntity($wife->getId()) . ": StewReady! Lets eat");

                //let hubby know the stew is ready
                MessageDispatcher::getInstance()->dispatchMessage(
                    MessageDispatcher::SEND_MSG_IMMEDIATELY,
                    $wife->getId(),
                    EntityNames::ENT_MINER_BOB,
                    MessageTypes::Msg_StewReady,
                    MessageDispatcher::NO_ADDITIONAL_INFO
                );

                $wife->setCooking(false);

                $wife->getFSM()->changeState(DoHouseWork::getInstance());

            return true;

        }

        return false;
    }
}