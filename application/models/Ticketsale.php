<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for ticketsale classes
 */

class Ticketsale extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}


	/**
	 * Determines whether the given ticketsale exists in the ticketsale database table
	 *
	 * @param integer $ticketsale_id identifier of the ticketsale to verify the existence
	 *
	 * @return boolean TRUE if the ticketsale exists, FALSE if not
	 */
	public function exists($ticketsale_id)
	{
		$this->db->from('ticketsales');
		$this->db->where('ticketsale_id', $ticketsale_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all ticketsale from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of ticketsale table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('ticketsales');
		$this->db->order_by('serieno', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of ticketsale database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('ticketsales');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a ticketsale as an array
	 *
	 * @param integer $ticketsale_id identifier of the ticketsale
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($ticketsale_id,$cash_book_id)
	{
		$this->db->select("
				ticketsales.*,
				CASE WHEN ticketsales.person_id IS NULL THEN ticketsales.person_name ELSE CONCAT(people.first_name,' ',people.last_name) END AS name,
				CONCAT(user.first_name,' ',user.last_name,' (',cash_books.code,')') AS cash_book,
				CASE WHEN ticketsales.movementtype = 'C' THEN ticketsales.totalamt ELSE 0 END cash,
				CASE WHEN ticketsales.movementtype = 'B' THEN ticketsales.totalamt ELSE 0 END bank,
				CASE WHEN ticketsales.cash_book_id = $cash_book_id AND DATE_FORMAT(ticketsales.documentdate,'%Y-%m-%d') = DATE_FORMAT(CURDATE(),'%Y-%m-%d') THEN 0 ELSE 1 END readonly 
			");
		$this->db->from('ticketsales AS ticketsales');
		$this->db->join('cash_books AS cash_books', 'cash_books.cash_book_id = ticketsales.cash_book_id');
		$this->db->join('people AS user', 'user.person_id = cash_books.user_id');
		$this->db->join('people AS people', 'people.person_id = ticketsales.person_id','LEFT');
		$this->db->where('ticketsales.ticketsale_id', $ticketsale_id);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$ticketsale_obj = new stdClass;

			foreach($this->db->list_fields('ticketsales') as $field)
			{
				$ticketsale_obj->$field = '';
			}

			return $ticketsale_obj;
		}
	}

	/**
	 * Gets information about a Credit as an array
	 *
	 * @param integer $ticketsale_id identifier of the Credit
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_detail_info($ticketsale_id)
	{
		$this->db->from('lineticketsales');
		$this->db->where('ticketsale_id', $ticketsale_id);

		$this->db->order_by('lineticketsale_id');

		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			//create object with empty properties.
			$Credit_obj = new stdClass;

			foreach($this->db->list_fields('lineticketsales') as $field)
			{
				$ticketsale_obj->$field = '';
			}
			$ticketsale_obj->name = '';
			$ticketsale_obj->location_name = '';

			return array($ticketsale_obj);
		}
	}

	/**
	 * Gets information about ticketsale as an array of rows
	 *
	 * @param array $ticketsale_ids array of ticketsale identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($ticketsale_ids)
	{
		$this->db->from('ticketsales');
		$this->db->where_in('ticketsale_id', $ticketsale_ids);
		$this->db->order_by('serieno', 'asc');

		return $this->db->get();
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search,$cash_book_id)
	{
		return $this->search($search, 0, 0, 'serieno', 'asc', TRUE);
	}

	/*
	Perform a search on ticketsale
	*/
	public function search($search, $cash_book_id, $rows = 0, $limit_from = 0, $sort = 'serieno', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(ticketsales.ticketsale_id) as count');
		}
		else
		{
			$this->db->select("
				ticketsales.*,
				CASE WHEN ticketsales.person_id IS NULL THEN ticketsales.person_name ELSE CONCAT(people.first_name,' ',people.last_name) END AS name,
				CONCAT(user.first_name,' ',user.last_name,' (',cash_books.code,')') AS cash_book,
				CASE WHEN ticketsales.movementtype = 'C' THEN ticketsales.totalamt ELSE 0 END cash,
				CASE WHEN ticketsales.movementtype = 'B' THEN ticketsales.totalamt ELSE 0 END bank,
				CASE WHEN ticketsales.cash_book_id = $cash_book_id AND DATE_FORMAT(ticketsales.documentdate,'%Y-%m-%d') = DATE_FORMAT(CURDATE(),'%Y-%m-%d') THEN 0 ELSE 1 END readonly   
			");
		}

		$this->db->from('ticketsales AS ticketsales');
		$this->db->join('cash_books AS cash_books', 'cash_books.cash_book_id = ticketsales.cash_book_id');
		$this->db->join('people AS user', 'user.person_id = cash_books.user_id');
		$this->db->join('people AS people', 'people.person_id = ticketsales.person_id','LEFT');
		$this->db->group_start();
			$this->db->like('ticketsales.serieno', $search);
			$this->db->or_like('ticketsales.documentno', $search);
			$this->db->or_like('CONCAT(people.first_name,\' \',people.last_name)', $search);
			$this->db->or_like('CONCAT(user.first_name,\' \',user.last_name,\' (\',cash_books.code,\')\')', $search);
		$this->db->group_end();
		$this->db->where('ticketsales.deleted', 0);

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
	 * Get search suggestions to find ticketsale
	 *
	 * @param string $search string containing the term to search in the ticketsale table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('ticketsale_id');
		$this->db->from('ticketsales');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('serieno', $search);
			$this->db->or_like('documentno', $search);
			$this->db->group_end();
		$this->db->order_by('serieno', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->ticketsale_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Inserts or updates a ticketsale
	 *
	 * @param array $ticketsale_data array containing ticketsale information
	 *
	 * @param var $ticketsale_id identifier of the ticketsale to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$ticketsale_data, &$ticketsale_item_data, &$cash_daily_data, $ticketsale_id = FALSE)
	{
		$success = FALSE;

		$this->db->trans_start();

		$this->set_log("ID: ".$ticketsale_id);

		if(!$this->exists($ticketsale_id))
		{
			$this->set_log($this->db->last_query());
			$success = $this->db->insert('ticketsales', $ticketsale_data);
			$this->set_log($this->db->last_query());
			$ticketsale_data['ticketsale_id'] = $this->db->insert_id();
			$ticketsale_id = $ticketsale_data['ticketsale_id'];
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
			$this->db->where('ticketsale_id', $ticketsale_id);
			$success = $this->db->update('ticketsales', $ticketsale_data);
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

		//We have either inserted or updated a new user, now lets set permissions.
		if($success)
		{
			//First lets clear out any grants the user currently has.
			$success = $this->db->delete('lineticketsales', array('ticketsale_id' => $ticketsale_id));
			$this->set_log($this->db->last_query());
			if($success)
			{
				$this->set_log("Instruccion ejecutada con exito!");
			}
			else
			{
				$this->set_log("Error al ejecutar la instruccion!");
			}

			//Now insert the new grants
			if($success)
			{
				foreach($ticketsale_item_data as $item)
				{
					$success = $this->db->insert('lineticketsales', 
						array(
							'detail' => $item['detail'], 
							'quantity' => $item['quantity'], 
							'price' => $item['price'], 
							'amount' => $item['amount'], 
							'ticketsale_id' => $ticketsale_id)
						);
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
		}

		if($success)
		{
			foreach($cash_daily_data as $cash_daily)
			{
				$cash_daily['reference_id'] = $ticketsale_id;

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
	 * Deletes one ticketsale
	 *
	 * @param integer $ticketsale_id ticketsale identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($ticketsale_id,$currency)
	{
		$success = FALSE;

		$this->db->trans_start();
		$this->db->where('ticketsale_id', $ticketsale_id);

		if($this->db->update('ticketsales', array('deleted' => 1)))
		{
			$success = TRUE;
		}

		$this->set_log($this->db->last_query());

		if($success)
		{
			$this->db->where('ticketsale_id', $ticketsale_id);

			if($this->db->update('lineticketsales', array('deleted' => 1)))
			{
				$success = TRUE;
			}
		}

		if($success)
		{
			$this->db->where('table_reference', 'ticketsales');
			$this->db->where('reference_id', $ticketsale_id);
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
	 * Deletes a list of ticketsale
	 *
	 * @param array $ticketsale_ids list of ticketsale identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($ticketsale_ids)
	{
		$this->db->where_in('ticketsale_id', $ticketsale_ids);

		return $this->db->update('ticketsales', array('deleted' => 1));
 	}
}
?>
