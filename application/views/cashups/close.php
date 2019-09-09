<div id="required_fields_message"><?php echo $this->lang->line('cashups_confirm_close'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('cashups/closed/'.$cashup_info->cashup_id, array('id'=>'cashup_delete_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="cashups">
		<div class="form-group form-group-sm">	
			<div class="form-group">
                <div class="col-sm-offset-5 col-sm-10">
                    <label class="radio-inline">
                        <input type="radio" name="confirm" id="confirm1" value="Y" checked="checked"> <?php echo $this->lang->line('common_yes'); ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="confirm" id="confirm0" value="N"> <?php echo $this->lang->line('common_no'); ?>
                    </label>
                </div>
            </div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_close_date'), 'close_date', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'close_date',
							'id'=>'close_date',
							'class'=>'form-control input-sm',
							'readonly' => TRUE,
							'value'=>to_datetime(strtotime($cashup_info->close_date)))
							);?>
					<?php echo form_input(array(
							'name'=>'close_employee_id',
							'id'=>'close_employee_id',
							'class'=>'form-control input-sm',
							'type' => 'hidden',
							'value'=>$cashup_info->open_employee_id)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label('Total '.$this->lang->line('cashups_income'), 'income', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'income',
						'id'=>'income',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => 0.01,
						'readonly' => TRUE,
						'value'=>$cashup_info->income)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">	
			<?php echo form_label('Total '.$this->lang->line('cashups_cost'), 'cost', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'cost',
						'id'=>'cost',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => 0.01,
						'readonly' => TRUE,
						'value'=>$cashup_info->cost)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">	
			<?php echo form_label('Total '.$this->lang->line('cashups_expense'), 'expense', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'expense',
						'id'=>'expense',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => 0.01,
						'readonly' => TRUE,
						'value'=>$cashup_info->expense)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('cashups_closed_amount_cash'), 'closed_amount_total', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'closed_amount_total',
						'id'=>'closed_amount_total',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$cashup_info->closed_amount_total)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<div class='col-xs-16'>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<td><?php echo $this->lang->line('overall_cashs_denomination')?></td>
							<td><?php echo $this->lang->line('overall_cashs_quantity')?></td>
							<td><?php echo $this->lang->line('overall_cashs_amount')?></td>
							<td><button type="button" onclick="agrega_campos()" class='btn btn-info btn-sm pull-right'><span class="glyphicon glyphicon-plus"></span></button></td>
						</tr>
					</thead>
					<tbody id="currency_denominations">
					</tbody>
				</table>
			</div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
	//	Currency
	var denominations = document.getElementsByName('denominations[]');
	var quantities = document.getElementsByName('quantities[]');
	var line_amounts = document.getElementsByName('line_amounts[]');
	var contador=line_amounts.length;

	function agrega_campos(){
		$("#currency_denominations").append("<tr id='cur_"+contador+"' >"+
			"<td><select id='denomination_"+contador+"' name='denominations[]' class='form-control input-sm' ><option value='200 - Billete' data-amount='200'>200 - Billete</option><option value='100 - Billete' data-amount='100'>100 - Billete</option><option value='50 - Billete' data-amount='50'>50 - Billete</option><option value='20 - Billete' data-amount='20'>20 - Billete</option><option value='10 - Billete' data-amount='10'>10 - Billete</option><option value='5 - Moneda' data-amount='5'>5 - Moneda</option><option value='2 - Moneda' data-amount='2'>2 - Moneda</option><option value='1 - Moneda' data-amount='1'>1 - Moneda</option><option value='0.50 - Moneda' data-amount='0.5'>0.50 - Moneda</option><option value='0.20 - Moneda' data-amount='0.2'>0.20 - Moneda</option><option value='0.10 - Moneda' data-amount='0.1'>0.10 - Moneda</option><option value='0.05 - Moneda' data-amount='0.05'>0.05 - Moneda</option></select></td>"+
			"<td><input type='number' name='quantities[]' id='quantity_"+contador+"' class='form-control input-sm' value='0' onchange='return calcularTotal("+contador+",this.value);'></td>"+
			"<td><input type='number' name='line_amounts[]' id='line_amount_"+contador+"' class='form-control input-sm' value='0' readonly></td>"+
			"<td><button type='button' class='btn btn-info btn-sm pull-right' onclick='elimina_me("+contador+")'><span class='glyphicon glyphicon-minus'></span></button></td>"+
			"</tr>");
		contador++;
		$('#quantity_'+contador).focus();
	}
	function elimina_me(elemento){
		$("#cur_"+elemento).remove();
		for(var i=0;i<line_amounts.length;i++){
			denominations[i].removeAttribute('id');
			quantities[i].removeAttribute('id');
			line_amounts[i].removeAttribute('id');
		}
		for(var i=0;i<line_amounts.length;i++){
			denominations[i].setAttribute('id','denomination_'+i);
			quantities[i].setAttribute('id','quantity_'+i);
			line_amounts[i].setAttribute('id','line_amount_'+i);
		}
		contador--;
	}

	function calcularTotal(pos,value)
	{
		var amount = parseFloat($("#denomination_"+pos).find(':selected').attr("data-amount"));
		var total = amount * parseFloat(value);
		var lineAmounts = document.getElementsByName('line_amounts[]');
		totalLineAmt = 0;
		for(var i=0;i<lineAmounts.length;i++)
		{
			totalLineAmt = parseFloat(totalLineAmt) + parseFloat(lineAmounts[i].value);
		}
		totalLineAmt = parseFloat(totalLineAmt) + parseFloat(total) - parseFloat($("#line_amount_"+pos).val());

		if(parseFloat(totalLineAmt) > parseFloat($('#closed_amount_total').val()))
		{
			var diferencia = parseFloat(totalLineAmt) - parseFloat($('#closed_amount_total').val());
			alert("No se puede exceder del saldo de cierre: "+$('#closed_amount_total').val()+"\nTotal a asignar: "+totalLineAmt+"\nDiferencia: "+diferencia);
			$("#quantity_"+pos).val(0);
			$("#line_amount_"+pos).val(0);
		}
		else
		{
			$("#line_amount_"+pos).val(total.toFixed(2));
		}
	}

	function check_declaration_currency()
	{
		var flag = false;
		var lineAmounts = document.getElementsByName('line_amounts[]');
		var totalLineAmt = 0;
		for(var i=0;i<lineAmounts.length;i++)
		{
			totalLineAmt = parseFloat(totalLineAmt) + parseFloat(lineAmounts[i].value);
		}
		if(parseFloat(totalLineAmt) == parseFloat($('#closed_amount_total').val()))
		{
			flag = true
		}
		else
		{
			var diferencia = parseFloat($('#closed_amount_total').val()) - parseFloat(totalLineAmt);
			alert("Debe desglozar la denominacion de efectivo que maneja en caja:\nSaldo de cierre: "+$('#closed_amount_total').val()+"\nTotal desglozado: "+totalLineAmt+"\nDiferencia: "+diferencia);
		}

		return flag;
	}

</script>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	//	Change with of modal form
	var wide = $('.modal-dialog').css('width');
    var calculate = parseInt(wide, 10)*1;

    $('.modal-dialog').css('width', calculate);

	<?php $this->load->view('partial/datepicker_locale'); ?>

	$('#close_date').datetimepicker({
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

	$('#cashup_delete_form').validate($.extend({
		submitHandler: function(form) {

			if(check_declaration_currency())
			{
				$(form).ajaxSubmit({
					success: function(response)
					{
						dialog_support.hide();
						table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
						setTimeout(function(){
							self.parent.location.reload();
						},2000)
					},
					dataType: 'json'
				});
			}
		}
	}, form_support.error));
});
</script>