<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for voucher classes
 */

class Voucher extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}


	/**
	 * Determines whether the given voucher exists in the voucher database table
	 *
	 * @param integer $voucher_id identifier of the voucher to verify the existence
	 *
	 * @return boolean TRUE if the voucher exists, FALSE if not
	 */
	public function exists($voucher_id)
	{
		$this->db->from('vouchers');
		$this->db->where('voucher_id', $voucher_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all voucher from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of voucher table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('vouchers');
		$this->db->order_by('voucher_number', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of voucher database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('vouchers');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a voucher as an array
	 *
	 * @param integer $voucher_id identifier of the voucher
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($voucher_id,$cash_book_id)
	{
		$this->db->select("
				vouchers.*,
				people.dni,
				CONCAT(people.first_name,' ',people.last_name) AS name,
				vouchers.amount-COALESCE((SELECT SUM(amount) FROM ". $this->db->dbprefix('payment_vouchers') ." AS payment_vouchers WHERE vouchers.voucher_id = payment_vouchers.voucher_id),0) AS balance,
				COALESCE((SELECT SUM(amount) FROM ". $this->db->dbprefix('payment_vouchers') ." AS payment_vouchers WHERE vouchers.voucher_id = payment_vouchers.voucher_id),0) AS rendered 
			");
		$this->db->from('vouchers AS vouchers');
		$this->db->join('people AS people', 'people.person_id = vouchers.person_id');
		$this->db->where('voucher_id', $voucher_id);
		$this->db->where('cash_book_id', $cash_book_id);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$voucher_obj = new stdClass;

			foreach($this->db->list_fields('vouchers') as $field)
			{
				$voucher_obj->$field = '';
			}

			return $voucher_obj;
		}
	}

	/**
	 * Gets information about voucher as an array of rows
	 *
	 * @param array $voucher_ids array of voucher identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($voucher_ids)
	{
		$this->db->from('vouchers');
		$this->db->where_in('voucher_id', $voucher_ids);
		$this->db->order_by('voucher_number', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a voucher
	 *
	 * @param array $voucher_data array containing voucher information
	 *
	 * @param var $voucher_id identifier of the voucher to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$voucher_data, $voucher_id = FALSE)
	{
		$this->set_log("ID: ".$voucher_id);

		if(!$voucher_id || !$this->exists($voucher_id))
		{
			$this->set_log($this->db->last_query());
			if($this->db->insert('vouchers', $voucher_data))
			{
				$this->set_log($this->db->last_query());
				$voucher_data['voucher_id'] = $this->db->insert_id();

				return TRUE;
			}
			$this->set_log($this->db->last_query());

			return FALSE;
		}else{
			$this->set_log($this->db->last_query());	
		}

		$this->db->where('voucher_id', $voucher_id);

		return $this->db->update('vouchers', $voucher_data);
	}

	/**
	 * Inserts or updates a voucher
	 *
	 * @param array $voucher_data array containing voucher information
	 *
	 * @param var $voucher_id identifier of the voucher to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save_payment(&$payvoucher_data)
	{

		if($this->db->insert('payment_vouchers', $payvoucher_data))
		{
			$this->set_log($this->db->last_query());
			$payvoucher_data['payment_voucher_id'] = $this->db->insert_id();
			return TRUE;

		}else{
			$this->set_log($this->db->last_query());
			return FALSE;
		}
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search,$cash_book_id)
	{
		return $this->search($search, $cash_book_id, 0, 0, 'voucher_number', 'asc', TRUE);
	}

	/*
	Perform a search on voucher
	*/
	public function search($search, $cash_book_id, $rows = 0, $limit_from = 0, $sort = 'voucher_number', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(vouchers.voucher_id) as count');
		}
		else
		{
			$this->db->select("
				vouchers.*,
				people.dni,
				CONCAT(people.first_name,' ',people.last_name) AS name,
				COALESCE((SELECT SUM(amount) FROM ". $this->db->dbprefix('payment_vouchers') ." AS payment_vouchers WHERE vouchers.voucher_id = payment_vouchers.voucher_id),0) AS rendered 
			");
		}

		$this->db->from('vouchers AS vouchers');
		$this->db->join('people AS people', 'people.person_id = vouchers.person_id');
		$this->db->where('cash_book_id', $cash_book_id);
		$this->db->group_start();
			$this->db->like('vouchers.voucher_number', $search);
			$this->db->or_like('vouchers.detail', $search);
			$this->db->or_like('people.dni', $search);
			$this->db->or_like('people.first_name', $search);
			$this->db->or_like('people.last_name', $search);
		$this->db->group_end();
		$this->db->where('vouchers.deleted', 0);

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
	 * Get search suggestions to find voucher
	 *
	 * @param string $search string containing the term to search in the voucher table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('voucher_id');
		$this->db->from('vouchers');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('voucher_number', $search);
			$this->db->or_like('detail', $search);
			$this->db->group_end();
		$this->db->order_by('voucher_number', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->voucher_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one voucher
	 *
	 * @param integer $voucher_id voucher identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($voucher_id)
	{
		$this->db->where('voucher_id', $voucher_id);

		$result &= $this->db->update('vouchers', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of voucher
	 *
	 * @param array $voucher_ids list of voucher identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($voucher_ids)
	{
		$this->db->where_in('voucher_id', $voucher_ids);

		return $this->db->update('vouchers', array('deleted' => 1));
 	}

	/*
	Gets rows
	*/
	public function get_found_payment_rows($voucher_id,$search)
	{
		return $this->search_payment($voucher_id,$search, 0, 0, 'payment_voucher_id', 'asc', TRUE);
	}

	/*
	Perform a search on voucher
	*/
	public function search_payment($voucher_id, $search, $rows = 0, $limit_from = 0, $sort = 'payment_voucher_id', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(payment_vouchers.payment_voucher_id) as count');
		}

		$this->db->from('payment_vouchers AS payment_vouchers');
		$this->db->group_start();
			$this->db->like('payment_vouchers.observations', $search);
		$this->db->group_end();
		$this->db->where('payment_vouchers.deleted', 0);

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
?>
