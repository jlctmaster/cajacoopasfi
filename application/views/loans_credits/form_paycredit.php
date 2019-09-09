<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('loans_credits/payment_credit/'.$credit_info->credit_id, array('id'=>'credit_pay_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="credits">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_dni'), 'dni', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'person_id',
						'id'=>'person_id',
						'type'=>'hidden',
						'value'=>$credit_info->person_id)
						);?>
				<?php echo form_input(array(
						'name'=>'cashup_id',
						'id'=>'cashup_id',
						'type'=>'hidden',
						'value'=>$cashups->cashup_id)
						);?>
				<?php echo form_input(array(
						'name'=>'cash_book_id',
						'id'=>'cash_book_id',
						'type'=>'hidden',
						'value'=>$cashups->cash_book_id)
						);?>
				<?php echo form_input(array(
						'name'=>'dni',
						'id'=>'dni',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$credit_info->dni)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_name'), 'name', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$credit_info->name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('loans_credits_observations'), 'observations', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'observations',
						'id'=>'observations',
						'class'=>'form-control input-sm',
						'value'=>$credit_info->observations)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_paydate'), 'paydate', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'paydate',
							'id'=>'paydate',
							'class'=>'form-control input-sm',
							'type' => 'text',
							'value'=>to_date(strtotime($credit_info->paydate)))
							); ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_paytype'), 'paytype', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('paytype',
					array(-1 => $this->lang->line('common_none_selected_text'),
						0 => $this->lang->line('loans_credits_paytype_0'),
						1 => $this->lang->line('loans_credits_paytype_1'),
						2 => $this->lang->line('loans_credits_paytype_2')),(!empty($loan_info->paytype) ? $loan_info->paytype : -1),array('id' => 'paytype', 'class' => 'form-control')); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_balance'), 'balance', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'balance',
						'id'=>'balance',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'readonly' => TRUE,
						'value'=>0)
						); ?>
				<?php echo form_input(array(
						'name'=>'capital',
						'id'=>'capital',
						'type' => 'hidden',
						'value'=>$loan_info->capital)
						); ?>
				<?php echo form_input(array(
						'name'=>'interest',
						'id'=>'interest',
						'type' => 'hidden',
						'value'=>$loan_info->interest)
						); ?>
				<?php echo form_input(array(
						'name'=>'cumulate_interest',
						'id'=>'cumulate_interest',
						'type' => 'hidden',
						'value'=>$loan_info->cumulate_interest)
						); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_amount'), 'amount', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'amount',
						'id'=>'amount',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'min' => 0.00,
						'max' => $credit_info->balance,
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

	          $.validator.messages["greaterThan"] = "<?php echo $this->lang->line('loans_credits_amount_greater_than_balance'); ?> "+$otherElement.val();

	          return parseFloat(value) <= parseFloat($otherElement.val());
	    }
	);

	$('#paytype').change(function(){
		if($(this).val()== -1) 
		{
		    $('#balance').val(0);
            $('#capital').val(0);
            $('#interest').val(0);
            $('#cumulate_interest').val(0);
		}
		else
		{
			let credit_id = "<?php echo $credit_info->credit_id;?>";
			$.ajax({
				type: 'GET',
				url: "<?php echo site_url('loans_credits/get_balance/c/"+credit_id+"/"+$(this).val()+"'); ?>",
				dataType: 'json',
				success: function(resp){
		            $('#balance').val(resp.balance);
		            $('#capital').val(resp.capital);
		            $('#interest').val(resp.interest);
		            $('#cumulate_interest').val(resp.cumulate_interest);
		        },
		        error: function(jqXHR, textStatus, errorThrown){
		            console.log(jqXHR);
		        }
			});
		}
	});

	$('#credit_pay_form').validate($.extend({
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
			paydate: "<?php echo $this->lang->line('loans_credits_paydate_required'); ?>",
			amount: {
				required: "<?php echo $this->lang->line('loans_credits_amount_required'); ?>"
			}
		}
	}, form_support.error));
});
</script>
