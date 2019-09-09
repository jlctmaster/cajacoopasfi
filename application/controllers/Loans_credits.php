<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Loans_credits extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('loans_credits');
	}

	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_loan_credit_manage_table_headers());

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view('loans_credits/manage', $data);
	}

	public function summary()
	{
		$data['table_headers'] = $this->xss_clean(get_loan_credit_summary_manage_table_headers());

		$this->load->view('loans_credits/manage_summary', $data);
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		//	Search Loan
		$loans = $this->Loan->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Loan->get_found_rows($search);
		//	Search Credit
		$credits = $this->Credit->search($search, $limit, $offset, $sort, $order);
		$total_rows += $this->Credit->get_found_rows($search);

		$data_rows = array();
		//	Loan Data
		foreach($loans->result() as $loan)
		{
			$data_rows[] = $this->xss_clean(get_loan_data_row($loan));
		}
		//	Credit Data
		foreach($credits->result() as $credit)
		{
			$data_rows[] = $this->xss_clean(get_credit_data_row($credit));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_summary()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		//	Search Loan
		$loans = $this->Loan->search_summary($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Loan->get_summary_found_rows($search);
		//	Search Credit
		$credits = $this->Credit->search_summary($search, $limit, $offset, $sort, $order);
		$total_rows += $this->Credit->get_summary_found_rows($search);

		$data_rows = array();
		//	Loan Data
		foreach($loans->result() as $loan)
		{
			$data_rows[] = $this->xss_clean(get_loan_summary_data_row($loan));
		}
		//	Credit Data
		foreach($credits->result() as $credit)
		{
			$data_rows[] = $this->xss_clean(get_credit_summary_data_row($credit));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_location()
	{
		$suggestions = $this->xss_clean($this->Stock_location->get_search_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_partner()
	{
		$suggestions = $this->xss_clean($this->Supplier->get_search_suggestions($this->input->get('term'),TRUE));

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_employee()
	{
		$suggestions = $this->xss_clean($this->Employee->get_search_suggestions($this->input->get('term'),TRUE));

		echo json_encode($suggestions);
	}

	public function get_row($row_id)
	{
		$loandata_row = $this->xss_clean(get_loan_data_row($this->Loan->get_info($row_id)));
		$creditdata_row = $this->xss_clean(get_credit_data_row($this->Credit->get_info($row_id)));

		$data_row = array_merge($loandata_row,$creditdata_row);

		echo json_encode($data_row);
	}

	public function view($loan_id = -1)
	{
		$data['loan_info'] = $this->Loan->get_info($loan_id);

		if(empty($data['loan_info']->loandate))
		{
			$data['loan_info']->loandate = date('Y-m-d');
		}
		if(empty($data['loan_info']->returndate))
		{
			$data['loan_info']->returndate = date('Y-m-d');
		}

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view("loans_credits/form_loan", $data);
	}

	public function pay_loan($loan_id)
	{
		$data['loan_info'] = $this->Loan->get_info($loan_id);

		$data['loan_info']->paydate = date('Y-m-d');

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view("loans_credits/form_payloan", $data);
	}

	public function pay_credit($credit_id)
	{
		$data['credit_info'] = $this->Credit->get_info($credit_id);

		$data['credit_info']->paydate = date('Y-m-d');

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view("loans_credits/form_paycredit", $data);
	}

	public function pay_summary_loan($dni)
	{
		$data['loan_credit_info'] = $this->Loan->get_summary_info($dni);

		$data['table'] = "l";
		
		$data['loan_credit_info']->paydate = date('Y-m-d');

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view("loans_credits/form_paysummary", $data);
	}

	public function pay_summary_credit($dni)
	{
		$data['loan_credit_info'] = $this->Credit->get_summary_info($dni);

		$data['table'] = "c";
		
		$data['loan_credit_info']->paydate = date('Y-m-d');

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view("loans_credits/form_paysummary", $data);
	}

	public function view_credit($credit_id = -1)
	{
		$data['credit_info'] = $this->Credit->get_info($credit_id);

		if(empty($data['credit_info']->creditdate))
		{
			$data['credit_info']->creditdate = date('Y-m-d');
		}
		if(empty($data['credit_info']->returndate))
		{
			$data['credit_info']->returndate = date('Y-m-d');
		}

		$data['credit_item_info'] = $this->Credit->get_detail_info($credit_id);

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view("loans_credits/form_credit", $data);
	}

	public function save($loan_id = -1)
	{

		$loandate = $this->input->post('loandate');
		$loandate_formatter = date_create_from_format($this->config->item('dateformat'), $loandate);

		$returndate = $this->input->post('returndate');
		$returndate_formatter = date_create_from_format($this->config->item('dateformat'), $returndate);

		$loan_data = array(
			'loan_type' => $this->input->post('loan_type'),
			'person_id' => $this->input->post('person_id'),
			'loandate' => $loandate_formatter->format('Y-m-d'),
			'cuote' => $this->input->post('cuote'),
			'returndate' => $returndate_formatter->format('Y-m-d'),
			'motive' => $this->input->post('motive'),
			'amount' => $this->input->post('amount'),
			'percent' => $this->input->post('percent'),
			'amt_interest' => $this->input->post('amt_interest')
		);

		$cash_concept = $this->Cash_concept->get_info_by_code('00-02-01');

		if(!empty($cash_concept->cash_concept_id))
		{
			$cash_daily_data[] = array(
				'cash_concept_id' => $cash_concept->cash_concept_id,
				'cashup_id' => $this->input->post('cashup_id'),
				'cash_book_id' => $this->input->post('cash_book_id'),
				'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
				'movementdate' => date('Y-m-d H:i:s'),
				'description' => $this->input->post('motive'),
				'currency' => CURRENCY,
				'amount' => parse_decimals($this->input->post('amount')),
				'table_reference' => 'loans'
			);

			if($this->Loan->save($loan_data, $cash_daily_data, $loan_id))
			{
				$loan_data = $this->xss_clean($loan_data);

				// New loan_id
				if($loan_id == -1)
				{
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('loans_credits_successful_adding_loan'), 'id' => $loan_data['loan_id']));
				}
				else // Existing Loans_credit
				{
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('loans_credits_successful_updating_loan'), 'id' => $loan_id));
				}
			}
			else//failure
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('loans_credits_error_adding_updating_loan') . ' ' . $loan_data['loandate'], 'id' => -1));
			}
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('cashups_error_cash_concept_cash_book_notfound'), 'id' => -1));
		}
	}

	public function save_credit($credit_id = -1)
	{

		$this->Credit->set_log("<< START LOG >>");

		$creditdate = $this->input->post('creditdate');
		$creditdate_formatter = date_create_from_format($this->config->item('dateformat'), $creditdate);

		$returndate = $this->input->post('returndate');
		$returndate_formatter = date_create_from_format($this->config->item('dateformat'), $returndate);

		$credit_data = array(
			'person_id' => $this->input->post('person_id'),
			'creditdate' => $creditdate_formatter->format('Y-m-d'),
			'cuote' => $this->input->post('cuote'),
			'returndate' => $returndate_formatter->format('Y-m-d'),
			'amount' => $this->input->post('amount'),
			'percent' => $this->input->post('percent'),
			'amt_interest' => $this->input->post('amt_interest')
		);

		for ($i=0; $i < count($this->input->post('items')); $i++) { 
			$locations = explode('_', $this->input->post('locations')[$i]);
			$items = explode('_', $this->input->post('items')[$i]);
			$credit_item_data[] = array(
				'location_id' => $locations[0],
				'item_id' => $items[0],
				'quantity' => parse_decimals($this->input->post('qtys')[$i]),
				'price' => $this->input->post('prices')[$i],
				'amount' => $this->input->post('amounts')[$i]
			);

			if($credit_id > 0)
			{
				$this->Inventory->delete_movement(array('trans_items' => $items[0],'trans_location' => $locations[0],'trans_comment' => $this->lang->line('loans_credits_items_editing_of_quantity')." Doc: ".$credit_id));
				$this->Credit->set_log($this->db->last_query());
			}

		}

		if($this->Credit->save($credit_data, $credit_item_data, $credit_id))
		{
			$credit_data = $this->xss_clean($credit_data);
			$credit_item_data = $this->xss_clean($credit_item_data);

			//Save item quantity
			for ($i=0; $i < count($this->input->post('items')); $i++) { 
				$locations = explode('_', $this->input->post('locations')[$i]);
				$items = explode('_', $this->input->post('items')[$i]);

				$item_quantity = $this->Item_quantity->get_item_quantity($items[0], $locations[0]);

				$updated_quantity = $item_quantity->quantity - parse_decimals($this->input->post('qtys')[$i]);

				$location_detail = array('item_id' => $items[0],
										'location_id' => $locations[0],
										'quantity' => $updated_quantity);
				
				$success &= $this->Item_quantity->save($location_detail, $items[0], $locations[0]);

				$inv_data = array(
					'trans_date' => date('Y-m-d H:i:s'),
					'trans_items' => $items[0],
					'trans_user' => $this->User->get_logged_in_user_info()->person_id,
					'trans_location' => $locations[0],
					'trans_comment' => $this->lang->line('loans_credits_items_editing_of_quantity')." Doc: ".$credit_data['credit_id'],
					'trans_inventory' => (parse_decimals($this->input->post('qtys')[$i]) * -1)
				);

				$success &= $this->Inventory->insert($inv_data);
			}

			$this->Credit->set_log("<< END LOG >>");

			// New loan_id
			if($credit_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('loans_credits_successful_adding_credit'), 'id' => $credit_data['credit_id']));
			}
			else // Existing Loans_credit
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('loans_credits_successful_updating_credit'), 'id' => $credit_id));
			}
		}
		else//failure
		{
			$this->Credit->set_log("<< END LOG >>");
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('loans_credits_error_adding_updating_credit') . ' ' . $credit_data['creditdate'], 'id' => -1));
		}
	}

	public function payment_loan($loan_id)
	{

		$paydate = $this->input->post('paydate');
		$paydate_formatter = date_create_from_format($this->config->item('dateformat'), $paydate);

		$amount = $this->input->post('amount');
		$interest = (($this->input->post('paytype') == 0 || $this->input->post('paytype') == 2) ? ($this->input->post('interest') >= $amount ? $amount : $this->input->post('interest')) : 0);
		$cumulate_interest = (($this->input->post('paytype') == 0 || $this->input->post('paytype') == 2) ? ($this->input->post('interest') >= $amount ? $this->input->post('interest')-$amount : 0) : $this->input->post('cumulate_interest'));
		$amount-=($this->input->post('paytype') == 0 || $this->input->post('paytype') == 2 ? $this->input->post('interest') : 0);
		$capital = (($this->input->post('paytype') == 0 || $this->input->post('paytype') == 1) ? ($amount>0 ? $amount : 0) : 0);
		$amount-=$this->input->post('capital');

		$payloan_data = array(
			'loan_id' => $loan_id,
			'paytype' => (!empty($this->input->post('paytype')) ? $this->input->post('paytype') : 0),
			'paydate' => $paydate_formatter->format('Y-m-d'),
			'observations' => $this->input->post('observations'),
			'amount' => $this->input->post('amount'),
			'capital' => $capital,
			'interest' => $interest,
			'cumulate_interest' => $cumulate_interest
		);

		if($capital > 0)
		{

			$cash_concept = $this->Cash_concept->get_info_by_code('00-01-01');

			$cash_daily_data[] = array(
				'cash_concept_id' => $cash_concept->cash_concept_id,
				'cashup_id' => $this->input->post('cashup_id'),
				'cash_book_id' => $this->input->post('cash_book_id'),
				'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
				'movementdate' => date('Y-m-d H:i:s'),
				'description' => $this->input->post('observations'),
				'currency' => CURRENCY,
				'amount' => parse_decimals($capital),
				'table_reference' => 'payment_loans'
			);
		}

		if($interest > 0)
		{
			$cash_concept = $this->Cash_concept->get_info_by_code('00-01-02');

			$cash_daily_data[] = array(
				'cash_concept_id' => $cash_concept->cash_concept_id,
				'cashup_id' => $this->input->post('cashup_id'),
				'cash_book_id' => $this->input->post('cash_book_id'),
				'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
				'movementdate' => date('Y-m-d H:i:s'),
				'description' => $this->input->post('observations'),
				'currency' => CURRENCY,
				'amount' => parse_decimals($interest),
				'table_reference' => 'payment_loans'
			);
		}

		if($this->Loan->save_payment($payloan_data, $cash_daily_data, $loan_id))
		{
			$payloan_data = $this->xss_clean($payloan_data);
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('loans_credits_successful_updating_loan'), 'id' => $loan_id));
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('loans_credits_error_adding_updating_loan') . ' ' . $loan_data['paydate'], 'id' => -1));
		}
	}

	public function payment_credit($credit_id)
	{

		$paydate = $this->input->post('paydate');
		$paydate_formatter = date_create_from_format($this->config->item('dateformat'), $paydate);

		$amount = $this->input->post('amount');
		$interest = (($this->input->post('paytype') == 0 || $this->input->post('paytype') == 2) ? ($this->input->post('interest') >= $amount ? $amount : $this->input->post('interest')) : 0);
		$cumulate_interest = (($this->input->post('paytype') == 0 || $this->input->post('paytype') == 2) ? ($this->input->post('interest') >= $amount ? $this->input->post('interest')-$amount : 0) : $this->input->post('cumulate_interest'));
		$amount-=($this->input->post('paytype') == 0 || $this->input->post('paytype') == 2 ? $this->input->post('interest') : 0);
		$capital = (($this->input->post('paytype') == 0 || $this->input->post('paytype') == 1) ? ($amount>0 ? $amount : 0) : 0);
		$amount-=$this->input->post('capital');

		$paycredit_data = array(
			'credit_id' => $credit_id,
			'paytype' => (!empty($this->input->post('paytype')) ? $this->input->post('paytype') : 0),
			'paydate' => $paydate_formatter->format('Y-m-d'),
			'observations' => $this->input->post('observations'),
			'amount' => $this->input->post('amount'),
			'capital' => $capital,
			'interest' => $interest,
			'cumulate_interest' => $cumulate_interest
		);

		if($capital > 0)
		{

			$cash_concept = $this->Cash_concept->get_info_by_code('00-01-03');

			$cash_daily_data[] = array(
				'cash_concept_id' => $cash_concept->cash_concept_id,
				'cashup_id' => $this->input->post('cashup_id'),
				'cash_book_id' => $this->input->post('cash_book_id'),
				'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
				'movementdate' => date('Y-m-d H:i:s'),
				'description' => $this->input->post('observations'),
				'currency' => CURRENCY,
				'amount' => parse_decimals($capital),
				'table_reference' => 'payment_credits'
			);
		}

		if($interest > 0)
		{
			$cash_concept = $this->Cash_concept->get_info_by_code('00-01-04');

			$cash_daily_data[] = array(
				'cash_concept_id' => $cash_concept->cash_concept_id,
				'cashup_id' => $this->input->post('cashup_id'),
				'cash_book_id' => $this->input->post('cash_book_id'),
				'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
				'movementdate' => date('Y-m-d H:i:s'),
				'description' => $this->input->post('observations'),
				'currency' => CURRENCY,
				'amount' => parse_decimals($interest),
				'table_reference' => 'payment_credits'
			);
		}

		if($this->Credit->save_payment($paycredit_data, $cash_daily_data, $credit_id))
		{
			$paycredit_data = $this->xss_clean($paycredit_data);
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('loans_credits_successful_updating_credit'), 'id' => $credit_id));
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->Credit->get_log()."<br>".$this->lang->line('loans_credits_error_adding_updating_credit') . ' ' . $credit_data['paydate'], 'id' => -1));
		}
	}

	public function payment_summary($person_id)
	{

		$paydate = $this->input->post('paydate');
		$paydate_formatter = date_create_from_format($this->config->item('dateformat'), $paydate);

		$amount = $this->input->post('amount');

		if($this->input->post('table')=="l")
		{
			$success = FALSE;

			foreach ($this->Loan->get_open_loan($person_id) as $open_info) {
				if($amount>0)
				{
					$capital = ($amount >= $open_info->capital ? $open_info->capital : $amount-$open_info->interest);
					$interest = ($amount >= $open_info->interest ? $open_info->interest : $amount);
					$cumulate_interest = ($open_info->interest >= $amount ? $open_info->interest-$amount : 0);

					$payloan_data = array(
						'loan_id' => $open_info->loan_id,
						'paytype' => (!empty($this->input->post('paytype')) ? $this->input->post('paytype') : 0),
						'paydate' => $paydate_formatter->format('Y-m-d'),
						'observations' => $this->input->post('observations'),
						'amount' => ($amount >= $open_info->balance ? $open_info->balance : $amount),
						'capital' => $capital,
						'interest' => $interest,
						'cumulate_interest' => $cumulate_interest
					);

					if($capital > 0)
					{

						$cash_concept = $this->Cash_concept->get_info_by_code('00-01-01');

						$cash_daily_data[] = array(
							'cash_concept_id' => $cash_concept->cash_concept_id,
							'cashup_id' => $this->input->post('cashup_id'),
							'cash_book_id' => $this->input->post('cash_book_id'),
							'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
							'movementdate' => date('Y-m-d H:i:s'),
							'description' => $this->input->post('observations'),
							'currency' => CURRENCY,
							'amount' => parse_decimals($capital),
							'table_reference' => 'payment_loans'
						);
					}

					if($interest > 0)
					{
						$cash_concept = $this->Cash_concept->get_info_by_code('00-01-02');

						$cash_daily_data[] = array(
							'cash_concept_id' => $cash_concept->cash_concept_id,
							'cashup_id' => $this->input->post('cashup_id'),
							'cash_book_id' => $this->input->post('cash_book_id'),
							'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
							'movementdate' => date('Y-m-d H:i:s'),
							'description' => $this->input->post('observations'),
							'currency' => CURRENCY,
							'amount' => parse_decimals($interest),
							'table_reference' => 'payment_loans'
						);
					}

					$success = $this->Loan->save_payment($payloan_data, $cash_daily_data, $open_info->loan_id);

					$amount -= $open_info->balance;
				}
				else
				{
					break;
				}
			}

			if($success)
			{
				$payloan_data = $this->xss_clean($payloan_data);
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('loans_credits_successful_updating_loan'), 'id' => $loan_id));
			}
			else//failure
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('loans_credits_error_adding_updating_loan') . ' ' . $loan_data['paydate'], 'id' => -1));
			}
		}
		else
		{
			$success = FALSE;

			foreach ($this->Credit->get_open_credit($person_id) as $open_info) {
				if($amount>0)
				{

					$capital = ($amount >= $open_info->capital ? $open_info->capital : $amount-$open_info->interest);
					$interest = ($amount >= $open_info->interest ? $open_info->interest : $amount);
					$cumulate_interest = ($open_info->interest >= $amount ? $open_info->interest-$amount : 0);

					$paycredit_data = array(
						'credit_id' => $open_info->credit_id,
						'paytype' => (!empty($this->input->post('paytype')) ? $this->input->post('paytype') : 0),
						'paydate' => $paydate_formatter->format('Y-m-d'),
						'observations' => $this->input->post('observations'),
						'amount' => ($amount >= $open_info->balance ? $open_info->balance : $amount),
						'capital' => $capital,
						'interest' => $interest,
						'cumulate_interest' => $cumulate_interest
					);

					if($capital > 0)
					{

						$cash_concept = $this->Cash_concept->get_info_by_code('00-01-03');

						$cash_daily_data[] = array(
							'cash_concept_id' => $cash_concept->cash_concept_id,
							'cashup_id' => $this->input->post('cashup_id'),
							'cash_book_id' => $this->input->post('cash_book_id'),
							'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
							'movementdate' => date('Y-m-d H:i:s'),
							'description' => $this->input->post('observations'),
							'currency' => CURRENCY,
							'amount' => parse_decimals($capital),
							'table_reference' => 'payment_credits'
						);
					}

					if($interest > 0)
					{
						$cash_concept = $this->Cash_concept->get_info_by_code('00-01-04');

						$cash_daily_data[] = array(
							'cash_concept_id' => $cash_concept->cash_concept_id,
							'cashup_id' => $this->input->post('cashup_id'),
							'cash_book_id' => $this->input->post('cash_book_id'),
							'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
							'movementdate' => date('Y-m-d H:i:s'),
							'description' => $this->input->post('observations'),
							'currency' => CURRENCY,
							'amount' => parse_decimals($interest),
							'table_reference' => 'payment_credits'
						);
					}

					$success = $this->Credit->save_payment($paycredit_data, $cash_daily_data, $open_info->credit_id);

					$amount -= $open_info->balance;
				}
				else
				{
					break;
				}
			}

			if($success)
			{
				$paycredit_data = $this->xss_clean($paycredit_data);
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('loans_credits_successful_updating_credit'), 'id' => $person_id));
			}
			else//failure
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('loans_credits_error_adding_updating_credit') . ' ' . $this->input->post('name'), 'id' => -1));
			}
		}
	}

	public function delete()
	{
		$loans_credit_to_delete = $this->input->post('ids');

		if($this->input->post('confirm')=='Y'){
			if($this->input->post('table')=='loans')
			{
				if($this->Loan->delete_list($loans_credit_to_delete))
				{
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('loans_credits_successful_deleted') . ' ' . count($loans_credit_to_delete) . ' ' . $this->lang->line('loans_credits_one_or_multiple')));
				}
				else
				{
					echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('loans_credits_cannot_be_deleted')));
				}
			}
			else
			{
				if($this->Credit->delete_list($loans_credit_to_delete))
				{
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('loans_credits_successful_deleted') . ' ' . count($loans_credit_to_delete) . ' ' . $this->lang->line('loans_credits_one_or_multiple')));
				}
				else
				{
					echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('loans_credits_cannot_be_deleted')));
				}
			}
		}
		else{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('loans_credits_none_to_be_deleted')));
		}
	}

	public function delete_loan($loan_id)
	{

		$data['loan_info'] = $this->Loan->get_info($loan_id);

		$this->load->view("loans_credits/delete_loan", $data);
	}

	public function delete_credit($credit_id)
	{
		
		$data['credit_info'] = $this->Credit->get_info($credit_id);

		$this->load->view("loans_credits/delete_credit", $data);
	}

	public function see_loan($loan_id)
	{
		
		$data['loan_credit_info'] = $this->Loan->get_info($loan_id);

		$data['table'] = "l";

		$data['table_headers'] = $this->xss_clean(get_payment_loan_credit_manage_table_headers("a"));

		$data['see_type'] = "a";

		$data['show_buttom'] = FALSE;

		$this->load->view('loans_credits/manage_payment', $data);
	}

	public function see_credit($credit_id)
	{
		
		$data['loan_credit_info'] = $this->Credit->get_info($credit_id);

		$data['table'] = "c";

		$data['table_headers'] = $this->xss_clean(get_payment_loan_credit_manage_table_headers("a"));

		$data['see_type'] = "a";

		$data['show_buttom'] = FALSE;

		$this->load->view('loans_credits/manage_payment', $data);
	}

	public function seepay_loan($loan_id)
	{
		
		$data['loan_credit_info'] = $this->Loan->get_info($loan_id);

		$data['table'] = "l";

		$data['table_headers'] = $this->xss_clean(get_payment_loan_credit_manage_table_headers("p"));

		$data['see_type'] = "p";

		$data['show_buttom'] = TRUE;

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view('loans_credits/manage_payment', $data);
	}

	public function seepay_credit($credit_id)
	{
		
		$data['loan_credit_info'] = $this->Credit->get_info($credit_id);

		$data['table'] = "c";

		$data['table_headers'] = $this->xss_clean(get_payment_loan_credit_manage_table_headers("p"));

		$data['see_type'] = "p";

		$data['show_buttom'] = TRUE;

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view('loans_credits/manage_payment', $data);
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_payment($table,$type,$id)
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$data_rows = array();

		//	For cumulate values
		$amortization_capital = 0;
		$amortization_interest = 0;
		$capital = 0;
		$interest = 0;
		$balance = 0;
		$count = 0;

		//	Search Loan
		if($table=="l")
		{
			$loans = $this->Loan->payment_search($id,$type,$search, $limit, $offset, $sort, $order);
			$total_rows = $this->Loan->get_payment_found_rows($id,$type,$search);
			//	Loan Data
			foreach($loans->result() as $loan)
			{
				if($type=="a")
				{
					$data_rows[] = $this->xss_clean(get_payment_loan_data_row($type,$loan));
				}
				else
				{
					if($count == 0)
					{
						$capital = $loan->amt_capital;
						$percent = $loan->percent;
						$interest = $loan->amt_interest;
					}
					$amortization_capital = $loan->capital;
					$amortization_interest = $loan->interest;

					$capital-=$amortization_capital;
					$interest=$loan->cumulate_interest;
					$balance = $capital+$interest;

					$loan->amortization_capital = $amortization_capital;
					$loan->amortization_interest = $amortization_interest;
					$loan->capital = $capital;
					$loan->interest = $interest;
					$loan->balance = $balance;
					$data_rows[] = $this->xss_clean(get_payment_loan_data_row($type,$loan));
					$count++;
				}
			}
		}
		//	Search Credit
		else
		{
			$credits = $this->Credit->payment_search($id,$type,$search, $limit, $offset, $sort, $order);
			$total_rows += $this->Credit->get_payment_found_rows($id,$type,$search);
			//	Credit Data
			foreach($credits->result() as $credit)
			{
				if($type=="a")
				{
					$data_rows[] = $this->xss_clean(get_payment_credit_data_row($type,$credit));
				}
				else
				{
					if($count == 0)
					{
						$capital = $credit->amt_capital;
						$percent = $credit->percent;
						$interest = $credit->amt_interest;
					}
					$amortization_capital = $credit->capital;
					$amortization_interest = $credit->interest;

					$capital-=$amortization_capital;
					$interest=$credit->cumulate_interest;
					$balance = $capital+$interest;

					$credit->amortization_capital = $amortization_capital;
					$credit->amortization_interest = $amortization_interest;
					$credit->capital = $capital;
					$credit->interest = $interest;
					$credit->balance = $balance;
					$data_rows[] = $this->xss_clean(get_payment_credit_data_row($type,$credit));
					$count++;
				}
			}
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_balance($table,$id,$paytype)
	{
		if($table == "l")
		{
			$data_row = $this->Loan->get_balance($id,$paytype);
			foreach(get_object_vars($data_row) as $property => $value)
			{
				$data_row->$property = $this->xss_clean($value);
			}
			echo json_encode($data_row);
		}
		else
		{
			$data_row = $this->Credit->get_balance($id,$paytype);
			foreach(get_object_vars($data_row) as $property => $value)
			{
				$data_row->$property = $this->xss_clean($value);
			}
			echo json_encode($data_row);
		}
	}


}
?>
