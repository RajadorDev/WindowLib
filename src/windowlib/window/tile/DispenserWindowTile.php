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
use pocketmine\level\format\FullChunk;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Tile;

class DispenserWindowTile extends BlocksSpawnableTile
{

    public static function create(FullChunk $fullChunk, CompoundTag $nbts): WindowTile
    {
        return new self(
            $fullChunk,
            $nbts
        );
    }

    public function getTileBlocksFrom(Vector3 $tilePosition): array
    {
        return [
            Block::get(Block::DISPENSER, 0, Position::fromObject($tilePosition))
        ];
    }

    public function getInventoryTileId(): string
    {
        return Tile::DISPENSER;
    }

}