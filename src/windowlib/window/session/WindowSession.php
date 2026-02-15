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

namespace windowlib\window\session;

use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\InventoryType;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\LevelException;
use Throwable;
use windowlib\utils\WindowLibUtils;
use windowlib\window\exception\CustomNameNotSupportedException;
use windowlib\window\exception\InvalidWindowPositionException;
use windowlib\window\inventory\WindowInventory;
use windowlib\window\session\PlayerSession;
use windowlib\window\session\exception\SessionNotFoundException;
use windowlib\window\tile\NameableTile;
use windowlib\window\tile\WindowTile;
use windowlib\WindowLibLoader;

class WindowSession implements InventoryHolder
{

    const TAG_SESSION_ID = 'WindowLibSession';

    /** @var int */
    protected static $sessionsRuntimeId = 0;

    /** @var string */
    private $uuid;

    /** @var WindowInventory */
    protected $inventory;

    /** @var class-string<WindowTile> */
    protected $windowTileClass;

    /** @var string|null */
    protected $inventoryName = null;

    /** @var array<int,PlayerSession> */
    protected $players = [];

    /** @var boolean */
    protected $isRegistered = false;

    /** @var callable|null `(Player) : void` */
    protected $closeListener = null;

    public static function generateNewSessionId() : string 
    {
        $runtimeId = self::$sessionsRuntimeId++;
        $microtime = microtime();
        return $runtimeId . '-' . $microtime;
    }

    /**
     * @param class-string<WindowInventory> $inventoryClass
     * @param InventoryType $inventoryType
     * @param class-string<WindowTile> $windowTileClass
     */
    public function __construct(string $inventoryClass, InventoryType $inventoryType, string $windowTileClass)
    {
        $this->windowTileClass = $windowTileClass;
        $this->inventory = $inventoryClass::create($this, $inventoryType);
        $this->uuid = self::generateNewSessionId();
    }

    final public function getId() : string 
    {
        return $this->uuid;
    }

    public function isDestroyed() : bool 
    {
        try {
            SessionsManager::getInstance()->getWindowSessionById($this->getId());
            return true;
        } catch (SessionNotFoundException $error) {
            return false;
        }
    }

    /**
     * @param string $newName
     * @return WindowSession
     * @throws CustomNameNotSupportedException
     */
    public function setName(string $newName) : WindowSession 
    {
        if (!is_subclass_of($this->windowTileClass, NameableTile::class)) {
            throw new CustomNameNotSupportedException("Window tile {$this->windowTileClass} does not support custom names");
        }
        $this->inventoryName = $newName;
        return $this;
    }

    /**
     * @return WindowInventory
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @param callable $callback `(Player) : void`
     * @return WindowSession
     */
    public function setCloseListener(callable $callback) : WindowSession
    {
        WindowLibUtils::validCallableSignature(
            function (Player $player) {},
            $callback
        );
        $this->closeListener = $callback;
        return $this;
    }

    /**
     * @param Position $from
     * @return WindowTile
     * @throws LevelException|InvalidWindowPositionException
     */
    public function createTile(Position $from) : WindowTile
    {
        $floorPos = $from->floor();
        $world = $from->getLevel();
        $position = $floorPos->subtract(0, 2);
        if (!WindowLibUtils::canBeSpawnnedIn($position, $world)) {
            $position = $floorPos->add(0, 3);
        }

        if (!$from->isValid()) {
            throw new LevelException("Can't create a tile: world can't be null");
        }

        if (!WindowLibUtils::canBeSpawnnedIn($position, $world)) {
            throw new InvalidWindowPositionException("Position {$position->__toString()} is invalid");
        }
        $chunk = $world->getChunk($floorPos->x >> 4, $floorPos->z >> 4);
        $compoundTag = WindowLibUtils::getDefaultCompound($position, $this->getId());
        $windowTileClass = $this->windowTileClass;
        $tile = $windowTileClass::create($chunk, $compoundTag);
        if ($this->inventoryName) {
            $tile->setName($this->inventoryName);
        }
        return $tile;
    }

    public function createTileToPlayerSession(PlayerSession $session) : WindowTile
    {
        return $this->createTile($session->getPlayer()->getPosition());
    }

    /**
     * @param Player $player
     * @return void
     * @throws InvalidWindowPositionException|LevelException
     */
    public function openTo(Player $player) 
    {
        if (!$this->isRegistered) {
            SessionsManager::getInstance()->registerWindowSession($this);
            $this->isRegistered = true;
        }
        $playerSession = SessionsManager::getInstance()->getPlayerSession($player);
        $playerSession->open($this);
        $this->players[$player->getLoaderId()] = $playerSession;
    }

    public function closeTo(Player $player)
    {
        $loaderId = $player->getLoaderId();
        if (isset($this->players[$loaderId])) {
            unset($this->players[$loaderId]);
            if (!$this->isDestroyed() && count($this->players) == 0) {
                $this->destroy();
            }
        }

        $this->onWindowInventoryClose($player);
    }

    public function onWindowInventoryClose(Player $player)
    {
        try {
            if ($this->closeListener) {
                ($this->closeListener)($player);
            }
        } catch (Throwable $error) {
            WindowLibLoader::getInstance()->getLogger()->error((string) $error);
        }
    }

    public function destroy()
    {
        $this->getInventory()->closeForAll();
        SessionsManager::getInstance()->unregisterWindowSession($this);
    }

    public function __destruct()
    {
        unset(
            $this->inventory,
            $this->windowTileClass
        );
    }

}