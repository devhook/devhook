<?php namespace Devhook\Fields;

use \Page;
use \View;

class HtmlField extends BaseField {

	//--------------------------------------------------------------------------

	const CKEDITOR = 'ckeditor';
	const TINYMCE  = 'tinymce';

	//--------------------------------------------------------------------------

	protected static $editor = self::CKEDITOR;

	//--------------------------------------------------------------------------

	public function render($value, $attr)
	{
		$editor = 'ckeditor';
		if (!$editor) $editor = static::$editor;

		$view = View::make("fields/html_{$editor}");

		$attr['id'] = 'i_html_field_' . $this->name;

		// $view->with('settings', static::fieldSettings());
		$view->with('editor',   $editor);
		$view->with('field',    $this->name);
		$view->with('value',    $value);
		$view->with('model',    $this->model);
		$view->with('attr',     $attr);

		return $view;
	}

	//--------------------------------------------------------------------------
}