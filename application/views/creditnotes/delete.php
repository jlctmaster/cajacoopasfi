<div id="required_fields_message"><?php echo $this->lang->line('creditnotes_confirm_delete'); ?></div>

<ul id="error_message_box" class="error_message_box">
	<?php echo $this->lang->line('creditnotes_documentno').": ".$creditnote_info->documentno."<br>".
			$this->lang->line('common_dni').": ".$creditnote_info->dni."<br>".
			$this->lang->line('common_person_name').": ".$creditnote_info->name."<br>".
			$this->lang->line('creditnotes_documentdate').": ".to_datetime(strtotime($creditnote_info->documentdate))."<br>".
			$this->lang->line('creditnotes_amount').": ".to_currency($creditnote_info->amount);?></ul>

<?php echo form_open('creditnotes/delete/', array('id'=>'creditnote_delete_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="creditnotes">
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
                    <?php echo form_hidden(array('ids'=>$creditnote_info->creditnote_id)); ?>
                </div>
            </div>
		</div>
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	$('#creditnote_delete_form').validate($.extend({
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