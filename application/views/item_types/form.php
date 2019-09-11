<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name.'/save/'.$item_type_info->item_type_id, array('id'=>'item_types_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="item_types">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('item_types_family'), 'family', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-tag"></span></span>
					<?php echo form_input(array(
							'name'=>'family',
							'id'=>'family',
							'class'=>'form-control input-sm',
							'value'=>$item_type_info->family)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('item_types_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$item_type_info->name)
						);?>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	$("#family").autocomplete({
		source: "<?php echo site_url('item_types/suggest_family');?>",
		delay: 10,
		appendTo: '.modal-content'
	});

	$('#item_types_edit_form').validate($.extend({
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
			name: 'required',
			family: 'required'
		},

		messages:
		{
			name: "<?php echo $this->lang->line($controller_name.'_name_required'); ?>",
			family: "<?php echo $this->lang->line($controller_name.'_family_required'); ?>"
		}
	}, form_support.error));
});
</script>
