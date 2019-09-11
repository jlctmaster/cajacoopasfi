<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name.'/save/'.$info->id, array('id'=>'singlemaster_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="certifiers">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$info->name)
						);?>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	$('#singlemaster_edit_form').validate($.extend({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)
				{
					dialog_support.hide();
					table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
				},
				dataType: 'json'
			});
		},

		errorLabelContainer: '#error_message_box',

		rules:
		{
			name: 'required'
		},

		messages:
		{
			name: "<?php echo $this->lang->line($controller_name.'_name_required'); ?>"
		}
	}, form_support.error));
});
</script>
