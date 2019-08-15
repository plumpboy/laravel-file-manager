<?php

namespace PlumpBoy\FileManager;

use Illuminate\Support\Facades\Facade;

class FileManagerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
    	return 'file-manager';
    }
}