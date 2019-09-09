<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>
<div class="modal-body">
	<?php if($row_inserted > 0): ?>
		<hr>
		<i class="fas fa-times fa-4x animated rotateIn"></i>
		<p class="text-center">
	      <strong><?php echo $this->lang->line('voucher_operations_import_success_data').": ".count($row_inserted);?></strong>
	    </p>
	<?php endif;?>

	<?php if($row_sended > $row_inserted): 
		$failed_row = $row_sended-$row_inserted;
	?>
		<hr>
		<i class="fas fa-times fa-4x animated rotateIn"></i>
		<p class="text-center">
	      <strong><?php echo $this->lang->line('voucher_operations_import_failed_data').": ".count($failed_data);?></strong>
	    </p>
	<?php endif;?>

	<?php if(count($failed_data)>0): 
		$count = 1;
	?>
	<hr>
	<p class="text-center">
      <strong><?php echo $this->lang->line('voucher_operations_import_failed_data').": ".count($failed_data);?></strong>
    </p>
    <table class="table table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th><?php echo $this->lang->line('quality_certificates_depositdate');?></th>
          <th><?php echo $this->lang->line('quality_certificates_number');?></th>
          <th><?php echo $this->lang->line('common_dni');?></th>
        </tr>
      </thead>
      <tbody>
      	<?php foreach ($failed_data as $data):?>
        <tr>
          <th scope="row"><?php echo $count;?></th>
          <td><?php echo to_date(strtotime($data->depositdate));?></td>
          <td><?php echo $data->certificate_number;?></td>
          <td><?php echo $data->dni;?></td>
        </tr>
        <?php 
        	$count++;
        	endforeach;
        ?>
      </tbody>
    </table>
	<?php endif;?>
</div>