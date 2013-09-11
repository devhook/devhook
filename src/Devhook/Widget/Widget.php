<?php namespace Devhook;

use View, Session;

class Widget {

	//--------------------------------------------------------------------------

	public static function alerts()
	{
		if (Session::get('errors')) {
			return self::view('alerts');
		}
	}

	//--------------------------------------------------------------------------

	public static function breadcrumbs($data)
	{
		if ($data) {
			return self::view('breadcrumbs', array('data'=>$data));
		}
	}

	//--------------------------------------------------------------------------

	public static function __callStatic($name, $args)
	{
		$data = array('data'=>count($args) ? $args[0] : null);

		return self::view($name, $data);
	}

	//--------------------------------------------------------------------------

	public static function view($name, $data = array())
	{
		return View::make("widgets/{$name}", $data);
	}

	//--------------------------------------------------------------------------

}