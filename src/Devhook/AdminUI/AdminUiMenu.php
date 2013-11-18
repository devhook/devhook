<?php namespace Devhook;


class AdminUiMenu implements \ArrayAccess {

	//-------------------------------------------------------------------------

	protected $currentSelector;
	protected $data = array();

	//-------------------------------------------------------------------------

	public function get($key)
	{
		$menuPath = explode('.', $menuSelector);

		$menu =& $this->data;
		foreach ($menuPath as $key) {
			$menu =& $menu[$key];
		}
		return $menu;
	}

	//-------------------------------------------------------------------------

	public function add($menuSelector, $link, $title = null)
	{
		$menuPath = explode('.', $menuSelector);

		$this->get($menuSelector);
		$menu =& $this->data;
		foreach ($menuPath as $key) {
			$menu =& $menu[$key];
		}

		$menu[] = $title;

		return $this;
	}

	//-------------------------------------------------------------------------

	public function active($icon)
	{
		return $this;
	}

	//-------------------------------------------------------------------------

	public function icon($icon)
	{
		return $this;
	}

	//-------------------------------------------------------------------------

	public function render()
	{
		// $result = '<ul>';

		// foreach ((array) $this->data as $item) {

		// 	$result .= '<li>';
		// 	$result .= '<a>xxx</a>';
		// 	$result .= '</li>';
		// }

		// $result .= '</ul>';
		// return $result;
	}

	//-------------------------------------------------------------------------

	public function __toString()
	{
		return $this->render();
	}

	//-------------------------------------------------------------------------

	public function offsetExists($offset) {}
	public function offsetGet($offset) {}
	public function offsetSet($offset, $value) {}
	public function offsetUnset($offset) {}

	//-------------------------------------------------------------------------
}