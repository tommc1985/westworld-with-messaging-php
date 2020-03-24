<?php

class MessageTypes
{
    const Msg_HiHoneyImHome = 1;
    const Msg_StewReady = 2;

    public static function messageToString(int $msg)
    {
        switch ($msg) {
            case self::Msg_HiHoneyImHome:
                return 'HiHoneyImHome';
            case self::Msg_StewReady:
                return 'StewReady';
        }

        return 'Not recognized!';
    }
}