<?php namespace Devhook;

use \Devhook;
use \Config;
use \File;
use \URL;

class Image extends \Model {

	protected $table = 'images';

	protected $parent;
	protected $parentField = 'image';

	//--------------------------------------------------------------------------

	protected function initFields()
	{
		return array(
			'id'             => array(),
			'path'           => array(),
			'imageable_id'   => array(),
			'imageable_type' => array(),
			'primary'        => array(),
			'updated_at'     => array(),
			'created_at'     => array(),
		);
	}

	//--------------------------------------------------------------------------

	public static function boot()
	{
		parent::boot();

		static::deleting(function($model){
			File::delete(public_path($model->path));
		});
	}

	//--------------------------------------------------------------------------

	// public static function saveImage($model, $file, $field = 'image')
	// {
	// 	return ImageField::saveFile($model, $file, $field);
	// }

	//--------------------------------------------------------------------------

	public function setParent($model, $field = 'image')
	{
		$this->parent      = $model;
		$this->parentField = $field;
	}

	//--------------------------------------------------------------------------

	public function src($type)
	{

		$force = false;
		// $force = true;
		// return \ImageField::imageUrl($this->parent, $this->parentField, $type, $force);

		$id = $this->primary ? '' : '-' . $this->getKey();
		return asset($this->dir($force) . '/' . $this->parentField . '-' . $type . $id . '.jpg');
	}

	//--------------------------------------------------------------------------

	public function dir($force = false)
	{
		static $imageRoute;

		$force = $force ? 'force/' : '';

		if (!$imageRoute) {
			$imageRoute = Config::get('devhook.imageRoute') . '/';
		}

		return $imageRoute . $force . $this->imageable_type . '/' . $this->imageable_id;
	}

	//--------------------------------------------------------------------------

	public function setDefaultAction($field = null)
	{
		return URL::to(Config::get('devhook.imageRoute') . '/set-default/' . $this->id . ($field ? "/{$field}" : ''));
	}

	//--------------------------------------------------------------------------

	public function removeAction()
	{
		return URL::to(Devhook::backendRoute('action/delete', $this->getModelFullKeyword(), $this->id));
	}

	//--------------------------------------------------------------------------

	public function clearCache()
	{
		$dir = public_path($this->dir());

		File::deleteDirectory($dir);
	}

	//--------------------------------------------------------------------------
}