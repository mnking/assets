<?php
/**
 * Created by PhpStorm.
 * User: vuong
 * Date: 8/20/14
 * Time: 5:24 PM
 */

namespace Mnking\Assets\Facades;

use Illuminate\Support\Facades\Facade;
class Assets extends Facade{

    protected static function getFacadeAccessor()
    {
        return 'assets';
    }
} 