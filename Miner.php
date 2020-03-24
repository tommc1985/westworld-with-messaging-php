<?php

class Miner extends BaseGameEntity
{

    //the amount of gold a miner must have before he feels he can go home
    const ComfortLevel       = 5;
    //the amount of nuggets a miner can carry
    const MaxNuggets         = 3;
    //above this value a miner is thirsty
    const ThirstLevel        = 5;
    //above this value a miner is sleepy
    const TirednessThreshold = 5;

    //an instance of the state machine class
    protected $m_pStateMachine;

    protected $m_Location;

    //how many nuggets the miner has in his pockets
    protected $m_iGoldCarried;

    protected $m_iMoneyInBank;

    //the higher the value, the thirstier the miner
    protected $m_iThirst;

    //the higher the value, the more tired the miner
    protected $m_iFatigue;

    public function __construct($id)
    {
        parent::__construct($id);
        $this->m_Location = Locations::SHACK;
        $this->m_iGoldCarried = 0;
        $this->m_iMoneyInBank = 0;
        $this->m_iThirst = 0;
        $this->m_iFatigue = 0;

        //set up state machine
        $this->m_pStateMachine = new StateMachine($this);

        $this->m_pStateMachine->setCurrentState(GoHomeAndSleepTilRested::getInstance());

        /* NOTE, A GLOBAL STATE HAS NOT BEEN IMPLEMENTED FOR THE MINER */
    }

    public function update()
    {
        $this->m_iThirst += 1;

        $this->m_pStateMachine->update();
    }

    public function handleMessage($msg)
    {
        return $this->m_pStateMachine->handleMessage($msg);
    }

    public function location()
    {
        return $this->m_Location;
    }

    public function changeLocation($location)
    {
        $this->m_Location = $location;
    }

    public function getFSM()
    {
        return $this->m_pStateMachine;
    }

    public function getGoldCarried()
    {
        return $this->m_iGoldCarried;
    }

    public function setGoldCarried($val)
    {
        return $this->m_iGoldCarried = $val;
    }

    public function addToGoldCarried($val)
    {
        $this->m_iGoldCarried += $val;

        if ($this->m_iGoldCarried < 0) {
            $this->m_iGoldCarried = 0;
        }
    }

    public function isPocketsFull()
    {
        return $this->m_iGoldCarried >= self::MaxNuggets;
    }

    public function isFatigued()
    {
        return $this->m_iFatigue > self::TirednessThreshold;
    }

    public function decreaseFatigue()
    {
        return $this->m_iFatigue--;
    }

    public function increaseFatigue()
    {
        return $this->m_iFatigue++;
    }

    public function wealth()
    {
        return $this->m_iMoneyInBank;
    }

    public function setWealth($val)
    {
        $this->m_iMoneyInBank = $val;
    }

    public function addToWealth($val)
    {
        $this->m_iMoneyInBank += $val;

        if ($this->m_iMoneyInBank < 0) {
             $this->m_iMoneyInBank = 0;
        }
    }

    public function isThirsty()
    {
        return $this->m_iThirst >= self::ThirstLevel;
    }

    public function buyAndDrinkAWhiskey()
    {
        $this->m_iThirst = 0;
        $this->m_iMoneyInBank -= 2;
    }
}