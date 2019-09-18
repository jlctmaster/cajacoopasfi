<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name.'/save/'.$fees_deposit_info->id_fee_deposit, array('id'=>'fees_deposit_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="fees_deposit">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('fees_deposit_supplier'), 'supplier', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'supplier',
						'id'=>'supplier',
						'class'=>'form-control input-sm',
						'value'=>$fees_deposit_info->name)
						);?>
                                               
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('fees_deposit_period'), 'period', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('period', $period, $selected_period, array('class'=>'form-control', 'id' => 'user_id')); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('fees_deposit_fee_kilos'), 'value', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'fee_kilos',
						'id'=>'fee_kilos',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => 1,
						'value'=>$fees_deposit_info->fee_kilos)
						);?>
			</div>
		</div>
                
                <div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('fees_deposit_fee_qqs'), 'value', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'fee_qqs',
						'id'=>'fee_qqs',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => 1,
						'value'=>$fees_deposit_info->fee_qqs)
						);?>
			</div>
		</div>
                
            
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	$("#supplier").autocomplete({
		source: "<?php echo site_url('fees_deposit/suggest_supplier');?>",
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
