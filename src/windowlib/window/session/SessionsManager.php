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

use InvalidArgumentException;
use pocketmine\Player;
use SmartCommand\utils\SingletonTrait;
use windowlib\listener\WindowLibListener;
use windowlib\window\session\PlayerSession;
use windowlib\window\session\exception\SessionNotFoundException;
use windowlib\WindowLibLoader;

final class SessionsManager 
{

    use SingletonTrait;

    /** @var array<string,WindowSession> */
    protected $windowSessions = [];

    /** @var array<int,PlayerSession> */
    protected $playerSessions = [];

    public static function init(WindowLibLoader $loader) : self 
    {
        $instance = new self();
        $loader->registerListener(new WindowLibListener);
        return $instance;
    }

    public function __construct()
    {
        self::setInstance($this);
    }

    public function registerWindowSession(WindowSession $session)
    {
        $id = $session->getId();
        if (isset($this->windowSessions[$id])) {
            throw new InvalidArgumentException("Session $id is already registered");
        }
        $this->windowSessions[$id] = $session;
    }

    public function unregisterWindowSession(WindowSession $session)
    {
        unset($this->windowSessions[$session->getId()]);
    }

    /**
     * @param string $id
     * @return WindowSession
     * @throws SessionNotFoundException
     */
    public function getWindowSessionById(string $id) : WindowSession
    {
        if (isset($this->windowSessions[$id])) {
            return $this->windowSessions[$id];
        }
        throw new SessionNotFoundException("Window session $id does not found");
    }

    public function registerPlayerSession(Player $player) : PlayerSession
    {
        $playerId = $player->getLoaderId();
        if (isset($this->playerSessions[$playerId])) {
            throw new InvalidArgumentException("Player $playerId is already registered");
        }
        return $this->playerSessions[$playerId] = new PlayerSession($player);
    }

    public function unregisterPlayerSession(Player $player) 
    {
        $sessionId = $player->getLoaderId();
        if (isset($this->playerSessions[$sessionId])) {
            $this->playerSessions[$sessionId]->despawnCurrentTile();
        }
        unset($this->playerSessions[$sessionId]);
    }

    public function getPlayerSession(Player $player) : PlayerSession
    {
        if (isset($this->playerSessions[$playerId = $player->getLoaderId()])) {
            return $this->playerSessions[$playerId];
        }
        return $this->registerPlayerSession($player);
    }

}