<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for UOM classes
 */

class Uom extends CI_Model
{
	/**
	 * Determines whether the given UOM exists in the uom database table
	 *
	 * @param integer $uom_id identifier of the UOM to verify the existence
	 *
	 * @return boolean TRUE if the UOM exists, FALSE if not
	 */
	public function exists($uom_id)
	{
		$this->db->from('uom');
		$this->db->where('uom_id', $uom_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all uom from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of uom table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('uom');
		$this->db->order_by('name', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of uom database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('uom');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Uom as an array
	 *
	 * @param integer $uom_id identifier of the Uom
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($uom_id)
	{
		$query = $this->db->get_where('uom', array('uom_id' => $uom_id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Uom_obj = new stdClass;

			foreach($this->db->list_fields('uom') as $field)
			{
				$uom_obj->$field = '';
			}

			return $uom_obj;
		}
	}
        
        public function get_uom_item_suggestions($item)
        {
            $query = $this->db->query("select * from iom_uom where uom_id in(select uom_id from iom_items where item_id =".$item." )");
            
             foreach($query->result() as $row)
            {
                $suggestions[] = array('id' => $row->uom_id,'name'=> strtoupper($row->name)) ;
            }
            //if(count($suggestions)>0)
            return $suggestions;
            
            
            
        }
        
	public function get_magnitude_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('magnitude');
		$this->db->from('uom');
		$this->db->like('magnitude', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by('magnitude', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->magnitude);
		}

		return $suggestions;
	}

	/**
	 * Gets information about uom as an array of rows
	 *
	 * @param array $uom_ids array of uom identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($uom_ids)
	{
		$this->db->from('uom');
		$this->db->where_in('uom_id', $uom_ids);
		$this->db->order_by('name', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Uom
	 *
	 * @param array $uom_data array containing Uom information
	 *
	 * @param var $uom_id identifier of the Uom to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$uom_data, $uom_id = FALSE)
	{
		if(!$uom_id || !$this->exists($uom_id))
		{
			if($this->db->insert('uom', $uom_data))
			{
				$uom_data['uom_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}

		$this->db->where('uom_id', $uom_id);

		return $this->db->update('uom', $uom_data);
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'name', 'asc', TRUE);
	}

	/*
	Perform a search on uom
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'name', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(uom_id) as count');
		}

		$this->db->from('uom');
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('symbol', $search);
			$this->db->or_like('magnitude', $search);
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

	/**
	 * Get search suggestions to find Uom
	 *
	 * @param string $search string containing the term to search in the uom table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('uom_id');
		$this->db->from('uom');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('symbol', $search);
			$this->db->group_end();
		$this->db->order_by('name', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->uom_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one uom
	 *
	 * @param integer $uom_id Uom identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($uom_id)
	{
		$this->db->where('uom_id', $uom_id);

		$result &= $this->db->update('uom', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of uom
	 *
	 * @param array $uom_ids list of Uom identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($uom_ids)
	{
		$this->db->where_in('uom_id', $uom_ids);

		return $this->db->update('uom', array('deleted' => 1));
 	}
}
?>
