<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('vouchers/save/'.$voucher_info->voucher_id, array('id'=>'voucher_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="vouchers">
		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('vouchers_type'), 'voucher_type', !empty($basic_version) ? array('class'=>'required control-label col-xs-3') : array('class'=>'control-label col-xs-3')); ?>
			<div class="col-xs-6">
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'voucher_type',
							'type'=>'radio',
							'id'=>'typeP',
							'value'=>'P',
							'checked'=>(!empty($voucher_info->voucher_type) ? ($voucher_info->voucher_type === 'P') : TRUE ))
							); ?> <?php echo $this->lang->line('vouchers_type_partner'); ?>
				</label>
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'voucher_type',
							'type'=>'radio',
							'id'=>'typeE',
							'value'=>'E',
							'checked'=>$voucher_info->voucher_type === 'E')
							); ?> <?php echo $this->lang->line('vouchers_type_employee'); ?>
				</label>

			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('vouchers_number'), 'voucher_number', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php if($this->config->item('voucher_number_automatic') == '1')
				{
					echo form_input(array(
						'name'=>'voucher_number',
						'id'=>'voucher_number',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$voucher_info->voucher_number)
						);
				}
				else
				{
					echo form_input(array(
						'name'=>'voucher_number',
						'id'=>'voucher_number',
						'class'=>'form-control input-sm',
						'value'=>$voucher_info->voucher_number)
						);
				}
				?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('vouchers_voucherdate'), 'voucherdate', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'voucherdate',
							'id'=>'voucherdate',
							'class'=>'form-control input-sm',
							'value'=>to_date(strtotime($voucher_info->voucherdate)))
							); ?>
				</div>
			</div>
		</div>

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
						'name'=>'cash_book_id',
						'id'=>'cash_book_id',
						'type'=>'hidden',
						'value'=>$cashups->cash_book_id)
						);?>
				<?php echo form_input(array(
						'name'=>'dni',
						'id'=>'dni',
						'class'=>'form-control input-sm',
						'value'=>$voucher_info->dni,
						'placeholder' => $this->lang->line('common_search_dni'))
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
			<?php echo form_label($this->lang->line('vouchers_detail'), 'detail', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'detail',
						'id'=>'detail',
						'class'=>'form-control input-sm',
						'value'=>$voucher_info->detail)
						);?>
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
						'step' => 0.01,
						'value'=>$voucher_info->amount)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('vouchers_type'), 'voucher_type', !empty($basic_version) ? array('class'=>'required control-label col-xs-3') : array('class'=>'control-label col-xs-3')); ?>
			<div class="col-xs-8">
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'cash_type',
							'type'=>'radio',
							'id'=>'cash_typeC',
							'value'=>'C',
							'checked'=>(!empty($voucher_info->cash_type) ? ($voucher_info->cash_type === 'C') : TRUE ))
							); ?> <?php echo $this->lang->line('vouchers_cash_type_cash'); ?>
				</label>
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'cash_type',
							'type'=>'radio',
							'id'=>'cash_typeB',
							'value'=>'B',
							'checked'=>$voucher_info->cash_type === 'B')
							); ?> <?php echo $this->lang->line('vouchers_cash_type_bank'); ?>
				</label>

			</div>
		</div>

		<div class="form-group form-group-sm" id="cash_type_bank_trx" style="display:none">
			<?php echo form_label($this->lang->line('vouchers_trx_number'), 'trx_number', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'trx_number',
						'id'=>'trx_number',
						'class'=>'form-control input-sm',
						'value'=>$voucher_info->trx_number)
						);?>
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
    $('#voucherdate').daterangepicker({
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
		source: "<?php echo site_url('vouchers/suggest_partner');?>",
		delay: 10,
		appendTo: '.modal-content',
		cacheLength: 1,
		select: fill_value
	});

	$('input[name="voucher_type"]').click(function(){
	    if(this.checked){
	        if(this.value == "P"){
	            $("#dni").autocomplete({
					source: "<?php echo site_url('vouchers/suggest_partner');?>",
					delay: 10,
					appendTo: '.modal-content',
					cacheLength: 1,
					select: fill_value
				});
	        } else {
	            $("#dni").autocomplete({
					source: "<?php echo site_url('vouchers/suggest_employee');?>",
					delay: 10,
					appendTo: '.modal-content',
					cacheLength: 1,
					select: fill_value
				});
	        }
	    }
	});
	$('input[name="cash_type"]').click(function(){
		if(this.checked){
			if(this.value == "B"){
				$('#cash_type_bank_trx').show();
				$("#trx_number").addClass("required");
			}
			else{
				$('#cash_type_bank_trx').hide();
				$("#trx_number").removeClass("required");
			}
		}
	});

	if($("input[name='cash_type']:checked").val() == "B")
	{
		$('#cash_type_bank_trx').show();
		$("#trx_number").addClass("required");
	}

	$('#voucher_edit_form').validate($.extend({
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
			voucherdate: 'required',
			name: 'required',
			amount: 'required',
			trx_number: {
				required: "#cash_typeB:checked"
			}
		},

		messages:
		{
			voucherdate: "<?php echo $this->lang->line('voucher_voucherdate_required'); ?>",
			name: "<?php echo $this->lang->line('voucher_name_required'); ?>",
			amount: "<?php echo $this->lang->line('voucher_amount_required'); ?>",
			trx_number: "<?php echo $this->lang->line('voucher_trx_number_required'); ?>"
		}
	}, form_support.error));
});
</script>
