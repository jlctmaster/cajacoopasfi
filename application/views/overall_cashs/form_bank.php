<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('overall_cashs/save_bank/'.$bank_info->bank_id, array('id'=>'bank_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="banks">
		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('banks_ruc'), 'ruc', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'ruc',
						'id'=>'ruc',
						'class'=>'form-control input-sm',
						'value'=>$bank_info->ruc)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('banks_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$bank_info->name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('banks_add_accounts'), 'accounts', array('class'=>'required control-label col-xs-8')); ?>
			<div class='col-xs-16'>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<td><?php echo $this->lang->line('banks_currency')?></td>
							<td><?php echo $this->lang->line('banks_account_number')?></td>
							<td><button type="button" onclick="agrega_campos()" class='btn btn-info btn-sm pull-right'><span class="glyphicon glyphicon-plus"></span></button></td>
						</tr>
					</thead>
					<tbody id="bank_accounts">
						<?php if(!empty($bankaccount_info[0]->bankaccount_id)):
							$contador = 0; ?>
						<?php 	foreach ($bankaccount_info as $account_info):?>
							<tr id='<?php echo $contador;?>' >
								<td><select id="currency_<?php echo $contador?>" name="currencys[]" class='form-control input-sm' ><option value=''>Seleccione</option><option value='PEN' <?php echo ($account_info->currency =="PEN" ? "selected" : "");?>>Soles</option><option value='USD' <?php echo ($account_info->currency =="USD" ? "selected" : "");?>>Dólar</option></select></td>
								<td><input type='text' name='accounts[]' id='account_<?php echo $contador;?>' title='<?php echo $this->lang->line('banks_selected_account')?>' placeholder='<?php echo $this->lang->line('banks_selected_accounts')?>' class='form-control input-sm' value="<?php echo $account_info->account_number; ?>"></td>
								<td><button type='button' class='btn btn-info btn-sm pull-right' onclick='elimina_me(<?php echo $contador;?>)'><span class='glyphicon glyphicon-minus'></span></button></td>
							</tr>
							<?php $contador++; ?>
						<?php 	endforeach;?>
						<?php 	endif;?>
					</tbody>
				</table>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript">
	var currencys = document.getElementsByName('currencys[]');
	var accounts = document.getElementsByName('accounts[]');
	var contador=accounts.length;

	var lbAccount = "<?php echo $this->lang->line('banks_selected_account')?>";
	var options = "<option value=''>Seleccione</option><option value='PEN'>Soles</option><option value='USD'>Dólar</option>";

	function agrega_campos(){
		$("#bank_accounts").append("<tr id='"+contador+"' >"+
		"<td><select id='currency_"+contador+"' name='currencys[]' class='form-control input-sm'>"+options+"</select></td>"+
		"<td><input type='text' name='accounts[]' id='account_"+contador+"' title='"+lbAccount+"' placeholder='"+lbAccount+"' class='form-control input-sm'/></td>"+
		"<td><button type='button' class='btn btn-info btn-sm pull-right' onclick='elimina_me("+contador+")'><span class='glyphicon glyphicon-minus'></span></button></td>"+
		"</tr>");
		contador++;
	}
	function elimina_me(elemento){
		$("#"+elemento).remove();
		for(var i=0;i<accounts.length;i++){
			currencys[i].removeAttribute('id');
			accounts[i].removeAttribute('id');
		}
		for(var i=0;i<accounts.length;i++){
			currencys[i].setAttribute('id','currency_'+i);
			accounts[i].setAttribute('id','account_'+i);
		}
		contador--;
	}

</script>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	//	Change with of modal form
	var wide = $('.modal-dialog').css('width');
    var calculate = parseInt(wide, 10)*1.2;

    $('.modal-dialog').css('width', calculate);

	$('#bank_edit_form').validate($.extend({
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
			ruc: 'required',
			name: 'required'
		},

		messages:
		{
			ruc: "<?php echo $this->lang->line('bank_ruc_required'); ?>",
			name: "<?php echo $this->lang->line('bank_name_required'); ?>"
		}
	}, form_support.error));

});
</script>
