<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('growing_areas/save/'.$growing_area_info->growing_area_id, array('id'=>'growing_area_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="growing_areas">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('growing_areas_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$growing_area_info->name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('growing_areas_district'), 'district', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-tag"></span></span>
					<?php echo form_input(array(
							'name'=>'district',
							'id'=>'district',
							'class'=>'form-control input-sm',
							'value'=>$growing_areas_info->district)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('growing_areas_state'), 'state', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-globe"></span></span>
					<?php echo form_input(array(
							'name'=>'state',
							'id'=>'state',
							'class'=>'form-control input-sm',
							'value'=>$growing_area_info->state)
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('growing_areas_country'), 'country', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-globe"></span></span>
					<?php echo form_input(array(
							'name'=>'country',
							'id'=>'country',
							'class'=>'form-control input-sm',
							'value'=>$growing_area_info->country)
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

	$("#district").autocomplete({
		source: "<?php echo site_url('growing_areas/suggest_district');?>",
		delay: 10,
		appendTo: '.modal-content'
	});

	$("#state").autocomplete({
		source: "<?php echo site_url('growing_areas/suggest_state');?>",
		delay: 10,
		appendTo: '.modal-content'
	});

	$("#country").autocomplete({
		source: "<?php echo site_url('growing_areas/suggest_country');?>",
		delay: 10,
		appendTo: '.modal-content'
	});

	$('#growing_area_edit_form').validate($.extend({
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
			growing_area_name: 'required'
		},

		messages:
		{
			growing_area_name: "<?php echo $this->lang->line('growing_areas_name_required'); ?>"
		}
	}, form_support.error));
});
</script>
