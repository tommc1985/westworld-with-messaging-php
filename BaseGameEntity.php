<?php

BaseGameEntity::$m_iNextValidID = 0;

class BaseGameEntity
{
    protected $m_ID;
    public static $m_iNextValidID;

    /**
     * Constructor
     * @param int $id
     */
    public function __construct($id)
    {
        $this->setId($id);
    }

    /**
     * Set Id
     * @param int $id
     */
    public function setId($id)
    {
        //make sure the val is equal to or greater than the next available ID
        if ($id < self::$m_iNextValidID) {
            say(colouredString("BaseGameEntity::setId: invalid ID ({$id})", 'red'));
            die();
        }

        $this->m_ID = $id;

        BaseGameEntity::$m_iNextValidID = $this->m_ID + 1;
    }

    /**
     * Get Id
     * @return int
     */
    public function getId()
    {
        return $this->m_ID;
    }

    /**
     * Update function
     */
    public function update() {}

    /**
     * Handle Message
     */
    public function handleMessage($msg)
    {

    }
}