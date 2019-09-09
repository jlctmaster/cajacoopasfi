<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for CREDIT classes
 */

class Credit extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}

	/**
	 * Determines whether the given CREDIT exists in the credit database table
	 *
	 * @param integer $credit_id identifier of the CREDIT to verify the existence
	 *
	 * @return boolean TRUE if the CREDIT exists, FALSE if not
	 */
	public function exists($credit_id)
	{
		$this->db->from('credits');
		$this->db->where('credit_id', $credit_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all credit from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of credit table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('credits');
		$this->db->order_by('creditdate', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of credit database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('credits');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Credit as an array
	 *
	 * @param integer $credit_id identifier of the Credit
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($credit_id)
	{
		$this->db->select('credits.credit_id AS id,
				credits.credit_id,
				credits.person_id,
				people.dni,
				CONCAT(people.first_name,\' \',people.last_name) AS name,
				credits.creditdate,
				credits.returndate,
				credits.amount,
				credits.percent,
				(credits.percent * 30) AS percent_monthly,
				credits.amt_interest,
				credits.cuote,
				(credits.amount
					+ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_credits.capital) FROM ' . $this->db->dbprefix('payment_credits') . ' AS payment_credits WHERE credits.credit_id = payment_credits.credit_id),0) AS balance,
				COALESCE((SELECT SUM(payment_credits.amount) FROM ' . $this->db->dbprefix('payment_credits') . ' AS payment_credits 
						WHERE credits.credit_id = payment_credits.credit_id),0) AS pay_amount');

		$this->db->from('credits AS credits');
		$this->db->join('people AS people','credits.person_id = people.person_id');
		$this->db->where('credits.credit_id', $credit_id);

		$query = $this->db->get();

		//echo $this->db->last_query();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Credit_obj = new stdClass;

			foreach($this->db->list_fields('credits') as $field)
			{
				$credit_obj->$field = '';
			}

			foreach($this->db->list_fields('people') as $field)
			{
				$credit_obj->$field = '';
			}

			return $credit_obj;
		}
	}

	/**
	 * Gets information about a Credit as an array
	 *
	 * @param integer $credit_id identifier of the Credit
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_open_credit($person_id)
	{
		$this->db->select('credits.credit_id,
				(credits.amount
					+ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_credits.capital) FROM ' . $this->db->dbprefix('payment_credits') . ' AS payment_credits WHERE credits.credit_id = payment_credits.credit_id),0) AS balance,
				(credits.amount)-SUM(COALESCE(payment_credits.capital,0)) AS capital,
				(ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2)) AS interest');

		$this->db->from('credits AS credits');
		$this->db->join('payment_credits AS payment_credits','credits.credit_id = payment_credits.credit_id','LEFT');
		$this->db->where('credits.person_id', $person_id);

		$this->db->group_by(array('credits.credit_id','credits.amount','credits.amt_interest'));

		$this->db->order_by('credits.credit_id');

		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			//create object with empty properties.
			$credit_obj = new stdClass;

			$credit_obj->$credit_id = '';
			$credit_obj->$balance = '';

			return $credit_obj;
		}
	}

	/**
	 * Gets information about a Credit as an array
	 *
	 * @param integer $credit_id identifier of the Credit
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_summary_info($dni)
	{
		$this->db->select('credits.person_id,
				people.dni,
				CONCAT(people.first_name,\' \',people.last_name) AS name,
				SUM((credits.amount
					+ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_credits.capital) FROM ' . $this->db->dbprefix('payment_credits') . ' AS payment_credits WHERE credits.credit_id = payment_credits.credit_id),0)) AS balance');

		$this->db->from('credits AS credits');
		$this->db->join('people AS people','credits.person_id = people.person_id');
		$this->db->where('people.dni', $dni);

		$this->db->group_by(array('credits.person_id','people.dni','people.first_name','people.last_name',));

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Credit_obj = new stdClass;

			foreach($this->db->list_fields('credits') as $field)
			{
				$credit_obj->$field = '';
			}

			foreach($this->db->list_fields('people') as $field)
			{
				$credit_obj->$field = '';
			}

			return $credit_obj;
		}
	}

	/**
	 * Gets information about a Credit as an array
	 *
	 * @param integer $credit_id identifier of the Credit
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_detail_info($credit_id)
	{

		$this->db->select('credit_items.*,
			items.name,
			stock_locations.location_name');

		$this->db->from('credit_items AS credit_items');
		$this->db->join('items AS items','credit_items.item_id = items.item_id');
		$this->db->join('stock_locations AS stock_locations','credit_items.location_id = stock_locations.location_id');
		$this->db->where('credit_items.credit_id', $credit_id);

		$this->db->order_by('credit_items.credit_item_id');

		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			//create object with empty properties.
			$Credit_obj = new stdClass;

			foreach($this->db->list_fields('credit_items') as $field)
			{
				$credit_obj->$field = '';
			}
			$credit_obj->name = '';
			$credit_obj->location_name = '';

			return array($credit_obj);
		}
	}

	/**
	 * Gets information about credit as an array of rows
	 *
	 * @param array $credit_ids array of credit identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($credit_ids)
	{
		$this->db->from('credits');
		$this->db->where_in('credit_id', $credit_ids);
		$this->db->order_by('creditdate', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Credit
	 *
	 * @param array $credit_data array containing Credit information
	 *
	 * @param var $credit_id identifier of the Credit to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$credit_data, &$credit_item_data, $credit_id = FALSE)
	{
		$success = FALSE;

		$this->db->trans_start();

		$this->set_log("ID To Send: ".$credit_id);
		if(!$credit_id || !$this->exists($credit_id))
		{
			$this->set_log($this->db->last_query());
			$success = $this->db->insert('credits', $credit_data);
			$this->set_log($this->db->last_query());
			$credit_data['credit_id'] = $this->db->insert_id();
			$credit_id = $credit_data['credit_id'];
		}
		else
		{
			$this->db->where('credit_id', $credit_id);
			$success = $this->db->update('credits', $credit_data);
			$this->set_log($this->db->last_query());
		}

		//We have either inserted or updated a new user, now lets set permissions.
		if($success)
		{
			//First lets clear out any grants the user currently has.
			$success = $this->db->delete('credit_items', array('credit_id' => $credit_id));
			$this->set_log($this->db->last_query());

			//Now insert the new grants
			if($success)
			{
				$count = 0;
				foreach($credit_item_data as $item)
				{
					$success = $this->db->insert('credit_items', array('location_id' => $item['location_id'], 'item_id' => $item['item_id'], 'quantity' => $item['quantity'], 'price' => $item['price'], 'amount' => $item['amount'], 'credit_id' => $credit_id));
					$this->set_log($this->db->last_query());
					$count = $count+ 1;
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
		return $this->search($search, 0, 0, 'creditdate', 'asc', TRUE);
	}

	/*
	Perform a search on credit
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'creditdate', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(credits.credit_id) as count');
		}
		else
		{
			$this->db->select('credits.credit_id,
				people.dni,
				CONCAT(people.first_name,\' \',people.last_name) AS name,
				GROUP_CONCAT(items.name SEPARATOR \',\') AS detail,
				credits.creditdate,
				credits.returndate,
				credits.amount,
				credits.amt_interest,
				(credits.amount
					+ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_credits.capital) FROM ' . $this->db->dbprefix('payment_credits') . ' AS payment_credits WHERE credits.credit_id = payment_credits.credit_id),0) AS balance,
				COALESCE((SELECT SUM(payment_credits.amount) FROM ' . $this->db->dbprefix('payment_credits') . ' AS payment_credits 
						WHERE credits.credit_id = payment_credits.credit_id),0) AS pay_amount ');
		}

		$this->db->from('credits AS credits');
		$this->db->join('credit_items AS credit_items','credits.credit_id = credit_items.credit_id');
		$this->db->join('items AS items','items.item_id = credit_items.item_id');
		$this->db->join('people AS people','credits.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search);
			$this->db->or_like('dni', $search);
		$this->db->group_end();
		$this->db->where('credits.deleted', 0);

		// get_found_rows case
		if($count_only == TRUE)
		{
			return $this->db->get()->row()->count;
		}

		$this->db->group_by(array('credits.credit_id', 
			'people.dni',
			'people.first_name',
			'people.last_name',
			'credits.creditdate',
			'credits.returndate',
			'credits.amount',
			'credits.amt_interest'));

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
	Perform a search on credit
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
				MIN(credits.creditdate) AS creditdate,
				MAX(credits.returndate) AS returndate,
				SUM(credits.amount) AS amount,
				SUM(ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2)) AS amt_interest,
				SUM((credits.amount
					+ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM ' . $this->db->dbprefix('payment_credits') . ' pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2))
						-COALESCE((SELECT SUM(payment_credits.capital) FROM ' . $this->db->dbprefix('payment_credits') . ' AS payment_credits WHERE credits.credit_id = payment_credits.credit_id),0)) AS balance');
		}

		$this->db->from('credits AS credits');
		$this->db->join('people AS people','credits.person_id = people.person_id');
		$this->db->group_start();
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search);
			$this->db->or_like('dni', $search);
		$this->db->group_end();
		$this->db->where('credits.deleted', 0);

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
	public function get_payment_found_rows($id,$search)
	{
		return $this->payment_search($id,$type,$search, 0, 0, 'paydate', 'asc', TRUE);
	}

	/*
	Perform a search on credit
	*/
	public function payment_search($id,$type,$search, $rows = 0, $limit_from = 0, $sort = 'paydate', $order='asc', $count_only = FALSE)
	{
		if($type=="a")
		{
			$temp_table = $this->credit_amortization($id);

			// get_found_rows case
			if($count_only == TRUE)
			{
				$query = $this->db->query('SELECT COUNT(credit_amortization.credit_id) as count
					FROM '.$temp_table.' AS credit_amortization 
					WHERE credit_amortization.credit_id = '.$id);

				return $query->row()->count;
			}
			{
				$sql = 'SELECT credit_amortization.*
					FROM '.$temp_table.' AS credit_amortization 
					WHERE credit_amortization.credit_id = '.$id.' 
					ORDER BY credit_amortization.cuote ASC '.(($rows > 0) ? 'LIMIT '.$limit_from.','.$rows : '');
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
				$this->db->select('COUNT(payment_credit_id) as count');
			}
			else
			{
				$this->db->select('payment_credits.*,credits.amount AS amt_capital,credits.amt_interest,(credits.percent / 100) AS percent,credits.cuote ');
			}

			$this->db->from('payment_credits AS payment_credits');
			$this->db->join('credits AS credits','payment_credits.credit_id = credits.credit_id');
			$this->db->where('payment_credits.credit_id',$id);
			$this->db->group_start();
				$this->db->like('payment_credits.observations', $search);
			$this->db->group_end();
			$this->db->where('payment_credits.deleted', 0);

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

	public function credit_amortization($id)
	{
		$this->db->from('credits');
		$this->db->where('credit_id',$id);

		foreach ($this->db->get()->result() as $row)
		{
			$capital = $row->amount;
			$creditdate = $row->creditdate;
			$amt_interest = round(($row->amount * (($row->percent * 30) / 100)),2);
			for($x=1;$x<=$row->cuote;$x++)
			{
				$interest += $amt_interest;
				$balance = $capital + $interest;
				$sql .= 'SELECT '.$id.' AS credit_id,'.$x.' AS cuote, DATE_ADD(\''.$creditdate.'\',INTERVAL '.$x.' MONTH) AS estimate_paydate, \''.$this->lang->line('credits_credits_cuote').' '.$x.'\' AS observations, '.$capital.' AS capital, '.$interest.' AS amt_interest, '.$balance.' AS amount UNION ';
			}
			$sql = substr($sql,0,-6);
		}
		return '('.$sql.')';
	}

	/**
	 * Get search suggestions to find Credit
	 *
	 * @param string $search string containing the term to search in the credit table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('credit_id');
		$this->db->from('credits');
		$this->db->join('people','credits.person_id = people.person_id');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search);
			$this->db->or_like('dni', $search);
			$this->db->group_end();
		$this->db->order_by('creditdate', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->credit_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one credit
	 *
	 * @param integer $credit_id Credit identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($credit_id)
	{
		$this->db->where('credit_id', $credit_id);

		$result &= $this->db->update('credits', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of credit
	 *
	 * @param array $credit_ids list of Credit identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($credit_ids)
	{
		$success = FALSE;

		$this->db->trans_start();

		$this->db->where_in('credit_id', $credit_ids);
		$success = $this->db->update('credits', array('deleted' => 1));

		if($success)
		{
			$this->db->from('credit_items');
			$this->db->where_in('credit_id', $credit_ids);

			foreach($this->db->get()->result() as $row)
			{
				$item_quantity = $this->Item_quantity->get_item_quantity($row->item_id, $row->location_id);

				$updated_quantity = $item_quantity->quantity + parse_decimals($row->quantity);

				$location_detail = array('item_id' => $row->item_id,
										'location_id' => $row->location_id,
										'quantity' => $updated_quantity);
				
				$success &= $this->Item_quantity->save($location_detail, $row->item_id, $row->location_id);

				if($success)
				{
					$inv_data = array(
						'trans_date' => date('Y-m-d H:i:s'),
						'trans_items' => $row->item_id,
						'trans_user' => $this->User->get_logged_in_user_info()->person_id,
						'trans_location' => $row->location_id,
						'trans_comment' => $this->lang->line('loans_credits_items_restoring_of_quantity')." Doc: ".$row->credit_id,
						'trans_inventory' => parse_decimals($row->quantity)
					);

					$success &= $this->Inventory->insert($inv_data);
				}
			}
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
 	}

	/**
	 * Inserts or updates a Credit
	 *
	 * @param array $credit_data array containing Credit information
	 *
	 * @param var $credit_id identifier of the Credit to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save_payment(&$paycredit_data,$cash_daily_data)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		if($this->db->insert('payment_credits', $paycredit_data))
		{
			$paycredit_data['payment_credit_id'] = $this->db->insert_id();
			$this->set_log($this->db->last_query());
			$success = TRUE;
		}

		if($success)
		{
			foreach($cash_daily_data as $cash_daily)
			{
				$cash_daily['reference_id'] = $paycredit_data['payment_credit_id'];

				$success = $this->Cash_daily->save($cash_daily,-1,TRUE);
				$this->set_log($this->db->last_query());
			}
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}

	/*
	Gets the payment summary for the expenses (expenses/manage) view
	*/
	public function get_credits_summary($search, $filters)
	{
		// get payment summary
		$this->db->select('SUM(credits.amount) AS amount,SUM(COALESCE(payment_credits.amount,0)) AS payamount');
		$this->db->from('credits AS credits');
		$this->db->from('payment_credits AS payment_credits','payment_credits.credit_id = credits.credits_id','LEFT');
		$this->db->where('credits.deleted', $filters['is_deleted']);

		if(empty($this->config->item('date_or_time_format')))
		{
			$this->db->where('DATE_FORMAT(credits.creditdate, "%Y-%m-%d") BETWEEN ' . $this->db->escape($filters['start_date']) . ' AND ' . $this->db->escape($filters['end_date']));
		}
		else
		{
			$this->db->where('credits.creditdate BETWEEN ' . $this->db->escape(rawurldecode($filters['start_date'])) . ' AND ' . $this->db->escape(rawurldecode($filters['end_date'])));
		}

		$credits = $this->db->get()->result_array();

		return $credits;
	}

	/**
	 * Gets information about a Credit as an array
	 *
	 * @param integer $credit_id identifier of the Credit
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_balance($id,$paytype)
	{
		if($paytype == 0)
		{
			$select = "MAX((credits.amount
						+ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2))
							-COALESCE((SELECT SUM(payment_credits.capital) FROM " . $this->db->dbprefix('payment_credits') . " AS payment_credits WHERE credits.credit_id = payment_credits.credit_id),0)) AS balance,
					MAX(credits.amount)-SUM(COALESCE(payment_credits.capital,0)) AS capital,
					MAX(ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2)) AS interest,
					MAX(COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),credits.amt_interest)) AS cumulate_interest ";
		}
		else if($paytype == 1)
		{
			$select = "MAX(credits.amount)-SUM(COALESCE(CASE WHEN payment_credits.paytype IN (0,1) THEN payment_credits.capital ELSE 0 END,0)) AS balance,
					MAX(credits.amount)-SUM(COALESCE(payment_credits.capital,0)) AS capital,
					MAX(ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2)) AS interest,
					MAX(COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),credits.amt_interest)) AS cumulate_interest  ";
		}
		else{
			$select = "MAX(ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2)) AS balance,
					MAX(credits.amount)-SUM(COALESCE(payment_credits.capital,0)) AS capital,
					MAX(ROUND((((credits.amount-COALESCE((SELECT SUM(capital) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),0)) * (credits.percent / 100)) * DATEDIFF(CASE WHEN (SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id) IS NULL AND CURDATE() = credits.creditdate THEN DATE_ADD(CURDATE(),INTERVAL 1 DAY) ELSE CURDATE() END,COALESCE((SELECT MAX(paydate) FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id),CURDATE()))) + COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),0),2)) AS interest,
					MAX(COALESCE((SELECT cumulate_interest FROM " . $this->db->dbprefix('payment_credits') . " pl WHERE pl.credit_id = credits.credit_id ORDER BY payment_credit_id DESC LIMIT 1),credits.amt_interest)) AS cumulate_interest  ";
		}

		$sql = "SELECT 
			$select
		FROM ". $this->db->dbprefix('credits') . " AS credits 
		LEFT JOIN ". $this->db->dbprefix('payment_credits') . " AS payment_credits ON credits.credit_id = payment_credits.credit_id 
		WHERE credits.credit_id = $id";

		$query = $this->db->query($sql);

		//echo $this->db->last_query();

		if($query->num_rows() >= 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$credit_obj = new stdClass;
			$credit_obj->$balance = '';

			return $credit_obj;
		}
	}
}
?>
