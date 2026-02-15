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

namespace windowlib\window\inventory;

use Exception;
use pocketmine\inventory\BaseInventory;
use pocketmine\inventory\ChestInventory;
use pocketmine\inventory\ContainerInventory;
use pocketmine\inventory\InventoryType;
use pocketmine\network\protocol\ContainerOpenPacket;
use pocketmine\Player;
use windowlib\window\page\Page;
use windowlib\window\session\SessionsManager;
use windowlib\window\session\WindowSession;
use windowlib\window\tile\WindowTile;
use windowlib\window\transaction\WindowTransaction;
use windowlib\window\transaction\WindowTransactionResult;

class WindowInventory extends ContainerInventory
{
    
    /** @var Page|null */
    protected $page = null;

    public static function create(WindowSession $session, InventoryType $type) : WindowInventory
    {
        return new self(
            $session,
            $type
        );
    }

    public function setPage(Page $page, bool $sendContents = true) : WindowInventory
    {
        $this->page = $page;
        $page->updateInventory($this, $sendContents);
        return $this;
    }

    /**
     * @return WindowSession
     */
    public function getHolder()
    {
        return parent::getHolder();
    }

    public function getPage()
    {
        return $this->page;
    }

    public function closeForAll()
    {
        foreach ($this->getViewers() as $player) {
            $player->removeWindow($this);
        }
    }

    public function onOpen(Player $who)
    {
        BaseInventory::onOpen($who);
        $playerSession = SessionsManager::getInstance()->getPlayerSession($who);
        if ($currentTile = $playerSession->getCurrentTile()) {
            if ($currentTile->getInventory() === $this) {
                $packet = new ContainerOpenPacket();
                $packet->windowid = $who->getWindowId($this);
                $packet->x = $currentTile->x;
                $packet->y = $currentTile->y;
                $packet->z = $currentTile->z;
                $packet->type = $this->getType()->getNetworkType();
                $packet->slots = $this->getSize();
                $who->dataPacket($packet);
                $this->sendContents($who);
                if ($this->page) {
                    $this->page->onRenderize($who);
                }
            }
        }
    }

    public function onClose(Player $who)
    {
        if ($this->page) {
            $this->page->onClose($who);
        }
        SessionsManager::getInstance()->getPlayerSession($who)->closeCurrentSession();
        parent::onClose($who);
    }

    public function processTransaction(WindowTransaction $transaction) 
    {
        if ($this->page) {
            $this->page->interact($transaction);
        } else {
            $transaction->setResult(WindowTransactionResult::IGNORE());
        }
    }
    
}