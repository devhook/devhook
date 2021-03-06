<?php namespace Devhook\Facades;

use \Illuminate\Support\Facades\Facade;

class AdminUIFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
    	return 'adminUI';
    }

}