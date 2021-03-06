<div id="required_fields_message"><?php echo $this->lang->line('loans_credits_confirm_delete'); ?></div>

<ul id="error_message_box" class="error_message_box">
	<?php echo $this->lang->line('loans_credits_dni').": ".$loan_info->dni."<br>".
			$this->lang->line('loans_credits_name').": ".$loan_info->name."<br>".
			$this->lang->line('loans_credits_loandate').": ".to_date(strtotime($loan_info->loandate))."<br>".
			$this->lang->line('loans_credits_amount').": ".$loan_info->amount;?></ul>

<?php echo form_open('loans_credits/delete/', array('id'=>'loan_delete_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="loans">
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
                    <?php echo form_hidden(array('ids'=>$loan_info->loan_id)); ?>
                    <?php echo form_hidden(array('table'=>'loans')); ?>
                </div>
            </div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	$('#loan_delete_form').validate($.extend({
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