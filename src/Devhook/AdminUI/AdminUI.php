<?php namespace Devhook;

use \View;
use \Field;
use \Admin;
use \Request;
use \Config;
// use \Devhook;

class AdminUI
{
	//--------------------------------------------------------------------------

	public $menu;

	//-------------------------------------------------------------------------

	protected static $instance;
	protected $devhook;

	//-------------------------------------------------------------------------

	public static function get_instance()
	{
		if (is_null(static::$instance)) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	//-------------------------------------------------------------------------

	protected function __construct()
	{
		$this->devhook = app()->devhook;

		if ( ! $this->devhook->backendAllowed()) {
			return;
		}

		$this->menu = new AdminUiMenu;
		$this->bootMenu();
	}

	//--------------------------------------------------------------------------

	public function bootMenu()
	{

		// MODES
		$isBackend = $this->devhook->isBackend();
		$this->menu->add('navbar.site', 'action/set-mode/site', 'Caйт')->icon('globe')->active(!$isBackend);

		// $this->menu('navbar')->add('action/set-mode/site', 'Caйт')->icon('globe')->active(!$isBackend);
		// $this->menu('modes')->add('action/set-mode/admin', 'Управление')->active($isBackend);

		// Data
		// $this->menu('navbar')->add('data', 'Данные')->icon('folder-close');

		// TRAY
		$this->menu('tray')->add('user', app('user')->login)->icon('user');
		$this->menu('tray', 'user')->add('action/logout', 'Выход');

		$models = (array) $this->devhook->scanModels();
		foreach ($models as $model => $path) {
			$model::initBackend($this);
		}
	}

	/***************************************************************************
		!!! NEW UI Methods:
	***************************************************************************/

	// public function addMenuItem($itemKey, $backendAction, $title = null) {

	// }

	/***************************************************************************
		UI Methods:
	***************************************************************************/

	public function navbar()
	{
		if ( ! $this->devhook->backendAllowed()) {
			return;
		}

		return View::make('admin.ui.navbar');
	}

	//--------------------------------------------------------------------------

	public function alerts()
	{
		return View::make('admin.ui.alerts');
	}

	//--------------------------------------------------------------------------

	public function actions($actions, $sizeClass='btn-xs') {
		$result = '';
		$html = app('html');
		foreach ($actions as $key => $act) {
			$url      = isset($act['link']) ? URL::to($this->devhook->backendRoute($act['link'])) : '#';
			$icon     = empty($act['icon']) ? '' : '<i class="icon-'.$act['icon'].'"></i>';
			$title    = isset($act['title']) ? ($icon ? ' ' : '') . $act['title'] : '';
			$btnClass = $sizeClass . ' btn btn-' . (empty($act['class']) ? 'default' : $act['class']);
			$attr     = array('class' => $btnClass);

			$result .= '<a href="'.$url.'"'.$html->attributes($attr).'>'.$icon.$html->entities($title).'</a>' . PHP_EOL;
		}

		return $result;
	}

	//--------------------------------------------------------------------------

	public function breadcrumbs($breadcrumbs = null, $custom_data = null)
	{
		static $data = array();

		if ($breadcrumbs) {
			$data['data'] = $breadcrumbs;
		}

		if ($custom_data) {
			$data += $custom_data;
		}

		if ($data) {
			if (empty($data['link'])) {
				$data['link'] = function($row){
					return $row->link();
				};
			}
			return View::make('admin.ui.breadcrumbs', $data);
		}
	}

	//--------------------------------------------------------------------------

	public function menu($key, $subKey = null)
	{
		// return $this->menu->get($key);

		static $menus = array();

		if (empty($menus[$key])) {
			$menus[$key] = new \iMenu();
			$menus[$key]->linkPrefix($this->devhook->backendRoute());
			$menus[$key]->elem()->className('dh-' . $key);
		}

		if ($subKey !== null) {
			$submenu = $menus[$key]->submenu($subKey);
			$submenu->linkPrefix($this->devhook->backendRoute());
			return $submenu;
			// if (empty($menus[$key]->submenu)) {
			// 	$menus[$key]->submenu = new \iMenu();
			// 	$menus[$key]->submenu->elem()->className('dropdown');

			// 	return $menus[$key]->submenu;
			// }
		}

		return $menus[$key];
	}

	/***************************************************************************
		Common widgets
	***************************************************************************/

	public function dataTable($model, $data = null)
	{
		static $defaultColumnsByName = array('id', 'image', 'name', 'title', 'login', 'email', 'price', 'created_at');
		static $defaultData    = array(
			'columns'  => true,
			'paginate' => true,
			// 'sorting'     => true,
			// 'filter'      => true,

			'data'       => null,
			'pagination' => '',
		);

		$data = array_merge($defaultData, (array) $data);

		if (is_string($model)) {
			$model = new $model;
		}

		$fields = $model->getFields();

		$defaultColumns = array();
		foreach ($defaultColumnsByName as $name) {
			if (isset($fields[$name])) {
				$defaultColumns[$name] = null;
			}
		}


		// Data
		if ( ! $data['data']) {
			$fn = isset($data['query']) ? $data['query'] : function($query){
				$query->orderBy('id', 'DESC');
			};

			$model->setRelation('images', function($model){
				return $model->morphMany('Image', 'imageable')->orderBy('primary', 'desc');
			});

			$query = $model->newQuery();
			$fn($query);

			// foreach ($fields as $field) {
			// 	if (is_object($field)) {
			// 		$field->adminQueryBulder($model);
			// 	}
			// }

			// $query->with('images');

			if ($data['paginate']) {
				$limit = $data['paginate'] === true ? 30 : $data['paginate'];
				$data['data'] = $query->paginate($limit);
			} else {
				$data['data'] = $query->get();
			}
		}

		// Columns
		$columns         = is_array($data['columns']) ? $data['columns'] : $defaultColumns;
		$data['columns'] = array();

		foreach ($columns as $key => $mutator) {
			if (isset($fields[$key])) {
				$data['columns'][$key] = $fields[$key];
				$data['columns'][$key]->mutator = $mutator ? $mutator : false;
			}
		}

		// Pagination
		if ($data['paginate']) {
			$data['pagination'] = $data['data']->links();
		}


		return View::make('admin.ui.data_table', $data);
	}

}