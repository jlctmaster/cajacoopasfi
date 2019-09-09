<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for cash_concept classes
 */

class Cash_concept extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}


	/**
	 * Determines whether the given cash_concept exists in the cash_concept database table
	 *
	 * @param integer $cash_concept_id identifier of the cash_concept to verify the existence
	 *
	 * @return boolean TRUE if the cash_concept exists, FALSE if not
	 */
	public function exists($cash_concept_id)
	{
		$this->db->from('cash_concepts');
		$this->db->where('cash_concept_id', $cash_concept_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all cash_concept from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of cash_concept table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('cash_concepts');
		$this->db->where('is_internal',0);
		$this->db->order_by('code', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets all cash_concept from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of cash_concept table rows
	 */
	public function get_all_summary($concept_type,$limit = 10000, $offset = 0)
	{
		$this->db->from('cash_concepts');
		$this->db->where('is_internal',0);
		$this->db->where('is_summary',1);
		$this->db->where('concept_type',$concept_type);
		$this->db->where('deleted',0);
		$this->db->where('cash_concept_parent_id IS NULL',null,false);
		$this->db->order_by('code', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets all cash_concept from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of cash_concept table rows
	 */
	public function get_exists_on_cash_daily($concept_type = 0,$limit = 10000, $offset = 0)
	{
		$this->db->from('cash_concepts AS cash_concepts');
		$this->db->where('deleted',0);
		$this->db->where('EXISTS(SELECT 1 FROM '.$this->db->dbprefix('cash_daily').' cash_daily WHERE cash_daily.cash_concept_id = cash_concepts.cash_concept_id)',NULL);
		if($concept_type != 0)
		{
			$this->db->where('concept_type',$concept_type);
		}
		$this->db->order_by('code', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets all cash_concept from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of cash_concept table rows
	 */
	public function get_parent_all($parent_id, $overall_cash = 0, $limit = 10000, $offset = 0)
	{
		$this->db->select('concepts.*');
		$this->db->from('cash_concepts AS concepts');
		$this->db->join('cash_concepts AS subconcept','concepts.cash_concept_parent_id = subconcept.cash_concept_id','left');
		$this->db->where('concepts.is_internal',0);
		$this->db->where('concepts.deleted',0);
		$this->db->where('concepts.is_summary',1);
		$this->db->where('concepts.is_cash_general_used',$overall_cash);
		$this->db->group_start();
		$this->db->where('concepts.cash_concept_parent_id',$parent_id);
		$this->db->or_where('subconcept.cash_concept_parent_id',$parent_id);
		$this->db->group_end();
		$this->db->order_by('concepts.code', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of cash_concept database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('cash_concepts');
		$this->db->where('is_internal',0);
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a cash_concept as an array
	 *
	 * @param integer $cash_concept_id identifier of the cash_concept
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($cash_concept_id)
	{
		$query = $this->db->get_where('cash_concepts', array('cash_concept_id' => $cash_concept_id,'is_internal' => 0), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$cash_concept_obj = new stdClass;

			foreach($this->db->list_fields('cash_concepts') as $field)
			{
				$cash_concept_obj->$field = '';
			}

			return $cash_concept_obj;
		}
	}

	/**
	 * Gets information about a cash_concept as an array
	 *
	 * @param integer $cash_concept_id identifier of the cash_concept
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info_by_code($code)
	{
		$query = $this->db->get_where('cash_concepts', array('code' => $code), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$cash_concept_obj = new stdClass;

			foreach($this->db->list_fields('cash_concepts') as $field)
			{
				$cash_concept_obj->$field = '';
			}

			return $cash_concept_obj;
		}
	}

	/**
	 * Gets information about cash_concept as an array of rows
	 *
	 * @param array $cash_concept_ids array of cash_concept identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($cash_concept_ids)
	{
		$this->db->from('cash_concepts');
		$this->db->where('is_internal',0);
		$this->db->where_in('cash_concept_id', $cash_concept_ids);
		$this->db->order_by('code', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a cash_concept
	 *
	 * @param array $cash_concept_data array containing cash_concept information
	 *
	 * @param var $cash_concept_id identifier of the cash_concept to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$cash_concept_data, $cash_concept_id = FALSE)
	{
		$this->set_log("ID: ".$cash_concept_id);

		if(!$cash_concept_id || !$this->exists($cash_concept_id))
		{
			$this->set_log($this->db->last_query());
			if($this->db->insert('cash_concepts', $cash_concept_data))
			{
				$this->set_log($this->db->last_query());
				$cash_concept_data['cash_concept_id'] = $this->db->insert_id();

				return TRUE;
			}
			$this->set_log($this->db->last_query());

			return FALSE;
		}else{
			$this->set_log($this->db->last_query());	
		}

		$this->db->where('cash_concept_id', $cash_concept_id);

		return $this->db->update('cash_concepts', $cash_concept_data);
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'code', 'asc', TRUE);
	}

	/*
	Perform a search on cash_concept
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'code', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(cash_concept_id) as count');
		}

		$this->db->from('cash_concepts');
		$this->db->where('is_internal',0);
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('code', $search);
		$this->db->group_end();
		$this->db->where('deleted', 0);
		$this->db->where('cash_concept_parent_id IS NULL', null, false);

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
	Perform a search on cash_concept
	*/
	public function search_subconcept($cash_concept_parent_id ,$search, $rows = 0, $limit_from = 0, $sort = 'code', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(concepts.cash_concept_id) as count');
		}
		else{
			$this->db->select(' concepts.*, 
				subconcept.name AS cash_concept_parent_name, 
				'.$cash_concept_parent_id.' AS parent_id,
				(SELECT name FROM iom_cash_concepts WHERE cash_concept_id = '.$cash_concept_parent_id.') AS parent_name,
				(SELECT code FROM iom_cash_concepts WHERE cash_concept_id = '.$cash_concept_parent_id.') AS parent_code');	
		}

		$this->db->from('cash_concepts AS concepts');
		$this->db->join('cash_concepts AS subconcept','concepts.cash_concept_parent_id = subconcept.cash_concept_id');
		$this->db->where('concepts.is_internal',0);
		$this->db->group_start();
			$this->db->like('concepts.name', $search);
			$this->db->or_like('concepts.code', $search);
		$this->db->group_end();
		$this->db->where('concepts.deleted', 0);
		$this->db->group_start();
			$this->db->where('subconcept.cash_concept_parent_id',$cash_concept_parent_id);
			$this->db->or_where('concepts.cash_concept_parent_id',$cash_concept_parent_id);
		$this->db->group_end();

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
	 * Get search suggestions to find cash_concept
	 *
	 * @param string $search string containing the term to search in the cash_concept table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('cash_concept_id');
		$this->db->from('cash_concepts');
		$this->db->where('is_internal',0);
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('code', $search);
			$this->db->group_end();
		$this->db->order_by('name', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->cash_concept_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one cash_concept
	 *
	 * @param integer $cash_concept_id cash_concept identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($cash_concept_id)
	{
		$this->db->where('cash_concept_id', $cash_concept_id);

		$result &= $this->db->update('cash_concepts', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of cash_concept
	 *
	 * @param array $cash_concept_ids list of cash_concept identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($cash_concept_ids)
	{
		$this->db->where_in('cash_concept_id', $cash_concept_ids);

		return $this->db->update('cash_concepts', array('deleted' => 1));
 	}
}
?>
