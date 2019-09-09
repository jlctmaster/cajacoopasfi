<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Vouchers extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('vouchers');
	}

	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_voucher_manage_table_headers());

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view('vouchers/manage', $data);
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

		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$vouchers = $this->Voucher->search($search, $cashups->cash_book_id,  $limit, $offset, $sort, $order);
		$total_rows = $this->Voucher->get_found_rows($search, $cashups->cash_book_id);

		$data_rows = array();
		foreach($vouchers->result() as $voucher)
		{
			$data_rows[] = $this->xss_clean(get_voucher_data_row($voucher));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));
		$data_row = $this->xss_clean(get_voucher_data_row($this->Voucher->get_info($row_id, $cashups->cash_book_id)));

		echo json_encode($data_row);
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

	public function view($voucher_id = -1)
	{
		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));
		$data['voucher_info'] = $this->Voucher->get_info($voucher_id, $data['cashups']->cash_book_id);

		if(empty($data['voucher_info']->voucherdate))
		{
			$data['voucher_info']->voucherdate = date('Y-m-d');
		}

		$this->load->view("vouchers/form", $data);
	}

	public function save($voucher_id = -1)
	{
		$this->Voucher->set_log("<< Start Log >>");

		$voucherdate = $this->input->post('voucherdate');
		$voucherdate_formatter = date_create_from_format($this->config->item('dateformat'), $voucherdate);

		if($voucher_id != -1)
		{
			$voucher_number = $this->input->post('voucher_number');
		}
		else
		{
			$voucher_number = ($this->config->item('voucher_number_automatic') == '0' ? $this->input->post('voucher_number') : $this->Appconfig->acquire_save_next_doctype_sequence($this->config->item('voucher_doctype_sequence')));
		}

		$voucher_data = array(
			'voucher_type' => $this->input->post('voucher_type'),
			'voucher_number' => $voucher_number,
			'voucherdate' => $voucherdate_formatter->format('Y-m-d'),
			'cash_book_id' => $this->input->post('cash_book_id'),
			'person_id' => $this->input->post('person_id'),
			'detail' => $this->input->post('detail'),
			'amount' => $this->input->post('amount'),
			'cash_type' => $this->input->post('cash_type'),
			'trx_number' => $this->input->post('trx_number')
		);

		if($this->Voucher->save($voucher_data, $voucher_id))
		{
			$voucher_data = $this->xss_clean($voucher_data);

			// New voucher_id
			if($voucher_id == -1)
			{
				$this->Voucher->set_log("<< End Log >>");
				//	Use log 
				//	$this->Voucher->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('vouchers_successful_adding'), 'id' => $voucher_data['voucher_id']));
			}
			else // Existing Voucher
			{
				$this->Voucher->set_log($this->db->last_query());
				$this->Voucher->set_log("<< End Log >>");
				//	Use log 
				//	$this->Voucher->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('vouchers_successful_updating'), 'id' => $voucher_id));
			}
		}
		else//failure
		{
			if($voucher_id != -1)
			{
				$this->Voucher->set_log($this->db->last_query());
			}
			$this->Voucher->set_log("<< End Log >>");
			//	Use log 
			//	$this->Voucher->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('vouchers_error_adding_updating') . ' ' . $voucher_data['voucher_number'], 'id' => -1));
		}
	}

	public function pay($voucher_id)
	{
		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));
		$data['voucher_info'] = $this->Voucher->get_info($voucher_id,$cashups->cash_book_id);

		$data['voucher_info']->paydate = date('Y-m-d');

		$this->load->view("vouchers/payform", $data);
	}

	public function payment($voucher_id)
	{
		$this->Voucher->set_log("<< Start Log >>");

		$paydate = $this->input->post('paydate');
		$paydate_formatter = date_create_from_format($this->config->item('dateformat'), $paydate);

		$payvoucher_data = array(
			'voucher_id' => $voucher_id,
			'paydate' => $paydate_formatter->format('Y-m-d'),
			'observations' => $this->input->post('observations'),
			'amount' => $this->input->post('amount')
		);

		if($this->Voucher->save_payment($payvoucher_data))
		{
			$payvoucher_data = $this->xss_clean($payvoucher_data);
			$this->Voucher->set_log("<< End Log >>");
			//	Use log 
			//	$this->Voucher->get_log()."<br>".
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('vouchers_successful_updating'), 'id' => $voucher_id));
		}
		else//failure
		{
			$this->Voucher->set_log("<< End Log >>");
			//	Use log 
			//	$this->Voucher->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->Voucher->get_log()."<br>".$this->lang->line('vouchers_error_adding_updating') . ' ' . $payvoucher_data['payment_voucher_id'], 'id' => -1));
		}
	}

	public function see($voucher_id)
	{
		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));
		$data['voucher_info'] = $this->Voucher->get_info($voucher_id,$cashups->cash_book_id);

		$data['table_headers'] = $this->xss_clean(get_payment_voucher_manage_table_headers());

		$this->load->view('vouchers/manage_payment', $data);
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_payment($voucher_id)
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$payments = $this->Voucher->search_payment($voucher_id,$search, $limit, $offset, $sort, $order);
		$total_rows = $this->Voucher->get_found_payment_rows($voucher_id,$search);

		$data_rows = array();
		foreach($payments->result() as $payment)
		{
			$data_rows[] = $this->xss_clean(get_payment_voucher_data_row($payment));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function delete_voucher($voucher_id)
	{
		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));
		$data['voucher_info'] = $this->Voucher->get_info($voucher_id,$cash_book_id);

		$this->load->view("vouchers/delete", $data);
	}

	public function delete()
	{
		$voucher_to_delete = $this->input->post('ids');
		
		if($this->input->post('confirm')=='Y'){
			if($this->Voucher->delete_list($voucher_to_delete))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('vouchers_successful_deleted') . ' ' . count($voucher_to_delete) . ' ' . $this->lang->line('vouchers_one_or_multiple')));
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('vouchers_cannot_be_deleted')));
			}
		}
		else{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('vouchers_none_to_be_deleted')));
		}
	}
}
?>
