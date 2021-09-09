<?php

namespace AdamGaskins\Deployed;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AdamGaskins\Deployed\Deployed
 */
class DeployedFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'skeleton';
    }
}
