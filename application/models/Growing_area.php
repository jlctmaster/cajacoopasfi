<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for growing_area classes
 */

class Growing_area extends CI_Model
{
	/**
	 * Determines whether the given growing_area exists in the growing_area database table
	 *
	 * @param integer $growing_area_id identifier of the growing_area to verify the existence
	 *
	 * @return boolean TRUE if the growing_area exists, FALSE if not
	 */
	public function exists($growing_area_id)
	{
		$this->db->from('growing_areas');
		$this->db->where('growing_area_id', $growing_area_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all growing_area from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of growing_area table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('growing_areas');
		$this->db->order_by('name', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of growing_area database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('growing_areas');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a growing_area as an array
	 *
	 * @param integer $growing_area_id identifier of the growing_area
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($growing_area_id)
	{
		$query = $this->db->get_where('growing_areas', array('growing_area_id' => $growing_area_id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$growing_area_obj = new stdClass;

			foreach($this->db->list_fields('growing_areas') as $field)
			{
				$growing_area_obj->$field = '';
			}

			return $growing_area_obj;
		}
	}

	/**
	 * Gets information about a growing_area as an array
	 *
	 * @param integer $growing_area_id identifier of the growing_area
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info_by_name($name)
	{
		$query = $this->db->get_where('growing_areas', array('name' => $name), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$growing_area_obj = new stdClass;

			foreach($this->db->list_fields('growing_areas') as $field)
			{
				$growing_area_obj->$field = '';
			}

			return $growing_area_obj;
		}
	}

	public function get_district_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('district');
		$this->db->from('growing_areas');
		$this->db->like('district', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by('district', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->district);
		}

		return $suggestions;
	}

	public function get_state_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('state');
		$this->db->from('growing_areas');
		$this->db->like('state', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by('state', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->state);
		}

		return $suggestions;
	}

	public function get_country_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('country');
		$this->db->from('growing_areas');
		$this->db->like('country', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by('country', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->country);
		}

		return $suggestions;
	}

	/**
	 * Gets information about growing_area as an array of rows
	 *
	 * @param array $growing_area_ids array of growing_area identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($growing_area_ids)
	{
		$this->db->from('growing_areas');
		$this->db->where_in('growing_area_id', $growing_area_ids);
		$this->db->order_by('name', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a growing_area
	 *
	 * @param array $growing_area_data array containing growing_area information
	 *
	 * @param var $growing_area_id identifier of the growing_area to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$growing_area_data, $growing_area_id = FALSE)
	{
		if(!$growing_area_id || !$this->exists($growing_area_id))
		{
			if($this->db->insert('growing_areas', $growing_area_data))
			{
				$growing_area_data['growing_area_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}

		$this->db->where('growing_area_id', $growing_area_id);

		return $this->db->update('growing_areas', $growing_area_data);
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'name', 'asc', TRUE);
	}

	/*
	Perform a search on growing_area
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'name', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(growing_area_id) as count');
		}

		$this->db->from('growing_areas');
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('district', $search);
			$this->db->or_like('state', $search);
			$this->db->or_like('country', $search);
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
	 * Get search suggestions to find growing_area
	 *
	 * @param string $search string containing the term to search in the growing_area table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('growing_area_id');
		$this->db->from('growing_areas');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('district', $search);
			$this->db->or_like('state', $search);
			$this->db->or_like('country', $search);
			$this->db->group_end();
		$this->db->order_by('name', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->growing_area_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one growing_area
	 *
	 * @param integer $growing_area_id growing_area identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($growing_area_id)
	{
		$this->db->where('growing_area_id', $growing_area_id);

		$result &= $this->db->update('growing_areas', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of growing_area
	 *
	 * @param array $growing_area_ids list of growing_area identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($growing_area_ids)
	{
		$this->db->where_in('growing_area_id', $growing_area_ids);

		return $this->db->update('growing_areas', array('deleted' => 1));
 	}
}
?>
