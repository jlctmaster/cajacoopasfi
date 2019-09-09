<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

	table_support.init({
		resource: '<?php echo site_url($controller_name."/search_payment/".$voucher_info->voucher_id);?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'payment_voucher_id',
		
	});

	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e)
	{
		table_support.refresh();
	});
});
</script>

<div id="page_title"><?php echo $this->lang->line("vouchers_payment_detail"); ?><br>
	<?php echo anchor('vouchers', $this->lang->line('common_back')); ?></div>

<div id="page_subtitle"><?php echo $this->lang->line("common_dni").": ".$voucher_info->dni."<br>".$this->lang->line("common_person_name").": ".$voucher_info->name."<br>".$this->lang->line("vouchers_number").": ".$voucher_info->voucher_number; ?></div>

<div id="title_bar" class="btn-toolbar">
</div>

<div id="toolbar">
</div>

<div id="table_holder">
	<table id="table"></table>
</div>

<div id="report_summary">
	<div class="summary_row"><?php echo $this->lang->line('vouchers_amount') . ': ' .$voucher_info->amount; ?></div>
	<div class="summary_row"><?php echo $this->lang->line('vouchers_rendered') . ': ' .$voucher_info->rendered; ?></div>
	<div class="summary_row"><?php echo $this->lang->line('vouchers_balance') . ': ' .$voucher_info->balance; ?></div>
</div>

<?php $this->load->view("partial/footer"); ?>
