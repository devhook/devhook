<!DOCTYPE html>
<html>
<head>
	<title><?=Page::title() ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Bootstrap -->
	<?=HTML::style('packages/devhook/devhook/bootstrap/css/bootstrap.min.css') ?>
	<?=HTML::style('packages/devhook/devhook/font-awesome/css/font-awesome.min.css') ?>
	<?=HTML::script('packages/devhook/devhook/js/jquery.js') ?>

	<?=Page::head() ?>
</head>
<body style="padding-top:70px">

<?=Page::bodyBegin() ?>

<div class="container">
<div class="row">

	<?//=Widget::alerts() ?>

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
</div>

<script src="/packages/devhook/devhook/bootstrap/js/bootstrap.min.js"></script>
<script>
	$('a[data-toggle=tooltip]').tooltip();
</script>

<?=Page::bodyEnd() ?>

</body>
</html>