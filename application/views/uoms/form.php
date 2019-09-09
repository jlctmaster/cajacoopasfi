<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('uoms/save/'.$uom_info->uom_id, array('id'=>'uom_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="uoms">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('uoms_symbol'), 'symbol', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'symbol',
						'id'=>'symbol',
						'class'=>'form-control input-sm',
						'value'=>$uom_info->symbol)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('uoms_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$uom_info->name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('uoms_magnitude'), 'magnitude', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-tag"></span></span>
					<?php echo form_input(array(
							'name'=>'magnitude',
							'id'=>'magnitude',
							'class'=>'form-control input-sm',
							'value'=>$uom_info->magnitude)
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
