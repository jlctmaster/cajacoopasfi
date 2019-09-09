<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Stock_location class
 */

class Stock_location extends CI_Model
{
	public function exists($location_id = -1)
	{
		$this->db->from('stock_locations');
		$this->db->where('location_id', $location_id);

		return ($this->db->get()->num_rows() >= 1);
	}

	public function get_all()
	{
		$this->db->from('stock_locations');
		$this->db->where('deleted', 0);

		return $this->db->get();
	}

	public function get_undeleted_all($module_id = 'items')
	{
		$this->db->from('stock_locations');
		$this->db->join('permissions AS permissions', 'permissions.location_id = stock_locations.location_id');
		$this->db->join('grants AS grants', 'grants.permission_id = permissions.permission_id');
		$this->db->where('person_id', $this->session->userdata('person_id'));
		$this->db->like('permissions.permission_id', $module_id, 'after');
		$this->db->where('deleted', 0);

		return $this->db->get();
	}

	public function show_locations($module_id = 'items')
	{
		$stock_locations = $this->get_allowed_locations($module_id);

		return count($stock_locations) > 1;
	}

	public function multiple_locations()
	{
		return $this->get_all()->num_rows() > 1;
	}

	public function get_allowed_locations($module_id = 'items')
	{
		$stock = $this->get_undeleted_all($module_id)->result_array();
		$stock_locations = array();
		foreach($stock as $location_data)
		{
			$stock_locations[$location_data['location_id']] = $location_data['location_name'];
		}

		return $stock_locations;
	}

	public function is_allowed_location($location_id, $module_id = 'items')
	{
		$this->db->from('stock_locations');
		$this->db->join('permissions AS permissions', 'permissions.location_id = stock_locations.location_id');
		$this->db->join('grants AS grants', 'grants.permission_id = permissions.permission_id');
		$this->db->where('person_id', $this->session->userdata('person_id'));
		$this->db->like('permissions.permission_id', $module_id, 'after');
		$this->db->where('stock_locations.location_id', $location_id);
		$this->db->where('deleted', 0);

		return ($this->db->get()->num_rows() == 1);
	}

	public function get_default_location_id()
	{
		$this->db->from('stock_locations');
		$this->db->join('permissions AS permissions', 'permissions.location_id = stock_locations.location_id');
		$this->db->join('grants AS grants', 'grants.permission_id = permissions.permission_id');
		$this->db->where('person_id', $this->session->userdata('person_id'));
		$this->db->where('deleted', 0);
		$this->db->limit(1);

		return $this->db->get()->row()->location_id;
	}

	public function get_location_name($location_id)
	{
		$this->db->from('stock_locations');
		$this->db->where('location_id', $location_id);

		return $this->db->get()->row()->location_name;
	}
 	
 	/*
	Get search suggestions to find employees
	*/
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->from('stock_locations');
		$this->db->group_start();
			$this->db->like('location_code', $search);
			$this->db->or_like('location_name', $search);
		$this->db->group_end();
		$this->db->where('deleted', 0);
		$this->db->order_by('location_code', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->location_id, 'label' => $row->location_name);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	public function get_location_id($location_name)
	{
		$this->db->from('stock_locations');
		$this->db->where('location_name', $location_name);

		return $this->db->get()->row()->location_id;
	}

	public function save(&$location_data, $location_id)
	{
		$location_code = $location_data['location_code'];
		$location_name = $location_data['location_name'];

		$location_data_to_save = array('location_code' => $location_code, 'location_name' => $location_name, 'deleted' => 0);

		if(!$this->exists($location_id))
		{
			$this->db->trans_start();

   			$this->db->insert('stock_locations', $location_data_to_save);
   			$location_id = $this->db->insert_id();

   			$this->_insert_new_permission('items', $location_id, $location_name);
   			//$this->_insert_new_permission('sales', $location_id, $location_name);
   			//$this->_insert_new_permission('receivings', $location_id, $location_name);

   			// insert quantities for existing items
   			$items = $this->Item->get_all();
   			foreach($items->result_array() as $item)
   			{
   				$quantity_data = array('item_id' => $item['item_id'], 'location_id' => $location_id, 'quantity' => 0);
   				$this->db->insert('item_quantities', $quantity_data);
   			}

   			$this->db->trans_complete();

			return $this->db->trans_status();
   		}

   		$original_location_name = $this->get_location_name($location_id);

		if($original_location_name != $location_name)
		{
			$this->db->where('location_id', $location_id);
			$this->db->delete('permissions');

			$this->_insert_new_permission('items', $location_id, $location_name);
			//$this->_insert_new_permission('sales', $location_id, $location_name);
			//$this->_insert_new_permission('receivings', $location_id, $location_name);
		}

		$this->db->where('location_id', $location_id);

		return $this->db->update('stock_locations', $location_data_to_save);
	}

	private function _insert_new_permission($module, $location_id, $location_name)
	{
		// insert new permission for stock location
		$permission_id = $module . '_' . $location_name;
		$permission_data = array('permission_id' => $permission_id, 'module_id' => $module, 'location_id' => $location_id);
		$this->db->insert('permissions', $permission_data);

		// insert grants for new permission
		$users = $this->User->get_all();
		foreach($users->result_array() as $user)
		{
			// Retrieve the menu_group assigned to the grant for the module and use that for the new stock locations
			$menu_group = $this->User->get_menu_group($module, $user['person_id']);

			$grants_data = array('permission_id' => $permission_id, 'person_id' => $user['person_id'], 'menu_group' => $menu_group);
			$this->db->insert('grants', $grants_data);
		}
	}

	/*
	 Deletes one item
	*/
	public function delete($location_id)
	{
		$this->db->trans_start();

		$this->db->where('location_id', $location_id);
		$this->db->update('stock_locations', array('deleted' => 1));

		$this->db->where('location_id', $location_id);
		$this->db->delete('permissions');

		$this->db->trans_complete();

		return $this->db->trans_status();
	}
}
?>
