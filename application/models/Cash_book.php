<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for cash_book classes
 */

class Cash_book extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}


	/**
	 * Determines whether the given cash_book exists in the cash_book database table
	 *
	 * @param integer $cash_book_id identifier of the cash_book to verify the existence
	 *
	 * @return boolean TRUE if the cash_book exists, FALSE if not
	 */
	public function exists($cash_book_id)
	{
		$this->db->from('cash_books');
		$this->db->where('cash_book_id', $cash_book_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Determines whether the given cash_book exists in the cash_book database table
	 *
	 * @param integer $cash_book_id identifier of the cash_book to verify the existence
	 *
	 * @return boolean TRUE if the cash_book exists, FALSE if not
	 */
	public function have_movements($cash_book_id)
	{
		$query = $this->db->query('SELECT * FROM '.$this->db->dbprefix('cash_books').' AS cash_books 
			WHERE cash_books.cash_book_id = '.$cash_book_id.' 
			AND (EXISTS(SELECT 1 FROM '.$this->db->dbprefix('cash_flow').' AS cash_flow WHERE cash_books.cash_book_id = cash_flow.cash_book_id) 
				OR EXISTS(SELECT 1 FROM '.$this->db->dbprefix('cash_up').' AS cash_up WHERE cash_books.cash_book_id = cash_up.cash_book_id))');

		return ($query->num_rows() == 1);
	}

	/**
	 * Determines whether the given cash_book exists in the cash_book database table
	 *
	 * @param integer $cash_book_id identifier of the cash_book to verify the existence
	 *
	 * @return boolean TRUE if the cash_book exists, FALSE if not
	 */
	public function exists_cash_general($cash_book_id)
	{
		$this->db->from('cash_books');
		$this->db->where('deleted', 0);
		$this->db->where('is_cash_general', 1);
		if($cash_book_id != -1){
			$this->db->where('cash_book_id <> ', $cash_book_id);
		}

		return ($this->db->get()->result());
	}

	/**
	 * Gets all cash_book from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of cash_book table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('cash_books');
		$this->db->order_by('code', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of cash_book database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('cash_books');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a cash_book as an array
	 *
	 * @param integer $cash_book_id identifier of the cash_book
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($cash_book_id)
	{
		$this->db->select("
				cash_books.*,
				stock_locations.location_name,
				CONCAT(people.first_name,' ',people.last_name,' (',users.username,')') AS username 
			");
		$this->db->from('cash_books AS cash_books');
		$this->db->join('stock_locations AS stock_locations', 'cash_books.stock_location_id = stock_locations.location_id');
		$this->db->join('users AS users', 'cash_books.user_id = users.person_id');
		$this->db->join('people AS people', 'people.person_id = users.person_id');
		$this->db->where('cash_books.cash_book_id',$cash_book_id);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$cash_book_obj = new stdClass;

			foreach($this->db->list_fields('cash_books') as $field)
			{
				$cash_book_obj->$field = '';
			}

			return $cash_book_obj;
		}
	}

	/**
	 * Gets information about a cash_book as an array
	 *
	 * @param integer $cash_book_id identifier of the cash_book
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info_overall_cash()
	{
		$this->db->select("
				cash_books.*,
				stock_locations.location_name,
				CONCAT(people.first_name,' ',people.last_name) AS username 
			");
		$this->db->from('cash_books AS cash_books');
		$this->db->join('stock_locations AS stock_locations', 'cash_books.stock_location_id = stock_locations.location_id');
		$this->db->join('users AS users', 'cash_books.user_id = users.person_id');
		$this->db->join('people AS people', 'people.person_id = users.person_id');
		$this->db->where('is_cash_general',1);

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$cash_book_obj = new stdClass;

			foreach($this->db->list_fields('cash_books') as $field)
			{
				$cash_book_obj->$field = '';
			}

			return $cash_book_obj;
		}
	}

	/**
	 * Gets information about a cash_book as an array
	 *
	 * @param integer $cash_book_id identifier of the cash_book
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info_by_user($user_id)
	{
		$query = $this->db->get_where('cash_books', array('user_id' => $user_id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$cash_book_obj = new stdClass;

			foreach($this->db->list_fields('cash_books') as $field)
			{
				$cash_book_obj->$field = '';
			}

			return $cash_book_obj;
		}
	}

	/**
	 * Gets information about cash_book as an array of rows
	 *
	 * @param array $cash_book_ids array of cash_book identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($cash_book_ids)
	{
		$this->db->from('cash_books');
		$this->db->where_in('cash_book_id', $cash_book_ids);
		$this->db->order_by('code', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a cash_book
	 *
	 * @param array $cash_book_data array containing cash_book information
	 *
	 * @param var $cash_book_id identifier of the cash_book to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$cash_book_data, $cash_book_id = FALSE)
	{
		$this->set_log("ID: ".$cash_book_id);

		if(!$cash_book_id || !$this->exists($cash_book_id))
		{
			$this->set_log($this->db->last_query());
			if($this->db->insert('cash_books', $cash_book_data))
			{
				$this->set_log($this->db->last_query());
				$cash_book_data['cash_book_id'] = $this->db->insert_id();

				return TRUE;
			}
			$this->set_log($this->db->last_query());

			return FALSE;
		}else{
			$this->set_log($this->db->last_query());	
		}

		$this->db->where('cash_book_id', $cash_book_id);

		return $this->db->update('cash_books', $cash_book_data);
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'code', 'asc', TRUE);
	}

	/*
	Perform a search on cash_book
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'code', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(cash_books.cash_book_id) as count');
		}
		else
		{
			$this->db->select("
				cash_books.*,
				stock_locations.location_name,
				CONCAT(people.first_name,' ',people.last_name,' (',users.username,')') AS username 
			");
		}

		$this->db->from('cash_books AS cash_books');
		$this->db->join('stock_locations AS stock_locations', 'cash_books.stock_location_id = stock_locations.location_id');
		$this->db->join('users AS users', 'cash_books.user_id = users.person_id');
		$this->db->join('people AS people', 'people.person_id = users.person_id');
		$this->db->group_start();
			$this->db->like('cash_books.address', $search);
			$this->db->or_like('cash_books.code', $search);
			$this->db->or_like('stock_locations.location_name', $search);
			$this->db->or_like('users.username', $search);
			$this->db->or_like('people.first_name', $search);
			$this->db->or_like('people.last_name', $search);
		$this->db->group_end();
		$this->db->where('cash_books.deleted', 0);

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
	 * Get search suggestions to find cash_book
	 *
	 * @param string $search string containing the term to search in the cash_book table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('cash_book_id');
		$this->db->from('cash_books');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('address', $search);
			$this->db->or_like('code', $search);
			$this->db->group_end();
		$this->db->order_by('code', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->cash_book_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one cash_book
	 *
	 * @param integer $cash_book_id cash_book identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($cash_book_id)
	{
		$result = FALSE;

		$this->db->where('cash_book_id', $cash_book_id);

		if($this->db->update('cash_books', array('deleted' => 1)))
		{
			$result = TRUE;
		}
		
		return $result;
	}

	/**
	 * Deletes a list of cash_book
	 *
	 * @param array $cash_book_ids list of cash_book identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($cash_book_ids)
	{
		$this->db->where_in('cash_book_id', $cash_book_ids);

		return $this->db->update('cash_books', array('deleted' => 1));
 	}
}
?>
