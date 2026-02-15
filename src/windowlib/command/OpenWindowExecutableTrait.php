<?php

declare (strict_types=1);
 
/***
 *   
 * Rajador Developer
 * 
 * ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
 * ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
 * ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█
 * 
 * GitHub: https://github.com/rajadordev
 * 
 * Discord: rajadortv
 * 
 * 
**/ 

namespace windowlib\command;

use pocketmine\Player;
use pocketmine\utils\LevelException;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\SubCommand;
use windowlib\window\exception\InvalidWindowPositionException;
use windowlib\window\exception\InvalidWindowTypeException;
use windowlib\WindowLibLoader;

trait OpenWindowExecutableTrait
{

    protected function tryToOpen(Player $player, CommandArguments $args) : bool 
    {
        try {
            $this->openWindowTo($player, $args);
            return true;
        } catch (InvalidWindowPositionException $error) {
            if ($this instanceof SmartCommand) {
                $prefix = $this->getPrefix();
            } else if ($this instanceof SubCommand) {
                $prefix = $this->getCommand()->getPrefix();
            }
            $message = WindowLibLoader::getInstance()->getConfigValue('invalid-window-position-message', '', true);
            if ($message != '') {
                $player->sendMessage(($prefix ?? '') . $message);
            }
        }
        return false;
    }

    /**
     * @param Player $player
     * @param CommandArguments $args
     * @return void
     * @throws LevelException|InvalidWindowTypeException|InvalidWindowPositionException
     */
    abstract protected function openWindowTo(Player $player, CommandArguments $args);
}