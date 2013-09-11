<?php namespace Devhook;



class Page {

	//--------------------------------------------------------------------------

	protected static $data = array();

	//--------------------------------------------------------------------------

	public static function head($content = null)
	{
		return static::data('head', $content, PHP_EOL);
	}

	//--------------------------------------------------------------------------

	public static function bodyBegin($content = null)
	{
		return static::data('bodyBegin', $content, PHP_EOL);
	}

	//--------------------------------------------------------------------------

	public static function bodyEnd($content = null)
	{
		return static::data('bodyEnd', $content, true);
	}

	//--------------------------------------------------------------------------

	public static function title($content = null, $append = false)
	{
		return static::data('title', $content, $append);
	}

	//--------------------------------------------------------------------------

	public static function data($key, $value = null, $append = false)
	{
		if (!isset(static::$data[$key])) {
			static::$data[$key] = null;
		}

		if ($value !== null) {
			if ($append !== false) {
				$divider = !static::$data[$key] || $append === true ? '' : $append;
				static::$data[$key] .= $divider . $value;
			} else {
				static::$data[$key] = $value;
			}
		}

		return static::$data[$key];
	}

	//--------------------------------------------------------------------------
}