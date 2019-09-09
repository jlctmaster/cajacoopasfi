<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{

	$("#operation_type").change(function() {
       table_support.refresh();
    });

    $("#currency").change(function() {
       table_support.refresh();
    });

    $("#cash_concept_id").change(function() {
       table_support.refresh();
    });

	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

	table_support.init({
		resource: '<?php echo site_url($controller_name."/search_detail/".$cashup_summary->cashup_id);?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'cashup_id',
        queryParams: function() {
            return $.extend(arguments[0], {
                operation_type: $("#operation_type").val(),
                currency: $("#currency").val(),
                cash_concept_id: $("#cash_concept_id").val()
            });
        }
		
	});

	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e)
	{
		table_support.refresh();
	});
});
</script>

<div id="page_title" class="btn-toolbar">
	<?php echo anchor('cashups/detail/'.$cashup_summary->cashup_id, $this->lang->line('common_back')); ?>
</div>

<div id="toolbar">
	<?php echo form_dropdown('operation_type', $operation_types, $operation_type, array('id'=>'operation_type', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
	<?php echo form_dropdown('currency', $currencies, $currency, array('id'=>'currency', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
	<?php echo form_dropdown('cash_concept_id', $cash_concepts, $cash_concept_id, array('id'=>'cash_concept_id', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
</div>

<div id="table_holder">
	<table id="table"></table>
</div>

<div id="report_summary">
	<?php echo $this->lang->line('costs_summary'); ?>
	<div class="summary_row"><?php echo $this->lang->line('cashups_closed_amount_cash') . ': ' .to_currency((!empty($cashup_summary->closed_amount_total) ? $cashup_summary->closed_amount_total : 0)); ?></div>
</div>

<?php $this->load->view("partial/footer"); ?>
