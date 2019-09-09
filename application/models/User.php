<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User class
 */

class User extends Person
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}
	/*
	Determines if a given person_id is an user
	*/
	public function exists($person_id)
	{
		$this->db->from('users');
		$this->db->join('people', 'people.person_id = users.person_id');
		$this->db->where('users.person_id', $person_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/*
	Gets total of rows
	*/
	public function get_total_rows()
	{
		$this->db->from('users');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/*
	Returns all the users
	*/
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('users');
		$this->db->where('deleted', 0);
		$this->db->join('people', 'users.person_id = people.person_id');
		$this->db->order_by('last_name', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/*
	Gets information about a particular user
	*/
	public function get_employee_info($person_id)
	{
		$this->db->select('
				people.*,
				users.username,
				users.password,
				users.stock_location_id,
				users.language,
				users.language_code,
				users.hash_version');

		$this->db->from('people');
		$this->db->join('users', 'people.person_id = users.person_id', 'left');
		$this->db->where('people.person_id', $person_id);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $user_id is NOT an user
			$person_obj = parent::get_info(-1);

			//Get all the fields from user table
			//append those fields to base parent object, we we have a complete empty object
			foreach($this->db->list_fields('users') as $field)
			{
				$person_obj->$field = '';
			}

			return $person_obj;
		}
	}

	/*
	Gets users information about a particular location
	*/
	public function get_user_by_location($location_id)
	{
		$this->db->select('users.person_id,
			users.username,
			people.first_name,
			people.last_name,
			stock_locations.location_code,
			stock_locations.location_name');
		$this->db->from('users');
		$this->db->join('people', 'people.person_id = users.person_id');
		$this->db->join('stock_locations', 'stock_locations.location_id = users.stock_location_id');
		$this->db->where('users.stock_location_id', $location_id);
		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			//Get empty base parent object, as $user_id is NOT an user
			$person_obj = parent::get_info(-1);

			//Get all the fields from user table
			//append those fields to base parent object, we we have a complete empty object
			foreach($this->db->list_fields('users') as $field)
			{
				$person_obj->$field = '';
			}
			foreach($this->db->list_fields('stock_locations') as $field)
			{
				$person_obj->$field = '';
			}

			return $person_obj;
		}
	}

	/*
	Gets information about a particular user
	*/
	public function get_info($user_id)
	{
		$this->db->from('users');
		$this->db->join('people', 'people.person_id = users.person_id');
		$this->db->where('users.person_id', $user_id);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $user_id is NOT an user
			$person_obj = parent::get_info(-1);

			//Get all the fields from user table
			//append those fields to base parent object, we we have a complete empty object
			foreach($this->db->list_fields('users') as $field)
			{
				$person_obj->$field = '';
			}

			return $person_obj;
		}
	}

	/*
	Gets information about multiple users
	*/
	public function get_multiple_info($user_ids)
	{
		$this->db->from('users');
		$this->db->join('people', 'people.person_id = users.person_id');
		$this->db->where_in('users.person_id', $user_ids);
		$this->db->order_by('last_name', 'asc');

		return $this->db->get();
	}

	/*
	Inserts or updates an user
	*/
	public function save_user(&$person_data, &$user_data, &$grants_data, $user_id = FALSE)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		$this->set_log("ID To Send: ".$user_id);

		if(parent::save($person_data, $user_id))
		{
			$this->set_log($this->db->last_query());
			if(!$user_id || !$this->exists($user_id))
			{
				$this->set_log($this->db->last_query());
				$user_data['person_id'] = (!empty($person_data['person_id']) ? $person_data['person_id'] : $user_id);
				$success = $this->db->insert('users', $user_data);
				if($user_id == -1)
				{
					$user_id = $user_data['person_id'];	
				}
				$this->set_log($this->db->last_query());
			}
			else
			{
				$this->db->where('person_id', $user_id);
				$success = $this->db->update('users', $user_data);
				$this->set_log($this->db->last_query());
			}

			//We have either inserted or updated a new user, now lets set permissions.
			if($success)
			{
				//First lets clear out any grants the user currently has.
				$success = $this->db->delete('grants', array('person_id' => $user_id));
				$this->set_log($this->db->last_query());

				//Now insert the new grants
				if($success)
				{
					$count = 0;
					foreach($grants_data as $grant)
					{
						$success = $this->db->insert('grants', array('permission_id' => $grant['permission_id'], 'person_id' => $user_id, 'menu_group' => $grant['menu_group']));
						$this->set_log($this->db->last_query());
						$count = $count+ 1;
					}
				}
			}
		}else{
			$this->set_log(parent::get_log());

			$this->set_log($this->db->last_query());	
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}

	/*
	Deletes one user
	*/
	public function delete($user_id)
	{
		$success = FALSE;

		//Don't let users delete theirself
		if($user_id == $this->get_logged_in_user_info()->person_id)
		{
			return FALSE;
		}

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		//Delete permissions
		if($this->db->delete('grants', array('person_id' => $user_id)))
		{
			$this->db->where('person_id', $user_id);
			$success = $this->db->update('users', array('deleted' => 1));
		}

		$this->db->trans_complete();

		return $success;
	}

	/*
	Deletes a list of users
	*/
	public function delete_list($user_ids)
	{
		$success = FALSE;

		//Don't let users delete theirself
		if(in_array($this->get_logged_in_user_info()->person_id, $user_ids))
		{
			return FALSE;
		}

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->where_in('person_id', $user_ids);
		//Delete permissions
		if($this->db->delete('grants'))
		{
			//delete from user table
			$this->db->where_in('person_id', $user_ids);
			$success = $this->db->update('users', array('deleted' => 1));
		}

		$this->db->trans_complete();

		return $success;
 	}

	/*
	Get search suggestions to find users
	*/
	public function get_search_suggestions($search, $limit = 5)
	{
		$suggestions = array();

		$this->db->from('users');
		$this->db->join('people', 'users.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search);
			$this->db->or_like('CONCAT(first_name, " ", last_name)', $search);
		$this->db->group_end();
		$this->db->where('deleted', 0);
		$this->db->order_by('last_name', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' => $row->first_name.' '.$row->last_name);
		}

		$this->db->from('users');
		$this->db->join('people', 'users.person_id = people.person_id');
		$this->db->where('deleted', 0);
		$this->db->like('email', $search);
		$this->db->order_by('email', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' => $row->email);
		}

		$this->db->from('users');
		$this->db->join('people', 'users.person_id = people.person_id');
		$this->db->where('deleted', 0);
		$this->db->like('username', $search);
		$this->db->order_by('username', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' => $row->username);
		}

		$this->db->from('users');
		$this->db->join('people', 'users.person_id = people.person_id');
		$this->db->where('deleted', 0);
		$this->db->like('phone_number', $search);
		$this->db->order_by('phone_number', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' => $row->phone_number);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

 	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'last_name', 'asc', TRUE);
	}

	/*
	Performs a search on users
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'last_name', $order = 'asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(users.person_id) as count');
		}

		$this->db->from('users AS users');
		$this->db->join('people', 'users.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search);
			$this->db->or_like('dni', $search);
			$this->db->or_like('email', $search);
			$this->db->or_like('phone_number', $search);
			$this->db->or_like('username', $search);
			$this->db->or_like('CONCAT(first_name, " ", last_name)', $search);
		$this->db->group_end();
		$this->db->where('deleted', 0);

		// get_found_rows case
		if($count_only == TRUE)
		{
			return $this->db->get()->row()->count;
		}

		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}

	/*
	Attempts to login user and set session. Returns boolean based on outcome.
	*/
	public function login($username, $password)
	{
		$query = $this->db->get_where('users', array('username' => $username, 'deleted' => 0), 1);

		if($query->num_rows() == 1)
		{
			$row = $query->row();

			// compare passwords depending on the hash version
			if($row->hash_version == 1 && $row->password == md5($password))
			{
				$this->db->where('person_id', $row->person_id);
				$this->session->set_userdata('person_id', $row->person_id);
				$password_hash = password_hash($password, PASSWORD_DEFAULT);

				return $this->db->update('users', array('hash_version' => 2, 'password' => $password_hash));
			}
			elseif($row->hash_version == 2 && password_verify($password, $row->password))
			{
				$this->session->set_userdata('person_id', $row->person_id);

				return TRUE;
			}

		}

		return FALSE;
	}

	/*
	Logs out a user by destorying all session data and redirect to login
	*/
	public function logout()
	{
		$this->session->sess_destroy();

		redirect('login');
	}

	/*
	Determins if a user is logged in
	*/
	public function is_logged_in()
	{
		return ($this->session->userdata('person_id') != FALSE);
	}

	/*
	Gets information about the currently logged in user.
	*/
	public function get_logged_in_user_info()
	{
		if($this->is_logged_in())
		{
			return $this->get_info($this->session->userdata('person_id'));
		}

		return FALSE;
	}

	/*
	Determines whether the user has access to at least one submodule
	 */
	public function has_module_grant($permission_id, $person_id)
	{
		$this->db->from('grants');
		$this->db->like('permission_id', $permission_id, 'after');
		$this->db->where('person_id', $person_id);
		$result_count = $this->db->get()->num_rows();

		if($result_count != 1)
		{
			return ($result_count != 0);
		}

		return $this->has_subpermissions($permission_id);
	}

 	/*
	Checks permissions
	*/
	public function has_subpermissions($permission_id)
	{
		$this->db->from('permissions');
		$this->db->like('permission_id', $permission_id.'_', 'after');

		return ($this->db->get()->num_rows() == 0);
	}

	/**
	 * Determines whether the user specified user has access the specific module.
	 */
	public function has_grant($permission_id, $person_id)
	{
		//if no module_id is null, allow access
		if($permission_id == NULL)
		{
			return TRUE;
		}

		$query = $this->db->get_where('grants', array('person_id' => $person_id, 'permission_id' => $permission_id), 1);

		return ($query->num_rows() == 1);
	}

	/**
	 * Returns the menu group designation that this module is to appear in
	 */
	public function get_menu_group($permission_id, $person_id)
	{
		$this->db->select('menu_group');
		$this->db->from('grants');
		$this->db->where('permission_id', $permission_id);
		$this->db->where('person_id', $person_id);

		$row = $this->db->get()->row();

		// If no grants are assigned yet then set the default to 'home'
		if($row == NULL)
		{
			return 'home';
		}
		else
		{
			return $row->menu_group;
		}
	}

	/*
   Gets user permission grants
   */
	public function get_user_grants($person_id)
	{
		$this->db->from('grants');
		$this->db->where('person_id', $person_id);

		return $this->db->get()->result_array();
	}


	/*
	Attempts to login user and set session. Returns boolean based on outcome.
	*/
	public function check_password($username, $password)
	{
		$query = $this->db->get_where('users', array('username' => $username, 'deleted' => 0), 1);

		if($query->num_rows() == 1)
		{
			$row = $query->row();

			// compare passwords
			if(password_verify($password, $row->password))
			{
				return TRUE;
			}

		}

		return FALSE;
	}

	/*
	Change password for the user
	*/
	public function change_password($user_data, $user_id = FALSE)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->where('person_id', $user_id);
		$success = $this->db->update('users', $user_data);

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}
}
?>
