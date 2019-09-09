<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Invoices extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('invoices');
	}

	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_invoice_manage_table_headers());

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view('invoices/manage', $data);
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

		$invoices = $this->Invoice->search($search,$cashups->cash_book_id, $limit, $offset, $sort, $order);
		$total_rows = $this->Invoice->get_found_rows($search,$cashups->cash_book_id);

		$data_rows = array();
		foreach($invoices->result() as $invoice)
		{
			$data_rows[] = $this->xss_clean(get_invoice_data_row($invoice));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));
		$data_row = $this->xss_clean(get_invoice_data_row($this->Invoice->get_info($row_id,$cashups->cash_book_id)));

		echo json_encode($data_row);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_customers()
	{
		$suggestions = $this->xss_clean($this->Customer->get_search_suggestions($this->input->get('term'),TRUE));

		echo json_encode($suggestions);
	}

	public function view($invoice_id = -1)
	{

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));
		$data['invoice_info'] = $this->Invoice->get_info($invoice_id,$data['cashups']->cash_book_id);
		$data['lineinvoice_info'] = $this->Invoice->get_detail_info($invoice_id);

		if(empty($data['invoice_info']->documentdate))
		{
			$data['invoice_info']->documentdate = date('Y-m-d H:i:s');
		}
		if(empty($data['invoice_info']->discount))
		{
			$data['invoice_info']->discount = 0;
			$data['invoice_info']->discountamt = 0;
		}

		$data['action'] = "save";

		$data['readonlyform'] = FALSE;

		$this->load->view("invoices/form", $data);
	}

	public function see($invoice_id)
	{
		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));
		$data['invoice_info'] = $this->Invoice->get_info($invoice_id,$data['cashups']->cash_book_id);
		$data['lineinvoice_info'] = $this->Invoice->get_detail_info($invoice_id);

		$data['action'] = "print";

		$data['readonlyform'] = TRUE;

		$this->load->view("invoices/form", $data);
	}

	public function save($invoice_id = -1)
	{
		$this->Invoice->set_log("<< Start Log >>");

		$documentdate = $this->input->post('documentdate');
		$documentdate_formatter = date_create_from_format($this->config->item('dateformat'). " " .$this->config->item('timeformat'), $documentdate);

		if($adjustnote_id != -1)
		{
			$serieno = $this->input->post('serieno');
		}
		else
		{
			$serieno = ($this->config->item('invoice_number_automatic') == '0' ? $this->input->post('serieno') : $this->Appconfig->acquire_save_next_doctype_sequence($this->config->item('invoice_doctype_sequence')));
		}

		$invoice_data = array(
			'serieno' => $serieno,
			'documentdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'person_id' => $this->input->post('person_id'),
			'cash_book_id' => $this->input->post('cash_book_id'),
			'description' => $this->input->post('description'),
			'subtotal' => parse_decimals($this->input->post('subtotal')),
			'discount' => parse_decimals($this->input->post('discount')),
			'discountamt' => parse_decimals($this->input->post('discountamt')),
			'tax' => parse_decimals($this->input->post('tax')),
			'taxamt' => parse_decimals($this->input->post('taxamt')),
			'totalamt' => parse_decimals($this->input->post('totalamt')),
			'movementtype' => $this->input->post('movementtype'),
			'trx_number' => ($this->input->post('movementtype') == "B" ? $this->input->post('trx_number') : NULL)
		);

		for ($i=0; $i < count($this->input->post('details')); $i++) {
			$line_data[] = array(
				'quantity' => parse_decimals($this->input->post('qtys')[$i]),
				'detail' => $this->input->post('details')[$i],
				'price' => parse_decimals($this->input->post('prices')[$i]),
				'amount' => parse_decimals($this->input->post('amounts')[$i])
			);
		}

		$cash_concept = $this->Cash_concept->get_info_by_code('00-01-06');

		$cash_daily_data[] = array(
			'cash_concept_id' => $cash_concept->cash_concept_id,
			'cashup_id' => $this->input->post('cashup_id'),
			'cash_book_id' => $this->input->post('cash_book_id'),
			'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
			'movementdate' => date('Y-m-d H:i:s'),
			'description' => $this->input->post('description'),
			'currency' => CURRENCY,
			'amount' => parse_decimals($this->input->post('totalamt')),
			'table_reference' => 'invoices'
		);

		if($this->Invoice->save($invoice_data, $line_data, $cash_daily_data, $invoice_id))
		{
			$invoice_data = $this->xss_clean($invoice_data);

			// New invoice_id
			if($invoice_id == -1)
			{
				$this->Invoice->set_log("<< End Log >>");
				//	Use log 
				//	$this->Invoice->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('invoices_successful_adding'), 'id' => $invoice_data['invoice_id']));
			}
			else // Existing Invoice
			{
				$this->Invoice->set_log($this->db->last_query());
				$this->Invoice->set_log("<< End Log >>");
				//	Use log 
				//	$this->Invoice->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('invoices_successful_updating'), 'id' => $invoice_id));
			}
		}
		else//failure
		{
			$this->Invoice->set_log("<< End Log >>");
			//	Use log 
			//	$this->Invoice->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->Invoice->get_log()."<br>".$this->lang->line('invoices_error_adding_updating') . ' ' . $invoice_data['serieno'] . '-' . $invoice_data['documentno'], 'id' => -1));
		}
	}

	public function delete_invoice($invoice_id)
	{
		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));
		$data['invoice_info'] = $this->Invoice->get_info($invoice_id,$cashups->cash_book_id);

		$this->load->view("invoices/delete", $data);
	}

	public function delete()
	{
		$invoice_to_delete = $this->input->post('ids');
		
		if($this->input->post('confirm')=='Y'){
			if($this->Invoice->delete($invoice_to_delete,$this->input->post('currency')))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('invoices_successful_deleted') . ' ' . count($invoice_to_delete) . ' ' . $this->lang->line('invoices_one_or_multiple')));
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('invoices_cannot_be_deleted')));
			}
		}
		else{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('invoices_none_to_be_deleted')));
		}
	}
}
?>
