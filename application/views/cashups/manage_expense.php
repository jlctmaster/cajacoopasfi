<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	// load the preset datarange picker
	<?php $this->load->view('partial/daterangepicker'); ?>

	$("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {
		table_support.refresh();
	});

	(function ($) {
	'use strict';

		$.fn.bootstrapTable.locales['<?php echo current_language_code(); ?>'] = {
			formatLoadingMessage: function () {
				return "<?php echo $this->lang->line('tables_loading');?>";
			},
			formatRecordsPerPage: function (pageNumber) {
				return "<?php echo $this->lang->line('tables_rows_per_page'); ?>".replace('{0}', pageNumber);
			},
			formatShowingRows: function (pageFrom, pageTo, totalRows) {
				return "<?php echo $this->lang->line('tables_page_from_to'); ?>".replace('{0}', pageFrom).replace('{1}', pageTo).replace('{2}', totalRows);
			},
			formatSearch: function () {
				return "<?php echo $this->lang->line('common_search'); ?>";
			},
			formatNoMatches: function () {
				return "<?php echo $this->lang->line('expenses_no_expense_to_display'); ?>";
			},
			formatPaginationSwitch: function () {
				return "<?php echo $this->lang->line('tables_hide_show_pagination'); ?>";
			},
			formatRefresh: function () {
				return "<?php echo $this->lang->line('tables_refresh'); ?>";
			},
			formatToggle: function () {
				return "<?php echo $this->lang->line('tables_toggle'); ?>";
			},
			formatColumns: function () {
				return "<?php echo $this->lang->line('tables_columns'); ?>";
			},
			formatAllRows: function () {
				return "<?php echo $this->lang->line('tables_all'); ?>";
			},
			formatConfirmAction: function(action) {
				if (action == "delete")
				{
					return "<?php echo $this->lang->line((isset($editable) ? $editable : $controller_name). "_confirm_delete")?>";
				}
				else
				{
					return "<?php echo $this->lang->line((isset($editable) ? $editable : $controller_name). "_confirm_restore")?>";
				}
	        }
		};

		$.extend($.fn.bootstrapTable.defaults, $.fn.bootstrapTable.locales["<?php echo current_language_code();?>"]);

	})(jQuery);

	table_support.init({
		resource: '<?php echo site_url($controller_name."/search_expense");?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'expense_id',
		queryParams: function() {
			return $.extend(arguments[0], {
				start_date: start_date,
				end_date: end_date
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

<div id="page_title"><?php echo $this->lang->line("expenses_info"); ?><br>
	<?php echo anchor('cashups', $this->lang->line('common_back')); ?></div>

<div id="title_bar" class="btn-toolbar">
	<button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>' data-href='<?php echo site_url($controller_name."/view_expense"); ?>'
			title='<?php echo $this->lang->line('expenses_new'); ?>'>
		<span class="glyphicon glyphicon-plus">&nbsp</span><?php echo $this->lang->line('expenses_new'); ?>
	</button>
</div>

<div id="toolbar">
	<?php echo form_input(array('name'=>'daterangepicker', 'class'=>'form-control input-sm', 'id'=>'daterangepicker')); ?>
	<!--<div class="pull-left form-inline" role="toolbar">
		<button id="delete" class="btn btn-default btn-sm print_hide">
			<span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete");?>
		</button>
	</div>-->
</div>

<div id="table_holder">
	<table id="table"></table>
</div>

<div id="report_summary">
	<?php echo $this->lang->line('expenses_summary'); ?>
	<div class="summary_row"><?php echo 'Total '.$this->lang->line('expenses_cash_currency') . ': ' .to_currency((!empty($expense_summary->cash_amount) ? $expense_summary->cash_amount : 0)); ?></div>
	<hr>
	<div class="summary_row"><?php echo 'Total '.$this->lang->line('expenses_bank_currency') . ': ' .to_currency((!empty($expense_summary->check_amount) ? $expense_summary->check_amount : 0)); ?></div>
</div>

<?php $this->load->view("partial/footer"); ?>
