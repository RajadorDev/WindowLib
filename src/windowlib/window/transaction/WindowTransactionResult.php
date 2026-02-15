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

namespace windowlib\window\transaction;

class WindowTransactionResult 
{

    public static function PASS() : WindowTransactionResult
    {
        return new self(true);
    }

    public static function IGNORE() : WindowTransactionResult
    {
        return new self(false);
    }

    /** @var boolean */
    protected $pass;

    public function __construct(bool $pass)
    {
        $this->pass = $pass;
    }

    public function canPass() : bool 
    {
        return $this->pass;
    }
}