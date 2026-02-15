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

namespace windowlib\listener;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\inventory\PlayerInventory;
use windowlib\window\inventory\WindowInventory;
use windowlib\window\session\SessionsManager;
use pocketmine\inventory\SimpleTransactionQueue;
use pocketmine\inventory\Transaction;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use Throwable;
use windowlib\window\transaction\BaseWindowTransaction;
use pocketmine\inventory\BaseTransaction;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\UpdateBlockPacket;
use windowlib\window\tile\BlocksSpawnableTile;
use windowlib\window\tile\WindowTile;

final class WindowLibListener implements Listener
{

    /** @var SessionsManager */
    protected $manager;

    public function __construct()
    {
        $this->manager = SessionsManager::getInstance();
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function join(PlayerJoinEvent $event)
    {
        $this->manager->registerPlayerSession($event->getPlayer());
    }

    /**
     * @priority MONITOR
     * @ignoreCancelled TRUE
     */
    public function quit(PlayerQuitEvent $event)
    {
        $this->manager->unregisterPlayerSession($event->getPlayer());
    }

    /**
     * @priority HIGH
     * @ignoreCancelled TRUE
     */
    public function transaction(InventoryTransactionEvent $event)
    {
        $transaction = $event->getTransaction();
        $player = $transaction instanceof SimpleTransactionQueue ? $transaction->getPlayer() : null;
        $windowInventory = null;
        $itemClickedWithItemSelected = null;
        $itemSelected = null;

        foreach ($event->getTransaction()->getTransactions() as $transactionType) {
            /** @var BaseTransaction $transactionType */
            $inventory = $transactionType->getInventory();
            if ($inventory instanceof WindowInventory) {
                $windowInventory = $inventory;
                $change = $transactionType->getChange();
                if (isset($change['in']) && $change['in'] instanceof Item) {
                    $itemSelected = $change['in'];
                } else {
                    $itemSelected = Item::get(Item::AIR);
                }
                
                if (isset($change['out']) && $change['out'] instanceof Item) {
                    $itemClickedWithItemSelected = $change['out'];
                } else {
                    $itemClickedWithItemSelected = Item::get(Item::AIR);
                }
            } else if ($inventory instanceof PlayerInventory && !isset($player)) {
                $player = $inventory->getHolder();
                /** It could happen, cause Human uses PlayerInventory too :) */
                if (!($player instanceof Player)) {
                    $player = null;
                }
            }
        }

        if (isset($itemSelected, $itemClickedWithItemSelected, $player, $windowInventory)) {
            $event->setCancelled(true);
            $libTransaction = new BaseWindowTransaction($player, $windowInventory, $itemClickedWithItemSelected, $itemSelected);
            try {
                $windowInventory->processTransaction($libTransaction);

                $result = $libTransaction->getResult();
                if ($result->canPass()) {
                    $event->setCancelled(false);
                }
            } catch (Throwable $error) {
                Server::getInstance()->getLogger()->error((string) $error);
            }
        }
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function updateBlocks(DataPacketSendEvent $event)
    {
        $packet = $event->getPacket();
        $player = $event->getPlayer();
        if ($packet instanceof UpdateBlockPacket) {
            $playerSession = $this->manager->getPlayerSession($player);
            if ($tile = $playerSession->getCurrentTile()) {
                $position = new Vector3($packet->x, $packet->y, $packet->z);
                if ($tile instanceof BlocksSpawnableTile && !$tile->isAllowedPacket($packet)) {
                    foreach ($tile->getTileBlocksFrom($tile) as $block) {
                        if ($block->equals($position)) {
                            $event->setCancelled(true);
                            break;
                        }
                    } 
                }
            }
        }
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function place(BlockPlaceEvent $event)
    {
        $block = $event->getBlock();
        $tile = $block->getLevel()->getTile($block);
        if ($tile instanceof WindowTile) {
            $event->setCancelled(true);
            if (count($tile->getInventory()->getViewers()) == 0) {
                $tile->close();
            }
        }
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function blockBreak(BlockBreakEvent $event) 
    {
        $block = $event->getBlock();
        if ($block->getLevel()->getTile($event->getBlock()) instanceof WindowTile) {
            $event->setCancelled(true);
        }
    }
    
}