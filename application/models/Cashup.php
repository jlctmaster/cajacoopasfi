<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cashup class
 */

class Cashup extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}
	/*
	Determines if a given Cashup_id is an Cashup
	*/
	public function exists($cashup_id)
	{
		$this->db->from('cash_up');
		$this->db->where('cashup_id', $cashup_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/*
	Gets employee info
	*/
	public function get_employee($cashup_id)
	{
		$this->db->from('cash_up');
		$this->db->where('cashup_id', $cashup_id);

		return $this->Employee->get_info($this->db->get()->row()->employee_id);
	}

	public function get_multiple_info($cash_up_ids)
	{
		$this->db->from('cash_up');
		$this->db->where_in('cashup_id', $cashup_ids);
		$this->db->order_by('cashup_id', 'asc');

		return $this->db->get();
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search, $filters)
	{
		return $this->search($search, $filters, 0, 0, 'cashup_id', 'asc', TRUE);
	}

	/*
	Searches cashups
	*/
	public function search($search, $filters, $rows = 0, $limit_from = 0, $sort = 'cashup_id', $order = 'asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(cash_up.cashup_id) as count');
		}

		$this->db->select('
			cash_up.cashup_id,
			MAX(cash_books.code) AS cash_book,
			MAX(cash_up.open_date) AS open_date,
			MAX(cash_up.close_date) AS close_date,
			MAX(cash_up.open_amount_cash) AS open_amount_cash,
			MAX(cash_up.transfer_amount_cash) AS transfer_amount_cash,
			MAX(cash_up.closed_amount_cash) AS closed_amount_cash,
			MAX(cash_up.closed_amount_due) AS closed_amount_due,
			MAX(cash_up.closed_amount_card) AS closed_amount_card,
			MAX(cash_up.closed_amount_check) AS closed_amount_check,
			COALESCE(SUM(cash_daily.amount * (CASE WHEN cash_daily.operation_type = 1 THEN 1 ELSE -1 END)),MAX(cash_up.closed_amount_total))+COALESCE((SELECT SUM(initial.amount * (CASE WHEN initial.operation_type = 1 THEN 1 ELSE -1 END)) FROM '. $this->db->dbprefix('cash_daily').' AS initial WHERE initial.cash_book_id = cash_up.cash_book_id AND initial.deleted = 0 AND initial.cashup_id < cash_up.cashup_id),0) AS closed_amount_total,
			MAX(cash_up.description) AS description,
			MAX(cash_up.note) AS note,
			MAX(cash_up.open_employee_id) AS open_employee_id,
			MAX(cash_up.close_employee_id) AS close_employee_id,
			MAX(open_employees.first_name) AS open_first_name,
			MAX(open_employees.last_name) AS open_last_name,
			MAX(close_employees.first_name) AS close_first_name,
			MAX(close_employees.last_name) AS close_last_name,
			SUM(COALESCE((CASE WHEN cash_daily.operation_type = 1 AND cash_daily.currency = \'' . CURRENCY . '\' THEN cash_daily.amount ELSE 0 END),0)) AS income,
			SUM(COALESCE((CASE WHEN cash_daily.operation_type = 2 AND cash_daily.isbankmovement = 0 AND cash_daily.currency = \'' . CURRENCY . '\' THEN cash_daily.amount ELSE 0 END),0)) AS cost_cash,
			SUM(COALESCE((CASE WHEN cash_daily.operation_type = 2 AND cash_daily.isbankmovement = 1 AND cash_daily.currency = \'' . CURRENCY . '\' THEN cash_daily.amount ELSE 0 END),0)) AS cost_bank,
			SUM(COALESCE((CASE WHEN cash_daily.operation_type = 3 AND cash_daily.currency = \'' . CURRENCY . '\' THEN cash_daily.amount ELSE 0 END),0)) AS expense,
			CASE WHEN MAX(cash_up.close_date) IS NULL THEN 0 ELSE 1 END state,
			COALESCE((SELECT SUM(initial.amount * (CASE WHEN initial.operation_type = 1 THEN 1 ELSE -1 END)) FROM '. $this->db->dbprefix('cash_daily').' AS initial WHERE initial.cash_book_id = cash_up.cash_book_id AND initial.deleted = 0 AND initial.cashup_id < cash_up.cashup_id),0) AS initial_balance 
		');
		$this->db->from('cash_up AS cash_up');
		$this->db->join('cash_books AS cash_books', 'cash_books.cash_book_id = cash_up.cash_book_id');
		$this->db->join('people AS open_employees', 'open_employees.person_id = cash_up.open_employee_id', 'LEFT');
		$this->db->join('people AS close_employees', 'close_employees.person_id = cash_up.close_employee_id', 'LEFT');
		$this->db->join('cash_daily AS cash_daily', 'cash_daily.cashup_id = cash_up.cashup_id AND cash_daily.deleted = 0', 'LEFT');
		$this->db->where('cash_up.open_employee_id',$filters['user_id']);
		$this->db->group_start();
			$this->db->like('cash_up.open_date', $search);
			$this->db->or_like('open_employees.first_name', $search);
			$this->db->or_like('open_employees.last_name', $search);
			$this->db->or_like('close_employees.first_name', $search);
			$this->db->or_like('close_employees.last_name', $search);
			$this->db->or_like('cash_up.closed_amount_total', $search);
			$this->db->or_like('CONCAT(open_employees.first_name, " ", open_employees.last_name)', $search);
			$this->db->or_like('CONCAT(close_employees.first_name, " ", close_employees.last_name)', $search);
		$this->db->group_end();

		$this->db->where('cash_up.deleted', $filters['is_deleted']);

		if(empty($this->config->item('date_or_time_format')))
		{
			$this->db->where('DATE_FORMAT(cash_up.open_date, "%Y-%m-%d") BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		}
		else
		{
			$this->db->where('cash_up.open_date BETWEEN ' . $this->db->escape(rawurldecode($filters['start_date'])) . ' AND ' . $this->db->escape(rawurldecode($filters['end_date'])));
		}

		$this->db->group_by('cashup_id');

		// get_found_rows case
		if($count_only == TRUE)
		{
			$query = $this->db->get();
			return $query->row_array()['count'];
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

	/*
	Gets information about a particular cashup
	*/
	public function get_info($cashup_id)
	{

		$this->db->select('
			cash_up.cashup_id AS cashup_id,
			MAX(cash_up.cash_book_id) AS cash_book_id,
			MAX(cash_up.deleted) AS deleted,
			MAX(cash_books.code) AS code,
			MAX(cash_up.open_date) AS open_date,
			MAX(cash_up.close_date) AS close_date,
			MAX(cash_up.open_amount_cash) AS open_amount_cash,
			MAX(cash_up.transfer_amount_cash) AS transfer_amount_cash,
			MAX(cash_up.closed_amount_cash) AS closed_amount_cash,
			MAX(cash_up.closed_amount_due) AS closed_amount_due,
			MAX(cash_up.closed_amount_card) AS closed_amount_card,
			MAX(cash_up.closed_amount_check) AS closed_amount_check,
			COALESCE(SUM(cash_daily.amount * (CASE WHEN cash_daily.operation_type = 1 THEN 1 ELSE -1 END)),MAX(cash_up.closed_amount_total))+COALESCE((SELECT SUM(initial.amount * (CASE WHEN initial.operation_type = 1 THEN 1 ELSE -1 END)) FROM '. $this->db->dbprefix('cash_daily').' AS initial WHERE initial.cash_book_id = cash_up.cash_book_id AND initial.deleted = 0 AND initial.cashup_id < cash_up.cashup_id),0) AS closed_amount_total,
			MAX(cash_up.description) AS description,
			MAX(cash_up.note) AS note,
			MAX(cash_up.open_employee_id) AS open_employee_id,
			MAX(cash_up.close_employee_id) AS close_employee_id,
			MAX(open_employees.first_name) AS open_first_name,
			MAX(open_employees.last_name) AS open_last_name,
			MAX(close_employees.first_name) AS close_first_name,
			MAX(close_employees.last_name) AS close_last_name,
			SUM(COALESCE((CASE WHEN cash_daily.operation_type = 1 AND cash_daily.currency = \'' . CURRENCY . '\' THEN cash_daily.amount ELSE 0 END),0)) AS income,
			SUM(COALESCE((CASE WHEN cash_daily.operation_type = 2 AND cash_daily.isbankmovement = 0 AND cash_daily.currency = \'' . CURRENCY . '\' THEN cash_daily.amount ELSE 0 END),0)) AS cost_cash,
			SUM(COALESCE((CASE WHEN cash_daily.operation_type = 2 AND cash_daily.isbankmovement = 1 AND cash_daily.currency = \'' . CURRENCY . '\' THEN cash_daily.amount ELSE 0 END),0)) AS cost_bank,
			SUM(COALESCE((CASE WHEN cash_daily.operation_type = 3 AND cash_daily.currency = \'' . CURRENCY . '\' THEN cash_daily.amount ELSE 0 END),0)) AS expense,
			CASE WHEN MAX(cash_up.close_date) IS NULL THEN 0 ELSE 1 END state,
			COALESCE((SELECT SUM(initial.amount * (CASE WHEN initial.operation_type = 1 THEN 1 ELSE -1 END)) FROM '. $this->db->dbprefix('cash_daily').' AS initial WHERE initial.cash_book_id = cash_up.cash_book_id AND initial.deleted = 0 AND initial.cashup_id < cash_up.cashup_id),0) AS initial_balance 
		');
		$this->db->from('cash_up AS cash_up');
		$this->db->join('cash_books AS cash_books', 'cash_books.cash_book_id = cash_up.cash_book_id');
		$this->db->join('people AS open_employees', 'open_employees.person_id = cash_up.open_employee_id', 'LEFT');
		$this->db->join('people AS close_employees', 'close_employees.person_id = cash_up.close_employee_id', 'LEFT');
		$this->db->join('cash_daily AS cash_daily', 'cash_daily.cashup_id = cash_up.cashup_id AND cash_daily.deleted = 0', 'LEFT');
		$this->db->where('cash_up.cashup_id', $cashup_id);
		$this->db->group_by('cash_up.cashup_id');
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object
			$cash_up_obj = new stdClass();

			//Get all the fields from cashup table
			foreach($this->db->list_fields('cash_up') as $field)
			{
				$cash_up_obj->$field = '';
			}

			return $cash_up_obj;
		}
	}

	public function get_cashup_employee_daily($employee_id,$today)
	{

		$initial_balance = "(SELECT SUM(amount * (CASE WHEN cd.operation_type = 1 THEN 1 ELSE -1 END)) FROM ". $this->db->dbprefix('cash_daily')." AS cd 
				WHERE cd.cash_book_id = (SELECT MAX(cash_book_id) FROM ".$this->db->dbprefix('cash_books')." WHERE user_id = $employee_id AND deleted = 0) AND cd.deleted = 0 ";

		if(empty($this->config->item('date_or_time_format')))
		{
			$initial_balance .=' AND DATE_FORMAT(cd.movementdate, "%Y-%m-%d") < ' . $this->db->escape($today) . ')';
		}
		else
		{
			$initial_balance .=' AND cd.movementdate < ' . $this->db->escape(rawurldecode($today)) . ')';
		}

		$this->db->select('
			cash_up.cashup_id AS cashup_id,
			cash_up.cash_book_id AS cash_book_id,
			cash_up.open_date AS open_date,
			cash_up.close_date AS close_date,
			cash_up.open_amount_cash AS open_amount_cash,
			cash_up.transfer_amount_cash AS transfer_amount_cash,
			cash_up.closed_amount_cash AS closed_amount_cash,
			cash_up.closed_amount_due AS closed_amount_due,
			cash_up.closed_amount_card AS closed_amount_card,
			cash_up.closed_amount_check AS closed_amount_check,
			COALESCE((SELECT SUM(amount * (CASE WHEN cash_daily.operation_type = 1 THEN 1 ELSE -1 END)) FROM '. $this->db->dbprefix('cash_daily').' AS cash_daily 
				WHERE cash_daily.cashup_id = cash_up.cashup_id AND cash_daily.deleted = 0),cash_up.closed_amount_total,0)+COALESCE('.$initial_balance.',0) AS closed_amount_total,
			cash_up.description AS description,
			cash_up.note AS note,
			cash_up.open_employee_id AS open_employee_id,
			cash_up.close_employee_id AS close_employee_id,
			cash_up.deleted AS deleted,
			open_employees.first_name AS open_first_name,
			open_employees.last_name AS open_last_name,
			close_employees.first_name AS close_first_name,
			close_employees.last_name AS close_last_name,
			cash_books.code,
			stock_locations.location_name,
			COALESCE((SELECT SUM(amount) FROM '. $this->db->dbprefix('cash_daily').' AS cash_daily 
				WHERE cash_daily.cashup_id = cash_up.cashup_id AND cash_daily.deleted = 0 AND cash_daily.operation_type = 1),0) AS income,
			COALESCE((SELECT SUM(amount) FROM '. $this->db->dbprefix('cash_daily').' AS cash_daily 
				WHERE cash_daily.cashup_id = cash_up.cashup_id AND cash_daily.deleted = 0 AND cash_daily.operation_type = 2),0) AS cost,
			COALESCE((SELECT SUM(amount) FROM '. $this->db->dbprefix('cash_daily').' AS cash_daily 
				WHERE cash_daily.cashup_id = cash_up.cashup_id AND cash_daily.deleted = 0 AND cash_daily.operation_type = 3),0) AS expense,
		');
		$this->db->from('cash_up AS cash_up');
		$this->db->join('cash_books AS cash_books', 'cash_books.cash_book_id = cash_up.cash_book_id');
		$this->db->join('stock_locations AS stock_locations', 'cash_books.stock_location_id = stock_locations.location_id');
		$this->db->join('people AS open_employees', 'open_employees.person_id = cash_up.open_employee_id', 'LEFT');
		$this->db->join('people AS close_employees', 'close_employees.person_id = cash_up.close_employee_id', 'LEFT');
		$this->db->where('cash_up.deleted', 0);
		$this->db->where('cash_up.open_employee_id', $employee_id);
		if(empty($this->config->item('date_or_time_format')))
		{
			$this->db->where('DATE_FORMAT(cash_up.open_date, "%Y-%m-%d") BETWEEN ' . $this->db->escape($today) . ' AND ' . $this->db->escape($today));
		}
		else
		{
			$this->db->where('cash_up.open_date BETWEEN ' . $this->db->escape(rawurldecode($today)) . ' AND ' . $this->db->escape(rawurldecode($today)));
		}

		$query = $this->db->get();
		if($query->num_rows() >= 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object
			$cash_up_obj = new stdClass();

			//Get all the fields from cashup table
			foreach($this->db->list_fields('cash_up') as $field)
			{
				$cash_up_obj->$field = '';
			}

			return $cash_up_obj;
		}
	}

	/**
	 * Inserts or updates a cashup
	 *
	 * @param array $cashup_data array containing cashup information
	 *
	 * @param var $cashup_id identifier of the cashup to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function opened(&$cashup_data, &$cash_daily_data, $cashup_id = FALSE)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->set_log("ID: ".$cashup_id);

		$success = $this->db->insert('cash_up', $cashup_data);
		
		$this->set_log($this->db->last_query());
		
		$cashup_id = $this->db->insert_id();

		$cashup_data['cashup_id'] = $cashup_id;

		if($success)
		{

			foreach($cash_daily_data as $cash_daily)
			{
				$cash_daily['cashup_id'] = $cashup_id;
				$cash_daily['reference_id'] = $cashup_id;

				$success = $this->Cash_daily->save($cash_daily,-1);
				$this->set_log($this->db->last_query());
			}
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}

	/**
	 * Deletes one cashup
	 *
	 * @param integer $cashup_id cashup identificator
	 *
	 * @return boolean always TRUE
	 */
	public function closed(&$cashup_data,&$currency_data,$cashup_id)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->where('cashup_id', $cashup_id);

		if($this->db->update('cash_up', $cashup_data))
		{
			$success = TRUE;
		}

		$this->set_log($this->db->last_query());

		if($success)
		{
			//	Delete all
			$this->db->delete('cashup_currencys',array('cashup_id'=>$cashup_id));
			$this->set_log($this->db->last_query());
			foreach($currency_data as $currency)
			{
				$success = $this->db->insert('cashup_currencys',
					array('cashup_id'=>$cashup_id,
						'currency'=>$currency['currency'],
						'denomination'=>$currency['denomination'],
						'quantity'=>$currency['quantity'],
						'amount'=>$currency['amount']));
				$this->set_log($this->db->last_query());
			}
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();
		
		return $success;
	}

	/*
	Inserts or updates an cashup
	*/
	public function save(&$cash_up_data, $cashup_id = FALSE)
	{

		$this->set_log("ID To Send: ".$cashup_id);

		if(!$cashup_id == -1 || !$this->exists($cashup_id))
		{
			$this->set_log($this->db->last_query());
			if($this->db->insert('cash_up', $cash_up_data))
			{
				$this->set_log($this->db->last_query());
				$cash_up_data['cashup_id'] = $this->db->insert_id();

				return TRUE;
			}
			
			$this->Cashup->set_log($this->db->last_query());

			return FALSE;
		}

		$this->db->where('cashup_id', $cashup_id);

		return $this->db->update('cash_up', $cash_up_data);
	}

	/*
	Deletes a list of cashups
	*/
	public function delete_list($cashup_ids)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
			$this->db->where_in('cashup_id', $cashup_ids);
			$success = $this->db->update('cash_up', array('deleted'=>1));
		$this->db->trans_complete();

		return $success;
	}

	/**
	 * Gets information about a cashup as an array
	 *
	 * @param integer $cashup_id identifier of the cashup
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_denominations($cashup_id,$currency)
	{
		$query = $this->db->get_where('cashup_currencys', array('cashup_id' => $cashup_id,'currency' => $currency));

		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			//create object with empty properties.
			$cashup_obj = new stdClass;

			foreach($this->db->list_fields('cashup_currencys') as $field)
			{
				$cashup_obj->$field = '';
			}

			return $cashup_obj;
		}
	}
}
?>
