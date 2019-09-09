<div id="required_fields_message"><?php echo $this->lang->line('expenses_confirm_delete'); ?></div>

<ul id="error_message_box" class="error_message_box">
	<?php echo $this->lang->line('expenses_person').": ".$expense_info->person."<br>".
			$this->lang->line('expenses_documentno').": ".$expense_info->documentno."<br>".
			$this->lang->line('expenses_documentdate').": ".to_datetime(strtotime($expense_info->documentdate))."<br>".
			$this->lang->line('expenses_amount').": ".($expense_info->currency == CURRENCY ? to_currency($expense_info->amount) : to_usd($expense_info->amount));?></ul>

<?php echo form_open('cashups/deleted_expense/', array('id'=>'expense_delete_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="expenses">
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
                    <?php echo form_hidden(array('ids'=>$expense_info->expense_id)); ?>
                    <?php echo form_hidden(array('currency'=>$expense_info->currency)); ?>
                </div>
            </div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	$('#expense_delete_form').validate($.extend({
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