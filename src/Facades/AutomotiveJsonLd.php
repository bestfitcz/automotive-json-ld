<?php

namespace Bestfitcz\AutomotiveJsonLd\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bestfitcz\AutomotiveJsonLd\AutomotiveJsonLd
 */
class AutomotiveJsonLd extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Bestfitcz\AutomotiveJsonLd\AutomotiveJsonLd::class;
    }
}
