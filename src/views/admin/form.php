<? $customFields = array('enabled', 'creator_id', 'created_at', 'updated_at') ?>
<? $allFields = $form->fields() ?>
<? $fields = array_except($allFields, $customFields) ?>
<? $model = $form->model ?>

<div style="max-width:700px; margin:20px auto">
	<?=Form::open(array('class'=>'form', 'enctype'=>'multipart/form-data')) ?>
		<? if (array_only($form->model->fields(), $customFields)): ?>
			<div class='form-group row'>

				<div class="col-md-3">
					<?=$form->field('enabled') ?>
				</div>

				<div class="col-md-9 text-right text-muted">
					<? if ($model->created_at): ?>
						<div>
							Дата создания:
							<span class="label label-default"><?=$model->created_at->diffForHumans() ?></span>
							<span class="label label-default"><?=$model->created_at ?></span>
							<? if ($model->creator_id): ?>
								<? $creator = User::find($model->creator_id) ?>
								<a href="" class="label label-primary"><i class="icon-user"></i> <?=$creator->login ?></a>
							<? endif ?>
						</div>
					<? endif ?>
					<? if ($model->updated_at): ?>
						<div>
							Дата обновления:
							<span class="label label-default"><?=$model->updated_at->diffForHumans() ?></span>
							<span class="label label-default"><?=$model->updated_at ?></span>
							<? if ($model->updater_id): ?>
								<? $updater_id = $model->updater_id == $model->creator_id ? $creator : User::find($model->updater_id) ?>
								<a href="" class="label label-primary"><i class="icon-user"></i> <?=$updater_id->login ?></a>
							<? endif ?>
						</div>
					<? endif ?>
				</div>
			</div>
		<? endif ?>

		<? foreach ($fields as $field => $opt): ?>
			<?=$form->row($field) ?>
		<? endforeach ?>

		<button class="btn btn-primary" type="submit">Сохранить</button>
		<input class="btn btn-default" type="submit" name="redirect_back" value="Сохранить и  вернуться" />
	<?=Form::close() ?>
</div>

