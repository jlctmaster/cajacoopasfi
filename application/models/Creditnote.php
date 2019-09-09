<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for creditnote classes
 */

class Creditnote extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}


	/**
	 * Determines whether the given creditnote exists in the creditnote database table
	 *
	 * @param integer $creditnote_id identifier of the creditnote to verify the existence
	 *
	 * @return boolean TRUE if the creditnote exists, FALSE if not
	 */
	public function exists($creditnote_id)
	{
		$this->db->from('creditnotes');
		$this->db->where('creditnote_id', $creditnote_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all creditnote from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of creditnote table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('creditnotes');
		$this->db->order_by('documentno', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of creditnote database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('creditnotes');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a creditnote as an array
	 *
	 * @param integer $creditnote_id identifier of the creditnote
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($creditnote_id,$cash_book_id)
	{
		$this->db->select("
				creditnotes.*,
				people.dni,
				CONCAT(people.first_name,' ',people.last_name) AS name,
				CONCAT(user.first_name,' ',user.last_name,' (',cash_books.code,')') AS cash_book,
				(CASE WHEN creditnotes.movementtype = 'C' THEN creditnotes.amount ELSE 0 END) cash,
				(CASE WHEN creditnotes.movementtype = 'B' THEN creditnotes.amount ELSE 0 END) bank,
				(CASE WHEN creditnotes.cash_book_id = $cash_book_id AND DATE_FORMAT(creditnotes.documentdate,'%Y-%m-%d') = DATE_FORMAT(CURDATE(),'%Y-%m-%d') THEN 0 ELSE 1 END) readonly
			");
		$this->db->from('creditnotes AS creditnotes');
		$this->db->join('people AS people', 'people.person_id = creditnotes.person_id');
		$this->db->join('cash_books AS cash_books', 'cash_books.cash_book_id = creditnotes.cash_book_id');
		$this->db->join('people AS user', 'user.person_id = cash_books.user_id');
		$this->db->where('creditnote_id', $creditnote_id);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$creditnote_obj = new stdClass;

			foreach($this->db->list_fields('creditnotes') as $field)
			{
				$creditnote_obj->$field = '';
			}

			return $creditnote_obj;
		}
	}

	/**
	 * Gets information about creditnote as an array of rows
	 *
	 * @param array $creditnote_ids array of creditnote identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($creditnote_ids)
	{
		$this->db->from('creditnotes');
		$this->db->where_in('creditnote_id', $creditnote_ids);
		$this->db->order_by('documentno', 'asc');

		return $this->db->get();
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search, $cash_book_id)
	{
		return $this->search($search, $cash_book_id, 0, 0, 'documentno', 'asc', TRUE);
	}

	/*
	Perform a search on creditnote
	*/
	public function search($search, $cash_book_id, $rows = 0, $limit_from = 0, $sort = 'documentno', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(creditnotes.creditnote_id) as count');
		}
		else
		{
			$this->db->select("
				creditnotes.*,
				people.dni,
				CONCAT(people.first_name,' ',people.last_name) AS name,
				CONCAT(user.first_name,' ',user.last_name,' (',cash_books.code,')') AS cash_book,
				(CASE WHEN creditnotes.movementtype = 'C' THEN creditnotes.amount ELSE 0 END) cash,
				(CASE WHEN creditnotes.movementtype = 'B' THEN creditnotes.amount ELSE 0 END) bank,
				(CASE WHEN creditnotes.cash_book_id = $cash_book_id AND DATE_FORMAT(creditnotes.documentdate,'%Y-%m-%d') = DATE_FORMAT(CURDATE(),'%Y-%m-%d') THEN 0 ELSE 1 END) readonly
			");
		}

		$this->db->from('creditnotes AS creditnotes');
		$this->db->join('people AS people', 'people.person_id = creditnotes.person_id');
		$this->db->join('cash_books AS cash_books', 'cash_books.cash_book_id = creditnotes.cash_book_id');
		$this->db->join('people AS user', 'user.person_id = cash_books.user_id');
		$this->db->group_start();
			$this->db->like('creditnotes.documentno', $search);
			$this->db->or_like('creditnotes.description', $search);
			$this->db->or_like('CONCAT(people.first_name,\' \',people.last_name)', $search);
			$this->db->or_like('CONCAT(user.first_name,\' \',user.last_name,\' (\',cash_books.code,\')\')', $search);
		$this->db->group_end();
		$this->db->where('creditnotes.deleted', 0);

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
	 * Get search suggestions to find creditnote
	 *
	 * @param string $search string containing the term to search in the creditnote table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('creditnote_id');
		$this->db->from('creditnotes');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('documentno', $search);
			$this->db->or_like('description', $search);
			$this->db->group_end();
		$this->db->order_by('documentno', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->creditnote_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Inserts or updates a creditnote
	 *
	 * @param array $creditnote_data array containing creditnote information
	 *
	 * @param var $creditnote_id identifier of the creditnote to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$creditnote_data, &$cash_daily_data, $creditnote_id = FALSE)
	{
		$success = FALSE;

		$this->db->trans_start();

		$this->set_log("ID: ".$creditnote_id);

		if(!$this->exists($creditnote_id))
		{
			$this->set_log($this->db->last_query());
			$success = $this->db->insert('creditnotes', $creditnote_data);
			$this->set_log($this->db->last_query());
			$creditnote_data['creditnote_id'] = $this->db->insert_id();
			$creditnote_id = $creditnote_data['creditnote_id'];
			if($success)
			{
				$this->set_log("Instruccion ejecutada con exito!");
			}
			else
			{
				$this->set_log("Error al ejecutar la instruccion!");
			}

		}
		else
		{
			$this->db->where('creditnote_id', $creditnote_id);
			$success = $this->db->update('creditnotes', $creditnote_data);
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

		if($success)
		{
			foreach($cash_daily_data as $cash_daily)
			{
				$cash_daily['reference_id'] = $creditnote_id;

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

	/**
	 * Deletes one creditnote
	 *
	 * @param integer $creditnote_id creditnote identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($creditnote_id,$currency)
	{
		$success = FALSE;

		$this->db->trans_start();
		$this->db->where('creditnote_id', $creditnote_id);

		if($this->db->update('creditnotes', array('deleted' => 1)))
		{
			$success = TRUE;
		}

		$this->set_log($this->db->last_query());

		if($success)
		{
			$this->db->where('table_reference', 'creditnotes');
			$this->db->where('reference_id', $creditnote_id);
			$this->db->where('currency', $currency);

			if($this->db->update('cash_daily', array('deleted' => 1)))
			{
				$success = TRUE;
			}
			$this->set_log($this->db->last_query());
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}

	/**
	 * Deletes a list of creditnote
	 *
	 * @param array $creditnote_ids list of creditnote identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($creditnote_ids)
	{
		$this->db->where_in('creditnote_id', $creditnote_ids);

		return $this->db->update('creditnotes', array('deleted' => 1));
 	}
}
?>
