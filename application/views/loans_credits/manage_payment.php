<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

	table_support.init({
		resource: '<?php echo site_url($controller_name."/search_payment/".$table."/".$see_type."/".$loan_credit_info->id);?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'id',
		
	});

	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e)
	{
		table_support.refresh();
	});
});
</script>

<div id="page_title"><?php echo ($see_type=="a" ? $this->lang->line("loans_credits_payschedule") : $this->lang->line("loans_credits_payment_detail")." ".($table =='l' ? $this->lang->line("loans_credits_loan_id") : $this->lang->line("loans_credits_credit_id"))); ?><br>
	<?php echo anchor('loans_credits', $this->lang->line('common_back')); ?></div>

<div id="page_subtitle"><?php echo $this->lang->line("loans_credits_dni").": ".$loan_credit_info->dni."<br>".$this->lang->line("loans_credits_name").": ".$loan_credit_info->name; ?></div>

<div id="title_bar" class="btn-toolbar">
	<?php if(empty($cashups->cashup_id) || (!empty($cashups->cashup_id) && !empty($cashups->close_date))):?>
	<div class="alert alert-warning" role="alert" style="padding: 2.5px;">
		<h5>Primero debe aperturar un <a class="modal-dlg" data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' href="<?php echo site_url("cashups/view"); ?>"><span class="badge badge-secondary"><?php echo $this->lang->line('module_cashups');?></span></a> para el usuario: <span class="badge badge-secondary"><?php echo $user_info->first_name." ".$user_info->last_name?></span></h5>
	</div>
	<?php else: ?>
		<?php if($show_buttom && $table == "c"):?>
		<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/pay_credit/".$loan_credit_info->id); ?>'
				title='<?php echo $this->lang->line($controller_name.'_pay_credit'); ?>'>
			<span class="glyphicon glyphicon-usd">&nbsp</span><?php echo $this->lang->line($controller_name . '_pay_credit'); ?>
		</button>
		<?php elseif($show_buttom && $table == "l"): ?>
		<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/pay_loan/".$loan_credit_info->id); ?>'
				title='<?php echo $this->lang->line($controller_name.'_pay_loan'); ?>'>
			<span class="glyphicon glyphicon-usd">&nbsp</span><?php echo $this->lang->line($controller_name . '_pay_loan'); ?>
		</button>
		<?php endif;?>
	<?php endif;?>
</div>

<div id="toolbar">
</div>

<div id="table_holder">
	<table id="table"></table>
</div>

<div id="report_summary">
	<div class="summary_row"><?php echo $this->lang->line('loans_credits_amount') . ': ' .$loan_credit_info->amount; ?></div>
	<div class="summary_row"><?php echo $this->lang->line('loans_credits_interest') . ': ' .$loan_credit_info->amt_interest; ?></div>
	<div class="summary_row"><?php echo $this->lang->line('loans_credits_payment_amount') . ': ' .$loan_credit_info->pay_amount; ?></div>
	<div class="summary_row"><?php echo $this->lang->line('loans_credits_balance') . ': ' .$loan_credit_info->balance; ?></div>
</div>

<?php $this->load->view("partial/footer"); ?>
