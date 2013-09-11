<? if ($data): ?>
	<ul class="breadcrumb">
		<li><a href="/">Главная</a></li>
		<? foreach ($data as $row): ?>
			<? if ($row->active): ?>
				<li><?=$row->title ?></li>
			<? else: ?>
				<li><a href="<?=$row->link() ?>"><?=$row->title ?></a></li>
			<? endif ?>
		<? endforeach ?>
	</ul>
<? endif ?>