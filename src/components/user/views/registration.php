<div class="row">
	<?=Widget::alerts() ?>

	<div class="col-lg-6 col-lg-offset-3">
		<div class="well">
		<?=Form::open() ?>

			<div class="row">
				<div class="col-lg-6"><?=$form->row('login', false, false) ?></div>
				<div class="col-lg-6"><?=$form->row('email', false, false) ?></div>
			</div>
			<div class="row">
				<div class="col-lg-6"><?=$form->row('password', false, false) ?></div>
				<div class="col-lg-6"><?=$form->row('password_confirmation', false, false) ?></div>
			</div>
			<div class="row">
				<div class="col-lg-6"><?=$form->row('name', false, false) ?></div>
				<div class="col-lg-6"><?=$form->row('phone', false, false) ?></div>
			</div>

			<?=Form::token() ?>

			<button type="submit" class="btn btn-primary">Зарегистрироваться</button>

		<?=Form::close() ?>
		</div>
	</div>

</div>