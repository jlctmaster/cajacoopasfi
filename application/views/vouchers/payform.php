<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('vouchers/payment/'.$voucher_info->voucher_id, array('id'=>'voucher_pay_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="vouchers">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_dni'), 'dni', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'person_id',
						'id'=>'person_id',
						'type'=>'hidden',
						'value'=>$voucher_info->person_id)
						);?>
				<?php echo form_input(array(
						'name'=>'dni',
						'id'=>'dni',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$voucher_info->dni)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_person_name'), 'name', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$voucher_info->name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('vouchers_observations'), 'observations', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'observations',
						'id'=>'observations',
						'class'=>'form-control input-sm',
						'value'=>$voucher_info->observations)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('vouchers_paydate'), 'paydate', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'paydate',
							'id'=>'paydate',
							'class'=>'form-control input-sm',
							'type' => 'text',
							'value'=>to_date(strtotime($voucher_info->paydate)))
							); ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('vouchers_balance'), 'balance', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'balance',
						'id'=>'balance',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'readonly' => TRUE,
						'value'=>$voucher_info->balance)
						); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('vouchers_amount'), 'amount', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'amount',
						'id'=>'amount',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'min' => 0.00,
						'max' => $voucher_info->balance,
						'value'=>0)
						); ?>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	// load the preset datarange picker
	<?php $this->load->view('partial/daterangepicker'); ?>
    // set the beginning of time as starting date
    $('#paydate').daterangepicker({
    	singleDatePicker: true,
    	showDropdowns: true,
		locale: {
			format: '<?php echo dateformat_momentjs($this->config->item("dateformat"))?>',
			separator: ' - ',
			applyLabel: '<?php echo $this->lang->line("datepicker_apply"); ?>',
			cancelLabel: '<?php echo $this->lang->line("datepicker_cancel"); ?>',
			fromLabel: '<?php echo $this->lang->line("datepicker_from"); ?>',
			toLabel: '<?php echo $this->lang->line("datepicker_to"); ?>',
			customRangeLabel: '<?php echo $this->lang->line("datepicker_custom"); ?>',
			daysOfWeek: [
				'<?php echo $this->lang->line("cal_su"); ?>',
				'<?php echo $this->lang->line("cal_mo"); ?>',
				'<?php echo $this->lang->line("cal_tu"); ?>',
				'<?php echo $this->lang->line("cal_we"); ?>',
				'<?php echo $this->lang->line("cal_th"); ?>',
				'<?php echo $this->lang->line("cal_fr"); ?>',
				'<?php echo $this->lang->line("cal_sa"); ?>',
				'<?php echo $this->lang->line("cal_su"); ?>'
			],
			monthNames: [
				'<?php echo $this->lang->line("cal_january"); ?>',
				'<?php echo $this->lang->line("cal_february"); ?>',
				'<?php echo $this->lang->line("cal_march"); ?>',
				'<?php echo $this->lang->line("cal_april"); ?>',
				'<?php echo $this->lang->line("cal_may"); ?>',
				'<?php echo $this->lang->line("cal_june"); ?>',
				'<?php echo $this->lang->line("cal_july"); ?>',
				'<?php echo $this->lang->line("cal_august"); ?>',
				'<?php echo $this->lang->line("cal_september"); ?>',
				'<?php echo $this->lang->line("cal_october"); ?>',
				'<?php echo $this->lang->line("cal_november"); ?>',
				'<?php echo $this->lang->line("cal_december"); ?>'
			],
			firstDay: '<?php echo $this->lang->line("datepicker_weekstart"); ?>'
		}
    });

	$.validator.addMethod("greaterThan",
	    function (value, element, param) {
	          var $otherElement = $(param);

	          $.validator.messages["greaterThan"] = "<?php echo $this->lang->line('vouchers_amount_greater_than_balance'); ?> "+$otherElement.val();

	          return parseFloat(value) <= parseFloat($otherElement.val());
	    },''
	);

	$('#voucher_pay_form').validate($.extend({
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
			paydate: 'required',
			amount: {
				required: true,
			    number: true,
			    min: 0,
			    greaterThan: "#balance"
			}
		},

		messages:
		{
			paydate: "<?php echo $this->lang->line('vouchers_paydate_required'); ?>",
			amount: {
				required: "<?php echo $this->lang->line('vouchers_amount_required'); ?>"
			}
		}
	}, form_support.error));
});
</script>
