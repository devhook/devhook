<?php namespace Devhook;

use \View;
use \Field;
use \Admin;
use \Request;
use \Config;

class AdminUI
{
	//--------------------------------------------------------------------------

	public static function boot()
	{
		static $booted;

		if ( ! \Admin::enable()) {
			return;
		}

		if (!$booted) {
			$booted = true;
			static::bootMenu();
		}
	}

	//--------------------------------------------------------------------------

	public static function bootMenu()
	{
		// Data
		static::menu('navbar')->add('data', 'Данные')->icon('folder-close');

		// TRAY
		static::menu('tray')->add('user', app('user')->login)->icon('user');
		// static::menu('tray')->add('user', '')->icon('star');

		// MODES
		$mode = \Admin::currentMode();
		static::menu('modes')->add('action/set-mode/site', 'Caйт')->active($mode == 'site');
		static::menu('modes')->add('action/set-mode/admin', 'Управление')->active($mode == 'admin');
	}

	/***************************************************************************
		UI Methods:
	***************************************************************************/

	public static function navbar()
	{
		if ( ! \Admin::enable()) {
			return;
		}

		$models = \Devhook::scanModels();
		foreach ($models as $model => $path) {
			$model::initAdminUi();
		}

		return View::make('admin.navbar');
	}

	//--------------------------------------------------------------------------

	public static function alerts()
	{
		return View::make('admin.alerts');
	}

	//--------------------------------------------------------------------------

	public static function actions($actions, $sizeClass='btn-xs') {
		$result = '';
		$html = app('html');
		foreach ($actions as $key => $act) {
			$url      = isset($act['link']) ? Admin::url($act['link']) : '#';
			$icon     = empty($act['icon']) ? '' : '<i class="icon-'.$act['icon'].'"></i>';
			$title    = isset($act['title']) ? ($icon ? ' ' : '') . $act['title'] : '';
			$btnClass = $sizeClass . ' btn btn-' . (empty($act['class']) ? 'default' : $act['class']);
			$attr     = array('class' => $btnClass);

			$result .= '<a href="'.$url.'"'.$html->attributes($attr).'>'.$icon.$html->entities($title).'</a>' . PHP_EOL;
		}

		return $result;
	}

	//--------------------------------------------------------------------------

	public static function breadcrumbs($breadcrumbs = null, $custom_data = null)
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
			return View::make('admin.breadcrumbs', $data);
		}
	}

	//--------------------------------------------------------------------------

	public static function menu($key, $subKey = null)
	{
		static $menus = array();

		if (empty($menus[$key])) {
			$menus[$key] = new \iMenu();
			$menus[$key]->linkPrefix(Admin::adminRoute());
			$menus[$key]->elem()->className('dh-' . $key);
		}

		if ($subKey !== null) {
			$submenu = $menus[$key]->submenu($subKey);
			$submenu->linkPrefix(Admin::adminRoute());
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

	public static function dataTable($model, $data = null)
	{
		static $defaultColumnFilter = array('id', 'image', 'title', 'name', 'login', 'email', 'created_at');
		static $defaultData = array(
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

		// Data
		if ( ! $data['data']) {
			$fn = isset($data['query']) ? $data['query'] : function($query){
				$query->orderBy('id', 'DESC');
			};
			$query = $model->newQuery();
			$fn($query);

			if ($data['paginate']) {
				$limit = $data['paginate'] === true ? 30 : $data['paginate'];
				$data['data'] = $query->paginate($limit);
			} else {
				$data['data'] = $query->get();
			}
		}

		// Columns
		$columnFilter    = is_array($data['columns']) ? $data['columns'] : $defaultColumnFilter;
		$fields          = $model->fields();
		$data['columns'] = array();
		foreach ($columnFilter as $key => $mutator) {
			if (is_numeric($key)) {
				$key     = $mutator;
				$mutator = null;
			}

			if (isset($fields[$key])) {
				// if (!$mutator) {
				// 	$mutator = $fields[$key]->valueMutator($model, $key);
				// }
				$data['columns'][$key] = $fields[$key];
				// $data['columns'][$key]->mutator = $mutator;
			}
		}

		// Pagination
		if ($data['paginate']) {
			// ->appends(array('sort'=>$sort, 'desc'=>$desc))
			$data['pagination'] = $data['data']->links();
		}


		return View::make('admin.data_table', $data);
	}

}