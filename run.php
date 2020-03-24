<?php
require_once('BaseGameEntity.php');
require_once('EntityManager.php');
require_once('EntityNames.php');
require_once('Locations.php');
require_once('State.php');
require_once('StateMachine.php');
require_once('PriorityQueue.php');
require_once('Telegram.php');
require_once('MessageDispatcher.php');
require_once('MessageTypes.php');
require_once('Miner.php');
require_once('MinerOwnedStates.php');
require_once('MinersWife.php');
require_once('MinersWifeOwnedStates.php');

define('SHOW_MESSAGING_INFO', true);

function say($string)
{
    echo $string . PHP_EOL;
}

function randomFloat()
{
    return mt_rand() / mt_getrandmax();
}

//create a miner
$bob = new Miner(EntityNames::ENT_MINER_BOB);

//create his wife
$elsa = new MinersWife(EntityNames::ENT_ELSA);

//register them with the entity manager
EntityManager::getInstance()->registerEntity($bob);
EntityManager::getInstance()->registerEntity($elsa);

//run Bob and Elsa through a few Update calls
for ($i=0; $i<30; ++$i) {
    $bob->update();
    $elsa->update();

    //dispatch any delayed messages
    MessageDispatcher::getInstance()->dispatchDelayedMessages();

    sleep(1);
}


function colouredString($string, $fgColor = null, $bgColor = null)
{
    $foregroundColors['black'] = '0;30';
    $foregroundColors['dark_gray'] = '1;30';
    $foregroundColors['blue'] = '0;34';
    $foregroundColors['light_blue'] = '1;34';
    $foregroundColors['green'] = '0;32';
    $foregroundColors['light_green'] = '1;32';
    $foregroundColors['cyan'] = '0;36';
    $foregroundColors['light_cyan'] = '1;36';
    $foregroundColors['red'] = '0;31';
    $foregroundColors['light_red'] = '1;31';
    $foregroundColors['purple'] = '0;35';
    $foregroundColors['light_purple'] = '1;35';
    $foregroundColors['brown'] = '0;33';
    $foregroundColors['yellow'] = '1;33';
    $foregroundColors['light_gray'] = '0;37';
    $foregroundColors['white'] = '1;37';

    $backgroundColors['black'] = '40';
    $backgroundColors['red'] = '41';
    $backgroundColors['green'] = '42';
    $backgroundColors['yellow'] = '43';
    $backgroundColors['blue'] = '44';
    $backgroundColors['magenta'] = '45';
    $backgroundColors['cyan'] = '46';
    $backgroundColors['light_gray'] = '47';

    $colored_string = "";

    // Check if given foreground color found
    if (isset($foregroundColors[$fgColor])) {

        $colored_string .= "\033[" . $foregroundColors[$fgColor] . "m";
    }

    // Check if given background color found
    if (isset($foregroundColors[$bgColor])) {
        $colored_string .= "\033[" . $foregroundColors[$bgColor] . "m";
    }

    // Add string and end coloring
    $colored_string .=  $string . "\033[0m";

    return $colored_string;
}