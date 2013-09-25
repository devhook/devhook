<?php namespace Devhook;

use Intervention\Image\Image as IMG;
use Response;
use Redirect;
use Request;
use Config;
use File;

class ImageController extends \Controller
{
	// public function getIndex() {
	// 	return 'ok';
	// }

	public function getSetDefault($id, $field)
	{
		$image     = Image::find($id);
		$modelName = ucfirst($image->imageable_type);
		$model     = $modelName::find($image->imageable_id);

		$image->clearCache();

		Image::where('imageable_type', $image->imageable_type)->where('imageable_id', $image->imageable_id)->where('primary', 1)->update(array('primary' => 0));

		$image->primary = 1;
		$image->forceSave();

		if ($model) {
			$model->$field = $image->path;
			$model->forceSave();
		}

		return Redirect::back();
	}

	//--------------------------------------------------------------------------

	public function missingMethod($args)
	{
		if (count($args) < 3) {
			return parent::missingMethod($args);
		}

		if ($args[0] == 'force') {
			array_shift($args);
		}
		$modelKeyword = $args[0];
		$modelClass   = \Devhook::getClassByKey($modelKeyword);
		$id           = $args[1];
		$file         = $args[2];

		$parts = explode('-', $file);

		if (count($parts) == 3) {
			list($field, $sizeKey, $file) = $parts;
			@list($imageId, $ext) = explode('.', $file);
		} else {
			$imageId = false;
			@list($field, $file) = $parts;
			@list($sizeKey, $ext) = explode('.', $file);
		}

		if (!$field || !$sizeKey || $ext != 'jpg') {
			return parent::missingMethod($args);
		}

		$model = $modelClass::find($id);

		if (!$model || !$model->$field) {
			return parent::missingMethod($args);
		}

		// $fileExt = pathinfo($model->$field, PATHINFO_EXTENSION);

		$fields     = (array) $model->fields();
		$modelField = isset($fields[$field]) ? $fields[$field] : false;

		if (!$modelField || empty($modelField->type)) {
			return parent::missingMethod($args);
		}

		$imageTypes = array('image');
		$type       = $modelField->type;
		$sizes      = (array) $modelField->settings('sizes');
		if (!in_array($type, $imageTypes) || empty($sizes[$sizeKey])) {
			return parent::missingMethod($args);
		}
		$size = $sizes[$sizeKey];

		$width   = $size[0];
		$hegiht  = $size[1];
		$crop    = isset($size[2]) ? $size[2] : false;
		$quality = isset($size[3]) ? $size[3] : 95;

		if ($imageId) {
			$imageFile = \Image::where('imageable_type', $modelKeyword)->where('imageable_id', $id)->find($imageId);
			if (!$imageFile) {
				return parent::missingMethod($args);
			}
			$imageFile = $imageFile->path;
		} else {
			$imageFile = $model->$field;
		}

		$image = IMG::make(public_path($imageFile));

		if ($crop) {
			$image->grab($width, $hegiht);
		} else {
			$image->resize($width, $hegiht, true);
		}

		$tmpFile = public_path($model->$field) . '.tmp_' . uniqid();
		$newFile = public_path(Config::get('devhook.imageRoute') . '/' . implode('/', $args));
		$newPath = dirname($newFile);

		if (!File::exists($newPath)) {
			File::makeDirectory($newPath, 0777, true);
		}

		$image->save($newFile, $quality);
		$img = $image->encode($ext, $quality);

		$response = Response::make($img, 200);
		$response->header('Content-Type', 'image/jpeg');

		return $response;
	}

}