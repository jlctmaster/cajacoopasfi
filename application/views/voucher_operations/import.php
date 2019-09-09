<div id="required_fields_message"><?php echo $this->lang->line('quality_certificates_confirm_import'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('voucher_operations/save_import/', array('id'=>'quality_certificate_import_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="vouchers">
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
		</div>
		<hr>
		<p class="text-center">
	      <strong><?php echo $this->lang->line('quality_certificates_import_sended_data').": ".count($data_sended);?></strong>
	    </p>
		<br>
		<p class="text-center">
	      <strong><?php echo $this->lang->line('quality_certificates_import_failed_data').": ".count($failed_data);?></strong>
	    </p>

	    <?php if(count($data_sended)>0):
	    	$countD = 1;?>
		<div style="display:none;">
		    <table class="table table-hover">
		      <tbody>
		      	<?php foreach ($data_sended as $row):?>
		        <tr>
		          <td><?php echo $countD;?></td>
		          <td><input type="hidden" name="depositdate[]" value="<?php echo $row->depositdate;?>"></td>
		          <td><input type="hidden" name="serieno[]" value="<?php echo $row->serieno;?>"></td>
		          <td><input type="hidden" name="certificate_number[]" value="<?php echo $row->certificate_number;?>"></td>
		          <td><input type="hidden" name="person_id[]" value="<?php echo $row->person_id;?>"></td>
		          <td><input type="hidden" name="kg_dry[]" value="<?php echo $row->kg_dry;?>"></td>
		          <td><input type="hidden" name="qq_dry[]" value="<?php echo $row->qq_dry;?>"></td>
		          <td><input type="hidden" name="rate_profile[]" value="<?php echo $row->rate_profile;?>"></td>
		          <td><input type="hidden" name="physical_performance[]" value="<?php echo $row->physical_performance;?>"></td>
		          <td><input type="hidden" name="quality[]" value="<?php echo $row->quality;?>"></td>
		          <td><input type="hidden" name="location_id[]" value="<?php echo $row->location_id;?>"></td>
		          <td><input type="hidden" name="price[]" value="<?php echo $row->price;?>"></td>
		          <td><input type="hidden" name="amount[]" value="<?php echo $row->amount;?>"></td>
		          <td><input type="hidden" name="reference_id[]" value="<?php echo $row->reference_id;?>"></td>
		          <td><input type="hidden" name="imported[]" value="<?php echo $row->imported;?>"></td>
		        </tr>
		        <?php $countD++;  endforeach;?>
		      </tbody>
		    </table>
	    </div>
		<?php endif;?>

		<?php if(count($failed_data)>0): 
			$count = 1;
		?>
		<hr>
		<p class="text-center">
	      <strong><?php echo $this->lang->line('quality_certificates_import_failed_detail_data').": ".count($failed_data);?></strong>
	    </p>
	    <table class="table table-hover">
	      <thead>
	        <tr>
	          <th>#</th>
	          <th><?php echo $this->lang->line('quality_certificates_depositdate');?></th>
	          <th><?php echo $this->lang->line('quality_certificates_number');?></th>
	          <th><?php echo $this->lang->line('common_dni');?></th>
	          <th><?php echo $this->lang->line('common_person_name');?></th>
	        </tr>
	      </thead>
	      <tbody>
	      	<?php foreach ($failed_data as $fail):?>
	        <tr>
	          <th scope="row"><?php echo $count;?></th>
	          <td><?php echo to_date(strtotime($fail->depositdate));?></td>
	          <td><?php echo $fail->certificate_number;?></td>
	          <td><?php echo $fail->dni;?></td>
	          <td><?php echo $fail->name;?></td>
	        </tr>
	        <?php 
	        	$count++;
	        	endforeach;
	        ?>
	      </tbody>
	    </table>
		<?php endif;?>

	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	$('#quality_certificate_import_form').validate($.extend({
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