<?php namespace Devhook;

use Illuminate\Support\ServiceProvider;
use App;
use Config;
use View;
use User;
use Auth;;


class DevhookServiceProvider extends ServiceProvider {

	//--------------------------------------------------------------------------

	protected $aliases = array(
		'Model'           => 'Devhook\Model',
		'FrontController' => 'Devhook\FrontController',
		'AdminController' => 'Devhook\AdminController',

		'Page'  => 'Devhook\Page',
		'Asset' => 'Devhook\Asset',

		'iElem' => 'Devhook\iHtml\iElem',
		'iForm' => 'Devhook\iHtml\iForm',
		'iMenu' => 'Devhook\iHtml\iMenu',

		'HtmlField'   => 'Devhook\Fields\HtmlField',
		'FileField'   => 'Devhook\Fields\FileField',
		'ImageField'  => 'Devhook\Fields\ImageField',
		'ColorField'  => 'Devhook\Fields\ColorField',
		'IconField'   => 'Devhook\Fields\IconField',
		'ToggleField' => 'Devhook\Fields\ToggleField',

		'Devhook' => 'Devhook\Devhook',
		'Admin'   => 'Devhook\AdminFacade',
		'AdminUI' => 'Devhook\AdminUI',
		'Widget'  => 'Devhook\Widget',

		'User'    => 'Devhook\User',
		'Image'   => 'Devhook\Image',
	);

	protected $defer = false;

	//--------------------------------------------------------------------------

	public function register()
	{
		$this->app['admin'] = $this->app->share(function($app) {
			return Admin::get_instance();
		});
	}

	//--------------------------------------------------------------------------

	public function boot()
	{
		devhook_class_aliases($this->aliases);

		App::singleton('user', function() {
			return Auth::check() ? Auth::user() : new User;
		});

		View::addLocation(__DIR__ . '/../views');

		View::share('user', $this->app->user);

		\AdminUI::boot();

		\Devhook::registerFieldType('html',   'HtmlField');
		\Devhook::registerFieldType('file',   'FileField');
		\Devhook::registerFieldType('image',  'ImageField');
		\Devhook::registerFieldType('color',  'ColorField');
		\Devhook::registerFieldType('icon',   'IconField');
		\Devhook::registerFieldType('toggle', 'ToggleField');
	}

	//--------------------------------------------------------------------------

	public function provides()
	{
		return array('admin');
	}

	//--------------------------------------------------------------------------

	public function __destruct()
	{
		if (App::runningInConsole()) {
			return;
		}

		// $runtime = microtime(true) - LARAVEL_START;
		// echo "<div style='text-align:center; color:#CCC; text-shadow:0 0 1px rgba(255,255,255,.5)'>{$runtime}</div>";
	}
}