<div class="col-lg-12 col-md-6">
<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name.'/save/'.$delivery_document_info->id_delivery_document, array('id'=>'delivery_document_edit_form', 'class'=>'form-horizontal')); ?>
<div class="form-group form-group-sm">
        <?php echo form_label($this->lang->line('delivery_document_supplier'), 'supplier', array('class'=>'required control-label col-xs-3')); ?>
        <div class='col-xs-8'>
                <?php echo form_input(array(
                    'name'=>'supplier',
                    'id'=>'supplier',
                    'class'=>'form-control input-sm',
                    'value'=>$supplier_selected->first_name.' '.$supplier_selected->last_name)
                    );?>
                <?php echo form_input(array(
                    'name'=>'supplier_id',
                    'id'=>'supplier_id',
                    'type'=>'hidden',
                    'value'=>$supplier_selected->person_id)
                    );?>       
        </div>

</div>
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Acopio</a></li>
    <li><a data-toggle="tab" href="#menu1">Certificadora</a></li>
    <li><a data-toggle="tab" href="#menu2">Observaciones</a></li>
  </ul>


<div class="tab-content" id="myTabContent">
  <div id="home" class="tab-pane fade in active">
	<br>
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
                <?php echo form_label($this->lang->line('delivery_document_period'), 'fee_period', array('class'=>'required control-label col-xs-3')); ?>
                <div class='col-xs-8'>
                        <?php echo form_dropdown('period_id',$period,$selected_period, array('class'=>'form-control input-sm', 'id' => 'period_id')); ?>
                </div>
        </div>
        
        
        <div class="form-group form-group-sm">
                <?php echo form_label($this->lang->line('delivery_document_type_item'), 'item', array('class'=>'required control-label col-xs-3')); ?>
                <div class='col-xs-8'>
                        <?php echo form_dropdown('type_item_id',$item_type,$selected_type_item, array('class'=>'form-control input-sm', 'id' => 'type_item_id')); ?>
                </div>
        </div>
        
        <div class="form-group form-group-sm">
                <?php echo form_label($this->lang->line('delivery_document_item'), 'item', array('class'=>'required control-label col-xs-3')); ?>
                <div class='col-xs-8'>
                        <?php echo form_dropdown('item_id',$item,$selected_item, array('class'=>'form-control input-sm', 'id' => 'item_id')); ?>
                </div>
        </div>
        
        <div class="form-group form-group-sm">
                <?php echo form_label($this->lang->line('delivery_document_fee_deposit'), 'fee_deposit', array('class'=>'required control-label col-xs-3')); ?>
                <div class='col-xs-8'>
                        <?php echo form_dropdown('id_fee_deposit',$fee,$fee_selected, array('class'=>'form-control input-sm', 'id' => 'id_fee_deposit')); ?>
                </div>
        </div>
        <div class="form-group form-group-sm">
                <?php echo form_label($this->lang->line('delivery_document_uom_item'), 'uom', array('class'=>'required control-label col-xs-3')); ?>
                <div class='col-xs-8'>
                    <?php echo form_input(array(
                        'name'=>'uom',
                        'id'=>'uom',
                        'class'=>'form-control input-sm',
                        'value'=>$uom_selected)
                        );?>
                </div>
        </div>
        <div class="form-group form-group-sm">
                <?php echo form_label($this->lang->line('delivery_document_amount'), 'amount', array('class'=>'required control-label col-xs-3')); ?>
                <div class='col-xs-8'>
                    <?php echo form_input(array(
                        'name'=>'amount',
                        'id'=>'amount',
                        'class'=>'form-control input-sm',
                        'value'=>$delivery_document_info->amount_entered)
                        );?>
                </div>
        </div>
		
	
  </div>
  <div id="menu1" class="tab-pane fade">
	<br>			 
        <div class="form-group form-group-sm">
                <?php echo form_label($this->lang->line('delivery_document_certifier'), 'certifier', array('class'=>'required control-label col-xs-3')); ?>
                <div class='col-xs-8'>
                        <?php echo form_dropdown('certifier_id',$certifier,$certifier_selected, array('class'=>'form-control input-sm', 'id' => 'certifier_id')); ?>
                </div>
        </div>
					
  </div>
   <div id="menu2" class="tab-pane fade">
       <br>			 
        <div class="form-group form-group-sm">
                <?php echo form_label($this->lang->line('delivery_document_observations'), 'observations', array('class'=>'required control-label col-xs-3')); ?>
                <div class='col-xs-8'>
                    <?php echo form_input(array(
                        'name'=>'observation',
                        'id'=>'observation',
                        'type'=>'text-area',
                        'class'=>'form-control input-sm',
                        'value'=>$delivery_document_info->observation)
                        );?>
                </div>
        </div>
					
  </div> 
    
</div>	
	
	
	
<?php echo form_close(); ?>
</div>
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
       
       $(document).on('change','#item_id',function(){
	var item = this.value;
        var supplier = $('#supplier_id').val();
        var type = $('#type_item_id').val();
        var period = $('#period_id').val();
        
	  // alert(id);
        $.ajax( {  
                url: "<?php echo site_url($controller_name.'/suggest_supplier'); ?>",
                type: 'GET',
                dataType : 'json',
                async: true,
                data: 'period='+period+'&supplier='+supplier+'&type='+type+'&item='+item,
                success:function(datos){
                    if(datos.length > 0)
                    {
                        $("#id_fee_deposit").html('');
	        	$('#id_fee_deposit').append('<option value="" >-Seleccione-</option>');
	        	var cadena="";   
	        	for(i=0;i < datos.length;i++)
	        	{
                            cadena = datos[i].deposito.toUpperCase()+' ( CUOTA: '+datos[i].cuota+' SALDO: '+datos[i].saldo+')';
                            $("#id_fee_deposit").append("<option value='"+datos[i].id+"'>"+cadena+"</option>");	
	        	}
             
             
                    }
                          },
                error: function(xhr, status) {
                                alert('Disculpe, existiÃ³ un problema');
                                }
                });

    });
        
       $(document).on('change','#id_fee_deposit',function(){
	var  valor = $('#item_id').val();
	  // alert(id);
            $.ajax( {  
                    url: "<?php echo site_url($controller_name.'/suggest_uom'); ?>",
                    type: 'GET',
                    dataType : 'json',
                    async: true,
                    data: 'item='+valor,
                    success:function(datos){
                        if(datos)
                        {
                            $('#uom').val(datos[0].name);
                            $('#uom').attr('disabled',true);
                        }

                                    },
                    error: function(xhr, status) {
                                    alert('Disculpe, existiÃ³ un problema');
                                    }
                    });

    });   
    
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
			supplier_id: 'required',
			type_item_id: 'required',
			period_id: 'required',
                        item_id: 'required',
                        id_fee_deposit:'required',
                        amount:'required',
                        certifier_id:'required',
                        code: 'required'
                        
		},

		messages:
		{
			supplier_id: "<?php echo $this->lang->line($controller_name.'_supplier_required'); ?>",
			type_item_id: "<?php echo $this->lang->line($controller_name.'_type_item_required'); ?>",
			period_id: "<?php echo $this->lang->line($controller_name.'_period_id_required'); ?>",
                        item_id: "<?php echo $this->lang->line($controller_name.'_item_id_required'); ?>",
                        id_fee_deposit: "<?php echo $this->lang->line($controller_name.'_id_fee_deposit_required'); ?>",
                        amount: "<?php echo $this->lang->line($controller_name.'_amount_required'); ?>",
                        certifier: "<?php echo $this->lang->line($controller_name.'_certifier_required'); ?>",
                        code: "<?php echo $this->lang->line($controller_name.'_code_required'); ?>"
		}
	}, form_support.error));
});
    
    

	
</script>
