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

namespace windowlib\command\test;

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\utils\AdminPermissionTrait;
use windowlib\command\OpenWindowCommandTrait;
use windowlib\window\page\ClosurePage;
use windowlib\window\transaction\WindowTransaction;
use windowlib\window\transaction\WindowTransactionResult;
use windowlib\window\WindowMenuList;

final class TestWindowCommand extends SmartCommand
{

    use AdminPermissionTrait, OpenWindowCommandTrait;

    protected function prepare()
    {
        $this->registerRule(new OnlyInGameCommandRule);
    }

    protected function openWindowTo(Player $player, CommandArguments $args)
    {
        $window = WindowMenuList::createSession(WindowMenuList::TYPE_CHEST, '§cTeste');
        $window->getInventory()->setPage(
            ClosurePage::readonly(
                [
                    0 => Item::get(Item::IRON_SWORD)->setCustomName('§r§eVoltar para o lobby')
                ],
                static function (WindowTransaction $transaction) use ($player) {
                    $transaction->getPlayer()->teleport(Server::getInstance()->getDefaultLevel()->getSpawnLocation());
                    $player->sendMessage('§7Você foi para o lobby');
                }
            )
        );
        $window->openTo($player);
    }
}