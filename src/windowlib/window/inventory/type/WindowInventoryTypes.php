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

namespace windowlib\window\inventory\type;

use pocketmine\inventory\InventoryType;

class WindowInventoryTypes 
{

    public static function CHEST() : InventoryType
    {
        return InventoryType::get(InventoryType::CHEST);
    }

    public static function DOUBLE_CHEST() : InventoryType
    {
        return InventoryType::get(InventoryType::DOUBLE_CHEST);
    }

    public static function HOPPER() : InventoryType
    {
        return InventoryType::get(InventoryType::HOPPER);
    }

    public static function DISPENSER() : InventoryType
    {
        return InventoryType::get(InventoryType::DISPENSER);
    }
}