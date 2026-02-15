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

namespace windowlib\window\page;

use Throwable;
use windowlib\utils\WindowLibUtils;
use windowlib\window\transaction\WindowTransaction;
use windowlib\window\transaction\WindowTransactionResult;
use windowlib\WindowLibLoader;

class ClosurePage extends Page
{

    /** @var callable `(WindowTransaction) : WindowTransactionResult` */
    protected $callback;

    /**
     * @param array<int,Item> $items
     * @param callable $callback `(WindowTransaction) : WindowTransactionResult`
     */
    public function __construct(array $items, callable $callback)
    {
        $this->callback = $callback;
        WindowLibUtils::validCallableSignature(
            function (WindowTransaction $transaction) : WindowTransactionResult {
                return WindowTransactionResult::PASS();
            },
            $callback
        );
        parent::__construct($items);
    }

    /**
     * @param array<string,Item> $items
     * @param callable $callback `(WindowTransaction) : WindowTransactionResult`
     * @return ClosurePage
     */
    public static function create(array $items, callable $callback) : ClosurePage
    {
        return new self($items, $callback);
    }

    /**
     * @param array<int,Item> $items
     * @param callable $callback `(WindowTransaction) : void`
     * @return ClosurePage
     */
    public static function readonly(array $items, callable $callback) : ClosurePage
    {
        WindowLibUtils::validCallableSignature(
            function (WindowTransaction $transaction) {},
            $callback
        );
        $callCallback = function (WindowTransaction $transaction) use ($callback) : WindowTransactionResult {
            $callback($transaction);
            return WindowTransactionResult::IGNORE();
        };
        return new ClosurePage($items, $callCallback);
    }

    public function interact(WindowTransaction $transaction)
    {
        $result = WindowTransactionResult::IGNORE();
        try {
            $closure = $this->callback;
            $result = $closure($transaction);
        } catch (Throwable $error) {
            WindowLibLoader::getInstance()->getLogger()->error((string) $error);
        }
        $transaction->setResult($result);
    }
}