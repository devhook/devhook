<?php namespace Devhook\Fields;

use \View;

class ColorField extends BaseField
{

	//--------------------------------------------------------------------------

	public function render($value, $attr)
	{
		$view = View::make('fields/color');

		$attr['id'] = 'i_html_field_' . $this->name;

		$view->with('field', $this->name);
		$view->with('value', $value);
		$view->with('attr',  $attr);

		return $view;
	}

	//--------------------------------------------------------------------------
}