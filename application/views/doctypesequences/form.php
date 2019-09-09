<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('doctypesequences/save/'.$doctypesequence_info->sequence_id, array('id'=>'doctypesequence_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="doctypesequences">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('doctypesequences_doctype'), 'doctype', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('doctype',$doctypes,$selected_doctype,array('id' => 'doctype', 'class' => 'form-control'));?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('doctypesequences_is_cashup'), 'is_cashup', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'is_cashup',
						'id'=>'is_cashup',
						'value'=>1,
						'checked'=>($doctypesequence_info->is_cashup) ? 1 : 0)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('doctypesequences_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$doctypesequence_info->name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('doctypesequences_prefix'), 'prefix', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
					<?php echo form_input(array(
							'name'=>'prefix',
							'id'=>'prefix',
							'class'=>'form-control input-sm',
							'value'=>$doctypesequence_info->prefix)
							);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('doctypesequences_next_sequence'), 'next_sequence', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
					<?php echo form_input(array(
							'name'=>'next_sequence',
							'id'=>'next_sequence',
							'class'=>'form-control input-sm',
							'value'=>$doctypesequence_info->next_sequence)
							);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('doctypesequences_number_incremental'), 'number_incremental', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
					<?php echo form_input(array(
							'name'=>'number_incremental',
							'id'=>'number_incremental',
							'class'=>'form-control input-sm',
							'value'=>$doctypesequence_info->number_incremental)
							);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('doctypesequences_suffix'), 'suffix', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
					<?php echo form_input(array(
							'name'=>'suffix',
							'id'=>'suffix',
							'class'=>'form-control input-sm',
							'value'=>$doctypesequence_info->suffix)
							);?>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	// add the rule here
	jQuery.validator.addMethod(
		"notEqualTo",
		function(elementValue,element,param) {
			return elementValue != param;
		},
		"<?php echo $this->lang->line('doctypesequence_doctype_required'); ?>"
	);

	$('#doctypesequence_edit_form').validate($.extend({
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
			doctype: {
				required: true,
				notEqualTo: -1
			},
			next_sequence: 'required',
			number_incremental: 'required',
		},

		messages:
		{
			name: "<?php echo $this->lang->line('doctypesequence_name_required'); ?>",
			doctype: "<?php echo $this->lang->line('doctypesequence_doctype_required'); ?>",
			next_sequence: "<?php echo $this->lang->line('doctypesequence_next_sequence_required'); ?>",
			number_incremental: "<?php echo $this->lang->line('doctypesequence_number_incremental_required'); ?>"
		}
	}, form_support.error));
});
</script>
