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

namespace windowlib;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use rajadordev\autoupdater\api\CheckUpdateScheduler;
use rajadordev\autoupdater\api\plugin\defaults\github\GitHubPluginUpdaterAPI;
use rajadordev\autoupdater\api\PluginUpdaterChecker;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\DefaultMessages;
use SmartCommand\utils\SingletonTrait;
use windowlib\command\test\TestWindowCommand;
use windowlib\window\session\SessionsManager;
use windowlib\window\WindowMenuList;

class WindowLibLoader extends PluginBase
{

    use SingletonTrait;

    /** @var boolean */
    private static $developerMode = false;

    public static function setDeveloperModeEnabled(bool $set)
    {
        self::$developerMode = $set;
    }

    public static function isDeveloperModeEnabled() : bool 
    {
        return self::$developerMode;
    }

    public function onLoad()
    {
        self::setInstance($this);
    }

    public function onEnable()
    {
        $this->saveResource('config.yml');
        self::setDeveloperModeEnabled(
            $this->getConfigValue('developer-mode', false, true)
        );

        $isDeveloperMode = self::isDeveloperModeEnabled();
        
        if ($isDeveloperMode) {
            SmartCommandAPI::register('windowlib', new TestWindowCommand('testwindow', 'Testar o sistema de window', SmartCommand::DEFAULT_USAGE_PREFIX, [], DefaultMessages::PORTUGUESE()));
        }
        
        WindowMenuList::register();
        SessionsManager::init($this);

        CheckUpdateScheduler::getInstance()->schedule(
            PluginUpdaterChecker::create(
                $this,
                GitHubPluginUpdaterAPI::createFromPlugin(
                    $this,
                    'RajadorDev',
                    'WindowLib'
                )
            )
        );
    }

    public function registerListener(Listener $listener)
    {
        Server::getInstance()->getPluginManager()->registerEvents(
            $listener,
            $this
        );
    }

    /**
     * @param string $id
     * @param mixed $default
     * @param boolean $warnConsole
     * @return mixed
     */
    public function getConfigValue(string $id, $default = null, bool $warnConsole = true)
    {
        $config = $this->getConfig();
        if ($config->exists($id)) {
            return $config->get($id);
        } else if ($warnConsole) {
            $this->getLogger()->warning("Setting with id $id does not found");
        }
        return $default;
    }

}