<div id="required_fields_message"><?php echo $this->lang->line('loans_credits_confirm_delete'); ?></div>

<ul id="error_message_box" class="error_message_box">
	<?php echo $this->lang->line('loans_credits_dni').": ".$credit_info->dni."<br>".
			$this->lang->line('loans_credits_name').": ".$credit_info->name."<br>".
			$this->lang->line('loans_credits_loandate').": ".to_date(strtotime($credit_info->loandate))."<br>".
			$this->lang->line('loans_credits_amount').": ".$credit_info->amount;?></ul>

<?php echo form_open('loans_credits/delete/', array('id'=>'credit_delete_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="credits">
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
                    <?php echo form_hidden(array('ids'=>$credit_info->credit_id)); ?>
                    <?php echo form_hidden(array('table'=>'credits')); ?>
                </div>
            </div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	$('#credit_delete_form').validate($.extend({
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