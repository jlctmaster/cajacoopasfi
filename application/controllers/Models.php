<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Models extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('models');
	}

	public function index()
	{
		 $data['table_headers'] = $this->xss_clean(get_model_manage_table_headers());

		 $this->load->view('models/manage', $data);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_type()
	{
		$suggestions = $this->xss_clean($this->Model->get_type_suggestions($this->input->get('term')));

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

		$models = $this->Model->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Model->get_found_rows($search);

		$data_rows = array();
		foreach($models->result() as $model)
		{
			$data_rows[] = $this->xss_clean(get_model_data_row($model));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_model_data_row($this->Model->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($model_id = -1)
	{
		$data['model_info'] = $this->Model->get_info($model_id);

		$this->load->view("models/form", $data);
	}

	public function save($model_id = -1)
	{
		$model_data = array(
			'name' => $this->input->post('name'),
			'type' => $this->input->post('type'),
			'value' => $this->input->post('value')
		);

		if($this->Model->save($model_data, $model_id))
		{
			$model_data = $this->xss_clean($model_data);

			// New id
			if($model_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('models_successful_adding'), 'id' => $model_data['id']));
			}
			else // Existing Model
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('models_successful_updating'), 'id' => $model_id));
			}
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('models_error_adding_updating') . ' ' . $model_data['name'], 'id' => -1));
		}
	}

	public function delete()
	{
		$model_to_delete = $this->input->post('ids');

		if($this->Model->delete_list($model_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('models_successful_deleted') . ' ' . count($model_to_delete) . ' ' . $this->lang->line('models_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('models_cannot_be_deleted')));
		}
	}
}
?>
