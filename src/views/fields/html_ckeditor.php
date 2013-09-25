<? Page::head('<script src="'.asset('packages/devhook/ckeditor/ckeditor.js').'"></script>'); ?>
<?=Form::textarea($field, $value, $attr); ?>
<script>CKEDITOR.replace("<?=$attr['id'] ?>");</script>