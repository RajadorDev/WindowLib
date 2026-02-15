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

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\nbt\tag\StringTag;

final class ItemUtils 
{

    const TAG_ID = 'id';

    const TAG_META = 'meta';

    const TAG_COUNT = 'count';

    const TAG_NBT = 'nbt_base64';

    /**
     * @param Item $item
     * @return array{id:int,meta:int,count:int,nbt_base64:string}
     */
    public static function serializeItem(Item $item) : array 
    {
        $itemData = [
            self::TAG_ID => $item->getId(),
            self::TAG_META => $item->getDamage(),
            self::TAG_COUNT => $item->getCount(),
        ];
        if ($namedtag = $item->getNamedTag())
        {
            $itemData[self::TAG_NBT] = base64_encode(serialize($namedtag));
        }
        return $itemData;
    }

    /**
     * @param array{id:int,meta:int,count:int,nbt_base64:string} $itemData
     * @return Item
     */
    public static function unserializeItem(array $itemData) : Item
    {
        $id = $itemData[self::TAG_ID];
        $meta = $itemData[self::TAG_META];
        $count = $itemData[self::TAG_COUNT];
        $item = Item::get($id, $meta, $count);
        if (isset($itemData[self::TAG_NBT]))
        {
            $nbt = $itemData[self::TAG_NBT];
            $nbt = unserialize(base64_decode($nbt));
            $item->setNamedTag($nbt);
        }
        return $item;
    }

    public static function glowItem(Item $item) : Item 
	{
		$enchantment = Enchantment::getEnchantment(Enchantment::TYPE_INVALID);
		$item->addEnchantment($enchantment);
		return $item;
	}

    public static function setTag(Item $item, NamedTag $tag) : Item
    {
        $tagIdentifier = $tag->getName();
        $namedtag = $item->getNamedTag() ?? new CompoundTag();
        $namedtag->{$tagIdentifier} = $tag;
        $item->setNamedTag($namedtag);
        return $item;
    }

    /**
     * @param Item $item
     * @param string $name
     * @param class-string<NamedTag> $classType
     * @return mixed
     */
    public static function getTagValue(Item $item, string $name, string $classType = null) 
    {
        if ($namedtag = $item->getNamedTag())
        {
            if (isset($namedtag[$name])) {
                if (is_string($classType))
                {
                    if (get_class($namedtag->{$name}) === $classType) {
                        return $namedtag[$name];
                    }
                    return null;
                }
                return $namedtag[$name];
            }
        }
        return null;
    }

    /**
     * @param Item $item
     * @param string $name
     * @return string|null
     */
    public static function getString(Item $item, string $name)
    {
        return self::getTagValue($item, $name, StringTag::class);
    }

    public static function setString(Item $item, string $name, string $value) : Item
    {
        return self::setTag($item, new StringTag($name, $value));
    }

    /**
     * @param Item $item
     * @param string $name
     * @param boolean|null $mustBeValue
     * @return int|null
     */
    public static function getByte(Item $item, string $name, bool $mustBeValue = null)  
    {
        $value = self::getTagValue($item, $name, ByteTag::class);
        if (!is_null($value)) {
            if (is_null($mustBeValue)) {
                return $value;
            } else if ($value == ((int) $mustBeValue)) {
                return $value;
            }
        }
        return null;
    }

    public static function setByte(Item $item, string $name, bool $setValue = true) : Item
    {
        return self::setTag($item, new ByteTag($name, (int) $setValue));
    }

    /**
     * @param Item $item
     * @param string $name
     * @return int|null
     */
    public static function getInt(Item $item, string $name) 
    {
        return self::getTagValue($item, $name, IntTag::class);
    }

    public static function setInt(Item $item, string $name, int $value) : Item
    {
        return self::setTag($item, new IntTag($name, $value));
    }

}