<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('voucher_operations/save/'.$voucher_operation_info->voucher_operation_id, array('id'=>'voucher_operation_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="voucher_operations">
		<div class="form-group form-group-sm">
			<?php echo form_label('Serie', 'serieno', !empty($basic_version) ? array('class'=>'required control-label col-xs-3') : array('class'=>'control-label col-xs-3')); ?>
			<div class="col-xs-4">
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'serieno',
							'type'=>'radio',
							'id'=>'serieno01',
							'value'=>'01',
							'checked'=>(!empty($voucher_operation_info->serieno) ? $voucher_operation_info->serieno == '01' : TRUE))
					); ?> 01
				</label>
				<br>
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'serieno',
							'type'=>'radio',
							'id'=>'serieno02',
							'value'=>'02',
							'checked'=>$voucher_operation_info->serieno == '02')
					); ?> 02
				</label>
			</div>
			<div class="col-xs-4">
				<?php echo form_label($this->lang->line('voucher_operations_number'), 'voucher_operation_number', array('class'=>'required control-label col-xs-4')); ?>
				<div class="col-xs-6">
				<?php echo form_input(array(
						'name'=>'voucher_operation_number',
						'id'=>'voucher_operation_number',
						'class'=>'form-control input-sm',
						'value'=>$voucher_operation_info->voucher_operation_number)
						);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_dni'), 'dni', array('class'=>'required control-label col-xs-3')); ?>
			<div class="col-xs-4">
			<?php echo form_input(array(
					'name'=>'person_id',
					'id'=>'person_id',
					'type'=>'hidden',
					'value'=>$voucher_operation_info->person_id)
					);?>
			<?php echo form_input(array(
					'name'=>'dni',
					'id'=>'dni',
					'class'=>'form-control input-sm',
					'value'=>$voucher_operation_info->dni,
					'placeholder' => $this->lang->line('common_search_dni'))
					);?>
			</div>
			<div class="col-xs-4">
				<?php echo form_label($this->lang->line('voucher_operations_voucherdate'), 'voucherdate', array('class'=>'required control-label col-xs-4')); ?>
				<div class="col-xs-6">
				<?php echo form_input(array(
						'name'=>'voucherdate',
						'id'=>'voucherdate',
						'class'=>'form-control input-sm',
						'value'=>to_date(strtotime($voucher_operation_info->voucherdate)))
						);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_person_name'), 'name', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-4'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$voucher_operation_info->name)
						);?>
			</div>
			<div class='col-xs-4'>
				<?php echo form_label($this->lang->line('voucher_operations_amount'), 'amount', array('class'=>'control-label col-xs-4')); ?>
				<div class='col-xs-6'>
					<?php echo form_input(array(
							'name'=>'amount',
							'id'=>'amount',
							'class'=>'form-control input-sm',
							'type' => 'number',
							'step' => 0.01,
							'value'=>(!empty($voucher_operation_info->amount) ? $voucher_operation_info->amount : 0))
							);?>
				</div>
			</div>
		</div>

		<div class="row">
			<?php echo form_label($this->lang->line('voucher_operations_quality_certificates_available'), 'quality_certificates', array('class'=>'required control-label col-xs-4')); ?>
			<?php echo form_label($this->lang->line('voucher_operations_quality_certificates_selected'), 'quality_certificates', array('class'=>'required control-label col-xs-7')); ?>
		</div>
		<br>
		<div class="row">
			<?php echo form_label('N° - Calidad - Kg - Qqs - Precio - Importe', 'header_available', array('class'=>'control-label col-xs-4')); ?>
			<?php echo form_label('N° - Calidad - Kg - Qqs - Precio - Importe', 'header_selected', array('class'=>'control-label col-xs-7')); ?>
		</div>
		<br>
		<div class="row">
			<div class="col-xs-5">
				<select name="quality_certificates_from[]" id="multiselect" class="form-control" size="8" multiple="multiple">
					<!--<option value="1">Item 1</option>
					<option value="3">Item 3</option>
					<option value="2">Item 2</option>-->
					<?php 
					if(!empty($quality_certificates_from))
					{
						foreach ($quality_certificates_from as $qc) {
							$text = $qc['certificate_number']." - ".$qc['quality']." - ".$qc['kg_dry']." - ".$qc['qq_dry']." - ".$qc['price']." - ".$qc['amount'];
							echo "<option value='".$qc['quality_certificate_id']."' data-amount='".$qc['amount']."'>$text</option>";
						}
					}
					?>
				</select>
			</div>
			
			<div class="col-xs-2">
				<button type="button" id="multiselect_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
				<button type="button" id="multiselect_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
				<button type="button" id="multiselect_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
				<button type="button" id="multiselect_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
			</div>
			
			<div class="col-xs-5">
				<select name="quality_certificates[]" id="multiselect_to" class="form-control" size="8" multiple="multiple">
				<?php 
				if(!empty($quality_certificates_to))
				{
					foreach ($quality_certificates_to as $qc) {
						$text = $qc['certificate_number']." - ".$qc['quality']." - ".$qc['kg_dry']." - ".$qc['qq_dry']." - ".$qc['price']." - ".$qc['amount'];
						echo "<option value='".$qc['quality_certificate_id']."' data-amount='".$qc['amount']."'>$text</option>";
					}
				}
				?>
				</select>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	//	Change with of modal form
	var wide = $('.modal-dialog').css('width');
    var calculate = parseInt(wide, 10)* 2;

    $('.modal-dialog').css('width', calculate);
    //	End

    $('#multiselect').multiselect(
    	{
	        right: '#multiselect_to',
	        rightAll: '#multiselect_rightAll',
	        rightSelected: '#multiselect_rightSelected',
	        leftSelected: '#multiselect_leftSelected',
	        leftAll: '#multiselect_leftAll',
    		afterMoveToRight: function($letf,$right,$options)
    		{
    			let totalAmount = $('#amount').val();
    			let amount = $options[0].dataset.amount;
    			totalAmount = parseFloat(totalAmount) + parseFloat(amount);
    			$('#amount').val(totalAmount);
    		}
    	});

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
		let serieno = $("input[name='serieno']:checked").val();
		$.ajax({
			type: 'GET',
			url: "<?php echo site_url('voucher_operations/get_qualities_certificates/"+ui.item.value+"/"+serieno+"'); ?>",
			dataType: 'json',
			success: function(resp){
				if(resp.length > 0)
				{
	            	$("#multiselect").empty();
	            	$("#multiselect_to").empty();
					var options;
					for(var i =0;i<resp.length;i++)
					{
						var text = resp[i].certificate_number+" - "+resp[i].quality+" - "+resp[i].kg_dry+" - "+resp[i].qq_dry+" - "+resp[i].price+" - "+resp[i].amount;
		            	var value = resp[i].quality_certificate_id;
		            	options += '<option value="' + value + '" data-amount="'+ resp[i].amount+'" >' + text + '</option>';
		            	//$("#multiselect").append(new Option(text, value));	
					}
		        	$('#multiselect').html(options);
				}
				else
				{
					alert("No se encontraron certificados de calidad disponibles para "+ui.item.label);
				}
	        },
	        error: function(jqXHR, textStatus, errorThrown){
	            console.log(jqXHR);
	        }
		})
	};

	$("#dni").autocomplete({
		source: "<?php echo site_url('vouchers/suggest_partner');?>",
		delay: 10,
		appendTo: '.modal-content',
		cacheLength: 1,
		select: fill_value
	});

	$.validator.addMethod("needsSelection", function (value, element) {
	    var count = $(element).find('option:selected').length;
	    return count > 0;
	});

	$('#voucher_operation_edit_form').validate($.extend({
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
			voucher_operation_number: 'required',
			amount: 'required',
			name: 'required',
			'quality_certificates[]': {
				required: true, 
				needsSelection: true
			}
		},

		messages:
		{
			voucher_operation_number: "<?php echo $this->lang->line('voucher_operation_number_required'); ?>",
			amount: "<?php echo $this->lang->line('voucher_operation_amount_required'); ?>",
			name: "<?php echo $this->lang->line('quality_certificate_name_required'); ?>",
			'quality_certificates[]': {
				required: "<?php echo $this->lang->line('voucher_operation_quality_certificates_required'); ?>", 
				needsSelection: "<?php echo $this->lang->line('voucher_operation_quality_certificates_required'); ?>"
			}
		}
	}, form_support.error));
});
</script>
