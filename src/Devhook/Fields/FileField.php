<?php namespace Devhook\Fields;

use \Config;
use \Input;
use \Page;
use \File;
use \Form;
use \View;

class FileField extends Field {

	//--------------------------------------------------------------------------

	const SIMPLE_MODE  = 'simple';
	const DEFAULT_MODE = 'default';

	protected static $mode = self::DEFAULT_MODE;

	//--------------------------------------------------------------------------

	/**
	 * Генерирует html-поле
	 *
	 * @param [type] $form [description]
	 * @param [type] $field [description]
	 * @param [type] $value [description]
	 * @param [type] $attr [description]
	 *
	 * @return [type]
	 */
	public static function makeField($form, $field, $value, $attr)
	{
		$mode = static::fieldSettings('mode');
		if (!$mode) $mode = static::$mode;

		switch ($mode) {
			case self::DEFAULT_MODE;
				$view = View::make('fields/file');

				$view->with('settings', static::fieldSettings());
				$view->with('mode',     $mode);
				$view->with('mode',     $mode);
				$view->with('form',     $form);
				$view->with('field',    $field);
				$view->with('file',     $value);
				$view->with('model',    $form->model);
				$view->with('attr',     $attr);

				if ($value) {
					$view->with('filename',     pathinfo($value, PATHINFO_BASENAME));
					$view->with('filesize',     File::size(public_path($value)));
					$view->with('removeAction', \Admin::url('action/delete', $form->model->modelFullKeyword(), $form->model->id, $field));
				}

				return $view;

			default:
				return Form::file($field, $attr);
		}

	}

	//--------------------------------------------------------------------------

	public static function setValue($model, $field, $data)
	{
		if (empty($data[$field])) {
			return;
		}

		$oldfile = $model->getAttribute($field);

		if ($newfile = static::saveFile($model, $field, $data[$field])) {
			$model->$field = $newfile;

			$model->event('saved', function($model) use($field) {
				\FileField::afterSave($model, $field);
			});

			// Удаляем старый файл
			if ($oldfile && $oldfile != $newfile) {
				File::delete(public_path($oldfile));
			}
		}
	}

	//--------------------------------------------------------------------------

	public static function saveFile($model, $field, $file = null)
	{
		static::init($model, $field);
		$path = static::fieldSettings('path', function() use ($model, $field) {
			return Config::get('devhook.publicFilesPath') . '/' . $model->modelKeyword() . '/' . $field;
		});
		// die('ok');

		if (empty($file)) {
			$file = Input::file($field);
		}
		elseif (is_string($file)) {
			$file = new \Symfony\Component\HttpFoundation\File\File($file, false);
		}

		if ( ! (is_object($file) && file_exists($file))) {
			return;
		}

		$ext      = '.' . (method_exists($file, 'getClientOriginalExtension') ? $file->getClientOriginalExtension() : $file->getExtension());
		$absPath  = public_path($path);
		$fileName = uniqid() . $ext;
		$newFile  = $path . '/' . $fileName;

		if (!File::exists($absPath)) {
			File::makeDirectory($absPath, 0777, true);
		}

		if (File::copy($file, public_path($newFile))) {
			return $newFile;
		}
	}

	//--------------------------------------------------------------------------

	public static function afterSave($model, $field)
	{
		static::init($model, $field);

		$oldFile = $model->$field;

		if (!$oldFile) {
			return;
		}

		$absFile = public_path($oldFile);

		if (!file_exists($absFile)) {
			return;
		}

		if ($moveFile = static::fieldSettings('moveFile')) {
			$fileObj = new \Symfony\Component\HttpFoundation\File\File($absFile, false);

			if (!($newFile = $moveFile($model, $fileObj))) {
				return;
			}

			if ($newFile == $oldFile) {
				return;
			}

			$absPath = dirname(public_path($newFile));

			if (!File::exists($absPath)) {
				File::makeDirectory($absPath, 0777, true);
			}

			if (File::copy(public_path($oldFile), public_path($newFile))) {

				// Удаляем старый файл
				if ($oldFile && $oldFile != $newFile) {
					File::delete(public_path($oldFile));
				}

				$model->$field = $newFile;
				$model->forceSave();
			}
		}
	}

	//--------------------------------------------------------------------------

	public static function setRules($model, $field, $rules)
	{
		if ($rules && is_string($rules)) {
			$rules = explode('|', $rules);
		} else {
			$rules = array();
		}

		if ($mimes = static::fieldSettings('mimes')) {
			$rules[] = 'mimes:' . $mimes;
		}

		return $rules;
	}

	//--------------------------------------------------------------------------


}