<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for BANK classes
 */

class Bank extends CI_Model
{
	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}

	/**
	 * Determines whether the given BANK exists in the bank database table
	 *
	 * @param integer $bank_id identifier of the BANK to verify the existence
	 *
	 * @return boolean TRUE if the BANK exists, FALSE if not
	 */
	public function exists($bank_id)
	{
		$this->db->from('banks');
		$this->db->where('bank_id', $bank_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Determines whether the given BANK exists in the bank database table
	 *
	 * @param integer $bank_id identifier of the BANK to verify the existence
	 *
	 * @return boolean TRUE if the BANK exists, FALSE if not
	 */
	public function exists_account($bank_id,$account_number)
	{
		$this->db->from('bankaccounts');
		$this->db->where('bank_id', $bank_id);
		$this->db->where('account_number', $account_number);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all bank from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of bank table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('banks');
		$this->db->order_by('name', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets all bank from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of bank table rows
	 */
	public function get_all_bankaccounts($currency = "all",$limit = 10000, $offset = 0)
	{
		$this->db->select('banks.*,
			bankaccounts.bankaccount_id,
			bankaccounts.account_number,
			bankaccounts.currency');
		$this->db->from('banks AS banks');

		if($currency != "all")
		{
			$this->db->where('bankaccounts.currency',$currency);
		}

		$this->db->join('bankaccounts AS bankaccounts','bankaccounts.bank_id = banks.bank_id');
		$this->db->order_by('bankaccounts.bankaccount_id', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of bank database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('banks');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Bank as an array
	 *
	 * @param integer $bank_id identifier of the Bank
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($bank_id)
	{
		$query = $this->db->get_where('banks', array('bank_id' => $bank_id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Bank_obj = new stdClass;

			foreach($this->db->list_fields('banks') as $field)
			{
				$bank_obj->$field = '';
			}

			return $bank_obj;
		}
	}

	/**
	 * Gets information about a Bank as an array
	 *
	 * @param integer $bank_id identifier of the Bank
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_accountinfo($bank_id)
	{
		$query = $this->db->get_where('bankaccounts', array('bank_id' => $bank_id));

		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			//create object with empty properties.
			$Bank_obj = new stdClass;

			foreach($this->db->list_fields('bankaccounts') as $field)
			{
				$bank_obj->$field = '';
			}

			return array($bank_obj);
		}
	}

	/**
	 * Gets information about bank as an array of rows
	 *
	 * @param array $bank_ids array of bank identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($bank_ids)
	{
		$this->db->from('banks');
		$this->db->where_in('bank_id', $bank_ids);
		$this->db->order_by('name', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Bank
	 *
	 * @param array $bank_data array containing Bank information
	 *
	 * @param var $bank_id identifier of the Bank to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$bank_data, &$bankaccount_data, $bank_id = FALSE)
	{
		$success = FALSE;

		$this->db->trans_start();

		if(!$bank_id || !$this->exists($bank_id))
		{
			$this->set_log($this->db->last_query());
			$success = $this->db->insert('banks', $bank_data);
			$this->set_log($this->db->last_query());
			$bank_data['bank_id'] = $this->db->insert_id();
			$bank_id = $bank_data['bank_id'];
		}
		else
		{
			$this->db->where('bank_id', $bank_id);
			$success = $this->db->update('banks', $bank_data);
			$this->set_log($this->db->last_query());
		}

		//We have either inserted or updated a new user, now lets set permissions.
		if($success)
		{
			//First lets clear out any grants the user currently has.
			
			//Now insert the new grants
			if($success)
			{
				$count = 0;
				foreach($bankaccount_data as $account)
				{
					if(!$this->exists_account($bank_id,$account['account_number']))
					{
						$success = $this->db->insert('bankaccounts', array('currency' => $account['currency'], 'account_number' => $account['account_number'], 'bank_id' => $bank_id));
						$this->set_log($this->db->last_query());
					}
					else
					{
						$this->db->where('bank_id', $bank_id);
						$this->db->where('account_number', $account['account_number']);
						$success = $this->db->update('bankaccounts', array('currency' => $account['currency'], 'account_number' => $account['account_number'], 'bank_id' => $bank_id));
					}
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
		return $this->search($search, 0, 0, 'name', 'asc', TRUE);
	}

	/*
	Perform a search on bank
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'name', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(banks.bank_id) as count');
		}
		else
		{
			$this->db->select('banks.bank_id,banks.ruc,banks.name,
			GROUP_CONCAT((CASE WHEN bankaccounts.currency = \'PEN\' THEN \'Soles\' ELSE \'Dólar\' END) SEPARATOR \'<br>\') AS currency,
			GROUP_CONCAT(bankaccounts.account_number SEPARATOR \'<br>\') AS account_number');	
		}

		$this->db->from('banks AS banks');
		$this->db->join('bankaccounts AS bankaccounts','banks.bank_id = bankaccounts.bank_id','LEFT');
		$this->db->group_start();
			$this->db->like('banks.name', $search);
			$this->db->or_like('banks.ruc', $search);
		$this->db->group_end();
		$this->db->where('banks.deleted', 0);

		// get_found_rows case
		if($count_only == TRUE)
		{
			return $this->db->get()->row()->count;
		}
		else
		{
			$this->db->group_by('banks.bank_id');
			$this->db->group_by('banks.ruc');
			$this->db->group_by('banks.name');
		}

		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();
	}

	/**
	 * Get search suggestions to find Bank
	 *
	 * @param string $search string containing the term to search in the bank table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('bank_id');
		$this->db->from('banks');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('ruc', $search);
			$this->db->group_end();
		$this->db->order_by('name', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->bank_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one bank
	 *
	 * @param integer $bank_id Bank identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($bank_id)
	{
		$this->db->where('bank_id', $bank_id);

		$result &= $this->db->update('banks', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of bank
	 *
	 * @param array $bank_ids list of Bank identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($bank_ids)
	{
		$this->db->where_in('bank_id', $bank_ids);

		return $this->db->update('banks', array('deleted' => 1));
 	}
}
?>
