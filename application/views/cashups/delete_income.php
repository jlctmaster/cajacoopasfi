<div id="required_fields_message"><?php echo $this->lang->line('incomes_confirm_delete'); ?></div>

<ul id="error_message_box" class="error_message_box">
	<?php echo $this->lang->line('incomes_person').": ".$income_info->person."<br>".
			$this->lang->line('incomes_documentno').": ".$income_info->documentno."<br>".
			$this->lang->line('incomes_documentdate').": ".to_datetime(strtotime($income_info->documentdate))."<br>".
			$this->lang->line('incomes_amount').": ".($income_info->currency == CURRENCY ? to_currency($income_info->amount) : to_usd($income_info->amount));?></ul>

<?php echo form_open('cashups/deleted_income/', array('id'=>'income_delete_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="incomes">
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
                    <?php echo form_hidden(array('ids'=>$income_info->income_id)); ?>
                    <?php echo form_hidden(array('currency'=>$income_info->currency)); ?>
                </div>
            </div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	$('#income_delete_form').validate($.extend({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)
				{
					dialog_support.hide();
					table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
					self.parent.location.reload();
				},
				dataType: 'json'
			});
		}
	}, form_support.error));
});
</script>