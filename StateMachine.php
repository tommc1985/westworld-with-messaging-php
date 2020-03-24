<?php

class StateMachine
{
    public $m_pOwner;
    protected $m_pCurrentState;
    protected $m_pPreviousState;
    protected $m_pGlobalState;

    /**
     * Constructor
     */
    public function __construct($owner)
    {
        $this->m_pOwner = $owner;
        $this->m_pCurrentState = null;
        $this->m_pPreviousState = null;
        $this->m_pGlobalState = null;
    }

    /**
     * Set current state
     * @param State $state
     */
    public function setCurrentState($state)
    {
        $this->m_pCurrentState = $state;
    }

    /**
     * Set global state
     * @param State $state
     */
    public function setGlobalState($state)
    {
        $this->m_pGlobalState = $state;
    }

    /**
     * Set previous state
     * @param State $state
     */
    public function setPreviousState($state)
    {
        $this->m_pPreviousState = $state;
    }

    public function update()
    {
        //if a global state exists, call its execute method, else do nothing
        if($this->m_pGlobalState) {
            $this->m_pGlobalState->execute($this->m_pOwner);
        }

        //same for the current state
        if($this->m_pCurrentState) {
            $this->m_pCurrentState->execute($this->m_pOwner);
        }
    }

    public function handleMessage(Telegram &$msg)
    {
        //first see if the current state is valid and that it can handle
        //the message
        if ($this->m_pCurrentState && $this->m_pCurrentState->OnMessage($this->m_pOwner, $msg)) {
            return true;
        }

        //if not, and if a global state has been implemented, send
        //the message to the global state
        if ($this->m_pGlobalState && $this->m_pGlobalState->OnMessage($this->m_pOwner, $msg)) {
            return true;
        }

        return false;
    }

    public function changeState($pNewState)
    {
        if (empty($pNewState)) {
            say('StateMachine::changeState : trying to assign null state to current');
        }

        //keep a record of the previous state
        $this->m_pPreviousState = $this->m_pCurrentState;

        //call the exit method of the existing state
        $this->m_pCurrentState->exit($this->m_pOwner);

        //change state to the new state
        $this->m_pCurrentState = $pNewState;

        //call the entry method of the new state
        $this->m_pCurrentState->enter($this->m_pOwner);
    }

    /**
     * Revert to previous state
     * @return boolean
     */
    public function revertToPreviousState()
    {
        $this->changeState($this->m_pPreviousState);
    }

    /**
     * returns true if the current state's type is equal to the type of the class passed as a parameter
     * @param  State  $state
     * @return boolean
     */
    public function isInState($state)
    {
        return $this->m_pCurrentState === $state;
    }

    /**
     * Get Current State
     */
    public function getCurrentState()
    {
        return $this->m_pCurrentState;
    }

    /**
     * Get Global State
     */
    public function getGlobalState()
    {
        return $this->m_pGlobalState;
    }

    /**
     * Get Previous State
     */
    public function getPreviousState()
    {
        return $this->m_pPreviousState;
    }




    /**
     * Only ever used during debugging to grab the name of the current state
     * @return string
     */
    public function getNameOfCurrentState()
    {
        return get_class($this->m_pCurrentState);
    }
}