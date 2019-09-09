<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for DOCTYPESEQUENCE classes
 */

class Doctypesequence extends CI_Model
{
	/**
	 * Determines whether the given DOCTYPESEQUENCE exists in the doctype_sequences database table
	 *
	 * @param integer $sequence_id identifier of the DOCTYPESEQUENCE to verify the existence
	 *
	 * @return boolean TRUE if the DOCTYPESEQUENCE exists, FALSE if not
	 */
	public function exists($sequence_id)
	{
		$this->db->from('doctype_sequences');
		$this->db->where('sequence_id', $sequence_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all doctype_sequences from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of doctype_sequences table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('doctype_sequences');
		$this->db->order_by('name', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of doctype_sequences database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('doctype_sequences');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Doctypesequence as an array
	 *
	 * @param integer $sequence_id identifier of the Doctypesequence
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($sequence_id)
	{
		$query = $this->db->get_where('doctype_sequences', array('sequence_id' => $sequence_id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Doctypesequence_obj = new stdClass;

			foreach($this->db->list_fields('doctype_sequences') as $field)
			{
				$doctypesequence_obj->$field = '';
			}

			return $doctypesequence_obj;
		}
	}

	/**
	 * Gets information about a Doctypesequence as an array
	 *
	 * @param integer $sequence_id identifier of the Doctypesequence
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info_doctype($doctype,$is_cashup)
	{
		$query = $this->db->get_where('doctype_sequences', array('doctype' => $doctype,'is_cashup' => $is_cashup,'deleted' => 0), 1);

		if($query->num_rows() >= 1)
		{
			return $query->result_array();
		}
		else
		{
			//create object with empty properties.
			$Doctypesequence_obj = new stdClass;

			foreach($this->db->list_fields('doctype_sequences') as $field)
			{
				$doctypesequence_obj->$field = '';
			}

			return $doctypesequence_obj;
		}
	}

	/**
	 * Gets information about doctypesequence as an array of rows
	 *
	 * @param array $sequence_ids array of doctypesequence identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($sequence_ids)
	{
		$this->db->from('doctype_sequences');
		$this->db->where_in('sequence_id', $sequence_ids);
		$this->db->order_by('name', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Doctypesequence
	 *
	 * @param array $doctypesequence_data array containing Doctypesequence information
	 *
	 * @param var $sequence_id identifier of the Doctypesequence to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$doctypesequence_data, $sequence_id = FALSE)
	{
		if(!$sequence_id || !$this->exists($sequence_id))
		{
			if($this->db->insert('doctype_sequences', $doctypesequence_data))
			{
				$doctypesequence_data['sequence_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}

		$this->db->where('sequence_id', $sequence_id);

		return $this->db->update('doctype_sequences', $doctypesequence_data);
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'name', 'asc', TRUE);
	}

	/*
	Perform a search on doctypesequence
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'name', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(sequence_id) as count');
		}

		$this->db->from('doctype_sequences');
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
	 * Get search suggestions to find Doctypesequence
	 *
	 * @param string $search string containing the term to search in the doctype_sequences table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('sequence_id');
		$this->db->from('doctype_sequences');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->group_end();
		$this->db->order_by('name', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->sequence_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one doctype_sequences
	 *
	 * @param integer $sequence_id Doctypesequence identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($sequence_id)
	{
		$this->db->where('sequence_id', $sequence_id);

		$result &= $this->db->update('doctype_sequences', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of doctype_sequences
	 *
	 * @param array $sequence_ids list of doctype_sequences identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($sequence_ids)
	{
		$this->db->where_in('sequence_id', $sequence_ids);

		return $this->db->update('doctype_sequences', array('deleted' => 1));
 	}
}
?>
