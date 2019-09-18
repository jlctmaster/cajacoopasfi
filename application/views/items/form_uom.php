<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name.'/save_convert/', array('id'=>'uom_convert_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="convert_uom">
                 <?php echo form_input(array('name'=>'item_id','id'=>'item_id','type'=>'hidden','value'=>$item_id));?>   
                <div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('convert_uom_uom'), 'uom', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('uom', $uom, $selected_uom, array('class'=>'form-control', 'id' => 'uom_id')); ?>
			</div>
		</div>
                <div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('convert_uom_uomto'), 'uomto', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('uomto', $uomto, $selected_uomto, array('class'=>'form-control', 'id' => 'uomto')); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('convert_uom_factor_mult'), 'fact_mult', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'multiplierfactor',
						'id'=>'multiplierfactor',
						'class'=>'form-control input-sm',
						'value'=>$uom_conversion_info->multiplierfactor)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('convert_uom_factor_div'), 'fact_div', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'dividingfactor',
						'id'=>'dividingfactor',
						'class'=>'form-control input-sm',
						'value'=>$uom_conversion_info->dividingfactor)
						);?>
			</div>
		</div>

		
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	$("#magnitude").autocomplete({
		source: "<?php echo site_url('uoms/suggest_magnitude');?>",
		delay: 10,
		appendTo: '.modal-content'
	});

	$('#uom_edit_form').validate($.extend({
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
			name: 'required',
			symbol: 'required'
		},

		messages:
		{
			name: "<?php echo $this->lang->line('uom_name_required'); ?>",
			symbol: "<?php echo $this->lang->line('uom_symbol_required'); ?>"
		}
	}, form_support.error));
});
</script>
