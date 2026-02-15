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

namespace windowlib\utils;

use pocketmine\item\Item;
use pocketmine\level\Level;
use windowlib\libs\DaveRandom\CallbackValidator\CallbackType;
use windowlib\libs\DaveRandom\CallbackValidator\InvalidCallbackException;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use windowlib\window\session\WindowSession;

class WindowLibUtils 
{

    const TAG_PAGE = 'WindowPage';

    public static function validCallableSignature(callable $structure, callable $argumentGiven)
    {
        $type = CallbackType::createFromCallable($structure);
        if (!$type->isSatisfiedBy($argumentGiven)) {
            throw new InvalidCallbackException("Callback is not compatible with signature given");
        }
    }

    public static function getDefaultCompound(Vector3 $where, string $sessionId) : CompoundTag
    {
        return new CompoundTag('', [
            new IntTag('x', (int) $where->getFloorX()),
            new IntTag('y', (int) $where->getFloorY()),
            new IntTag('z', (int) $where->getFloorZ()),
            new StringTag(WindowSession::TAG_SESSION_ID, $sessionId)
        ]);
    }

    public static function canBeSpawnnedIn(Vector3 $where, Level $world) : bool 
    {
        if ($world->getTile($where)) {
            return false;
        }
        return ($where->y >= 1 && $where->y < 128);
    }

    /**
     * @param mixed[] $contents
     * @param integer $page
     * @param integer $pageLength
     * @return mixed
     */
    public static function pageList(array $contents, int $page, int $pageLength) : array 
    {
        $index = $page <= 1 ? 0 : (($page - 1) * $pageLength);
        return array_slice($contents, $index, $pageLength);
    }
    
    /**
     * @param array<int,Item> $slots
     * @param integer $maxInventorySlot
     * @param Item|null $item If null, the item to fill the window will be Glass Pane
     * @return void
     */
    public static function fillEmptySlots(&$slots, int $maxInventorySlot = 26, Item $item = null)
    {
        $item = Item::get(Item::GLASS_PANE);
        for ($slot = 0; $slot <= $maxInventorySlot; $slot++) {
            if (!isset($slots[$slot]) || $slots[$slot]->getId() == Item::AIR) {
                $slots[$slot] = $item;
            }
        }
    }

    /**
     * @param integer $page
     * @param string $customName value `{page}` will be replaced by the page number
     * @param Item|null $item If null, the item returned will be Arrow
     * @return Item
     */
    public static function setPageItem(int $page, string $customName = '§r§ePage §f{page}', Item $item = null) : Item
    {
        ItemUtils::setInt($item, self::TAG_PAGE, $page);
        $item->setCustomName(str_replace('{page}', (string) $page, $customName));
        return $item;
    }

    public static function getPageFromItem(Item $item) 
    {
        return ItemUtils::getInt($item, self::TAG_PAGE);
    }
    
}