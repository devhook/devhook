<?php namespace Devhook\Fields;

use \Form;

class BaseField {

	//--------------------------------------------------------------------------

	public $db;
	public $name;
	public $type;
	public $label;
	public $required = false;

	protected $options;
	protected $model;
	protected $original = array();

	//--------------------------------------------------------------------------

	public static function boot(){}

	//--------------------------------------------------------------------------

	protected function init(){}

	//--------------------------------------------------------------------------

	public function __construct($name, $field, $model)
	{
		$this->original = $field;

		$this->settings = is_array($field['field']) ? $field['field'] : array();
		$this->type     = $this->settings ? $this->settings['type'] : $field['field'];
		$this->label    = $this->get('label');
		$this->options  = $this->get('options');
		$this->db       = $this->get('db');
		$this->name     = $name;

		$this->model   = $model;

		$this->init();
	}

	//--------------------------------------------------------------------------

	public function get($key)
	{
		return isset($this->original[$key]) ? $this->original[$key] : null;
	}

	//--------------------------------------------------------------------------

	public function rules($key)
	{
		return $this->setRules($this->get($key));
	}

	//--------------------------------------------------------------------------

	public function required($rulesKey = 'rules')
	{
		if ($rules = $this->get($rulesKey)) {
			if (is_array($rules)) {
				return current($rules) == 'required';
			}

			return strpos($rules, 'required') !== false;
		}

		return false;
	}

	//--------------------------------------------------------------------------

	public function settings($key = null, $default = null)
	{
		if($key) {
			return isset($this->settings[$key]) ? $this->settings[$key] : value($default);
		}

		return $this->settings;
	}

	//--------------------------------------------------------------------------

	public function render($value, $attr)
	{
		$type = $this->type;

		switch ($type) {
			case 'password':
				return Form::password($this->name, $attr);

			case 'select':
				return Form::select($this->name, $this->options, $value, $attr);

			default:
				// if (method_exists('Form', $type)) {
					return Form::$type($this->name, $value, $attr);
				// }
		}
	}

	//--------------------------------------------------------------------------

	protected function setRules($rules)
	{
		return $rules;
	}

	//--------------------------------------------------------------------------

	public function setValue($data)
	{
		$name  = $this->name;
		$model = $this->model;

		if (isset($data[$name])) {
			$model->$name = $data[$name];
		}
	}

	//--------------------------------------------------------------------------

	/**
	 * Мутатор для рендеринга значений в админке
	 *
	 * @param [type] $row [description]
	 * @param [type] $key [description]
	 *
	 * @return [type]
	 */
	public function adminValueMutator($row, $key)
	{
		return $row->$key;
	}

	//--------------------------------------------------------------------------

}