<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Persons.php");

class Users extends Persons
{
	public function __construct()
	{
		parent::__construct('users');
	}

	/*
	Returns user table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$users = $this->User->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->User->get_found_rows($search);

		$data_rows = array();
		foreach($users->result() as $person)
		{
			$data_rows[] = $this->xss_clean(get_person_data_row($person));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
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
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->User->get_search_suggestions($this->input->post('term')));

		echo json_encode($suggestions);
	}

	/*
	Loads the user edit form
	*/
	public function view($user_id = -1)
	{
		$person_info = $this->User->get_info($user_id);
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

		$this->load->view('users/form', $data);
	}

	/*
	Inserts/updates an user
	*/
	public function save($user_id = -1)
	{
		$this->User->set_log("<< Start Log >>");
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
			'comments' => $this->input->post('comments'),
		);

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
				'username' 	=> $this->input->post('username'),
				'stock_location_id' => $this->input->post('stock_location_id'),
				'language_code'	=> $exploded[0],
				'language' 	=> $exploded[1]
			);
		}

		if($this->User->save_user($person_data, $user_data, $grants_array, $user_id))
		{
			$this->User->set_log("<< End Log >>");
			// New user
			if($user_id == -1)
			{
				echo json_encode(array('success' => TRUE,
								'message' => $this->lang->line('users_successful_adding') . ' ' . $first_name . ' ' . $last_name,
								'id' => $this->xss_clean($user_data['person_id'])));
			}
			else // Existing user
			{
				echo json_encode(array('success' => TRUE,
								'message' => $this->lang->line('users_successful_updating') . ' ' . $first_name . ' ' . $last_name,
								'id' => $user_id));
			}
		}
		else // Failure
		{
			$this->User->set_log("<< End Log >>");
			//	Use get_log method for debugg
			// --> $this->User->get_log().'<br>'.
			echo json_encode(array('success' => FALSE,
							'message' => $this->User->get_log().'<br>'.$this->lang->line('users_error_adding_updating') . ' ' . $first_name . ' ' . $last_name,
							'id' => -1));
		}
	}

	/*
	This deletes users from the users table
	*/
	public function delete()
	{
		$users_to_delete = $this->xss_clean($this->input->post('ids'));

		if($this->User->delete_list($users_to_delete))
		{
			echo json_encode(array('success' => TRUE,'message' => $this->lang->line('users_successful_deleted') . ' ' .
							count($users_to_delete) . ' ' . $this->lang->line('users_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('users_cannot_be_deleted')));
		}
	}
}
?>
