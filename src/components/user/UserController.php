<?php namespace Devhook;

use \iForm;
use Auth;
use Redirect;
use Input;

class UserController extends FrontController
{

	//--------------------------------------------------------------------------

	protected $comName = 'user';

	//--------------------------------------------------------------------------

	public function getIndex()
	{
		return $this->view('account');
	}

	//--------------------------------------------------------------------------

	public function getLogin()
	{
		if (app('user')->id) {
			return Redirect::to('/');
		}

		return $this->view('login')
			->with('form', iForm::model(app('user')));
	}

	//--------------------------------------------------------------------------

	public function postLogin()
	{
		$user = app('user');

		if ($user->validate('loginRules')) {
			if (User::auth()) {
				return Redirect::to('/');
			}

			$user->validator()->messages()->add('', 'Не верный логин или пароль');
		}

		return Redirect::back()->withErrors($user->errors())->withInput(Input::all());
	}

	//--------------------------------------------------------------------------

	public function getLogout()
	{
		Auth::logout();

		return Redirect::back();
	}

	//--------------------------------------------------------------------------

	public function getRegistration()
	{
		return $this->view('registration')
			->with('form', iForm::model(app('user')));
	}

	//--------------------------------------------------------------------------

	public function postRegistration()
	{
		$user = app('user');

		if ($user->save()) {
			Auth::loginUsingId($user->id);
			return Redirect::to('account/login');
		}

		return Redirect::back()->withErrors($user->validator())->withInput(Input::all());
	}

	//--------------------------------------------------------------------------
}