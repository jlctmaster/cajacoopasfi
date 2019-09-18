<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for PERIOD classes
 */

class Period extends CI_Model
{
	/**
	 * Determines whether the given PERIOD exists in the period database table
	 *
	 * @param integer $id identifier of the PERIOD to verify the existence
	 *
	 * @return boolean TRUE if the PERIOD exists, FALSE if not
	 */
	public function exists($id)
	{
		$this->db->from('periods');
		$this->db->where('id', $id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all period from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of period table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('periods');
		$this->db->order_by('name', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of period database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('periods');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Period as an array
	 *
	 * @param integer $id identifier of the Period
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($id)
	{
		$query = $this->db->get_where('periods', array('id' => $id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Period_obj = new stdClass;

			foreach($this->db->list_fields('periods') as $field)
			{
				$period_obj->$field = '';
			}

			return $period_obj;
		}
	}

	/**
	 * Gets information about period as an array of rows
	 *
	 * @param array $ids array of period identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($ids)
	{
		$this->db->from('periods');
		$this->db->where_in('id', $ids);
		$this->db->order_by('name', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Period
	 *
	 * @param array $period_data array containing Period information
	 *
	 * @param var $id identifier of the Period to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$period_data, $id = FALSE)
	{
		if(!$id || !$this->exists($id))
		{
			if($this->db->insert('periods', $period_data))
			{
				$period_data['id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}

		$this->db->where('id', $id);

		return $this->db->update('periods', $period_data);
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'name', 'asc', TRUE);
	}

	/*
	Perform a search on period
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'name', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(id) as count');
		}

		$this->db->from('periods');
		$this->db->group_start();
		$this->db->like('name', $search);
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
	 * Get search suggestions to find Period
	 *
	 * @param string $search string containing the term to search in the period table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('id');
		$this->db->from('periods');
		$this->db->where('deleted', 0);
		$this->db->group_start();
		$this->db->like('name', $search);
		$this->db->group_end();
		$this->db->order_by('name', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
                    $suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one period
	 *
	 * @param integer $id Period identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($id)
	{
		$this->db->where('id', $id);

		$result &= $this->db->update('periods', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of period
	 *
	 * @param array $ids list of Period identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($ids)
	{
		$this->db->where_in('id', $ids);

		return $this->db->update('periods', array('deleted' => 1));
 	}
}
?>
