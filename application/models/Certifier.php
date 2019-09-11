<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for CERTIFIER classes
 */

class Certifier extends CI_Model
{
	/**
	 * Determines whether the given CERTIFIER exists in the certifier database table
	 *
	 * @param integer $id identifier of the CERTIFIER to verify the existence
	 *
	 * @return boolean TRUE if the CERTIFIER exists, FALSE if not
	 */
	public function exists($id)
	{
		$this->db->from('certifiers');
		$this->db->where('id', $id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all certifier from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of certifier table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('certifiers');
		$this->db->order_by('name', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of certifier database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('certifiers');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Certifier as an array
	 *
	 * @param integer $id identifier of the Certifier
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($id)
	{
		$query = $this->db->get_where('certifiers', array('id' => $id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Certifier_obj = new stdClass;

			foreach($this->db->list_fields('certifiers') as $field)
			{
				$certifier_obj->$field = '';
			}

			return $certifier_obj;
		}
	}

	/**
	 * Gets information about certifier as an array of rows
	 *
	 * @param array $ids array of certifier identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($ids)
	{
		$this->db->from('certifiers');
		$this->db->where_in('id', $ids);
		$this->db->order_by('name', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Certifier
	 *
	 * @param array $certifier_data array containing Certifier information
	 *
	 * @param var $id identifier of the Certifier to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$certifier_data, $id = FALSE)
	{
		if(!$id || !$this->exists($id))
		{
			if($this->db->insert('certifiers', $certifier_data))
			{
				$certifier_data['id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}

		$this->db->where('id', $id);

		return $this->db->update('certifiers', $certifier_data);
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'name', 'asc', TRUE);
	}

	/*
	Perform a search on certifier
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'name', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(id) as count');
		}

		$this->db->from('certifiers');
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
	 * Get search suggestions to find Certifier
	 *
	 * @param string $search string containing the term to search in the certifier table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('id');
		$this->db->from('certifiers');
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
	 * Deletes one certifier
	 *
	 * @param integer $id Certifier identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($id)
	{
		$this->db->where('id', $id);

		$result &= $this->db->update('certifiers', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of certifier
	 *
	 * @param array $ids list of Certifier identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($ids)
	{
		$this->db->where_in('id', $ids);

		return $this->db->update('certifiers', array('deleted' => 1));
 	}
}
?>
