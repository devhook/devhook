<?php namespace Devhook;



class AdminController extends Controller
{

	//--------------------------------------------------------------------------

	const DEFAULT_LAYOUT = 'admin.page';

	//--------------------------------------------------------------------------

	protected $model;

	//--------------------------------------------------------------------------

	public function __construct($model = null)
	{
		$this->model = $model;

		parent::__construct();
	}

	//--------------------------------------------------------------------------
}