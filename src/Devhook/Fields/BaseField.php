<?php namespace Devhook\Fields;

use \Form;

class BaseField {

	//--------------------------------------------------------------------------

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

	public function __construct($name, $field, $model)
	{
		$this->label   = isset($field['label']) ? $field['label'] : '';
		$this->type    = is_array($field['field']) ? $field['field']['type'] : $field['field'];
		$this->name    = $name;
		$this->model   = $model;
		$this->options = isset($field['options']) ? $field['options'] : array();


		$this->original = $field;
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
				// if (is_callable(array('Form', $type))) {
					return Form::$type($this->name, $value, $attr);
				// }
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