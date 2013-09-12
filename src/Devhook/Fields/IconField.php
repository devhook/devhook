<?php namespace Devhook\Fields;

use \View;

class IconField extends BaseField
{

	//--------------------------------------------------------------------------

	public function render($value, $attr)
	{
		$view = View::make('fields/Icon');

		$attr['id'] = 'i_html_field_' . $this->name;

		$view->with('field', $this->name);
		$view->with('value', $value);
		$view->with('attr',  $attr);

		return $view;
	}

	//--------------------------------------------------------------------------
}