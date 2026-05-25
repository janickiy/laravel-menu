<?php

namespace Harimayco\Menu\Facades;

use Illuminate\Support\Facades\Facade;

class Menu extends Facade
{
    /**
     * Return facade accessor
     */
    protected static function getFacadeAccessor(): string
    {
        return 'harimayco-menu';
    }
}
