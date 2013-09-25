<?php



function file_size($size)
{
	$dim  = '';

	if (!is_numeric($size)) {
		$size = \File::size($size);
	}

	if ($size) {
		$size = $size / 1024;
		$dim  = ' KB';
	}
	if ($size > 1000) {
		$size = $size / 1024;
		$dim  = ' MB';
	}
	if ($size > 1000) {
		$size = $size / 1024;
		$dim  = ' GB';
	}
	return number_format($size, 2) . $dim;
}

//--------------------------------------------------------------------------

function devhook_class_aliases($aliases)
{
	$eval = '';
	foreach ($aliases as $alias => $target) {
		if ( ! class_exists($alias)) {
			class_alias($target, $alias);
			// $eval .= "class {$alias} extends {$target} {}" . PHP_EOL;
		}
	}

	if ($eval) {
		echo '<pre>';print_r($eval);exit;
		eval($eval);
	}
}

//--------------------------------------------------------------------------