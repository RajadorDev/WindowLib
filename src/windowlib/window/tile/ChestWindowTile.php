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

use pocketmine\block\Block;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\inventory\InventoryType;
use pocketmine\level\format\FullChunk;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\tile\Tile;
use windowlib\utils\WindowLibUtils;
use windowlib\window\page\Page;
use windowlib\window\session\WindowSession;

class ChestWindowTile extends BlocksSpawnableTile implements NameableTile
{

    use NameableTileTrait;

    public static function create(FullChunk $fullChunk, CompoundTag $nbts): WindowTile
    {
        return new self($fullChunk, $nbts);
    }

    public function getInventoryTileId(): string
    {
        return Tile::CHEST;
    }

    public function getTileBlocksFrom(Vector3 $tilePosition): array
    {
        return [
            Block::get(Block::CHEST)->setComponents(
                $tilePosition->x,
                $tilePosition->y,
                $tilePosition->z
            )
        ];
    }

}