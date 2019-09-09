<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('invoices/'.$action.'/'.$invoice_info->invoice_id, array('id'=>'invoice_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="invoices">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('invoices_documentdate'), 'documentdate', array('class'=>'required control-label col-xs-2')); ?>
			<div class='col-xs-4'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'documentdate',
							'id'=>'documentdate',
							'class'=>'form-control input-sm',
							'value'=>to_datetime(strtotime($invoice_info->documentdate)))
							); ?>
				</div>
			</div>
			<div class='col-xs-6'>
				<?php echo form_label($this->lang->line('invoices_serieno'), 'serieno', array('class'=>'required control-label col-xs-3')); ?>
				<div class='col-xs-8'>
					<?php if($this->config->item('invoice_number_automatic') == '1')
					{
						echo form_input(array(
							'name'=>'serieno',
							'id'=>'serieno',
							'class'=>'form-control input-sm',
							'readonly' => TRUE,
							'value'=>$invoice_info->serieno)
							);
					}
					else
					{
						echo form_input(array(
							'name'=>'serieno',
							'id'=>'serieno',
							'class'=>'form-control input-sm',
							'value'=>$invoice_info->serieno)
							);
					}
					?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_person_name'), 'person', array('class'=>'required control-label col-xs-2')); ?>
			<div class='col-xs-4'>
				<?php echo form_input(array(
						'name'=>'person_id',
						'id'=>'person_id',
						'type'=>'hidden',
						'value'=>$invoice_info->person_id)
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
						'name'=>'person',
						'id'=>'person',
						'class'=>'form-control input-sm',
						'value'=>$invoice_info->name,
						'placeholder' => $this->lang->line('common_search_dni'))
						);?>
			</div>
			<div class='col-xs-6'>
				<?php echo form_label($this->lang->line('common_ruc'), 'ruc', array('class'=>'required control-label col-xs-3')); ?>
				<div class='col-xs-8'>
					<?php echo form_input(array(
							'name'=>'ruc',
							'id'=>'ruc',
							'class'=>'form-control input-sm',
							'readonly' => TRUE,
							'value'=>$invoice_info->ruc)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('customers_company_name'), 'company_name', array('class'=>'control-label col-xs-2')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'company_name',
						'id'=>'company_name',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$invoice_info->company_name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('invoices_description'), 'description', array('class'=>'control-label col-xs-2')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'description',
						'id'=>'description',
						'class'=>'form-control input-sm',
						'value'=>$invoice_info->description)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('invoices_add_lines'), 'details', array('class'=>'required control-label col-xs-6')); ?>
			<div class='col-xs-12'>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<td><?php echo $this->lang->line('invoices_quantity')?></td>
							<td><?php echo $this->lang->line('invoices_detail')?></td>
							<td><?php echo $this->lang->line('invoices_price')?></td>
							<td><?php echo $this->lang->line('invoices_amount')?></td>
							<td><button type="button" onclick="agrega_campos()" class='btn btn-info btn-sm pull-right'><span class="glyphicon glyphicon-plus"></span></button></td>
						</tr>
					</thead>
					<tbody id="item_details">
						<?php if(!empty($lineinvoice_info[0]->lineinvoice_id)):
							$contador = 0; ?>
						<?php 	foreach ($lineinvoice_info as $detail_info):?>
							<tr id='<?php echo $contador;?>' >
								<td><input type='number' name='qtys[]' id='quantity_<?php echo $contador;?>' onKeyUp='return calculateTotal(<?php echo $contador;?>)' class='form-control input-sm' value="<?php echo $detail_info->quantity; ?>"></td>
								<td><input type='text' name='details[]' id='detail_<?php echo $contador;?>' class='form-control input-sm' value="<?php echo $detail_info->detail; ?>"></td>
								<td><input type='number' name='prices[]' id='price_<?php echo $contador;?>' onKeyUp='return calculateTotal(<?php echo $contador;?>)' class='form-control input-sm' value="<?php echo $detail_info->price; ?>"></td>
								<td><input type='number' readonly name='amounts[]' id='amount_<?php echo $contador;?>' class='form-control input-sm' value="<?php echo $detail_info->amount; ?>"></td>
								<td><button type='button' class='btn btn-info btn-sm pull-right' onclick='elimina_me(<?php echo $contador;?>)'><span class='glyphicon glyphicon-minus'></span></button></td>
							</tr>
							<?php $contador++; ?>
						<?php 	endforeach;?>
						<?php 	endif;?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('invoices_subtotal'), 'subtotal', array('class'=>'required control-label col-xs-8')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'subtotal',
						'id'=>'subtotal',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'readonly' => TRUE,
						'value'=>$invoice_info->totalamt)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('invoices_discount'), 'discount', array('class'=>'required control-label col-xs-8')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'discount',
						'id'=>'discount',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => 0.01,
						'max' => 100,
						'value'=>$invoice_info->discount)
						);?>
				<?php echo form_input(array(
						'name'=>'discountamt',
						'id'=>'discountamt',
						'class'=>'form-control input-sm',
						'type' => 'hidden',
						'readonly' => TRUE,
						'value'=>$invoice_info->discountamt)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->config->item('default_tax_1_name').' '.$this->config->item('default_tax_1_rate').'%', 'taxamt', array('class'=>'required control-label col-xs-8')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'tax',
						'id'=>'tax',
						'class'=>'form-control input-sm',
						'type' => 'hidden',
						'value'=>(!empty($this->config->item('default_tax_1_rate')) ? $this->config->item('default_tax_1_rate') : 0))
						);?>
				<?php echo form_input(array(
						'name'=>'taxamt',
						'id'=>'taxamt',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'readonly' => TRUE,
						'value'=>$invoice_info->taxamt)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('invoices_totalamt'), 'totalamt', array('class'=>'required control-label col-xs-8')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'totalamt',
						'id'=>'totalamt',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'readonly' => TRUE,
						'value'=>$invoice_info->totalamt)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('invoices_paymentterm'), 'movementtype', !empty($basic_version) ? array('class'=>'required control-label col-xs-3') : array('class'=>'control-label col-xs-3')); ?>
			<div class="col-xs-8">
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'movementtype',
							'type'=>'radio',
							'id'=>'cash_typeC',
							'value'=>'C',
							'checked'=>(!empty($invoice_info->movementtype) ? ($invoice_info->movementtype === 'C') : TRUE ))
							); ?> <?php echo $this->lang->line('common_cash'); ?>
				</label>
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'movementtype',
							'type'=>'radio',
							'id'=>'cash_typeB',
							'value'=>'B',
							'checked'=>$invoice_info->movementtype === 'B')
							); ?> <?php echo $this->lang->line('common_bank'); ?>
				</label>

			</div>
		</div>

		<div class="form-group form-group-sm" id="movementtype_bank_trx" style="display:none">
			<?php echo form_label($this->lang->line('invoices_trx_number'), 'trx_number', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'trx_number',
						'id'=>'trx_number',
						'class'=>'form-control input-sm',
						'value'=>$invoice_info->trx_number)
						);?>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
	var details = document.getElementsByName('details[]');
	var qtys = document.getElementsByName('qtys[]');
	var prices = document.getElementsByName('prices[]');
	var amounts = document.getElementsByName('amounts[]');
	var contador=details.length;

	function agrega_campos(){
		$("#item_details").append("<tr id='"+contador+"' >"+
		"<td><input type='number' name='qtys[]' id='quantity_"+contador+"' onKeyUp='return calculateTotal("+contador+")' class='form-control input-sm' value='0.00'/></td>"+
		"<td><input type='text' name='details[]' id='detail_"+contador+"' class='form-control input-sm'/></td>"+
		"<td><input type='number' name='prices[]' id='price_"+contador+"' onKeyUp='return calculateTotal("+contador+")' class='form-control input-sm' value='0.00'/></td>"+
		"<td><input type='number' readonly name='amounts[]' id='amount_"+contador+"' class='form-control input-sm' value='0.00'/></td>"+
		"<td><button type='button' class='btn btn-info btn-sm pull-right' onclick='elimina_me("+contador+")'><span class='glyphicon glyphicon-minus'></span></button></td>"+
		"</tr>");
		contador++;
	}
	function elimina_me(elemento){
		$("#"+elemento).remove();
		for(var i=0;i<details.length;i++){
			details[i].removeAttribute('id');
			qtys[i].removeAttribute('id');
			prices[i].removeAttribute('id');
			amounts[i].removeAttribute('id');
		}
		for(var i=0;i<details.length;i++){
			details[i].setAttribute('id','detail_'+i);
			qtys[i].setAttribute('id','quantity_'+i);
			prices[i].setAttribute('id','price_'+i);
			amounts[i].setAttribute('id','amount_'+i);
		}
		contador--;
		//	Recalculamos el subtotal y total

		var subtotal = parseFloat(obtenerTotal()).toFixed(2);
		$('#subtotal').val(subtotal);
		var percent = (parseFloat($('#discount').val()) / 100).toFixed(4);
		var discountamt = (parseFloat(subtotal) * parseFloat(percent)).toFixed(2);
		$('#discountamt').val(discountamt);
		var tax = (parseFloat($('#tax').val()) / 100).toFixed(4);
		var taxamt = (parseFloat(subtotal) * parseFloat(tax)).toFixed(2);
		$('#taxamt').val(taxamt);
		var totalamt = ((parseFloat(subtotal)-parseFloat(discountamt)+parseFloat(taxamt))).toFixed(2);
		$('#totalamt').val(totalamt);
	}

	function obtenerTotal()
	{
		var amounts = document.getElementsByName('amounts[]');
		var total=0;
		for(var i=0;i<amounts.length;i++)
		{
			total = parseFloat(total) + parseFloat(amounts[i].value);
		}
		return total;
	}

	function calculateTotal(pos)
	{
		var total = parseFloat($('#price_'+pos).val()) * parseFloat($('#quantity_'+pos).val());
		$('#amount_'+pos).val(total.toFixed(2));
		var subtotal = parseFloat(obtenerTotal()).toFixed(2);
		$('#subtotal').val(subtotal);
		var percent = parseFloat($('#discount').val() / 100).toFixed(4);
		var discountamt = (parseFloat(subtotal) * parseFloat(percent)).toFixed(2);
		$('#discountamt').val(discountamt);
		var tax = (parseFloat($('#tax').val()) / 100).toFixed(4);
		var taxamt = (parseFloat(subtotal) * parseFloat(tax)).toFixed(2);
		$('#taxamt').val(taxamt);
		var totalamt = ((parseFloat(subtotal)-parseFloat(discountamt)+parseFloat(taxamt))).toFixed(2);
		$('#totalamt').val(totalamt);
	}

</script>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	<?php if($readonlyform): ?>
		$("#invoice_edit_form :input").prop("disabled", true);
	<?php endif;?>

	<?php $this->load->view('partial/datepicker_locale'); ?>

	$('#documentdate').datetimepicker({
		format: "<?php echo dateformat_bootstrap($this->config->item('dateformat')) . ' ' . dateformat_bootstrap($this->config->item('timeformat'));?>",
		startDate: "<?php echo date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), mktime(0, 0, 0, 1, 1, 2010));?>",
		<?php
		$t = $this->config->item('timeformat');
		$m = $t[strlen($t)-1];
		if( strpos($this->config->item('timeformat'), 'a') !== false || strpos($this->config->item('timeformat'), 'A') !== false )
		{
		?>
			showMeridian: true,
		<?php
		}
		else
		{
		?>
			showMeridian: false,
		<?php
		}
		?>
		minuteStep: 1,
		autoclose: true,
		todayBtn: true,
		todayHighlight: true,
		bootcssVer: 3,
		language: '<?php echo current_language(); ?>'
	});

	//	Change with of modal form
	var wide = $('.modal-dialog').css('width');
    var calculate = parseInt(wide, 10)*2;

    $('.modal-dialog').css('width', calculate);

	var fill_value = function(event, ui) {
		event.preventDefault();
		$('#person_id').val(ui.item.value);
		$('#person').val(ui.item.label);
		$('#ruc').val(ui.item.ruc);
		$('#company_name').val(ui.item.company_name);
	};

	$("#person").autocomplete({
		source: "<?php echo site_url('invoices/suggest_customers');?>",
		delay: 10,
		appendTo: '.modal-content',
		cacheLength: 1,
		select: fill_value
	});

	$('input[name="movementtype"]').click(function(){
		if(this.checked){
			if(this.value == "B"){
				$('#movementtype_bank_trx').show();
				$("#trx_number").addClass("required");
			}
			else{
				$('#movementtype_bank_trx').hide();
				$("#trx_number").removeClass("required");
			}
		}
	});

	if($("input[name='movementtype']:checked").val() == "B")
	{
		$('#movementtype_bank_trx').show();
		$("#trx_number").addClass("required");
	}

	$('#discount').change(function(){
		var subtotal = parseFloat($('#subtotal').val()).toFixed(2);
		var percent = (parseFloat($(this).val()) / 100).toFixed(4);
		var discountamt = (parseFloat(subtotal) * parseFloat(percent)).toFixed(2);
		$('#discountamt').val(discountamt);
		var tax = (parseFloat($('#tax').val()) / 100).toFixed(4);
		var taxamt = (parseFloat(subtotal) * parseFloat(tax)).toFixed(2);
		$('#taxamt').val(taxamt);
		var totalamt = ((parseFloat(subtotal)-parseFloat(discountamt)+parseFloat(taxamt))).toFixed(2);
		$('#totalamt').val(totalamt);
	});

	$('#invoice_edit_form').validate($.extend({
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
			<?php if($this->config->item('invoice_number_automatic') == '0'):?>
				serieno: 'required',
			<?php endif;?>
			documentdate: 'required',
			person: 'required',
			totalamt: 'required',
			trx_number: {
				required: "#cash_typeB:checked"
			}
		},

		messages:
		{
			<?php if($this->config->item('invoice_number_automatic') == '0'):?>
				serieno: "<?php echo $this->lang->line('invoice_serieno_required'); ?>",
			<?php endif;?>
			documentdate: "<?php echo $this->lang->line('invoice_documentdate_required'); ?>",
			person: "<?php echo $this->lang->line('invoice_name_required'); ?>",
			totalamt: "<?php echo $this->lang->line('invoice_total_required'); ?>",
			trx_number: "<?php echo $this->lang->line('invoice_trx_number_required'); ?>"
		}
	}, form_support.error));
});
</script>
