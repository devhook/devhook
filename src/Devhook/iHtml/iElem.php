<?php namespace Devhook\iHtml;


class iElem {

	//--------------------------------------------------------------------------

	protected $tagName       = '';
	protected $icon          = null;
	protected $beforeContent = '';
	protected $content       = '';
	protected $parent        = null;
	protected $childs        = array();
	protected $childIndex    = -1;
	protected $attr          = array();

	//--------------------------------------------------------------------------

	public static function make($tagName = null)
	{
		return new static($tagName);
	}

	//--------------------------------------------------------------------------

	public function __construct($tagName = null)
	{
		$this->tagName = $tagName;
	}

	//--------------------------------------------------------------------------

	public function __clone()
	{
		foreach ($this->childs as &$child)
		{
			$child = clone $child;
			$child->parent($this);
		}
	}

	//--------------------------------------------------------------------------

	public function __toString()
	{
		$attr = '';
		foreach ($this->attr as $key => $value) {
			$attr .= ' ' . $key . '="' . $value . '"';
		}

		$content = '';
		if ($this->beforeContent) {
			$content .= $this->beforeContent . ' ';
		}
		if ($this->icon) {
			$content .= "<i class='icon-".$this->icon."'></i> ";
		}
		$content .= $this->content;

		foreach ($this->childs as $index => $child) {
			$content .= PHP_EOL . ((string) $child);
		}

		return "<{$this->tagName}{$attr}>{$content}</{$this->tagName}>";
	}

	//--------------------------------------------------------------------------

	public function link($href, $text = '')
	{
		$this->tagName = 'a';
		$this->attr('href', $href);
		$this->text($text);

		return $this;
	}

	//--------------------------------------------------------------------------

	public function icon($icon)
	{
		$this->icon = $icon;

		return $this;
	}

	//--------------------------------------------------------------------------

	public function append($tagName = null)
	{
		$this->childIndex++;

		if (is_object($tagName)) {
			$obj = clone $tagName;
			$obj->parent($this);
		} else {
			$obj = static::make($tagName);
			$obj->parent($this);
		}
		$this->childs[$this->childIndex] = $obj;


		return $this->childs[$this->childIndex];
	}

	//--------------------------------------------------------------------------

	public function child($index = 0)
	{
		return $this->childs[$index];
	}

	//--------------------------------------------------------------------------

	public function reverseChilds()
	{
		$this->childs = array_reverse($this->childs, true);
		return $this;
	}

	//--------------------------------------------------------------------------

	public function childs()
	{
		return $this->childs;
	}

	//--------------------------------------------------------------------------

	public function parent($parent = null)
	{
		if ($parent) {
			$this->parent = $parent;
		}

		return $this->parent;
	}

	//--------------------------------------------------------------------------

	public function root()
	{
		if ( ! $this->parent)
		{
			return $this;
		}

		return $this->parent->root();
	}

	//--------------------------------------------------------------------------

	public function where($name, $value)
	{
		// switch ($name)
		// {
		// 	case 'tag':
		// 		...
		// }

		foreach ($this->childs as $i => $child)
		{
			if ($child->attr($name) == $value) {
				return $this->childs[$i];
			}
		}

		foreach ($this->childs as $i => $child) {
			if ($found = $this->childs[$i]->where($name, $value)) {
				return $found;
			}
		}

		return null;
	}

	/***************************************************************************

	***************************************************************************/

	public function tagName()
	{
		return $this->tagName;
	}

	//--------------------------------------------------------------------------

	public function text($text)
	{
		$this->content = $text;

		return $this;
	}

	//--------------------------------------------------------------------------

	public function beforeText($item)
	{
		$this->beforeContent = $item;

		return $this;
	}

	/***************************************************************************
		Attribute helpers:
	***************************************************************************/

	public function attr($name, $value = null)
	{
		if (is_array($name)) {
			foreach ($name as $key => $val) {
				$this->attr[$key] = $val;
			}
			return $this;
		}

		if ($value === null) {
			return isset($this->attr[$name]) ? $this->attr[$name] : null;
		}

		$this->attr[$name] = $value;

		return $this;
	}

	//--------------------------------------------------------------------------

	public function className($value)
	{
		return $this->attr('class', $value);
	}

	//--------------------------------------------------------------------------

	public function appendClass($value)
	{
		return $this->attr('class', $this->attr('class') . ' ' . $value);
	}

	/***************************************************************************
		Protected
	***************************************************************************/

}