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

		'Field'       => 'Devhook\Fields\Field',
		'BaseField'   => 'Devhook\Fields\BaseField',
		'HtmlField'   => 'Devhook\Fields\HtmlField',
		'FileField'   => 'Devhook\Fields\FileField',
		'ImageField'  => 'Devhook\Fields\ImageField',
		'ColorField'  => 'Devhook\Fields\ColorField',
		'IconField'   => 'Devhook\Fields\IconField',
		'ToggleField' => 'Devhook\Fields\ToggleField',

		'Devhook' => 'Devhook\Facade\DevhookFacade',
		'Admin'   => 'Devhook\Facade\AdminFacade',

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
		if (App::runningInConsole()) {
			return;
		}

		devhook_class_aliases($this->aliases);

		App::singleton('user', function() {
			return Auth::check() ? Auth::user() : new User;
		});

		View::addLocation(__DIR__ . '/../views');

		View::share('user', $this->app->user);

		\AdminUI::boot();

		\Field::register('html',   'HtmlField');
		\Field::register('file',   'FileField');
		\Field::register('image',  'ImageField');
		\Field::register('color',  'ColorField');
		\Field::register('icon',   'IconField');
		\Field::register('toggle', 'ToggleField');
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