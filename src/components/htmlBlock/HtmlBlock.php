<?php namespace Devhook;

class HtmlBlock extends \Model {

	protected $table     = 'htmlblocks';
	protected $modelName = 'Текстовые блоки';

	//-------------------------------------------------------------------------

	public static function view($key) {
		$body = static::where('name', $key)->pluck('body');

		if ($body === null) {
			$row = new static;
			$row->name  = $key;
			$row->title = '';
			$row->forceSave();
		}

		return $body;
	}

	//-------------------------------------------------------------------------

	protected function initFields()
	{
		return array(
			'id' => array(),
			'status' => array(
				'label'   => 'Status',
				// 'field'   => 'toggle',
				'default' => 1,
			),
			'name' => array(
				'label'      => 'Название',
				'field'      => 'text',
				'rules'      => 'required',
			),
			'title' => array(
				'label'      => 'Описание',
				'field'      => 'text',
//				'rules'      => 'required',
			),
			'body' => array(
				'label'      => 'Содержание',
				'field'      => 'html',
//				'rules'      => 'required',
			),
			'created_at' => array(),
			'updated_at' => array(),
		);
	}

	//-------------------------------------------------------------------------

}