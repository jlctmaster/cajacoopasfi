<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Cash_concepts extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('cash_concepts');
	}

	public function index()
	{
		 $data['table_headers'] = $this->xss_clean(get_cash_concept_manage_table_headers());

		 $this->load->view('cash_concepts/manage', $data);
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

		$cash_concepts = $this->Cash_concept->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Cash_concept->get_found_rows($search);

		$data_rows = array();
		foreach($cash_concepts->result() as $cash_concept)
		{
			$data_rows[] = $this->xss_clean(get_cash_concept_data_row($cash_concept));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_subconcept($cash_concept_parent_id)
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$cash_concepts = $this->Cash_concept->search_subconcept($cash_concept_parent_id,$search, $limit, $offset, $sort, $order);
		$total_rows = $this->Cash_concept->get_found_rows($search);

		$data_rows = array();
		foreach($cash_concepts->result() as $cash_concept)
		{
			$data_rows[] = $this->xss_clean(get_cash_concept_parent_data_row($cash_concept));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_cash_concept_data_row($this->Cash_concept->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($cash_concept_id = -1)
	{
		$data['cash_concept_info'] = $this->Cash_concept->get_info($cash_concept_id);

		$cash_concept = array('-1' => $this->lang->line('cash_concepts_not_parent'));
		foreach($this->Cash_concept->get_all_summary(1)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}
		$data['cash_concept_parent'] = $cash_concept;
		$data['selected_cash_concept_parent'] = $data['cash_concept_info']->cash_concept_parent_id;

		$this->load->view("cash_concepts/form_income", $data);
	}

	public function view_cost($cash_concept_id = -1)
	{
		$data['cash_concept_info'] = $this->Cash_concept->get_info($cash_concept_id);

		$cash_concept = array('-1' => $this->lang->line('cash_concepts_not_parent'));
		foreach($this->Cash_concept->get_all_summary(2)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}
		$data['cash_concept_parent'] = $cash_concept;
		$data['selected_cash_concept_parent'] = $data['cash_concept_info']->cash_concept_parent_id;

		$this->load->view("cash_concepts/form_cost", $data);
	}

	public function view_expense($cash_concept_id = -1)
	{
		$data['cash_concept_info'] = $this->Cash_concept->get_info($cash_concept_id);

		$cash_concept = array('-1' => $this->lang->line('cash_concepts_not_parent'));
		foreach($this->Cash_concept->get_all_summary(3)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}
		$data['cash_concept_parent'] = $cash_concept;
		$data['selected_cash_concept_parent'] = $data['cash_concept_info']->cash_concept_parent_id;

		$this->load->view("cash_concepts/form_expense", $data);
	}

	public function subconcept($cash_concept_parent_id)
	{
		$data['table_headers'] = $this->xss_clean(get_cash_concept_parent_manage_table_headers());

		$data['parent_info'] = $this->Cash_concept->get_info($cash_concept_parent_id);

		$this->load->view('cash_concepts/manage_subconcept', $data);
	}

	public function view_subconcept($cash_concept_parent_id,$cash_concept_id = -1){
		$data['cash_concept_parent_info'] = $this->Cash_concept->get_info($cash_concept_parent_id);
		$data['cash_concept_info'] = $this->Cash_concept->get_info($cash_concept_id);

		$cash_concept[$this->xss_clean($data['cash_concept_parent_info']->cash_concept_id)] = $this->xss_clean($data['cash_concept_parent_info']->name);

		foreach($this->Cash_concept->get_parent_all($cash_concept_parent_id)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}
		$data['cash_concept_parent'] = $cash_concept;
		$data['selected_cash_concept_parent'] = (!empty($data['cash_concept_info']->cash_concept_parent_id) ? $data['cash_concept_info']->cash_concept_parent_id : $cash_concept_parent_id);

		$this->load->view("cash_concepts/form_subconcept", $data);
	}

	public function save($cash_concept_id = -1)
	{
		$this->Cash_concept->set_log("<< Start Log >>");

		$cash_concept_data = array(
			'code' => $this->input->post('code'),
			'name' => $this->input->post('name'),
			'concept_type' => $this->input->post('concept_type'),
			'document_sequence' => $this->input->post('document_sequence'),
			'description' => $this->input->post('description'),
			'is_summary' => ($this->input->post('cash_concept_parent_id')==-1 ? TRUE: ($this->input->post('is_summary') != NULL)),
			'is_cash_general_used' => $this->input->post('is_cash_general_used') != NULL,
			'affected_voucheroperation' => (!empty($this->input->post('affected_voucheroperation')) ? $this->input->post('affected_voucheroperation') != NULL : 0),
			'cash_concept_parent_id' => ($this->input->post('cash_concept_parent_id')==-1 ? NULL : $this->input->post('cash_concept_parent_id'))
		);

		if($this->Cash_concept->save($cash_concept_data, $cash_concept_id))
		{
			$cash_concept_data = $this->xss_clean($cash_concept_data);

			// New cash_concept_id
			if($cash_concept_id == -1)
			{
				$this->Cash_concept->set_log("<< End Log >>");
				//	Use log 
				//	$this->Cash_concept->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('cash_concepts_successful_adding'), 'id' => $cash_concept_data['cash_concept_id']));
			}
			else // Existing Cash_concept
			{
				$this->Cash_concept->set_log($this->db->last_query());
				$this->Cash_concept->set_log("<< End Log >>");
				//	Use log 
				//	$this->Cash_concept->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('cash_concepts_successful_updating'), 'id' => $cash_concept_id));
			}
		}
		else//failure
		{
			if($cash_concept_id != -1)
			{
				$this->Cash_concept->set_log($this->db->last_query());
			}
			$this->Cash_concept->set_log("<< End Log >>");
			//	Use log 
			//	$this->Cash_concept->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->Cash_concept->get_log()."<br>".$this->lang->line('cash_concepts_error_adding_updating') . ' ' . $cash_concept_data['name'], 'id' => -1));
		}
	}

	public function delete()
	{
		$cash_concept_to_delete = $this->input->post('ids');

		if($this->Cash_concept->delete_list($cash_concept_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('cash_concepts_successful_deleted') . ' ' . count($cash_concept_to_delete) . ' ' . $this->lang->line('cash_concepts_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('cash_concepts_cannot_be_deleted')));
		}
	}
}
?>
