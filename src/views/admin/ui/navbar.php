<script>
(function($){
	$('head').append('<link href="<?=asset('packages/devhook/devhook/css/admin.css') ?>" rel="stylesheet" media="screen">')
		.append('<link href="<?=asset('packages/devhook/devhook/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet" media="screen">')
		.append('<script src="<?=asset('packages/devhook/devhook/js/admin.js') ?>"><'+'/script>');
})(jQuery);
</script>

<div id="devhook-navbar" class="devhook-navbar">
	<?=AdminUI::menu('modes') ?>
	<?=AdminUI::menu('navbar') ?>
	<?=AdminUI::menu('tray') ?>
</div>

<script>
	$('body').css({'margin-top':$('#devhook-navbar').height()});
</script>