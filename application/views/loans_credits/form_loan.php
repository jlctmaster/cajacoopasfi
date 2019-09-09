<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('loans_credits/save/'.$loan_info->loan_id, array('id'=>'loan_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="loans">
		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('loans_credits_type'), 'loan_type', !empty($basic_version) ? array('class'=>'required control-label col-xs-3') : array('class'=>'control-label col-xs-3')); ?>
			<div class="col-xs-6">
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'loan_type',
							'type'=>'radio',
							'id'=>'typeP',
							'value'=>'P',
							'checked'=>(!empty($loan_info->loan_type) ? ($loan_info->loan_type === 'P') : TRUE ))
							); ?> <?php echo $this->lang->line('loans_credits_type_partner'); ?>
				</label>
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'loan_type',
							'type'=>'radio',
							'id'=>'typeE',
							'value'=>'E',
							'checked'=>$loan_info->loan_type === 'E')
							); ?> <?php echo $this->lang->line('loans_credits_type_employee'); ?>
				</label>

			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_dni'), 'dni', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'person_id',
						'id'=>'person_id',
						'type'=>'hidden',
						'value'=>$loan_info->person_id)
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
						'value'=>$loan_info->dni,
						'placeholder' => $this->lang->line('common_search_dni'))
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
						'value'=>$loan_info->name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('loans_credits_motive'), 'motive', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'motive',
						'id'=>'motive',
						'class'=>'form-control input-sm',
						'value'=>$loan_info->motive)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_loandate'), 'loandate', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'loandate',
							'id'=>'loandate',
							'class'=>'form-control input-sm',
							'type' => 'text',
							'readonly' => TRUE,
							'value'=> to_date(strtotime($loan_info->loandate)))
							); ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm" style="display:none;">
			<?php echo form_label($this->lang->line('loans_credits_cuote'), 'cuote', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
					'name'=>'cuote',
					'id'=>'cuote',
					'class'=>'form-control input-sm',
					'type' => 'hidden',
					'value'=>$loan_info->cuote)
					); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_returndate'), 'returndate', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'returndate',
							'id'=>'returndate',
							'class'=>'form-control input-sm',
							'value'=> to_date(strtotime($loan_info->returndate)))
							); ?>
				</div>
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
						'step' => 0.01,
						'max' => $cashups->closed_amount_total,
						'value'=>$loan_info->amount)
						); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_percent'), 'percent', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'percent_monthly',
						'id'=>'percent_monthly',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'value'=>(!empty($loan_info->percent_monthly) ? $loan_info->percent_monthly : 0))
						); ?>
				<?php echo form_input(array(
						'name'=>'percent',
						'id'=>'percent',
						'class'=>'form-control input-sm',
						'type' => 'hidden',
						'value'=>(!empty($loan_info->percent) ? $loan_info->percent : 0))
						); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_interest_daily'), 'amt_interest', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'amt_interest',
						'id'=>'amt_interest',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'readonly' => TRUE,
						'value'=>$loan_info->amt_interest)
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
    $('#returndate').daterangepicker({
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

	var fill_value = function(event, ui) {
		event.preventDefault();
		$('#person_id').val(ui.item.value);
		$('#name').val(ui.item.label);
	};

	$("#dni").autocomplete({
		source: "<?php echo site_url('loans_credits/suggest_partner');?>",
		delay: 10,
		appendTo: '.modal-content',
		cacheLength: 1,
		select: fill_value
	});

	$('#percent_monthly').change(function(){
		let percent = parseFloat($(this).val() / 30).toFixed(2);
		let amount = parseFloat($('#amount').val());
		let amt_interest = amount * (percent / 100);
		$('#percent').val(percent);
		$('#amt_interest').val(amt_interest.toFixed(2));
	});

	$('input[name="loan_type"]').click(function(){
	    if(this.checked){
	        if(this.value == "P"){
	            $("#dni").autocomplete({
					source: "<?php echo site_url('loans_credits/suggest_partner');?>",
					delay: 10,
					appendTo: '.modal-content',
					cacheLength: 1,
					select: fill_value
				});
	        } else {
	            $("#dni").autocomplete({
					source: "<?php echo site_url('loans_credits/suggest_employee');?>",
					delay: 10,
					appendTo: '.modal-content',
					cacheLength: 1,
					select: fill_value
				});
	        }
	    }
	});

	$.validator.addMethod("greaterThan", 
	function(value, element, params) {

		var thisDate = moment(value, '<?php echo dateformat_momentjs($this->config->item("dateformat"))?>');
		var thatDate = moment($(params).val(), '<?php echo dateformat_momentjs($this->config->item("dateformat"))?>');

		$.validator.messages["greaterThan"] = "<?php echo $this->lang->line('loan_credit_returndate_validate'); ?> "+$(params).val();

	    if (!/Invalid|NaN/.test(thisDate.toDate())) {
	        return thisDate.toDate() > thatDate.toDate();
	    }

	    return isNaN(value) && isNaN($(params).val()) 
	        || (Number(value) > Number($(params).val())); 
	},'');

	$('#returndate').change(function(){
		let loandate = moment($('#loandate').val(), '<?php echo dateformat_momentjs($this->config->item("dateformat"))?>');
		let returndate = moment($(this).val(), '<?php echo dateformat_momentjs($this->config->item("dateformat"))?>');
		let cuote = returndate.diff(loandate,'months',true);
		$('#cuote').val(cuote.toFixed(0));
	});


	$('#loan_edit_form').validate($.extend({
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
			dni: 'required',
			loandate: 'required',
			amount: {
				required: true,
				max: $('#amount').attr("max")
			},
			returndate: {
            	required: true,
            	greaterThan: '#loandate'
			},
			percent: 'required',
			amt_interest: 'required'
		},

		messages:
		{
			dni: "<?php echo $this->lang->line('loan_credit_dni_required'); ?>",
			loandate: "<?php echo $this->lang->line('loan_loandate_required'); ?>",
			amount: {
				required: "<?php echo $this->lang->line('loan_credit_amount_required'); ?>",
				max: "<?php echo $this->lang->line('cashups_amount_not_exceeded'); ?>"+$('#amount').attr("max")
			},
			returndate: {
				required: "<?php echo $this->lang->line('loan_credit_returndate_required'); ?>"
			},
			percent: "<?php echo $this->lang->line('loan_credit_percent_required'); ?>",
			amt_interest: "<?php echo $this->lang->line('loan_credit_amt_interest_required'); ?>"
		}
	}, form_support.error));
});
</script>
