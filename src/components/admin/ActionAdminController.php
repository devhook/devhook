<?php namespace Devhook;

use \Redirect;
use \Input;
use \View;
use \File;

class ActionAdminController extends \AdminController {

	//--------------------------------------------------------------------------

	public function anySetMode($mode)
	{
		switch ($mode) {
			case 'admin':
				return Redirect::to(\Admin::url());
				break;

			// case 'edit':
			// 	return Redirect::to('/');
			// 	break;

			default:
				return Redirect::to('/');
				break;
		}
	}

	//--------------------------------------------------------------------------

	public function getDelete($modelKey, $id, $fieldName = null)
	{
		$modelClass = \Devhook::getClassByKey($modelKey);
		$model      = $modelClass::find($id);

		if (is_null($fieldName)) {
			$model->delete();
		} else {
			if (File::delete(public_path($model->$fieldName))) {
				$model->$fieldName = '';
				$model->forceSave();
			}
		}

		return Redirect::back();
	}

	//--------------------------------------------------------------------------

	public function getEnabled($modelKey, $id, $enabled = 1)
	{
		$modelClass = \Devhook::getClassByKey($modelKey);
		$model      = $modelClass::find($id);

		$model->enabled = (int) (bool) $enabled;
		$model->forceSave();

		return Redirect::back();
	}

	//--------------------------------------------------------------------------

	public function missingMethod($params)
	{
		return 'AdminController';
	}

}