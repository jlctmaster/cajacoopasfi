<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Employee class
 */

class Employee extends Person
{	

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}

	/*
	Determines if a given person_id is a employee
	*/
	public function exists($person_id)
	{
		$this->db->from('employees AS employees');	
		$this->db->join('people AS people', 'people.person_id = employees.person_id');
		$this->db->where('employees.person_id', $person_id);
		
		return ($this->db->get()->num_rows() == 1);
	}

	/*
	Determines if a given person_id is an user
	*/
	public function user_exists($person_id)
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
		$this->db->from('employees');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}
	
	/*
	Returns all the employees
	*/
	public function get_all($limit_from = 0, $rows = 0)
	{
		$this->db->from('employees AS employees');
		$this->db->join('people AS people', 'employees.person_id = people.person_id');
		$this->db->where('employees.deleted', 0);
		$this->db->order_by('employees.ruc', 'asc');
		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();		
	}
	
	/*
	Gets information about a particular employee
	*/
	public function get_info($employee_id)
	{
		$this->db->from('employees AS employees');	
		$this->db->join('people AS people', 'people.person_id = employees.person_id');
		$this->db->where('employees.person_id', $employee_id);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $employee_id is NOT an employee
			$person_obj = parent::get_info(-1);
			
			//Get all the fields from employee table		
			//append those fields to base parent object, we we have a complete empty object
			foreach($this->db->list_fields('employees') as $field)
			{
				$person_obj->$field = '';
			}
			
			return $person_obj;
		}
	}
	
	/*
	Gets information about multiple employees
	*/
	public function get_multiple_info($employees_ids)
	{
		$this->db->from('employees AS employees');
		$this->db->join('people AS people', 'people.person_id = employees.person_id');
		$this->db->where_in('employees.person_id', $employees_ids);
		$this->db->order_by('people.last_name', 'asc');

		return $this->db->get();
	}
	
	/*
	Inserts or updates a employees
	*/
	public function save_employee(&$person_data, &$employee_data, $employee_id = FALSE)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->set_log("ID To Send: ".$employee_id);
		
		if(parent::save($person_data,$employee_id))
		{
			$this->set_log($this->db->last_query());
			if(!$employee_id || !$this->exists($employee_id))
			{
				$this->set_log($this->db->last_query());
				$employee_data['person_id'] = (!empty($person_data['person_id']) ? $person_data['person_id'] : $employee_id);
				$success = $this->db->insert('employees', $employee_data);
				$this->set_log($this->db->last_query());
			}
			else
			{
				$this->db->where('person_id', $employee_id);
				$success = $this->db->update('employees', $employee_data);
				$this->set_log($this->db->last_query());
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
	Inserts or updates an user
	*/
	public function save_user(&$person_data, &$user_data, &$grants_data, $employee_id)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->set_log("ID To Send: ".$employee_id);

		if(parent::save($person_data, $employee_id))
		{
			$this->set_log($this->db->last_query());
			if(!$this->user_exists($employee_id))
			{
				$success = $this->db->insert('users', $user_data);
				$this->set_log($this->db->last_query());
			}
			else
			{
				$this->db->where('person_id', $employee_id);
				$success = $this->db->update('users', $user_data);
				$this->set_log($this->db->last_query());
			}

			//We have either inserted or updated a new user, now lets set permissions.
			if($success)
			{
				//First lets clear out any grants the user currently has.
				$success = $this->db->delete('grants', array('person_id' => $employee_id));
				$this->set_log($this->db->last_query());

				//Now insert the new grants
				if($success)
				{
					$count = 0;
					foreach($grants_data as $grant)
					{
						$success = $this->db->insert('grants', array('permission_id' => $grant['permission_id'], 'person_id' => $employee_id, 'menu_group' => $grant['menu_group']));
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
	Deletes one employee
	*/
	public function delete($employee_id)
	{
		$this->db->where('person_id', $employee_id);

		return $this->db->update('employees', array('deleted' => 1));
	}
	
	/*
	Deletes a list of employees
	*/
	public function delete_list($employee_ids)
	{
		$this->db->where_in('person_id', $employee_ids);

		return $this->db->update('employees', array('deleted' => 1));
 	}

	public function get_job_title_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('job_title');
		$this->db->from('employees');
		$this->db->like('job_title', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by('job_title', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->job_title);
		}

		return $suggestions;
	}

	public function get_contract_type_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('contract_type');
		$this->db->from('employees');
		$this->db->like('contract_type', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by('contract_type', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->contract_type);
		}

		return $suggestions;
	}
 	
 	/*
	Get search suggestions to find employees
	*/
	public function get_search_suggestions($search, $unique = FALSE, $limit = 25)
	{
		$suggestions = array();

		$this->db->from('employees AS employees');
		$this->db->join('people AS people', 'employees.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('people.first_name', $search);
			$this->db->or_like('people.last_name', $search); 
			$this->db->or_like('people.dni', $search); 
			$this->db->or_like('CONCAT(people.first_name, " ",people.last_name)', $search);
		$this->db->group_end();
		$this->db->where('employees.deleted', 0);
		$this->db->order_by('people.last_name', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' => $row->first_name . ' ' . $row->last_name);
		}

		if(!$unique)
		{
			$this->db->from('employees AS employees');
			$this->db->join('people AS people', 'employees.person_id = people.person_id');
			$this->db->where('employees.deleted', 0);
			$this->db->like('people.email', $search);
			$this->db->order_by('people.email', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->email);
			}

			$this->db->from('employees AS employees');
			$this->db->join('people AS people', 'employees.person_id = people.person_id');
			$this->db->where('employees.deleted', 0);
			$this->db->like('people.phone_number', $search);
			$this->db->order_by('people.phone_number', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->phone_number);
			}

			$this->db->from('employees AS employees');
			$this->db->join('people AS people', 'employees.person_id = people.person_id');
			$this->db->where('employees.deleted', 0);
			$this->db->like('employees.ruc', $search);
			$this->db->where('employees.ruc IS NOT NULL');
			$this->db->order_by('employees.ruc', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->ruc);
			}

			$this->db->from('employees AS employees');
			$this->db->join('people AS people', 'employees.person_id = people.person_id');
			$this->db->where('employees.deleted', 0);
			$this->db->distinct();
			$this->db->like('employees.job_title', $search);
			$this->db->where('employees.job_title IS NOT NULL');
			$this->db->order_by('employees.job_title', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->job_title);
			}

			$this->db->from('employees AS employees');
			$this->db->join('people AS people', 'employees.person_id = people.person_id');
			$this->db->where('employees.deleted', 0);
			$this->db->distinct();
			$this->db->like('employees.contract_type', $search);
			$this->db->where('employees.contract_type IS NOT NULL');
			$this->db->order_by('employees.contract_type', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->contract_type);
			}

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
	Perform a search on employees
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'last_name', $order = 'asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(employees.person_id) as count');
		}

		$this->db->from('employees AS employees');
		$this->db->join('people AS people', 'employees.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('people.first_name', $search);
			$this->db->or_like('people.last_name', $search);
			$this->db->or_like('employees.job_title', $search);
			$this->db->or_like('employees.contract_type', $search);
			$this->db->or_like('people.email', $search);
			$this->db->or_like('people.phone_number', $search);
			$this->db->or_like('employees.ruc', $search);
			$this->db->or_like('CONCAT(people.first_name, " ",people.last_name)', $search);
		$this->db->group_end();
		$this->db->where('employees.deleted', 0);
		
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
}
?>
