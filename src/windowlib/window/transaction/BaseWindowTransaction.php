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

class BaseWindowTransaction implements WindowTransaction
{

    /** @var Player */
    protected $player;

    /** @var WindowInventory */
    protected $inventory;

    /** @var Item */
    protected $itemClicked, $itemSelected;

    /** @var WindowTransactionResult */
    protected $result;

    public function __construct(
        Player $player,
        WindowInventory $inventory,
        Item $itemClicked,
        Item $itemSelected
    )
    {
        $this->player = $player;
        $this->inventory = $inventory;
        $this->itemClicked = $itemClicked;
        $this->itemSelected = $itemSelected;
        $this->result = WindowTransactionResult::PASS();
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getItemClicked(): Item
    {
        return $this->itemClicked;
    }

    public function getItemSelected() : Item
    {
        return $this->itemSelected;
    }

    public function getWindowInventory(): WindowInventory
    {
        return $this->inventory;
    }

    public function getPlayerInventory(): PlayerInventory
    {
        return $this->player->getInventory();
    }

    public function setResult(WindowTransactionResult $result) : BaseWindowTransaction
    {
        $this->result = $result;
        return $this;
    }

    public function getResult(): WindowTransactionResult
    {
        return $this->result;
    }

}