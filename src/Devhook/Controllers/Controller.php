<?php namespace Devhook;

use View, Config;

class Controller extends \Controller
{

	//--------------------------------------------------------------------------

	const DEFAULT_LAYOUT = 'frontend.page';

	//--------------------------------------------------------------------------

	protected $layout;
	protected $comName;
	// protected $viewPath;
	protected $viewNamespace;
	// protected $viewDir;

	//--------------------------------------------------------------------------

	public function __construct()
	{
		if (is_null($this->comName)) {
			$this->comName = strtolower(substr(get_called_class(), 0, -strlen('Controller')));
		}

		if (is_null($this->viewNamespace)) {
			$this->viewNamespace = 'com-' . $this->comName;
		}

		if ($this->viewNamespace) {
			View::addNamespace($this->viewNamespace, base_path() . '/components/' . $this->comName . '/views');
			View::addNamespace($this->viewNamespace, $this->viewPath());
		}

		if (is_null($this->layout)) {
			$this->layout = Config::get('view.layout', static::DEFAULT_LAYOUT);
		}

		$ctrl = $this;
		$this->afterFilter(function() use ($ctrl) {
			return call_user_func_array(array($ctrl, 'renderLayoutCallback'), func_get_args());
		});

		$this->init();
	}

	//-------------------------------------------------------------------------

	public function renderLayoutCallback($route, $request, $response){
		if ( ! method_exists($response, 'getContent')) {
			return $response;
		}

		// Рендеринг шалблона страницы
		if ($this->layout) {
			$content = $response->getContent();
			$layout  = $this->layout;

			// Рендерим шаблон если он не был отрендерин до этого
			if ( ! is_object($this->layout)) {
				$this->makeLayout();
			}

			if ($layout != $content) {
				$this->layout->with('content', $content);
			}

			$response->setContent($this->layout);
		}
	}

	//--------------------------------------------------------------------------

	protected function init() {}

	//--------------------------------------------------------------------------

	public function setupLayout()
	{
		// Рендерить шаблон до вызова действия текщего контроллера
		$renderBeforeAction = false;

		// Рендерим шаблон
		if ($renderBeforeAction && $this->layout) {
			$this->layout = View::make($this->layout);
		}
	}

	//--------------------------------------------------------------------------

	public function viewPath()
	{
		return __DIR__ . '/../../components/' . $this->comName . '/views';
	}

	//--------------------------------------------------------------------------

	public function view($name, $data = array())
	{
		$view = $this->viewNamespace ? $this->viewNamespace . '::' . $name : $name;

		return View::make($view, $data);
	}

	//--------------------------------------------------------------------------

	protected function makeLayout()
	{
		$this->layout = View::make($this->layout)->with('content', '');

		return $this->layout;
	}

	//--------------------------------------------------------------------------

	public function missingMethod($args)
	{
		if (method_exists($this, 'route')) {
			return call_user_func_array(array($this, 'route'), $args);
		}

		return parent::missingMethod($args);
	}

	//--------------------------------------------------------------------------
}