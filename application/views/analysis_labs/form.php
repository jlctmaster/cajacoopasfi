<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name.'/save/'.$fees_deposit_info->id_fee_deposit, array('id'=>'fees_deposit_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="fees_deposit">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('fees_deposit_supplier'), 'supplier', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'supplier',
						'id'=>'supplier',
						'class'=>'form-control input-sm',
						'value'=>$fee_deposit_info->supplier_id)
						);?>
                <?php echo form_input(array(
						'name'=>'supplier_id',
						'id'=>'supplier_id',
						'type'=>'hidden',
						'value'=>$fee_deposit_info->supplier_id)
						);?>                               
			</div>
		</div>
                
        <div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('fees_deposit_location'), 'location', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('location', $locations, $selected_location, array('class'=>'form-control', 'id' => 'location_id')); ?>
			</div>
		</div>
		
        <div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('fees_deposit_period'), 'period', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('period', $period, $selected_period, array('class'=>'form-control', 'id' => 'period_id')); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('fees_deposit_type_item'), 'type_item', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('type_item_id', $item_type, $selected_type, array('class'=>'form-control', 'id' => 'type_item_id')); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('fees_deposit_item'), 'item', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('item_id', $item, $selected_item, array('class'=>'form-control', 'id' => 'item_id')); ?>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('fees_deposit_fee_deposit'), 'value', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'fee_deposit',
						'id'=>'fee_deposit',
						'class'=>'form-control input-sm',
						'type' => 'number',
						'step' => 1,
						'value'=>$fee_deposit_info->fee_deposit)
						);?>
			</div>
		</div>
                
            
                
            
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	$("#supplier").autocomplete({
		source: "<?php echo site_url('fees_deposit/suggest_supplier');?>",
		delay: 10,
                 minLength: 3,
		appendTo: '.modal-content',
                select: function(event, ui){
                    var value = ui.item.value
                    $("#supplier").val(value.nombre);
                    $("#supplier_id").val(value.id);
                    return false;
                },
                focus:function(event, ui){
                    var value = ui.item.value
                    $("#supplier").val(value.nombre);
                    return false;
                }
		
	});
        
        
        $(document).on('change','#type_item',function(e){
	var  valor = this.value
	  // alert(id);
        $.ajax( {  
                url: "<?php echo site_url($controller_name.''); ?>",
                type: 'POST',
                dataType : 'json',
                async: true,
                data: 'codigo='+valor,
                success:function(datos){
                    if(datos)
                    {
                           

                    }

                                },
                error: function(xhr, status) {
                                alert('Disculpe, existiÃ³ un problema');
                                }
                });

    });

	$('#fees_deposit_edit_form').validate($.extend({
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
			supplier: 'required',
			location: 'required',
			period: 'required'
		},

		messages:
		{
			supplier: "<?php echo $this->lang->line($controller_name.'_supplier_required'); ?>",
			location: "<?php echo $this->lang->line($controller_name.'_location_required'); ?>",
			period: "<?php echo $this->lang->line($controller_name.'_pariod_required'); ?>"
		}
	}, form_support.error));
});
</script>
