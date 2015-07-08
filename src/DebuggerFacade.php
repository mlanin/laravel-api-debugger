<?php namespace Lanin\ApiDebugger;

use Illuminate\Support\Facades\Facade;

class DebuggerFacade extends Facade {

	protected static function getFacadeAccessor()
	{
		return Debugger::class;
	}

}