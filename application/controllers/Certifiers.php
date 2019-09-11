<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Certifiers extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('certifiers');
	}

	public function index()
	{
		 $data['table_headers'] = $this->xss_clean(get_singlemaster_manage_table_headers());

		 $this->load->view('singlemasters/manage', $data);
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

		$certifiers = $this->Certifier->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Certifier->get_found_rows($search);

		$data_rows = array();
		foreach($certifiers->result() as $certifier)
		{
			$data_rows[] = $this->xss_clean(get_singlemaster_data_row($certifier));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_singlemaster_data_row($this->Certifier->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($id = -1)
	{
		$data['info'] = $this->Certifier->get_info($id);

		$this->load->view("singlemasters/form", $data);
	}

	public function save($id = -1)
	{
		$certifier_data = array(
			'name' => $this->input->post('name')
		);

		if($this->Certifier->save($certifier_data, $id))
		{
			$certifier_data = $this->xss_clean($certifier_data);

			// New id
			if($id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('certifiers_successful_adding'), 'id' => $certifier_data['id']));
			}
			else // Existing Certifier
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('certifiers_successful_updating'), 'id' => $id));
			}
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('certifiers_error_adding_updating') . ' ' . $certifier_data['name'], 'id' => -1));
		}
	}

	public function delete()
	{
		$certifier_to_delete = $this->input->post('ids');

		if($this->Certifier->delete_list($certifier_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('certifiers_successful_deleted') . ' ' . count($certifier_to_delete) . ' ' . $this->lang->line('certifiers_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('certifiers_cannot_be_deleted')));
		}
	}
}
?>
