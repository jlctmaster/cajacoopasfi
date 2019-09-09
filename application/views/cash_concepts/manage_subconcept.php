<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

	table_support.init({
		resource: '<?php echo site_url($controller_name."/search_subconcept/".$parent_info->cash_concept_id);?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'cash_concept_id',
		
	});

	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e)
	{
		table_support.refresh();
	});
});
</script>

<div id="page_title"><?php echo $parent_info->code." ".$parent_info->name; ?></div>

<div id="title_bar" class="btn-toolbar">
	<?php echo anchor('cash_concepts', $this->lang->line('common_back')); ?>
	<?php if($parent_info->concept_type=="3"): ?>
	<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view_subconcept/".$parent_info->cash_concept_id); ?>'
			title='<?php echo $this->lang->line($controller_name.'_new_expense'); ?>'>
		<span class="glyphicon glyphicon-plus">&nbsp</span><?php echo $this->lang->line($controller_name . '_new_expense'); ?>
	</button>
	<?php elseif($parent_info->concept_type=="2"): ?>
	<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view_subconcept/".$parent_info->cash_concept_id); ?>'
			title='<?php echo $this->lang->line($controller_name.'_new_cost'); ?>'>
		<span class="glyphicon glyphicon-plus">&nbsp</span><?php echo $this->lang->line($controller_name . '_new_cost'); ?>
	</button>
	<?php else: ?>
	<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view_subconcept/".$parent_info->cash_concept_id); ?>'
			title='<?php echo $this->lang->line($controller_name.'_new'); ?>'>
		<span class="glyphicon glyphicon-plus">&nbsp</span><?php echo $this->lang->line($controller_name . '_new'); ?>
	</button>
	<?php endif; ?>
</div>

<div id="toolbar">
	<div class="pull-left form-inline" role="toolbar">
		<button id="delete" class="btn btn-default btn-sm print_hide">
			<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete");?>
		</button>
	</div>
</div>

<div id="table_holder">
	<table id="table"></table>
</div>

<?php $this->load->view("partial/footer"); ?>
