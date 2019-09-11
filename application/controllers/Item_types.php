<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Item_types extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('item_types');
	}

	public function index()
	{
		 $data['table_headers'] = $this->xss_clean(get_item_type_manage_table_headers());

		 $this->load->view('item_types/manage', $data);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_family()
	{
		$suggestions = $this->xss_clean($this->Item_type->get_family_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
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

		$item_types = $this->Item_type->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Item_type->get_found_rows($search);

		$data_rows = array();
		foreach($item_types->result() as $item_type)
		{
			$data_rows[] = $this->xss_clean(get_item_type_data_row($item_type));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_item_type_data_row($this->Item_type->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($item_type_id = -1)
	{
		$data['item_type_info'] = $this->Item_type->get_info($item_type_id);

		$this->load->view("item_types/form", $data);
	}

	public function save($item_type_id = -1)
	{
		$item_type_data = array(
			'name' => $this->input->post('name'),
			'family' => $this->input->post('family')
		);

		if($this->Item_type->save($item_type_data, $item_type_id))
		{
			$item_type_data = $this->xss_clean($item_type_data);

			// New id
			if($item_type_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('item_types_successful_adding'), 'id' => $item_type_data['id']));
			}
			else // Existing Item_type
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('item_types_successful_updating'), 'id' => $item_type_id));
			}
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('item_types_error_adding_updating') . ' ' . $item_type_data['name'], 'id' => -1));
		}
	}

	public function delete()
	{
		$item_type_to_delete = $this->input->post('ids');

		if($this->Item_type->delete_list($item_type_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('item_types_successful_deleted') . ' ' . count($item_type_to_delete) . ' ' . $this->lang->line('item_types_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('item_types_cannot_be_deleted')));
		}
	}
}
?>
