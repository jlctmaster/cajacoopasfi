<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Creditnotes extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('creditnotes');
	}

	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_creditnote_manage_table_headers());

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view('creditnotes/manage', $data);
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search()
	{

		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$creditnotes = $this->Creditnote->search($search, $cashups->cash_book_id, $limit, $offset, $sort, $order);
		$total_rows = $this->Creditnote->get_found_rows($search,$cashups->cash_book_id);

		$data_rows = array();
		foreach($creditnotes->result() as $creditnote)
		{
			$data_rows[] = $this->xss_clean(get_creditnote_data_row($creditnote));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$data_row = $this->xss_clean(get_creditnote_data_row($this->Creditnote->get_info($row_id,$cash_book_id)));

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

	public function view($creditnote_id = -1)
	{
		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$data['creditnote_info'] = $this->Creditnote->get_info($creditnote_id,$data['cashups']->cash_book_id);

		if(empty($data['creditnote_info']->documentdate))
		{
			$data['creditnote_info']->documentdate = date('Y-m-d H:i:s');
		}

		$this->load->view("creditnotes/form", $data);
	}

	public function save($creditnote_id = -1)
	{
		$this->Creditnote->set_log("<< Start Log >>");

		$documentdate = $this->input->post('documentdate');
		$documentdate_formatter = date_create_from_format($this->config->item('dateformat')." ".$this->config->item('timeformat'), $documentdate);

		if($creditnote_id != -1)
		{
			$documentno = $this->input->post('documentno');
		}
		else
		{
			$documentno = ($this->config->item('creditnote_number_automatic') == '0' ? $this->input->post('documentno') : $this->Appconfig->acquire_save_next_doctype_sequence($this->config->item('creditnote_doctype_sequence')));
		}

		$creditnote_data = array(
			'documentno' => $documentno,
			'documentdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'cash_book_id' => $this->input->post('cash_book_id'),
			'person_id' => $this->input->post('person_id'),
			'description' => $this->input->post('description'),
			'amount' => $this->input->post('amount'),
			'movementtype' => $this->input->post('movementtype'),
			'trx_number' => (!empty($this->input->post('trx_number')) ? $this->input->post('trx_number') : NULL)
		);

		$cash_concept = $this->Cash_concept->get_info_by_code('00-02-02');

		$cash_daily_data[] = array(
			'cash_concept_id' => $cash_concept->cash_concept_id,
			'cashup_id' => $this->input->post('cashup_id'),
			'cash_book_id' => $this->input->post('cash_book_id'),
			'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
			'movementdate' => date('Y-m-d H:i:s'),
			'description' => $this->input->post('description'),
			'isbankmovement' => ($this->input->post('movementtype') == "B" ? 1 : 0),
			'currency' => CURRENCY,
			'amount' => parse_decimals($this->input->post('amount')),
			'table_reference' => 'creditnotes'
		);

		if($this->Creditnote->save($creditnote_data, $cash_daily_data, $creditnote_id))
		{
			$creditnote_data = $this->xss_clean($creditnote_data);

			// New creditnote_id
			if($creditnote_id == -1)
			{
				$this->Creditnote->set_log("<< End Log >>");
				//	Use log 
				//	$this->Creditnote->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('creditnotes_successful_adding'), 'id' => $creditnote_data['creditnote_id']));
			}
			else // Existing Creditnote
			{
				$this->Creditnote->set_log($this->db->last_query());
				$this->Creditnote->set_log("<< End Log >>");
				//	Use log 
				//	$this->Creditnote->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('creditnotes_successful_updating'), 'id' => $creditnote_id));
			}
		}
		else//failure
		{
			if($creditnote_id != -1)
			{
				$this->Creditnote->set_log($this->db->last_query());
			}
			$this->Creditnote->set_log("<< End Log >>");
			//	Use log 
			//	$this->Creditnote->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->Creditnote->get_log()."<br>".$this->lang->line('creditnotes_error_adding_updating') . ' ' . $creditnote_data['documentno'], 'id' => -1));
		}
	}

	public function delete_creditnote($creditnote_id)
	{
		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));
		$data['creditnote_info'] = $this->Creditnote->get_info($creditnote_id,$cashups->cash_book_id);

		$this->load->view("creditnotes/delete", $data);
	}

	public function delete()
	{
		$creditnote_to_delete = $this->input->post('ids');
		
		if($this->input->post('confirm')=='Y'){
			if($this->Creditnote->delete($creditnote_to_delete,CURRENCY))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('creditnotes_successful_deleted') . ' ' . count($creditnote_to_delete) . ' ' . $this->lang->line('creditnotes_one_or_multiple')));
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('creditnotes_cannot_be_deleted')));
			}
		}
		else{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('creditnotes_none_to_be_deleted')));
		}
	}
}
?>
