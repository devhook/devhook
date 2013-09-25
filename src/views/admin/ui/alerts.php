<? if ($errors->count()): ?>
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<? foreach ($errors->all() as $msg): ?>
			<p><?=$msg ?></p>
		<? endforeach ?>
	</div>
<? endif ?>