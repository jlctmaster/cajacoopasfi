<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Cashups extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('cashups');
	}

	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_cashups_manage_table_headers());

		$data['cashup_openend'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date('Y-m-d'));

		// filters that will be loaded in the multiselect dropdown
		$data['filters'] = array('is_deleted' => $this->lang->line('cashups_is_deleted'));

		$this->load->view('cashups/manage', $data);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function get_subconcept($cash_concept_id)
	{
		$suggestions = $this->xss_clean($this->Cash_concept->get_parent_all($cash_concept_id,0)->result_array());

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_person()
	{
		$suggestions = $this->xss_clean($this->Person->get_search_person_suggestions($this->input->get('term'),TRUE));

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_suppliers()
	{
		$suggestions = $this->xss_clean($this->Supplier->get_search_suppliers_suggestions($this->input->get('term'),TRUE));

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_expense_doctype()
	{
		$suggestions = $this->xss_clean($this->Expense->get_doctype_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}

	public function search()
	{
		$cash_up = 0;
		$search   = $this->input->get('search');
		$limit    = $this->input->get('limit');
		$offset   = $this->input->get('offset');
		$sort     = $this->input->get('sort');
		$order    = $this->input->get('order');
		$filters  = array(
					 'user_id' => $this->User->get_logged_in_user_info()->person_id,
					 'start_date' => $this->input->get('start_date'),
					 'end_date' => $this->input->get('end_date'),
					 'is_deleted' => FALSE);

		// check if any filter is set in the multiselect dropdown
		$filledup = array_fill_keys($this->input->get('filters'), TRUE);
		$filters = array_merge($filters, $filledup);
		$cash_ups = $this->Cashup->search($search, $filters, $limit, $offset, $sort, $order);
		$total_rows = $this->Cashup->get_found_rows($search, $filters);
		$data_rows = array();
		foreach($cash_ups->result() as $cash_up)
		{
			$data_rows[] = $this->xss_clean(get_cash_up_data_row($cash_up));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function view($cashup_id = -1)
	{
		$data['cash_book_notfound'] = FALSE;
		$user_info = $this->User->get_logged_in_user_info();
		$data['cash_book_info'] = $this->Cash_book->get_info_by_user($user_info->person_id);
		$cash_ups_info = $this->Cashup->get_info($cashup_id);
		// open cashup
		if(empty($cash_ups_info->cashup_id))
		{
			$cash_ups_info->open_date = date('Y-m-d H:i:s');
			$cash_ups_info->open_amount_cash = 0;
		}

		if(empty($data['cash_book_info']->cash_book_id))
		{
			$data['cash_book_notfound'] = TRUE;
		}

		$data['cash_ups_info'] = $cash_ups_info;

		$this->load->view("cashups/form", $data);
	}

	public function get_row($row_id)
	{
		$cash_ups_info = $this->Cashup->get_info($row_id);
		$data_row = $this->xss_clean(get_cash_up_data_row($cash_ups_info));

		echo json_encode($data_row);
	}

	public function get_employee($cash_book_id)
	{
		$data_row = $this->Cash_book->get_info($cash_book_id);
		foreach(get_object_vars($data_row) as $property => $value)
		{
			$data_row->$property = $this->xss_clean($value);
		}

		echo json_encode($data_row);
	}

	public function get_vouchers($person_id)
	{
		$data_row = $this->Voucher_operation->get_voucheropen_by_person($person_id)->result_array();
		foreach(get_object_vars($data_row) as $property => $value)
		{
			$data_row->$property = $this->xss_clean($value);
		}

		echo json_encode($data_row);
	}

	public function get_prepaymentvouchers($person_id)
	{
		$data_row = $this->Voucher_operation->get_voucherprepayment_by_person($person_id)->result_array();
		foreach(get_object_vars($data_row) as $property => $value)
		{
			$data_row->$property = $this->xss_clean($value);
		}

		echo json_encode($data_row);
	}

	public function close($cashup_id)
	{
		$data['cashup_info'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date('Y-m-d'));

		$data['cashup_info']->close_date = date('Y-m-d H:i:s');

		$this->load->view("cashups/close", $data);
	}

	public function detail($cashup_id)
	{

		//$data['cashup_summary'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date('Y-m-d'));

		$data['cashup_summary'] = $this->Cashup->get_info($cashup_id);

		$data['denomination_currency'] = $this->Cashup->get_denominations($cashup_id,CURRENCY);
		$data['initial_balance'] = $data['cashup_summary']->initial_balance; //$this->Cash_daily->get_initial_balance($data['cashup_summary']->cash_book_id,$data['cashup_summary']->open_date)->balance;
		//	Incomes
		$data['receipt_income'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'01-01')->balance;
		$data['open_cash'] = ($this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-01-00')->balance + $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'01-02')->balance);
		$data['ticket_sales'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-01-05')->balance;
		$data['invoices'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-01-06')->balance;
		$data['vouchers'] = $this->Cash_daily->get_vouchers($data['cashup_summary']->cash_book_id,$data['cashup_summary']->open_date)->balance;
		//	Costs 
		$data['receipt_cost'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'02-01')->balance;
		$data['vo_serie01'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-02-03')->balance;
		$data['vo_serie02'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-02-04')->balance;
		$data['adjustnotes'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'02-02')->balance;
		$data['credittnotes'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-02-02')->balance;

		$data['expenses'] = $this->Cash_daily->get_expenses($data['cashup_summary']->cashup_id);
		//	Expenses
		$this->load->view('cashups/manage_detail', $data);
	}

	public function detail_income($cashup_id,$currency = 'all',$cash_concept_id = 'all')
	{
		$data['table_headers'] = $this->xss_clean(get_cash_daily_manage_table_headers());

		$data['cashup_summary'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date('Y-m-d'));

		$data['operation_types'] = array('all' => $this->lang->line('common_none_selected_text'), '1' => "INGRESO", '2' => "EGRESO", '3' => "GASTO");

		$data['operation_type'] = 1;

		$data['currencies'] = array('all' => $this->lang->line('common_none_selected_text'), CURRENCY => CURRENCY_LABEL, USDCURRENCY => USDCURRENCY_LABEL);

		$data['currency'] = $currency;

		$cash_concept = array('all' => $this->lang->line('common_none_selected_text'));

		foreach($this->Cash_concept->get_exists_on_cash_daily(1)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}

		$data['cash_concepts'] = $cash_concept;

		$data['cash_concept_id'] = $cash_concept_id;

		$this->load->view('cashups/manage_detailed', $data);
	}

	public function detail_cost($cashup_id,$currency = 'all')
	{
		$data['table_headers'] = $this->xss_clean(get_cash_daily_manage_table_headers());

		$data['cashup_summary'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date('Y-m-d'));

		$data['operation_types'] = array('all' => $this->lang->line('common_none_selected_text'), '1' => "INGRESO", '2' => "EGRESO", '3' => "GASTO");

		$data['operation_type'] = 2;

		$data['currencies'] = array('all' => $this->lang->line('common_none_selected_text'), CURRENCY => CURRENCY_LABEL, USDCURRENCY => USDCURRENCY_LABEL);

		$data['currency'] = $currency;

		$cash_concept = array('all' => $this->lang->line('common_none_selected_text'));

		foreach($this->Cash_concept->get_exists_on_cash_daily(2)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}

		$data['cash_concepts'] = $cash_concept;

		$data['cash_concept_id'] = $cash_concept_id;

		$this->load->view('cashups/manage_detailed', $data);
	}

	public function detail_expense($cashup_id,$currency = 'all',$cash_concept_id = 'all')
	{
		$data['table_headers'] = $this->xss_clean(get_cash_daily_manage_table_headers());

		$data['cashup_summary'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date('Y-m-d'));

		$data['operation_types'] = array('all' => $this->lang->line('common_none_selected_text'), '1' => "INGRESO", '2' => "EGRESO", '3' => "GASTO");

		$data['operation_type'] = 3;

		$data['currencies'] = array('all' => $this->lang->line('common_none_selected_text'), CURRENCY => CURRENCY_LABEL, USDCURRENCY => USDCURRENCY_LABEL);

		$data['currency'] = $currency;

		$cash_concept = array('all' => $this->lang->line('common_none_selected_text'));

		foreach($this->Cash_concept->get_exists_on_cash_daily(3)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}

		$data['cash_concepts'] = $cash_concept;

		$data['cash_concept_id'] = $cash_concept_id;

		$this->load->view('cashups/manage_detailed', $data);
	}

	/*
	Returns cost_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_detail($cashup_id)
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = (!empty($this->input->get('sort')) ? $this->input->get('sort') : 'cash_daily.movementdate');
		$order  = (!empty($this->input->get('order')) ? $this->input->get('order') : 'ASC');

		$filters = array('operation_type' => $this->input->get('operation_type'),'currency' => $this->input->get('currency'),'cash_concept_id' => $this->input->get('cash_concept_id'));

		$cash_dailys = $this->Cash_daily->search($cashup_id,$search,$filters, $limit, $offset, $sort, $order);
		$total_rows = $this->Cash_daily->get_found_rows($cashup_id,$search,$filters);

		$data_rows = array();
		foreach($cash_dailys->result() as $cash_daily)
		{
			$data_rows[] = $this->xss_clean(get_cash_daily_data_row($cash_daily));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function print_report($cashup_id)
	{
		$data['cashup_summary'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date('Y-m-d'));

		$data['denomination_currency'] = $this->Cashup->get_denominations($cashup_id,CURRENCY);
		$data['initial_balance'] = $this->Cash_daily->get_initial_balance($data['cashup_summary']->cash_book_id,$data['cashup_summary']->open_date)->balance;
		//	Incomes
		$data['receipt_income'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'01-01')->balance;
		$data['open_cash'] = ($this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-01-00')->balance + $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'01-02')->balance);
		$data['ticket_sales'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-01-05')->balance;
		$data['invoices'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-01-06')->balance;
		$data['vouchers'] = $this->Cash_daily->get_vouchers($data['cashup_summary']->cash_book_id,$data['cashup_summary']->open_date)->balance;
		//	Costs 
		$data['receipt_cost'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'02-01')->balance;
		$data['vo_serie01'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-02-03')->balance;
		$data['vo_serie02'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-02-04')->balance;
		$data['adjustnotes'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'02-02')->balance;
		$data['credittnotes'] = $this->Cash_daily->get_balance_by_code($data['cashup_summary']->cashup_id,'00-02-02')->balance;

		$data['expenses'] = $this->Cash_daily->get_expenses($data['cashup_summary']->cashup_id);

		//$this->load->view('cashups/report_cash_register', $data);

		$html = $this->load->view('cashups/report_cash_register', $data, TRUE);
		
		// Cargamos la librería
		$this->load->library('pdfgenerator_lib');
		// definamos un nombre para el archivo. No es necesario agregar la extension .pdf
		$filename = 'arqueo_de_caja';
		// generamos el PDF. Pasemos por encima de la configuración general y definamos otro tipo de papel
		$this->pdfgenerator_lib->generate($html, $filename, true, 'Letter', 'portrait');
	}

	public function save($cashup_id = -1)
	{

		$this->Cashup->set_log("<< START LOG >>");

		$open_date = $this->input->post('open_date');
		$open_date_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $open_date);

		$cash_up_data = array(
			'open_date' => $open_date_formatter->format('Y-m-d H:i:s'),
			'open_amount_cash' => parse_decimals($this->input->post('open_amount_cash')),
			'description' => $this->input->post('description'),
			'open_employee_id' => $this->input->post('open_employee_id'),
			'cash_book_id' => $this->input->post('cash_book_id'),
			'deleted' => $this->input->post('deleted') != NULL
		);

		$cash_concept = $this->Cash_concept->get_info_by_code('00-01-00');

		if(!empty($cash_concept->cash_concept_id))
		{
			$cash_daily_data[] = array(
				'cash_concept_id' => $cash_concept->cash_concept_id,
				'cash_book_id' => $this->input->post('cash_book_id'),
				'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
				'movementdate' => date('Y-m-d H:i:s'),
				'description' => $this->lang->line('overall_cashs_cash_opening'),
				'currency' => CURRENCY,
				'amount' => parse_decimals($this->input->post('open_amount_cash')),
				'table_reference' => 'cash_up'
			);

			if($this->Cashup->opened($cash_up_data, $cash_daily_data, $cashup_id))
			{
				$cash_up_data = $this->xss_clean($cash_up_data);

				//New cashup_id
				if($cashup_id == -1)
				{
					$this->Cashup->set_log("<< END LOG >>");
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('cashups_successful_adding'), 'id' => $cash_up_data['cashup_id']));
				}
				else // Existing Cashup
				{
					$this->Cashup->set_log($this->db->last_query());
					$this->Cashup->set_log("<< END LOG >>");
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('cashups_successful_updating'), 'id' => $cashup_id));
				}
			}
			else//failure
			{
				$this->Cashup->set_log("<< END LOG >>");
				//$this->Cashup->get_log()."<br>".
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('cashups_error_adding_updating'), 'id' => -1));
			}
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('cashups_error_cash_concept_cash_book_notfound'), 'id' => -1));
		}
	}

	public function closed($cashup_id)
	{
		if($this->input->post('confirm') == "Y")
		{

			$close_date = $this->input->post('close_date');
			$close_date_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $close_date);

			$cashup_data = array(
				'close_date' => $close_date_formatter->format('Y-m-d H:i:s'),
				'closed_amount_total' => $this->input->post('closed_amount_total'),
				'close_employee_id' => $this->input->post('close_employee_id')
			);
			//	For Currency
			for ($i=0; $i < count($this->input->post('denominations')); $i++) {
				$denominates_currency_data[] = array(
					'cashup_id' => $cashup_id,
					'currency' => CURRENCY,
					'denomination' => $this->input->post('denominations')[$i],
					'quantity' => $this->input->post('quantities')[$i],
					'amount' => $this->input->post('line_amounts')[$i]
				);
			}

			if($this->Cashup->closed($cashup_data, $denominates_currency_data, $cashup_id))
			{
				$cashup_data = $this->xss_clean($cashup_data);
				
				$this->Cashup->set_log($this->db->last_query());
				$this->Cashup->set_log("<< End Log >>");
				//	Use log 
				//	$this->Cashup->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('cashups_successful_updating'), 'id' => $cashup_id));
			}
			else//failure
			{
				$this->Cashup->set_log("<< End Log >>");
				//	Use log 
				//	$this->Cashup->get_log()."<br>".
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('cashups_error_adding_updating') . ' ' . $cashup_data['close_date'], 'id' => -1));
			}
		}
		else
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('cashups_none_closed'), 'id' => $cashup_id));
		}
	}

	public function delete()
	{
		$cash_ups_to_delete = $this->input->post('ids');

		if($this->Cashup->delete_list($cash_ups_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('cashups_successful_deleted') . ' ' . count($cash_ups_to_delete) . ' ' . $this->lang->line('cashups_one_or_multiple'), 'ids' => $cash_ups_to_delete));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('cashups_cannot_be_deleted'), 'ids' => $cash_ups_to_delete));
		}
	}

	/*
	AJAX call from cashup input form to calculate the total
	*/
	public function ajax_cashup_total()
	{
		$open_amount_cash = parse_decimals($this->input->post('open_amount_cash'));
		$transfer_amount_cash = parse_decimals($this->input->post('transfer_amount_cash'));
		$closed_amount_cash = parse_decimals($this->input->post('closed_amount_cash'));
		$closed_amount_due = parse_decimals($this->input->post('closed_amount_due'));
		$closed_amount_card = parse_decimals($this->input->post('closed_amount_card'));
		$closed_amount_check = parse_decimals($this->input->post('closed_amount_check'));

		$total = $this->_calculate_total($open_amount_cash, $transfer_amount_cash, $closed_amount_due, $closed_amount_cash, $closed_amount_card, $closed_amount_check);

		echo json_encode(array('total' => to_currency_no_money($total)));
	}

	/*
	Calculate total
	*/
	private function _calculate_total($open_amount_cash, $transfer_amount_cash, $closed_amount_due, $closed_amount_cash, $closed_amount_card, $closed_amount_check)
	{
		return ($closed_amount_cash - $open_amount_cash - $transfer_amount_cash + $closed_amount_due + $closed_amount_card + $closed_amount_check);
	}


	//	Income Controller

	public function income()
	{
		$data['table_headers'] = $this->xss_clean(get_income_manage_table_headers(1));

		$data['income_summary'] = $this->Income->get_summary_info(1);

		$this->load->view('cashups/manage_income', $data);
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_income()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');
		$filters  = array(
					 'start_date' => $this->input->get('start_date'),
					 'end_date' => $this->input->get('end_date')
					);

		$incomes = $this->Income->search($search, $filters, 1, $limit, $offset, $sort, $order);
		$total_rows = $this->Income->get_found_rows($search,$filters, 1);

		$data_rows = array();
		foreach($incomes->result() as $income)
		{
			$data_rows[] = $this->xss_clean(get_income_data_row($income, 1));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function view_income($income_id = -1)
	{
		$data['income_info'] = $this->Income->get_info($income_id, 1);

		if(empty($data['income_info']->documentdate))
		{
			$data['income_info']->documentdate = date('Y-m-d H:i:s');
		}
		if(empty($data['income_info']->amount))
		{
			$data['income_info']->amount = 0;
		}

		$cash_concept = array('-1' => $this->lang->line('common_none_selected_text'));
		foreach($this->Cash_concept->get_all_summary(1)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}
		$data['cash_concepts'] = $cash_concept;
		$data['selected_cash_concept'] = $data['income_info']->cash_concept_id;

		if(!empty($data['income_info']->cash_subconcept_id))
		{
			$cash_subconcept = array('-1' => $this->lang->line('common_none_selected_text'));
			foreach ($this->Cash_concept->get_parent_all($data['income_info']->cash_concept_id)->result_array() as $row) {
				$cash_subconcept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
			}
			$data['cash_subconcepts'] = $cash_subconcept;
			$data['selected_cash_subconcept'] = $data['income_info']->cash_subconcept_id;
		}

		if(!empty($data['income_info']->voucher_operation_id))
		{
			$voucher_operations = array('-1' => $this->lang->line('common_none_selected_text'));
			foreach ($this->Voucher_operation->get_voucherprepayment_by_person($data['income_info']->person_id)->result_array() as $row) {
				$voucher_operations[$this->xss_clean($row['voucher_operation_id'])] = $this->xss_clean($row['serieno']."-".$row['voucher_operation_number']);
			}
			$data['voucher_operations'] = $voucher_operations;
			$data['selected_voucher_operation'] = $data['income_info']->voucher_operation_id;
			$data['voucher_openamt'] = $this->Voucher_operation->get_prepaymentamount($data['income_info']->voucher_operation_id)->openamt;
		}

		$this->load->view("cashups/form_income", $data);
	}

	public function save_income($income_id = -1)
	{
		$this->Income->set_log("<< Start Log >>");

		$documentdate = $this->input->post('documentdate');
		$documentdate_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $documentdate);

		if($income_id != -1)
		{
			$documentno = $this->input->post('documentno');
		}
		else
		{
			$documentno = ($this->config->item('income_number_automatic') == '0' ? $this->input->post('documentno') : $this->Appconfig->acquire_save_next_doctype_sequence($this->config->item('income_doctype_sequence')));
		}

		$income_data = array(
			'documentno' => $documentno,
			'documentdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'person_id' => (!empty($this->input->post('person_id')) ? $this->input->post('person_id') : NULL),
			'person_name' => $this->input->post('person'),
			'bankaccount_id' => NULL,
			'voucher_operation_id' => (!empty($this->input->post('voucher_operation_id')) ? $this->input->post('voucher_operation_id') : NULL),
			'cash_concept_id' => $this->input->post('cash_concept_id'),
			'cash_subconcept_id' => $this->input->post('cash_subconcept_id'),
			'detail' => $this->input->post('detail'),
			'is_cashupmovement' => 1,
			'movementtype' => $this->input->post('movementtype'),
			'trx_number' => (!empty($this->input->post('trx_number')) ? $this->input->post('trx_number') : NULL),
			'currency' => $this->input->post('currency'),
			'amount' => $this->input->post('amount')
		);

		$cash_ups_info = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date('Y-m-d'));

		$cash_daily_data[] = array(
			'cash_concept_id' => $this->input->post('cash_subconcept_id'),
			'cash_book_id' => $cash_ups_info->cash_book_id,
			'operation_type' => 1,
			'movementdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'description' => $this->input->post('detail'),
			'currency' => $this->input->post('currency'),
			'amount' => $this->input->post('amount'),
			'isbankmovement' => (!empty($this->input->post('trx_number')) ? 1 : 0),
			'table_reference' => 'incomes',
			'cashup_id' => $cash_ups_info->cashup_id
		);

		if($this->Income->save($income_data, $cash_daily_data, $income_id,1))
		{
			$income_data = $this->xss_clean($income_data);

			// New cashup_id
			if($income_id == -1)
			{
				$this->Income->set_log("<< End Log >>");
				//	Use log 
				//	$this->Income->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('incomes_successful_adding'), 'id' => $income_data['income_id']));
			}
			else // Existing Cashup
			{
				$this->Income->set_log($this->db->last_query());
				$this->Income->set_log("<< End Log >>");
				//	Use log 
				//	$this->Income->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('incomes_successful_updating'), 'id' => $income_id));
			}
		}
		else//failure
		{
			if($income_id != -1)
			{
				$this->Income->set_log($this->db->last_query());
			}
			$this->Income->set_log("<< End Log >>");
			//	Use log 
			//	$this->Income->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->Income->get_log()."<br>".$this->lang->line('incomes_error_adding_updating') . ' ' . $income_data['documentno'], 'id' => -1));
		}
	}

	public function delete_income($income_id)
	{
		$data['income_info'] = $this->Income->get_info($income_id,1);

		$this->load->view("cashups/delete_income", $data);
	}

	public function deleted_income()
	{

		if($this->input->post('confirm')=="Y")
		{
			$this->Income->set_log('<< START LOG >>');
			$income_to_delete = $this->input->post('ids');
			
			if($this->Income->delete($income_to_delete,$this->input->post('currency'),1))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('incomes_successful_deleted') . ' ' . count($income_to_delete) . ' ' . $this->lang->line('incomes_one_or_multiple')));
			}
			else
			{
				$this->Income->set_log('<< END LOG >>');
				echo json_encode(array('success' => FALSE, 'message' => $this->Income->get_log()."<br>".$this->lang->line('incomes_cannot_be_deleted')));
			}
		}
		else
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('incomes_none_to_be_deleted')));
		}
	}

	//	Cost Controller

	public function cost()
	{
		$data['table_headers'] = $this->xss_clean(get_cost_manage_table_headers(1));

		$data['cost_summary'] = $this->Cost->get_summary_info(1);

		$this->load->view('cashups/manage_cost', $data);
	}

	/*
	Returns cost_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_cost()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');
		$filters  = array(
					 'start_date' => $this->input->get('start_date'),
					 'end_date' => $this->input->get('end_date')
					);

		$costs = $this->Cost->search($search, $filters, 1, $limit, $offset, $sort, $order);
		$total_rows = $this->Cost->get_found_rows($search,$filters, 1);

		$data_rows = array();
		foreach($costs->result() as $cost)
		{
			$data_rows[] = $this->xss_clean(get_cost_data_row($cost,1));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function view_cost($cost_id = -1)
	{
		$data['cost_info'] = $this->Cost->get_info($cost_id,1);

		if(empty($data['cost_info']->documentdate))
		{
			$data['cost_info']->documentdate = date('Y-m-d H:i:s');
		}
		if(empty($data['cost_info']->amount))
		{
			$data['cost_info']->amount = 0;
		}

		$bankaccount = array('-1' => $this->lang->line('common_none_selected_text'));
		$bankaccount_cur = array();

		foreach($this->Bank->get_all_bankaccounts(CURRENCY)->result_array() AS $bank)
		{
			$bankaccount[$this->xss_clean($bank['bankaccount_id'])] = $this->xss_clean($bank['name'].' '.$bank['account_number'].' ('.($bank['currency']==CURRENCY ? CURRENCY_LABEL : USDCURRENCY_LABEL).')');
			$bankaccount_cur[$this->xss_clean($bank['bankaccount_id'])] = $this->xss_clean($bank['currency']);
		}

		$data['bankaccounts'] = $bankaccount;
		$data['bankaccounts_cur'] = $bankaccount_cur;
		$data['selected_bankaccount'] = (!empty($data['cost_info']->bankaccount_id) ? $data['cost_info']->bankaccount_id : -1);

		$cash_concept = array('-1' => $this->lang->line('common_none_selected_text'));
		foreach($this->Cash_concept->get_all_summary(2)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}
		$data['cash_concepts'] = $cash_concept;
		$data['selected_cash_concept'] = $data['cost_info']->cash_concept_id;

		if(!empty($data['cost_info']->cash_subconcept_id))
		{
			$cash_subconcept = array('-1' => $this->lang->line('common_none_selected_text'));
			foreach ($this->Cash_concept->get_parent_all($data['cost_info']->cash_concept_id)->result_array() as $row) {
				$cash_subconcept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
			}
			$data['cash_subconcepts'] = $cash_subconcept;
			$data['selected_cash_subconcept'] = $data['cost_info']->cash_subconcept_id;
		}

		if(!empty($data['cost_info']->voucher_operation_id))
		{
			$voucher_operations = array('-1' => $this->lang->line('common_none_selected_text'));
			foreach ($this->Voucher_operation->get_voucheropen_by_person($data['cost_info']->person_id)->result_array() as $row) {
				$voucher_operations[$this->xss_clean($row['voucher_operation_id'])] = $this->xss_clean($row['serieno']."-".$row['voucher_operation_number']);
			}
			$data['voucher_operations'] = $voucher_operations;
			$data['selected_voucher_operation'] = $data['cost_info']->voucher_operation_id;
			$data['voucher_openamt'] = $this->Voucher_operation->get_openamount($data['cost_info']->voucher_operation_id)->openamt;
		}

		$data['cash_ups_info'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date('Y-m-d'));


		$this->load->view("cashups/form_cost", $data);
	}

	public function save_cost($cost_id = -1)
	{
		$this->Cost->set_log("<< Start Log >>");

		$documentdate = $this->input->post('documentdate');
		$documentdate_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $documentdate);

		if($cost_id != -1)
		{
			$documentno = $this->input->post('documentno');
		}
		else
		{
			$documentno = ($this->config->item('cost_number_automatic') == '0' ? $this->input->post('documentno') : $this->Appconfig->acquire_save_next_doctype_sequence($this->config->item('cost_doctype_sequence')));
		}

		$cost_data = array(
			'documentno' => $documentno,
			'documentdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'person_id' => $this->input->post('person_id'),
			'bankaccount_id' => ($this->input->post('bankaccount_id') == -1 ? NULL : $this->input->post('bankaccount_id')),
			'voucher_operation_id' => (!empty($this->input->post('voucher_operation_id')) ? $this->input->post('voucher_operation_id') : NULL),
			'cash_concept_id' => $this->input->post('cash_concept_id'),
			'cash_subconcept_id' => $this->input->post('cash_subconcept_id'),
			'detail' => $this->input->post('detail'),
			'is_cashupmovement' => 1,
			'movementtype' => $this->input->post('movementtype'),
			'trx_number' => (!empty($this->input->post('trx_number')) ? $this->input->post('trx_number') : NULL),
			'currency' => $this->input->post('currency'),
			'amount' => $this->input->post('amount')
		);

		$cash_daily_data[] = array(
			'cash_concept_id' => $this->input->post('cash_subconcept_id'),
			'cash_book_id' => $this->input->post('cash_book_id'),
			'operation_type' => 2,
			'movementdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'description' => $this->input->post('detail'),
			'currency' => $this->input->post('currency'),
			'amount' => $this->input->post('amount'),
			'isbankmovement' => (!empty($this->input->post('trx_number')) ? 1 : 0),
			'table_reference' => 'costs',
			'cashup_id' => $this->input->post('cashup_id')
		);

		if($this->Cost->save($cost_data, $cash_daily_data, $cost_id, 1))
		{
			$cost_data = $this->xss_clean($cost_data);

			// New cashup_id
			if($cost_id == -1)
			{
				$this->Cost->set_log("<< End Log >>");
				//	Use log 
				//	$this->Cost->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('costs_successful_adding'), 'id' => $cost_data['cost_id']));
			}
			else // Existing Cashup
			{
				$this->Cost->set_log($this->db->last_query());
				$this->Cost->set_log("<< End Log >>");
				//	Use log 
				//	$this->Cost->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('costs_successful_updating'), 'id' => $cost_id));
			}
		}
		else//failure
		{
			if($cost_id != -1)
			{
				$this->Cost->set_log($this->db->last_query());
			}
			$this->Cost->set_log("<< End Log >>");
			//	Use log 
			//	$this->Cost->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->Cost->get_log()."<br>".$this->lang->line('costs_error_adding_updating') . ' ' . $cost_data['documentno'], 'id' => -1));
		}
	}

	public function delete_cost($cost_id)
	{
		$data['cost_info'] = $this->Cost->get_info($cost_id,1);

		$this->load->view("cashups/delete_cost", $data);
	}

	public function deleted_cost()
	{

		if($this->input->post('confirm')=="Y")
		{
			$this->Cost->set_log('<< START LOG >>');
			$cost_to_delete = $this->input->post('ids');
			
			if($this->Cost->delete($cost_to_delete,$this->input->post('currency'),1))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('costs_successful_deleted') . ' ' . count($cost_to_delete) . ' ' . $this->lang->line('costs_one_or_multiple')));
			}
			else
			{
				$this->Cost->set_log('<< END LOG >>');
				echo json_encode(array('success' => FALSE, 'message' => $this->Cost->get_log()."<br>".$this->lang->line('costs_cannot_be_deleted')));
			}
		}
		else
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('costs_none_to_be_deleted')));
		}
	}

	//	Expense Controller

	public function expense()
	{
		$data['table_headers'] = $this->xss_clean(get_expense_manage_table_headers(1));

		$data['expense_summary'] = $this->Expense->get_summary_info(1);

		$this->load->view('cashups/manage_expense', $data);
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_expense()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');
		$filters  = array(
					 'start_date' => $this->input->get('start_date'),
					 'end_date' => $this->input->get('end_date')
					);

		$expenses = $this->Expense->search($search, $filters, 1, $limit, $offset, $sort, $order);
		$total_rows = $this->Expense->get_found_rows($search,$filters, 1);

		$data_rows = array();
		foreach($expenses->result() as $expense)
		{
			$data_rows[] = $this->xss_clean(get_expense_data_row($expense,1));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function view_expense($expense_id = -1)
	{
		$data['expense_info'] = $this->Expense->get_info($expense_id,1);

		if(empty($data['expense_info']->documentdate))
		{
			$data['expense_info']->documentdate = date('Y-m-d H:i:s');
		}
		if(empty($data['expense_info']->amount))
		{
			$data['expense_info']->amount = 0;
		}

		$bankaccount = array('-1' => $this->lang->line('common_none_selected_text'));
		$bankaccount_cur = array();

		foreach($this->Bank->get_all_bankaccounts(CURRENCY)->result_array() AS $bank)
		{
			$bankaccount[$this->xss_clean($bank['bankaccount_id'])] = $this->xss_clean($bank['name'].' '.$bank['account_number'].' ('.($bank['currency']==CURRENCY ? CURRENCY_LABEL : USDCURRENCY_LABEL).')');
			$bankaccount_cur[$this->xss_clean($bank['bankaccount_id'])] = $this->xss_clean($bank['currency']);
		}

		$data['bankaccounts'] = $bankaccount;
		$data['bankaccounts_cur'] = $bankaccount_cur;
		$data['selected_bankaccount'] = (!empty($data['expense_info']->bankaccount_id) ? $data['expense_info']->bankaccount_id : -1);

		$cash_concept = array('-1' => $this->lang->line('common_none_selected_text'));
		foreach($this->Cash_concept->get_all_summary(3)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}
		$data['cash_concepts'] = $cash_concept;
		$data['selected_cash_concept'] = $data['expense_info']->cash_concept_id;

		/*if(!empty($data['expense_info']->cash_subconcept_id))
		{
			$cash_subconcept = array('-1' => $this->lang->line('common_none_selected_text'));
			foreach ($this->Cash_concept->get_parent_all($data['expense_info']->cash_concept_id)->result_array() as $row) {
				$cash_subconcept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
			}
			$data['cash_subconcepts'] = $cash_subconcept;
			$data['selected_cash_subconcept'] = $data['expense_info']->cash_subconcept_id;
		}*/

		$data['cash_ups_info'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date('Y-m-d'));

		$this->load->view("cashups/form_expense", $data);
	}

	public function save_expense($expense_id = -1)
	{
		$this->Expense->set_log("<< Start Log >>");

		$documentdate = $this->input->post('documentdate');
		$documentdate_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $documentdate);

		if($expense_id != -1)
		{
			$documentno = $this->input->post('documentno');
		}
		else
		{
			$documentno = ($this->config->item('expense_number_automatic') == '0' ? $this->input->post('documentno') : $this->Appconfig->acquire_save_next_doctype_sequence($this->config->item('expense_doctype_sequence')));
		}

		$expense_data = array(
			'documentno' => $documentno,
			'documentdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'person_id' => (!empty($this->input->post('person_id')) ? $this->input->post('person_id') : NULL),
			'person_name' => $this->input->post('person'),
			'bankaccount_id' => ($this->input->post('bankaccount_id') == -1 ? NULL : $this->input->post('bankaccount_id')),
			'cash_concept_id' => $this->input->post('cash_concept_id'),
			'cash_subconcept_id' => NULL, //$this->input->post('cash_subconcept_id'),
			'detail' => $this->input->post('detail'),
			'doctype' => $this->input->post('doctype'),
			'docnumber' => $this->input->post('docnumber'),
			'is_cashupmovement' => 1,
			'movementtype' => $this->input->post('movementtype'),
			'trx_number' => (!empty($this->input->post('trx_number')) ? $this->input->post('trx_number') : NULL),
			'currency' => $this->input->post('currency'),
			'amount' => $this->input->post('amount')
		);

		$cash_ups_info = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date('Y-m-d'));

		$cash_daily_data[] = array(
			'cash_concept_id' => $this->input->post('cash_concept_id'),
			'cash_book_id' => $cash_ups_info->cash_book_id,
			'operation_type' => 3,
			'movementdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'description' => $this->input->post('detail'),
			'currency' => $this->input->post('currency'),
			'amount' => $this->input->post('amount'),
			'isbankmovement' => (!empty($this->input->post('trx_number')) ? 1 : 0),
			'table_reference' => 'expenses',
			'cashup_id' => $cash_ups_info->cashup_id
		);

		if($this->Expense->save($expense_data, $cash_daily_data, $expense_id, 1))
		{
			$expense_data = $this->xss_clean($expense_data);

			// New cashup_id
			if($expense_id == -1)
			{
				$this->Expense->set_log("<< End Log >>");
				//	Use log 
				//	$this->Expense->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('expenses_successful_adding'), 'id' => $expense_data['expense_id']));
			}
			else // Existing Cashup
			{
				$this->Expense->set_log($this->db->last_query());
				$this->Expense->set_log("<< End Log >>");
				//	Use log 
				//	$this->Expense->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('expenses_successful_updating'), 'id' => $expense_id));
			}
		}
		else//failure
		{
			if($expense_id != -1)
			{
				$this->Expense->set_log($this->db->last_query());
			}
			$this->Expense->set_log("<< End Log >>");
			//	Use log 
			//	$this->Expense->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->Expense->get_log()."<br>".$this->lang->line('expenses_error_adding_updating') . ' ' . $expense_data['documentno'], 'id' => -1));
		}
	}

	public function delete_expense($expense_id)
	{
		$data['expense_info'] = $this->Expense->get_info($expense_id,1);

		$this->load->view("cashups/delete_expense", $data);
	}

	public function deleted_expense()
	{

		if($this->input->post('confirm')=="Y")
		{
			$this->Expense->set_log('<< START LOG >>');
			$expense_to_delete = $this->input->post('ids');
			
			if($this->Expense->delete($expense_to_delete,$this->input->post('currency'),1))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('expenses_successful_deleted') . ' ' . count($expense_to_delete) . ' ' . $this->lang->line('expenses_one_or_multiple')));
			}
			else
			{
				$this->Expense->set_log('<< END LOG >>');
				echo json_encode(array('success' => FALSE, 'message' => $this->Expense->get_log()."<br>".$this->lang->line('expenses_cannot_be_deleted')));
			}
		}
		else
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('expenses_none_to_be_deleted')));
		}
	}
}
?>
