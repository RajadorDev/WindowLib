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

namespace windowlib\window\session;

use Exception;
use pocketmine\Player;
use pocketmine\Server;
use windowlib\window\tile\WindowTile;
use pocketmine\tile\Chest;
use windowlib\task\ClosureTask;

class PlayerSession
{

    /** @var int */
    private $loaderId;

    /** @var Player */
    protected $player;

    /** @var int|null */
    protected $windowSessionId = null;

    /** @var WindowTile|null */
    protected $currentTile = null;

    /** @var WindowSession|null */
    protected $windowSession = null;

    /** @var ClosureTask|null */
    protected $inventoryDelayTask = null;

    public function __construct(
        Player $player
    )
    {
        $this->player = $player;
        $this->loaderId = $player->getLoaderId();
    }

    final public function getId() : int 
    {
        return $this->loaderId;
    }

    public function getPlayer() : Player
    {
        return $this->player;
    }

    public function getCurrentTile()
    {
        return $this->currentTile;
    }

    public function despawnCurrentTile() 
    {
        if (isset($this->currentTile)) {
            if (!$this->currentTile->closed) {
                $this->currentTile->sendDespawnPackets($this->getPlayer());
                $this->currentTile->close();
            }
            $this->currentTile = null;
        }
        $this->cancelOpenInventoryDelayTask();
    }

    public function closeCurrentSession()
    {
        $this->despawnCurrentTile();
        if ($this->windowSession) {
            $this->windowSession->closeTo($this->getPlayer());
            $this->windowSession = null;
        }
        $this->cancelOpenInventoryDelayTask();
    }

    public function openCurrentSession() : bool 
    {
        if ($this->windowSession) {
            if ($this->windowSession->isDestroyed()) {
                $this->closeCurrentSession();
                return false;
            }
            $this->internalOpenSession($this->windowSession);
            return true;
        }
        return false;
    }

    public function open(WindowSession $session)
    {
        if ($this->windowSession) {
            if (!$this->windowSession->isDestroyed())
            {
                if ($this->windowSession === $session) {
                    $this->openCurrentSession();
                    return;
                } else {
                    $this->closeCurrentSession();
                }
            }
        }

        $this->windowSession = $session;
        $this->internalOpenSession($session);
    }

    protected function internalOpenSession(WindowSession $session)
    {
        $this->despawnCurrentTile(); 
        $this->currentTile = $session->createTile($this->getPlayer()->getPosition());
        $tile = $this->currentTile;
        $tile->spawnTo($this->getPlayer());
        $delayTicks = $this->currentTile->getInventoryOpenDelay();
        if ($delayTicks == 0) {
            $this->getPlayer()->addWindow($session->getInventory());
        } else {
            $this->inventoryDelayTask = ClosureTask::scheduleDelayed(
                $delayTicks,
                function () use ($tile) {
                    $this->inventoryDelayTask = null;
                    if ($this->currentTile === $tile && $this->player instanceof Player && $this->player->isOnline() && $this->windowSession instanceof WindowSession) {
                        $this->getPlayer()->addWindow($this->windowSession->getInventory());
                    }
                }
            );
        }
    }

    public function cancelOpenInventoryDelayTask()
    {
        if ($this->inventoryDelayTask) {
            Server::getInstance()->getScheduler()->cancelTask($this->inventoryDelayTask->getTaskId());
            $this->inventoryDelayTask = null;
        }
    }

    public function __destruct()
    {
        unset(
            $this->currentTile,
            $this->windowSession
        );
    }
    
}