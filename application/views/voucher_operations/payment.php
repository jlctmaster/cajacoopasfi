<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('voucher_operations/save_payment/'.$voucher_operation_info->voucher_operation_id, array('id'=>'voucher_operation_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="voucher_operations">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('voucher_operations_liquidatedate'), 'liquidatedate', array('class'=>'required control-label col-xs-2')); ?>
			<div class="col-xs-3">
				<?php echo form_input(array(
						'name'=>'liquidatedate',
						'id'=>'liquidatedate',
						'class'=>'form-control input-sm',
						'value'=>to_date(strtotime($voucher_operation_info->liquidatedate)))
						);?>
			</div>
			<div class="col-xs-6">
				<?php echo form_label($this->lang->line('voucher_operations_number'), 'voucher_operation_number', array('class'=>'required control-label col-xs-6')); ?>
				<div class="col-xs-4">
				<?php echo form_input(array(
						'name'=>'voucher_operation_number',
						'id'=>'voucher_operation_number',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$voucher_operation_info->serieno."-".$voucher_operation_info->voucher_operation_number)
						);?>
					<?php echo form_hidden('serieno', $voucher_operation_info->serieno); ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_person_name'), 'name', array('class'=>'control-label col-xs-2')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$voucher_operation_info->name)
						);?>
			</div>
			<div class='col-xs-6'>
				<?php echo form_label($this->lang->line('common_dni'), 'dni', array('class'=>'control-label col-xs-6')); ?>
				<div class='col-xs-4'>
					<?php echo form_input(array(
							'name'=>'dni',
							'id'=>'dni',
							'class'=>'form-control input-sm',
							'readonly' => TRUE,
							'value'=>$voucher_operation_info->dni)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_address'), 'address', array('class'=>'control-label col-xs-2')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'address',
						'id'=>'address',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$voucher_operation_info->address)
						);?>
			</div>
			<div class='col-xs-6'>
				<?php echo form_label($this->lang->line('customers_user'), 'user', array('class'=>'control-label col-xs-6')); ?>
				<div class='col-xs-4'>
					<?php echo form_input(array(
							'name'=>'user',
							'id'=>'user',
							'class'=>'form-control input-sm',
							'value'=>$user,
							'readonly'=>'true')
							); ?>
					<?php echo form_hidden('user_id', $voucher_operation_info->user_id); ?>
					<?php echo form_hidden('cash_book_id', $cashups->cash_book_id); ?>
					<?php echo form_hidden('cashup_id', $cashups->cashup_id); ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<div class='col-xs-8'>
				<fieldset>Referencia de movimientos vinculados
					<div class="table-responsive">
					<table class="table table-striped w-auto">
						<thead>
					      <tr>
					        <th scope="col">#</th>
					        <th scope="col">Tipo</th>
					        <th scope="col">Estado</th>
					        <th scope="col">Monto</th>
					      </tr>
					    </thead>
					    <tbody>
					    	<?php 
					    		$conteo = 0;
					    		$total_vinculado = 0;
					    		foreach ($documents_allocates_info as $doc):
					    			$total_vinculado += $doc['amount'];
					  				$conteo++;
					    	?>
					      <tr>
					        <th scope="row"><?php echo $conteo;?></th>
					        <td><?php echo $doc['type'];?></td>
					        <td><?php echo $doc['status'];?></td>
					        <td><?php echo $doc['amount'];?></td>
					      </tr>
					  		<?php 
					  			endforeach;
					  		?>
					</table>
				</fieldset>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('Monto Saldado', 'amounted', array('class'=>'control-label col-xs-2')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'amounted',
						'id'=>'amounted',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$total_vinculado)
						);?>
			</div>
		</div>

		<div class="row">
			<div class='col-xs-8'>
				<div class="table-responsive">
					<table class="table table-striped w-auto">
						<thead>
					      <tr>
					        <th scope="col">#</th>
					        <th scope="col">Certificado</th>
					        <th scope="col">Calidad</th>
					        <th scope="col">Kgs</th>
					        <th scope="col">P.Unit</th>
					        <th scope="col">Importe</th>
					      </tr>
					    </thead>
					    <tbody>
					    	<?php 
					    		$cont = 0;
					    		$total = 0;
					    		foreach ($quality_certificates_info as $qc):
					    			$total += $qc['amount'];
					  				$cont++;
					    	?>
							<tr>
								<th scope="row"><?php echo $cont;?></th>
								<td><?php echo $qc['certificate_number'];?></td>
								<td><?php echo $qc['quality'];?></td>
								<td><?php echo $qc['kg_dry'];?></td>
								<td><?php echo $qc['price'];?></td>
								<td><?php echo $qc['amount'];?></td>
							</tr>
					  		<?php 
					  			endforeach;
					  		?>
					</table>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label('Total', 'total_amount', array('class'=>'control-label col-xs-2')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'total_amount',
						'id'=>'total_amount',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'max' => $cashups->closed_amount_total,
						'value'=>$total)
						);?>
			</div>
			<div class='col-xs-6'>
				<?php echo form_label('Imprimir', 'printed', array('class'=>'control-label col-xs-3')); ?>
				<div class='col-xs-1'>
					<?php echo form_checkbox(array(
						'name'=>'printed',
						'id'=>'printed',
						'value'=>1,
						'checked'=>1)
						);?>
				</div>
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
			amount: {
				required: true,
				max: $('#amount').attr("max")
			},
			name: 'required',
			'quality_certificates[]': {
				required: true, 
				needsSelection: true
			}
		},

		messages:
		{
			voucher_operation_number: "<?php echo $this->lang->line('voucher_operation_number_required'); ?>",
			amount: {
				required: "<?php echo $this->lang->line('loan_credit_amount_required'); ?>",
				max: "<?php echo $this->lang->line('cashups_amount_not_exceeded'); ?>"+$('#amount').attr("max")
			},
			name: "<?php echo $this->lang->line('quality_certificate_name_required'); ?>",
			'quality_certificates[]': {
				required: "<?php echo $this->lang->line('voucher_operation_quality_certificates_required'); ?>", 
				needsSelection: "<?php echo $this->lang->line('voucher_operation_quality_certificates_required'); ?>"
			}
		}
	}, form_support.error));
});
</script>
