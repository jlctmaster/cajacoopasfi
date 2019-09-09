<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Persons.php");

class Employees extends Persons
{
	public function __construct()
	{
		parent::__construct('employees');
	}

	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_employees_manage_table_headers());

		$this->load->view('people/manage', $data);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_job_title()
	{
		$suggestions = $this->xss_clean($this->Employee->get_job_title_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_contract_type()
	{
		$suggestions = $this->xss_clean($this->Employee->get_contract_type_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function get_row_by_dni($dni)
	{
		$data_row = $this->Person->get_info_by_dni($dni);
		foreach(get_object_vars($data_row) as $property => $value)
		{
			$data_row->$property = $this->xss_clean($value);
		}
		echo json_encode($data_row);
	}

	/*
	Gets one row for a employee manage table. This is called using AJAX to update one row.
	*/
	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_employee_data_row($this->Employee->get_info($row_id)));

		echo json_encode($data_row);
	}
	
	/*
	Returns Employee table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$employees = $this->Employee->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Employee->get_found_rows($search);

		$data_rows = array();
		foreach($employees->result() as $employee)
		{
			$data_rows[] = $this->xss_clean(get_employee_data_row($employee));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest()
	{
		$suggestions = $this->xss_clean($this->Employee->get_search_suggestions($this->input->get('term'), TRUE));

		echo json_encode($suggestions);
	}

	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Employee->get_search_suggestions($this->input->post('term'), FALSE));

		echo json_encode($suggestions);
	}

	/*
	Loads the user edit form
	*/
	public function user_allocation($user_id = -1)
	{
		$person_info = $this->User->get_employee_info($user_id);
		foreach(get_object_vars($person_info) as $property => $value)
		{
			$person_info->$property = $this->xss_clean($value);
		}
		$data['person_info'] = $person_info;

		$stock_location = array('-1' => $this->lang->line('common_none_selected_text'));
		foreach($this->Stock_location->get_all()->result_array() as $row)
		{
			$stock_location[$this->xss_clean($row['location_id'])] = $this->xss_clean($row['location_name']);
		}
		$data['stock_location'] = $stock_location;
		$data['selected_stock_location'] = $data['person_info']->stock_location_id;

		$modules = array();
		foreach($this->Module->get_all_modules()->result() as $module)
		{
			$module->module_id = $this->xss_clean($module->module_id);
			$module->grant = $this->xss_clean($this->User->has_grant($module->module_id, $person_info->person_id));
			$module->menu_group = $this->xss_clean($this->User->get_menu_group($module->module_id, $person_info->person_id));

			$modules[] = $module;
		}
		$data['all_modules'] = $modules;

		$permissions = array();
		foreach($this->Module->get_all_subpermissions()->result() as $permission)
		{
			$permission->module_id = $this->xss_clean($permission->module_id);
			$permission->permission_id = $this->xss_clean($permission->permission_id);
			$permission->grant = $this->xss_clean($this->User->has_grant($permission->permission_id, $person_info->person_id));

			$permissions[] = $permission;
		}
		$data['all_subpermissions'] = $permissions;

		$this->load->view('employees/form_user', $data);
	}
	
	/*
	Loads the employee edit form
	*/
	public function view($employee_id = -1)
	{
		$info = $this->Employee->get_info($employee_id);
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}
		$data['person_info'] = $info;

		$this->load->view("employees/form", $data);
	}
	
	/*
	Inserts/updates a employee
	*/
	public function save($employee_id = -1, $user_allocation = -1)
	{

		$this->Employee->set_log("<< Start Log >>");

		$this->Employee->set_log("ID: ".$employee_id);

		$first_name = $this->xss_clean($this->input->post('first_name'));
		$last_name = $this->xss_clean($this->input->post('last_name'));
		$email = $this->xss_clean(strtolower($this->input->post('email')));

		// format first and last name properly
		$first_name = $this->nameize($first_name);
		$last_name = $this->nameize($last_name);

		$person_data = array(
			'first_name' => $first_name,
			'last_name' => $last_name,
			'dni' => $this->input->post('dni'),
			'gender' => $this->input->post('gender'),
			'email' => $email,
			'phone_number' => $this->input->post('phone_number'),
			'address_1' => $this->input->post('address_1'),
			'address_2' => $this->input->post('address_2'),
			'city' => $this->input->post('city'),
			'state' => $this->input->post('state'),
			'zip' => $this->input->post('zip'),
			'country' => $this->input->post('country'),
			'comments' => $this->input->post('comments')
		);

		//	Only for new records from insert / update employees
		if($user_allocation == -1){
			$admission_date = $this->input->post('admission_date');
			$admission_date_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $admission_date);

			$employee_data = array(
				'ruc' => $this->input->post('ruc'),
				'admission_date' => $admission_date_formatter->format('Y-m-d H:i:s'),
				'job_title' => $this->input->post('job_title'),
				'contract_type' => $this->input->post('contract_type')
			);

			if($this->Employee->save_employee($person_data, $employee_data, $employee_id))
			{
				$employee_data = $this->xss_clean($employee_data);
				$this->Employee->set_log("<< End Log >>");
				//New employee
				if($employee_id == -1)
				{
					echo json_encode(array('success' => TRUE,
									'message' => $this->lang->line('employees_successful_adding') . ' ' . $person_data['first_name'] . ' ' . $person_data['last_name'],
									'id' => $employee_data['person_id']));
				}
				else //Existing employee
				{
					echo json_encode(array('success' => TRUE,
									'message' => $this->lang->line('employees_successful_updating') . ' ' . $person_data['first_name'] . ' ' . $person_data['last_name'],
									'id' => $employee_id));
				}
			}
			else//failure
			{
				$this->Employee->set_log("<< End Log >>");
				//	Use get_log method for debugg
				// --> $this->Employee->get_log().'<br>'.
				$employee_data = $this->xss_clean($employee_data);

				echo json_encode(array('success' => FALSE,
								'message' => $this->lang->line('employees_error_adding_updating') . ' ' . $person_data['first_name'] . ' ' . $person_data['last_name'],
								'id' => -1));
			}
		}
		//	Only when need allocated user to employee
		else{
			$grants_array = array();
			foreach($this->Module->get_all_permissions()->result() as $permission)
			{
				$grants = array();
				$permission_post = "grant_".str_replace(' ','',$permission->permission_id);
				$grant = $this->input->post($permission_post) != NULL ? $this->input->post($permission_post) : '';
				if($grant == $permission->permission_id)
				{
					$grants['permission_id'] = $permission->permission_id;
					$grants['menu_group'] = $this->input->post('menu_group_'.$permission->permission_id) != NULL ? $this->input->post('menu_group_'.$permission->permission_id) : '--';
					$grants_array[] = $grants;
				}
			}

			//Password has been changed OR first time password set
			if($this->input->post('password') != '')
			{
				$exploded = explode(":", $this->input->post('language'));
				$user_data = array(
					'person_id' => $employee_id,
					'username' 	=> $this->input->post('username'),
					'password' 	=> password_hash($this->input->post('password'), PASSWORD_DEFAULT),
					'hash_version' 	=> 2,
					'stock_location_id' => $this->input->post('stock_location_id'),
					'language_code' => $exploded[0],
					'language' 	=> $exploded[1]
				);
			}
			else //Password not changed
			{
				$exploded = explode(":", $this->input->post('language'));
				$user_data = array(
					'person_id' => $employee_id,
					'username' 	=> $this->input->post('username'),
					'stock_location_id' => $this->input->post('stock_location_id'),
					'language_code'	=> $exploded[0],
					'language' 	=> $exploded[1]
				);
			}

			if($this->Employee->save_user($person_data, $user_data, $grants_array, $employee_id))
			{
				$this->Employee->set_log("<< End Log >>");
				echo json_encode(array('success' => TRUE,
								'message' => $this->lang->line('users_successful_adding') . ' ' . $first_name . ' ' . $last_name,
								'id' => $this->xss_clean($user_data['person_id'])));
			}
			else // Failure
			{
				$this->Employee->set_log("<< End Log >>");
				//	Use get_log method for debugg
				// --> $this->Employee->get_log().'<br>'.
				echo json_encode(array('success' => FALSE,
								'message' => $this->lang->line('users_error_adding_updating') . ' ' . $first_name . ' ' . $last_name,
								'id' => $employee_id));
			}
		}
	}
	
	/*
	This deletes employees from the employees table
	*/
	public function delete()
	{
		$employees_to_delete = $this->xss_clean($this->input->post('ids'));

		if($this->Employee->delete_list($employees_to_delete))
		{
			echo json_encode(array('success' => TRUE,'message' => $this->lang->line('employees_successful_deleted').' '.
							count($employees_to_delete).' '.$this->lang->line('employees_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE,'message' => $this->lang->line('employees_cannot_be_deleted')));
		}
	}
	
}
?>
