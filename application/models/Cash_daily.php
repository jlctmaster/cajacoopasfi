<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for CASH_FLOW classes
 */

class Cash_daily extends CI_Model
{
	/**
	 * Determines whether the given CASH_FLOW exists in the cash_daily database table
	 *
	 * @param integer $cash_daily_id identifier of the CASH_FLOW to verify the existence
	 *
	 * @return boolean TRUE if the CASH_FLOW exists, FALSE if not
	 */
	public function exists($cash_daily_id)
	{
		$this->db->from('cash_daily');
		$this->db->where('cash_daily_id', $cash_daily_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Determines whether the given CASH_FLOW exists in the cash_daily database table
	 *
	 * @param integer $cash_daily_id identifier of the CASH_FLOW to verify the existence
	 *
	 * @return boolean TRUE if the CASH_FLOW exists, FALSE if not
	 */
	public function exists_movement($table,$reference_id,$currency,$cash_concept_id = -1)
	{
		$this->db->from('cash_daily');
		$this->db->where('table_reference', $table);
		$this->db->where('reference_id', $reference_id);
		$this->db->where('currency', $currency);

		if($cash_concept_id != -1)
		{
			$this->db->where('cash_concept_id', $cash_concept_id);
		}

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all cash_daily from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of cash_daily table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('cash_daily');
		$this->db->order_by('movementdate', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of cash_daily database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('cash_daily');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Cash_daily as an array
	 *
	 * @param integer $cash_daily_id identifier of the Cash_daily
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($cash_daily_id)
	{
		$query = $this->db->get_where('cash_daily', array('cash_daily_id' => $cash_daily_id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Cash_daily_obj = new stdClass;

			foreach($this->db->list_fields('cash_daily') as $field)
			{
				$cash_daily_obj->$field = '';
			}

			return $cash_daily_obj;
		}
	}

	/**
	 * Gets information about a Cash_daily as an array
	 *
	 * @param integer $cash_daily_id identifier of the Cash_daily
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_initial_balance($cash_book_id,$date)
	{
		$query = $this->db->query("SELECT SUM(amount * (CASE WHEN cash_daily.operation_type = 1 THEN 1 ELSE -1 END)) AS balance 
			FROM ". $this->db->dbprefix('cash_daily')." AS cash_daily 
			WHERE cash_daily.cash_book_id = $cash_book_id AND cash_daily.deleted = 0 
			AND DATE_FORMAT(cash_daily.movementdate,'%Y-%m-%d') < DATE_FORMAT('$date','%Y-%m-%d')");

		//echo $this->db->last_query()."<br>";

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Cash_daily_obj = new stdClass;
			$cash_daily_obj->balance = 0;

			return $cash_daily_obj;
		}
	}

	/**
	 * Gets information about a Cash_daily as an array
	 *
	 * @param integer $cash_daily_id identifier of the Cash_daily
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_balance_by_code($cashup_id,$code)
	{
		$query = $this->db->query("
			SELECT SUM(COALESCE(cd.amount,0)) AS balance 
			FROM ".$this->db->dbprefix('cash_daily')." cd 
			WHERE cd.cashup_id = $cashup_id AND cd.deleted = 0 
			AND cd.cash_concept_id IN (SELECT COALESCE(sc.cash_concept_id,cc.cash_concept_id) FROM ".$this->db->dbprefix('cash_concepts')." sc 
			RIGHT JOIN ".$this->db->dbprefix('cash_concepts')." cc ON sc.cash_concept_parent_id = cc.cash_concept_id 
			WHERE cc.code = '".$code."')");

		//echo $this->db->last_query()."<br>";

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Cash_daily_obj = new stdClass;
			$cash_daily_obj->balance = 0;

			return $cash_daily_obj;
		}
	}

	/**
	 * Gets information about a Cash_daily as an array
	 *
	 * @param integer $cash_daily_id identifier of the Cash_daily
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_vouchers($cash_book_id,$today)
	{
		$query = $this->db->query(" SELECT 
				vouchers.amount-COALESCE((SELECT SUM(amount) FROM ". $this->db->dbprefix('payment_vouchers') ." AS payment_vouchers WHERE vouchers.voucher_id = payment_vouchers.voucher_id),0) AS balance 
				FROM ". $this->db->dbprefix('vouchers') ." AS vouchers
				WHERE vouchers.cash_book_id = $cash_book_id 
				AND DATE_FORMAT(vouchers.voucherdate,'%Y-%m-%d') = DATE_FORMAT('$today','%Y-%m-%d') 
				AND vouchers.deleted = 0 
			");

		//echo $this->db->last_query()."<br>";

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Cash_daily_obj = new stdClass;
			$cash_daily_obj->balance = 0;

			return $cash_daily_obj;
		}
	}

	/**
	 * Gets information about a Cash_daily as an array
	 *
	 * @param integer $cash_daily_id identifier of the Cash_daily
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_expenses($cashup_id)
	{
		$this->db->select('cc.cash_concept_id,cc.code,cc.name AS concept,SUM(cd.amount) AS balance');
		$this->db->from('cash_daily AS cd');
		$this->db->join('cash_concepts AS cc','cc.cash_concept_id = cd.cash_concept_id');
		$this->db->where('cd.cashup_id',$cashup_id);
		$this->db->where('cc.concept_type',3);
		$this->db->group_by(array('cc.cash_concept_id','cc.code','cc.name'));
		$this->db->order_by(array('cc.cash_concept_id','cc.code','cc.name'));

		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
	}

	/**
	 * Gets information about cash_daily as an array of rows
	 *
	 * @param array $cash_daily_ids array of cash_daily identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($cash_daily_ids)
	{
		$this->db->from('cash_daily');
		$this->db->where_in('cash_daily_id', $cash_daily_ids);
		$this->db->order_by('movementdate', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Cash_daily
	 *
	 * @param array $cash_daily_data array containing Cash_daily information
	 *
	 * @param var $cash_daily_id identifier of the Cash_daily to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$cash_daily_data, $cash_daily_id = FALSE, $compare_concepts = FALSE)
	{

		if($compare_concepts)
		{
			if(!$this->exists_movement($cash_daily_data['table_reference'],$cash_daily_data['reference_id'],$cash_daily_data['currency'],$cash_daily_data['cash_concept_id']))
			{
				if($this->db->insert('cash_daily', $cash_daily_data))
				{
					$cash_daily_data['cash_daily_id'] = $this->db->insert_id();

					return TRUE;
				}

				return FALSE;
			}
		}
		else
		{
			if(!$this->exists_movement($cash_daily_data['table_reference'],$cash_daily_data['reference_id'],$cash_daily_data['currency']))
			{
				if($this->db->insert('cash_daily', $cash_daily_data))
				{
					$cash_daily_data['cash_daily_id'] = $this->db->insert_id();

					return TRUE;
				}

				return FALSE;
			}
		}

		$this->db->where('table_reference', $cash_daily_data['table_reference']);
		$this->db->where('reference_id', $cash_daily_data['reference_id']);
		$this->db->where('currency', $cash_daily_data['currency']);

		if($this->db->update('cash_daily', $cash_daily_data))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/*
	Gets rows
	*/
	public function get_found_rows($cashup_id,$search,$filters)
	{
		return $this->search($cashup_id,$search, $filters, 0, 0, 'cash_daily.movementdate', 'asc', TRUE);
	}

	/*
	Perform a search on cash_daily
	*/
	public function search($cashup_id,$search,$filters, $rows = 0, $limit_from = 0, $sort = 'cash_daily.movementdate', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(cash_daily.cash_daily_id) as count');
		}
		else
		{
			$this->db->select('cash_daily.*,
				CASE WHEN cash_daily.operation_type = 1 THEN \'INGRESO\' WHEN cash_daily.operation_type = 2 THEN \'EGRESO\' ELSE \'GASTO\' END AS movementtype,
				cash_concepts.name AS cash_concept_name,
				COALESCE(cash_up.cashup_id,incomes.documentno,costs.documentno,expenses.documentno,loans.loan_id,payment_loans.payment_loan_id,payment_credits.payment_credit_id) AS referenceno,
				CONCAT(\'( \',cash_books.code,\' ) \',stock_locations.location_name,\' \',people.first_name,\' \',people.last_name) AS cash_book_name
				');
		}

		$this->db->from('cash_daily AS cash_daily');
		$this->db->join('cash_concepts AS cash_concepts','cash_concepts.cash_concept_id = cash_daily.cash_concept_id');
		$this->db->join('cash_books AS cash_books','cash_books.cash_book_id = cash_daily.cash_book_id');
		$this->db->join('stock_locations AS stock_locations','cash_books.stock_location_id = stock_locations.location_id');
		$this->db->join('people AS people','cash_books.user_id = people.person_id');
		$this->db->join('cash_up AS cash_up','cash_daily.reference_id = cash_up.cashup_id AND cash_daily.table_reference=\'cash_up\'','LEFT');
		$this->db->join('incomes AS incomes','cash_daily.reference_id = incomes.income_id AND cash_daily.table_reference=\'incomes\'','LEFT');
		$this->db->join('costs AS costs','cash_daily.reference_id = costs.cost_id AND cash_daily.table_reference=\'costs\'','LEFT');
		$this->db->join('expenses AS expenses','cash_daily.reference_id = expenses.expense_id AND cash_daily.table_reference=\'expenses\'','LEFT');
		$this->db->join('loans AS loans','cash_daily.reference_id = loans.loan_id AND cash_daily.table_reference=\'loans\'','LEFT');
		$this->db->join('payment_loans AS payment_loans','cash_daily.reference_id = payment_loans.payment_loan_id AND cash_daily.table_reference=\'payment_loans\'','LEFT');
		$this->db->join('payment_credits AS payment_credits','cash_daily.reference_id = payment_credits.payment_credit_id AND cash_daily.table_reference=\'payment_credits\'','LEFT');
		$this->db->where('cash_daily.cashup_id',$cashup_id);
		$this->db->group_start();
			$this->db->like('cash_daily.description', $search);
		$this->db->group_end();
		$this->db->where('cash_daily.deleted', 0);

		if($filters['operation_type'] != "all")
		{
			$this->db->where('cash_daily.operation_type', $filters['operation_type']);
		}

		if($filters['currency'] != "all")
		{
			$this->db->where('cash_daily.currency', $filters['currency']);
		}

		if($filters['cash_concept_id'] != "all")
		{
			$this->db->where('cash_daily.cash_concept_id', $filters['cash_concept_id']);
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
		return $query;
	}

	/**
	 * Get search suggestions to find Cash_daily
	 *
	 * @param string $search string containing the term to search in the cash_daily table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('cash_daily_id');
		$this->db->from('cash_daily');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('description', $search);
			$this->db->group_end();
		$this->db->order_by('movementdate', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->cash_daily_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one cash_daily
	 *
	 * @param integer $cash_daily_id Cash_daily identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($cash_daily_id)
	{
		$this->db->where('cash_daily_id', $cash_daily_id);

		$result &= $this->db->update('cash_daily', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of cash_daily
	 *
	 * @param array $cash_daily_ids list of Cash_daily identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($cash_daily_ids)
	{
		$this->db->where_in('cash_daily_id', $cash_daily_ids);

		return $this->db->update('cash_daily', array('deleted' => 1));
 	}
}
?>
