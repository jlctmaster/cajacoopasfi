<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('loans_credits/save_credit/'.$credit_info->credit_id, array('id'=>'credit_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="loans">
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
						'name'=>'dni',
						'id'=>'dni',
						'class'=>'form-control input-sm',
						'value'=>$credit_info->dni,
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
						'value'=>$credit_info->name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_creditdate'), 'creditdate', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'creditdate',
							'id'=>'creditdate',
							'class'=>'form-control input-sm',
							'type' => 'text',
							'readonly' => TRUE,
							'value'=>to_date(strtotime($credit_info->creditdate)))
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
					'value'=>$credit_info->cuote)
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
							'type' => 'text',
							'value'=>to_date(strtotime($credit_info->returndate)))
							); ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_add_items'), 'items', array('class'=>'required control-label col-xs-8')); ?>
			<div class='col-xs-16'>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<td><?php echo $this->lang->line('loans_credits_location_id')?></td>
							<td><?php echo $this->lang->line('loans_credits_item_id')?></td>
							<td><?php echo $this->lang->line('loans_credits_quantity')?></td>
							<td><?php echo $this->lang->line('loans_credits_price')?></td>
							<td><?php echo $this->lang->line('loans_credits_import')?></td>
							<td><button type="button" onclick="agrega_campos()" class='btn btn-info btn-sm pull-right'><span class="glyphicon glyphicon-plus"></span></button></td>
						</tr>
					</thead>
					<tbody id="item_details">
						<?php if(!empty($credit_item_info[0]->item_id)):
							$contador = 0; ?>
						<?php 	foreach ($credit_item_info as $detail_info):?>
							<tr id='<?php echo $contador;?>' >
								<td><input type='text' name='locations[]' id='location_<?php echo $contador;?>' onKeyUp='return ACLocation(this.id,"loans_credits/suggest_location",<?php echo $contador;?>)' title='<?php echo $this->lang->line('loans_credits_selected_locations')?>' placeholder='<?php echo $this->lang->line('loans_credits_selected_locations')?>' class='form-control input-sm' value="<?php echo $detail_info->location_id.'_'.$detail_info->location_name; ?>"></td>
								<td><input type='text' name='items[]' id='item_<?php echo $contador;?>' onKeyUp='return ACDataGrid(this.id,"items/suggest_by_location",<?php echo $contador;?>)' title='<?php echo $this->lang->line('loans_credits_selected_items')?>' placeholder='<?php echo $this->lang->line('loans_credits_selected_items')?>' class='form-control input-sm' value="<?php echo $detail_info->item_id.'_'.$detail_info->name; ?>"></td>
								<td><input type='number' name='qtys[]' id='quantity_<?php echo $contador;?>' onKeyUp='return validateStock(this.id,<?php echo $contador;?>)' title='<?php echo $this->lang->line('loans_credits_input_quantity')?>' placeholder='<?php echo $this->lang->line('loans_credits_input_quantity')?>' class='form-control input-sm' value="<?php echo $detail_info->quantity; ?>"></td>
								<td><input type='number' name='prices[]' id='price_<?php echo $contador;?>' onKeyUp='return updateAmount(this.id,<?php echo $contador;?>)' title='<?php echo $this->lang->line('loans_credits_input_price')?>' placeholder='<?php echo $this->lang->line('loans_credits_input_price')?>' class='form-control input-sm' value="<?php echo $detail_info->price; ?>"></td>
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
			<?php echo form_label($this->lang->line('loans_credits_amount'), 'amount', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'amount',
						'id'=>'amount',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'readonly' => TRUE,
						'step' => 0.01,
						'max' => $cashups->closed_amount_total,
						'value'=>(!empty($credit_info->amount) ? $credit_info->amount : 0))
						); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_percent'), 'percent', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'percent_monthly',
						'id'=>'percent_monthly',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'value'=>(!empty($credit_info->percent_monthly) ? $credit_info->percent_monthly : 0))
						); ?>
				<?php echo form_input(array(
						'name'=>'percent',
						'id'=>'percent',
						'class'=>'form-control input-sm',
						'type' => 'hidden',
						'value'=>(!empty($credit_info->percent) ? $credit_info->percent : 0))
						); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('loans_credits_interest_daily'), 'amt_interest', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'amt_interest',
						'id'=>'amt_interest',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'readonly' => TRUE,
						'value'=>(!empty($credit_info->amt_interest) ? $credit_info->amt_interest : 0))
						); ?>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
	var locations = document.getElementsByName('locations[]');
	var items = document.getElementsByName('items[]');
	var qtys = document.getElementsByName('qtys[]');
	var prices = document.getElementsByName('prices[]');
	var amounts = document.getElementsByName('amounts[]');
	var contador=items.length;

	var lbLocation = "<?php echo $this->lang->line('loans_credits_selected_locations')?>";
	var lbItem = "<?php echo $this->lang->line('loans_credits_selected_items')?>";
	var lbQty = "<?php echo $this->lang->line('loans_credits_input_quantity')?>";
	var lbPrice = "<?php echo $this->lang->line('loans_credits_input_price')?>";

	function agrega_campos(){
		$("#item_details").append("<tr id='"+contador+"' >"+
		"<td><input type='text' name='locations[]' id='location_"+contador+"' onKeyUp='return ACLocation(this.id,\"loans_credits/suggest_location\","+contador+")' title='"+lbLocation+"' placeholder='"+lbLocation+"' class='form-control input-sm'/></td>"+
		"<td><input type='text' name='items[]' id='item_"+contador+"' onKeyUp='return ACDataGrid(this.id,\"items/suggest_by_location\","+contador+")' title='"+lbItem+"' placeholder='"+lbItem+"' class='form-control input-sm'/></td>"+
		"<td><input type='number' name='qtys[]' id='quantity_"+contador+"' onKeyUp='return validateStock(this.id,"+contador+")' title='"+lbQty+"' placeholder='"+lbQty+"' class='form-control input-sm'/></td>"+
		"<td><input type='number' name='prices[]' id='price_"+contador+"' onKeyUp='return updateAmount(this.id,"+contador+")' title='"+lbPrice+"' placeholder='"+lbPrice+"' class='form-control input-sm'/></td>"+
		"<td><input type='number' readonly name='amounts[]' id='amount_"+contador+"' class='form-control input-sm'/></td>"+
		"<td><button type='button' class='btn btn-info btn-sm pull-right' onclick='elimina_me("+contador+")'><span class='glyphicon glyphicon-minus'></span></button></td>"+
		"</tr>");
		contador++;
	}
	function elimina_me(elemento){
		$("#"+elemento).remove();
		for(var i=0;i<items.length;i++){
			locations[i].removeAttribute('id');
			items[i].removeAttribute('id');
			qtys[i].removeAttribute('id');
			prices[i].removeAttribute('id');
			amounts[i].removeAttribute('id');
		}
		for(var i=0;i<items.length;i++){
			locations[i].setAttribute('id','location_'+i);
			items[i].setAttribute('id','item_'+i);
			qtys[i].setAttribute('id','quantity_'+i);
			prices[i].setAttribute('id','price_'+i);
			amounts[i].setAttribute('id','amount_'+i);
		}
		contador--;
	}

	function ACLocation(obj,url,pos){
	    $('#'+obj).autocomplete({
	        source: function(request, response) {
	            $.ajax({
	                url: "<?php echo site_url('"+url+"');?>",
	                dataType: "json",
	                data: request,
	                success: function(data) {
	                    response(data);
	                }
	            });
	        },
	        minLength:1,
	        select: function(event,ui){
	        	event.preventDefault();
	        	$('#'+obj).val(ui.item.value+"_"+ui.item.label);
	        }
	    });
	}

	function ACDataGrid(obj,url,pos){
	    $('#'+obj).autocomplete({
	        source: function(request, response) {
	        	var locations = $('#location_'+pos).val().split('_');
	        	var Data = {items:[{term : request.term, location_id: locations[0]}]};
	            $.ajax({
	                url: "<?php echo site_url('"+url+"');?>",
	                dataType: "json",
	                data: request,
	                success: function(data) {
	                    response(data);
	                }
	            });
	        },
	        minLength:1,
	        select: function(event,ui){
	        	event.preventDefault();
	        	var locations = $('#location_'+pos).val().split('_');
	        	$('#'+obj).val(ui.item.value+"_"+ui.item.label);
	        	$.ajax({
					type: 'GET',
					url: "<?php echo site_url('items/get_item_info/"+ui.item.value+"/"+locations[0]+"'); ?>",
					dataType: 'json',
					success: function(resp){
						console.log(resp);
						if(resp.length > 0){
			            	$('#quantity_'+pos).attr("max",resp[0].quantity);
			            	$('#price_'+pos).val(resp[0].unit_price);	
						}
						else
						{
							alert("No hay Existencia");
							$('#'+obj).val("");
							$('#'+obj).focus();
						}
			        },
			        error: function(jqXHR, textStatus, errorThrown){
			            console.log(jqXHR);
			        }
				})
	        }
	    });
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

	function validateStock(obj,pos)
	{
		var attr = $('#'+obj).attr('max'); 
		if (typeof attr !== typeof undefined && attr !== false) {
			if(parseFloat($('#'+obj).val()) > parseFloat(attr)){
				alert("No puede pedir más de "+attr);
				$('#'+obj).val(attr);
				$('#'+obj).focus();
			}
		}
		var total = parseFloat($('#'+obj).val()) * parseFloat($('#price_'+pos).val());
		$('#amount_'+pos).val(total);
		$('#amount').val(obtenerTotal());
	}

	function updateAmount(obj,pos)
	{
		var total = parseFloat($('#'+obj).val()) * parseFloat($('#quantity_'+pos).val());
		$('#amount_'+pos).val(total);
		$('#amount').val(obtenerTotal());
	}

</script>

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
		let percent = parseFloat($(this).val() / 30);
		let amount = parseFloat($('#amount').val());
		let amt_interest = amount * (percent / 100);
		$('#percent').val(percent);
		$('#amt_interest').val(amt_interest.toFixed(2));
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
		let loandate = moment($('#creditdate').val(), '<?php echo dateformat_momentjs($this->config->item("dateformat"))?>');
		let returndate = moment($(this).val(), '<?php echo dateformat_momentjs($this->config->item("dateformat"))?>');
		let cuote = returndate.diff(loandate,'months',true);
		$('#cuote').val(cuote.toFixed(0));
	});
	
	$('#credit_edit_form').validate($.extend({
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
			creditdate: 'required',
			returndate: {
            	required: true,
            	greaterThan: '#creditdate'
			},
			amount: {
				required: true,
				max: $('#amount').attr("max")
			}
		},

		messages:
		{
			dni: "<?php echo $this->lang->line('loan_credit_dni_required'); ?>",
			creditdate: "<?php echo $this->lang->line('credit_creditdate_required'); ?>",
			returndate: {
				required: "<?php echo $this->lang->line('loan_credit_returndate_required'); ?>"
			},
			amount: {
				required: "<?php echo $this->lang->line('loan_credit_amount_required'); ?>",
				max: "<?php echo $this->lang->line('cashups_amount_not_exceeded'); ?>"+$('#amount').attr("max")
			}
		}
	}, form_support.error));
});
</script>
