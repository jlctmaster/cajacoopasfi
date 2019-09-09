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

	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

	table_support.init({
		resource: '<?php echo site_url($controller_name."/search_detail/".$overall_cash_summary->overall_cash_id);?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'overall_cash_id',
        queryParams: function() {
            return $.extend(arguments[0], {
                operation_type: $("#operation_type").val(),
                currency: $("#currency").val()
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
	<?php echo anchor('overall_cashs/detail/'.$overall_cash_summary->overall_cash_id, $this->lang->line('common_back')); ?>
</div>

<div id="toolbar">
	<?php echo form_dropdown('operation_type', $operation_types, $operation_type, array('id'=>'operation_type', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
	<?php echo form_dropdown('currency', $currencies, $currency, array('id'=>'currency', 'class'=>'selectpicker show-menu-arrow', 'data-style'=>'btn-default btn-sm', 'data-width'=>'fit')); ?>
</div>

<div id="table_holder">
	<table id="table"></table>
</div>

<div id="report_summary">
	<?php echo $this->lang->line('costs_summary'); ?>
	<div class="summary_row"><?php echo $this->lang->line('overall_cashs_endingbalance') . ': ' .to_currency((!empty($overall_cash_summary->endingbalance) ? $overall_cash_summary->endingbalance : 0)); ?></div>
	<div class="summary_row"><?php echo $this->lang->line('overall_cashs_endingbalance') . ': ' .to_usd((!empty($overall_cash_summary->usdendingbalance) ? $overall_cash_summary->usdendingbalance : 0)); ?></div>
</div>

<?php $this->load->view("partial/footer"); ?>
