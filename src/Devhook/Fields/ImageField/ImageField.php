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
	protected static $globalSizes;
	protected static $adminImageSizeKey;

	//--------------------------------------------------------------------------

	public static function boot()
	{
		self::$imageRoute        = Config::get('devhook.imageRoute');
		self::$globalSizes       = Config::get('devhook.imageFieldSizes');
		self::$adminImageSizeKey = Config::get('devhook.adminImageSizeKey');
	}

	//--------------------------------------------------------------------------

	protected function init()
	{
		if (empty($this->settings['mimes'])) {
			$this->settings['mimes'] = 'jpeg,bmp,png,gif';
		}

		if (empty($this->settings['model'])) {
			$this->settings['model'] = 'Image';
		}

		$this->settings['sizes'] = array_merge(self::$globalSizes, (array) $this->settings['sizes']);
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
					$view->with('removeAction', URL::to(Devhook::backendRoute('action/delete', $model->getModelFullKeyword(), $model->id, $this->name)));
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

		if ($mode != self::MULTIPLE_MODE) {
			return parent::setValue($data);
		}

		if (empty($data[$name])) {
			return;
		}


		if ($newfile = $this->saveFile($data[$name])) {

			if (!$this->model->$name) {
				$this->model->$name = $newfile;
			}

			$modelClass = get_class($this->model);
			$field      = $this;
			$modelClass::saved(function() use ($field, $newfile) {
				$field->afterSave($newfile);
			});
		}
	}

	//--------------------------------------------------------------------------

	public function afterSave($newfile=null)
	{
		$name     = $this->name;
		$model    = $this->model;
		$filepath = $newfile;

		if ( ! $filepath) {
			return;
		}

		$absFile = public_path($filepath);

		if (!file_exists($absFile)) {
			return;
		}
		$imageModel = $this->settings('model');
		$image = new $imageModel;
		$image->imageable_id   = $model->getKey();
		$image->imageable_type = $model->getModelKeyword();
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

	//-------------------------------------------------------------------------

	// public function url()
	// {
	// 	$force = false;
	// 	$force = $force ? 'force/' : '';

	// 	// return self::$imageRoute . $force . $this->imageable_type . '/' . $this->imageable_id;
	// }

	//--------------------------------------------------------------------------

	public function adminValueMutator($row = null, $field = null)
	{
		if ($row->$field) {
			$img =  Config::get('devhook.imageRoute') . '/' . $row->getModelKeyword() . '/' . $row->getKey() . '/' . $field . '-' . self::$adminImageSizeKey . '.jpg';
			return HTML::image($img, null, array('style'=>'max-height:34px; margin:-7px 0'));
		}
	}

	//--------------------------------------------------------------------------
}