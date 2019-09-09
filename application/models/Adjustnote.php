<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for adjustnote classes
 */

class Adjustnote extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}


	/**
	 * Determines whether the given adjustnote exists in the adjustnote database table
	 *
	 * @param integer $adjustnote_id identifier of the adjustnote to verify the existence
	 *
	 * @return boolean TRUE if the adjustnote exists, FALSE if not
	 */
	public function exists($adjustnote_id)
	{
		$this->db->from('adjustnotes');
		$this->db->where('adjustnote_id', $adjustnote_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all adjustnote from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of adjustnote table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('adjustnotes');
		$this->db->order_by('documentno', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of adjustnote database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('adjustnotes');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a adjustnote as an array
	 *
	 * @param integer $adjustnote_id identifier of the adjustnote
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($adjustnote_id,$cash_book_id)
	{
		$this->db->select("
				adjustnotes.*,
				people.dni,
				CONCAT(people.first_name,' ',people.last_name) AS name,
				CONCAT(user.first_name,' ',user.last_name,' (',cash_books.code,')') AS cash_book,
				cash_concepts.name AS cash_concept,
				(CASE WHEN adjustnotes.movementtype = 'C' THEN adjustnotes.amount ELSE 0 END) cash,
				(CASE WHEN adjustnotes.movementtype = 'B' THEN adjustnotes.amount ELSE 0 END) bank,
				(CASE WHEN adjustnotes.cash_book_id = $cash_book_id AND DATE_FORMAT(adjustnotes.documentdate,'%Y-%m-%d') = DATE_FORMAT(CURDATE(),'%Y-%m-%d') THEN 0 ELSE 1 END) readonly
			");
		$this->db->from('adjustnotes AS adjustnotes');
		$this->db->join('people AS people', 'people.person_id = adjustnotes.person_id');
		$this->db->join('cash_books AS cash_books', 'cash_books.cash_book_id = adjustnotes.cash_book_id');
		$this->db->join('cash_concepts AS cash_concepts', 'cash_concepts.cash_concept_id = adjustnotes.cash_concept_id');
		$this->db->join('people AS user', 'user.person_id = cash_books.user_id');
		$this->db->where('adjustnote_id', $adjustnote_id);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$adjustnote_obj = new stdClass;

			foreach($this->db->list_fields('adjustnotes') as $field)
			{
				$adjustnote_obj->$field = '';
			}

			return $adjustnote_obj;
		}
	}

	/**
	 * Gets information about adjustnote as an array of rows
	 *
	 * @param array $adjustnote_ids array of adjustnote identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($adjustnote_ids)
	{
		$this->db->from('adjustnotes');
		$this->db->where_in('adjustnote_id', $adjustnote_ids);
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
	Perform a search on adjustnote
	*/
	public function search($search, $cash_book_id, $rows = 0, $limit_from = 0, $sort = 'documentno', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(adjustnotes.adjustnote_id) as count');
		}
		else
		{
			$this->db->select("
				adjustnotes.*,
				people.dni,
				CONCAT(people.first_name,' ',people.last_name) AS name,
				CONCAT(user.first_name,' ',user.last_name,' (',cash_books.code,')') AS cash_book,
				cash_concepts.name AS cash_concept,
				(CASE WHEN adjustnotes.movementtype = 'C' THEN adjustnotes.amount ELSE 0 END) cash,
				(CASE WHEN adjustnotes.movementtype = 'B' THEN adjustnotes.amount ELSE 0 END) bank,
				(CASE WHEN adjustnotes.cash_book_id = $cash_book_id AND DATE_FORMAT(adjustnotes.documentdate,'%Y-%m-%d') = DATE_FORMAT(CURDATE(),'%Y-%m-%d') THEN 0 ELSE 1 END) readonly
			");
		}

		$this->db->from('adjustnotes AS adjustnotes');
		$this->db->join('people AS people', 'people.person_id = adjustnotes.person_id');
		$this->db->join('cash_books AS cash_books', 'cash_books.cash_book_id = adjustnotes.cash_book_id');
		$this->db->join('cash_concepts AS cash_concepts', 'cash_concepts.cash_concept_id = adjustnotes.cash_concept_id');
		$this->db->join('people AS user', 'user.person_id = cash_books.user_id');
		$this->db->group_start();
			$this->db->like('adjustnotes.documentno', $search);
			$this->db->or_like('adjustnotes.description', $search);
			$this->db->or_like('cash_concepts.name', $search);
			$this->db->or_like('CONCAT(people.first_name,\' \',people.last_name)', $search);
			$this->db->or_like('CONCAT(user.first_name,\' \',user.last_name,\' (\',cash_books.code,\')\')', $search);
		$this->db->group_end();
		$this->db->where('adjustnotes.deleted', 0);

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
	 * Get search suggestions to find adjustnote
	 *
	 * @param string $search string containing the term to search in the adjustnote table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('adjustnote_id');
		$this->db->from('adjustnotes');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('documentno', $search);
			$this->db->or_like('description', $search);
			$this->db->group_end();
		$this->db->order_by('documentno', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->adjustnote_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Inserts or updates a adjustnote
	 *
	 * @param array $adjustnote_data array containing adjustnote information
	 *
	 * @param var $adjustnote_id identifier of the adjustnote to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$adjustnote_data, &$cash_daily_data, $adjustnote_id = FALSE)
	{
		$success = FALSE;

		$this->db->trans_start();

		$this->set_log("ID: ".$adjustnote_id);

		if(!$this->exists($adjustnote_id))
		{
			$this->set_log($this->db->last_query());
			$success = $this->db->insert('adjustnotes', $adjustnote_data);
			$this->set_log($this->db->last_query());
			$adjustnote_data['adjustnote_id'] = $this->db->insert_id();
			$adjustnote_id = $adjustnote_data['adjustnote_id'];
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
			$this->db->where('adjustnote_id', $adjustnote_id);
			$success = $this->db->update('adjustnotes', $adjustnote_data);
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
				$cash_daily['reference_id'] = $adjustnote_id;

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
	 * Deletes one adjustnote
	 *
	 * @param integer $adjustnote_id adjustnote identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($adjustnote_id,$currency)
	{
		$success = FALSE;

		$this->db->trans_start();
		$this->db->where('adjustnote_id', $adjustnote_id);

		if($this->db->update('adjustnotes', array('deleted' => 1)))
		{
			$success = TRUE;
		}

		$this->set_log($this->db->last_query());

		if($success)
		{
			$this->db->where('table_reference', 'adjustnotes');
			$this->db->where('reference_id', $adjustnote_id);
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
	 * Deletes a list of adjustnote
	 *
	 * @param array $adjustnote_ids list of adjustnote identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($adjustnote_ids)
	{
		$this->db->where_in('adjustnote_id', $adjustnote_ids);

		return $this->db->update('adjustnotes', array('deleted' => 1));
 	}
}
?>
