<?php namespace Devhook;

use Devhook\Fields\Field;
use \Devhook;
use \Request;
use \ImageField;
use \Validator;
use \Config;
use \Input;
use \Schema;
use \Admin;
use \URL;

class Model extends \Illuminate\Database\Eloquent\Model {

	//--------------------------------------------------------------------------

	protected $modelName;

	//-------------------------------------------------------------------------

	protected $_modelKeyword;     // model
	protected $_modelFullKeyword; // name.space.model
	protected $_fields;
	protected $_is_valid = false;
	protected $_validator;

	//--------------------------------------------------------------------------

	public function __construct(array $attributes = array())
	{
		$class = get_class($this);

		$this->_modelFullKeyword = strtolower(str_replace('\\', '.', $class));

		if (is_null($this->_modelKeyword)) {
			if ($classNamePos = strrpos($class, '\\')) {
				$class = substr($class, $classNamePos+1);
			}
			$this->_modelKeyword = strtolower($class);
		}

		parent::__construct($attributes);
	}

	//--------------------------------------------------------------------------

	protected function initFields() {
		return array();
	}

	/***************************************************************************
		Api
	***************************************************************************/

	public function getModelName()
	{
		return $this->modelName ? $this->modelName : $this->getModelKeyword();
	}

	//--------------------------------------------------------------------------

	public function getModelFullKeyword()
	{
		return $this->_modelFullKeyword;
	}

	//--------------------------------------------------------------------------

	public function getModelKeyword()
	{
		return $this->_modelKeyword;
	}

	//--------------------------------------------------------------------------

	public function getFields() {

		if (is_null($this->_fields)) {
			$extendFields = (array) Config::get('fields.' . $this->_modelKeyword);
			$this->_fields = array_merge($this->initFields(), $extendFields);

			foreach ($this->_fields as $key => $field) {
				$this->_fields[$key] = Field::make($key, $field, $this);
			}
		}

		return (array) $this->_fields;
	}

	/***************************************************************************
		ADMIN API
	***************************************************************************/

	protected function initBackend($adminUI)
	{
		if ($this->modelName && Request::is(Devhook::backendRoute('data*'))) {
			$link = 'data/' . $this->getModelFullKeyword();
			$adminUI->menu('subnav')->add($link , $this->getModelName());
		}
	}

	//--------------------------------------------------------------------------

	public function getModelActions()
	{
		return array(
			'list' => array(
				'title' => 'Просмотр',
				// 'link'  => $this->listAction(),
				'link'  => 'data/' . $this->getModelKeyword(),
				'icon' => 'list'
			),
			'add' => array(
				'title' => 'Добавить',
				'link'  => 'data/' . $this->getModelKeyword() . '/add',
				'icon' => 'plus'
			),
		);
	}

	//--------------------------------------------------------------------------

	public function getRowActions()
	{
		return array(
			'edit' => array(
				'title' => 'Редактировать',
				'icon'  => 'pencil',
				'class' => 'primary',
				'link'  => 'data/' . $this->getModelKeyword() . '/edit/' . $this->getKey(),
			),
			'remove' => array(
				'title' => 'Удалить',
				'icon'  => 'remove',
				'class' => 'danger',
				'link'  => 'action/delete/' . $this->getModelKeyword() . '/' . $this->getKey(),
			),
		);
	}


	/***************************************************************************
		Default mutators:
	***************************************************************************/

	protected function getLinkAttribute()
	{
		return false;
	}

	/***************************************************************************
		Automation:
	***************************************************************************/

	public function forceSave()
	{
		return parent::save();
	}

	//--------------------------------------------------------------------------

	public function save(array $options = array())
	{
		if ( ! $this->_is_valid) {
			if ( ! $this->validate()) {
				return false;
			}
		}

		$save            = false;
		$this->_is_valid = false;

		if (!empty($_POST)) {
			$this->setData();
		}

		if ($this->attributes) {
			$save = true;
		}

		if ($save && ($result = parent::save($options))) {
			$fields = $this->getFields();

			return $result;
		}

		return false;
	}

	//--------------------------------------------------------------------------

	public function validate() {
		$rules  = array();
		$names  = array();
		$fields = $this->getFields();

		// Rules
		foreach ($fields as $name => $field) {
			if ($field && ($fieldRules = $field->getRules())) {
				$rules[$name] = $fieldRules;
			}
		}

		// Field names
		foreach ($fields as $name => $field) {
			if ($field && $label = $field->getLabel()) {
				$names[$name] = $label;
			}
		}

		if ( ! $rules) {
			$this->_is_valid = true;
			return true;
		}

		$this->_validator = Validator::make(Input::all(), $rules);
		$this->_validator->setAttributeNames($names);

		$this->_is_valid = $this->_validator->passes();

		return $this->_is_valid;
	}

	//--------------------------------------------------------------------------

	public function validator()
	{
		return $this->_validator;
	}

	//--------------------------------------------------------------------------

	public function errors()
	{
		if ($this->_validator) {
			return $this->_validator->messages();
		}
	}

	//--------------------------------------------------------------------------

	protected function setData($data = null)
	{
		if (is_null($data)) {
			$data = Input::all();
		}

		$fields = $this->getFields();

		foreach ($fields as $name => $field) {
			if ($field && $field->db === false) {
				continue;
			}

			if ($field) {
				$field->setValue($data);
			} elseif (isset($data[$name])) {
				$this->$name = $data[$name];
			}
		}
	}

	//--------------------------------------------------------------------------
}