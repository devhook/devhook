<?php namespace Devhook;

use App;
use Auth;

class AdminController extends Controller
{

	//--------------------------------------------------------------------------

	const DEFAULT_LAYOUT = 'admin.page';

	//--------------------------------------------------------------------------

	protected $model;
	protected $publicActions = array();

	//--------------------------------------------------------------------------

	public function __construct($model = null)
	{
		$this->model = $model;

		$ctrl = $this;
		$this->beforeFilter(function($route, $request) use($ctrl) {
			return $ctrl->_authFilter($route, $request);
		});

		parent::__construct();
	}

	//-------------------------------------------------------------------------

	public function _authFilter($route, $request)
	{
		$action    = $route->getAction();
		$protected = true;

		if (strpos($action, '@')) {
			list($controller, $action) = explode('@', $action);
			$protected = !in_array($action, $this->publicActions);
		}

		if ($protected) {
			if (Auth::guest()) return App::abort(404);
			if ( ! Auth::user()->isSuperUser()) return App::abort(404);
		}
	}

	//--------------------------------------------------------------------------
}