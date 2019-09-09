<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('cashups/save_cost/'.$cost_info->cost_id, array('id'=>'cost_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="costs">
		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('costs_documentdate'), 'documentdate', array('class'=>'required control-label col-xs-2')); ?>
			<div class='col-xs-4'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
				<?php echo form_input(array(
						'name'=>'documentdate',
						'id'=>'documentdate',
						'class'=>'form-control input-sm',
						'value'=>to_datetime(strtotime($cost_info->documentdate)))
						);?>
				</div>
			</div>
			<div class='col-xs-6'>
				<?php echo form_label($this->lang->line('costs_documentno'), 'documentno', array('class'=>'required control-label col-xs-4')); ?>
				<div class='col-xs-6'>
					<?php if($this->config->item('cost_number_automatic') == '1')
					{
						echo form_input(array(
							'name'=>'documentno',
							'id'=>'documentno',
							'class'=>'form-control input-sm',
							'readonly' => TRUE,
							'value'=>$cost_info->documentno)
							);
					}
					else
					{
						echo form_input(array(
							'name'=>'documentno',
							'id'=>'documentno',
							'class'=>'form-control input-sm',
							'value'=>$cost_info->documentno)
							);
					}
					?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('costs_person'), 'person', array('class'=>'required control-label col-xs-2')); ?>
			<div class='col-xs-4'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-search"></span></span>
					<?php echo form_input(array(
						'name'=>'person',
						'id'=>'person',
						'class'=>'form-control input-sm',
						'value'=>$cost_info->person)
						);?>
					<?php echo form_input(array(
						'name'=>'person_id',
						'id'=>'person_id',
						'class'=>'form-control input-sm',
						'type' => 'hidden',
						'value'=>$cost_info->person_id)
						);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('costs_cash_concept_id'), 'cash_concept_id', array('class'=>'required control-label col-xs-2')); ?>
			<div class='col-xs-4'>
				<?php echo form_dropdown('cash_concept_id',$cash_concepts,$selected_cash_concept,array('id' => 'cash_concept_id', 'class' => 'form-control input-sm'));?>
			</div>
			<div class='col-xs-6'>
				<?php echo form_label($this->lang->line('costs_cash_subconcept_id'), 'cash_subconcept_id', array('class'=>'required control-label col-xs-4')); ?>
				<div class='col-xs-6'>
					<?php echo form_dropdown('cash_subconcept_id',$cash_subconcepts,$selected_cash_subconcept,array('id' => 'cash_subconcept_id', 'class' => 'form-control input-sm'));?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm" id="voucher_operation_related" style="display: none;">	
			<?php echo form_label($this->lang->line('costs_voucher_operation_id'), 'voucher_operation_id', array('class'=>'required control-label col-xs-2')); ?>
			<div class='col-xs-4'>
				<?php echo form_dropdown('voucher_operation_id',$voucher_operations,$selected_voucher_operation,array('id' => 'voucher_operation_id', 'class' => 'form-control input-sm'));?>
			</div>
			<div class='col-xs-6'>
				<?php echo form_label($this->lang->line('costs_openamt'), 'openamt', array('class'=>'control-label col-xs-4')); ?>
				<div class='col-xs-6'>
					<?php echo form_input(array(
						'name'=>'openamt',
						'id'=>'openamt',
						'class'=>'form-control input-sm',
						'type' => 'numner',
						'readonly' => TRUE,
						'value'=>$voucher_openamt)
						);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('costs_detail'), 'detail', array('class'=>'control-label col-xs-2')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'detail',
						'id'=>'detail',
						'class'=>'form-control input-sm',
						'value'=>$cost_info->detail)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('costs_amount'), 'amount', array('class'=>'required control-label col-xs-2')); ?>
			<div class='col-xs-4'>
				<?php echo form_input(array(
						'name'=>'amount',
						'id'=>'amount',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => 0.01,
						'max' => $cash_ups_info->closed_amount_total,
						'value'=>$cost_info->amount)
						);?>
				<?php echo form_input(array(
					'name'=>'cashup_id',
					'id'=>'cashup_id',
					'class'=>'form-control input-sm',
					'type' => 'hidden',
					'value'=>$cash_ups_info->cashup_id)
					);?>
				<?php echo form_input(array(
					'name'=>'cash_book_id',
					'id'=>'cash_book_id',
					'class'=>'form-control input-sm',
					'type' => 'hidden',
					'value'=>$cash_ups_info->cash_book_id)
					);?>
				<?php echo form_input(array(
					'name'=>'cash_balance',
					'id'=>'cash_balance',
					'class'=>'form-control input-sm',
					'type' => 'hidden',
					'value'=>$cash_ups_info->closed_amount_total)
					);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('costs_type'), 'movementtype', !empty($basic_version) ? array('class'=>'required control-label col-xs-2') : array('class'=>'control-label col-xs-2')); ?>
			<div class="col-xs-8">
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'movementtype',
							'type'=>'radio',
							'id'=>'movementtypeC',
							'value'=>'C',
							'checked'=>(!empty($cost_info->movementtype) ? ($cost_info->movementtype === 'C') : TRUE ))
							); ?> <?php echo $this->lang->line('costs_movementtype_cash'); ?>
				</label>
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'movementtype',
							'type'=>'radio',
							'id'=>'movementtypeB',
							'value'=>'B',
							'checked'=>$cost_info->movementtype === 'B')
							); ?> <?php echo $this->lang->line('costs_movementtype_bank'); ?>
				</label>

			</div>
		</div>

		<div class="form-group form-group-sm" id="movementtype_bank_trx" style="display:none">
			<?php echo form_label($this->lang->line('overall_cashs_financialentity'), 'bankaccount_id', array('class'=>'required control-label col-xs-2')); ?>
			<div class='col-xs-4'>
				<select name="bankaccount_id" id="bankaccount_id" class="form-control input-sm">
					<?php foreach ($bankaccounts as $id => $text){
						echo "<option value='".$id."' ".($id==$selected_bankaccount ? "selected" : "")." data-currency=".$bankaccounts_cur[$id].">".$text."</option>";
					}?>
				</select>
			</div>
			<div class='col-xs-6'>
				<?php echo form_label($this->lang->line('costs_trx_number'), 'trx_number', array('class'=>'control-label col-xs-3')); ?>
				<div class='col-xs-6'>
					<?php echo form_input(array(
							'name'=>'trx_number',
							'id'=>'trx_number',
							'class'=>'form-control input-sm',
							'value'=>$cost_info->trx_number)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm" id="currency_trx" style="display:none">
			<?php echo form_label($this->lang->line('costs_currency'), 'currency', !empty($basic_version) ? array('class'=>'required control-label col-xs-3') : array('class'=>'control-label col-xs-2')); ?>
			<div class="col-xs-4">
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'currency',
							'type'=>'radio',
							'id'=>'currency'.CURRENCY,
							'value'=> CURRENCY,
							'checked'=>(!empty($cost_info->currency) ? ($cost_info->currency === CURRENCY) : TRUE ))
							); ?> <?php echo CURRENCY_LABEL; ?>
				</label>
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'currency',
							'type'=>'radio',
							'id'=>'currency'.USDCURRENCY,
							'value'=>USDCURRENCY,
							'checked'=>$cost_info->currency === USDCURRENCY)
							); ?> <?php echo USDCURRENCY_LABEL; ?>
				</label>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

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

	var fill_value = function(event, ui) {
		event.preventDefault();
		$('#person_id').val(ui.item.value);
		$('#person').val(ui.item.label);
	};

	$("#person").autocomplete({
		source: "<?php echo site_url('cashups/suggest_person');?>",
		delay: 1,
		appendTo: '.modal-content',
		cacheLength: 1,
		select: fill_value
	});

	/*$('#bankaccount_id').change(function(){
		var cur = $(this).find(':selected').attr('data-currency');
		$('#currency'+cur).prop("checked", true);
		if(cur=="<?php echo CURRENCY;?>")
		{
			$('#amount').attr("max",$('#cash_balance').val());
			if(parseFloat($('#amount').val()) > parseFloat($('#cash_balance').val()))
			{
				$('#amount').val($('#cash_balance').val());
			}
		}
		else
		{
			$('#amount').attr("max",$('#usdcash_balance').val());	
			if(parseFloat($('#amount').val()) > parseFloat($('#usdcash_balance').val()))
			{
				$('#amount').val($('#usdcash_balance').val());
			}
		}
	});*/

	$('#cash_concept_id').change(function(){
		var cash_concept_id = $(this).find(':selected').val();
		if(cash_concept_id!='-1'){
			$.ajax({
				type: 'GET',
				url: "<?php echo site_url('cashups/get_subconcept/"+cash_concept_id+"'); ?>",
				dataType: 'json',
				success: function(resp){
					var options;
					options += "<option value='-1' selected><?php echo $this->lang->line('common_none_selected_text');?></option>";
					for(var i=0; i< resp.length; i++)
					{
						options += "<option value='"+resp[i].cash_concept_id+"' data-voucheroperation='"+resp[i].affected_voucheroperation+"'>"+resp[i].name+"</option>";
					}
					$('#cash_subconcept_id').html(options);
		        },
		        error: function(jqXHR, textStatus, errorThrown){
		            console.log(jqXHR);
		        }
			})
		}
		else
		{
			$('#cash_subconcept_id').empty();
		}
	});

	$('#cash_subconcept_id').change(function(){
		var managevoucher = $(this).find(':selected').attr('data-voucheroperation');
		if(managevoucher == 1)
		{
			$('#voucher_operation_related').show();
			var person_id = $('#person_id').val();
			$.ajax({
				type: 'GET',
				url: "<?php echo site_url('cashups/get_vouchers/"+person_id+"'); ?>",
				dataType: 'json',
				success: function(resp){
					var options;
					options += "<option value='-1' selected><?php echo $this->lang->line('common_none_selected_text');?></option>";
					for(var i=0; i< resp.length; i++)
					{
						options += "<option value='"+resp[i].voucher_operation_id+"' data-openamount="+resp[i].openamt+">"+resp[i].serieno+"-"+resp[i].voucher_operation_number+"</option>";
					}
					$('#voucher_operation_id').html(options);
		        },
		        error: function(jqXHR, textStatus, errorThrown){
		            console.log(jqXHR);
		        }
			})
		}
		else
		{
			$('#voucher_operation_related').hide();
			$('#voucher_operation_id').empty();
			$('#openamt').val(0);
		}

	});

	<?php if(!empty($cost_info->voucher_operation_id)):?>
		$('#voucher_operation_related').show();
	<?php endif;?>

	$('#voucher_operation_id').change(function(){
		var openamt = $(this).find(':selected').attr('data-openamount');
		$('#openamt').val(openamt);
	})

	// add the rule here
	jQuery.validator.addMethod(
		"notEqualTo",
		function(elementValue,element,param) {
			return elementValue != param;
		},
		""
	);

	$.validator.addMethod("greaterThan",
	    function (value, element, param) {
	          var $otherElement = $(param);

	          $.validator.messages["greaterThan"] = "<?php echo $this->lang->line('cost_amount_greater_than_balance'); ?> "+$otherElement.val();

	          return parseFloat(value) <= parseFloat($otherElement.val());
	    },''
	);

	$('#cost_edit_form').validate($.extend({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)
				{
					dialog_support.hide();
					table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
					setTimeout(function(){
						self.parent.location.reload();
					},2000);
				},
				dataType: 'json'
			});
		},

		errorLabelContainer: '#error_message_box',

		rules:
		{
			<?php if($this->config->item('cost_number_automatic') == '0'):?>
				documentno: 'required',
			<?php endif;?>
			documentdate: 'required',
			person: 'required',
			bankaccount_id: {
				required: true,
				notEqualTo: -1
			},
			cash_concept_id: {
				required: true,
				notEqualTo: -1
			},
			cash_subconcept_id: {
				required: true,
				notEqualTo: -1
			},
			amount: {
				required: true,
				max: parseFloat($("#amount").attr("max")),
				greaterThan: "#cash_balance"
			},
			trx_number: {
				required: "#movementtypeB:checked"
			}
		},

		messages:
		{
			<?php if($this->config->item('cost_number_automatic') == '0'):?>
				documentno: "<?php echo $this->lang->line('cost_documentno_required'); ?>",
			<?php endif;?>
			documentdate: "<?php echo $this->lang->line('cost_documentdate_required'); ?>",
			person: "<?php echo $this->lang->line('cost_person_required'); ?>",
			bankaccount_id: "<?php echo $this->lang->line('cost_bankaccount_id_required'); ?>",
			cash_concept_id: "<?php echo $this->lang->line('cost_cash_concept_id_required'); ?>",
			cash_subconcept_id: "<?php echo $this->lang->line('cost_cash_subconcept_id_required'); ?>",
			amount: {
				required: "<?php echo $this->lang->line('cost_amount_required'); ?>",
				greaterThan: "<?php echo $this->lang->line('cost_amount_greater_than_balance'); ?> "+$("#cash_balance").val()
			},
			trx_number: "<?php echo $this->lang->line('cost_trx_number_required'); ?>"
		}
	}, form_support.error));

});
</script>
