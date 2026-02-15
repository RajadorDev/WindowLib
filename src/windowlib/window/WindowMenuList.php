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

namespace windowlib\window;

use pocketmine\inventory\InventoryType;
use pocketmine\tile\Tile;
use RuntimeException;
use windowlib\window\exception\InvalidWindowTypeException;
use windowlib\window\inventory\type\WindowInventoryTypes;
use windowlib\window\inventory\WindowInventory;
use windowlib\window\session\SessionsManager;
use windowlib\window\session\WindowSession;
use windowlib\window\tile\ChestWindowTile;
use windowlib\window\exception\InvalidWindowPositionException;
use windowlib\window\tile\DispenserWindowTile;
use windowlib\window\tile\HopperWindowTile;
use windowlib\window\exception\CustomNameNotSupportedException;
use windowlib\window\tile\DoubleChestWindowTile;

class WindowMenuList 
{

    const TYPE_CHEST = 1;

    const TYPE_DOUBLE_CHEST = 2;

    const TYPE_HOPPER = 3;

    const TYPE_DISPENSER = 4;

    const TYPES_TILES = [
        self::TYPE_CHEST => ChestWindowTile::class,
        self::TYPE_DOUBLE_CHEST => DoubleChestWindowTile::class,
        self::TYPE_HOPPER => HopperWindowTile::class,
        self::TYPE_DISPENSER => DispenserWindowTile::class
    ];

    const TYPES_INVENTORY_NAME = [
        self::TYPE_CHEST => 'CHEST',
        self::TYPE_DOUBLE_CHEST => 'DOUBLE_CHEST',
        self::TYPE_HOPPER => 'HOPPER',
        self::TYPE_DISPENSER => 'DISPENSER'
    ];

    const TYPES_INVENTORY_CLASS = [
        
    ];

    /** @var boolean */
    private static $registered = false;

    public static function register()
    {
        if (self::$registered) {
            throw new RuntimeException("WindowMenuList is already initialized");
        }

        foreach (self::TYPES_TILES as $type) {
            Tile::registerTile($type);
        }
        self::$registered = true;
    }

    /**
     * @param integer $type
     * @param string|null $customName
     * @return WindowSession
     * @throws InvalidWindowTypeException|InvalidWindowPositionException|CustomNameNotSupportedException
     */
    public static function createSession(int $type, string $customName = null) : WindowSession
    {
        if (isset(self::TYPES_TILES[$type])) {
            $tile = self::TYPES_TILES[$type];
            $inventoryFunctionName = self::TYPES_INVENTORY_NAME[$type];
            $inventoryType = WindowInventoryTypes::{$inventoryFunctionName}();
            $session = new WindowSession(
                self::TYPES_INVENTORY_CLASS[$type] ?? WindowInventory::class,
                $inventoryType,
                $tile
            );

            if ($customName) {
                $session->setName($customName);
            }
            return $session;
        } else {
            throw new InvalidWindowTypeException("Window with id $type does not exists");
        }
    }
}