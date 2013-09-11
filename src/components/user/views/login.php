<div class="row">

	<?=Widget::alerts() ?>

	<div class="col-lg-4 col-lg-offset-4">
		<div class="well">
		<?=Form::open() ?>
			<?=$form->row('login') ?>
			<?=$form->row('password') ?>

			<div class="checkbox">
				<label><?=Form::checkbox('remember'); ?> Запомнить</label>
			</div>

			<?=Form::token() ?>

			<?=Form::submit('Войти', array('class'=>'btn btn-primary')) ?>

		<?=Form::close() ?>
		</div>
	</div>

</div>