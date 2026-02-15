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
use pocketmine\nbt\tag\IntTag;
use pocketmine\tile\Tile;

class DoubleChestWindowTile extends ChestWindowTile 
{

    public static function create(FullChunk $fullChunk, CompoundTag $nbts): WindowTile
    {
        return new self($fullChunk, $nbts);
    }

    public function getInventoryTileId(): string
    {
        return Tile::CHEST;
    }

    public function getPairedPos() : Vector3
    {
        return $this->add(1);
    }

    public function getSpawnCompound()
    {
        $nbt = parent::getSpawnCompound();
        $pairedPos = $this->getPairedPos();
        $nbt->pairx = new IntTag('pairx', $pairedPos->x);
        $nbt->pairz = new IntTag('pairz', $pairedPos->z);
        return $nbt;
    }

    public function getTileBlocksFrom(Vector3 $tilePosition): array
    {
        $blocks = parent::getTileBlocksFrom($tilePosition);
        $blocks[] = Block::get(Block::CHEST, 0, Position::fromObject($this->getPairedPos()));
        return $blocks;
    }

    public function getInventoryOpenDelay(): int
    {
        return 5;
    }
    
}