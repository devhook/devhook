<?php namespace Devhook\Fields;

use \Config;
use \Input;
use \Page;
use \File;
use \Form;
use \View;

class FileField extends BaseField {

	//--------------------------------------------------------------------------

	const SIMPLE_MODE  = 'simple';
	const DEFAULT_MODE = 'default';

	//--------------------------------------------------------------------------

	public function render($value, $attr)
	{
		switch ($this->settings('mode')) {
			case self::SIMPLE_MODE;
				return Form::file($this->name, $attr);

			default:
				$view = View::make('fields/file');

				$view->with('field',    $this->name);
				$view->with('file',     $value);
				$view->with('attr',     $attr);

				if ($value) {
					$view->with('filename',     pathinfo($value, PATHINFO_BASENAME));
					$view->with('filesize',     File::size(public_path($value)));
					$view->with('removeAction', URL::to(Devhook::backendRoute('action/delete', $this->model->getModelFullKeyword(), $this->model->id, $this->name)));
				}

				return $view;
		}

	}

	//--------------------------------------------------------------------------

	public function setValue($data)
	{
		$name  = $this->name;
		$model = $this->model;

		if (empty($data[$name])) {
			return;
		}

		$oldfile = $model->getAttribute($name);

		if ($newfile = $this->saveFile($data[$name])) {
			$model->$name = $newfile;

			$modelClass = get_class($this->model);
			$field      = $this;
			$modelClass::saved(function() use ($field) {
				$field->afterSave();
			});

			// Удаляем старый файл
			if ($oldfile && $oldfile != $newfile) {
				File::delete(public_path($oldfile));
			}
		}
	}

	//--------------------------------------------------------------------------

	public function saveFile($file = null)
	{
		$model = $this->model;
		$name  = $this->name;
		$path  = $this->settings('path', function() use ($model, $name) {
			return Config::get('devhook.publicFilesPath') . '/' . $model->getModelKeyword() . '/' . $name;
		});


		if (empty($file)) {
			$file = Input::file($name);
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

	public function afterSave()
	{
		$model   = $this->model;
		$name    = $this->name;

		$oldFile = $model->$name;

		if (!$oldFile) {
			return;
		}

		$absFile = public_path($oldFile);

		if (!file_exists($absFile)) {
			return;
		}

		if ($moveFile = $this->settings('moveFile')) {
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

				$model->$name = $newFile;
				$model->forceSave();
			}
		}
	}

	//--------------------------------------------------------------------------

	protected function rulesMutator($rules)
	{
		if ($rules && is_string($rules)) {
			$rules = explode('|', $rules);
		} else {
			$rules = array();
		}

		if ($mimes = $this->settings('mimes')) {
			$rules[] = 'mimes:' . $mimes;
		}

		return $rules;
	}

	//--------------------------------------------------------------------------


}