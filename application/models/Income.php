<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for INCOME classes
 */

class Income extends CI_Model
{
	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}

	/**
	 * Determines whether the given INCOME exists in the income database table
	 *
	 * @param integer $income_id identifier of the INCOME to verify the existence
	 *
	 * @return boolean TRUE if the INCOME exists, FALSE if not
	 */
	public function exists($income_id)
	{
		$this->db->from('incomes');
		$this->db->where('income_id', $income_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all income from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of income table rows
	 */
	public function get_all($cashupmovement = 0,$limit = 10000, $offset = 0)
	{
		$this->db->from('incomes');
		$this->db->where('is_cashupmovement',$cashupmovement);
		$this->db->order_by('documentno', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of income database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('incomes');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Income as an array
	 *
	 * @param integer $income_id identifier of the Income
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($income_id,$cashupmovement = 0)
	{
		$this->db->select('incomes.*,
				(CASE WHEN incomes.person_id IS NULL THEN incomes.person_name ELSE CONCAT(people.first_name,\' \',people.last_name, \' (\',people.dni,\')\') END) AS person,
				(CASE WHEN incomes.person_id IS NULL THEN incomes.person_name ELSE CONCAT(people.first_name,\' \',people.last_name, \' (\',people.dni,\')\') END) AS name,
				CONCAT(banks.name,\' \',bankaccounts.account_number) AS bank,
				cash_concepts.name AS cash_concept,
				CONCAT(cash_subconcepts.name,COALESCE(CONCAT(\' (\',voucher_operations.serieno,\'-\',voucher_operations.voucher_operation_number,\')\'),\'\')) AS cash_subconcept,
				CASE WHEN incomes.movementtype = \'C\' AND incomes.currency = \'' . CURRENCY . '\' THEN incomes.amount ELSE 0 END AS cash_amount,
				CASE WHEN incomes.movementtype = \'B\' AND incomes.currency = \'' . CURRENCY . '\' THEN incomes.amount ELSE 0 END AS check_amount,
				CASE WHEN incomes.movementtype = \'C\' AND incomes.currency = \'' . USDCURRENCY . '\' THEN incomes.amount ELSE 0 END AS cash_usdamount,
				CASE WHEN incomes.movementtype = \'B\' AND incomes.currency = \'' . USDCURRENCY . '\' THEN incomes.amount ELSE 0 END AS check_usdamount');
		$this->db->from('incomes AS incomes');
		$this->db->join('cash_concepts AS cash_concepts','cash_concepts.cash_concept_id = incomes.cash_concept_id');
		$this->db->join('cash_concepts AS cash_subconcepts','cash_subconcepts.cash_concept_id = incomes.cash_subconcept_id');
		$this->db->join('people AS people','incomes.person_id = people.person_id','LEFT');
		$this->db->join('bankaccounts AS bankaccounts','incomes.bankaccount_id = bankaccounts.bankaccount_id','LEFT');
		$this->db->join('banks AS banks','banks.bank_id = bankaccounts.bank_id','LEFT');
		$this->db->join('voucher_operations AS voucher_operations','incomes.voucher_operation_id = voucher_operations.voucher_operation_id','LEFT');
		$this->db->where('incomes.income_id',$income_id);
		$this->db->where('incomes.is_cashupmovement',$cashupmovement);

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Income_obj = new stdClass;

			foreach($this->db->list_fields('incomes') as $field)
			{
				$income_obj->$field = '';
			}

			return $income_obj;
		}
	}

	/**
	 * Gets information about a Income as an array
	 *
	 * @param integer $income_id identifier of the Income
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_summary_info($cashupmovement = 0)
	{

		$this->db->select('
				SUM(CASE WHEN movementtype = \'C\' AND currency = \'' . CURRENCY . '\' THEN amount ELSE 0 END) AS cash_amount,
				SUM(CASE WHEN movementtype = \'B\' AND currency = \'' . CURRENCY . '\' THEN amount ELSE 0 END) AS check_amount,
				SUM(CASE WHEN movementtype = \'C\' AND currency = \'' . USDCURRENCY . '\' THEN amount ELSE 0 END) AS cash_usdamount,
				SUM(CASE WHEN movementtype = \'B\' AND currency = \'' . USDCURRENCY . '\' THEN amount ELSE 0 END) AS check_usdamount');
		$this->db->from('incomes');
		$this->db->where('is_cashupmovement',$cashupmovement);
		$this->db->where('deleted',0);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Income_obj = new stdClass;

			foreach($this->db->list_fields('incomes') as $field)
			{
				$income_obj->$field = '';
			}

			return $income_obj;
		}
	}

	/**
	 * Gets information about income as an array of rows
	 *
	 * @param array $income_ids array of income identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($income_ids,$cashupmovement = 0)
	{
		$this->db->from('incomes');
		$this->db->where_in('income_id', $income_ids);
		$this->db->where('is_cashupmovement',$cashupmovement);
		$this->db->order_by('documentno', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Income
	 *
	 * @param array $income_data array containing Income information
	 *
	 * @param var $income_id identifier of the Income to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$income_data, &$cash_movement_data, $income_id = FALSE, $daily = 0)
	{
		$success = FALSE;

		$this->db->trans_start();

		if(!$income_id || !$this->exists($income_id))
		{
			$this->set_log($this->db->last_query());
			$success = $this->db->insert('incomes', $income_data);
			$this->set_log($this->db->last_query());
			$income_data['income_id'] = $this->db->insert_id();
			$income_id = $income_data['income_id'];
		}
		else
		{
			$this->db->where('income_id', $income_id);
			$success = $this->db->update('incomes', $income_data);
			$this->set_log($this->db->last_query());
		}

		//We have either inserted or updated a new user, now lets set permissions.
		if($success)
		{
			foreach($cash_movement_data as $cash_movement)
			{
				$this->set_log($this->db->last_query());
				$cash_movement['reference_id'] = $income_id;

				if($daily == 0)
				{
					$success = $this->Cash_flow->save($cash_movement,-1);
					$this->set_log($this->db->last_query());
				}
				else
				{
					$success = $this->Cash_daily->save($cash_movement,-1);
					$this->set_log($this->db->last_query());
				}
			}
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search,$filters,$cashupmovement = 0)
	{
		return $this->search($search, $filters, $cashupmovement, 0, 0, 'documentno', 'asc', TRUE);
	}

	/*
	Perform a search on income
	*/
	public function search($search, $filters, $cashupmovement = 0, $rows = 0, $limit_from = 0, $sort = 'documentno', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(incomes.income_id) as count');
		}
		else
		{
			$this->db->select('incomes.*,
				CASE WHEN DATE_FORMAT(incomes.documentdate,\'%Y-%m-%d\') = DATE_FORMAT(CURDATE(),\'%Y-%m-%d\') THEN 0 ELSE 1 END AS readonly,
				(CASE WHEN incomes.person_id IS NULL THEN incomes.person_name ELSE CONCAT(people.first_name,\' \',people.last_name, \' (\',people.dni,\')\') END) AS name,
				CONCAT(banks.name,\' \',bankaccounts.account_number) AS bank,
				cash_concepts.name AS cash_concept,
				CONCAT(cash_subconcepts.name,COALESCE(CONCAT(\' (\',voucher_operations.serieno,\'-\',voucher_operations.voucher_operation_number,\')\'),\'\')) AS cash_subconcept,
				CASE WHEN incomes.movementtype = \'C\' AND incomes.currency = \'' . CURRENCY . '\' THEN incomes.amount ELSE 0 END AS cash_amount,
				CASE WHEN incomes.movementtype = \'B\' AND incomes.currency = \'' . CURRENCY . '\' THEN incomes.amount ELSE 0 END AS check_amount,
				CASE WHEN incomes.movementtype = \'C\' AND incomes.currency = \'' . USDCURRENCY . '\' THEN incomes.amount ELSE 0 END AS cash_usdamount,
				CASE WHEN incomes.movementtype = \'B\' AND incomes.currency = \'' . USDCURRENCY . '\' THEN incomes.amount ELSE 0 END AS check_usdamount');	
		}

		$this->db->from('incomes AS incomes');
		$this->db->join('cash_concepts AS cash_concepts','cash_concepts.cash_concept_id = incomes.cash_concept_id');
		$this->db->join('cash_concepts AS cash_subconcepts','cash_subconcepts.cash_concept_id = incomes.cash_subconcept_id');
		$this->db->join('people AS people','incomes.person_id = people.person_id','LEFT');
		$this->db->join('bankaccounts AS bankaccounts','incomes.bankaccount_id = bankaccounts.bankaccount_id','LEFT');
		$this->db->join('banks AS banks','banks.bank_id = bankaccounts.bank_id','LEFT');
		$this->db->join('voucher_operations AS voucher_operations','incomes.voucher_operation_id = voucher_operations.voucher_operation_id','LEFT');
		$this->db->group_start();
			$this->db->like('incomes.documentno', $search);
			$this->db->or_like('CONCAT(people.first_name,\' \',people.last_name)', $search);
			$this->db->or_like('cash_concepts.name', $search);
			$this->db->or_like('cash_subconcepts.name', $search);
			$this->db->or_like('CONCAT(banks.name,\' \',bankaccounts.account_number)', $search);
		$this->db->group_end();
		$this->db->where('incomes.is_cashupmovement',$cashupmovement);
		$this->db->where('incomes.deleted', 0);

		if(empty($this->config->item('date_or_time_format')))
		{
			$this->db->where('DATE_FORMAT(incomes.documentdate, "%Y-%m-%d") BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		}
		else
		{
			$this->db->where('incomes.documentdate BETWEEN ' . $this->db->escape(rawurldecode($filters['start_date'])) . ' AND ' . $this->db->escape(rawurldecode($filters['end_date'])));
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
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query;
	}

	/**
	 * Get search suggestions to find Income
	 *
	 * @param string $search string containing the term to search in the income table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $cashupmovement = 0, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('income_id');
		$this->db->from('incomes');
		$this->db->where('is_cashupmovement',$cashupmovement);
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('documentno', $search);
		$this->db->group_end();
		$this->db->order_by('documentno', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->income_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one income
	 *
	 * @param integer $income_id Income identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($income_id,$currency,$daily = 0)
	{
		$success = FALSE;

		$this->db->trans_start();
		$this->db->where('income_id', $income_id);

		if($this->db->update('incomes', array('deleted' => 1)))
		{
			$success = TRUE;
		}

		$this->set_log($this->db->last_query());

		if($success)
		{
			$this->db->where('table_reference', 'incomes');
			$this->db->where('reference_id', $income_id);
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
	}

	/**
	 * Deletes a list of income
	 *
	 * @param array $income_ids list of Income identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($income_ids)
	{
		$this->db->where_in('income_id', $income_ids);

		return $this->db->update('incomes', array('deleted' => 1));
 	}
}
?>
