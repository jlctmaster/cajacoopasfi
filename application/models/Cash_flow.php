<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

define('CURRENCY', 'PEN');
define('USDCURRENCY', 'USD');

define('CURRENCY_LABEL', 'SOLES');
define('USDCURRENCY_LABEL', 'DÓLAR');

/**
 * Base class for CASH_FLOW classes
 */

class Cash_flow extends CI_Model
{
	/**
	 * Determines whether the given CASH_FLOW exists in the cash_flow database table
	 *
	 * @param integer $cash_flow_id identifier of the CASH_FLOW to verify the existence
	 *
	 * @return boolean TRUE if the CASH_FLOW exists, FALSE if not
	 */
	public function exists($cash_flow_id)
	{
		$this->db->from('cash_flow');
		$this->db->where('cash_flow_id', $cash_flow_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Determines whether the given CASH_FLOW exists in the cash_flow database table
	 *
	 * @param integer $cash_flow_id identifier of the CASH_FLOW to verify the existence
	 *
	 * @return boolean TRUE if the CASH_FLOW exists, FALSE if not
	 */
	public function exists_movement($table,$reference_id,$currency)
	{
		$this->db->from('cash_flow');
		$this->db->where('table_reference', $table);
		$this->db->where('reference_id', $reference_id);
		$this->db->where('currency', $currency);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all cash_flow from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of cash_flow table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('cash_flow');
		$this->db->order_by('movementdate', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of cash_flow database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('cash_flow');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Cash_flow as an array
	 *
	 * @param integer $cash_flow_id identifier of the Cash_flow
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($cash_flow_id)
	{
		$query = $this->db->get_where('cash_flow', array('cash_flow_id' => $cash_flow_id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Cash_flow_obj = new stdClass;

			foreach($this->db->list_fields('cash_flow') as $field)
			{
				$cash_flow_obj->$field = '';
			}

			return $cash_flow_obj;
		}
	}

	/**
	 * Gets information about cash_flow as an array of rows
	 *
	 * @param array $cash_flow_ids array of cash_flow identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($cash_flow_ids)
	{
		$this->db->from('cash_flow');
		$this->db->where_in('cash_flow_id', $cash_flow_ids);
		$this->db->order_by('movementdate', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Cash_flow
	 *
	 * @param array $cash_flow_data array containing Cash_flow information
	 *
	 * @param var $cash_flow_id identifier of the Cash_flow to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$cash_flow_data, $cash_flow_id = FALSE)
	{
		if(!$this->exists_movement($cash_flow_data['table_reference'],$cash_flow_data['reference_id'],$cash_flow_data['currency']))
		{
			if($this->db->insert('cash_flow', $cash_flow_data))
			{
				$cash_flow_data['cash_flow_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}

		$this->db->where('table_reference', $cash_flow_data['table_reference']);
		$this->db->where('reference_id', $cash_flow_data['reference_id']);
		$this->db->where('currency', $cash_flow_data['currency']);

		return $this->db->update('cash_flow', $cash_flow_data);
	}

	/*
	Gets rows
	*/
	public function get_found_rows($overall_cash_id,$search,$filters)
	{
		return $this->search($overall_cash_id,$search, $filters, 0, 0, 'cash_flow.movementdate', 'asc', TRUE);
	}

	/*
	Perform a search on cash_flow
	*/
	public function search($overall_cash_id,$search,$filters, $rows = 0, $limit_from = 0, $sort = 'cash_flow.movementdate', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(cash_flow.cash_flow_id) as count');
		}
		else
		{
			$this->db->select('cash_flow.*,
				CASE WHEN cash_flow.operation_type = 1 THEN \'INGRESO\' ELSE \'EGRESO\' END AS movementtype,
				cash_concepts.name AS cash_concept_name,
				COALESCE(overall_cashs.overall_cash_id,incomes.documentno,costs.documentno) AS referenceno,
				CONCAT(\'( \',cash_books.code,\' ) \',stock_locations.location_name,\' \',people.first_name,\' \',people.last_name) AS cash_book_name
				');
		}

		$this->db->from('cash_flow AS cash_flow');
		$this->db->join('cash_concepts AS cash_concepts','cash_concepts.cash_concept_id = cash_flow.cash_concept_id');
		$this->db->join('cash_books AS cash_books','cash_books.cash_book_id = cash_flow.cash_book_id');
		$this->db->join('stock_locations AS stock_locations','cash_books.stock_location_id = stock_locations.location_id');
		$this->db->join('people AS people','cash_books.user_id = people.person_id');
		$this->db->join('overall_cashs AS overall_cashs','cash_flow.reference_id = overall_cashs.overall_cash_id AND cash_flow.table_reference=\'overall_cashs\'','LEFT');
		$this->db->join('incomes AS incomes','cash_flow.reference_id = incomes.income_id AND cash_flow.table_reference=\'incomes\'','LEFT');
		$this->db->join('costs AS costs','cash_flow.reference_id = costs.cost_id AND cash_flow.table_reference=\'costs\'','LEFT');
		$this->db->where('cash_flow.overall_cash_id',$overall_cash_id);
		$this->db->group_start();
			$this->db->like('cash_flow.description', $search);
		$this->db->group_end();
		$this->db->where('cash_flow.deleted', 0);

		if($filters['operation_type'] != "all")
		{
			$this->db->where('cash_flow.operation_type', $filters['operation_type']);
		}

		if($filters['currency'] != "all")
		{
			$this->db->where('cash_flow.currency', $filters['currency']);
		}

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
	 * Get search suggestions to find Cash_flow
	 *
	 * @param string $search string containing the term to search in the cash_flow table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('cash_flow_id');
		$this->db->from('cash_flow');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('description', $search);
			$this->db->group_end();
		$this->db->order_by('movementdate', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->cash_flow_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one cash_flow
	 *
	 * @param integer $cash_flow_id Cash_flow identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($cash_flow_id)
	{
		$this->db->where('cash_flow_id', $cash_flow_id);

		$result &= $this->db->update('cash_flow', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of cash_flow
	 *
	 * @param array $cash_flow_ids list of Cash_flow identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($cash_flow_ids)
	{
		$this->db->where_in('cash_flow_id', $cash_flow_ids);

		return $this->db->update('cash_flow', array('deleted' => 1));
 	}
}
?>
