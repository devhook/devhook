<? if ($data): ?>
	<ul class="breadcrumb">
		<? foreach ($data as $row): ?>
			<? if (!empty($row->active)): ?>
				<li><?=$row->title ?></li>
			<? else: ?>
				<li><a href="<?=$link($row) ?>"><?=$row->title ?></a></li>
			<? endif ?>
		<? endforeach ?>
	</ul>
<? endif ?>