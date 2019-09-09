<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

	table_support.init({
		resource: '<?php echo site_url($controller_name."/search");?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'adjustnote_id',
		
	});

	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e)
	{
		table_support.refresh();
	});
});
</script>

<div id="title_bar" class="btn-toolbar">
	<?php if(empty($cashups->cashup_id) || (!empty($cashups->cashup_id) && !empty($cashups->close_date))):?>
	<div class="alert alert-warning" role="alert" style="padding: 2.5px;">
		<h5>Primero debe aperturar un <a class="modal-dlg" data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' href="<?php echo site_url("cashups/view"); ?>"><span class="badge badge-secondary"><?php echo $this->lang->line('module_cashups');?></span></a> para el usuario: <span class="badge badge-secondary"><?php echo $user_info->first_name." ".$user_info->last_name?></span></h5>
	</div>
	<?php else: ?>
	<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view"); ?>'
			title='<?php echo $this->lang->line($controller_name.'_new'); ?>'>
		<span class="glyphicon glyphicon-plus">&nbsp</span><?php echo $this->lang->line($controller_name . '_new'); ?>
	</button>
	<?php endif; ?>
</div>

<div id="toolbar">
	<!--<div class="pull-left form-inline" role="toolbar">
		<button id="delete" class="btn btn-default btn-sm print_hide">
			<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete");?>
		</button>
	</div>-->
</div>

<div id="table_holder">
	<table id="table"></table>
</div>

<?php $this->load->view("partial/footer"); ?>
