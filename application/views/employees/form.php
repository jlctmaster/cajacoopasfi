<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name . '/save/' . $person_info->person_id, array('id'=>'employee_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="employee_basic_info">

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('employees_ruc'), 'ruc', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
					'name'=>'ruc',
					'id'=>'ruc',
					'class'=>'form-control input-sm',
					'value'=>$person_info->ruc)
					);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('employees_admission_date'), 'admission_date', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'admission_date',
							'id'=>'admission_date',
							'class'=>'form-control input-sm datepicker',
							'value'=>to_datetime(strtotime($person_info->admission_date)))
							); ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('employees_job_title'), 'job_title', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
					'name'=>'job_title',
					'id'=>'job_title',
					'class'=>'form-control input-sm',
					'value'=>$person_info->job_title)
					);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('employees_contract_type'), 'contract_type', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
					'name'=>'contract_type',
					'id'=>'contract_type',
					'class'=>'form-control input-sm',
					'value'=>$person_info->contract_type)
					);?>
			</div>
		</div>

		<?php $this->load->view("people/form_basic_info"); ?>
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
	<?php $this->load->view('partial/datepicker_locale'); ?>

	$('#admission_date').datetimepicker({
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
		language: '<?php echo current_language_code(); ?>'
	});

	$('#employee_form').validate($.extend({
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
			admission_date: 'required',
			dni: 'required',
			first_name: 'required',
			last_name: 'required',
			email: 'email'
   		},

		messages: 
		{
			admission_date: "<?php echo $this->lang->line('employees_admission_date_required'); ?>",
			dni: "<?php echo $this->lang->line('common_dni_required'); ?>",
			first_name: "<?php echo $this->lang->line('common_first_name_required'); ?>",
			last_name: "<?php echo $this->lang->line('common_last_name_required'); ?>",
			email: "<?php echo $this->lang->line('common_email_invalid_format'); ?>"
		}
	}, form_support.error));

	$("#job_title").autocomplete({
		source: "<?php echo site_url('employees/suggest_job_title');?>",
		delay: 10,
		appendTo: '.modal-content'
	});

	$("#contract_type").autocomplete({
		source: "<?php echo site_url('employees/suggest_contract_type');?>",
		delay: 10,
		appendTo: '.modal-content'
	});

});
</script>
