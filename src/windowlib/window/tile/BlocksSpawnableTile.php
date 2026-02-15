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

use Exception;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\tile\Skull;

abstract class BlocksSpawnableTile extends WindowTile
{

    /** @var array<string,bool> */
    protected $allowedPackets = [];

    protected function sendAditionalSpawnPackets(Player $player)
    {
        parent::sendAditionalSpawnPackets($player);
        $this->sendBlockUpdatePackets($player, false);
    }

    public function sendDespawnPackets(Player $player)
    {
        parent::sendDespawnPackets($player);
        $this->sendBlockUpdatePackets($player, true);
    }

    public function sendBlockUpdatePackets(Player $player, bool $clearMode) 
    {
        foreach ($this->generateBlockUpdatePackets($clearMode) as $packet) {
            $player->dataPacket($packet);
        }
    }

    /**
     * @param boolean $clearMode If true, will update with the real world block in blocks positions
     * @return array
     */
    protected function generateBlockUpdatePackets(bool $clearMode) : array {
        $packets = [];
        foreach ($this->getTileBlocksFrom(new Vector3($this->x, $this->y, $this->z)) as $block) {
            $packet = new UpdateBlockPacket();
            $blockPosition = new Vector3($block->x, $block->y, $block->z);
            if ($clearMode) {
                $block = $this->level->getBlock($blockPosition);
            }
            $packet->x = $blockPosition->x;
            $packet->y = $blockPosition->y;
            $packet->z = $blockPosition->z;
            $packet->blockId = $block->getId();
            $packet->blockData = $block->getDamage();
            $packet->flags = UpdateBlockPacket::FLAG_PRIORITY;
            $this->allowedPackets[spl_object_hash($packet)] = true;
            $packets[] = $packet;
        }
        return $packets;
    }

    public function isAllowedPacket(DataPacket $packet) : bool 
    {
        return ($this->allowedPackets[spl_object_hash($packet)] ?? false);
    }

    /**
     * @param Vector3 $tilePosition
     * @return Block[]
     */
    abstract public function getTileBlocksFrom(Vector3 $tilePosition) : array;
}