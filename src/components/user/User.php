<?php namespace Devhook;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Input;
use Config;
use Auth;
use Hash;

class User extends Model implements UserInterface, RemindableInterface
{
	//--------------------------------------------------------------------------

	protected $modelName  = 'Пользователи';
	protected $table      = 'users';
	protected $hidden     = array('password');

	//--------------------------------------------------------------------------

	protected function initFields()
	{
		return array(
			'id' => array('db' => 'increments'),

			// 'status' => array(
			// 	'label'   => 'Status',
			// 	'field'   => 'bool',
			// 	'default' => 1,
			// 	'db'      => 'boolean',
			// ),

			'login' => array(
				'label'      => 'Логин',
				'field'      => 'text',
				'rules'      => 'required|between:3,16|unique:' . $this->table . ($this->id ? ',login,' . $this->id : ''),
				'loginRules' => 'required|between:3,16',
				'db'         => 'string:16|unique|index',
			),

			'email' => array(
				'label'      => 'E-mail',
				'field'      => 'text',
				'rules'      => 'required|email|unique:' . $this->table . ($this->id ? ',email,' . $this->id : ''),
				// 'loginRules' => 'required|email',
				'db'         => 'string|unique|index',
			),

			'password' => array(
				'label'      => 'Пароль',
				'field'      => 'password',
				'rules'      => ($this->id ? '' : 'required|') . 'alpha_num|between:7,16|confirmed',
				'loginRules' => 'required|alpha_num|between:7,16',
				'db'         => 'string:128',
			),

			'password_confirmation' => array(
				'label' => 'Подтверждение пароля',
				'field' => 'password',
				'rules' => ($this->id ? '' : 'required'),
				'db'    => false,
			),

			'created_at' => array('db' => 'timestamp'),
			'updated_at' => array('db' => 'timestamp'),
		);
	}

	//--------------------------------------------------------------------------

	protected function initAdminUi()
	{
		\AdminUi::menu('navbar')->add('users' , 'Пользователи')->icon('group');
	}

	//--------------------------------------------------------------------------

	protected function auth($remember = null)
	{
		if (is_null($remember)) {
			$remember = Input::get('remember');
		}

		$login    = Input::get('login');
		$password = Input::get('password');

		if (Auth::attempt(compact('login', 'password'), $remember)) {
			return true;
		}

		$user = User::where('login', $login)->where('password', base64_encode($password))->first();

		if ($user) {
			Auth::login($user, $remember);
			$user->password = base64_decode($user->password);
			$user->save();

			return true;
		}

		return false;
	}

	//--------------------------------------------------------------------------

	protected function loginFields()
	{
		return array_only($this->fields(), array('login', 'password'));
	}

	//--------------------------------------------------------------------------

	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	//--------------------------------------------------------------------------

	public function getAuthPassword()
	{
		return $this->password;
	}

	//--------------------------------------------------------------------------

	public function setPasswordAttribute($value)
	{
		if ($value) {
			$this->attributes['password'] = Hash::make($value);
		}
	}

	//--------------------------------------------------------------------------

	public function getReminderEmail()
	{
		return $this->email;
	}

	/***************************************************************************
		Role & Permissions:
	***************************************************************************/

	public function getGroups()
	{

	}

	//--------------------------------------------------------------------------

	public function getPermissions()
	{

	}

	//--------------------------------------------------------------------------

	public function hasAccess($role)
	{
		if ($this->isSuperUser()) {
			return true;
		}

		return false;
	}

	//--------------------------------------------------------------------------

	public function hasAnyAccess()
	{

	}

	//--------------------------------------------------------------------------

	public function isActivated()
	{
		return $this->status > 0;
	}

	//--------------------------------------------------------------------------

	public function isSuperUser()
	{
		return $this->group_id == 1;
	}

	//--------------------------------------------------------------------------

	public function inGroup($group)
	{

	}

	//--------------------------------------------------------------------------


}