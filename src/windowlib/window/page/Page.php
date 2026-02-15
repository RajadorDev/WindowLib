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

namespace windowlib\window\page;

use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryType;
use pocketmine\Player;
use pocketmine\Server;
use Throwable;
use windowlib\window\inventory\WindowInventory;
use windowlib\window\session\SessionsManager;
use windowlib\window\transaction\WindowTransaction;
use windowlib\WindowLibLoader;

abstract class Page
{

    /** @var string|null */
    protected $popup = null;

    /** @var array<int,Item> */
    protected $items = [];

    /** @var callable|null `(Player) : void` */
    protected $closeListener = null;

    /** @var array<int,int> */
    protected $scheduledPopup = [];

    /**
     * @param Item[]|array<int,Item> $items
     */
    public function __construct(
        array $items
    )
    {
        $this->items = $items;
    }

    /**
     * @return Item[]
     */
    public function getItems() : array 
    {
        return $this->items;
    }

    public function updateInventory(WindowInventory $inventory, bool $send = true) : Page
    {
        $inventory->setContents($this->items, $send);
        if ($send) {
            foreach ($inventory->getViewers() as $viewer) {
                $this->onRenderize($viewer);
            }
        }
        return $this;
    }

    public function setPopup(string $newText) : Page
    {
        $this->popup = $newText;
        return $this;
    }

    public function hasPopup() : bool 
    {
        return is_string($this->popup);
    }

    public function sendPopup(Player $player) : bool
    {
        if ($this->popup) {
            $player->sendPopup($this->popup);
            return true;
        }
        return false;
    }

    /**
     * @param callable $callback `(Player) : void`
     * @return Page
     */
    public function setCloseListener(callable $callback) : Page
    {
        $this->closeListener = $callback;
        return $this;
    }

    public function onClose(Player $player)
    {
        try {
            unset($this->scheduledPopup[$player->getLoaderId()]);
            if ($this->closeListener) {
                ($this->closeListener)($player);
            }
        } catch (Throwable $error) {
            WindowLibLoader::getInstance()->getLogger()->error((string) $error);
        }
    }

    public function sendScheduledPopup(Player $player, int $ticks = 20) : bool 
    {
        $loaderId = $player->getLoaderId();
        if (isset($this->scheduledPopup[$loaderId])) {
            $time = $this->scheduledPopup[$loaderId] -= 1;
            if ($time <= 0) {
                $this->sendPopup($player);
                $this->scheduledPopup[$loaderId] = $ticks;
                return true;
            }
            return false;
        }

        $this->scheduledPopup[$loaderId] = $ticks;
        $this->sendPopup($player);
        return true;
    }

    /**
     * Called when the player see the page
     * @param Player $player
     * @return void
     */
    public function onRenderize(Player $player) {
        $tile = SessionsManager::getInstance()->getPlayerSession($player)->getCurrentTile();
        if ($tile) {
            if ($this->hasPopup()) {
                $tile->scheduleUpdate();
            }
        }
    }

    /**
     * @param WindowTransaction $transaction
     * @return void
     */
    abstract public function interact(WindowTransaction $transaction);
}