<div id="required_fields_message"><?php echo $this->lang->line('costs_confirm_delete'); ?></div>

<ul id="error_message_box" class="error_message_box">
	<?php echo $this->lang->line('costs_person').": ".$cost_info->person."<br>".
			$this->lang->line('costs_documentno').": ".$cost_info->documentno."<br>".
			$this->lang->line('costs_documentdate').": ".to_datetime(strtotime($cost_info->documentdate))."<br>".
			$this->lang->line('costs_amount').": ".($cost_info->currency == CURRENCY ? to_currency($cost_info->amount) : to_usd($cost_info->amount));?></ul>

<?php echo form_open('cashups/deleted_cost/', array('id'=>'cost_delete_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="costs">
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
                    <?php echo form_hidden(array('ids'=>$cost_info->cost_id)); ?>
                    <?php echo form_hidden(array('currency'=>$cost_info->currency)); ?>
                </div>
            </div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	$('#cost_delete_form').validate($.extend({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)
				{
					dialog_support.hide();
					table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
					setTimeout(function(){
						self.parent.location.reload();
					},2000)
				},
				dataType: 'json'
			});
		}
	}, form_support.error));
});
</script>