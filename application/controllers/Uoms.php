<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Uoms extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('uoms');
	}

	public function index()
	{
		 $data['table_headers'] = $this->xss_clean(get_uom_manage_table_headers());

		 $this->load->view('uoms/manage', $data);
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

		$uoms = $this->Uom->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Uom->get_found_rows($search);

		$data_rows = array();
		foreach($uoms->result() as $uom)
		{
			$data_rows[] = $this->xss_clean(get_uom_data_row($uom));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_magnitude()
	{
		$suggestions = $this->xss_clean($this->Uom->get_magnitude_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_uom_data_row($this->Uom->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($uom_id = -1)
	{
		$data['uom_info'] = $this->Uom->get_info($uom_id);

		$this->load->view("uoms/form", $data);
	}

	public function save($uom_id = -1)
	{
		$uom_data = array(
			'symbol' => $this->input->post('symbol'),
			'name' => $this->input->post('name'),
			'magnitude' => $this->input->post('magnitude')
		);

		if($this->Uom->save($uom_data, $uom_id))
		{
			$uom_data = $this->xss_clean($uom_data);

			// New uom_id
			if($uom_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('uoms_successful_adding'), 'id' => $uom_data['uom_id']));
			}
			else // Existing Uom
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('uoms_successful_updating'), 'id' => $uom_id));
			}
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('uoms_error_adding_updating') . ' ' . $uom_data['name'], 'id' => -1));
		}
	}

	public function delete()
	{
		$uom_to_delete = $this->input->post('ids');

		if($this->Uom->delete_list($uom_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('uoms_successful_deleted') . ' ' . count($uom_to_delete) . ' ' . $this->lang->line('uoms_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('uoms_cannot_be_deleted')));
		}
	}
}
?>
