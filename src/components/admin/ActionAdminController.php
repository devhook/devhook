<?php namespace Devhook;

use \Redirect;
use \Input;
use \View;
use \File;
use \iForm;
use \Auth;

class ActionAdminController extends \AdminController {

	//-------------------------------------------------------------------------

	protected $publicActions = array(
		'getLogin',
		'postLogin',
	);

	//--------------------------------------------------------------------------

	public function getLogin()
	{
		$user = app('user');

		if ($user->isSuperUser()) {
			return 'Welcome!';
		}

		$this->layout = 'admin.login';
		$this->makeLayout()->with('form', iForm::model($user));
	}

	//-------------------------------------------------------------------------

	public function postLogin()
	{
		$user = app('user');

		if ($user->validate('loginRules')) {
			if (User::auth()) {
				return \Admin::redirect('/');
			}

			$user->validator()->messages()->add('', 'Не верный логин или пароль');
		}

		return Redirect::back()->withErrors($user->errors())->withInput(Input::all());
	}

	//--------------------------------------------------------------------------

	public function getLogout()
	{
		Auth::logout();

		return Redirect::to('/');
	}

	//-------------------------------------------------------------------------

	public function anySetMode($mode)
	{
		switch ($mode) {
			case 'admin':
				return Redirect::to(\Admin::url());
				break;

			// case 'edit':
			// 	return Redirect::to('/');
			// 	break;

			default:
				return Redirect::to('/');
				break;
		}
	}

	//--------------------------------------------------------------------------

	public function getDelete($modelKey, $id, $fieldName = null)
	{
		$modelClass = \Devhook::getClassByKey($modelKey);


		if ($model = $modelClass::find($id)) {
			if (is_null($fieldName)) {
				$model->delete();
			} else {
				if (File::delete(public_path($model->$fieldName))) {
					$model->$fieldName = '';
					$model->forceSave();
				}
			}
		}

		return Redirect::back();
	}

	//--------------------------------------------------------------------------

	public function getEnabled($modelKey, $id, $enabled = 1)
	{
		$modelClass = \Devhook::getClassByKey($modelKey);
		$model      = $modelClass::find($id);

		$model->enabled = (int) (bool) $enabled;
		$model->forceSave();

		return Redirect::back();
	}

	//--------------------------------------------------------------------------

	public function missingMethod($params)
	{
		return 'AdminController';
	}

}