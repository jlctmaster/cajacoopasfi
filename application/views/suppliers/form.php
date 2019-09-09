<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name . '/save/' . $person_info->person_id, array('id'=>'supplier_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="supplier_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('suppliers_ispartner'), 'ispartner', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'ispartner',
						'id'=>'ispartner',
						'value'=>1,
						'checked'=>($person_info->ispartner) ? 1 : 0)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm" id="growing_area_display" style="display:none;">
			<?php echo form_label($this->lang->line('suppliers_growing_area_id'), 'growing_area_id', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('growing_area_id', $growing_area, $selected_growing_area, array('class'=>'form-control', 'id' => 'growing_area_id')); ?>
			</div>
		</div>

		<div class="form-group form-group-sm" id="association_date_display" style="display:none;">
			<?php echo form_label($this->lang->line('suppliers_association_date'), 'association_date', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'association_date',
							'id'=>'association_date',
							'class'=>'form-control input-sm datepicker',
							'value'=>to_datetime(strtotime(($person_info->association_date == "0000-00-00 00:00:00" ? NULL : $person_info->association_date))))
							); ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('common_ruc'), 'ruc', array('class'=>'required control-label col-xs-3')); ?>
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
			<?php echo form_label($this->lang->line('suppliers_company_name'), 'company_name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
					'name'=>'company_name',
					'id'=>'company_name',
					'class'=>'form-control input-sm',
					'value'=>$person_info->company_name)
					);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('suppliers_agency_name'), 'agency_name', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
					'name'=>'agency_name',
					'id'=>'agency_name',
					'class'=>'form-control input-sm',
					'value'=>$person_info->agency_name)
					);?>
			</div>
		</div>

		<?php $this->load->view("people/form_basic_info"); ?>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('suppliers_account_number'), 'account_number', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
					'name'=>'account_number',
					'id'=>'account_number',
					'class'=>'form-control input-sm',
					'value'=>$person_info->account_number)
					);?>
			</div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
//validation and submit handling
$(document).ready(function()
{
	<?php $this->load->view('partial/datepicker_locale'); ?>

	$('#association_date').datetimepicker({
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

	// add the rule here
	jQuery.validator.addMethod(
		"notEqualTo",
		function(elementValue,element,param) {
			return elementValue != param;
		},
		"<?php echo $this->lang->line('suppliers_growing_area_none_selected'); ?>"
	);

	$('#supplier_form').validate($.extend({
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
			company_name: 'required',
			ruc: 'required',
			dni: 'required',
			first_name: 'required',
			last_name: 'required',
			email: 'email',
			growing_area_id: {
				required: true,
				notEqualTo: -1
			}
   		},

		messages: 
		{
			company_name: "<?php echo $this->lang->line('suppliers_company_name_required'); ?>",
			ruc: "<?php echo $this->lang->line('common_ruc_required'); ?>",
			dni: "<?php echo $this->lang->line('common_dni_required'); ?>",
			first_name: "<?php echo $this->lang->line('common_first_name_required'); ?>",
			last_name: "<?php echo $this->lang->line('common_last_name_required'); ?>",
			email: "<?php echo $this->lang->line('common_email_invalid_format'); ?>"
		}
	}, form_support.error));

	$("#ispartner").click( function(){
		if( $(this).is(':checked') ){
			$('#growing_area_display').show();
			$('#association_date_display').show();
			$('.form-group label[for=company_name]').removeClass("required");
			$('.form-group label[for=ruc]').removeClass("required");
			//	Change Rules 
			$('#company_name').rules('remove','required');
			$('#ruc').rules('remove','required');
	   	}
	   	else{
			$('#growing_area_display').hide();
			$('#association_date_display').hide();
			$('.form-group label[for=company_name]').addClass("required");
			$('.form-group label[for=ruc]').addClass("required");
			//	Change Rules 
			$('#company_name').rules('add',{
				required: true
			});
			$('#ruc').rules('add',{
				required: true
			});
	   	}
	});

	$('#growing_area_id').change(function(){
		if($(this).val()!='-1'){
			$.ajax({
				type: 'GET',
				url: "<?php echo site_url('suppliers/get_growing_area_locale/"+$(this).val()+"'); ?>",
				dataType: 'json',
				success: function(resp){
		            $('#state').val(resp.state);
		            $('#country').val(resp.country);
		        },
		        error: function(jqXHR, textStatus, errorThrown){
		            console.log(jqXHR);
		        }
			})
		}
	});

	if( $('#ispartner').is(':checked') ){
		$('#growing_area_display').show();
		$('#association_date_display').show();
		$('.form-group label[for=company_name]').removeClass("required");
		$('.form-group label[for=ruc]').removeClass("required");
		//	Change Rules 
		$('#company_name').rules('remove','required');
		$('#ruc').rules('remove','required');
   	}
   	else{
		$('#growing_area_display').hide();
		$('#association_date_display').hide();
		$('.form-group label[for=company_name]').addClass("required");
		$('.form-group label[for=ruc]').addClass("required");
		//	Change Rules 
		$('#company_name').rules('add',{
			required: true
		});
		$('#ruc').rules('add',{
			required: true
		});
   	}

});
</script>
