<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <title>Arqueo de Caja</title>
	    <style type="text/css">
			#page_title
			{
				font-size: 22px;
				font-family: "Lato", "Helvetica Neue", Helvetica, Arial, sans-serif;
				font-weight: 400;
			}

			table {
			  background-color: transparent;
			}

			.table {
			  width: 100%;
			  max-width: 100%;
			  margin-bottom: 20px;
			}
			.table > thead > tr > th,
			.table > tbody > tr > th,
			.table > tfoot > tr > th,
			.table > thead > tr > td,
			.table > tbody > tr > td,
			.table > tfoot > tr > td {
			  padding: 8px;
			  line-height: 1.42857143;
			  vertical-align: top;
			  border-top: 1px solid #ddd;
			}
			.table > thead > tr > th {
			  vertical-align: bottom;
			  border-bottom: 2px solid #ddd;
			}
			.table > caption + thead > tr:first-child > th,
			.table > colgroup + thead > tr:first-child > th,
			.table > thead:first-child > tr:first-child > th,
			.table > caption + thead > tr:first-child > td,
			.table > colgroup + thead > tr:first-child > td,
			.table > thead:first-child > tr:first-child > td {
			  border-top: 0;
			}
			.table > tbody + tbody {
			  border-top: 2px solid #ddd;
			}
			.table .table {
			  background-color: #fff;
			}

			.table-striped > tbody > tr:nth-of-type(odd) {
			  background-color: #f9f9f9;
			}

			.table-bordered th,
			.table-bordered td {
			  border: 1px solid #ddd !important;
			}

			.table-bordered {
			  border: 1px solid #ddd;
			}
			.table-bordered > thead > tr > th,
			.table-bordered > tbody > tr > th,
			.table-bordered > tfoot > tr > th,
			.table-bordered > thead > tr > td,
			.table-bordered > tbody > tr > td,
			.table-bordered > tfoot > tr > td {
			  border: 1px solid #ddd;
			}
			.table-bordered > thead > tr > th,
			.table-bordered > thead > tr > td {
			  border-bottom-width: 2px;
			}
	    </style>
	</head>
	<body>
		<div id="page_title" class="btn-toolbar">
			<?php echo $this->lang->line('incomes_summary').": ".to_date(strtotime($cashup_summary->open_date))." - ".$cashup_summary->location_name." ".$cashup_summary->open_first_name." ".$cashup_summary->open_last_name." (".$cashup_summary->code.")"; ?>
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
							</li>
							<li>
								<?php echo 'Caja General Ingreso de Efectivo: ' .to_currency((!empty($open_cash) ? $open_cash : 0)); ?>
							</li>
							<li>
								<?php echo $this->lang->line('ticketsales_one_or_multiple') . ': ' .to_currency((!empty($ticket_sales) ? $ticket_sales : 0)); ?>
							</li>
							<li>
								<?php echo $this->lang->line('invoices_one_or_multiple') . ': ' .to_currency((!empty($invoices) ? $invoices : 0)); ?>
							</li>
						</ul>
						<ul><b style="font-size: large;">Vales: </b><?php echo to_currency($vouchers);?>
							<li>
								<?php echo $this->lang->line('vouchers_one_or_multiple') . ' de Caja: ' .to_currency((!empty($vouchers) ? $vouchers : 0)); ?>
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
							</li>
							<li>
								<?php echo 'Comprobante de Operación Serie I: ' .to_currency((!empty($vo_serie01) ? $vo_serie01 : 0)); ?>
							</li>
							<li>
								<?php echo 'Comprobante de Operación Serie II: ' .to_currency((!empty($vo_serie02) ? $vo_serie02 : 0)); ?>
							</li>
							<li>
								<?php echo $this->lang->line('adjustnotes_one_or_multiple') . ': ' .to_currency((!empty($adjustnotes) ? $adjustnotes : 0)); ?>
							</li>
							<li>
								<?php echo $this->lang->line('creditnotes_one_or_multiple') . ': ' .to_currency((!empty($credittnotes) ? $credittnotes : 0)); ?>
							</li>
						</ul>
						<ul><b style="font-size: large;">Gastos: </b><?php echo to_currency($cashup_summary->expense);?>
							<?php 
								foreach ($expenses as $expense): ?>
							<li>
								<?php echo $expense->concept . ': ' .to_currency((!empty($expense->balance) ? $expense->balance : 0)); ?>
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
	</body>
</html>
