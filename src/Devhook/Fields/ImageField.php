<?php namespace Devhook\Fields;

use Intervention\Image\Image as IMG;
use \Input;
use \Image;
use \Config;
use \View;
use \File;
use \Form;

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

	public static function makeField($form, $field, $value, $attr)
	{
		$mode = static::fieldSettings('mode', self::DEFAULT_MODE);

		$model = $form->model;

		switch ($mode)
		{
			case self::DEFAULT_MODE:
				$view = View::make('fields/image');
				$view->with('image', $value);
				if ($value) {
					$exists = file_exists(public_path($value));
					$view->with('filename',     pathinfo($value, PATHINFO_BASENAME));
					$view->with('filesize',     $exists ? File::size(public_path($value)) : 0);
					$view->with('removeAction', \Admin::url('action/delete', $model->modelFullKeyword(), $model->id, $field));
				}
				break;

			case self::MULTIPLE_MODE:
				$view   = View::make('fields/image_multiple');
				$method = $field . 's';
				$images = $model->$method;

				$view->with('images', $images);
				$view->with('columns', static::fieldSettings('columns', 5));
				break;

			default:
				return Form::file($field, $attr);
		}

		$sizeKey = false;
		if ($sizes = static::fieldSettings('sizes')) {
			$keys    = array_keys($sizes);
			$sizeKey = current($keys);
		}

		$view->with('settings', static::fieldSettings());
		$view->with('mode',     $mode);
		$view->with('form',     $form);
		$view->with('field',    $field);
		$view->with('model',    $model);
		$view->with('attr',     $attr);
		$view->with('sizeKey',  $sizeKey);

		return $view;
	}

	//--------------------------------------------------------------------------

	public static function setValue($model, $field, $data)
	{
		parent::init($model, $field);

		$mode = static::fieldSettings('mode', self::DEFAULT_MODE);

		if ($mode == self::DEFAULT_MODE) {
			return parent::setValue($model, $field, $data);
		}

		if (empty($data[$field])) {
			return;
		}


		if ($newfile = static::saveFile($model, $field, $data[$field])) {
			if ( ! $model->$field) {
				$model->$field = $newfile;
			}

			$model->event('saved', function($model) use($field, $newfile) {
				\ImageField::afterSave($model, $field, $newfile);
			});
		}
	}

	//--------------------------------------------------------------------------

	public static function afterSave($model, $field, $filepath = null)
	{
		static::init($model, $field);

		if ( ! $filepath) {
			$filepath = $model->$field;
		}

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
		$image->primary        = $filepath == $model->getAttribute($field);
		$image->forceSave();


		if ($moveFile = static::fieldSettings('moveFile')) {
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

				$model->$field = $newFile;
				$model->forceSave();

				$image->path = $newFile;
				$image->forceSave();
			}
		}
	}

	//--------------------------------------------------------------------------

	public static function imageUrl($model, $field, $sizeKey, $force)
	{
		if ($model->$field) {
			return asset(self::$imageRoute . '/' . ($force ? 'force/' : '') . $model->modelKeyword() . '/' . $model->getKey() . '/' . $field . '-' . $sizeKey . '.jpg');
		}
	}

	//--------------------------------------------------------------------------

	public static function makeImage($model, $field, $sizeKey, $force)
	{
		if ($src = static::imageUrl($model, $field, $sizeKey, $force)) {
			return "<img src='{$src}' alt='' />";
		}
	}

	//--------------------------------------------------------------------------

	public function adminValueMutator($model = null, $field = null)
	{
		$fields = $model->fields();

		// if (empty($fields[$field]->field)) {
		// 	return null;
		// }
		return '{IMG}';
		// $fieldOpt = $fields[$field]['field'];
		// return function($row) use ($model, $field, $fieldOpt) {
		// 	$sizeKey = current(array_keys($fieldOpt['sizes']));
		// 	$src = static::imageUrl($row, $field, $sizeKey, false);
		// 	return "<img src='{$src}' style='max-height:34px; margin:-7px 0' />";
		// };
	}

	//--------------------------------------------------------------------------
}