<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name.'/save/'.$delivery_document_info->id_delivery_document, array('id'=>'delivary_document_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="delivery_document">
                <div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('delivery_document_code'), 'code', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'code',
						'id'=>'code',
						'class'=>'form-control input-sm',
						'value'=>$delivery_document_info->code)
						);?>
			</div>
		</div>
		
                <div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('delivery_document_supplier'), 'supplier', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'supplier',
						'id'=>'supplier',
						'class'=>'form-control input-sm',
						'value'=>$delivery_document_info->id_supplier)
						);?>
			</div>
		</div>
                
                <div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('delivery_document_fee_deposit'), 'fee_deposikt', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('id_fee_deposit',$fee_deposit,$selected_fee, array('class'=>'form-control', 'id' => 'fee_deposit')); ?>
			</div>
		</div>
            
                <div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('delivery_document_period'), 'id', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('id',$period,$selected_period, array('class'=>'form-control', 'id' => 'period_id')); ?>
			</div>
		</div>
		
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('delivery_document_tasting_profile'), 'tasting_profile', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'tasting',
						'id'=>'tasting',
						'class'=>'form-control input-sm',
						'value'=>$delivery_document_info->tasting_profile_rate)
						);?>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

//	$("#period").autocomplete({
//		source: "<?php echo site_url('Periods/suggest_period/');?>",
//		delay: 10,
//		appendTo: '.modal-content'
//	});
        
        
//        $('#user_id').change(function(){
//		var text= $(this).find('option:selected').text().split('(');
//		var str = text[0];
//		var matches = str.match(/\b(\w)/g); // ['J','S','O','N']
//		var acronym = matches.join(''); // JSON
//		var actualcode = $('#code').val();
//		if(actualcode.split('-').length > 2)
//		{
//			actualcode = actualcode.slice(0,-3);
//		}
//		$('#code').val(actualcode+"-"+acronym.toUpperCase());
//	});        
        
        
	$('#delivary_document_edit_form').validate($.extend({
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
			type: 'required',
			value: 'required'
		},

		messages:
		{
			name: "<?php echo $this->lang->line($controller_name.'_name_required'); ?>",
			type: "<?php echo $this->lang->line($controller_name.'_type_required'); ?>",
			value: "<?php echo $this->lang->line($controller_name.'_value_required'); ?>"
		}
	}, form_support.error));
});
</script>
