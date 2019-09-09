<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('voucher_operations/save_certificate/'.$quality_certificate_info->quality_certificate_id, array('id'=>'quality_certificate_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="quality_certificates">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('quality_certificates_depositdate'), 'depositdate', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'depositdate',
							'id'=>'depositdate',
							'class'=>'form-control input-sm',
							'value'=>to_date(strtotime($quality_certificate_info->depositdate)))
							); ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('quality_certificates_number'), 'certificate_number', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'serieno',
						'id'=>'serieno',
						'class'=>'form-control input-sm',
						'type' => 'hidden',
						'value'=>$quality_certificate_info->serieno)
						);?>
				<?php echo form_input(array(
						'name'=>'certificate_number',
						'id'=>'certificate_number',
						'class'=>'form-control input-sm',
						'value'=>$quality_certificate_info->certificate_number)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('common_dni'), 'dni', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'person_id',
						'id'=>'person_id',
						'type'=>'hidden',
						'value'=>$quality_certificate_info->person_id)
						);?>
				<?php echo form_input(array(
						'name'=>'dni',
						'id'=>'dni',
						'class'=>'form-control input-sm',
						'value'=>$quality_certificate_info->dni,
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
						'value'=>$quality_certificate_info->name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('quality_certificates_kg_dry'), 'kg_dry', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'kg_dry',
						'id'=>'kg_dry',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => '0.01',
						'value'=>$quality_certificate_info->kg_dry)
						);?>
			</div>
			<?php echo form_label($this->lang->line('quality_certificates_qq_dry'), 'qq_dry', array('class'=>'control-label col-xs-2')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'qq_dry',
						'id'=>'qq_dry',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => '0.01',
						'value'=>$quality_certificate_info->qq_dry)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('quality_certificates_rate_profile'), 'rate_profile', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'rate_profile',
						'id'=>'rate_profile',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => '0.01',
						'value'=>$quality_certificate_info->rate_profile)
						);?>
			</div>
			<?php echo form_label($this->lang->line('quality_certificates_physical_performance'), 'physical_performance', array('class'=>'control-label col-xs-2')); ?>
			<div class='col-xs-3'>
				<?php echo form_input(array(
						'name'=>'physical_performance',
						'id'=>'physical_performance',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => '0.01',
						'value'=>$quality_certificate_info->physical_performance)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('quality_certificates_quality'), 'quality', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'quality',
						'id'=>'quality',
						'class'=>'form-control input-sm',
						'value'=>$quality_certificate_info->quality)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('quality_certificates_location_id'), 'location_id', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('location_id',$locations,$selected_location_id,array('id' => 'location_id', 'class' => 'form-control'));?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('quality_certificates_price'), 'price', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'price',
						'id'=>'price',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => 0.01,
						'value'=>$quality_certificate_info->price)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('quality_certificates_amount'), 'amount', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'amount',
						'id'=>'amount',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'readonly' => TRUE,
						'value'=>$quality_certificate_info->amount)
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
    $('#depositdate').daterangepicker({
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
		source: "<?php echo site_url($controller_name.'/suggest_partner');?>",
		delay: 10,
		appendTo: '.modal-content',
		cacheLength: 1,
		select: fill_value
	});

	$('#kg_dry,#price').change(function(){
		let kg = $('#kg_dry').val();
		let price = $('#price').val();
		let amount = kg * price;
		$('#amount').val(amount.toFixed(2));
	});

	$("#quality").autocomplete({
		source: "<?php echo site_url($controller_name.'/suggest_quality');?>",
		delay: 10,
		appendTo: '.modal-content'
	});

	$('#quality_certificate_edit_form').validate($.extend({
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
			depositdate: 'required',
			certificate_number: 'required',
			name: 'required',
			kg_dry: 'required',
			quality: 'required',
			location_id: 'required',
			price: 'required',
			amount: 'required'
		},

		messages:
		{
			depositdate: "<?php echo $this->lang->line('quality_certificate_depositdate_required'); ?>",
			certificate_number: "<?php echo $this->lang->line('quality_certificate_number_required'); ?>",
			name: "<?php echo $this->lang->line('quality_certificate_name_required'); ?>",
			kg_dry: "<?php echo $this->lang->line('quality_certificate_kg_dry_required'); ?>",
			quality: "<?php echo $this->lang->line('quality_certificate_quality_required'); ?>",
			location_id: "<?php echo $this->lang->line('quality_certificate_location_required'); ?>",
			price: "<?php echo $this->lang->line('quality_certificate_price_required'); ?>",
			amount: "<?php echo $this->lang->line('quality_certificate_amount_required'); ?>"
		}
	}, form_support.error));
});
</script>
