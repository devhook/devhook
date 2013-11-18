<?php namespace Devhook\Fields;


trait ImageFieldModel {

	//--------------------------------------------------------------------------

	public function images()
	{
		return $this->morphMany('Image', 'imageable')->orderBy('primary', 'desc');
	}

	//--------------------------------------------------------------------------

	public function thumb()
	{
		return $this->morphOne('Image', 'imageable')->where('primary', 1);
	}

	// //--------------------------------------------------------------------------

	public function image($size = null, $attr = array())
	{
		$force = false;

		if ($this->image && isset(static::$imageFields['image'])) {
			if (!$size) {
				$size = static::$imageFields['image']['default_size'];
			}

			$src = ImageField::imageUrl($this, 'image', $size, $force);

			return app('html')->image($src, null, $attr);
		}

		return '';
	}

	//-------------------------------------------------------------------------

}