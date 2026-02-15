# WindowLib 🧩

WindowLib is a library to create windows in a safe, optimized, and faster way for developers.

By default, it supports the following windows:

- `Chest`
- `Double Chest`
- `Hopper`
- `Dispenser`

## Community

**Discord:**

<a href="https://discord.gg/HkfMbBN2AD"><img src="https://img.shields.io/discord/982037265075302551?label=discord&color=7289DA&logo=discord" alt="Discord"></a>

With it, you can create preloaded pages with items and send them to the player whenever you want.

## Requirements 📏

To run on your server, you must have the `PocketMine 2.0.0` API (0.14/15).

You must also have the following plugins installed:

- SmartCommand PM2: https://github.com/RajadorDev/SmartCommand/tree/pm-2.0.0  
- AutoPluginUpdater: https://github.com/RajadorDev/AutoPluginUpdater  

## How to Use 📑

- First, add it as a dependency in your `plugin.yml`:

```yml
depend: [WindowLib]
```

* Now, in your code, when you want to create a window and send it to a player:

```php
<?php

use windowlib\window\WindowMenuList;
use windowlib\window\page\Page;
use windowlib\window\page\ClosurePage;
use pocketmine\item\Item;
use pocketmine\Player;
use windowlib\window\exception\InvalidWindowPositionException;
use windowlib\window\transaction\WindowTransaction;
use windowlib\window\transaction\WindowTransactionResult;

/** Creating a window */
$window = WindowMenuList::createSession(
    WindowMenuList::TYPE_CHEST, /** @see WindowMenuList for more window types */
    'My Custom Name' /** It will throw an exception if the given window type does not support custom names */
);

/**
 * Setting the window page
 * @see Page
 * @see ClosurePage
 */
$window->getInventory()->setPage(
    // Here I will use a closure page
    ClosurePage::create(
        [
            // slot => Item
            0 => Item::get(Item::DIAMOND)->setCustomName('§eMy item')
        ],
        function (WindowTransaction $transaction) : WindowTransactionResult {
            /** The player who interacted */
            $player = $transaction->getPlayer();
            
            /** The item the player clicked */
            $item = $transaction->getItemClicked();

            // Do whatever you want here

            return WindowTransactionResult::IGNORE(); 
            // This keeps the item in the window inventory.
            // You can also use WindowTransactionResult::PASS()
        }
    )
);

/** 
 * Now I am opening the window to the desired player.
 * The player might be in an invalid position (a position with a registered tile),
 * so it is important to catch InvalidWindowPositionException.
 */
try {
    $window->openTo($player);
} catch (InvalidWindowPositionException $error) {
    $player->sendMessage('Sorry, I can\'t open the window here :(');
}

/** 
 * You can also handle when the player closes the window:
 * @see Page::setCloseListener
 */
$window->getInventory()->getPage()->setCloseListener(
    function (Player $player) {
        $player->sendMessage('You closed the window');
    }
);
```