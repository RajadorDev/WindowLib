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

use pocketmine\math\Vector3;


final class PosUtils 
{

    const TAG_WORLD = 'world_name';

    /**
     * @param Vector3 $pos
     * @return array{x:int|float,y:int|float,z:int|float}
     */
    public static function serializeVector3(Vector3 $pos) : array 
    {
        return [
            'x' => $pos->x,
            'y' => $pos->y,
            'z' => $pos->z
        ];
    }

    /**
     * @param array{x:int|float,y:int|float,z:int|float} $posData
     * @return Vector3
     */
    public static function unserializeVector3(array $posData) : Vector3
    {
        return new Vector3(
            $posData['x'],
            $posData['y'],
            $posData['z']
        );
    }

    /**
     * Write the world name inside the array given
     *
     * @param array $data
     * @return array
     */
    public static function writeWorldName(array $data, string $worldName) : array 
    {
        $data[self::TAG_WORLD] = $worldName;
        return $data;
    }

    /**
     * Extract the world name writed by writeWorldName() method
     *
     * @param array $data
     * @return string
     */
    public static function extractWorldName(array $data) : string 
    {
        return $data[self::TAG_WORLD];
    }

    /**
     * @param Vector3 $posA
     * @param Vector3 $posB
     * @return array{0:Vector3,1:Vector3}
     */
    public static function orderPositions(Vector3 $posA, Vector3 $posB) : array 
    {
        $minX = min($posA->x, $posB->x);
        $maxX = max($posA->x, $posB->x);
        $minY = min($posA->y, $posB->y);
        $maxY = max($posA->y, $posB->y);
        $minZ = min($posA->z, $posB->z);
        $maxZ = max($posA->z, $posB->z);
        return [
            new Vector3($minX, $minY, $minZ),
            new Vector3($maxX, $maxY, $maxZ)
        ];
    }

    /**
     * @param Vector3 $pos
     * @return string
     */
    public static function hash(Vector3 $pos) : string 
    {
        return "$pos->x:$pos->y:$pos->z";
    }

}