<div id="required_fields_message"><?php echo $this->lang->line('invoices_confirm_delete'); ?></div>

<ul id="error_message_box" class="error_message_box">
	<?php echo $this->lang->line('common_person_name').": ".$invoice_info->name."<br>".
			$this->lang->line('invoices_serieno').": ".$invoice_info->serieno."<br>".
			$this->lang->line('invoices_documentdate').": ".to_datetime(strtotime($invoice_info->documentdate))."<br>".
			$this->lang->line('invoices_totalamt').": ".to_currency($invoice_info->totalamt);?></ul>

<?php echo form_open('invoices/delete/', array('id'=>'invoice_delete_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="invoices">
		<div class="form-group form-group-sm">	
			<div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <label class="radio-inline">
                        <input type="radio" name="confirm" id="confirm1" value="Y" checked="checked"> <?php echo $this->lang->line('common_yes'); ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="confirm" id="confirm0" value="N"> <?php echo $this->lang->line('common_no'); ?>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <?php echo form_hidden(array('ids'=>$invoice_info->invoice_id)); ?>
                    <?php echo form_hidden(array('currency'=> CURRENCY)); ?>
                </div>
            </div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	$('#invoice_delete_form').validate($.extend({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)
				{
					dialog_support.hide();
					table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
				},
				dataType: 'json'
			});
		}
	}, form_support.error));
});
</script>