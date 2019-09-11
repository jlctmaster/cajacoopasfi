<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name.'/save/'.$model_info->model_id, array('id'=>'models_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="models">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('models_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$model_info->name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('models_type'), 'type', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-tag"></span></span>
					<?php echo form_input(array(
							'name'=>'type',
							'id'=>'type',
							'class'=>'form-control input-sm',
							'value'=>$model_info->type)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('models_value'), 'value', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'value',
						'id'=>'value',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => 0.01,
						'value'=>$model_info->value)
						);?>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	$("#type").autocomplete({
		source: "<?php echo site_url('models/suggest_type');?>",
		delay: 10,
		appendTo: '.modal-content'
	});

	$('#models_edit_form').validate($.extend({
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
			type: 'required',
			value: 'required'
		},

		messages:
		{
			name: "<?php echo $this->lang->line($controller_name.'_name_required'); ?>",
			type: "<?php echo $this->lang->line($controller_name.'_type_required'); ?>",
			value: "<?php echo $this->lang->line($controller_name.'_value_required'); ?>"
		}
	}, form_support.error));
});
</script>
