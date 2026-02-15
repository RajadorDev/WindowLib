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

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Chest;

trait NameableTileTrait 
{

    /** @var string */
    protected $customName = 'Window';

    public function getName() : string 
    {
        return $this->customName;
    }

    public function setName(string $newName) : NameableTile
    {
        $this->customName = $newName;
        return $this;
    }

    public function getSpawnCompound() 
    {
        $nbt = parent::getSpawnCompound();
        return $this->putNameInNBT($nbt);
    }

    public function putNameInNBT(CompoundTag $nbt) : CompoundTag
    {
        $nbt->CustomName = new StringTag('CustomName', $this->getName());
        return $nbt;
    }

}