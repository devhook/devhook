<?php namespace Devhook\Fields;

use \Page;
use \View;

class ToggleField extends Field {

	//--------------------------------------------------------------------------

	public static function makeField($form, $field, $value, $attr)
	{
		$view = View::make('fields/toggle');

		$id = 'i_toggle_field_' . $form->fieldName($field);

		$view->with('settings',  static::fieldSettings());
		// $view->with('editor', $editor);
		$view->with('id',        $id);
		$view->with('form',      $form);
		$view->with('field',     $field);
		$view->with('value',     $value);
		$view->with('model',     $form->model);
		$view->with('attr',      $attr);

		return $view;
		// return \iElem::make('input')->attr($attr); // "<input type='color' name='{$field}' value='{$value}' />";
	}

	//--------------------------------------------------------------------------
}