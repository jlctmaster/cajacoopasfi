<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('creditnotes/save/'.$creditnote_info->creditnote_id, array('id'=>'creditnote_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="creditnotes">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('creditnotes_documentdate'), 'documentdate', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'documentdate',
							'id'=>'documentdate',
							'class'=>'form-control input-sm',
							'value'=>to_datetime(strtotime($creditnote_info->documentdate)))
							); ?>
				</div>
			</div>
		</div>

		<div class='form-group form-group-sm'>
			<?php echo form_label($this->lang->line('creditnotes_documentno'), 'documentno', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php if($this->config->item('creditnote_number_automatic') == '1')
				{
					echo form_input(array(
						'name'=>'documentno',
						'id'=>'documentno',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$creditnote_info->documentno)
						);
				}
				else
				{
					echo form_input(array(
						'name'=>'documentno',
						'id'=>'documentno',
						'class'=>'form-control input-sm',
						'value'=>$creditnote_info->documentno)
						);
				}
				?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_person_name'), 'person', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'person_id',
						'id'=>'person_id',
						'type'=>'hidden',
						'value'=>$creditnote_info->person_id)
						);?>
				<?php echo form_input(array(
						'name'=>'cash_book_id',
						'id'=>'cash_book_id',
						'type'=>'hidden',
						'value'=>$cashups->cash_book_id)
						);?>
				<?php echo form_input(array(
						'name'=>'cashup_id',
						'id'=>'cashup_id',
						'type'=>'hidden',
						'value'=>$cashups->cashup_id)
						);?>
				<?php echo form_input(array(
						'name'=>'person',
						'id'=>'person',
						'class'=>'form-control input-sm',
						'value'=>$creditnote_info->name,
						'placeholder' => $this->lang->line('common_search_dni'))
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('creditnotes_description'), 'description', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'description',
						'id'=>'description',
						'class'=>'form-control input-sm',
						'value'=>$creditnote_info->description)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('creditnotes_amount'), 'amount', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'amount',
						'id'=>'amount',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => 0.01,
						'max' => $cashups->closed_amount_total,
						'value'=>$creditnote_info->amount)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('creditnotes_paymentterm'), 'movementtype', !empty($basic_version) ? array('class'=>'required control-label col-xs-3') : array('class'=>'control-label col-xs-3')); ?>
			<div class="col-xs-8">
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'movementtype',
							'type'=>'radio',
							'id'=>'cash_typeC',
							'value'=>'C',
							'checked'=>(!empty($creditnote_info->movementtype) ? ($creditnote_info->movementtype === 'C') : TRUE ))
							); ?> <?php echo $this->lang->line('common_cash'); ?>
				</label>
				<label class="radio-inline">
					<?php echo form_radio(array(
							'name'=>'movementtype',
							'type'=>'radio',
							'id'=>'cash_typeB',
							'value'=>'B',
							'checked'=>$creditnote_info->movementtype === 'B')
							); ?> <?php echo $this->lang->line('common_bank'); ?>
				</label>

			</div>
		</div>

		<div class="form-group form-group-sm" id="movementtype_bank_trx" style="display:none">
			<?php echo form_label($this->lang->line('creditnotes_trx_number'), 'trx_number', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'trx_number',
						'id'=>'trx_number',
						'class'=>'form-control input-sm',
						'value'=>$creditnote_info->trx_number)
						);?>
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
    var calculate = parseInt(wide, 10)*1.2;

    $('.modal-dialog').css('width', calculate);

	var fill_value = function(event, ui) {
		event.preventDefault();
		$('#person_id').val(ui.item.value);
		$('#person').val(ui.item.label);
	};

	$("#person").autocomplete({
		source: "<?php echo site_url('creditnotes/suggest_partner');?>",
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

	$('#creditnote_edit_form').validate($.extend({
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
			<?php if($this->config->item('creditnote_number_automatic') == '0'):?>
				documentno: 'required',
			<?php endif;?>
			documentdate: 'required',
			person: 'required',
			amount: {
				required: true,
				max: $('#amount').attr('max')
			},
			trx_number: {
				required: "#cash_typeB:checked"
			}
		},

		messages:
		{
			<?php if($this->config->item('adjustnote_number_automatic') == '0'):?>
				documentno: "<?php echo $this->lang->line('creditnote_documentno_required'); ?>",
			<?php endif;?>
			documentdate: "<?php echo $this->lang->line('creditnote_documentdate_required'); ?>",
			person: "<?php echo $this->lang->line('creditnote_name_required'); ?>",
			amount: {
				required: "<?php echo $this->lang->line('creditnote_amount_required'); ?>",
				max: "Monto no puede ser mayor a "+$('#amount').attr('max')
			},
			trx_number: "<?php echo $this->lang->line('creditnote_trx_number_required'); ?>"
		}
	}, form_support.error));
});
</script>
