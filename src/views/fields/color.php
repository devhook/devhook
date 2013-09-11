<? Page::head('<link href="'.asset('packages/devhook/devhook/spectrum/spectrum.css').'" rel="stylesheet" media="screen">'); ?>
<? Page::head('<script src="'.asset('packages/devhook/devhook/spectrum/spectrum.js').'"></script>'); ?>

<div>
	<?=Form::input('color', $field, $value, $attr); ?>
</div>

<script>$("#<?=$attr['id'] ?>").spectrum({
	// flat: true,
	// showInput: true,
	showInitial: true,
	// showAlpha: bool,
	// disabled: true,
	// localStorageKey: string,
	showPalette: true,
	// showPaletteOnly: bool,
	showSelectionPalette: false,
	// clickoutFiresChange: true,
	cancelText: 'Отмена',
	chooseText: 'OK',
	// className: string,
	preferredFormat: 'hex6',
	maxSelectionSize: 10,
	palette: [
['#A4C400','#60A917','#008A00','#00ABA9'],
['#1BA1E2','#3276b1','#6A00FF','#AA00FF'],
['#F472D0','#D80073','#A20025','#E51400'],
['#FA6800','#F0A30A','#E3C800','#825A2C'],
['#6D8764','#647687','#76608A','#87794E'],
['#333333','#999999','#CCCCCC','#EEEEEE']
	],
	// selectionPalette: [string]
})</script>