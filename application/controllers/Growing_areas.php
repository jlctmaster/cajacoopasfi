<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Growing_areas extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('growing_areas');
	}

	public function index()
	{
		 $data['table_headers'] = $this->xss_clean(get_growing_area_manage_table_headers());

		 $this->load->view('growing_areas/manage', $data);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_district()
	{
		$suggestions = $this->xss_clean($this->Growing_area->get_district_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_state()
	{
		$suggestions = $this->xss_clean($this->Growing_area->get_state_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_country()
	{
		$suggestions = $this->xss_clean($this->Growing_area->get_country_suggestions($this->input->get('term')));

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

		$growing_areas = $this->Growing_area->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Growing_area->get_found_rows($search);

		$data_rows = array();
		foreach($growing_areas->result() as $growing_area)
		{
			$data_rows[] = $this->xss_clean(get_growing_area_data_row($growing_area));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_growing_area_data_row($this->Growing_area->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($growing_area_id = -1)
	{
		$data['growing_area_info'] = $this->Growing_area->get_info($growing_area_id);

		$this->load->view("growing_areas/form", $data);
	}

	public function save($growing_area_id = -1)
	{
		$growing_area_data = array(
			'name' => $this->input->post('name'),
			'district' => $this->input->post('district'),
			'state' => $this->input->post('state'),
			'country' => $this->input->post('country')
		);

		if($this->Growing_area->save($growing_area_data, $growing_area_id))
		{
			$growing_area_data = $this->xss_clean($growing_area_data);

			// New growing_area_id
			if($growing_area_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('growing_areas_successful_adding'), 'id' => $growing_area_data['growing_area_id']));
			}
			else // Existing Growing_area
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('growing_areas_successful_updating'), 'id' => $growing_area_id));
			}
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('growing_areas_error_adding_updating') . ' ' . $growing_area_data['name'], 'id' => -1));
		}
	}

	public function delete()
	{
		$growing_area_to_delete = $this->input->post('ids');

		if($this->Growing_area->delete_list($growing_area_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('growing_areas_successful_deleted') . ' ' . count($growing_area_to_delete) . ' ' . $this->lang->line('growing_areas_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('growing_areas_cannot_be_deleted')));
		}
	}
}
?>
