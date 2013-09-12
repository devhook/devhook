<?php namespace Devhook\Fields;



class Field // implements \ArrayAccess
{
	//--------------------------------------------------------------------------

	protected static $registeredFields = array();

	//--------------------------------------------------------------------------

	public static function register($name, $className)
	{
		$className::boot();
		static::$registeredFields[$name] = $className;
	}

	//--------------------------------------------------------------------------

	public static function make($name, $field, $model)
	{
		if (!$field || empty($field['field'])) {
			return;
		}

		// if (!static::exists($field)) {
		// 	return false;
		// }

		$type = $field['field'];
		if (is_array($type)) {
			$type = @$type['type'];
		}

		$fieldClass = isset(static::$registeredFields[$type]) ? static::$registeredFields[$type] : 'BaseField';

		return new $fieldClass($name, $field, $model);
	}

	//--------------------------------------------------------------------------
	//--------------------------------------------------------------------------
	//--------------------------------------------------------------------------

	//--------------------------------------------------------------------------

	// public static function boot() {}

	// //--------------------------------------------------------------------------

	// public static function init($model, $field)
	// {
	// 	static $currentModel;
	// 	static $currentField;

	// 	if ($currentModel != $model->modelKeyword() || $currentField != $field)
	// 	{
	// 		$currentModel = $model->modelKeyword();
	// 		$currentField = $field;

	// 		$fileField = $model->fields($field);

	// 		static::fieldSettings((array) @$fileField['field']);
	// 	}
	// }

	//--------------------------------------------------------------------------

	// public static function fieldSettings($settings = null, $default = null)
	// {
	// 	static $fieldSettings;

	// 	if (is_array($settings)) {
	// 		$fieldSettings = $settings;
	// 	}
	// 	elseif(is_string($settings)) {
	// 		return isset($fieldSettings[$settings]) ? $fieldSettings[$settings] : value($default);
	// 	}

	// 	return $fieldSettings;
	// }

	//--------------------------------------------------------------------------

	// public static function renderField($form, $field, $value, $attr)
	// {
	// 	return self::makeField($form, $field, $value, $attr);
	// }
	// public static function makeField($form, $field, $value, $attr)
	// {
	// 	return \Form::text($field, $value, $attr);
	// }

	//--------------------------------------------------------------------------

	// public static function setValue($model, $field, $data)
	// {
	// 	if (isset($data[$field])) {
	// 		$model->$field = $data[$field];
	// 	}
	// }

	//--------------------------------------------------------------------------



	//--------------------------------------------------------------------------

	// public function valueMutator()
	// {
	// 	return null;
	// }

	//--------------------------------------------------------------------------

	// public static function afterSave($model, $field)
	// {

	// }

	//--------------------------------------------------------------------------

	// public static function afterInsert($model, $field)
	// {

	// }

	//--------------------------------------------------------------------------
}