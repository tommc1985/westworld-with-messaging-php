<?php

class EntityNames
{
    const ENT_MINER_BOB = 0;
    const ENT_ELSA = 1;

    public static function getNameOfEntity(int $entity)
    {
        switch ($entity) {
            case self::ENT_MINER_BOB:
                return 'Miner Bob';
            case self::ENT_ELSA:
                return 'Elsa';
        }

        return 'UNKNOWN!';
    }
}