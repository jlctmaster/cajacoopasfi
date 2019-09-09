<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
$(document).ready(function()
{
	$('#btn-modal').click(function()
	{
		$('.modal-dlg').modal("show");
	});
});
</script>

<div id="page_title" class="btn-toolbar">
	<?php echo anchor($controller_name.'/print_report/'.$cashup_summary->cashup_id,'<span class="glyphicon glyphicon-print">&nbsp</span>'.$this->lang->line('common_print'),array('class' => 'btn btn-info btn-sm pull-right')); ?>
	<!--<button id="btn-modal" class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_close') ?>' data-href='<?php echo site_url($controller_name."/close/".$cashup_summary->cashup_id); ?>'
			title='<?php echo $this->lang->line('expenses_new'); ?>'>
		<span class="glyphicon glyphicon-folder-close">&nbsp</span><?php echo $this->lang->line('cashups_close'); ?>
	</button>-->
	<?php echo $this->lang->line('incomes_summary').": ".to_date(strtotime($cashup_summary->open_date))." - ".$cashup_summary->location_name." ".$cashup_summary->open_first_name." ".$cashup_summary->open_last_name." (".$cashup_summary->code.")"; ?>
	<br>
	<?php echo anchor('cashups', $this->lang->line('common_back')); ?>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-6">
			<?php echo '<b style="font-size: large;"> Saldo Anterior de Apertura:</b> ' .to_currency((!empty($initial_balance) ? $initial_balance : 0)); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div id="table_holder">
				<ul><b style="font-size: large;">Ingresos: </b><?php echo to_currency($cashup_summary->income);?>
					<li>
						<?php echo 'Recibo de '.$this->lang->line('incomes_one_or_multiple') . ': ' .to_currency((!empty($receipt_income) ? $receipt_income : 0)); ?>
						<?php echo anchor($controller_name.'/detail_income/'.$cashup_summary->cashup_id.'/'.CURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de ingresos')); ?>
					</li>
					<li>
						<?php echo 'Caja General Ingreso de Efectivo: ' .to_currency((!empty($open_cash) ? $open_cash : 0)); ?>
						<?php echo anchor($controller_name.'/detail_income/'.$cashup_summary->cashup_id.'/'.CURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de ingresos')); ?>
					</li>
					<li>
						<?php echo $this->lang->line('ticketsales_one_or_multiple') . ': ' .to_currency((!empty($ticket_sales) ? $ticket_sales : 0)); ?>
						<?php echo anchor($controller_name.'/detail_income/'.$cashup_summary->cashup_id.'/'.CURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de ingresos')); ?>
					</li>
					<li>
						<?php echo $this->lang->line('invoices_one_or_multiple') . ': ' .to_currency((!empty($invoices) ? $invoices : 0)); ?>
						<?php echo anchor($controller_name.'/detail_income/'.$cashup_summary->cashup_id.'/'.CURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de ingresos')); ?>
					</li>
				</ul>
				<br>
				<ul><b style="font-size: large;">Vales: </b><?php echo to_currency($vouchers);?>
					<li>
						<?php echo $this->lang->line('vouchers_one_or_multiple') . ' de Caja: ' .to_currency((!empty($vouchers) ? $vouchers : 0)); ?>
						<?php echo anchor('vouchers','<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de vales')); ?>
					</li>
				</ul>
				<b style="font-size: large;">Total: </b><?php echo to_currency($initial_balance+$cashup_summary->income+$vouchers);?>
			</div>
		</div>
		<div class="col-md-6">
			<div id="table_holder">
				<ul><b style="font-size: large;">Egresos: </b><?php echo to_currency($cashup_summary->cost);?>
					<li>
						<?php echo 'Recibo de '.$this->lang->line('costs_one_or_multiple') . ': ' .to_currency((!empty($receipt_cost) ? $receipt_cost : 0)); ?>
						<?php echo anchor($controller_name.'/detail_cost/'.$cashup_summary->cashup_id.'/'.CURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de egresos')); ?>
					</li>
					<li>
						<?php echo 'Comprobante de Operación Serie I: ' .to_currency((!empty($vo_serie01) ? $vo_serie01 : 0)); ?>
						<?php echo anchor($controller_name.'/detail_cost/'.$cashup_summary->cashup_id.'/'.CURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de egresos')); ?>
					</li>
					<li>
						<?php echo 'Comprobante de Operación Serie II: ' .to_currency((!empty($vo_serie02) ? $vo_serie02 : 0)); ?>
						<?php echo anchor($controller_name.'/detail_cost/'.$cashup_summary->cashup_id.'/'.CURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de egresos')); ?>
					</li>
					<li>
						<?php echo $this->lang->line('adjustnotes_one_or_multiple') . ': ' .to_currency((!empty($adjustnotes) ? $adjustnotes : 0)); ?>
						<?php echo anchor($controller_name.'/detail_cost/'.$cashup_summary->cashup_id.'/'.CURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de egresos')); ?>
					</li>
					<li>
						<?php echo $this->lang->line('creditnotes_one_or_multiple') . ': ' .to_currency((!empty($credittnotes) ? $credittnotes : 0)); ?>
						<?php echo anchor($controller_name.'/detail_cost/'.$cashup_summary->cashup_id.'/'.CURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de egresos')); ?>
					</li>
				</ul>
				<br>
				<ul><b style="font-size: large;">Gastos: </b><?php echo to_currency($cashup_summary->expense);?>
					<?php 
						foreach ($expenses as $expense): ?>
					<li>
						<?php echo $expense->concept . ': ' .to_currency((!empty($expense->balance) ? $expense->balance : 0)); ?>
						<?php echo anchor($controller_name.'/detail_expense/'.$cashup_summary->cashup_id.'/'.CURRENCY.'/'.$expense->cash_concept_id,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de '.$expense->concept)); ?>
					</li>
					<?php endforeach;?>
				</ul>
				<b style="font-size: large;">Total: </b><?php echo to_currency($cashup_summary->cost+$cashup_summary->expense);?>
			</div>
		</div>
	</div>
	<div class="row">
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<td><?php echo $this->lang->line('overall_cashs_denomination')?></td>
					<td><?php echo $this->lang->line('overall_cashs_quantity')?></td>
					<td><?php echo $this->lang->line('overall_cashs_amount')?></td>
				</tr>
			</thead>
			<tbody id="currency_denominations">
				<?php foreach($denomination_currency AS $currency):?>
				<tr>
					<td style="text-align:center;"><?php echo $currency->denomination;?></td>
					<td style="text-align:right;"><?php echo $currency->quantity;?></td>
					<td style="text-align:right;"><?php echo to_currency($currency->amount);?></td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view("partial/footer"); ?>
