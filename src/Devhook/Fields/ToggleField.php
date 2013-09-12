<?php namespace Devhook\Fields;

use \View;

class ToggleField extends BaseField
{

	//--------------------------------------------------------------------------

	public function render($value, $attr)
	{
		$view = View::make('fields/toggle');

		$name = $this->name;
		$id   = 'i_toggle_field_' . $name;

		$view->with('id',    $id);
		$view->with('field', $name);
		$view->with('value', $value);
		$view->with('attr',  $attr);

		return $view;
	}

	//--------------------------------------------------------------------------

}
