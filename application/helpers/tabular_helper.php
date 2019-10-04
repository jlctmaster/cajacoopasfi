<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tabular views helper
 */

/*
Basic tabular headers function
*/
function transform_headers_readonly($array)
{
	$result = array();

	foreach($array as $key => $value)
	{
		$result[] = array('field' => $key, 'title' => $value, 'sortable' => $value != '', 'switchable' => !preg_match('(^$|&nbsp)', $value));
	}

	return json_encode($result);
}

/*
Basic tabular headers function
*/
function transform_headers($array, $readonly = FALSE, $editable = TRUE)
{
	$result = array();

	if(!$readonly)
	{
		$array = array_merge(array(array('checkbox' => 'select', 'sortable' => FALSE)), $array);
	}

	if($editable)
	{
		$array[] = array('edit' => '');
	}

	foreach($array as $element)
	{
		reset($element);
		$result[] = array('field' => key($element),
			'title' => current($element),
			'switchable' => isset($element['switchable']) ? $element['switchable'] : !preg_match('(^$|&nbsp)', current($element)),
			'sortable' => isset($element['sortable']) ? $element['sortable'] : current($element) != '',
			'checkbox' => isset($element['checkbox']) ? $element['checkbox'] : FALSE,
			'class' => isset($element['checkbox']) || preg_match('(^$|&nbsp)', current($element)) ? 'print_hide' : '',
			'sorter' => isset($element['sorter']) ? $element ['sorter'] : '');
	}

	return json_encode($result);
}


/*
Get the header for the sales tabular view
*/
function get_sales_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('sale_id' => $CI->lang->line('common_id')),
		array('sale_time' => $CI->lang->line('sales_sale_time')),
		array('customer_name' => $CI->lang->line('customers_customer')),
		array('amount_due' => $CI->lang->line('sales_amount_due')),
		array('amount_tendered' => $CI->lang->line('sales_amount_tendered')),
		array('change_due' => $CI->lang->line('sales_change_due')),
		array('payment_type' => $CI->lang->line('sales_payment_type'))
	);

	if($CI->config->item('invoice_enable') == TRUE)
	{
		$headers[] = array('invoice_number' => $CI->lang->line('sales_invoice_number'));
		$headers[] = array('invoice' => '&nbsp', 'sortable' => FALSE);
	}

	$headers[] = array('receipt' => '&nbsp', 'sortable' => FALSE);

	return transform_headers($headers);
}

/*
Get the html data row for the sales
*/
function get_sale_data_row($sale)
{
	$CI =& get_instance();
	$controller_name = $CI->uri->segment(1);

	$row = array (
		'sale_id' => $sale->sale_id,
		'sale_time' => date($CI->config->item('dateformat') . ' ' . $CI->config->item('timeformat'), strtotime($sale->sale_time)),
		'customer_name' => $sale->customer_name,
		'amount_due' => to_currency($sale->amount_due),
		'amount_tendered' => to_currency($sale->amount_tendered),
		'change_due' => to_currency($sale->change_due),
		'payment_type' => $sale->payment_type
	);

	if($CI->config->item('invoice_enable'))
	{
		$row['invoice_number'] = $sale->invoice_number;
		$row['invoice'] = empty($sale->invoice_number) ? '' : anchor($controller_name."/invoice/$sale->sale_id", '<span class="glyphicon glyphicon-list-alt"></span>',
			array('title'=>$CI->lang->line('sales_show_invoice'))
		);
	}

	$row['receipt'] = anchor($controller_name."/receipt/$sale->sale_id", '<span class="glyphicon glyphicon-usd"></span>',
		array('title' => $CI->lang->line('sales_show_receipt'))
	);
	$row['edit'] = anchor($controller_name."/edit/$sale->sale_id", '<span class="glyphicon glyphicon-edit"></span>',
		array('class' => 'modal-dlg print_hide', 'data-btn-delete' => $CI->lang->line('common_delete'), 'data-btn-submit' => $CI->lang->line('common_submit'), 'title' => $CI->lang->line($controller_name.'_update'))
	);

	return $row;
}

/*
Get the html data last row for the sales
*/
function get_sale_data_last_row($sales)
{
	$CI =& get_instance();
	$sum_amount_due = 0;
	$sum_amount_tendered = 0;
	$sum_change_due = 0;

	foreach($sales->result() as $key=>$sale)
	{
		$sum_amount_due += $sale->amount_due;
		$sum_amount_tendered += $sale->amount_tendered;
		$sum_change_due += $sale->change_due;
	}

	return array(
		'sale_id' => '-',
		'sale_time' => '<b>'.$CI->lang->line('sales_total').'</b>',
		'amount_due' => '<b>'.to_currency($sum_amount_due).'</b>',
		'amount_tendered' => '<b>'. to_currency($sum_amount_tendered).'</b>',
		'change_due' => '<b>'.to_currency($sum_change_due).'</b>'
	);
}

/*
Get the sales payments summary
*/
function get_sales_manage_payments_summary($payments, $sales)
{
	$CI =& get_instance();
	$table = '<div id="report_summary">';

	foreach($payments as $key=>$payment)
	{
		$amount = $payment['payment_amount'];

		// WARNING: the strong assumption here is that if a change is due it was a cash transaction always
		// therefore we remove from the total cash amount any change due
		if( $payment['payment_type'] == $CI->lang->line('sales_cash') )
		{
			foreach($sales->result_array() as $key=>$sale)
			{
				$amount -= $sale['change_due'];
			}
		}
		$table .= '<div class="summary_row">' . $payment['payment_type'] . ': ' . to_currency($amount) . '</div>';
	}
	$table .= '</div>';

	return $table;
}


/*
Get the header for the people tabular view
*/
function get_people_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('people.person_id' => $CI->lang->line('common_id')),
		array('dni' => $CI->lang->line('common_dni')),
		array('first_name' => $CI->lang->line('common_first_name')),
		array('last_name' => $CI->lang->line('common_last_name')),
		array('email' => $CI->lang->line('common_email')),
		array('phone_number' => $CI->lang->line('common_phone_number'))
	);

	if($CI->User->has_grant('messages', $CI->session->userdata('person_id')))
	{
		$headers[] = array('messages' => '', 'sortable' => FALSE);
	}

	return transform_headers($headers);
}

/*
Get the html data row for the person
*/
function get_person_data_row($person)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'people.person_id' => $person->person_id,
		'dni' => $person->dni,
		'first_name' => $person->first_name,
		'last_name' => $person->last_name,
		'email' => empty($person->email) ? '' : mailto($person->email, $person->email),
		'phone_number' => $person->phone_number,
		'messages' => empty($person->phone_number) ? '' : anchor("Messages/view/$person->person_id", '<span class="glyphicon glyphicon-phone"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('messages_sms_send'))),
		'edit' => anchor($controller_name."/view/$person->person_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
	));
}


/*
Get the header for the customer tabular view
*/
function get_customer_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('people.person_id' => $CI->lang->line('common_id')),
		array('dni' => $CI->lang->line('common_dni')),
		array('first_name' => $CI->lang->line('common_first_name')),
		array('last_name' => $CI->lang->line('common_last_name')),
		array('email' => $CI->lang->line('common_email')),
		array('phone_number' => $CI->lang->line('common_phone_number')),
		array('ruc' => $CI->lang->line('common_ruc')),
		array('company_name' => $CI->lang->line('customers_company_name')),
		array('total' => $CI->lang->line('common_total_spent'), 'sortable' => FALSE)
	);

	if($CI->User->has_grant('messages', $CI->session->userdata('person_id')))
	{
		$headers[] = array('messages' => '', 'sortable' => FALSE);
	}

	return transform_headers($headers);
}

/*
Get the html data row for the customer
*/
function get_customer_data_row($person, $stats)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'people.person_id' => $person->person_id,
		'dni' => $person->dni,
		'first_name' => $person->first_name,
		'last_name' => $person->last_name,
		'email' => empty($person->email) ? '' : mailto($person->email, $person->email),
		'phone_number' => $person->phone_number,
		'ruc' => $person->ruc,
		'company_name' => $person->company_name,
		'total' => to_currency($stats->total),
		'messages' => empty($person->phone_number) ? '' : anchor("Messages/view/$person->person_id", '<span class="glyphicon glyphicon-phone"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('messages_sms_send'))),
		'edit' => anchor($controller_name."/view/$person->person_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
	));
}

/*
Get the header for the suppliers tabular view
*/
function get_suppliers_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('people.person_id' => $CI->lang->line('common_id')),
		array('dni' => $CI->lang->line('common_dni')),
		array('first_name' => $CI->lang->line('common_first_name')),
		array('last_name' => $CI->lang->line('common_last_name')),
		array('ispartner' => $CI->lang->line('suppliers_ispartner')),
		array('growing_area_name' => $CI->lang->line('suppliers_growing_area_id')),
		array('association_date' => $CI->lang->line('suppliers_association_date')),
		array('ruc' => $CI->lang->line('common_ruc')),
		array('company_name' => $CI->lang->line('suppliers_company_name')),
		array('agency_name' => $CI->lang->line('suppliers_agency_name')),
		array('email' => $CI->lang->line('common_email')),
		array('phone_number' => $CI->lang->line('common_phone_number'))
	);

	if($CI->User->has_grant('messages', $CI->session->userdata('person_id')))
	{
		$headers[] = array('messages' => '');
	}

	return transform_headers($headers);
}

/*
Get the html data row for the supplier
*/
function get_supplier_data_row($supplier)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'people.person_id' => $supplier->person_id,
		'dni' => $supplier->dni,
		'first_name' => $supplier->first_name,
		'last_name' => $supplier->last_name,
		'ispartner' => ($supplier->ispartner==1 ? $CI->lang->line('common_yes') : $CI->lang->line('common_no')),
		'growing_area_name' => $supplier->growing_area_name,
		'association_date' => $supplier->association_date,
		'ruc' => $supplier->ruc,
		'company_name' => $supplier->company_name,
		'agency_name' => $supplier->agency_name,
		'email' => empty($supplier->email) ? '' : mailto($supplier->email, $supplier->email),
		'phone_number' => $supplier->phone_number,
		'messages' => empty($supplier->phone_number) ? '' : anchor("Messages/view/$supplier->person_id", '<span class="glyphicon glyphicon-phone"></span>',
			array('class'=>"modal-dlg", 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('messages_sms_send'))),
		'edit' => anchor($controller_name."/view/$supplier->person_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>"modal-dlg", 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update')))
		);
}

/*
Get the header for the employees tabular view
*/
function get_employees_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('people.person_id' => $CI->lang->line('common_id')),
		array('dni' => $CI->lang->line('common_dni')),
		array('first_name' => $CI->lang->line('common_first_name')),
		array('last_name' => $CI->lang->line('common_last_name')),
		array('admission_date' => $CI->lang->line('employees_admission_date')),
		array('job_title' => $CI->lang->line('employees_job_title')),
		array('ruc' => $CI->lang->line('employees_ruc')),
		array('contract_type' => $CI->lang->line('employees_contract_type')),
		array('email' => $CI->lang->line('common_email')),
		array('phone_number' => $CI->lang->line('common_phone_number'))
	);

	if($CI->User->has_grant('messages', $CI->session->userdata('person_id')))
	{
		$headers[] = array('messages' => '');
	}

	if($CI->User->has_grant('users', $CI->session->userdata('person_id')))
	{
		$headers[] = array('user' => '');
	}

	return transform_headers($headers);
}

/*
Get the html data row for the employee
*/
function get_employee_data_row($employee)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'people.person_id' => $employee->person_id,
		'dni' => $employee->dni,
		'first_name' => $employee->first_name,
		'last_name' => $employee->last_name,
		'admission_date' => $employee->admission_date,
		'job_title' => $employee->job_title,
		'ruc' => $employee->ruc,
		'contract_type' => $employee->contract_type,
		'email' => empty($employee->email) ? '' : mailto($employee->email, $employee->email),
		'phone_number' => $employee->phone_number,
		'messages' => empty($employee->phone_number) ? '' : anchor("Messages/view/$employee->person_id", '<span class="glyphicon glyphicon-phone"></span>',
			array('class'=>"modal-dlg", 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('messages_sms_send'))),
		'user' => anchor($controller_name."/user_allocation/$employee->person_id", '<span class="glyphicon glyphicon-user"></span>',
			array('class'=>"modal-dlg", 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_user_allocation'))),
		'edit' => anchor($controller_name."/view/$employee->person_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>"modal-dlg", 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update')))
		);
}

/*
Get the header for the uom tabular view
*/
function get_loan_credit_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('id' => $CI->lang->line('loans_credits_id')),
		array('dni' => $CI->lang->line('loans_credits_dni')),
		array('name' => $CI->lang->line('loans_credits_name')),
		array('type' => $CI->lang->line('loans_credits_type')),
		array('motive' => $CI->lang->line('loans_credits_motive_detail')),
		array('date' => $CI->lang->line('loans_credits_date')),
		array('returndate' => $CI->lang->line('loans_credits_returndate')),
		array('amount' => $CI->lang->line('loans_credits_capital_label')),
		array('interest' => $CI->lang->line('loans_credits_interest')),
		array('balance' => $CI->lang->line('loans_credits_balance'))
	);

	$headers[] = array('delete' => '');
	$headers[] = array('pay' => '');
	$headers[] = array('see' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the loan
*/
function get_loan_data_row($loan)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'id' => $loan->loan_id,
		'dni' => $loan->dni,
		'name' => $loan->name,
		'type' => $CI->lang->line($controller_name.'_type_loan'),
		'motive' => $loan->motive,
		'date' => to_date(strtotime($loan->loandate)),
		'returndate' => to_date(strtotime($loan->returndate)),
		'amount' => $loan->amount,
		'interest' => $loan->amt_interest,
		'balance' => $loan->balance,
		'delete' => (($loan->pay_amount > 0) ? '' : anchor($controller_name."/delete_loan/$loan->loan_id", '<span class="glyphicon glyphicon-trash"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_delete_loan'))
		)),
		'pay' => anchor($controller_name."/seepay_loan/$loan->loan_id", '<span class="glyphicon glyphicon-search"></span>',
			array('title'=>$CI->lang->line($controller_name.'_seepay_loan'))
		),
		'see' => anchor($controller_name."/see_loan/$loan->loan_id", '<span class="glyphicon glyphicon-eye-open"></span>',
			array('title'=>$CI->lang->line($controller_name.'_see_payschedule'))
		),
		'edit' => (($loan->pay_amount > 0) ? '' : anchor($controller_name."/view/$loan->loan_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update_loan'))
		)));
}

/*
Gets the html data row for the loan
*/
function get_credit_data_row($credit)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'id' => $credit->credit_id,
		'dni' => $credit->dni,
		'name' => $credit->name,
		'type' => $CI->lang->line($controller_name.'_type_credit'),
		'motive' => $credit->detail,
		'date' => to_date(strtotime($credit->creditdate)),
		'returndate' => to_date(strtotime($credit->returndate)),
		'amount' => $credit->amount,
		'interest' => $credit->amt_interest,
		'balance' => $credit->balance,
		'delete' => (($credit->pay_amount > 0) ? '' : anchor($controller_name."/delete_credit/$credit->credit_id", '<span class="glyphicon glyphicon-trash"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_delete_credit'))
		)),
		'pay' => anchor($controller_name."/seepay_credit/$credit->credit_id", '<span class="glyphicon glyphicon-search"></span>',
			array('title'=>$CI->lang->line($controller_name.'_seepay_credit'))
		),
		'see' => anchor($controller_name."/see_credit/$credit->credit_id", '<span class="glyphicon glyphicon-eye-open"></span>',
			array('title'=>$CI->lang->line($controller_name.'_see_payschedule'))
		),
		'edit' => (($credit->pay_amount > 0) ? '' : anchor($controller_name."/view_credit/$credit->credit_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update_credit'))
		)));
}

/*
Get the header for the uom tabular view
*/
function get_loan_credit_summary_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('dni' => $CI->lang->line('loans_credits_dni')),
		array('name' => $CI->lang->line('loans_credits_name')),
		array('date' => $CI->lang->line('loans_credits_date')),
		array('returndate' => $CI->lang->line('loans_credits_returndate')),
		array('amount' => $CI->lang->line('loans_credits_amount')),
		array('interest' => $CI->lang->line('loans_credits_interest')),
		array('balance' => $CI->lang->line('loans_credits_balance'))
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the loan
*/
function get_loan_summary_data_row($loan)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'dni' => $loan->dni,
		'name' => $loan->name,
		'date' => to_date(strtotime($loan->loandate)),
		'returndate' => to_date(strtotime($loan->returndate)),
		'amount' => $loan->amount,
		'interest' => $loan->amt_interest,
		'balance' => $loan->balance,
		'edit' => (($loan->balance == 0) ? '' : anchor($controller_name."/pay_summary_loan/$loan->dni", '<span class="glyphicon glyphicon-usd"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_pay_loan'))
		)));
}

/*
Gets the html data row for the loan
*/
function get_credit_summary_data_row($credit)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'dni' => $credit->dni,
		'name' => $credit->name,
		'date' => to_date(strtotime($credit->creditdate)),
		'returndate' => to_date(strtotime($credit->returndate)),
		'amount' => $credit->amount,
		'interest' => $credit->amt_interest,
		'balance' => $credit->balance,
		'edit' => (($credit->balance == 0) ? '' : anchor($controller_name."/pay_summary_credit/$credit->dni", '<span class="glyphicon glyphicon-usd"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_pay_credit'))
		)));
}

/*
Get the header for the uom tabular view
*/
function get_payment_loan_credit_manage_table_headers($type)
{
	$CI =& get_instance();

	if($type == "p")
	{
		$headers = array(
			array('id' => $CI->lang->line('loans_credits_id')),
			array('paytype' => $CI->lang->line('loans_credits_paytype')),
			array('paydate' => $CI->lang->line('loans_credits_paydate')),
			array('observations' => $CI->lang->line('loans_credits_observations')),
			array('amount' => $CI->lang->line('loans_credits_amortization')),
			array('interest' => $CI->lang->line('loans_credits_interest')),
			array('capital' => $CI->lang->line('loans_credits_capital')),
			array('amt_interest' => $CI->lang->line('loans_credits_interest_balance')),
			array('balance' => $CI->lang->line('loans_credits_balance')),
		);
	}
	else
	{
		$headers = array(
			array('cuote' => $CI->lang->line('loans_credits_cuote')),
			array('paydate' => $CI->lang->line('loans_credits_paydate')),
			array('capital' => $CI->lang->line('loans_credits_capital')),
			array('amt_interest' => $CI->lang->line('loans_credits_interest_balance')),
			array('amount' => $CI->lang->line('loans_credits_amount'))
		);
	}

	return transform_headers($headers);
}

/*
Gets the html data row for the loan
*/
function get_payment_loan_data_row($type,$loan)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	if($type=="p")
	{
		return array (
			'id' => $loan->payment_loan_id,
			'paytype' => $CI->lang->line('loans_credits_paytype_'.$loan->paytype),
			'paydate' => to_date(strtotime($loan->paydate)),
			'observations' => $loan->observations,
			'amount' => $loan->amortization_capital,
			'interest' => $loan->amortization_interest,
			'capital' => $loan->capital,
			'amt_interest' => $loan->interest,
			'balance' => $loan->balance);
	}
	else
	{
		return array (
			'cuote' => $loan->cuote,
			'paydate' => to_date(strtotime($loan->estimate_paydate)),
			'capital' => $loan->capital,
			'amt_interest' => $loan->amt_interest,
			'amount' => $loan->amount);
	}
}

/*
Gets the html data row for the loan
*/
function get_payment_credit_data_row($type,$credit)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	if($type=="p")
	{
		return array (
			'id' => $credit->payment_credit_id,
			'paytype' => $CI->lang->line('loans_credits_paytype_'.$credit->paytype),
			'paydate' => to_date(strtotime($credit->paydate)),
			'observations' => $credit->observations,
			'amount' => $credit->amortization_capital,
			'interest' => $credit->amortization_interest,
			'capital' => $credit->capital,
			'amt_interest' => $credit->interest,
			'balance' => $credit->balance);
	}
	else
	{
		return array (
			'cuote' => $credit->cuote,
			'paydate' => to_date(strtotime($credit->estimate_paydate)),
			'capital' => $credit->capital,
			'amt_interest' => $credit->amt_interest,
			'amount' => $credit->amount);
	}
}

/*
Get the header for the doctypesequence tabular view
*/
function get_doctypesequence_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('sequence_id' => $CI->lang->line('doctypesequences_sequence_id')),
		array('name' => $CI->lang->line('doctypesequences_name')),
		array('doctype' => $CI->lang->line('doctypesequences_doctype')),
		array('prefix' => $CI->lang->line('doctypesequences_prefix')),
		array('suffix' => $CI->lang->line('doctypesequences_suffix')),
		array('next_sequence' => $CI->lang->line('doctypesequences_next_sequence')),
		array('number_incremental' => $CI->lang->line('doctypesequences_number_incremental')),
		array('is_cashup' => $CI->lang->line('doctypesequences_is_cashup'))
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the doctypesequence
*/
function get_doctypesequence_data_row($doctypesequence)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'sequence_id' => $doctypesequence->sequence_id,
		'name' => $doctypesequence->name,
		'doctype' => $CI->lang->line('doctypesequences_doctype_'.substr($doctypesequence->doctype,0,-1)),
		'prefix' => $doctypesequence->prefix,
		'suffix' => $doctypesequence->suffix,
		'next_sequence' => $doctypesequence->next_sequence,
		'number_incremental' => $doctypesequence->number_incremental,
		'is_cashup' => ($doctypesequence->is_cashup == 0 ? $CI->lang->line('common_no') : $CI->lang->line('common_yes')),
		'edit' => anchor($controller_name."/view/$doctypesequence->sequence_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}

/*
Get the header for the single master tabular view
*/
function get_singlemaster_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('id' => $CI->lang->line('common_id')),
		array('name' => $CI->lang->line('common_name')),
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the single master
*/
function get_singlemaster_data_row($obj)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'id' => $obj->id,
		'name' => $obj->name,
		'edit' => anchor($controller_name."/view/$obj->id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}


/*
Get the header for the model tabular view
*/
function get_delivery_document_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('id' => $CI->lang->line('delivery_document_id_delivery_doocument')),
		array('created' => $CI->lang->line('delivery_document_created')),
                array('code' => $CI->lang->line('delivery_document_code')),
                array('supplier' => $CI->lang->line('delivery_document_supplier')),
                array('period' => $CI->lang->line('delivery_document_period')),
                array('type' => $CI->lang->line('delivery_document_type_item')),
                array('item' => $CI->lang->line('delivery_document_item')),
		array('amount' => $CI->lang->line('delivery_document_amount')),
		array('deposit' => $CI->lang->line('delivery_document_deposit')),
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the model
*/
function get_delivery_document_data_row($model)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'id_delivery_document' => $model->id_delivery_document,
                'created' => date('d-m-Y',strtotime($model->created)),
		'code' => $model->code,
		'supplier' => $model->first_name.' '.$model->last_name,
		'period' => $model->name,
                'type' => $model->types,
                'item' => $model->producto,
                'amount'=> $model->amount_entered,
                'deposit'=>$model->deposit,
                'edit' => anchor($controller_name."/view/$model->id_delivery_document", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg modal-dlg-wide', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}


/*
Get the header for the model tabular view
*/
function get_model_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('model_id' => $CI->lang->line('models_model_id')),
		array('name' => $CI->lang->line('models_name')),
		array('type' => $CI->lang->line('models_type')),
		array('value' => $CI->lang->line('models_value')),
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the model
*/
function get_model_data_row($model)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'model_id' => $model->model_id,
		'name' => $model->name,
		'type' => $model->type,
		'value' => $model->value,
		'edit' => anchor($controller_name."/view/$model->model_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}



/*
Get the header for the model tabular view
*/
function get_analysis_lab_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('id_analysis' => $CI->lang->line('analysis_labs_id')),
		array('created' => $CI->lang->line('analysis_labs_created')),
		array('delivery_document' => $CI->lang->line('analysis_labs_delivery_document')),
		array('supplier' => $CI->lang->line('analysis_labs_supplier')),
                array('sello' => $CI->lang->line('analysis_labs_sello')),
                array('sample' => $CI->lang->line('analysis_labs_muestra')),
                array('humedad' => $CI->lang->line('analysis_labs_humedad')),
                array('entered' => $CI->lang->line('analysis_labs_kilos_entered')),
                array('secos' => $CI->lang->line('analysis_labs_kilos_secos')),
                array('descontados' => $CI->lang->line('analysis_labs_kilos_descontado'))
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the model
*/
function get_analysis_lab_data_row($model)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'id_analysis' => $model->id_analysis_lab,
                'created'=> date('d-m-Y',strtotime($model->created)),
                'delivery_document'=>$model->document_delivery_id,
                'supplier'=>$model-> $model->first_name.' '.$model->last_name,
		'sello' => $model->id_sello,
		'sample' => $model->sample_analysis_lab,
		'humedad' => $model->humedad_estatica,
                'entered'=>$model->kilos_ingresados,
                'secos'=>$model->kilos_secos,
		'edit' => anchor($controller_name."/view/$model->id_analysis_lab", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}


/*
Get the header for the item_type tabular view
*/
function get_item_type_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('item_type_id' => $CI->lang->line('item_types_item_type_id')),
		array('family' => $CI->lang->line('item_types_family')),
		array('name' => $CI->lang->line('item_types_name')),
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the item_type
*/
function get_item_type_data_row($item_type)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'item_type_id' => $item_type->item_type_id,
		'family' => $item_type->family,
		'name' => $item_type->name,
		'edit' => anchor($controller_name."/view/$item_type->item_type_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}

/*
Get the header for the uom tabular view
*/
function get_fees_deposit_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
                array('created' => $CI->lang->line('fees_deposit_created')),
		array('supplier_id' => $CI->lang->line('fees_deposit_supplier')),
		array('period' => $CI->lang->line('fees_deposit_period')),
		array('location' => $CI->lang->line('fees_deposit_location')),
                array('type_item' => $CI->lang->line('fees_deposit_type_item')),
		array('item' => $CI->lang->line('fees_deposit_item')),
		array('fee_deposit' => $CI->lang->line('fees_deposit_fee_deposit')),
		array('input' => $CI->lang->line('fees_deposit_input')),
		array('output' => $CI->lang->line('fees_deposit_output'))		
	);

	return transform_headers($headers);
}
/*
Gets the html data row for the uom
*/
function get_fees_deposit_data_row($fees)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));
        
            return array (
		
		'created' => date('d-m-Y',strtotime($fees->created)),
		'supplier_id' => $fees->first_name.' '.$fees->last_name,
                'period'=> $fees->name,
                'location' => $fees->deposito,
                'type_item'=>$fees->types,
                'item'=>$fees->articulo,
                'fee_deposit'=>$fees->fee_deposit,
                'input'=>$fees->input,
                'output'=>$fees->output,
		'edit' => anchor($controller_name."/view/$fees->id_fee_deposit", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
        
	
}



/*
Get the header for the uom tabular view
*/
function get_uom_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('uom_id' => $CI->lang->line('uoms_uom_id')),
		array('symbol' => $CI->lang->line('uoms_symbol')),
		array('name' => $CI->lang->line('uoms_name')),
		array('magnitude' => $CI->lang->line('uoms_magnitude'))
	);

	return transform_headers($headers);
}


/*
Gets the html data row for the uom
*/
function get_uom_data_row($uom)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'uom_id' => $uom->uom_id,
		'symbol' => $uom->symbol,
		'name' => $uom->name,
		'magnitude' => $uom->magnitude,
		'edit' => anchor($controller_name."/view/$uom->uom_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}

/*
Get the header for the voucher operation tabular view
*/
function get_voucher_operation_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('voucher_operation_id' => $CI->lang->line('voucher_operations_voucher_operation_id')),
		array('voucherdate' => $CI->lang->line('voucher_operations_voucherdate')),
		array('serieno' => $CI->lang->line('voucher_operations_serieno')),
		array('number' => $CI->lang->line('voucher_operations_number')),
		array('dni' => $CI->lang->line('common_dni')),
		array('name' => $CI->lang->line('common_person_name')),
		array('cash_book_id' => $CI->lang->line('voucher_operations_cash_book_id')),
		array('amount' => $CI->lang->line('voucher_operations_amount')),
		array('amount_available' => $CI->lang->line('voucher_operations_amount_available')),
		array('cash' => $CI->lang->line('common_cash')),
		array('bank' => $CI->lang->line('common_bank')),
		array('state' => $CI->lang->line('common_state'))
	);

	$headers[] = array('delete' => '');
	$headers[] = array('pay' => '');
	$headers[] = array('see' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the voucher operation
*/
function get_voucher_operation_data_row($voucher_operation)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'voucher_operation_id' => $voucher_operation->voucher_operation_id,
		'voucherdate' => to_date(strtotime($voucher_operation->voucherdate)),
		'serieno' => $voucher_operation->serieno,
		'number' => $voucher_operation->voucher_operation_number,
		'dni' => $voucher_operation->dni,
		'name' => $voucher_operation->name,
		'cash_book_id' => $voucher_operation->cash_book_name,
		'amount' => $voucher_operation->amount,
		'amount_available' => $voucher_operation->amount_available,
		'cash' => $voucher_operation->cash,
		'bank' => $voucher_operation->bank,
		'state' => $CI->lang->line('voucher_operations_state_'.$voucher_operation->state),
		'delete' => ($voucher_operation->state == 0 ? anchor($controller_name."/confirm_delete/$voucher_operation->voucher_operation_id", '<span class="glyphicon glyphicon-trash"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_delete'))
		) : ''),
		'pay' => ($voucher_operation->amount == $voucher_operation->amount_available ? anchor($controller_name."/payment/$voucher_operation->voucher_operation_id", '<span class="glyphicon glyphicon-usd"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_liquidate'))
		) : ''),
		'see' => anchor($controller_name."/view_detail/$voucher_operation->voucher_operation_id", '<span class="glyphicon glyphicon-eye-open"></span>',
			array('title'=>$CI->lang->line($controller_name.'_see'))
		),
		'edit' => ($voucher_operation->state == 0 ? anchor($controller_name."/view/$voucher_operation->voucher_operation_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		) : ''));
}

/*
Get the header for the quality certificate tabular view
*/
function get_certificate_manage_table_headers($serieno = "01",$voucher_operation_id = FALSE)
{
	$CI =& get_instance();

	if(!$voucher_operation_id)
	{
		$headers = array(
			array('quality_certificate_id' => $CI->lang->line('quality_certificates_quality_certificate_id')),
			array('depositdate' => $CI->lang->line('quality_certificates_depositdate')),
			array('number' => $CI->lang->line('quality_certificates_number')),
			array('dni' => $CI->lang->line('common_dni')),
			array('name' => $CI->lang->line('common_person_name')),
			array('kg_dry' => $CI->lang->line('quality_certificates_kg_dry')),
			array('qq_dry' => $CI->lang->line('quality_certificates_qq_dry')),
			array('location_id' => $CI->lang->line('quality_certificates_location_id')),
			array('rate_profile' => $CI->lang->line('quality_certificates_rate_profile')),
			array('price' => $CI->lang->line('quality_certificates_price')),
			array('amount' => $CI->lang->line('quality_certificates_amount')),
			array('physical_performance' => $CI->lang->line('quality_certificates_physical_performance')),
			array('quality' => $CI->lang->line('quality_certificates_quality')),
			array('voucher_operation_id' => $CI->lang->line('quality_certificates_voucher_operation_allocated'))
		);
	}
	else
	{	
		$headers = array(
			array('quality_certificate_id' => $CI->lang->line('quality_certificates_quality_certificate_id')),
			array('depositdate' => $CI->lang->line('quality_certificates_depositdate')),
			array('number' => $CI->lang->line('quality_certificates_number')),
			array('dni' => $CI->lang->line('common_dni')),
			array('name' => $CI->lang->line('common_person_name')),
			array('kg_dry' => $CI->lang->line('quality_certificates_kg_dry')),
			array('qq_dry' => $CI->lang->line('quality_certificates_qq_dry')),
			array('location_id' => $CI->lang->line('quality_certificates_location_id')),
			array('rate_profile' => $CI->lang->line('quality_certificates_rate_profile')),
			array('price' => $CI->lang->line('quality_certificates_price')),
			array('amount' => $CI->lang->line('quality_certificates_amount')),
			array('physical_performance' => $CI->lang->line('quality_certificates_physical_performance')),
			array('quality' => $CI->lang->line('quality_certificates_quality'))
		);
	}

	if($serieno == "02" && !$voucher_operation_id)
	{
		$headers[] = array('delete' => '');
	}

	return transform_headers($headers);
}

/*
Gets the html data row for the voucher operation
*/
function get_certificate_data_row($serieno = "01",$quality_certificate,$voucher_operation_id = FALSE)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	if(!$voucher_operation_id)
	{
		$voucher_operation = (empty($quality_certificate->voucher_operation_id) ? '' : ($quality_certificate->voucher_state == 0 ? '<span class="allocated">'.$quality_certificate->voucher_operation_number.'</span>' : '<span class="liquidated">'.$quality_certificate->voucher_operation_number.'</span>'));

		return array (
			'quality_certificate_id' => $quality_certificate->quality_certificate_id,
			'depositdate' => to_date(strtotime($quality_certificate->depositdate)),
			'number' => $quality_certificate->certificate_number,
			'dni' => $quality_certificate->dni,
			'name' => $quality_certificate->name,
			'kg_dry' => $quality_certificate->kg_dry,
			'qq_dry' => $quality_certificate->qq_dry,
			'location_id' => $quality_certificate->location_name,
			'rate_profile' => $quality_certificate->rate_profile,
			'price' => $quality_certificate->price,
			'amount' => $quality_certificate->amount,
			'physical_performance' => $quality_certificate->physical_performance,
			'quality' => $quality_certificate->quality,
			'voucher_operation_id' => $voucher_operation,
			'delete' => (($serieno == "02" && empty($quality_certificate->voucher_operation_id)) ? anchor($controller_name."/confirm_delete/$quality_certificate->quality_certificate_id", '<span class="glyphicon glyphicon-trash"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('quality_certificates_delete'))
			) : ''),
			'edit' => ((empty($quality_certificate->voucher_operation_id)) ? anchor($controller_name."/view_certificate/$quality_certificate->quality_certificate_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('quality_certificates_update'))
			) : ''));
	}
	else
	{
		return array (
			'quality_certificate_id' => $quality_certificate->quality_certificate_id,
			'depositdate' => to_date(strtotime($quality_certificate->depositdate)),
			'number' => $quality_certificate->certificate_number,
			'dni' => $quality_certificate->dni,
			'name' => $quality_certificate->name,
			'kg_dry' => $quality_certificate->kg_dry,
			'qq_dry' => $quality_certificate->qq_dry,
			'location_id' => $quality_certificate->location_name,
			'rate_profile' => $quality_certificate->rate_profile,
			'price' => $quality_certificate->price,
			'amount' => $quality_certificate->amount,
			'physical_performance' => $quality_certificate->physical_performance,
			'quality' => $quality_certificate->quality);
	}
}

/*
Get the header for the growing areas tabular view
*/
function get_growing_area_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('growing_area_id' => $CI->lang->line('growing_areas_growing_area_id')),
		array('name' => $CI->lang->line('growing_areas_name')),
		array('district' => $CI->lang->line('growing_areas_district')),
		array('state' => $CI->lang->line('growing_areas_state')),
		array('country' => $CI->lang->line('growing_areas_country'))
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_growing_area_data_row($growing_area)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'growing_area_id' => $growing_area->growing_area_id,
		'name' => $growing_area->name,
		'district' => $growing_area->district,
		'state' => $growing_area->state,
		'country' => $growing_area->country,
		'edit' => anchor($controller_name."/view/$growing_area->growing_area_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}

/*
Get the header for the growing areas tabular view
*/
function get_cash_book_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('cash_book_id' => $CI->lang->line('cash_books_cash_book_id')),
		array('code' => $CI->lang->line('cash_books_code')),
		array('location_name' => $CI->lang->line('cash_books_stock_location_id')),
		array('username' => $CI->lang->line('cash_books_user_id')),
		array('address' => $CI->lang->line('cash_books_address')),
		array('is_cash_general' => $CI->lang->line('cash_books_is_cash_general'))
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_cash_book_data_row($cash_book)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'cash_book_id' => $cash_book->cash_book_id,
		'code' => $cash_book->code,
		'location_name' => $cash_book->location_name,
		'username' => $cash_book->username,
		'address' => $cash_book->address,
		'is_cash_general' => ($cash_book->is_cash_general == 1 ? $CI->lang->line('common_yes') : $CI->lang->line('common_no')),
		'edit' => anchor($controller_name."/view/$cash_book->cash_book_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
			)
		);
}

/*
Get the header for the growing areas tabular view
*/
function get_overall_cash_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('overall_cash_id' => $CI->lang->line('overall_cashs_overall_cash_id')),
		array('opendate' => $CI->lang->line('overall_cashs_opendate')),
		array('income_currency' => $CI->lang->line('overall_cashs_income_currency')),
		array('cost_currency' => $CI->lang->line('overall_cashs_cost_currency')),
		array('balance_currency' => $CI->lang->line('overall_cashs_balance_currency')),
		array('income_usd' => $CI->lang->line('overall_cashs_income_usd')),
		array('cost_usd' => $CI->lang->line('overall_cashs_cost_usd')),
		array('balance_usd' => $CI->lang->line('overall_cashs_balance_usd')),
		array('state' => $CI->lang->line('common_state'))
	);

	$headers[] = array('close' => '');
	$headers[] = array('detail' => '');
	$headers[] = array('print' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_overall_cash_data_row($overall_cash)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'overall_cash_id' => $overall_cash->overall_cash_id,
		'opendate' => to_date(strtotime($overall_cash->opendate)),
		'income_currency' => to_currency($overall_cash->income),
		'cost_currency' => to_currency($overall_cash->cost),
		'balance_currency' => to_currency(($overall_cash->income-$overall_cash->cost)),
		'income_usd' => to_usd($overall_cash->usdincome),
		'cost_usd' => to_usd($overall_cash->usdcost),
		'balance_usd' => to_usd(($overall_cash->usdincome-$overall_cash->usdcost)),
		'state' => ($overall_cash->state==0 ? $CI->lang->line('common_opened') : $CI->lang->line('common_closed')),
		'close' => ($overall_cash->state==0 ? anchor($controller_name."/close/$overall_cash->overall_cash_id", '<span class="glyphicon glyphicon-folder-close"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_close'))
		) : ''),
		'detail' => anchor($controller_name."/detail/$overall_cash->overall_cash_id", '<span class="glyphicon glyphicon-list-alt"></span>',
			array('title'=>$CI->lang->line($controller_name.'_showdetail'))
		),
		'print' => anchor($controller_name."/print_report/$overall_cash->overall_cash_id", '<span class="glyphicon glyphicon-print"></span>',
			array('title'=>$CI->lang->line($controller_name.'_print'))
		),
		'edit' => ''
		);
}

/*
Get the header for the growing areas tabular view
*/
function get_cash_flow_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('overall_cash_id' => $CI->lang->line('overall_cashs_overall_cash_id')),
		array('movementdate' => $CI->lang->line('overall_cashs_movementdate')),
		array('currency' => $CI->lang->line('overall_cashs_currency')),
		array('cash_concept_id' => $CI->lang->line('overall_cashs_cash_concept_id')),
		array('cash_book_id' => $CI->lang->line('overall_cashs_cash_book_id')),
		array('operation_type' => $CI->lang->line('overall_cashs_operation_type')),
		array('description' => $CI->lang->line('overall_cashs_description')),
		array('amount' => $CI->lang->line('overall_cashs_amount')),
		array('reference_id' => $CI->lang->line('overall_cashs_reference_id'))
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_cash_flow_data_row($cash_flow)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'overall_cash_id' => $cash_flow->cash_flow_id,
		'movementdate' => to_datetime(strtotime($cash_flow->movementdate)),
		'currency' => ($cash_flow->currency == CURRENCY ? CURRENCY_LABEL : USDCURRENCY_LABEL),
		'cash_concept_id' => $cash_flow->cash_concept_name,
		'cash_book_id' => $cash_flow->cash_book_name,
		'operation_type' => $cash_flow->movementtype,
		'description' => $cash_flow->description,
		'amount' => ($cash_flow->currency == CURRENCY ? to_currency($cash_flow->amount) : to_usd($cash_flow->amount)),
		'reference_id' => $cash_flow->referenceno 
		);
}

/*
Get the header for the growing areas tabular view
*/
function get_cash_daily_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('cashup_id' => $CI->lang->line('overall_cashs_overall_cash_id')),
		array('movementdate' => $CI->lang->line('overall_cashs_movementdate')),
		array('currency' => $CI->lang->line('overall_cashs_currency')),
		array('cash_concept_id' => $CI->lang->line('overall_cashs_cash_concept_id')),
		array('cash_book_id' => $CI->lang->line('overall_cashs_cash_book_id')),
		array('operation_type' => $CI->lang->line('overall_cashs_operation_type')),
		array('description' => $CI->lang->line('overall_cashs_description')),
		array('amount' => $CI->lang->line('overall_cashs_amount')),
		array('reference_id' => $CI->lang->line('overall_cashs_reference_id'))
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_cash_daily_data_row($cash_daily)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'overall_cash_id' => $cash_daily->cash_daily_id,
		'movementdate' => to_datetime(strtotime($cash_daily->movementdate)),
		'currency' => ($cash_daily->currency == CURRENCY ? CURRENCY_LABEL : USDCURRENCY_LABEL),
		'cash_concept_id' => $cash_daily->cash_concept_name,
		'cash_book_id' => $cash_daily->cash_book_name,
		'operation_type' => $cash_daily->movementtype,
		'description' => $cash_daily->description,
		'amount' => ($cash_daily->currency == CURRENCY ? to_currency($cash_daily->amount) : to_usd($cash_daily->amount)),
		'reference_id' => $cash_daily->referenceno 
		);
}

/*
Get the header for the growing areas tabular view
*/
function get_bank_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('bank_id' => $CI->lang->line('banks_bank_id')),
		array('ruc' => $CI->lang->line('banks_ruc')),
		array('name' => $CI->lang->line('banks_name')),
		array('account_type' => $CI->lang->line('banks_account_type')),
		array('account_number' => $CI->lang->line('banks_account_number'))
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_bank_data_row($bank)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'bank_id' => $bank->bank_id,
		'ruc' => $bank->ruc,
		'name' => $bank->name,
		'account_type' => $bank->currency,
		'account_number' => $bank->account_number,
		'edit' => anchor($controller_name."/view_bank/$bank->bank_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		)
		);
}

/*
Get the header for the growing areas tabular view
*/
function get_income_manage_table_headers($cashmovements = 0)
{
	$CI =& get_instance();

	if($cashmovements == 0)
	{
		$headers = array(
			array('income_id' => $CI->lang->line('incomes_income_id')),
			array('documentdate' => $CI->lang->line('incomes_documentdate')),
			array('documentno' => $CI->lang->line('incomes_documentno')),
			array('name' => $CI->lang->line('common_person_name')),
			array('bank' => $CI->lang->line('overall_cashs_financialentity')),
			array('detail' => $CI->lang->line('incomes_detail')),
			array('cash_currency' => $CI->lang->line('incomes_cash_currency')),
			array('bank_currency' => $CI->lang->line('incomes_bank_currency')),
			array('cash_usd' => $CI->lang->line('incomes_cash_usd')),
			array('bank_usd' => $CI->lang->line('incomes_bank_usd'))
		);
	}
	else
	{
		$headers = array(
			array('income_id' => $CI->lang->line('incomes_income_id')),
			array('documentdate' => $CI->lang->line('incomes_documentdate')),
			array('documentno' => $CI->lang->line('incomes_documentno')),
			array('name' => $CI->lang->line('common_person_name')),
			array('cash_concept' => $CI->lang->line('incomes_cash_concept_id')),
			array('cash_subconcept' => $CI->lang->line('incomes_cash_subconcept_id')),
			array('detail' => $CI->lang->line('incomes_detail')),
			array('cash' => $CI->lang->line('common_cash')),
			array('bank' => $CI->lang->line('common_bank'))
		);
	}

	$headers[] = array('delete' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_income_data_row($income,$cashmovements = 0)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	if($cashmovements == 0)
	{
		return array (
			'income_id' => $income->income_id,
			'documentdate' => to_datetime(strtotime($income->documentdate)),
			'documentno' => $income->documentno,
			'name' => $income->name,
			'bank' => $income->bank,
			'detail' => $income->detail,
			'cash_currency' => to_currency($income->cash_amount),
			'bank_currency' => to_currency($income->check_amount),
			'cash_usd' => to_usd($income->cash_usdamount),
			'bank_usd' => to_usd($income->check_usdamount),
			'delete' => ($income->readonly == 0 ? anchor($controller_name."/delete_income/$income->income_id", '<span class="glyphicon glyphicon-trash"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('incomes_delete'))
			) : ''),
			'edit' => ($income->readonly == 0 ? anchor($controller_name."/view_income/$income->income_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('incomes_update'))
			) : '')
			);
	}
	else
	{
		return array (
			'income_id' => $income->income_id,
			'documentdate' => to_datetime(strtotime($income->documentdate)),
			'documentno' => $income->documentno,
			'name' => $income->name,
			'cash_concept' => $income->cash_concept,
			'cash_subconcept' => $income->cash_subconcept,
			'detail' => $income->detail,
			'cash' => to_currency($income->cash_amount),
			'bank' => to_currency($income->check_amount),
			'delete' => ($income->readonly == 0 ? anchor($controller_name."/delete_income/$income->income_id", '<span class="glyphicon glyphicon-trash"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('incomes_delete'))
			) : ''),
			'edit' => ($income->readonly == 0 ? anchor($controller_name."/view_income/$income->income_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('incomes_update'))
			) : '')
			);
	}
}

/*
Get the header for the growing areas tabular view
*/
function get_cost_manage_table_headers($cashmovement = 0)
{
	$CI =& get_instance();

	if($cashmovement == 0)
	{
		$headers = array(
			array('cost_id' => $CI->lang->line('costs_cost_id')),
			array('documentdate' => $CI->lang->line('costs_documentdate')),
			array('documentno' => $CI->lang->line('costs_documentno')),
			array('name' => $CI->lang->line('common_person_name')),
			array('concept' => $CI->lang->line('costs_cash_concept_id')),
			array('detail' => $CI->lang->line('costs_detail')),
			array('cash_currency' => $CI->lang->line('costs_cash_currency')),
			array('bank_currency' => $CI->lang->line('costs_bank_currency'))
		);
	}
	else
	{
		$headers = array(
			array('cost_id' => $CI->lang->line('costs_cost_id')),
			array('documentdate' => $CI->lang->line('costs_documentdate')),
			array('documentno' => $CI->lang->line('costs_documentno')),
			array('name' => $CI->lang->line('common_person_name')),
			array('concept' => $CI->lang->line('costs_cash_concept_id')),
			array('subconcept' => $CI->lang->line('costs_cash_subconcept_id')),
			array('detail' => $CI->lang->line('costs_detail')),
			array('cash_currency' => $CI->lang->line('costs_cash_currency')),
			array('bank_currency' => $CI->lang->line('costs_bank_currency'))
		);
	}

	

	$headers[] = array('delete' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_cost_data_row($cost,$cashmovement = 0)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	if($cashmovement == 0)
	{
		return array (
			'cost_id' => $cost->cost_id,
			'documentdate' => to_datetime(strtotime($cost->documentdate)),
			'documentno' => $cost->documentno,
			'name' => $cost->name,
			'concept' => $cost->cash_subconcept_name,
			'detail' => $cost->detail,
			'cash_currency' => to_currency($cost->cash_amount),
			'bank_currency' => to_currency($cost->check_amount),
			'delete' => ($cost->readonly == 0 ? anchor($controller_name."/delete_cost/$cost->cost_id", '<span class="glyphicon glyphicon-trash"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('costs_delete'))
			) : ''),
			'edit' => ($cost->readonly == 0 ? anchor($controller_name."/view_cost/$cost->cost_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('costs_update'))
			) : '')
			);
	}
	else
	{
		return array (
		'cost_id' => $cost->cost_id,
		'documentdate' => to_datetime(strtotime($cost->documentdate)),
		'documentno' => $cost->documentno,
		'name' => $cost->name,
		'concept' => $cost->cash_concept_name,
		'subconcept' => $cost->cash_subconcept_name,
		'detail' => $cost->detail,
		'cash_currency' => to_currency($cost->cash_amount),
		'bank_currency' => to_currency($cost->check_amount),
		'delete' => ($cost->readonly == 0 ? anchor($controller_name."/delete_cost/$cost->cost_id", '<span class="glyphicon glyphicon-trash"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('costs_delete'))
		) : ''),
		'edit' => ($cost->readonly == 0 ? anchor($controller_name."/view_cost/$cost->cost_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('costs_update'))
		) : '')
		);
	}
}

/*
Get the header for the growing areas tabular view
*/
function get_expense_manage_table_headers($cashmovement = 0)
{
	$CI =& get_instance();

	/*if($cashmovement == 0)
	{
		$headers = array(
			array('expense_id' => $CI->lang->line('expenses_expense_id')),
			array('documentdate' => $CI->lang->line('expenses_documentdate')),
			array('documentno' => $CI->lang->line('expenses_documentno')),
			array('name' => $CI->lang->line('common_person_name')),
			array('concept' => $CI->lang->line('expenses_cash_concept_id')),
			array('detail' => $CI->lang->line('expenses_detail')),
			array('cash_currency' => $CI->lang->line('expenses_cash_currency')),
			array('bank_currency' => $CI->lang->line('expenses_bank_currency'))
		);
	}
	else
	{
		$headers = array(
			array('expense_id' => $CI->lang->line('expenses_expense_id')),
			array('documentdate' => $CI->lang->line('expenses_documentdate')),
			array('documentno' => $CI->lang->line('expenses_documentno')),
			array('name' => $CI->lang->line('common_person_name')),
			array('concept' => $CI->lang->line('expenses_cash_concept_id')),
			array('subconcept' => $CI->lang->line('expenses_cash_subconcept_id')),
			array('detail' => $CI->lang->line('expenses_detail')),
			array('cash_currency' => $CI->lang->line('expenses_cash_currency')),
			array('bank_currency' => $CI->lang->line('expenses_bank_currency'))
		);
	}*/

	$headers = array(
		array('expense_id' => $CI->lang->line('expenses_expense_id')),
		array('documentdate' => $CI->lang->line('expenses_documentdate')),
		array('documentno' => $CI->lang->line('expenses_documentno')),
		array('name' => $CI->lang->line('common_person_name')),
		array('concept' => $CI->lang->line('expenses_cash_concept_id')),
		array('detail' => $CI->lang->line('expenses_detail')),
		array('doctype' => $CI->lang->line('expenses_doctype')),
		array('docnumber' => $CI->lang->line('expenses_docnumber')),
		array('cash_currency' => $CI->lang->line('expenses_cash_currency')),
		array('bank_currency' => $CI->lang->line('expenses_bank_currency'))
	);

	$headers[] = array('delete' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_expense_data_row($expense,$cashmovement = 0)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	/*if($cashmovement == 0)
	{
		return array (
			'expense_id' => $expense->expense_id,
			'documentdate' => to_datetime(strtotime($expense->documentdate)),
			'documentno' => $expense->documentno,
			'name' => $expense->name,
			'concept' => $expense->cash_subconcept_name,
			'detail' => $expense->detail,
			'cash_currency' => to_currency($expense->cash_amount),
			'bank_currency' => to_currency($expense->check_amount),
			'delete' => ($expense->readonly == 0 ? anchor($controller_name."/delete_expense/$expense->expense_id", '<span class="glyphicon glyphicon-trash"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('expenses_delete'))
			) : ''),
			'edit' => ($expense->readonly == 0 ? anchor($controller_name."/view_expense/$expense->expense_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('expenses_update'))
			) : '')
			);
	}
	else
	{
		return array (
			'expense_id' => $expense->expense_id,
			'documentdate' => to_datetime(strtotime($expense->documentdate)),
			'documentno' => $expense->documentno,
			'name' => $expense->name,
			'concept' => $expense->cash_concept_name,
			'subconcept' => $expense->cash_subconcept_name,
			'detail' => $expense->detail,
			'cash_currency' => to_currency($expense->cash_amount),
			'bank_currency' => to_currency($expense->check_amount),
			'delete' => ($expense->readonly == 0 ? anchor($controller_name."/delete_expense/$expense->expense_id", '<span class="glyphicon glyphicon-trash"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('expenses_delete'))
			) : ''),
			'edit' => ($expense->readonly == 0 ? anchor($controller_name."/view_expense/$expense->expense_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('expenses_update'))
			) : '')
			);
	}*/
	return array (
		'expense_id' => $expense->expense_id,
		'documentdate' => to_datetime(strtotime($expense->documentdate)),
		'documentno' => $expense->documentno,
		'name' => $expense->name,
		'concept' => $expense->cash_concept_name,
		'detail' => $expense->detail,
		'doctype' => $expense->doctype,
		'docnumber' => $expense->docnumber,
		'cash_currency' => to_currency($expense->cash_amount),
		'bank_currency' => to_currency($expense->check_amount),
		'delete' => ($expense->readonly == 0 ? anchor($controller_name."/delete_expense/$expense->expense_id", '<span class="glyphicon glyphicon-trash"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('expenses_delete'))
		) : ''),
		'edit' => ($expense->readonly == 0 ? anchor($controller_name."/view_expense/$expense->expense_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line('expenses_update'))
		) : '')
	);
}

/*
Get the header for the growing areas tabular view
*/
function get_voucher_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('voucher_id' => $CI->lang->line('vouchers_voucher_id')),
		array('number' => $CI->lang->line('vouchers_number')),
		array('voucherdate' => $CI->lang->line('vouchers_voucherdate')),
		array('dni' => $CI->lang->line('common_dni')),
		array('name' => $CI->lang->line('common_person_name')),
		array('detail' => $CI->lang->line('vouchers_detail')),
		array('amount' => $CI->lang->line('vouchers_total_amount')),
		array('rendered' => $CI->lang->line('vouchers_rendered')),
		array('bank' => $CI->lang->line('common_bank')."s")
	);

	$headers[] = array('delete' => '');
	$headers[] = array('pay' => '');
	$headers[] = array('see' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_voucher_data_row($voucher)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'voucher_id' => $voucher->voucher_id,
		'number' => $voucher->voucher_number,
		'voucherdate' => to_date(strtotime($voucher->voucherdate)),
		'dni' => $voucher->dni,
		'name' => $voucher->name,
		'detail' => $voucher->detail,
		'amount' => to_currency($voucher->amount),
		'rendered' => to_currency($voucher->rendered),
		'bank' => $voucher->trx_number,
		'delete' => ($voucher->rendered == 0 ? anchor($controller_name."/delete_voucher/$voucher->voucher_id", '<span class="glyphicon glyphicon-trash"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_delete'))
			) : ''),
		'pay' => anchor($controller_name."/pay/$voucher->voucher_id", '<span class="glyphicon glyphicon-usd"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_pay'))
			),
		'see' => anchor($controller_name."/see/$voucher->voucher_id", '<span class="glyphicon glyphicon-eye-open"></span>',
				array('title'=>$CI->lang->line($controller_name.'_see'))
			),
		'edit' => ($voucher->rendered == 0 ? anchor($controller_name."/view/$voucher->voucher_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
			) : '')
		);
}

/*
Get the header for the growing areas tabular view
*/
function get_ticketsale_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('ticketsale_id' => $CI->lang->line('ticketsales_ticketsale_id')),
		array('documentdate' => $CI->lang->line('ticketsales_documentdate')),
		array('serieno' => $CI->lang->line('ticketsales_serieno')),
		array('cash_book_id' => $CI->lang->line('ticketsales_cash_book_id')),
		array('name' => $CI->lang->line('common_person_name')),
		array('cash' => $CI->lang->line('common_cash')),
		array('bank' => $CI->lang->line('common_bank'))
	);

	$headers[] = array('see' => '');
	$headers[] = array('delete' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_ticketsale_data_row($ticketsale)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'ticketsale_id' => $ticketsale->ticketsale_id,
		'documentdate' => to_date(strtotime($ticketsale->documentdate)),
		'serieno' => $ticketsale->serieno."-".$ticketsale->documentno,
		'cash_book_id' => $ticketsale->cash_book,
		'name' => $ticketsale->name,
		'cash' => to_currency($ticketsale->cash),
		'bank' => to_currency($ticketsale->bank),
		'see' => anchor($controller_name."/see/$ticketsale->ticketsale_id", '<span class="glyphicon glyphicon-list-alt"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_print'), 'title'=>$CI->lang->line($controller_name.'_see'))
			),
		'delete' => ($ticketsale->readonly == 0 ? anchor($controller_name."/delete_ticketsale/$ticketsale->ticketsale_id", '<span class="glyphicon glyphicon-trash"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_delete'))
			) : ''),
		'edit' => ($ticketsale->readonly == 0 ? anchor($controller_name."/view/$ticketsale->ticketsale_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
			) : '')
		);
}

/*
Get the header for the growing areas tabular view
*/
function get_invoice_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('invoice_id' => $CI->lang->line('invoices_invoice_id')),
		array('documentdate' => $CI->lang->line('invoices_documentdate')),
		array('serieno' => $CI->lang->line('invoices_serieno')),
		array('cash_book_id' => $CI->lang->line('invoices_cash_book_id')),
		array('ruc' => $CI->lang->line('common_ruc')),
		array('name' => $CI->lang->line('common_person_name')),
		array('cash' => $CI->lang->line('common_cash')),
		array('bank' => $CI->lang->line('common_bank'))
	);

	$headers[] = array('see' => '');
	$headers[] = array('delete' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_invoice_data_row($invoice)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'invoice_id' => $invoice->invoice_id,
		'documentdate' => to_date(strtotime($invoice->documentdate)),
		'serieno' => $invoice->serieno,
		'cash_book_id' => $invoice->cash_book,
		'ruc' => $invoice->ruc,
		'name' => $invoice->name,
		'cash' => to_currency($invoice->cash),
		'bank' => to_currency($invoice->bank),
		'see' => anchor($controller_name."/see/$invoice->invoice_id", '<span class="glyphicon glyphicon-list-alt"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_print'), 'title'=>$CI->lang->line($controller_name.'_see'))
			),
		'delete' => ($invoice->readonly == 0 ? anchor($controller_name."/delete_invoice/$invoice->invoice_id", '<span class="glyphicon glyphicon-trash"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_delete'))
			) : ''),
		'edit' => ($invoice->readonly == 0 ? anchor($controller_name."/view/$invoice->invoice_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
			) : '')
		);
}

/*
Get the header for the growing areas tabular view
*/
function get_adjustnote_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('adjustnote_id' => $CI->lang->line('adjustnotes_adjustnote_id')),
		array('documentdate' => $CI->lang->line('adjustnotes_documentdate')),
		array('documentno' => $CI->lang->line('adjustnotes_documentno')),
		array('cash_book_id' => $CI->lang->line('adjustnotes_cash_book_id')),
		array('name' => $CI->lang->line('common_person_name')),
		array('cash_concept_id' => $CI->lang->line('adjustnotes_cash_concept_id')),
		array('description' => $CI->lang->line('adjustnotes_description')),
		array('cash' => $CI->lang->line('common_cash')),
		array('bank' => $CI->lang->line('common_bank'))
	);

	$headers[] = array('delete' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_adjustnote_data_row($adjustnote)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'adjustnote_id' => $adjustnote->adjustnote_id,
		'documentdate' => to_datetime(strtotime($adjustnote->documentdate)),
		'documentno' => $adjustnote->documentno,
		'cash_book_id' => $adjustnote->cash_book,
		'name' => $adjustnote->name,
		'cash_concept_id' => $adjustnote->cash_concept,
		'description' => $adjustnote->description,
		'cash' => to_currency($adjustnote->cash),
		'bank' => to_currency($adjustnote->bank),
		'delete' => ($adjustnote->readonly == 0 ? anchor($controller_name."/delete_adjustnote/$adjustnote->adjustnote_id", '<span class="glyphicon glyphicon-trash"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_delete'))
			) : ''),
		'edit' => ($adjustnote->readonly == 0 ? anchor($controller_name."/view/$adjustnote->adjustnote_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
			) : '')
		);
}

/*
Get the header for the growing areas tabular view
*/
function get_creditnote_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('creditnote_id' => $CI->lang->line('creditnotes_creditnote_id')),
		array('documentdate' => $CI->lang->line('creditnotes_documentdate')),
		array('documentno' => $CI->lang->line('creditnotes_documentno')),
		array('cash_book_id' => $CI->lang->line('creditnotes_cash_book_id')),
		array('name' => $CI->lang->line('common_person_name')),
		array('description' => $CI->lang->line('creditnotes_description')),
		array('cash' => $CI->lang->line('common_cash')),
		array('bank' => $CI->lang->line('common_bank'))
	);

	$headers[] = array('delete' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_creditnote_data_row($creditnote)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'creditnote_id' => $creditnote->creditnote_id,
		'documentdate' => to_datetime(strtotime($creditnote->documentdate)),
		'documentno' => $creditnote->documentno,
		'cash_book_id' => $creditnote->cash_book,
		'name' => $creditnote->name,
		'description' => $creditnote->description,
		'cash' => to_currency($creditnote->cash),
		'bank' => to_currency($creditnote->bank),
		'delete' => ($creditnote->readonly == 0 ? anchor($controller_name."/delete_creditnote/$creditnote->creditnote_id", '<span class="glyphicon glyphicon-trash"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_delete'))
			) : ''),
		'edit' => ($creditnote->readonly == 0 ? anchor($controller_name."/view/$creditnote->creditnote_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
			) : '')
		);
}

/*
Get the header for the uom tabular view
*/
function get_payment_voucher_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('payment_voucher_id' => $CI->lang->line('vouchers_voucher_id')),
		array('paydate' => $CI->lang->line('vouchers_paydate')),
		array('observations' => $CI->lang->line('vouchers_observations')),
		array('amount' => $CI->lang->line('vouchers_amount'))
	);

	return transform_headers($headers);
}

/*
Get the header for the uom tabular view
*/
function get_payment_voucher_data_row($payment)
{
	$CI =& get_instance();

	return array(
		'payment_voucher_id' => $payment->payment_voucher_id,
		'paydate' => to_date(strtotime($payment->paydate)),
		'observations' => $payment->observations,
		'amount' => to_currency($payment->amount)
	);
}


/*
Get the header for the growing areas tabular view
*/
function get_cash_concept_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('cash_concept_id' => $CI->lang->line('cash_concepts_cash_concept_id')),
		array('code' => $CI->lang->line('cash_concepts_code')),
		array('name' => $CI->lang->line('cash_concepts_name')),
		array('concept_type' => $CI->lang->line('cash_concepts_concept_type')),
		array('description' => $CI->lang->line('cash_concepts_description')),
		array('is_cash_general_used' => $CI->lang->line('cash_concepts_is_cash_general_used'))
	);

	$headers[] = array('see' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_cash_concept_data_row($cash_concept)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'cash_concept_id' => $cash_concept->cash_concept_id,
		'code' => $cash_concept->code,
		'name' => $cash_concept->name,
		'concept_type' => ($cash_concept->concept_type=="1" ? $CI->lang->line($controller_name.'_income') : ($cash_concept->concept_type=="2" ? $CI->lang->line($controller_name.'_cost') : ($cash_concept->concept_type=="3" ? $CI->lang->line($controller_name.'_expense') : $CI->lang->line($controller_name.'_notdefined')))),
		'description' => $cash_concept->description,
		'is_cash_general_used' => ($cash_concept->is_cash_general_used == 1 ? $CI->lang->line('common_yes') : $CI->lang->line('common_no')),
		'edit' => ($cash_concept->concept_type=="1" ? anchor($controller_name."/view/$cash_concept->cash_concept_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
			) : ($cash_concept->concept_type=="2" ? anchor($controller_name."/view_cost/$cash_concept->cash_concept_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update_cost'))
			) : anchor($controller_name."/view_expense/$cash_concept->cash_concept_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update_expense'))
			))),
		'see' => ($cash_concept->concept_type=="1" ? anchor($controller_name."/subconcept/$cash_concept->cash_concept_id", '<span class="glyphicon glyphicon-list-alt"></span>',
				array('title'=>$CI->lang->line($controller_name.'_update'))
			) : ($cash_concept->concept_type=="2" ? anchor($controller_name."/subconcept/$cash_concept->cash_concept_id", '<span class="glyphicon glyphicon-list-alt"></span>',
				array('title'=>$CI->lang->line($controller_name.'_update_cost'))
			) : anchor($controller_name."/subconcept/$cash_concept->cash_concept_id", '<span class="glyphicon glyphicon-list-alt"></span>',
				array('title'=>$CI->lang->line($controller_name.'_update_expense'))
			)))
		);
}

/*
Get the header for the growing areas tabular view
*/
function get_cash_concept_parent_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('cash_concept_id' => $CI->lang->line('cash_concepts_cash_concept_id')),
		array('code' => $CI->lang->line('cash_concepts_code')),
		array('name' => $CI->lang->line('cash_concepts_name')),
		array('description' => $CI->lang->line('cash_concepts_description')),
		array('document_sequence' => $CI->lang->line('cash_concepts_document_sequence')),
		array('cash_concept_parent_name' => $CI->lang->line('cash_concepts_cash_concept_parent_id')),
		array('is_summary' => $CI->lang->line('cash_concepts_summary'))
	);

	return transform_headers($headers);
}

/*
Gets the html data row for the growing areas
*/
function get_cash_concept_parent_data_row($cash_concept)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'cash_concept_id' => $cash_concept->cash_concept_id,
		'code' => $cash_concept->code,
		'name' => $cash_concept->name,
		'description' => $cash_concept->description,		
		'document_sequence' => $cash_concept->document_sequence,
		'cash_concept_parent_name' => $cash_concept->cash_concept_parent_name,
		'is_summary' => ($cash_concept->is_summary==1 ? $CI->lang->line('common_yes') : $CI->lang->line('common_no')),
		'edit' => ($cash_concept->concept_type=="1" ? anchor($controller_name."/view_subconcept/$cash_concept->parent_id/$cash_concept->cash_concept_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
			) : ($cash_concept->concept_type=="2" ? anchor($controller_name."/view_subconcept/$cash_concept->parent_id/$cash_concept->cash_concept_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update_cost'))
			) : anchor($controller_name."/view_subconcept/$cash_concept->parent_id/$cash_concept->cash_concept_id", '<span class="glyphicon glyphicon-edit"></span>',
				array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update_expense'))
			)))
		);
}

/*
Get the header for the items tabular view
*/
function get_items_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('items.item_id' => $CI->lang->line('common_id')),
		array('item_number' => $CI->lang->line('items_item_number')),
		array('name' => $CI->lang->line('items_name')),
		array('uom' => $CI->lang->line('items_uom')),
		array('quantity' => $CI->lang->line('items_quantity')),
		array('unit_price' => $CI->lang->line('items_unit_price')),
		array('mark' => $CI->lang->line('items_mark')),
		array('category' => $CI->lang->line('items_category')),
		array('cost_price' => $CI->lang->line('items_cost_price')),
		array('tax_percents' => $CI->lang->line('items_tax_percents'), 'sortable' => FALSE),
		array('item_pic' => $CI->lang->line('items_image'), 'sortable' => FALSE),
		array('inventory' => ''),
		array('stock' => ''),
                array('convert' => '')
	);

	return transform_headers($headers);
}

/*
Get the html data row for the item
*/
function get_item_data_row($item)
{
	$CI =& get_instance();
	$item_tax_info = $CI->Item_taxes->get_info($item->item_id);
	$tax_percents = '';
	foreach($item_tax_info as $tax_info)
	{
		$tax_percents .= to_tax_decimals($tax_info['percent']) . '%, ';
	}
	// remove ', ' from last item
	$tax_percents = substr($tax_percents, 0, -2);
	$controller_name = strtolower(get_class($CI));

	$image = NULL;
	if($item->pic_filename != '')
	{
		$ext = pathinfo($item->pic_filename, PATHINFO_EXTENSION);
		if($ext == '')
		{
			// legacy
			$images = glob('./uploads/item_pics/' . $item->pic_filename . '.*');
		}
		else
		{
			// preferred
			$images = glob('./uploads/item_pics/' . $item->pic_filename);
		}

		if(sizeof($images) > 0)
		{
			$image .= '<a class="rollover" href="'. base_url($images[0]) .'"><img src="'.site_url('items/pic_thumb/' . pathinfo($images[0], PATHINFO_BASENAME)) . '"></a>';
		}
	}

	if ($CI->config->item('multi_pack_enabled') == '1')
	{
		$item->name .= NAME_SEPARATOR . $item->pack_name;
	}

	return array (
		'items.item_id' => $item->item_id,
		'item_number' => $item->item_number,
		'name' => $item->name,
		'uom' => $item->uom,
		'category' => $item->category,
		'mark' => $item->mark,
		'cost_price' => to_currency($item->cost_price),
		'unit_price' => to_currency($item->unit_price),
		'quantity' => to_quantity_decimals($item->quantity),
		'tax_percents' => !$tax_percents ? '-' : $tax_percents,
		'item_pic' => $image,
		'inventory' => anchor($controller_name."/inventory/$item->item_id", '<span class="glyphicon glyphicon-pushpin"></span>',
			array('class' => 'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title' => $CI->lang->line($controller_name.'_count'))
		),
		'stock' => anchor($controller_name."/count_details/$item->item_id", '<span class="glyphicon glyphicon-list-alt"></span>',
			array('class' => 'modal-dlg', 'title' => $CI->lang->line($controller_name.'_details_count'))
		),
                'convert' => anchor($controller_name."/convert/$item->item_id", '<span class="glyphicon glyphicon-random"></span>',
			array('class' => 'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title' => $CI->lang->line('convert_uom_new'))
		),
		'edit' => anchor($controller_name."/view/$item->item_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class' => 'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title' => $CI->lang->line($controller_name.'_update'))
		));
}


/*
Get the header for the giftcard tabular view
*/
function get_giftcards_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('giftcard_id' => $CI->lang->line('common_id')),
		array('last_name' => $CI->lang->line('common_last_name')),
		array('first_name' => $CI->lang->line('common_first_name')),
		array('giftcard_number' => $CI->lang->line('giftcards_giftcard_number')),
		array('value' => $CI->lang->line('giftcards_card_value'))
	);

	return transform_headers($headers);
}

/*
Get the html data row for the giftcard
*/
function get_giftcard_data_row($giftcard)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));

	return array (
		'giftcard_id' => $giftcard->giftcard_id,
		'last_name' => $giftcard->last_name,
		'first_name' => $giftcard->first_name,
		'giftcard_number' => $giftcard->giftcard_number,
		'value' => to_currency($giftcard->value),
		'edit' => anchor($controller_name."/view/$giftcard->giftcard_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}


/*
Get the header for the taxes tabular view
*/
function get_taxes_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('tax_code' => $CI->lang->line('taxes_tax_code')),
		array('tax_code_name' => $CI->lang->line('taxes_tax_code_name')),
		array('tax_code_type_name' => $CI->lang->line('taxes_tax_code_type')),
		array('tax_rate' => $CI->lang->line('taxes_tax_rate')),
		array('rounding_code_name' => $CI->lang->line('taxes_rounding_code')),
		array('city' => $CI->lang->line('common_city')),
		array('state' => $CI->lang->line('common_state'))
	);

	return transform_headers($headers);
}

/*
Get the html data row for the tax
*/
function get_tax_data_row($tax_code_row)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));

	return array (
		'tax_code' => $tax_code_row->tax_code,
		'tax_code_name' => $tax_code_row->tax_code_name,
		'tax_code_type' => $tax_code_row->tax_code_type,
		'tax_rate' => $tax_code_row->tax_rate,
		'rounding_code' =>$tax_code_row->rounding_code,
		'tax_code_type_name' => $CI->Tax->get_tax_code_type_name($tax_code_row->tax_code_type),
		'rounding_code_name' => Rounding_mode::get_rounding_code_name($tax_code_row->rounding_code),
		'city' => $tax_code_row->city,
		'state' => $tax_code_row->state,
		'edit' => anchor($controller_name."/view/$tax_code_row->tax_code", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}


/*
Get the header for the item kits tabular view
*/
function get_item_kits_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('item_kit_id' => $CI->lang->line('item_kits_kit')),
		array('name' => $CI->lang->line('item_kits_name')),
		array('description' => $CI->lang->line('item_kits_description')),
		array('total_cost_price' => $CI->lang->line('items_cost_price'), 'sortable' => FALSE),
		array('total_unit_price' => $CI->lang->line('items_unit_price'), 'sortable' => FALSE)
	);

	return transform_headers($headers);
}

/*
Get the html data row for the item kit
*/
function get_item_kit_data_row($item_kit)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));

	return array (
		'item_kit_id' => $item_kit->item_kit_id,
		'name' => $item_kit->name,
		'description' => $item_kit->description,
		'total_cost_price' => to_currency($item_kit->total_cost_price),
		'total_unit_price' => to_currency($item_kit->total_unit_price),
		'edit' => anchor($controller_name."/view/$item_kit->item_kit_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		));
}

/*
Get the expenses payments summary
*/
function get_expenses_manage_payments_summary($payments, $expenses)
{
	$CI =& get_instance();
	$table = '<div id="report_summary">';

	foreach($payments as $key=>$payment)
	{
		$amount = $payment['amount'];
		$table .= '<div class="summary_row">' . $payment['payment_type'] . ': ' . to_currency($amount) . '</div>';
	}
	$table .= '</div>';

	return $table;
}

/*
Get the header for the cashup tabular view
*/
function get_cashups_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('cashup_id' => $CI->lang->line('cashups_id')),
		array('cash_book_id' => $CI->lang->line('cashups_cash_book_id')),
		array('open_employee_id' => $CI->lang->line('cashups_open_employee')),
		array('open_date' => $CI->lang->line('cashups_opened_date')),
		array('income' => $CI->lang->line('cashups_income')),
		array('cost_cash' => $CI->lang->line('cashups_cost_cash')),
		array('cost_bank' => $CI->lang->line('cashups_cost_bank')),
		array('expense' => $CI->lang->line('cashups_expense')),
		array('balance' => $CI->lang->line('cashups_balance')),
		array('state' => $CI->lang->line('common_state')),
		array('close_date' => $CI->lang->line('cashups_closed_date'))
	);

	$headers[] = array('close' => '');
	$headers[] = array('detail' => '');
	$headers[] = array('print' => '');

	return transform_headers($headers);
}

/*
Gets the html data row for the cashups
*/
function get_cash_up_data_row($cash_up)
{
	$CI =& get_instance();

	$controller_name = strtolower(get_class($CI));

	return array (
		'cashup_id' => $cash_up->cashup_id,
		'cash_book_id' => $cash_up->cash_book,
		'open_employee_id' => $cash_up->open_first_name . ' ' . $cash_up->open_last_name,
		'open_date' => to_datetime(strtotime($cash_up->open_date)),
		'income' => to_currency($cash_up->income),
		'cost_cash' => to_currency($cash_up->cost_cash),
		'cost_bank' => to_currency($cash_up->cost_bank),
		'expense' => to_currency($cash_up->expense),
		'balance' => to_currency($cash_up->closed_amount_total),
		'state' => ($cash_up->state==0 ? $CI->lang->line('common_opened') : $CI->lang->line('common_closed')),
		'close_date' => (!empty($cash_up->close_date) ? to_datetime(strtotime($cash_up->close_date)) : ''),
		'close' => ($cash_up->state==0 ? anchor($controller_name."/close/$cash_up->cashup_id", '<span class="glyphicon glyphicon-folder-close"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_close'))
		) : ''),
		'detail' => anchor($controller_name."/detail/$cash_up->cashup_id", '<span class="glyphicon glyphicon-list-alt"></span>',
			array('title'=>$CI->lang->line($controller_name.'_showdetail'))
		),
		'print' => anchor($controller_name."/print_report/$cash_up->cashup_id", '<span class="glyphicon glyphicon-print"></span>',
			array('title'=>$CI->lang->line($controller_name.'_print'))
		),
		'edit' => ''
	);
}

/*
Get the header for the cashup tabular view
*/
/*function get_cashups_manage_table_headers()
{
	$CI =& get_instance();

	$headers = array(
		array('cashup_id' => $CI->lang->line('cashups_id')),
		array('cash_book_id' => $CI->lang->line('cashups_cash_book_id')),
		array('open_date' => $CI->lang->line('cashups_opened_date')),
		array('open_employee_id' => $CI->lang->line('cashups_open_employee')),
		array('open_amount_cash' => $CI->lang->line('cashups_open_amount_cash')),
		array('transfer_amount_cash' => $CI->lang->line('cashups_transfer_amount_cash')),
		array('close_date' => $CI->lang->line('cashups_closed_date')),
		array('close_employee_id' => $CI->lang->line('cashups_close_employee')),
		array('closed_amount_cash' => $CI->lang->line('cashups_closed_amount_cash')),
		array('note' => $CI->lang->line('cashups_note')),
		array('closed_amount_due' => $CI->lang->line('cashups_closed_amount_due')),
		array('closed_amount_card' => $CI->lang->line('cashups_closed_amount_card')),
		array('closed_amount_check' => $CI->lang->line('cashups_closed_amount_check')),
		array('closed_amount_total' => $CI->lang->line('cashups_closed_amount_total'))
	);

	return transform_headers($headers);
}*/

/*
Gets the html data row for the cashups
*/
/*function get_cash_up_data_row($cash_up)
{
	$CI =& get_instance();

	$controller_name = strtolower(get_class($CI));

	return array (
		'cashup_id' => $cash_up->cashup_id,
		'cash_book_id' => $cash_up->cash_book,
		'open_date' => to_datetime(strtotime($cash_up->open_date)),
		'open_employee_id' => $cash_up->open_first_name . ' ' . $cash_up->open_last_name,
		'open_amount_cash' => to_currency($cash_up->open_amount_cash),
		'transfer_amount_cash' => to_currency($cash_up->transfer_amount_cash),
		'close_date' => to_datetime(strtotime($cash_up->close_date)),
		'close_employee_id' => $cash_up->close_first_name . ' ' . $cash_up->close_last_name,
		'closed_amount_cash' => to_currency($cash_up->closed_amount_cash),
		'note' => $cash_up->note ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>',
		'closed_amount_due' => to_currency($cash_up->closed_amount_due),
		'closed_amount_card' => to_currency($cash_up->closed_amount_card),
		'closed_amount_check' => to_currency($cash_up->closed_amount_check),
		'closed_amount_total' => to_currency($cash_up->closed_amount_total),
		'edit' => anchor($controller_name."/view/$cash_up->cashup_id", '<span class="glyphicon glyphicon-edit"></span>',
			array('class'=>'modal-dlg', 'data-btn-submit' => $CI->lang->line('common_submit'), 'title'=>$CI->lang->line($controller_name.'_update'))
		)
	);
}*/

?>
