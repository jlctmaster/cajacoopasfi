<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for VOUCHER_OPERATION classes
 */

class Voucher_operation extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}

	/**
	 * Determines whether the given VOUCHER_OPERATION exists in the voucher_operation database table
	 *
	 * @param integer $voucher_operation_id identifier of the VOUCHER_OPERATION to verify the existence
	 *
	 * @return boolean TRUE if the VOUCHER_OPERATION exists, FALSE if not
	 */
	public function exists($voucher_operation_id)
	{
		$this->db->from('voucher_operations');
		$this->db->where('voucher_operation_id', $voucher_operation_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all voucher_operation from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of voucher_operation table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('voucher_operations');
		$this->db->order_by('voucher_operation_number', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets all voucher_operation from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of voucher_operation table rows
	 */
	public function get_voucherprepayment_by_person($person_id, $limit = 10000, $offset = 0)
	{
		$this->db->select('voucher_operations.voucher_operation_id,
			voucher_operations.serieno,voucher_operations.voucher_operation_number,
			SUM(costs.amount)-COALESCE(SUM(incomes.amount),0) AS prepayamt 
			');

		$this->db->from('voucher_operations AS voucher_operations');
		$this->db->join('costs AS costs','voucher_operations.voucher_operation_id = costs.voucher_operation_id AND costs.deleted = 0','LEFT');
		$this->db->join('incomes AS incomes','voucher_operations.voucher_operation_id = incomes.voucher_operation_id AND incomes.deleted = 0','LEFT');
		$this->db->where('voucher_operations.person_id',$person_id);
		$this->db->group_by(array('voucher_operations.voucher_operation_id','voucher_operations.serieno','voucher_operations.voucher_operation_number'));
		$this->db->order_by('voucher_operation_number', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		$query = $this->db->get();
		return $query;
	}

	/**
	 * Gets all voucher_operation from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of voucher_operation table rows
	 */
	public function get_voucheropen_by_person($person_id, $limit = 10000, $offset = 0)
	{
		$this->db->select('voucher_operations.voucher_operation_id,
			voucher_operations.serieno,voucher_operations.voucher_operation_number,
			MAX(voucher_operations.amount)-(COALESCE(SUM(costs.amount),0)-COALESCE(SUM(incomes.amount),0)) AS openamt 
			');

		$this->db->from('voucher_operations AS voucher_operations');
		$this->db->join('costs AS costs','voucher_operations.voucher_operation_id = costs.voucher_operation_id AND costs.deleted = 0','LEFT');
		$this->db->join('incomes AS incomes','voucher_operations.voucher_operation_id = incomes.voucher_operation_id AND incomes.deleted = 0','LEFT');
		$this->db->where('voucher_operations.person_id',$person_id);
		$this->db->group_by(array('voucher_operations.voucher_operation_id','voucher_operations.serieno','voucher_operations.voucher_operation_number'));
		$this->db->order_by('voucher_operation_number', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		$query = $this->db->get();
		return $query;
	}

	public function get_openamount($voucher_operation_id)
	{
		$this->db->select('MAX(voucher_operations.amount)-(COALESCE(SUM(costs.amount),0)-COALESCE(SUM(incomes.amount),0)) AS openamt ');
		$this->db->from('voucher_operations AS voucher_operations');
		$this->db->join('costs AS costs','voucher_operations.voucher_operation_id = costs.voucher_operation_id AND costs.deleted = 0','LEFT');
		$this->db->join('incomes AS incomes','voucher_operations.voucher_operation_id = incomes.voucher_operation_id AND incomes.deleted = 0','LEFT');
		$this->db->where('voucher_operations.voucher_operation_id' , $voucher_operation_id);
		$this->db->group_by('voucher_operations.voucher_operation_id');
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
	}

	public function get_prepaymentamount($voucher_operation_id)
	{
		$this->db->select('(COALESCE(SUM(costs.amount),0)-COALESCE(SUM(incomes.amount),0)) AS openamt ');
		$this->db->from('voucher_operations AS voucher_operations');
		$this->db->join('costs AS costs','voucher_operations.voucher_operation_id = costs.voucher_operation_id AND costs.deleted = 0','LEFT');
		$this->db->join('incomes AS incomes','voucher_operations.voucher_operation_id = incomes.voucher_operation_id AND incomes.deleted = 0','LEFT');
		$this->db->where('voucher_operations.voucher_operation_id' , $voucher_operation_id);
		$this->db->group_by('voucher_operations.voucher_operation_id');
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
	}

	/**
	 * Gets total of rows of voucher_operation database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('voucher_operations');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Voucher_operation as an array
	 *
	 * @param integer $voucher_operation_id identifier of the Voucher_operation
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($voucher_operation_id)
	{
		$this->db->select('voucher_operations.*,
				people.dni,
				CONCAT(people.first_name,\' \',people.last_name) AS name,
				CONCAT(people.address_1,\' \',COALESCE(people.address_2,\'\'),\' \',COALESCE(people.city,\'\')) AS address,
				CONCAT(cash_books.code,\' \',stock_locations.location_name) AS cash_book_name 
				');

		$this->db->from('voucher_operations AS voucher_operations');
		$this->db->join('people AS people','voucher_operations.person_id = people.person_id');
		$this->db->join('cash_books AS cash_books','voucher_operations.cash_book_id = cash_books.cash_book_id','LEFT');
		$this->db->join('stock_locations AS stock_locations', 'cash_books.stock_location_id = stock_locations.location_id','LEFT');
		$this->db->where('voucher_operations.voucher_operation_id' , $voucher_operation_id);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Voucher_operation_obj = new stdClass;

			foreach($this->db->list_fields('voucher_operations') as $field)
			{
				$voucher_operation_obj->$field = '';
			}

			return $voucher_operation_obj;
		}
	}

	/**
	 * Gets information about voucher_operation as an array of rows
	 *
	 * @param array $voucher_operation_ids array of voucher_operation identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($voucher_operation_ids)
	{
		$this->db->from('voucher_operations');
		$this->db->where_in('voucher_operation_id', $voucher_operation_ids);
		$this->db->order_by('voucher_operation_number', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Voucher_operation
	 *
	 * @param array $voucher_operation_data array containing Voucher_operation information
	 *
	 * @param var $voucher_operation_id identifier of the Voucher_operation to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$voucher_operation_data, &$quality_certificate_data, $voucher_operation_id = FALSE)
	{

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		if(!$voucher_operation_id || !$this->exists($voucher_operation_id))
		{
			$this->set_log($this->db->last_query());
			$success = $this->db->insert('voucher_operations', $voucher_operation_data);
			$this->set_log($this->db->last_query());
			if($success)
			{
				 $voucher_operation_id = $this->db->insert_id();
				 $voucher_operation_data['voucher_operation_id'] = $voucher_operation_id;
			}
		}
		else
		{
			$this->db->where('voucher_operation_id', $voucher_operation_id);

			$success = $this->db->update('voucher_operations', $voucher_operation_data);
			$this->set_log($this->db->last_query());
		}

		//We have either inserted or updated a new user, now lets set permissions.
		if($success)
		{

			//	UnAllocated vouchers
			$this->db->where('voucher_operation_id', $voucher_operation_id);
			$success = $this->db->update('quality_certificates', array('voucher_operation_id' => NULL));

			$count = 0;
			foreach($quality_certificate_data as $quality)
			{
				$this->db->where('quality_certificate_id', $quality);
				$success = $this->db->update('quality_certificates', array('voucher_operation_id' => $voucher_operation_id));
				$this->set_log($this->db->last_query());
				$count = $count+ 1;
			}
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}

	/**
	*
	**/
	public function update($voucher_operation_data, $cash_daily_data, $voucher_operation_id)
	{
		$success = FALSE;

		$this->db->trans_start();

		//	Update data
		$this->db->where('voucher_operation_id', $voucher_operation_id);
		$success = $this->db->update('voucher_operations', $voucher_operation_data);

		if($success)
		{
			foreach($cash_daily_data as $cash_daily)
			{
				$cash_daily['reference_id'] = $voucher_operation_id;

				$success = $this->Cash_daily->save($cash_daily,-1);
				$this->set_log($this->db->last_query());
				if($success)
				{
					$this->set_log("Instruccion ejecutada con exito!");
				}
				else
				{
					$this->set_log("Error al ejecutar la instruccion!");
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
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'voucher_operation_number', 'asc', TRUE);
	}

	/*
	Perform a search on voucher_operation
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'voucher_operation_number', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(voucher_operations.voucher_operation_id) as count');
		}
		else
		{
			$this->db->select('voucher_operations.*,
				people.dni,
				CONCAT(people.first_name,\' \',people.last_name) AS name,
				(CASE WHEN voucher_operations.liquidatedate IS NULL THEN voucher_operations.amount ELSE 0 END) AS amount_available,
				(CASE WHEN voucher_operations.liquidatedate IS NOT NULL THEN voucher_operations.amount ELSE 0 END) AS cash,
				CONCAT(cash_books.code,\' \',stock_locations.location_name) AS cash_book_name ');
		}

		$this->db->from('voucher_operations AS voucher_operations');
		$this->db->join('people AS people','voucher_operations.person_id = people.person_id');
		$this->db->join('cash_books AS cash_books','voucher_operations.cash_book_id = cash_books.cash_book_id','LEFT');
		$this->db->join('stock_locations AS stock_locations', 'cash_books.stock_location_id = stock_locations.location_id','LEFT');
		$this->db->group_start();
			$this->db->like('voucher_operations.voucher_operation_number', $search);
			$this->db->or_like('voucher_operations.serieno', $search);
			$this->db->or_like('people.dni', $search);
			$this->db->or_like('CONCAT(people.first_name,\' \',people.last_name)', $search);
		$this->db->group_end();
		$this->db->where('voucher_operations.deleted', 0);

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
	 * Get search suggestions to find Voucher_operation
	 *
	 * @param string $search string containing the term to search in the voucher_operation table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('voucher_operation_id');
		$this->db->from('voucher_operations');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('voucher_operation_number', $search);
			$this->db->or_like('serieno', $search);
			$this->db->group_end();
		$this->db->order_by('voucher_operation_number', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->voucher_operation_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one voucher_operation
	 *
	 * @param integer $voucher_operation_id Voucher_operation identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($voucher_operation_id)
	{
		$this->db->where('voucher_operation_id', $voucher_operation_id);

		$result &= $this->db->update('voucher_operations', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of voucher_operation
	 *
	 * @param array $voucher_operation_ids list of Voucher_operation identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($voucher_operation_ids)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->where_in('voucher_operation_id', $voucher_operation_ids);

		$success = $this->db->update('voucher_operations', array('deleted' => 1));

		$this->set_log($this->db->last_query());

		if($success)
		{
			$this->db->where_in('voucher_operation_id', $voucher_operation_ids);

			$success = $this->db->update('quality_certificates', array('voucher_operation_id' => NULL));
			$this->set_log($this->db->last_query());
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
 	}

	public function get_documents_allocated($voucher_operation_id,$person_id)
	{
		$suggestions = array();
		$query = $this->db->query("SELECT 'Vale' AS type,
			'Saldado' AS status,
			SUM(amount) amount 
			FROM ". $this->db->dbprefix('vouchers')." AS vouchers 
			WHERE person_id = ".$person_id." 
			AND (amount-COALESCE((SELECT SUM(amount) FROM ". $this->db->dbprefix('payment_vouchers') ." AS payment_vouchers WHERE vouchers.voucher_id = payment_vouchers.voucher_id),0)) = 0");
		//echo $this->db->last_query();
		foreach($query->result() as $row)
		{
			if(!empty($row->amount))
			{
				$suggestions[] = array(
					'type' => $row->type,
					'status' => $row->status,
					'amount' => $row->amount);
			}
		}

		$query1 = $this->db->query("SELECT 'Vale' AS type,
			'Pendiente' AS status,
			SUM(amount-COALESCE((SELECT SUM(amount) FROM ". $this->db->dbprefix('payment_vouchers') ." AS payment_vouchers WHERE vouchers.voucher_id = payment_vouchers.voucher_id),0)) * -1 AS amount 
			FROM ". $this->db->dbprefix('vouchers')." AS vouchers 
			WHERE person_id = ".$person_id." 
			HAVING SUM(amount-COALESCE((SELECT SUM(amount) FROM ". $this->db->dbprefix('payment_vouchers') ." AS payment_vouchers WHERE vouchers.voucher_id = payment_vouchers.voucher_id),0)) > 0");
		foreach($query1->result() as $row1)
		{
			$suggestions[] = array(
				'type' => $row1->type,
				'status' => $row1->status,
				'amount' => $row1->amount);
		}

		$query2 = $this->db->query("SELECT 'Préstamos' AS type,
			'Saldado' AS status,
			SUM(amount) amount 
			FROM ". $this->db->dbprefix('loans')." AS loans 
			WHERE person_id = ".$person_id." 
			AND ((loans.amount
					+ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_loans.capital) FROM " . $this->db->dbprefix('payment_loans') . " AS payment_loans WHERE loans.loan_id = payment_loans.loan_id),0)) = 0");
		
		foreach($query2->result() as $row2)
		{
			if(!empty($row2->amount))
			{
				$suggestions[] = array(
					'type' => $row2->type,
					'status' => $row2->status,
					'amount' => $row2->amount);
			}
		}

		$query3 = $this->db->query("SELECT 'Préstamos' AS type,
			'Pendiente' AS status,
			SUM((loans.amount
					+ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_loans.capital) FROM " . $this->db->dbprefix('payment_loans') . " AS payment_loans WHERE loans.loan_id = payment_loans.loan_id),0)) * -1 AS amount 
			FROM ". $this->db->dbprefix('loans')." AS loans 
			WHERE person_id = ".$person_id." 
			HAVING SUM((loans.amount
					+ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_loans.capital) FROM " . $this->db->dbprefix('payment_loans') . " AS payment_loans WHERE loans.loan_id = payment_loans.loan_id),0)) > 0");
		foreach($query3->result() as $row3)
		{
			$suggestions[] = array(
				'type' => $row3->type,
				'status' => $row3->status,
				'amount' => $row3->amount);
		}

		$query4 = $this->db->query("SELECT 'Créditos' AS type,
			'Saldado' AS status,
			SUM(amount) amount 
			FROM ". $this->db->dbprefix('credits')." AS credits 
			WHERE person_id = ".$person_id." 
			AND ((credits.amount
					+ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_credits.capital) FROM " . $this->db->dbprefix('payment_credits') . " AS payment_credits WHERE credits.credit_id = payment_credits.credit_id),0)) = 0");
		foreach($query4->result() as $row4)
		{
			if(!empty($row4->amount))
			{
				$suggestions[] = array(
					'type' => $row4->type,
					'status' => $row4->status,
					'amount' => $row4->amount);	
			}
		}

		$query5 = $this->db->query("SELECT 'Créditos' AS type,
			'Pendiente' AS status,
			SUM((credits.amount
					+ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_credits.capital) FROM " . $this->db->dbprefix('payment_credits') . " AS payment_credits WHERE credits.credit_id = payment_credits.credit_id),0)) * -1 AS amount 
			FROM ". $this->db->dbprefix('credits')." AS credits 
			WHERE person_id = ".$person_id." 
			HAVING SUM((credits.amount
					+ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_credits.capital) FROM " . $this->db->dbprefix('payment_credits') . " AS payment_credits WHERE credits.credit_id = payment_credits.credit_id),0)) > 0");
		foreach($query5->result() as $row5)
		{
			$suggestions[] = array(
				'type' => $row5->type,
				'status' => $row5->status,
				'amount' => $row5->amount);
		}

		$query6 = $this->db->query("SELECT 'Anticipo' AS type,
			'Pendiente' AS status,
			SUM(costs.amount-COALESCE(incomes.amount,0)) amount 
			FROM ". $this->db->dbprefix('costs')." AS costs 
			LEFT JOIN ". $this->db->dbprefix('incomes')." AS incomes ON costs.voucher_operation_id = incomes.voucher_operation_id AND incomes.deleted = 0
			WHERE costs.voucher_operation_id = ".$voucher_operation_id." 
			AND costs.deleted = 0");
		//echo $this->db->last_query();
		foreach($query6->result() as $row6)
		{
			if(!empty($row6->amount))
			{
				$suggestions[] = array(
					'type' => $row6->type,
					'status' => $row6->status,
					'amount' => $row6->amount);
			}
		}

		return $suggestions;
	}
}
?>
