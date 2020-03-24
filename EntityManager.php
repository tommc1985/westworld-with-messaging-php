<?php

class EntityManager {

    public static $instance;

    protected $entityMap;

    /**
     * Constructor
     */
    public function __construct() {
        $this->entityMap = [];
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function registerEntity(BaseGameEntity $newEntity)
    {
        $this->entityMap[$newEntity->getId()] = $newEntity;
    }

    public function removeEntity(BaseGameEntity $pEntity)
    {
        unset($this->entityMap[$pEntity->getId()]);
    }

    public function getEntityFromID(int $id)
    {
        if (empty($this->entityMap[$id])) {
            say(colouredString("EntityManager::getEntityFromID: invalid ID ({$id})", 'red'));
            die();
        }

        return $this->entityMap[$id];
    }
}