<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for COST classes
 */

class Cost extends CI_Model
{
	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}

	/**
	 * Determines whether the given COST exists in the cost database table
	 *
	 * @param integer $cost_id identifier of the COST to verify the existence
	 *
	 * @return boolean TRUE if the COST exists, FALSE if not
	 */
	public function exists($cost_id)
	{
		$this->db->from('costs');
		$this->db->where('cost_id', $cost_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all cost from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of cost table rows
	 */
	public function get_all($cashmovement = 0,$limit = 10000, $offset = 0)
	{
		$this->db->from('costs');
		$this->db->where('is_cashupmovement',$cashmovement);
		$this->db->order_by('documentno', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of cost database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('costs');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Cost as an array
	 *
	 * @param integer $cost_id identifier of the Cost
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($cost_id,$cashmovement = 0)
	{
		$this->db->select('costs.*,
				CONCAT(people.first_name,\' \',people.last_name, \' (\',people.dni,\')\') AS person,
				CONCAT(people.first_name,\' \',people.last_name) AS name,
				cash_concepts.name AS cash_concept_name,
				CONCAT(cash_subconcepts.name,COALESCE(CONCAT(\' (\',voucher_operations.serieno,\'-\',voucher_operations.voucher_operation_number,\')\'),\'\')) AS cash_subconcept_name,
				CASE WHEN costs.movementtype = \'C\' AND costs.currency = \'' . CURRENCY . '\' THEN costs.amount ELSE 0 END AS cash_amount,
				CASE WHEN costs.movementtype = \'B\' AND costs.currency = \'' . CURRENCY . '\' THEN costs.amount ELSE 0 END AS check_amount');
		$this->db->from('costs AS costs');
		$this->db->where('costs.is_cashupmovement',$cashmovement);
		$this->db->join('people AS people','costs.person_id = people.person_id');
		$this->db->join('cash_concepts AS cash_concepts','costs.cash_concept_id = cash_concepts.cash_concept_id');
		$this->db->join('cash_concepts AS cash_subconcepts','costs.cash_subconcept_id = cash_subconcepts.cash_concept_id');
		$this->db->join('voucher_operations AS voucher_operations','costs.voucher_operation_id = voucher_operations.voucher_operation_id','LEFT');
		$this->db->where('cost_id',$cost_id);

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Cost_obj = new stdClass;

			foreach($this->db->list_fields('costs') as $field)
			{
				$cost_obj->$field = '';
			}

			return $cost_obj;
		}
	}

	/**
	 * Gets information about a Cost as an array
	 *
	 * @param integer $cost_id identifier of the Cost
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_summary_info($cashmovement = 0)
	{

		$this->db->select('
				SUM(CASE WHEN movementtype = \'C\' AND currency = \'' . CURRENCY . '\' THEN amount ELSE 0 END) AS cash_amount,
				SUM(CASE WHEN movementtype = \'B\' AND currency = \'' . CURRENCY . '\' THEN amount ELSE 0 END) AS check_amount,
				SUM(CASE WHEN movementtype = \'C\' AND currency = \'' . USDCURRENCY . '\' THEN amount ELSE 0 END) AS cash_usdamount,
				SUM(CASE WHEN movementtype = \'B\' AND currency = \'' . USDCURRENCY . '\' THEN amount ELSE 0 END) AS check_usdamount');
		$this->db->from('costs');
		$this->db->where('is_cashupmovement',$cashmovement);
		$this->db->where('deleted',0);
		$query = $this->db->get();
		//echo $this->db->last_query();
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Cost_obj = new stdClass;

			foreach($this->db->list_fields('costs') as $field)
			{
				$cost_obj->$field = '';
			}

			return $cost_obj;
		}
	}

	/**
	 * Gets information about cost as an array of rows
	 *
	 * @param array $cost_ids array of cost identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($cost_ids,$cashmovement = 0)
	{
		$this->db->from('costs');
		$this->db->where('is_cashupmovement',$cashmovement);
		$this->db->where_in('cost_id', $cost_ids);
		$this->db->order_by('documentno', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Cost
	 *
	 * @param array $cost_data array containing Cost information
	 *
	 * @param var $cost_id identifier of the Cost to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$cost_data, &$cash_movement_data, $cost_id = FALSE, $daily = 0)
	{
		$success = FALSE;

		$this->db->trans_start();

		if(!$cost_id || !$this->exists($cost_id))
		{
			$this->set_log($this->db->last_query());
			$success = $this->db->insert('costs', $cost_data);
			$this->set_log($this->db->last_query());
			$cost_data['cost_id'] = $this->db->insert_id();
			$cost_id = $cost_data['cost_id'];
		}
		else
		{
			$this->db->where('cost_id', $cost_id);
			$success = $this->db->update('costs', $cost_data);
			$this->set_log($this->db->last_query());
		}

		//We have either inserted or updated a new user, now lets set permissions.
		if($success)
		{
			foreach($cash_movement_data as $cash_movement)
			{
				$this->set_log($this->db->last_query());
				$cash_movement['reference_id'] = $cost_id;

				if($daily == 0)
				{
					$success = $this->Cash_flow->save($cash_movement,-1);
				}
				else
				{
					$success = $this->Cash_daily->save($cash_movement,-1);
				}
				$this->set_log($this->db->last_query());
			}
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search, $filters, $cashmovement = 0)
	{
		return $this->search($search, $filters, $cashmovement, 0, 0, 'documentno', 'asc', TRUE);
	}

	/*
	Perform a search on cost
	*/
	public function search($search, $filters, $cashmovement = 0, $rows = 0, $limit_from = 0, $sort = 'documentno', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(costs.cost_id) as count');
		}
		else
		{
			$this->db->select('costs.*,
				CASE WHEN DATE_FORMAT(costs.documentdate,\'%Y-%m-%d\') = DATE_FORMAT(CURDATE(),\'%Y-%m-%d\') THEN 0 ELSE 1 END AS readonly,
				CONCAT(people.first_name,\' \',people.last_name) AS name,
				cash_concepts.name AS cash_concept_name,
				CONCAT(cash_subconcepts.name,COALESCE(CONCAT(\' (\',voucher_operations.serieno,\'-\',voucher_operations.voucher_operation_number,\')\'),\'\')) AS cash_subconcept_name,
				CASE WHEN costs.movementtype = \'C\' AND costs.currency = \'' . CURRENCY . '\' THEN costs.amount ELSE 0 END AS cash_amount,
				CASE WHEN costs.movementtype = \'B\' AND costs.currency = \'' . CURRENCY . '\' THEN costs.amount ELSE 0 END AS check_amount');	
		}

		$this->db->from('costs AS costs');
		$this->db->join('people AS people','costs.person_id = people.person_id');
		$this->db->join('cash_concepts AS cash_concepts','costs.cash_concept_id = cash_concepts.cash_concept_id');
		$this->db->join('cash_concepts AS cash_subconcepts','costs.cash_subconcept_id = cash_subconcepts.cash_concept_id');
		$this->db->join('voucher_operations AS voucher_operations','costs.voucher_operation_id = voucher_operations.voucher_operation_id','LEFT');
		$this->db->group_start();
			$this->db->like('costs.documentno', $search);
			$this->db->or_like('CONCAT(people.first_name,\' \',people.last_name)', $search);
			$this->db->or_like('cash_concepts.name', $search);
		$this->db->group_end();
		$this->db->where('costs.is_cashupmovement', $cashmovement);
		$this->db->where('costs.deleted', 0);

		if(empty($this->config->item('date_or_time_format')))
		{
			$this->db->where('DATE_FORMAT(costs.documentdate, "%Y-%m-%d") BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		}
		else
		{
			$this->db->where('costs.documentdate BETWEEN ' . $this->db->escape(rawurldecode($filters['start_date'])) . ' AND ' . $this->db->escape(rawurldecode($filters['end_date'])));
		}

		// get_found_rows case
		if($count_only == TRUE)
		{
			$query = $this->db->get();
			//echo $this->db->last_query();
			return $query->row()->count;
		}

		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		$query = $this->db->get();
		//echo $this->db->last_query();

		return $query;
	}

	/**
	 * Get search suggestions to find Cost
	 *
	 * @param string $search string containing the term to search in the cost table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $cashmovement = 0, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('cost_id');
		$this->db->from('costs');
		$this->db->where('deleted', 0);
		$this->db->where('is_cashupmovement', $cashmovement);
		$this->db->group_start();
			$this->db->like('documentno', $search);
		$this->db->group_end();
		$this->db->order_by('documentno', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->cost_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one cost
	 *
	 * @param integer $cost_id Cost identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($cost_id,$currency,$daily = 0)
	{
		$success = FALSE;

		$this->db->trans_start();
		$this->db->where('cost_id', $cost_id);

		if($this->db->update('costs', array('deleted' => 1)))
		{
			$success = TRUE;
		}

		$this->set_log($this->db->last_query());

		if($success)
		{
			$this->db->where('table_reference', 'costs');
			$this->db->where('reference_id', $cost_id);
			$this->db->where('currency', $currency);

			if($daily == 0)
			{
				if($this->db->update('cash_flow', array('deleted' => 1)))
				{
					$success = TRUE;
				}
			}
			else
			{
				if($this->db->update('cash_daily', array('deleted' => 1)))
				{
					$success = TRUE;
				}
			}

			$this->set_log($this->db->last_query());
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
		
		return $success;
	}

	/**
	 * Deletes a list of cost
	 *
	 * @param array $cost_ids list of Cost identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($cost_ids)
	{
		$this->db->where_in('cost_id', $cost_ids);

		return $this->db->update('costs', array('deleted' => 1));
 	}
}
?>
