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

namespace windowlib\window\tile;

use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\InventoryType;
use pocketmine\item\Item;
use pocketmine\level\format\FullChunk;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Container;
use pocketmine\tile\Spawnable;
use windowlib\window\inventory\WindowInventory;
use windowlib\window\page\Page;
use windowlib\window\session\exception\SessionNotFoundException;
use windowlib\window\session\SessionsManager;
use windowlib\window\session\WindowSession;
use pocketmine\tile\Chest;

abstract class WindowTile extends Spawnable implements Container, InventoryHolder
{

    /** @var WindowSession */
    protected $session;

    public function __construct(FullChunk $chunk, CompoundTag $nbt)
    {
        parent::__construct($chunk, $nbt);
        $sessionId = $nbt[WindowSession::TAG_SESSION_ID];
        try {
            $this->session = SessionsManager::getInstance()->getWindowSessionById($sessionId);
        } catch (SessionNotFoundException $error) {
            $this->close();
        }
    }


    /**
     * @param FullChunk $fullChunk
     * @param CompoundTag $nbts
     * @return WindowTile
     */
    abstract public static function create(FullChunk $fullChunk, CompoundTag $nbts) : WindowTile;

    /**
     * @return string
     */
    abstract public function getInventoryTileId() : string;

    /**
     * @return InventoryType
     */
    public function getInventoryType() : InventoryType
    {
        return $this->getInventory()->getType();
    }

    public function getItem($index)
    {
        return $this->getInventory()->getItem($index);
    }

    public function setItem($index, Item $item)
    {
        $this->getInventory()->setItem($index, $item);
    }

    public function getSize()
    {
        return $this->getInventory()->getSize();
    }

    /**
     * @return WindowInventory
     */
    public function getInventory()
    {
        return $this->session->getInventory();
    }

    public function canSpawnTo(Player $player) : bool 
    {
        return SessionsManager::getInstance()->getPlayerSession($player)->getCurrentTile() === $this;
    }

    public function spawnTo(Player $player)
    {
        if ($this->canSpawnTo($player) && !$this->closed) {
            $this->sendAditionalSpawnPackets($player);
            parent::spawnTo($player);
            return true;
        }
        return false;
    }

    protected function sendAditionalSpawnPackets(Player $player)
    {}

    public function onUpdate()
    {
        if ($this->closed) {
            return false;
        }

        $page = $this->getInventory()->getPage();
        if ($page instanceof Page && $page->hasPopup()) {
            foreach ($this->getInventory()->getViewers() as $viewer) {
                $page->sendScheduledPopup($viewer);
            }
            return true;
        }
        return false;
    }

    public function sendDespawnPackets(Player $player) {}

    public function getSpawnCompound()
    {
        $compoundTag = new CompoundTag(
            '',
            [
                new StringTag('id', $this->getInventoryTileId()),
                new IntTag('x', (int) $this->x),
                new IntTag('y', (int) $this->y),
                new IntTag('z', (int) $this->z)
            ]
        );
        return $compoundTag;
    }

    /**
     * @return integer Delay to open inventory after spawn the tile
     */
    public function getInventoryOpenDelay() : int 
    {
        return 0;
    }

}