<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for LOAN classes
 */

class Loan extends CI_Model
{
	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}

	/**
	 * Determines whether the given LOAN exists in the loan database table
	 *
	 * @param integer $loan_id identifier of the LOAN to verify the existence
	 *
	 * @return boolean TRUE if the LOAN exists, FALSE if not
	 */
	public function exists($loan_id)
	{
		$this->db->from('loans');
		$this->db->where('loan_id', $loan_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all loan from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of loan table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('loans');
		$this->db->order_by('loandate', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of loan database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('loans');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Loan as an array
	 *
	 * @param integer $loan_id identifier of the Loan
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($loan_id)
	{
		$this->db->select('loans.loan_id AS id,
				loans.loan_id,
				loans.loan_type,
				loans.person_id,
				people.dni,
				CONCAT(people.first_name,\' \',people.last_name) AS name,
				loans.motive,
				loans.loandate,
				loans.returndate,
				loans.amount,
				loans.percent,
				(loans.percent * 30) AS percent_monthly,
				loans.amt_interest,
				loans.cuote,
				(loans.amount
					+ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_loans.capital) FROM ' . $this->db->dbprefix('payment_loans') . ' AS payment_loans WHERE loans.loan_id = payment_loans.loan_id),0) AS balance,
				COALESCE((SELECT SUM(payment_loans.amount) FROM ' . $this->db->dbprefix('payment_loans') . ' AS payment_loans 
						WHERE loans.loan_id = payment_loans.loan_id),0) AS pay_amount ');

		$this->db->from('loans AS loans');
		$this->db->join('people AS people','loans.person_id = people.person_id');
		$this->db->where('loans.loan_id', $loan_id);

		$query = $this->db->get();

		//echo $this->db->last_query();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Loan_obj = new stdClass;

			foreach($this->db->list_fields('loans') as $field)
			{
				$loan_obj->$field = '';
			}

			foreach($this->db->list_fields('people') as $field)
			{
				$loan_obj->$field = '';
			}

			return $loan_obj;
		}
	}

	/**
	 * Gets information about a Loan as an array
	 *
	 * @param integer $loan_id identifier of the Loan
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_summary_info($dni)
	{
		$this->db->select('loans.person_id,
				people.dni,
				CONCAT(people.first_name,\' \',people.last_name) AS name,
				SUM((loans.amount
					+ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_loans.capital) FROM ' . $this->db->dbprefix('payment_loans') . ' AS payment_loans WHERE loans.loan_id = payment_loans.loan_id),0)) AS balance');

		$this->db->from('loans AS loans');
		$this->db->join('people AS people','loans.person_id = people.person_id');
		$this->db->where('people.dni', $dni);

		$this->db->group_by(array('loans.person_id','people.dni','people.first_name','people.last_name'));

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Loan_obj = new stdClass;

			foreach($this->db->list_fields('loans') as $field)
			{
				$loan_obj->$field = '';
			}

			foreach($this->db->list_fields('people') as $field)
			{
				$loan_obj->$field = '';
			}

			return $loan_obj;
		}
	}

	/**
	 * Gets information about a Loan as an array
	 *
	 * @param integer $loan_id identifier of the Loan
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_open_loan($person_id)
	{
		$this->db->select('loans.loan_id,
				(loans.amount
					+ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_loans.capital) FROM ' . $this->db->dbprefix('payment_loans') . ' AS payment_loans WHERE loans.loan_id = payment_loans.loan_id),0) AS balance,
				(loans.amount)-SUM(COALESCE(payment_loans.capital,0)) AS capital,
				(ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2)) AS interest');

		$this->db->from('loans AS loans');
		$this->db->join('payment_loans AS payment_loans','loans.loan_id = payment_loans.loan_id','LEFT');
		$this->db->where('loans.person_id', $person_id);

		$this->db->group_by(array('loans.loan_id','loans.amount','loans.amt_interest'));

		$this->db->order_by('loans.loan_id');

		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			//create object with empty properties.
			$loan_obj = new stdClass;

			$loan_obj->$loan_id = '';
			$loan_obj->$balance = '';

			return $loan_obj;
		}
	}

	/**
	 * Gets information about loan as an array of rows
	 *
	 * @param array $loan_ids array of loan identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($loan_ids)
	{
		$this->db->from('loans');
		$this->db->where_in('loan_id', $loan_ids);
		$this->db->order_by('loandate', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Loan
	 *
	 * @param array $loan_data array containing Loan information
	 *
	 * @param var $loan_id identifier of the Loan to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$loan_data, &$cash_daily_data, $loan_id = FALSE)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		if(!$loan_id || !$this->exists($loan_id))
		{
			$this->set_log($this->db->last_query());
			if($this->db->insert('loans', $loan_data))
			{
				$this->set_log($this->db->last_query());
				$loan_id = $this->db->insert_id();

				$loan_data['loan_id'] = $loan_id;				

				$success = TRUE;
			}
			else
			{
				$success = FALSE;
			}
		}
		else
		{
			$this->db->where('loan_id', $loan_id);

			$success = $this->db->update('loans', $loan_data);
			$this->set_log($this->db->last_query());
		}

		if($success)
		{
			foreach($cash_daily_data as $cash_daily)
			{
				$cash_daily['reference_id'] = $loan_id;

				$success = $this->Cash_daily->save($cash_daily,-1);
				$this->set_log($this->db->last_query());
			}
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;	
	}

	/**
	 * Inserts or updates a Loan
	 *
	 * @param array $loan_data array containing Loan information
	 *
	 * @param var $loan_id identifier of the Loan to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save_payment(&$payloan_data, $cash_daily_data)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		if($this->db->insert('payment_loans', $payloan_data))
		{
			$payloan_data['payment_loan_id'] = $this->db->insert_id();
			$this->set_log($this->db->last_query());
			$success = TRUE;
		}

		if($success)
		{
			foreach($cash_daily_data as $cash_daily)
			{
				$cash_daily['reference_id'] = $payloan_data['payment_loan_id'];

				$success = $this->Cash_daily->save($cash_daily,-1,TRUE);
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
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'loans.loandate', 'asc', TRUE);
	}

	/*
	Perform a search on loan
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'loans.loandate', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(loans.loan_id) as count');
		}
		else
		{
			$this->db->select('loans.loan_id,
				people.dni,
				CONCAT(people.first_name,\' \',people.last_name) AS name,
				loans.motive,
				loans.loandate,
				loans.returndate,
				loans.amount,
				loans.amt_interest,
				(loans.amount
					+ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_loans.capital) FROM ' . $this->db->dbprefix('payment_loans') . ' AS payment_loans WHERE loans.loan_id = payment_loans.loan_id),0) AS balance,
				COALESCE((SELECT SUM(payment_loans.amount) FROM ' . $this->db->dbprefix('payment_loans') . ' AS payment_loans 
						WHERE loans.loan_id = payment_loans.loan_id),0) AS pay_amount ');
		}

		$this->db->from('loans AS loans');
		$this->db->join('people AS people','loans.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('people.first_name', $search);
			$this->db->or_like('people.last_name', $search);
			$this->db->or_like('people.dni', $search);
			$this->db->or_like('loans.motive', $search);
		$this->db->group_end();
		$this->db->where('loans.deleted', 0);

		// get_found_rows case
		if($count_only == TRUE)
		{
			return $this->db->get()->row()->count;
		}
		else{
			$this->db->group_by(array('loans.loan_id','people.dni','people.first_name','people.last_name',
				'loans.motive','loans.loandate','loans.returndate','loans.amount','loans.amt_interest'));
		}

		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}

	/*
	Gets rows
	*/
	public function get_summary_found_rows($search)
	{
		return $this->search_summary($search, 0, 0, 'people.dni', 'asc', TRUE);
	}

	/*
	Perform a search on loan
	*/
	public function search_summary($search, $rows = 0, $limit_from = 0, $sort = 'people.dni', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(DISTINCT people.dni) as count');
		}
		else
		{
			$this->db->select('people.dni,
				CONCAT(people.first_name,\' \',people.last_name) AS name,
				MIN(loans.loandate) AS loandate,
				MAX(loans.returndate) AS returndate,
				SUM(loans.amount) AS amount,
				SUM(ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2)) AS amt_interest,
				SUM((loans.amount
					+ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_loans') . ' pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_loans.capital) FROM ' . $this->db->dbprefix('payment_loans') . ' AS payment_loans WHERE loans.loan_id = payment_loans.loan_id),0)) AS balance');
		}

		$this->db->from('loans AS loans');
		$this->db->join('people AS people','loans.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('people.first_name', $search);
			$this->db->or_like('people.last_name', $search);
			$this->db->or_like('people.dni', $search);
		$this->db->group_end();
		$this->db->where('loans.deleted', 0);

		$this->db->group_by(array('people.dni','people.first_name','people.last_name'));

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
	Gets rows
	*/
	public function get_payment_found_rows($id,$type,$search)
	{
		return $this->payment_search($id,$type,$search, 0, 0, 'paydate', 'asc', TRUE);
	}

	/*
	Perform a search on loan
	*/
	public function payment_search($id,$type,$search, $rows = 0, $limit_from = 0, $sort = 'paydate', $order='asc', $count_only = FALSE)
	{

		if($type=="a")
		{
			$temp_table = $this->loan_amortization($id);

			// get_found_rows case
			if($count_only == TRUE)
			{
				$query = $this->db->query('SELECT COUNT(loan_amortization.loan_id) as count
					FROM '.$temp_table.' AS loan_amortization 
					WHERE loan_amortization.loan_id = '.$id);

				return $query->row()->count;
			}
			else
			{
				$sql = 'SELECT loan_amortization.*  
					FROM '.$temp_table.' AS loan_amortization 
					WHERE loan_amortization.loan_id = '.$id.' 
					ORDER BY loan_amortization.cuote ASC '.(($rows > 0) ? 'LIMIT '.$limit_from.','.$rows : '');
				$query = $this->db->query($sql);
				//echo $this->db->last_query();
				return $query;
			}
		}
		else
		{
			// get_found_rows case
			if($count_only == TRUE)
			{
				$this->db->select('COUNT(payment_loans.payment_loan_id) as count');
			}
			else
			{
				$this->db->select('payment_loans.*,loans.amount AS amt_capital,loans.amt_interest,(loans.percent / 100) AS percent,loans.cuote ');
			}

			$this->db->from('payment_loans AS payment_loans');
			$this->db->join('loans AS loans','loans.loan_id = payment_loans.loan_id');
			$this->db->where('payment_loans.loan_id',$id);
			$this->db->group_start();
				$this->db->like('payment_loans.observations', $search);
			$this->db->group_end();
			$this->db->where('payment_loans.deleted', 0);

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
	}

	public function loan_amortization($id)
	{
		$this->db->from('loans');
		$this->db->where('loan_id',$id);

		foreach ($this->db->get()->result() as $row)
		{
			$loandate = $row->loandate;
			$capital = $row->amount;
			$amt_interest = round(($row->amount * (($row->percent * 30) / 100)),2);
			for($x=1;$x<=($row->cuote == 0 ? 1 : $row->cuote);$x++)
			{
				$interest += $amt_interest;
				$balance = $capital + $interest;
				$sql .= 'SELECT '.$id.' AS loan_id,'.$x.' AS cuote, DATE_ADD(\''.$loandate.'\',INTERVAL '.$x.' MONTH) AS estimate_paydate, \''.$this->lang->line('loans_credits_cuote').' '.$x.'\' AS observations, '.$capital.' AS capital, '.$interest.' AS amt_interest, '.$balance.' AS amount UNION ';
			}
			$sql = substr($sql,0,-6);
		}
		return '('.$sql.')';
	}

	/**
	 * Get search suggestions to find Loan
	 *
	 * @param string $search string containing the term to search in the loan table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('loan_id');
		$this->db->from('loans');
		$this->db->join('people','loans.person_id = people.person_id');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search);
			$this->db->or_like('dni', $search);
			$this->db->group_end();
		$this->db->order_by('loandate', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->loan_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one loan
	 *
	 * @param integer $loan_id Loan identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($loan_id)
	{
		$this->db->where('loan_id', $loan_id);

		$result &= $this->db->update('loans', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of loan
	 *
	 * @param array $loan_ids list of Loan identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($loan_ids)
	{
		$this->db->where_in('loan_id', $loan_ids);

		return $this->db->update('loans', array('deleted' => 1));
 	}

	/*
	Gets the payment summary for the expenses (expenses/manage) view
	*/
	public function get_loans_summary($search, $filters)
	{
		// get payment summary
		$this->db->select('SUM(loans.amount) AS amount,SUM(COALESCE(payment_loans.amount,0)) AS payamount');
		$this->db->from('loans AS loans');
		$this->db->from('payment_loans AS payment_loans','payment_loans.loan_id = loans.loan_id','LEFT');
		$this->db->where('loans.deleted', $filters['is_deleted']);

		if(empty($this->config->item('date_or_time_format')))
		{
			$this->db->where('DATE_FORMAT(loans.loandate, "%Y-%m-%d") BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		}
		else
		{
			$this->db->where('loans.loandate BETWEEN ' . $this->db->escape(rawurldecode($filters['start_date'])) . ' AND ' . $this->db->escape(rawurldecode($filters['end_date'])));
		}

		$loans = $this->db->get()->result_array();

		return $loans;
	}

	/**
	 * Gets information about a Loan as an array
	 *
	 * @param integer $loan_id identifier of the Loan
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_balance($id,$paytype)
	{

		if($paytype == 0)
		{
			$select = "MAX((loans.amount
						+ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2))
							-COALESCE((SELECT SUM(payment_loans.capital) FROM " . $this->db->dbprefix('payment_loans') . " AS payment_loans WHERE loans.loan_id = payment_loans.loan_id),0)) AS balance,
					MAX(loans.amount)-SUM(COALESCE(payment_loans.capital,0)) AS capital,
					MAX(ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2)) AS interest,
					MAX(COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),loans.amt_interest)) AS cumulate_interest ";
		}
		else if($paytype == 1)
		{
			$select = "MAX(loans.amount)-SUM(COALESCE(CASE WHEN payment_loans.paytype IN (0,1) THEN payment_loans.capital ELSE 0 END,0)) AS balance,
					MAX(loans.amount)-SUM(COALESCE(payment_loans.capital,0)) AS capital,
					MAX(ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2)) AS interest,
					MAX(COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),loans.amt_interest)) AS cumulate_interest ";
		}
		else{
			$select = "MAX(ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2)) AS balance,
					MAX(loans.amount)-SUM(COALESCE(payment_loans.capital,0)) AS capital,
					MAX(ROUND((((loans.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),0)) * (loans.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id) IS NULL AND CURDATE() = loans.loandate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),0),2)) AS interest,
					MAX(COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_loans') . " pl WHERE pl.loan_id = loans.loan_id ORDER BY payment_loan_id DESC LIMIT 1),loans.amt_interest)) AS cumulate_interest ";
		}

		$sql = "SELECT 
			$select
		FROM ". $this->db->dbprefix('loans') . " AS loans 
		LEFT JOIN ". $this->db->dbprefix('payment_loans') . " AS payment_loans ON loans.loan_id = payment_loans.loan_id 
		WHERE loans.loan_id = $id";

		$query = $this->db->query($sql);

		//echo $this->db->last_query();

		if($query->num_rows() >= 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$loan_obj = new stdClass;
			$loan_obj->$balance = '';

			return $loan_obj;
		}
	}

}
?>
