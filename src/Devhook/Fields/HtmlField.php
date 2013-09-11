<?php namespace Devhook\Fields;

use \Page;
use \View;

class HtmlField extends Field {

	//--------------------------------------------------------------------------

	const CKEDITOR = 'ckeditor';
	const TINYMCE  = 'tinymce';

	//--------------------------------------------------------------------------

	protected static $editor = self::CKEDITOR;

	//--------------------------------------------------------------------------

	public static function makeField($form, $field, $value, $attr)
	{
		$editor = static::fieldSettings('editor');
		if (!$editor) $editor = static::$editor;

		$view = View::make("fields/html_{$editor}");

		$attr['id'] = 'i_html_field_' . $form->fieldName($field);

		$view->with('settings', static::fieldSettings());
		$view->with('editor',   $editor);
		$view->with('form',     $form);
		$view->with('field',    $field);
		$view->with('value',    $value);
		$view->with('model',    $form->model);
		$view->with('attr',     $attr);

		return $view;
	}

	//--------------------------------------------------------------------------
}