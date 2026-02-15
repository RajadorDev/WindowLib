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

namespace windowlib\window\transaction;

use pocketmine\inventory\PlayerInventory;
use pocketmine\item\Item;
use pocketmine\Player;
use windowlib\window\inventory\WindowInventory;

interface WindowTransaction 
{

    /**
     * @return Player
     */
    public function getPlayer() : Player;

    /**
     * @return Item
     */
    public function getItemClicked() : Item;

    /**
     * @return Item
     */
    public function getItemSelected() : Item;
    
    /**
     * @return WindowInventory
     */
    public function getWindowInventory() : WindowInventory;

    /**
     * @return PlayerInventory
     */
    public function getPlayerInventory() : PlayerInventory;

    /**
     * @return WindowTransactionResult
     */
    public function getResult() : WindowTransactionResult;

    /**
     * @param WindowTransactionResult $result
     * @return BaseWindowTransaction
     */
    public function setResult(WindowTransactionResult $result) : BaseWindowTransaction;

}