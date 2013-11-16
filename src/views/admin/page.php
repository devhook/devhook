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

<?=AdminUI::navbar() ?>

	<? $subnav = AdminUI::menu('subnav') ?>
	<? if ($subnav->elem()->childs()): ?>
		<div class="navbar navbar-default navbar-fixed-top size-sm" style="top:40px">
			<div class="container">
				<?=$subnav->elem()->className('nav navbar-nav pull-left') ?>
			</div>
		</div>
	<? endif ?>


	<div class="container">

			<? $tabs    = AdminUI::menu('tabs') ?>
			<? $actions = AdminUI::menu('actions') ?>

			<? if ($actions->elem()->childs()): ?>
				<?=$actions->elem()->className('nav ' . ($tabs->elem()->childs() ? 'nav-pills pull-right' : 'nav-tabs')) ?>
			<? endif ?>

			<? if ($tabs->elem()->childs()): ?>
				<div class="dh-tabs" style="margin-bottom:20px">
					<?=$tabs->elem()->className('nav nav-tabs') ?>
				</div>
			<? endif ?>

		<?=AdminUI::breadcrumbs() ?>

		<?=AdminUI::alerts() ?>

		<div id="content">
			<?=$content ?>
		</div><!--#content-->

	</div>
<hr>

<script src="/packages/devhook/devhook/bootstrap/js/bootstrap.min.js"></script>
<script>
	$('a[data-toggle=tooltip]').tooltip();
</script>

<?=Page::bodyEnd() ?>

</body>
</html>