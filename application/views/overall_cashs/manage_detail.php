<?php $this->load->view("partial/header"); ?>

<div id="page_title" class="btn-toolbar">
	<?php echo anchor($controller_name.'/print_report/'.$overall_cash_summary->overall_cash_id,'<span class="glyphicon glyphicon-print">&nbsp</span>'.$this->lang->line('common_print'),array('class' => 'btn btn-info btn-sm pull-right')); ?>
	<?php echo $this->lang->line('incomes_summary').": ".to_date(strtotime($overall_cash_summary->opendate))." - ".$cash_book_info->location_name." ".$cash_book_info->username." (".$cash_book_info->username.")"; ?>
	<br>
	<?php echo anchor('overall_cashs', $this->lang->line('common_back')); ?>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-6">
			<div id="table_holder">
				<?php echo $this->lang->line('overall_cashs_openingbalance') . ': ' .to_currency((!empty($overall_cash_summary->startbalance) ? $overall_cash_summary->startbalance : 0)); ?>
				<br>
				<?php echo 'Recibo de '.$this->lang->line('incomes_one_or_multiple') . ': ' .to_currency((!empty($overall_cash_summary->income) ? $overall_cash_summary->income : 0)); ?>
				<?php echo anchor($controller_name.'/detail_income/'.$overall_cash_summary->overall_cash_id.'/'.CURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de ingresos')); ?>
				<br>
				<?php echo 'Recibo de '.$this->lang->line('costs_one_or_multiple') . ': ' .to_currency((!empty($overall_cash_summary->cost) ? $overall_cash_summary->cost : 0)); ?>
				<?php echo anchor($controller_name.'/detail_cost/'.$overall_cash_summary->overall_cash_id.'/'.CURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de egresos')); ?>
				<br>
				<?php echo $this->lang->line('overall_cashs_endingbalance') . ': ' .to_currency((!empty($overall_cash_summary->endingbalance) ? $overall_cash_summary->endingbalance : 0)); ?>
				<br>
				<br>
				<br>
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
		<div class="col-md-6">
			<div id="table_holder">
				<?php echo $this->lang->line('overall_cashs_openingbalance') . ': ' .to_usd((!empty($overall_cash_summary->usdstartbalance) ? $overall_cash_summary->usdstartbalance : 0)); ?>
				<br>
				<?php echo 'Recibo de '.$this->lang->line('incomes_one_or_multiple') . ': ' .to_usd((!empty($overall_cash_summary->usdincome) ? $overall_cash_summary->usdincome : 0)); ?>
				<?php echo anchor($controller_name.'/detail_income/'.$overall_cash_summary->overall_cash_id.'/'.USDCURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de ingresos')); ?>
				<br>
				<?php echo 'Recibo de '.$this->lang->line('costs_one_or_multiple') . ': ' .to_usd((!empty($overall_cash_summary->usdcost) ? $overall_cash_summary->usdcost : 0)); ?>
				<?php echo anchor($controller_name.'/detail_cost/'.$overall_cash_summary->overall_cash_id.'/'.USDCURRENCY,'<span class="glyphicon glyphicon-list-alt">&nbsp</span>',array('title' => 'Ver detalle de ingresos')); ?>
				<br>
				<?php echo $this->lang->line('overall_cashs_endingbalance') . ': ' .to_usd((!empty($overall_cash_summary->usdendingbalance) ? $overall_cash_summary->usdendingbalance : 0)); ?>
				<br>
				<br>
				<br>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<td><?php echo $this->lang->line('overall_cashs_denomination')?></td>
							<td><?php echo $this->lang->line('overall_cashs_quantity')?></td>
							<td><?php echo $this->lang->line('overall_cashs_amount')?></td>
						</tr>
					</thead>
					<tbody id="currency_denominations">
						<?php foreach($denomination_usd AS $usd):?>
						<tr>
							<td style="text-align:center;"><?php echo $usd->denomination;?></td>
							<td style="text-align:right;"><?php echo $usd->quantity;?></td>
							<td style="text-align:right;"><?php echo to_usd($usd->amount);?></td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view("partial/footer"); ?>
