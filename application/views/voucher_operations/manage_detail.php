<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function()
{
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
				return "<?php echo $this->lang->line('quality_certificates_no_quality_certificates_to_display'); ?>";
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
		resource: '<?php echo site_url($controller_name."/search_detail/".$voucher_operation_info->voucher_operation_id);?>',
		headers: <?php echo $table_headers; ?>,
		pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
		uniqueId: 'quality_certificate_id',
		
	});

	// when any filter is clicked and the dropdown window is closed
	$('#filters').on('hidden.bs.select', function(e)
	{
		table_support.refresh();
	});
});
</script>

<div id="page_title"><?php echo $this->lang->line("voucher_operations_details"); ?><br>
	<?php echo anchor('voucher_operations', $this->lang->line('common_back')); ?></div>

<div id="title_bar" class="btn-toolbar">
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
