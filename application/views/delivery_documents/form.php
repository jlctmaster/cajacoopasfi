<div class="col-lg-12 col-md-6">
<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name.'/save/'.$delivery_document_info->id_delivery_document, array('id'=>'delivary_document_edit_form', 'class'=>'form-horizontal')); ?>
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
<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Acopio</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Certificado</a>
	</li>
	
</ul>


<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
	<fieldset id="delivery_document">
		<br>
        <div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('delivery_document_item'), 'item', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'item',
						'id'=>'item',
						'class'=>'form-control input-sm',
						'value'=>$delivery_document_info->item)
						);?>
			</div>
		</div>
		
                           
        <div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('delivery_document_fee_deposit'), 'fee_deposit', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('id_fee_deposit',$fee_deposit,$selected_fee, array('class'=>'form-control input-sm', 'id' => 'fee_deposit')); ?>
			</div>
		</div>
            
       
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('delivery_document_uom_item'), 'certifier', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('uom_input',$uom_item,$selected, array('class'=>'form-control input-sm', 'id' => 'uom_item')); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('delivery_document_amount'), 'amount', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'amount',
						'id'=>'amount',
						'class'=>'form-control input-sm',
						'value'=>$delivery_document_info->tasting_profile_rate)
						);?>
			</div>
		</div>
		
	</fieldset>
 
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
		<fieldset id="certificadora">
		 
			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('delivery_document_certifier'), 'certifier', array('class'=>'required control-label col-xs-3')); ?>
				<div class='col-xs-8'>
					<?php echo form_dropdown('certifier_id',$certifier,$selected_certifier, array('class'=>'form-control input-sm', 'id' => 'period_id')); ?>
				</div>
			</div>
		 
			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('delivery_document_certifier'), 'certifier', array('class'=>'required control-label col-xs-3')); ?>
				<div class='col-xs-8'>
					<?php echo form_dropdown('certifier_id',$certifier,$selected_certifier, array('class'=>'form-control input-sm', 'id' => 'period_id')); ?>
				</div>
			</div>
  
		
		</fieldset>			
  </div>
  
</div>	
	
</div>	
	
<?php echo form_close(); ?>
</div>
<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	$('#myTab a').on('click', function (e) {
	e.preventDefault()
	$(this).tab('show')});

}



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
        
        
	$('#delivery_document_edit_form').validate($.extend({
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
