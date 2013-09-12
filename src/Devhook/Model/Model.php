<?php namespace Devhook;

use Devhook\Fields\Field;
use \ImageField;
use \Validator;
use \Config;
use \Input;
use \Schema;
use \Admin;
use \URL;

class Model extends \Illuminate\Database\Eloquent\Model {

	//--------------------------------------------------------------------------

	protected $modelName;

	protected $modelKeyword;     // model
	protected $modelFullKeyword; // name.space.model
	protected $modelClass;       // Name\Space\Model

	// protected static $imageFields = array();


	// protected $adminController = true;
	// protected $adminSettings   = array();

	protected $objectEvents = array();
	protected $valid = false;
	protected $validator;
	protected $fields;

	//--------------------------------------------------------------------------

	protected static function boot()
	{
		parent::boot();

		static::updating(function($model){
			$fields = $model->fields();

			if (isset($fields['updater_id'])) {
				$model->updater_id = app('user')->id;
			}
		});

		static::saved(function($model) {
			$model->callEvent('saved');
		});
	}

	//--------------------------------------------------------------------------

	public function __construct(array $attributes = array())
	{
		$this->modelClass       = get_class($this);
		$this->modelFullKeyword = strtolower(str_replace('\\', '.', $this->modelClass));

		if (is_null($this->modelKeyword)) {
			$class = $this->modelClass;
			if ($classNamePos = strrpos($this->modelClass, '\\')) {
				$class = substr($class, $classNamePos+1);
			}
			$this->modelKeyword = strtolower($class);
		}

		parent::__construct($attributes);
	}

	//--------------------------------------------------------------------------

	//FIXME: поробовать избавитсься от этого
	// public function newFromBuilder($attributes = array())
	// {
	// 	$instance = parent::newFromBuilder($attributes);

	// 	$instance->fields();

	// 	return $instance;
	// }

	//--------------------------------------------------------------------------

	public function event($name, $callback, $key = null)
	{
		if ($key) {
			$this->objectEvents[$name][$key] = $callback;
		} else {
			$this->objectEvents[$name][] = $callback;
		}
	}

	//--------------------------------------------------------------------------

	public function callEvent($name)
	{
		if (isset($this->objectEvents['saved'])) {
			foreach ($this->objectEvents['saved'] as $callback) {
				$callback($this);
			}
		}
	}

	//--------------------------------------------------------------------------

	public function images()
	{
		return $this->morphMany('Image', 'imageable');
	}

	//--------------------------------------------------------------------------

	// public function image($size = null, $attr = array())
	// {
	// 	$force = false;

	// 	if ($this->image && isset(static::$imageFields['image'])) {
	// 		if (!$size) {
	// 			$size = static::$imageFields['image']['default_size'];
	// 		}

	// 		$src = ImageField::imageUrl($this, 'image', $size, $force);

	// 		return app('html')->image($src, null, $attr);
	// 	}

	// 	return '';
	// }

	//--------------------------------------------------------------------------

	// protected function getImagesAttribute()
	// {
	// 	if (isset($this->relations['images'])) {
	// 		$images = $this->getRelation('images');
	// 	} else {
	// 		$images = $this->images()->get();
	// 	}
	// 	foreach ($images as $row) $row->setParent($this, 'image');
	// 	return $images;
	// }

	/***************************************************************************
		Api
	***************************************************************************/

	public function modelName()
	{
		return $this->modelName ? $this->modelName : $this->modelKeyword;
	}

	//--------------------------------------------------------------------------

	public function modelFullKeyword()
	{
		return $this->modelFullKeyword;
	}

	//--------------------------------------------------------------------------

	public function modelKeyword()
	{
		return $this->modelKeyword;
	}

	//--------------------------------------------------------------------------

	public function link($title = null, $attributes = array(), $secure = null)
	{
		static $link;

		if ($link === null) {
			$link = $this->link;
		}

		if ($link && $title) {
			return link_to($link, $title, $attributes = array(), $secure = null);
		}

		return $link;
	}

	//--------------------------------------------------------------------------

	public function fields($name = null) {

		if (is_null($this->fields)) {

			$extendFields = (array) Config::get('fields.' . $this->modelKeyword);
			$this->fields = array_merge($this->initFields(), $extendFields);

			foreach ($this->fields as $key => $field) {
				$this->fields[$key] = Field::make($key, $field, $this);
			}

			// foreach ($this->fields as $key => &$field) {
			// 	//FIXME: какая то лажа:
			// 	// if (isset($field['field']) && ($field['field'] == 'image' || is_array($field['field']) && isset($field['field']['field']) && $field['field']['field'] == 'image') ) {
			// 	// 	static::$imageFields[$key] = (array) $field['field'];
			// 	// 	if (empty(static::$imageFields[$key]['default_size']) && isset(static::$imageFields[$key]['sizes'])) {
			// 	// 		static::$imageFields[$key]['default_size'] = current(array_keys(static::$imageFields[$key]['sizes']));
			// 	// 	}
			// 	// }
			// }
		}

		if ($name) {
			return isset($this->fields[$name]) ? $this->fields[$name] : false;
		}

		return (array) $this->fields;
	}

	//--------------------------------------------------------------------------

	protected function initFields() {
		return array();
	}

	/***************************************************************************
		ADMIN API
	***************************************************************************/

	protected function initAdminUi()
	{
		$link  = 'data/' . $this->modelFullKeyword();
		\AdminUi::menu('navbar', 'data')->add($link , $this->modelName());
	}

	//--------------------------------------------------------------------------

	protected function modelActions()
	{
		return array(
			'list' => array(
				'title' => 'Просмотр',
				'link'  => $this->listAction(),
			),
			'add' => array(
				'title' => 'Добавить',
				'link'  => $this->addAction(),
			),
		);
	}

	//--------------------------------------------------------------------------

	public function rowActions()
	{
		return array(
			'edit' => array(
				'title' => 'Редактировать',
				'icon'  => 'pencil',
				'class' => 'primary',
				'link'  => $this->editAction(),
			),
			'remove' => array(
				'title' => 'Удалить',
				'icon'  => 'remove',
				'class' => 'danger',
				'link'  => $this->removeAction(),
			),
			// 'enabled' => array(
			// 	'title' => $this->enabled ? 'Отключить' : 'Включить',
			// 	'link'  => $this->enabledAction(),
			// ),
		);
	}

	//--------------------------------------------------------------------------

	public function addAction()
	{
		return 'data/' . $this->modelKeyword . '/add';
	}

	//--------------------------------------------------------------------------

	public function listAction()
	{
		return 'data/' . $this->modelKeyword;
	}

	//--------------------------------------------------------------------------

	public function editAction()
	{
		return 'data/' . $this->modelKeyword . '/edit/' . $this->getKey();
	}

	//--------------------------------------------------------------------------

	public function removeAction()
	{
		return 'action/delete/' . $this->modelKeyword . '/' . $this->getKey();
	}

	//--------------------------------------------------------------------------

	public function enabledAction()
	{
		return 'action/enabled/' . $this->modelKeyword . '/' . $this->getKey() . '/' . (int)(!$this->enabled);
	}



	/***************************************************************************
		Default mutators:
	***************************************************************************/

	protected function getLinkAttribute()
	{
		return false;
	}

	//--------------------------------------------------------------------------

	protected function getHomeLinkAttribute()
	{
		return false;
	}

	/***************************************************************************
		Automation:
	***************************************************************************/

	public function forceSave()
	{
		return parent::save();
	}

	//--------------------------------------------------------------------------

	public function save(array $options = array())
	{
		if ( ! $this->valid) {
			if ( ! $this->validate()) {
				return false;
			}
		}

		// $mode        = $this->exists ? 'Update' : 'Insert';
		$save        = false;
		$this->valid = false;
		// $beforeMode  = 'before' . $mode;
		// $afterMode   = 'after' . $mode;

		// if ($this->beforeSave() === false || $this->$beforeMode() === false) {
		// 	return false;
		// }

		if (!empty($_POST)) {
			$this->setData();
		}

		if ($this->attributes) {
			$save = true;
		}

		if ($save && ($result = parent::save($options))) {
			$fields = $this->fields();

			return $result;
		}

		return false;
	}

	//--------------------------------------------------------------------------

	public function validate($rulesKey = 'rules', $fieldPrefix = '') {
		$rules  = array();
		$names  = array();
		$fields = $this->fields();

		if ($rulesKey === false) {
			$this->valid = true;
			return true;
		}

		// Rules
		if (is_array($rulesKey)) {
			$rules = $rulesKey;
		} else {
			foreach ($fields as $name => $field) {
				if ($field && ($fieldRules = $field->rules($rulesKey))) {
					$rules[$fieldPrefix . $name] = $fieldRules;
				}
			}
		}

		// Field names
		foreach ($fields as $name => $field) {
			if (!empty($field->label)) {
				$names[$fieldPrefix . $name] = $field->label;
			}
		}

		if ( ! $rules) {
			$this->valid = true;
			return true;
		}

		$this->validator = Validator::make(Input::all(), $rules);
		$this->validator->setAttributeNames($names);

		$this->valid = $this->validator->passes();

		return $this->valid;
	}

	//--------------------------------------------------------------------------

	public function validator()
	{
		return $this->validator;
	}

	//--------------------------------------------------------------------------

	public function errors()
	{
		if ($this->validator) {
			return $this->validator->messages();
		}
	}

	//--------------------------------------------------------------------------

	protected function setData($data = null)
	{
		if (is_null($data)) {
			$data = Input::all();
		}

		$fields = $this->fields();

		foreach ($fields as $name => $field) {
			if ($field && $field->db === false) {
				continue;
			}

			if ($field) {
				$field->setValue($data);
			} elseif (isset($data[$name])) {
				$this->$name = $data[$name];
			}
		}
	}

	/***************************************************************************
		DB HELPERS
	***************************************************************************/

	// protected function createTable()
	// {
	// 	$fields = $this->fields();

	// 	$table = Schema::create($this->getTable(), function($table) use ($fields) {
	// 		foreach ($fields as $name => $field) {
	// 			$this->createTableColumn($table, $field, $name);
	// 		}
	// 	});
	// }

	// //--------------------------------------------------------------------------

	// protected function updateTable()
	// {
	// 	$newFields = array();
	// 	$fields    = $this->fields();
	// 	$table     = $this->getTable();

	// 	foreach ($fields as $name => $field) {
	// 		if (empty($field['db']) || Schema::hasColumn($table, $name)) {
	// 			 continue;
	// 		}

	// 		Schema::table($table, function($table) use ($field, $name) {
	// 			$this->createTableColumn($table, $field, $name);
	// 		});

	// 		$newFields[] = $name;
	// 	}

	// 	return $newFields;
	// }

	// //--------------------------------------------------------------------------

	// protected function createTableColumn(&$table, $field, $name)
	// {
	// 	if ($field['db']) {
	// 		return;
	// 	}

	// 	$prop = explode('|', $field['db']);

	// 	foreach ($prop as $i => $row) {
	// 		$args    = explode(':', $row);
	// 		$cmd     = $args[0];
	// 		$args[0] = $name;

	// 		$column = call_user_func_array(array($table, $cmd), $args);

	// 		if ( ! $i && isset($field['default'])) {
	// 			$column->default($field['default']);
	// 		}
	// 	}
	// }

	//--------------------------------------------------------------------------
}