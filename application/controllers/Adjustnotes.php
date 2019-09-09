<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Adjustnotes extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('adjustnotes');
	}

	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_adjustnote_manage_table_headers());

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view('adjustnotes/manage', $data);
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

		$adjustnotes = $this->Adjustnote->search($search, $cashups->cash_book_id, $limit, $offset, $sort, $order);
		$total_rows = $this->Adjustnote->get_found_rows($search,$cashups->cash_book_id);

		$data_rows = array();
		foreach($adjustnotes->result() as $adjustnote)
		{
			$data_rows[] = $this->xss_clean(get_adjustnote_data_row($adjustnote));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$data_row = $this->xss_clean(get_adjustnote_data_row($this->Adjustnote->get_info($row_id,$cash_book_id)));

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

	public function view($adjustnote_id = -1)
	{
		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$data['adjustnote_info'] = $this->Adjustnote->get_info($adjustnote_id,$data['cashups']->cash_book_id);

		if(empty($data['adjustnote_info']->documentdate))
		{
			$data['adjustnote_info']->documentdate = date('Y-m-d H:i:s');
		}

		$cash_concept_an = $this->Cash_concept->get_info_by_code('02-02');

		$cash_concept = array('-1' => $this->lang->line('common_none_selected_text'));
		foreach($this->Cash_concept->get_parent_all($cash_concept_an->cash_concept_id)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}
		$data['cash_concepts'] = $cash_concept;
		$data['selected_cash_concept'] = $data['adjustnote_info']->cash_concept_id;

		$this->load->view("adjustnotes/form", $data);
	}

	public function save($adjustnote_id = -1)
	{
		$this->Adjustnote->set_log("<< Start Log >>");

		$documentdate = $this->input->post('documentdate');
		$documentdate_formatter = date_create_from_format($this->config->item('dateformat')." ".$this->config->item('timeformat'), $documentdate);

		if($adjustnote_id != -1)
		{
			$documentno = $this->input->post('documentno');
		}
		else
		{
			$documentno = ($this->config->item('adjustnote_number_automatic') == '0' ? $this->input->post('documentno') : $this->Appconfig->acquire_save_next_doctype_sequence($this->config->item('adjustnote_doctype_sequence')));
		}

		$adjustnote_data = array(
			'documentno' => $documentno,
			'documentdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'cash_book_id' => $this->input->post('cash_book_id'),
			'person_id' => $this->input->post('person_id'),
			'cash_concept_id' => $this->input->post('cash_concept_id'),
			'description' => $this->input->post('description'),
			'amount' => $this->input->post('amount'),
			'movementtype' => $this->input->post('movementtype'),
			'trx_number' => (!empty($this->input->post('trx_number')) ? $this->input->post('trx_number') : NULL)
		);

		$cash_concept = $this->Cash_concept->get_info($this->input->post('cash_concept_id'));

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
			'table_reference' => 'adjustnotes'
		);

		if($this->Adjustnote->save($adjustnote_data, $cash_daily_data, $adjustnote_id))
		{
			$adjustnote_data = $this->xss_clean($adjustnote_data);

			// New adjustnote_id
			if($adjustnote_id == -1)
			{
				$this->Adjustnote->set_log("<< End Log >>");
				//	Use log 
				//	$this->Adjustnote->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('adjustnotes_successful_adding'), 'id' => $adjustnote_data['adjustnote_id']));
			}
			else // Existing Adjustnote
			{
				$this->Adjustnote->set_log($this->db->last_query());
				$this->Adjustnote->set_log("<< End Log >>");
				//	Use log 
				//	$this->Adjustnote->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('adjustnotes_successful_updating'), 'id' => $adjustnote_id));
			}
		}
		else//failure
		{
			if($adjustnote_id != -1)
			{
				$this->Adjustnote->set_log($this->db->last_query());
			}
			$this->Adjustnote->set_log("<< End Log >>");
			//	Use log 
			//	$this->Adjustnote->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->Adjustnote->get_log()."<br>".$this->lang->line('adjustnotes_error_adding_updating') . ' ' . $adjustnote_data['documentno'], 'id' => -1));
		}
	}

	public function delete_adjustnote($adjustnote_id)
	{
		$cashups = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));
		$data['adjustnote_info'] = $this->Adjustnote->get_info($adjustnote_id,$cashups->cash_book_id);

		$this->load->view("adjustnotes/delete", $data);
	}

	public function delete()
	{
		$adjustnote_to_delete = $this->input->post('ids');
		
		if($this->input->post('confirm')=='Y'){
			if($this->Adjustnote->delete($adjustnote_to_delete,CURRENCY))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('adjustnotes_successful_deleted') . ' ' . count($adjustnote_to_delete) . ' ' . $this->lang->line('adjustnotes_one_or_multiple')));
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('adjustnotes_cannot_be_deleted')));
			}
		}
		else{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('adjustnotes_none_to_be_deleted')));
		}
	}
}
?>
