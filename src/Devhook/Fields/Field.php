<?php namespace Devhook\Fields;



class Field {

	//--------------------------------------------------------------------------

	public static function boot() {}

	//--------------------------------------------------------------------------

	public static function init($model, $field)
	{
		static $currentModel;
		static $currentField;

		if ($currentModel != $model->modelKeyword() || $currentField != $field)
		{
			$currentModel = $model->modelKeyword();
			$currentField = $field;

			$fileField = $model->fields($field);

			static::fieldSettings((array) @$fileField['field']);
		}
	}

	//--------------------------------------------------------------------------

	public static function fieldSettings($settings = null, $default = null)
	{
		static $fieldSettings;

		if (is_array($settings)) {
			$fieldSettings = $settings;
		}
		elseif(is_string($settings)) {
			return isset($fieldSettings[$settings]) ? $fieldSettings[$settings] : value($default);
		}

		return $fieldSettings;
	}

	//--------------------------------------------------------------------------

	public static function makeField($form, $field, $value, $attr)
	{
		return \Form::text($field, $value, $attr);
	}

	//--------------------------------------------------------------------------

	public static function setValue($model, $field, $data)
	{
		if (isset($data[$field])) {
			$model->$field = $data[$field];
		}
	}

	//--------------------------------------------------------------------------

	public static function setRules($model, $field, $rules)
	{
		return $rules;
	}

	//--------------------------------------------------------------------------

	public static function adminFieldMutator()
	{
		return null;
	}

	//--------------------------------------------------------------------------

	public static function afterSave($model, $field)
	{

	}

	//--------------------------------------------------------------------------

	public static function afterInsert($model, $field)
	{

	}

	//--------------------------------------------------------------------------
}