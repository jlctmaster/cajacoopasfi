<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

	table_support.init({
		resource: '<?php echo site_url($controller_name."/search");?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'overall_cash_id',
		
	});

	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e)
	{
		table_support.refresh();
	});
});
</script>

<div id="title_bar" class="btn-toolbar">
	<?php echo anchor($controller_name.'/bank','<span class="glyphicon glyphicon-edit">&nbsp</span>'.$this->lang->line($controller_name . '_financialentity'),array('class' => 'btn btn-success btn-sm pull-right')); ?>
	<?php if(!$overall_cash_openend):?>
	<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view"); ?>'
			title='<?php echo $this->lang->line($controller_name.'_new'); ?>'>
		<span class="glyphicon glyphicon-folder-open">&nbsp</span><?php echo $this->lang->line($controller_name . '_new'); ?>
	</button>
	<?php else:?>
	<?php echo anchor($controller_name.'/cost','<span class="glyphicon glyphicon-minus">&nbsp</span>'.$this->lang->line($controller_name . '_costmanage'),array('class' => 'btn btn-success btn-sm pull-right')); ?>
	<?php echo anchor($controller_name.'/income','<span class="glyphicon glyphicon-plus">&nbsp</span>'.$this->lang->line($controller_name . '_incomemanage'),array('class' => 'btn btn-success btn-sm pull-right')); ?>
	<?php endif;?>
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
