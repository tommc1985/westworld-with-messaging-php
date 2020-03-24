<?php

class EnterMineAndDigForNugget extends State
{
    public static $instance;

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
     * @param  BaseGameEntity $pMiner
     */
    public function enter($pMiner)
    {
        //if the miner is not already located at the goldmine, he must
        //change location to the gold mine
        if ($pMiner->location() != Locations::GOLDMINE) {
            say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Walkin' to the goldmine");
        }

        $pMiner->changeLocation(Locations::GOLDMINE);
    }

    /**
     * Execute
     * @param  BaseGameEntity $pMiner
     */
    public function execute($pMiner)
    {
        //Now the miner is at the goldmine he digs for gold until he
        //is carrying in excess of MaxNuggets. If he gets thirsty during
        //his digging he packs up work for a while and changes state to
        //gp to the saloon for a whiskey.
        $pMiner->addToGoldCarried(1);

        $pMiner->increaseFatigue();

        say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Pickin' up a nugget");

        //if enough gold mined, go and put it in the bank
        if ($pMiner->isPocketsFull()) {
            $pMiner->getFSM()->changeState(VisitBankAndDepositGold::getInstance());
        }

        if ($pMiner->isThirsty()) {
            $pMiner->getFSM()->changeState(QuenchThirst::getInstance());
        }
    }

    /**
     * Exit
     * @param  BaseGameEntity $pMiner
     */
    public function exit($pMiner)
    {
        say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Ah'm leavin' the goldmine with mah pockets full o' sweet gold");
    }

    /**
     * On message
     * @param  BaseGameEntity $agent
     * @param  Telegram $message
     */
    public function onMessage($agent, $message)
    {
        //send msg to global message handler
        return false;
    }
}







class VisitBankAndDepositGold extends State
{
    public static $instance;

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
     * @param  BaseGameEntity $pMiner
     */
    public function enter($pMiner)
    {
        //on entry the miner makes sure he is located at the bank
        if ($pMiner->location() != Locations::BANK) {
            say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Goin' to the bank. Yes siree");

            $pMiner->changeLocation(Locations::BANK);
        }
    }

    /**
     * Execute
     * @param  BaseGameEntity $pMiner
     */
    public function execute($pMiner)
    {
        //deposit the gold
        $pMiner->addToWealth($pMiner->getGoldCarried());

        $pMiner->setGoldCarried(0);

        say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Depositing gold. Total savings now: " . $pMiner->wealth());

        //wealthy enough to have a well earned rest?
        if ($pMiner->Wealth() >= Miner::ComfortLevel) {
            say(EntityNames::getNameOfEntity($pMiner->getId()) . ": WooHoo! Rich enough for now. Back home to mah li'lle lady");

            $pMiner->getFSM()->changeState(GoHomeAndSleepTilRested::getInstance());
        } else { //otherwise get more gold
            $pMiner->getFSM()->changeState(EnterMineAndDigForNugget::getInstance());
        }
    }

    /**
     * Exit
     * @param  BaseGameEntity $pMiner
     */
    public function exit($pMiner)
    {
        say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Leavin' the bank");
    }

    /**
     * On message
     * @param  BaseGameEntity $agent
     * @param  Telegram $message
     */
    public function onMessage($agent, $message)
    {
        //send msg to global message handler
        return false;
    }
}







class GoHomeAndSleepTilRested extends State
{
    public static $instance;

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
     * @param  BaseGameEntity $pMiner
     */
    public function enter($pMiner)
    {
        if ($pMiner->location() != Locations::SHACK) {
            say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Walkin' home");

            $pMiner->changeLocation(Locations::SHACK);

            //let the wife know I'm home
            MessageDispatcher::getInstance()->dispatchMessage(
                MessageDispatcher::SEND_MSG_IMMEDIATELY, //time delay
                $pMiner->getId(),        //ID of sender
                EntityNames::ENT_ELSA,            //ID of recipient
                MessageTypes::Msg_HiHoneyImHome,   //the message
                MessageDispatcher::NO_ADDITIONAL_INFO
            );
        }
    }

    /**
     * Execute
     * @param  BaseGameEntity $pMiner
     */
    public function execute($pMiner)
    {
        //if miner is not fatigued start to dig for nuggets again.
        if (!$pMiner->isFatigued()) {
            say(EntityNames::getNameOfEntity($pMiner->getId()) . ": All mah fatigue has drained away. Time to find more gold!");

            $pMiner->getFSM()->changeState(EnterMineAndDigForNugget::getInstance());
        } else { //sleep
            $pMiner->DecreaseFatigue();

            say(EntityNames::getNameOfEntity($pMiner->getId()) . ": ZZZZ...");
        }
    }

    /**
     * Exit
     * @param  BaseGameEntity $pMiner
     */
    public function exit($pMiner)
    {

    }

    /**
     * On message
     * @param  BaseGameEntity $pMiner
     * @param  Telegram $message
     */
    public function onMessage($pMiner, $message)
    {
        switch($message->message) {
            case MessageTypes::Msg_StewReady:
                say(colouredString("Message handled by " . EntityNames::getNameOfEntity($pMiner->getId()) . " at time: " . date("h:i:s"), 'light_gray'));

                say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Okay Hun, ahm a comin'!");

                $pMiner->getFSM()->changeState(EatStew::getInstance());

                return true;

        }

        return false; //send message to global message handler
    }
}







class QuenchThirst extends State
{
    public static $instance;

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
     * @param  BaseGameEntity $pMiner
     */
    public function enter($pMiner)
    {
        if ($pMiner->location() != Locations::SALOON) {
            $pMiner->changeLocation(Locations::SALOON);

            say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Boy, ah sure is thusty! Walking to the saloon");
        }
    }

    /**
     * Execute
     * @param  BaseGameEntity $pMiner
     */
    public function execute($pMiner)
    {
        $pMiner->buyAndDrinkAWhiskey();

        say(EntityNames::getNameOfEntity($pMiner->getId()) . ": That's mighty fine sippin' liquer");

        $pMiner->getFSM()->changeState(EnterMineAndDigForNugget::getInstance());
    }

    /**
     * Exit
     * @param  BaseGameEntity $pMiner
     */
    public function exit($pMiner)
    {
        say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Leaving the saloon, feelin' good");
    }

    /**
     * On message
     * @param  BaseGameEntity $agent
     * @param  Telegram $message
     */
    public function onMessage($agent, $message)
    {
        //send msg to global message handler
        return false;
    }
}







class EatStew extends State
{
    public static $instance;

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
     * @param  BaseGameEntity $pMiner
     */
    public function enter($pMiner)
    {
        say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Smells Reaaal goood Elsa!");
    }

    /**
     * Execute
     * @param  BaseGameEntity $pMiner
     */
    public function execute($pMiner)
    {
        say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Tastes real good too!");

        $pMiner->getFSM()->revertToPreviousState();
    }

    /**
     * Exit
     * @param  BaseGameEntity $pMiner
     */
    public function exit($pMiner)
    {
        say(EntityNames::getNameOfEntity($pMiner->getId()) . ": Thankya li'lle lady. Ah better get back to whatever ah wuz doin'");
    }

    /**
     * On message
     * @param  BaseGameEntity $agent
     * @param  Telegram $message
     */
    public function onMessage($agent, $message)
    {
        //send msg to global message handler
        return false;
    }
}