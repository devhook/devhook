<div class="well well-sm">
	<?=Form::file($field) ?>
</div>

<table class="table table-bordered table-condensed table-responsive">
	<? for ($i=0; $i<$columns; $i++): ?>
		<col width="<?=ceil(100/$columns) ?>%">
	<? endfor ?>
	<tr>
		<? foreach ($images as $i => $img): ?>
			<? if ($i && $i%$columns == 0): ?></tr><tr><? endif ?>
			<td class="<?=$img->primary ? 'success' : '' ?> text-center">
				<div class='clearfix'>
					<a href="<?=$img->setDefaultAction($field) ?>" class="btn btn-xs pull-left <?=$img->primary ? 'text-success' : '' ?>" title="По умолчанию" data-toggle="tooltip"><i class="icon-ok"></i></a>
					<a href="<?=$img->removeAction() ?>" class="btn btn-xs text-danger pull-right" title="Удалить" data-toggle="tooltip"><i class="icon-remove"></i></a>
				</div>
				<a href="<?=asset($img->path) ?>"><img style="max-width:100%;" src="<?=$sizeKey ? $img->src($sizeKey) : asset($img->path) ?>" alt=""></a>
				<? /*<span class="label label-info"><?=$img->id ?></span>
				<span class="label label-info"><?=file_size($img->path) ?></span>*/ ?>
			</td>
		<? endforeach ?>
	</tr>
</table>
