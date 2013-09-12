<?php namespace Devhook\Fields;

use Intervention\Image\Image as IMG;
use \Input;
use \Image;
use \Config;
use \View;
use \File;
use \Form;
use \HTML;

class ImageField extends FileField {

	//--------------------------------------------------------------------------

	const DEFAULT_MODE  = 'default';
	const SIMPLE_MODE   = 'simple';
	const MULTIPLE_MODE = 'multiple';

	//--------------------------------------------------------------------------

	protected static $imageRoute;

	//--------------------------------------------------------------------------

	public static function boot()
	{
		self::$imageRoute = Config::get('devhook.imageRoute');
	}

	//--------------------------------------------------------------------------

	protected function init()
	{
		if (empty($this->settings['mimes'])) {
			$this->settings['mimes'] = 'jpeg,bmp,png,gif';
		}
	}

	//--------------------------------------------------------------------------

	public function render($value, $attr)
	{
		$model = $this->model;

		switch ($this->settings('mode'))
		{
			case self::SIMPLE_MODE:
				return Form::file($this->name, $attr);

			case self::MULTIPLE_MODE:
				$view   = View::make('fields/image_multiple');
				$method = $this->name . 's';
				$images = $model->$method;

				$view->with('images', $images);
				$view->with('columns', $this->settings('columns', 5));
				break;

			default:
				$view = View::make('fields/image');
				$view->with('image', $value);
				if ($value) {
					$exists = file_exists(public_path($value));
					$view->with('filename',     pathinfo($value, PATHINFO_BASENAME));
					$view->with('filesize',     $exists ? File::size(public_path($value)) : 0);
					$view->with('removeAction', \Admin::url('action/delete', $model->modelFullKeyword(), $model->id, $this->name));
				}
				break;
		}

		$sizeKey = false;
		if ($sizes = $this->settings('sizes')) {
			$keys    = array_keys($sizes);
			$sizeKey = current($keys);
		}

		$view->with('field',   $this->name);
		$view->with('attr',    $attr);
		$view->with('sizeKey', $sizeKey);

		return $view;
	}

	//--------------------------------------------------------------------------

	public function setValue($data)
	{
		$mode = $this->settings('mode', self::DEFAULT_MODE);
		$name = $this->name;

		if ($mode == self::DEFAULT_MODE) {
			return parent::setValue($data);
		}

		if (empty($data[$name])) {
			return;
		}


		if ($newfile = $this->saveFile($data[$name])) {
			if ( ! $model->$name) {
				$model->$name = $newfile;
			}

			$model->event('saved', array($this, 'afterSave'));
		}
	}

	//--------------------------------------------------------------------------

	public function afterSave()
	{
		$name     = $this->name;
		$model    = $this->model;
		$filepath = $model->$name;

		if ( ! $filepath) {
			return;
		}

		$absFile = public_path($filepath);

		if (!file_exists($absFile)) {
			return;
		}

		$image = new Image;
		$image->imageable_id   = $model->getKey();
		$image->imageable_type = $model->modelKeyword();
		$image->path           = $filepath;
		$image->primary        = $filepath == $model->getAttribute($name);
		$image->forceSave();


		if ($moveFile = $this->settings('moveFile')) {
			$fileObj = new \Symfony\Component\HttpFoundation\File\File($absFile, false);
			$newFile = $moveFile($model, $fileObj, $image);

			if ($newFile == $filepath) {
				return;
			}

			$absPath = dirname(public_path($newFile));

			if (!File::exists($absPath)) {
				File::makeDirectory($absPath, 0777, true);
			}

			if (File::copy(public_path($filepath), public_path($newFile))) {

				// Удаляем старый файл
				if ($filepath && $filepath != $newFile) {
					File::delete(public_path($filepath));
				}

				$model->$name = $newFile;
				$model->forceSave();

				$image->path = $newFile;
				$image->forceSave();
			}
		}
	}

	//--------------------------------------------------------------------------

	public function adminValueMutator($row = null, $field = null)
	{
		return HTML::image($row->$field, null, array('style'=>'max-height:34px; margin:-7px 0'));
	}

	//--------------------------------------------------------------------------
}