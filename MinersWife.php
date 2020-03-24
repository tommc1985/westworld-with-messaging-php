<?php

class MinersWife extends BaseGameEntity
{
    //an instance of the state machine class
    protected $m_pStateMachine;

    protected $m_Location;

    //is she presently cooking?
    protected $m_bCooking;

    public function __construct($id)
    {
        parent::__construct($id);
        $this->m_Location = Locations::SHACK;
        $this->m_bCooking = false;

        //set up state machine
        $this->m_pStateMachine = new StateMachine($this);

        $this->m_pStateMachine->setCurrentState(DoHouseWork::getInstance());

        $this->m_pStateMachine->setGlobalState(WifesGlobalState::getInstance());
    }

    public function update()
    {
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

    public function isCooking()
    {
        return $this->m_bCooking;
    }

    public function setCooking($val)
    {
        $this->m_bCooking = $val;
    }
}