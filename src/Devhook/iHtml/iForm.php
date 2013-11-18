<?php namespace Devhook\iHtml;

use Session;
use Form;
use Exception;

class iForm {

	//--------------------------------------------------------------------------

	protected static $globalAttr   = array();
	protected static $globalMacros = array();
	protected static $objectMacros = array();


	//--------------------------------------------------------------------------

	protected $attr   = array();
	protected $macros = array();
	protected $fields;
	public $model;
	protected $data;
	protected $errors;
	protected $rulesKey    = 'rules';
	protected $fieldPrefix = '';
	// protected $validator;

	/***************************************************************************
		STATIC METHODS:
	***************************************************************************/

	public static function model($model, $rulesKey = null, $fieldPrefix = null)
	{
		if (is_string($model)) {
			$model = new $model;
		}

		return new static($model, $rulesKey, $fieldPrefix);
	}

	//--------------------------------------------------------------------------

	public static function globalMacro($name, $callback)
	{
		static::$globalMacros[$name] = $callback;
		foreach (static::$objectMacros as $form) {
			$form->macro($name, $callback, false);
		}
	}

	//--------------------------------------------------------------------------

	public static function globalAttr($name, $key, $attr = null)
	{
		if (!is_null($attr)) {
			$attr = array($key => $attr);
		}

		foreach ($attr as $key => $value) {
			static::$globalAttr[$name][$key] = $value;

			foreach (static::$objectMacros as $form) {
				$form->attr($name, $key, $val);
			}
		}
	}

	/***************************************************************************
		HTML Helpers:
	***************************************************************************/

	// public static function open($action = null, $method = 'POST', $attr = array())
	// {
	// 	if (is_array($action)) {
	// 		$attr = $action;
	// 	} else {
	// 		$attr['action'] = $action;
	// 		$attr['method'] = $method;
	// 	}

	// 	if (!$attr['method']) {
	// 		$attr['method'] = 'POST';
	// 	}

	// 	if (!$attr['action']) {
	// 		$attr['action'] = '.';
	// 	}

	// 	return self::elem('form', $attr, false);
	// }

	// //--------------------------------------------------------------------------

	// public static function close()
	// {
	// 	return self::elem('/form', null, false);
	// }



	/***************************************************************************
		Base Elemets:
	***************************************************************************/

	// public static function staticLabel($label, $attr = array())
	// {
	// 	return self::elem('label', $attr, $label);
	// }

	// //--------------------------------------------------------------------------

	// public static function staticSubmit($label, $attr = array())
	// {
	// 	$attr['type'] = 'submit';
	// 	return self::button($label, $attr);
	// }

	// //--------------------------------------------------------------------------

	// public static function staticButton($label, $attr = array())
	// {
	// 	return self::elem('button', $attr, $label);
	// }



	/***************************************************************************
		Base Fields:
	***************************************************************************/

	// public static function staticInput($type, $name, $value, $attr = array())
	// {
	// 	$attr['type']  = $type;
	// 	$attr['name']  = $name;
	// 	$attr['value'] = $value;
	// 	return self::elem('input', $attr);
	// }

	// //--------------------------------------------------------------------------

	// public static function staticText($name, $value = null, $attr = array())
	// {
	// 	return self::input('text', $name, $value, $attr);
	// }

	// //--------------------------------------------------------------------------

	// public static function staticEmail($name, $value = null, $attr = array())
	// {
	// 	return self::input('email', $name, $value, $attr);
	// }

	// //--------------------------------------------------------------------------

	// public static function staticRadio($name, $value = null, $attr = array())
	// {
	// 	return self::input('radio', $name, $value, $attr);
	// }

	// //--------------------------------------------------------------------------

	// public static function staticCheckbox($name, $value = null, $attr = array())
	// {
	// 	return self::input('checkbox', $name, $value, $attr);
	// }

	// //--------------------------------------------------------------------------

	// public static function staticHidden($name, $value = null, $attr = array())
	// {
	// 	return self::input('hidden', $name, $value, $attr);
	// }

	// //--------------------------------------------------------------------------

	// public static function staticPassword($name, $attr = array())
	// {
	// 	return self::input('password', $name, null, $attr);
	// }

	// //--------------------------------------------------------------------------

	// public static function staticFile($name, $attr = array())
	// {
	// 	return self::input('file', $name, null, $attr);
	// }

	// //--------------------------------------------------------------------------

	// public static function staticTextarea($name, $value = null, $attr = array())
	// {
	// 	return self::elem('textarea', $attr, $value);
	// }

	//--------------------------------------------------------------------------

	// public static function staticSelect($name, $options = array(), $value = null, $attr = array())
	// {
	// 	$attr['name'] = $name;

	// 	$optionsHtml = '';
	// 	foreach ($options as $id => $name) {
	// 		$optionAttr = array('value' => $id);
	// 		if ($id == $value) {
	// 			$optionAttr['selected'] = 'selected';
	// 		}
	// 		$optionsHtml .= self::elem('option', $optionAttr, $name);
	// 	}

	// 	return self::elem('select', $attr, $optionsHtml);
	// }



	/***************************************************************************
		Advanced Fields:
	***************************************************************************/

	public static function staticRadioGroup() {}

	//--------------------------------------------------------------------------

	public static function staticCheckboxGroup() {}

	//--------------------------------------------------------------------------

	public static function staticToggle() {}

	//--------------------------------------------------------------------------

	public static function staticWysiwyg() {}




	/***************************************************************************
		STATIC HELPERS
	***************************************************************************/

	public function elem($node, $attr, $nodeClose = false)
	{

		if ($nodeClose && $nodeClose !== true) {
			$nodeClose .= '</' . $node . '>';
		}

		$this->fillAttr($attr, $node, null);
		foreach ($attr as $key => $val) {
			$node .= ' ' . $key . '="' . $val . '"';
		}

		if ($nodeClose === true) {
			$node .= ' /';
		}

		return '<'.$node.'>'.$nodeClose;
	}

	//--------------------------------------------------------------------------

	// public static function __callStatic($method, $args)
	// {
	// 	$method = 'static' . ucfirst($method);

	// 	return call_user_func_array(array('iForm', $method), $args);
	// }

	/***************************************************************************
		OBJECT METHODS:
	***************************************************************************/

	public function __construct($model, $rulesKey = null, $fieldPrefix = null)
	{
		$this->attr   = static::$globalAttr;
		$this->macros = static::$globalMacros;
		$this->model  = $model;
		$this->data   = $model->getAttributes();
		$this->errors = Session::get('errors');
		$allFields    = $model->getFields();

		static::$objectMacros[] = $this;

		if (!is_null($rulesKey)) {
			$this->rulesKey = $rulesKey;
		}

		if (!is_null($fieldPrefix)) {
			$this->fieldPrefix = $fieldPrefix;
		}

		foreach ($allFields as $name => $field) {
			if ($field && $field->type) {
				$this->fields[$name] = $field;
			}
		}
	}

	//--------------------------------------------------------------------------

	public function macro($name, $callback, $overwrite = true)
	{
		if ($overwrite || !isset($this->macros[$name])) {
			$this->macros[$name] = $callback;
		}
	}

	//--------------------------------------------------------------------------

	public function attr($name, $key, $attr = null)
	{
		if (!is_null($attr)) {
			$attr = array($key => $attr);
		}

		foreach ($attr as $key => $value) {
			$this->attr[$name][$key] = $value;
		}
	}

	//--------------------------------------------------------------------------

	protected function fillAttr(&$attr, $key, $field = null)
	{
		if (empty($this->attr[$key])) {
			return;
		}

		foreach ($this->attr[$key] as $key => $val) {
			if (!isset($attr[$key])) {
				$attr[$key] = is_callable($val) ? $val($this, $field) : $val;
			}
		}
	}

	//--------------------------------------------------------------------------

	public function defaultValue(&$attr, $key, $val)
	{
		if (!isset($attr[$key])) {
			$attr[$key] = $val;
		}
	}

	//--------------------------------------------------------------------------

	// public function validate() {}

	//--------------------------------------------------------------------------

	// public function save() {}

	//--------------------------------------------------------------------------

	// public function getModel() {}

	//--------------------------------------------------------------------------

	public function makeRows()
	{
		$macroResult = $this->runMacros('makeRows', func_get_args());
		if ($macroResult !== null) {
			return $macroResult;
		}

		$result = '';
		foreach ($this->fields as $field => $prop) {
			$result .= $this->row($field);
		}
		return $result;
	}

	//--------------------------------------------------------------------------

	public function make($attr = array())
	{
		$macroResult = $this->runMacros('make', func_get_args());
		if ($macroResult !== null) {
			return $macroResult;
		}

		return Form::open($attr) . $this->makeRows() . Form::close();
	}

	//--------------------------------------------------------------------------

	public function row($field, $label = true, $error = true, $attr = array())
	{
		if (!$this->fieldType($field)) {
			return;
		}

		$macroResult = $this->runMacros('row', func_get_args());
		if ($macroResult !== null) {
			return $macroResult;
		}

		$attr = (array) $attr;

		$this->fillAttr($attr, 'row', $field);

		$content = '';
		$content .= $label ? $this->label($field) : '';
		$content .= $this->field($field);
		$content .= $error ? $this->error($field) : '';

		return $this->elem('div', $attr, $content);
	}

	//--------------------------------------------------------------------------

	public function getFields()
	{
		return (array) $this->fields;
	}

	//--------------------------------------------------------------------------

	public function field($field, $value = null, $attr = array())
	{
		$type = $this->fieldType($field);

		if (!$type) {
			return;
		}

		$macroResult = $this->runMacros('field', func_get_args());
		if ($macroResult !== null) {
			return $macroResult;
		}

		if (is_null($value)) {
			$value = $this->fieldValue($field);
		}
		if (is_string($type)) {
			$this->fillAttr($attr, $type, $field);
		}
		$this->fillAttr($attr, 'field', $field);

		return $this->fields[$field]->render($value, $attr);
	}

	//--------------------------------------------------------------------------

	public function label($field, $attr = array())
	{
		$macroResult = $this->runMacros('label', func_get_args());
		if ($macroResult !== null) {
			return $macroResult;
		}

		$this->fillAttr($attr, 'label', $field);

		$for = isset($attr['for']) ? $attr['for'] : $this->fieldName($field);
		return Form::label($for, $this->fieldLabel($field), $attr);
	}

	//--------------------------------------------------------------------------

	public function error($field, $attr = array())
	{
		$fieldName = $this->fieldName($field);
		$error = $this->fieldError($fieldName);
		$this->fillAttr($attr, 'error', $field);
		return $error ? $this->elem('div', $attr, $error) : null;
	}

	//--------------------------------------------------------------------------

	public function fieldProperty($field, $option, $value = null)
	{
		if ($value !== null) {
			$this->fields[$field]->$option = $value;
		}

		$value = isset($this->fields[$field]->$option) ? $this->fields[$field]->$option : null;

		return $value;
	}

	//--------------------------------------------------------------------------

	public function fieldLabel($field, $value = null)
	{
		return $this->fieldProperty($field, 'label', $value);
	}

	//--------------------------------------------------------------------------

	public function fieldValue($field, $value = null)
	{
		if (is_null($value)) {
			$value = isset($this->data[$field]) ? $this->data[$field] : null;
		}

		return $this->fieldProperty($field, 'value', $value);
	}

	//--------------------------------------------------------------------------

	public function fieldRequired($field)
	{
		return $this->fields[$field]->isRequired($this->rulesKey);
	}

	//--------------------------------------------------------------------------

	public function fieldError($field)
	{
		if ( ! $this->errors) {
			return;
		}

		return $this->errors->has($field) ? $this->errors->first($field) : null;
	}

	//--------------------------------------------------------------------------

	public function fieldName($field)
	{
		return $this->fieldPrefix . $field;
	}

	//--------------------------------------------------------------------------

	public function fieldType($field, $value = null)
	{
		return  $this->fieldProperty($field, 'type', $value);
	}

	//--------------------------------------------------------------------------

	public function fieldOptions($field, $value = null)
	{
		return $this->fieldProperty($field, 'options', $value);
	}

	//--------------------------------------------------------------------------

	public function __call($method, $args = array())
	{
		if (isset($this->macros[$method])) {
			array_unshift($args, $this);

			return call_user_func_array($this->macros[$method], $args);
		}

		$macroMethod = 'macro' . $method;
		if (method_exists($this, $macroMethod)) {
			return call_user_func_array(array($this, $macroMethod), $args);
		}

		throw new Exception("Method {$method} not found", 1);
	}

	//--------------------------------------------------------------------------

	protected function runMacros($name, $args)
	{
		static $runMacros = array();

		if (isset($this->macros[$name]) && empty($runMacros[$name])) {
			array_unshift($args, $this);
			$runMacros[$name] = true;
			$result           = call_user_func_array($this->macros[$name], $args);
			$runMacros[$name] = false;

			return $result . '';
		}

		return null;
	}

	//--------------------------------------------------------------------------

}