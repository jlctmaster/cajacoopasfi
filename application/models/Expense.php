<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for EXPENSE classes
 */

class Expense extends CI_Model
{
	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}

	/**
	 * Determines whether the given EXPENSE exists in the expense database table
	 *
	 * @param integer $expense_id identifier of the EXPENSE to verify the existence
	 *
	 * @return boolean TRUE if the EXPENSE exists, FALSE if not
	 */
	public function exists($expense_id)
	{
		$this->db->from('expenses');
		$this->db->where('expense_id', $expense_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all expense from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of expense table rows
	 */
	public function get_all($cashmovement = 0,$limit = 10000, $offset = 0)
	{
		$this->db->from('expenses');
		$this->db->where('is_cashupmovement',$cashmovement);
		$this->db->order_by('documentno', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of expense database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('expenses');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Expense as an array
	 *
	 * @param integer $expense_id identifier of the Expense
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($expense_id,$cashmovement = 0)
	{
		$this->db->select('expenses.*,
				CASE WHEN expenses.person_id IS NULL THEN expenses.person_name ELSE CONCAT(people.first_name,\' \',people.last_name, \' (\',people.dni,\')\') END AS person,
				CASE WHEN DATE_FORMAT(expenses.documentdate,\'%Y-%m-%d\') = DATE_FORMAT(CURDATE(),\'%Y-%m-%d\') THEN 0 ELSE 1 END AS readonly,
				CASE WHEN expenses.person_id IS NULL THEN expenses.person_name ELSE CONCAT(people.first_name,\' \',people.last_name) END AS name,
				cash_concepts.name AS cash_concept_name,
				cash_subconcepts.name AS cash_subconcept_name,
				CASE WHEN expenses.movementtype = \'C\' AND expenses.currency = \'' . CURRENCY . '\' THEN expenses.amount ELSE 0 END AS cash_amount,
				CASE WHEN expenses.movementtype = \'B\' AND expenses.currency = \'' . CURRENCY . '\' THEN expenses.amount ELSE 0 END AS check_amount');
		$this->db->from('expenses AS expenses');
		$this->db->where('expenses.is_cashupmovement',$cashmovement);
		$this->db->join('people AS people','expenses.person_id = people.person_id','LEFT');
		$this->db->join('cash_concepts AS cash_concepts','expenses.cash_concept_id = cash_concepts.cash_concept_id');
		$this->db->join('cash_concepts AS cash_subconcepts','expenses.cash_subconcept_id = cash_subconcepts.cash_concept_id','LEFT');
		$this->db->where('expense_id',$expense_id);

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Expense_obj = new stdClass;

			foreach($this->db->list_fields('expenses') as $field)
			{
				$expense_obj->$field = '';
			}

			return $expense_obj;
		}
	}

	/**
	 * Gets information about a Expense as an array
	 *
	 * @param integer $expense_id identifier of the Expense
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
		$this->db->from('expenses');
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
			$Expense_obj = new stdClass;

			foreach($this->db->list_fields('expenses') as $field)
			{
				$expense_obj->$field = '';
			}

			return $expense_obj;
		}
	}

	/**
	 * Gets information about expense as an array of rows
	 *
	 * @param array $expense_ids array of expense identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($expense_ids,$cashmovement = 0)
	{
		$this->db->from('expenses');
		$this->db->where('is_cashupmovement',$cashmovement);
		$this->db->where_in('expense_id', $expense_ids);
		$this->db->order_by('documentno', 'asc');

		return $this->db->get();
	}

	public function get_doctype_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('doctype');
		$this->db->from('expenses');
		$this->db->like('doctype', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by('doctype', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->doctype);
		}

		return $suggestions;
	}

	/**
	 * Inserts or updates a Expense
	 *
	 * @param array $expense_data array containing Expense information
	 *
	 * @param var $expense_id identifier of the Expense to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$expense_data, &$cash_movement_data, $expense_id = FALSE, $daily = 0)
	{
		$success = FALSE;

		$this->db->trans_start();

		if(!$expense_id || !$this->exists($expense_id))
		{
			$this->set_log($this->db->last_query());
			$success = $this->db->insert('expenses', $expense_data);
			$this->set_log($this->db->last_query());
			$expense_data['expense_id'] = $this->db->insert_id();
			$expense_id = $expense_data['expense_id'];
		}
		else
		{
			$this->db->where('expense_id', $expense_id);
			$success = $this->db->update('expenses', $expense_data);
			$this->set_log($this->db->last_query());
		}

		//We have either inserted or updated a new user, now lets set permissions.
		if($success)
		{
			foreach($cash_movement_data as $cash_movement)
			{
				$this->set_log($this->db->last_query());
				$cash_movement['reference_id'] = $expense_id;

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
	Perform a search on expense
	*/
	public function search($search, $filters, $cashmovement = 0, $rows = 0, $limit_from = 0, $sort = 'documentno', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(expenses.expense_id) as count');
		}
		else
		{
			$this->db->select('expenses.*,
				CASE WHEN DATE_FORMAT(expenses.documentdate,\'%Y-%m-%d\') = DATE_FORMAT(CURDATE(),\'%Y-%m-%d\') THEN 0 ELSE 1 END AS readonly,
				CASE WHEN expenses.person_id IS NULL THEN expenses.person_name ELSE CONCAT(people.first_name,\' \',people.last_name) END AS name,
				cash_concepts.name AS cash_concept_name,
				cash_subconcepts.name AS cash_subconcept_name,
				CASE WHEN expenses.movementtype = \'C\' AND expenses.currency = \'' . CURRENCY . '\' THEN expenses.amount ELSE 0 END AS cash_amount,
				CASE WHEN expenses.movementtype = \'B\' AND expenses.currency = \'' . CURRENCY . '\' THEN expenses.amount ELSE 0 END AS check_amount');	
		}

		$this->db->from('expenses AS expenses');
		$this->db->join('people AS people','expenses.person_id = people.person_id','LEFT');
		$this->db->join('cash_concepts AS cash_concepts','expenses.cash_concept_id = cash_concepts.cash_concept_id');
		$this->db->join('cash_concepts AS cash_subconcepts','expenses.cash_subconcept_id = cash_subconcepts.cash_concept_id','LEFT');
		$this->db->group_start();
			$this->db->like('expenses.documentno', $search);
			$this->db->or_like('expenses.person_name', $search);
			$this->db->or_like('cash_concepts.name', $search);
		$this->db->group_end();
		$this->db->where('expenses.is_cashupmovement', $cashmovement);
		$this->db->where('expenses.deleted', 0);

		if(empty($this->config->item('date_or_time_format')))
		{
			$this->db->where('DATE_FORMAT(expenses.documentdate, "%Y-%m-%d") BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		}
		else
		{
			$this->db->where('expenses.documentdate BETWEEN ' . $this->db->escape(rawurldecode($filters['start_date'])) . ' AND ' . $this->db->escape(rawurldecode($filters['end_date'])));
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
	 * Get search suggestions to find Expense
	 *
	 * @param string $search string containing the term to search in the expense table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $cashmovement = 0, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('expense_id');
		$this->db->from('expenses');
		$this->db->where('deleted', 0);
		$this->db->where('is_cashupmovement', $cashmovement);
		$this->db->group_start();
			$this->db->like('documentno', $search);
		$this->db->group_end();
		$this->db->order_by('documentno', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->expense_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one expense
	 *
	 * @param integer $expense_id Expense identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($expense_id,$currency,$daily = 0)
	{
		$success = FALSE;

		$this->db->trans_start();
		$this->db->where('expense_id', $expense_id);

		if($this->db->update('expenses', array('deleted' => 1)))
		{
			$success = TRUE;
		}

		$this->set_log($this->db->last_query());

		if($success)
		{
			$this->db->where('table_reference', 'expenses');
			$this->db->where('reference_id', $expense_id);
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
	 * Deletes a list of expense
	 *
	 * @param array $expense_ids list of Expense identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($expense_ids)
	{
		$this->db->where_in('expense_id', $expense_ids);

		return $this->db->update('expenses', array('deleted' => 1));
 	}
}
?>
