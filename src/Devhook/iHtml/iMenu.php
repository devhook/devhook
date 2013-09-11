<?php namespace Devhook\iHtml;



class iMenu {

	//--------------------------------------------------------------------------

	protected $menu;
	protected $items = array();
	protected $submenu = array();
	protected $currentItem;
	protected $linkPrefix;

	//--------------------------------------------------------------------------

	public function __construct()
	{
		$this->menu = \iElem::make('ul');
	}

	//--------------------------------------------------------------------------

	public function linkPrefix($prefix)
	{
		$this->linkPrefix = $prefix;
	}

	//--------------------------------------------------------------------------

	public function elem()
	{
		return $this->menu;
	}

	//--------------------------------------------------------------------------

	public function __toString()
	{
		foreach ($this->submenu as $key => $submenu) {
			if (isset($this->items[$key])) {
				$this->items[$key]->appendClass('dropdown');
				$this->items[$key]->append($submenu->elem());
			}
		}
		return $this->items ? (string) $this->elem() : '';
	}

	//--------------------------------------------------------------------------

	public function add($href, $text = '')
	{
		$key = $href;

		if ($href && $this->linkPrefix && $href{0} != '/') {
			$href = $this->linkPrefix . '/' . $href;
		}
		$this->currentItem = $this->menu->append('li');
		$this->currentItem->append('a')->attr('href', \URL::to($href))->text($text);

		$this->items[$key] = $this->currentItem;

		if (\Request::is($href)) {
			$this->active(true);
		}

		return $this;
	}

	//--------------------------------------------------------------------------

	public function submenu($key)
	{
		if (!isset($this->submenu[$key])) {
			$this->submenu[$key] = new static();
		}

		return $this->submenu[$key];
	}

	//--------------------------------------------------------------------------

	public function item($key)
	{
		return $this->items[$key];
	}

	//--------------------------------------------------------------------------

	public function active($active = null)
	{
		if (is_string($active)) {
			if (isset($this->items[$active])) {
				$this->items[$active]->attr('class', 'active');
			}

			return;
		}

		if ($active === null) {
			$active = true;
		}

		if ($this->currentItem) {
			$this->currentItem->attr('class', $active ? 'active' : '');
		}
	}

	//--------------------------------------------------------------------------

	public function icon($icon)
	{
		// '<span class="label label-danger">3</span>'
		$this->currentItem->child()->beforeText(iElem::make('i')->className('icon-' . $icon));

		return $this;
	}

	//--------------------------------------------------------------------------

	public function largeIcon($icon)
	{
		return $this->icon($icon . ' icon-large');
	}

	//--------------------------------------------------------------------------

	public function badge($count)
	{
		// '<span class="label label-danger">3</span>'
		$this->currentItem->child()->append('span')->text($count)->className('label label-danger');

		return $this;
	}

	//--------------------------------------------------------------------------
}