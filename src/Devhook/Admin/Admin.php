<?php namespace Devhook;

use \Redirect;
use \Request;
use \Config;
use \Route;
use \URL;

class Admin
{
	//--------------------------------------------------------------------------

	protected static $instance;

	protected $adminRoute;
	protected $loadedModels = array();

	//--------------------------------------------------------------------------

	public static function get_instance()
	{
		if (is_null(static::$instance)) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	//--------------------------------------------------------------------------

	protected function __construct()
	{
		$this->adminRoute = Config::get('devhook.adminRoute', 'admin');
	}

	//--------------------------------------------------------------------------

	public function enable()
	{
		static $enable;

		if ($enable === null) {
			$enable = app('user')->isSuperUser();
		}

		return $enable;
	}

	//--------------------------------------------------------------------------

	public function route($route, $callback = null)
	{
		if ( ! Request::is(static::adminRoute() . '*')) {
			return;
		}

		if (is_callable($route)) {
			$callback = $route;
			$route    = null;
		}

		Route::group(array('prefix' => static::adminRoute($route)), $callback);
	}

	//--------------------------------------------------------------------------

	public function adminRoute()
	{
		$route = '';

		if (func_num_args()) {
			$route = '/' . implode('/', func_get_args());
		}

		return $this->adminRoute . $route;
	}

	//--------------------------------------------------------------------------

	public function registerModel(&$model)
	{
		$this->loadedModels[$model->modelKeyword()] = $model;
	}

	//--------------------------------------------------------------------------

	public function loadedModels()
	{
		return $this->loadedModels;
	}

	//--------------------------------------------------------------------------

	public function url()
	{
		return URL::to($this->adminRoute, implode('/', func_get_args()));
	}

	//--------------------------------------------------------------------------

	public function link($link, $text, $attr = array())
	{
		return '<a href="'.self::url($link).'">' . $text . '</a>';
	}

	//--------------------------------------------------------------------------

	public function redirect($to)
	{
		return Redirect::to($this->adminRoute . '/' . implode('/', func_get_args()));
	}

	//--------------------------------------------------------------------------

	public function currentMode()
	{
		static $mode;

		if (is_null($mode)) {
			if (Request::is($this->adminRoute . '*')) {
				$mode = 'admin';
			} else {
				$mode = 'site';
			}
		}

		return $mode;
	}

	//--------------------------------------------------------------------------
}