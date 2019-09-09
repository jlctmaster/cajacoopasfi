<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Doctypesequences extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('doctypesequences');
	}

	public function index()
	{
		 $data['table_headers'] = $this->xss_clean(get_doctypesequence_manage_table_headers());

		 $this->load->view('doctypesequences/manage', $data);
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

		$doctypesequences = $this->Doctypesequence->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Doctypesequence->get_found_rows($search);

		$data_rows = array();
		foreach($doctypesequences->result() as $doctypesequence)
		{
			$data_rows[] = $this->xss_clean(get_doctypesequence_data_row($doctypesequence));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_doctypesequence_data_row($this->Doctypesequence->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($doctypesequence_id = -1)
	{
		$data['doctypesequence_info'] = $this->Doctypesequence->get_info($doctypesequence_id);

		$data['doctypes'] = array(
			'-1' => $this->lang->line('common_none_selected_text'),
			'adjustnotes0' => $this->lang->line('doctypesequences_doctype_adjustnote'),
			'costs0' => $this->lang->line('doctypesequences_doctype_cost'),
			'costs1' => $this->lang->line('doctypesequences_doctype_cost_overallcash'),
			'creditnotes0' => $this->lang->line('doctypesequences_doctype_creditnote'),
			'expenses0' => $this->lang->line('doctypesequences_doctype_expense'),
			'incomes0' => $this->lang->line('doctypesequences_doctype_income'),
			'incomes1' => $this->lang->line('doctypesequences_doctype_income_overallcash'),
			'invoices0' => $this->lang->line('doctypesequences_doctype_invoice'),
			'ticketsales0' => $this->lang->line('doctypesequences_doctype_ticketsale'),
			'vouchers0' => $this->lang->line('doctypesequences_doctype_voucher'),
			);

		$data['selected_doctype'] = (!empty($data['doctypesequence_info']) ? $data['doctypesequence_info']->doctype : -1);

		$this->load->view("doctypesequences/form", $data);
	}

	public function save($sequence_id = -1)
	{
		$doctypesequence_data = array(
			'name' => $this->input->post('name'),
			'prefix' => $this->input->post('prefix'),
			'suffix' => $this->input->post('suffix'),
			'next_sequence' => $this->input->post('next_sequence'),
			'number_incremental' => $this->input->post('number_incremental'),
			'doctype' => substr($this->input->post('doctype'),0,-1),
			'is_cashup' => $this->input->post('is_cashup') != NULL
		);

		if($this->Doctypesequence->save($doctypesequence_data, $sequence_id))
		{
			$doctypesequence_data = $this->xss_clean($doctypesequence_data);

			// New doctypesequence_id
			if($sequence_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('doctypesequences_successful_adding'), 'id' => $doctypesequence_data['sequence_id']));
			}
			else // Existing Doctypesequence
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('doctypesequences_successful_updating'), 'id' => $sequence_id));
			}
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('doctypesequences_error_adding_updating') . ' ' . $doctypesequence_data['name'], 'id' => -1));
		}
	}

	public function delete()
	{
		$doctypesequence_to_delete = $this->input->post('ids');

		if($this->Doctypesequence->delete_list($doctypesequence_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('doctypesequences_successful_deleted') . ' ' . count($doctypesequence_to_delete) . ' ' . $this->lang->line('doctypesequences_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('doctypesequences_cannot_be_deleted')));
		}
	}
}
?>
