<div class="well well-sm">
	<? if ($image): ?>
		<div class="pull-right">
			<img src="<?=asset($image) ?>" style="height:30px; margin:-5px 0;" alt="">
			<a href="<?=asset($image) ?>" class="btn btn-xs btn-info"><?=$filename ?> | <?=file_size($filesize) ?></a>
			<a href="<?=$removeAction ?>" class="btn btn-xs btn-danger"><i class="icon-remove"></i> Удалить</a>
		</div>
	<? endif ?>
	<?=Form::file($field) ?>
</div>