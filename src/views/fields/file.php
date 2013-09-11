<div class="well well-sm">
	<? if ($file): ?>
		<div class="pull-right">
			<a href="<?=asset($file) ?>" class="btn btn-xs btn-info"><?=$filename ?> | <?=file_size($filesize) ?></a>
			<a href="<?=$removeAction ?>" class="btn btn-xs btn-danger"><i class="icon-remove"></i> Удалить</a>
		</div>
	<? endif ?>
	<?=Form::file($field) ?>
</div>