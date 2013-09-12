<?php namespace Devhook;

use Config;
use Redirect;
use Input;
use View;

class DataAdminController extends AdminController
{

	//--------------------------------------------------------------------------

	protected $link;

	//--------------------------------------------------------------------------

	public function getIndex()
	{

	}

	//--------------------------------------------------------------------------

	public function getList()
	{
		return \AdminUI::dataTable($this->model, array());
	}

	//--------------------------------------------------------------------------

	public function getAdd()
	{
		$form = \iForm::model($this->model);

		return View::make('admin.form')
			->with('form', $form);
	}

	//--------------------------------------------------------------------------

	public function postAdd()
	{
		if ($this->model->save()) {
			return \Admin::redirect($this->link());
		}

		return Redirect::back()
			->withErrors($this->model->validator())
			->withInput(Input::input());
	}

	//--------------------------------------------------------------------------

	public function getEdit($id)
	{
		$data = $this->model->find($id);

		$form = \iForm::model($data);

		return View::make('admin.form')
			->with('form', $form);
	}

	//--------------------------------------------------------------------------

	public function postEdit($id)
	{
		$data = $this->model->find($id);

		if ($data->save()) {
			if (Input::get('redirect_back')) {
				return \Admin::redirect($this->link());
			} else {
				return Redirect::back();
			}
		}

		return Redirect::back()
			->withErrors($data->validator())
			->withInput(Input::input());
	}

	//--------------------------------------------------------------------------

	public function getRemove($id)
	{
		$this->model->find($id)->forceDelete();
		return Redirect::back();
	}

	//--------------------------------------------------------------------------
	//--------------------------------------------------------------------------
	//--------------------------------------------------------------------------

	public function missingMethod($args = null)
	{
		// if (!$args) {
		// 	return parent::missingMethod($args);
		// }

		$modelClass   = \Devhook::getClassByKey($args[0]);
		$this->model  = new $modelClass;
		$action       = ucfirst(isset($args[1]) ? $args[1] : 'List');
		$methodAction = strtolower($_SERVER['REQUEST_METHOD']) . $action;
		$anyAction    = 'any' . $action;
		$args         = array_splice($args, 2);
		$modelActions = $modelClass::modelActions();
		$this->link   = $modelActions['list']['link'];
		\AdminUI::menu('navbar')->active('data');

		$actions = $modelClass::modelActions();
		foreach ($actions as $key => $act) {
			\AdminUI::menu('actions')->add($act['link'], $act['title']);
		}
		return call_user_func_array(array($this, $methodAction), $args);
		if (method_exists($this, $methodAction)) {
			return call_user_func_array(array($this, $methodAction), $args);
		} elseif (method_exists($this, $anyAction)) {
			return call_user_func_array(array($this, $anyAction), $args);
		}

		return parent::missingMethod($args);
	}

	//--------------------------------------------------------------------------
}