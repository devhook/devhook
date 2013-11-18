<? if (count($data)): ?>

	<table class="table table-striped">
		<thead>
		<tr>
			<? foreach ($columns as $key => $col): ?>
				<? if ($key == 'id'): ?>
					<th width="1"><?=$col->label == 'id' ? '#' : $col->label ?></th>
				<? else: ?>
					<th><?=$col->label ?></th>
				<? endif ?>
			<? endforeach ?>
			<th></th>
		</tr>
		</thead>
		<tbody>
			<? foreach ($data as $row): ?>
				<tr>
					<? foreach ($columns as $key => $col): ?>
						<td><?=$col->mutator ? call_user_func_array($col->mutator, array($row) ) : $col->adminValueMutator($row, $key) ?></td>
					<? endforeach ?>
					<td class="text-right">
						<? if ($link = $row->link): ?>
							<a href="<?=URL::to($link) ?>" class="btn btn-xs btn-default"><i class="icon-eye-open"></i></a>
						<? endif ?>
						<? $actions = $row->getRowActions() ?>
						<? $defClass = array('remove'=>'danger', 'edit'=>'primary') ?>
						<? $defIcon = array('remove'=>'remove', 'edit'=>'pencil') ?>
						<? foreach ($actions as $key => $act): ?>
							<? $class = isset($act['class']) ? $act['class'] : (isset($defClass[$key]) ? $defClass[$key] : 'default') ?>
							<? $icon  = isset($act['icon']) ? $act['icon'] : (isset($defIcon[$key]) ? $defIcon[$key] : '') ?>
							<? $icon  = $icon ? "<i class='icon-{$icon}'></i> " : '' ?>
							<? if (!empty($act['link'])): ?>
								<a href="<?=URL::to(Devhook::backendRoute($act['link'])) ?>" class="btn btn-xs btn-<?=$class ?>"><?=$icon . $act['title'] ?></a>
							<? else: ?>
								<span class="btn btn-xs btn-<?=$class ?>"><?=$icon . $act['title'] ?></span>
							<? endif ?>
						<? endforeach ?>
					</td>
				</tr>
			<? endforeach ?>
		</tbody>
	</table>

	<?=$pagination ?>
<? else: ?>
<div class="alert alert-info text-center"><i class="icon-gears icon-3x"></i><br>Нет записей</div>
<? endif ?>