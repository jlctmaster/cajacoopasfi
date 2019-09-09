<?php echo form_open('config/save_cashjournal/', array('id' => 'cashjournal_config_form', 'class' => 'form-horizontal')); ?>
	<div id="config_wrapper">
		<fieldset id="config_info">
			<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
			<ul id="cashjournal_error_message_box" class="error_message_box"></ul>

			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_adjustnote_number_automatic'), 'adjustnote_number_automatic', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-1'>
					<?php echo form_checkbox(array(
						'name' => 'adjustnote_number_automatic',
						'id' => 'adjustnote_number_automatic',
						'value' => 'adjustnote_number_automatic',
						'checked' => $this->config->item('adjustnote_number_automatic'))); ?>
					&nbsp
					<label class="control-label">
						<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" title="<?php echo $this->lang->line('config_adjustnote_number_automatic_tooltip'); ?>"></span>
					</label>
				</div>
				<div class='col-sm-2'>
					<?php 
					$adjustnotes_sequence = array('' => $this->lang->line('common_none_selected_text'));
					foreach ($this->Doctypesequence->get_info_doctype('adjustnotes',1) as $row) {
						if(!empty($row['sequence_id']))
						{
							$adjustnotes_sequence[$row['sequence_id']] = $row['name'];	
						}
					}
					echo form_dropdown('adjustnote_doctype_sequence',$adjustnotes_sequence,$this->config->item('adjustnote_doctype_sequence'), array('class' => 'form-control input-sm')); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_cost_number_automatic'), 'cost_number_automatic', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-1'>
					<?php echo form_checkbox(array(
						'name' => 'cost_number_automatic',
						'id' => 'cost_number_automatic',
						'value' => 'cost_number_automatic',
						'checked' => $this->config->item('cost_number_automatic'))); ?>
					&nbsp
					<label class="control-label">
						<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" title="<?php echo $this->lang->line('config_cost_number_automatic_tooltip'); ?>"></span>
					</label>
				</div>
				<div class='col-sm-2'>
					<?php 
					$costs_sequence = array('' => $this->lang->line('common_none_selected_text'));
					foreach ($this->Doctypesequence->get_info_doctype('costs',1) as $row) {
						if(!empty($row['sequence_id']))
						{
							$costs_sequence[$row['sequence_id']] = $row['name'];
						}
					}
					echo form_dropdown('cost_doctype_sequence',$costs_sequence,$this->config->item('cost_doctype_sequence'), array('class' => 'form-control input-sm')); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_cost_overallcash_number_automatic'), 'cost_overallcash_number_automatic', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-1'>
					<?php echo form_checkbox(array(
						'name' => 'cost_overallcash_number_automatic',
						'id' => 'cost_overallcash_number_automatic',
						'value' => 'cost_overallcash_number_automatic',
						'checked' => $this->config->item('cost_overallcash_number_automatic'))); ?>
					&nbsp
					<label class="control-label">
						<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" title="<?php echo $this->lang->line('config_cost_overallcash_number_automatic_tooltip'); ?>"></span>
					</label>
				</div>
				<div class='col-sm-2'>
					<?php 
					$costs_overallcash_sequence = array('' => $this->lang->line('common_none_selected_text'));
					foreach ($this->Doctypesequence->get_info_doctype('costs',0) as $row) {
						if(!empty($row['sequence_id']))
						{
							$costs_overallcash_sequence[$row['sequence_id']] = $row['name'];
						}
					}
					echo form_dropdown('cost_overallcash_doctype_sequence',$costs_overallcash_sequence,$this->config->item('cost_overallcash_doctype_sequence'), array('class' => 'form-control input-sm')); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_creditnote_number_automatic'), 'creditnote_number_automatic', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-1'>
					<?php echo form_checkbox(array(
						'name' => 'creditnote_number_automatic',
						'id' => 'creditnote_number_automatic',
						'value' => 'creditnote_number_automatic',
						'checked' => $this->config->item('creditnote_number_automatic'))); ?>
					&nbsp
					<label class="control-label">
						<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" title="<?php echo $this->lang->line('config_creditnote_number_automatic_tooltip'); ?>"></span>
					</label>
				</div>
				<div class='col-sm-2'>
					<?php 
					$creditnotes_sequence = array('' => $this->lang->line('common_none_selected_text'));
					foreach ($this->Doctypesequence->get_info_doctype('creditnotes',1) as $row) {
						if(!empty($row['sequence_id']))
						{
							$creditnotes_sequence[$row['sequence_id']] = $row['name'];
						}
					}
					echo form_dropdown('creditnote_doctype_sequence',$creditnotes_sequence,$this->config->item('creditnote_doctype_sequence'), array('class' => 'form-control input-sm')); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_expense_number_automatic'), 'expense_number_automatic', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-1'>
					<?php echo form_checkbox(array(
						'name' => 'expense_number_automatic',
						'id' => 'expense_number_automatic',
						'value' => 'expense_number_automatic',
						'checked' => $this->config->item('expense_number_automatic'))); ?>
					&nbsp
					<label class="control-label">
						<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" title="<?php echo $this->lang->line('config_expense_number_automatic_tooltip'); ?>"></span>
					</label>
				</div>
				<div class='col-sm-2'>
					<?php 
					$expenses_sequence = array('' => $this->lang->line('common_none_selected_text'));
					foreach ($this->Doctypesequence->get_info_doctype('expenses',1) as $row) {
						if(!empty($row['sequence_id']))
						{
							$expenses_sequence[$row['sequence_id']] = $row['name'];
						}
					}
					echo form_dropdown('expense_doctype_sequence',$expenses_sequence,$this->config->item('expense_doctype_sequence'), array('class' => 'form-control input-sm')); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_income_number_automatic'), 'income_number_automatic', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-1'>
					<?php echo form_checkbox(array(
						'name' => 'income_number_automatic',
						'id' => 'income_number_automatic',
						'value' => 'income_number_automatic',
						'checked' => $this->config->item('income_number_automatic'))); ?>
					&nbsp
					<label class="control-label">
						<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" title="<?php echo $this->lang->line('config_income_number_automatic_tooltip'); ?>"></span>
					</label>
				</div>
				<div class='col-sm-2'>
					<?php 
					$incomes_sequence = array('' => $this->lang->line('common_none_selected_text'));
					foreach ($this->Doctypesequence->get_info_doctype('incomes',1) as $row) {
						if(!empty($row['sequence_id']))
						{
							$incomes_sequence[$row['sequence_id']] = $row['name'];
						}
					}
					echo form_dropdown('income_doctype_sequence',$incomes_sequence,$this->config->item('income_doctype_sequence'), array('class' => 'form-control input-sm')); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_income_overallcash_number_automatic'), 'income_overallcash_number_automatic', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-1'>
					<?php echo form_checkbox(array(
						'name' => 'income_overallcash_number_automatic',
						'id' => 'income_overallcash_number_automatic',
						'value' => 'income_overallcash_number_automatic',
						'checked' => $this->config->item('income_overallcash_number_automatic'))); ?>
					&nbsp
					<label class="control-label">
						<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" title="<?php echo $this->lang->line('config_income_overallcash_number_automatic_tooltip'); ?>"></span>
					</label>
				</div>
				<div class='col-sm-2'>
					<?php 
					$income_overallcashs_sequence = array('' => $this->lang->line('common_none_selected_text'));
					foreach ($this->Doctypesequence->get_info_doctype('incomes',0) as $row) {
						if(!empty($row['sequence_id']))
						{
							$income_overallcashs_sequence[$row['sequence_id']] = $row['name'];
						}
					}
					echo form_dropdown('income_overallcash_doctype_sequence',$income_overallcashs_sequence,$this->config->item('income_overallcash_doctype_sequence'), array('class' => 'form-control input-sm')); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_invoice_number_automatic'), 'invoice_number_automatic', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-1'>
					<?php echo form_checkbox(array(
						'name' => 'invoice_number_automatic',
						'id' => 'invoice_number_automatic',
						'value' => 'invoice_number_automatic',
						'checked' => $this->config->item('invoice_number_automatic'))); ?>
					&nbsp
					<label class="control-label">
						<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" title="<?php echo $this->lang->line('config_invoice_number_automatic_tooltip'); ?>"></span>
					</label>
				</div>
				<div class='col-sm-2'>
					<?php 
					$invoices_sequence = array('' => $this->lang->line('common_none_selected_text'));
					foreach ($this->Doctypesequence->get_info_doctype('invoices',1) as $row) {
						if(!empty($row['sequence_id']))
						{
							$invoices_sequence[$row['sequence_id']] = $row['name'];
						}
					}
					echo form_dropdown('invoice_doctype_sequence',$invoices_sequence,$this->config->item('invoice_doctype_sequence'), array('class' => 'form-control input-sm')); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_ticketsale_number_automatic'), 'ticketsale_number_automatic', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-1'>
					<?php echo form_checkbox(array(
						'name' => 'ticketsale_number_automatic',
						'id' => 'ticketsale_number_automatic',
						'value' => 'ticketsale_number_automatic',
						'checked' => $this->config->item('ticketsale_number_automatic'))); ?>
					&nbsp
					<label class="control-label">
						<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" title="<?php echo $this->lang->line('config_ticketsale_number_automatic_tooltip'); ?>"></span>
					</label>
				</div>
				<div class='col-sm-2'>
					<?php 
					$ticketsales_sequence = array('' => $this->lang->line('common_none_selected_text'));
					foreach ($this->Doctypesequence->get_info_doctype('ticketsales',1) as $row) {
						if(!empty($row['sequence_id']))
						{
							$ticketsales_sequence[$row['sequence_id']] = $row['name'];
						}
					}
					echo form_dropdown('ticketsale_doctype_sequence',$ticketsales_sequence,$this->config->item('ticketsale_doctype_sequence'), array('class' => 'form-control input-sm')); ?>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('config_voucher_number_automatic'), 'voucher_number_automatic', array('class' => 'control-label col-xs-2')); ?>
				<div class='col-xs-1'>
					<?php echo form_checkbox(array(
						'name' => 'voucher_number_automatic',
						'id' => 'voucher_number_automatic',
						'value' => 'voucher_number_automatic',
						'checked' => $this->config->item('voucher_number_automatic'))); ?>
					&nbsp
					<label class="control-label">
						<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" title="<?php echo $this->lang->line('config_voucher_number_automatic_tooltip'); ?>"></span>
					</label>
				</div>
				<div class='col-sm-2'>
					<?php 
					$doctype_sequence = array('' => $this->lang->line('common_none_selected_text'));
					foreach ($this->Doctypesequence->get_info_doctype('vouchers',1) as $row) {
						if(!empty($row['sequence_id']))
						{
							$doctype_sequence[$row['sequence_id']] = $row['name'];
						}
					}
					echo form_dropdown('voucher_doctype_sequence',$doctype_sequence,$this->config->item('voucher_doctype_sequence'), array('class' => 'form-control input-sm')); ?>
				</div>
			</div>

			<?php echo form_submit(array(
				'name' => 'submit_general',
				'id' => 'submit_general',
				'value' => $this->lang->line('common_submit'),
				'class' => 'btn btn-primary btn-sm pull-right')); ?>
		</fieldset>
	</div>
<?php echo form_close(); ?>

<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{

	$('#cashjournal_config_form').validate($.extend(form_support.handler, {

		errorLabelContainer: "#cashjournal_error_message_box",

		rules:
		{
			adjustnote_doctype_sequence: {
				required: '#adjustnote_number_automatic:checked'
			},
			cost_doctype_sequence: {
				required: '#cost_number_automatic:checked'
			},
			cost_overallcash_doctype_sequence: {
				required: '#cost_overallcash_number_automatic:checked'
			},
			creditnote_doctype_sequence: {
				required: '#creditnote_number_automatic:checked'
			},
			expense_doctype_sequence: {
				required: '#expense_number_automatic:checked'
			},
			income_doctype_sequence: {
				required: '#income_number_automatic:checked'
			},
			income_overallcash_doctype_sequence: {
				required: '#income_overallcash_number_automatic:checked'
			},
			invoice_doctype_sequence: {
				required: '#invoice_number_automatic:checked'
			},
			ticketsale_doctype_sequence: {
				required: '#ticketsale_number_automatic:checked'
			},
			voucher_doctype_sequence: {
				required: '#voucher_number_automatic:checked'
			}
		},

		messages:
		{
			adjustnote_doctype_sequence: {
				required: "<?php echo $this->lang->line('config_adjustnote_doctype_sequence_required'); ?>"
			},
			cost_doctype_sequence: {
				required: "<?php echo $this->lang->line('config_cost_doctype_sequence_required'); ?>"
			},
			cost_overallcash_doctype_sequence: {
				required: "<?php echo $this->lang->line('config_cost_overallcash_doctype_sequence_required'); ?>"
			},
			creditnote_doctype_sequence: {
				required: "<?php echo $this->lang->line('config_creditnote_doctype_sequence_required'); ?>"
			},
			expense_doctype_sequence: {
				required: "<?php echo $this->lang->line('config_expense_doctype_sequence_required'); ?>"
			},
			income_doctype_sequence: {
				required: "<?php echo $this->lang->line('config_income_doctype_sequence_required'); ?>"
			},
			income_overallcash_doctype_sequence: {
				required: "<?php echo $this->lang->line('config_income_overallcash_doctype_sequence_required'); ?>"
			},
			invoice_doctype_sequence: {
				required: "<?php echo $this->lang->line('config_invoice_doctype_sequence_required'); ?>"
			},
			ticketsale_doctype_sequence: {
				required: "<?php echo $this->lang->line('config_ticketsale_doctype_sequence_required'); ?>"
			},
			voucher_doctype_sequence: {
				required: "<?php echo $this->lang->line('config_voucher_doctype_sequence_required'); ?>"
			}
		},

		submitHandler: function(form) {
			$(form).ajaxSubmit({
				beforeSerialize: function(arr, $form, options) {
					return true;
				},
				success: function(response) {
					$.notify(response.message, { type: response.success ? 'success' : 'danger'} );
				},
				dataType:'json'
			});
		}
	}));
});
</script>
