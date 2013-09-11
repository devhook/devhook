<input type="hidden" id="<?=$id ?>" name="<?=$field ?>" value="<?=$value ?>" />
<div class="btn-group" id="<?=$id ?>_group">
	<button type="button" class="toggle-off btn <?=$value ? 'btn-default' : 'btn-danger' ?>">Откл.</button>
	<button type="button" class="toggle-on btn <?=$value ? 'btn-success' : 'btn-default' ?>">Вкл.</button>
</div>

<script>
$("#<?=$id ?>_group button").click(function(){
	var $btnOn  = $('button.toggle-on', this.parentNode);
	var $btnOff = $('button.toggle-off', this.parentNode);

	$btnOn.toggleClass('btn-default btn-success');
	$btnOff.toggleClass('btn-default btn-danger');

	$("#<?=$id ?>").val( $btnOn.hasClass('btn-success') ? 1 : 0 );
});
</script>