<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for invoice classes
 */

class Invoice extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}


	/**
	 * Determines whether the given invoice exists in the invoice database table
	 *
	 * @param integer $invoice_id identifier of the invoice to verify the existence
	 *
	 * @return boolean TRUE if the invoice exists, FALSE if not
	 */
	public function exists($invoice_id)
	{
		$this->db->from('invoices');
		$this->db->where('invoice_id', $invoice_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all invoice from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of invoice table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('invoices');
		$this->db->order_by('serieno', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of invoice database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('invoices');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a invoice as an array
	 *
	 * @param integer $invoice_id identifier of the invoice
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($invoice_id,$cash_book_id)
	{
		$this->db->select("
				invoices.*,
				(CASE WHEN customers.ruc IS NULL THEN people.dni ELSE customers.ruc END) AS ruc,
				customers.company_name,
				(CASE WHEN customers.company_name IS NULL THEN CONCAT(people.first_name,' ',people.last_name) ELSE customers.company_name END) AS name,
				CONCAT(user.first_name,' ',user.last_name,' (',cash_books.code,')') AS cash_book,
				(CASE WHEN invoices.movementtype = 'C' THEN invoices.totalamt ELSE 0 END) cash,
				(CASE WHEN invoices.movementtype = 'B' THEN invoices.totalamt ELSE 0 END) bank,
				(CASE WHEN invoices.cash_book_id = $cash_book_id AND DATE_FORMAT(invoices.documentdate,'%Y-%m-%d') = DATE_FORMAT(CURDATE(),'%Y-%m-%d') THEN 0 ELSE 1 END) readonly   
			");
		$this->db->from('invoices AS invoices');
		$this->db->join('cash_books AS cash_books', 'cash_books.cash_book_id = invoices.cash_book_id');
		$this->db->join('people AS user', 'user.person_id = cash_books.user_id');
		$this->db->join('customers AS customers', 'customers.person_id = invoices.person_id');
		$this->db->join('people AS people', 'people.person_id = customers.person_id');
		$this->db->where('invoices.invoice_id', $invoice_id);
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$invoice_obj = new stdClass;

			foreach($this->db->list_fields('invoices') as $field)
			{
				$invoice_obj->$field = '';
			}

			return $invoice_obj;
		}
	}

	/**
	 * Gets information about a Credit as an array
	 *
	 * @param integer $invoice_id identifier of the Credit
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_detail_info($invoice_id)
	{
		$this->db->from('lineinvoices');
		$this->db->where('invoice_id', $invoice_id);

		$this->db->order_by('lineinvoice_id');

		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			//create object with empty properties.
			$Credit_obj = new stdClass;

			foreach($this->db->list_fields('lineinvoices') as $field)
			{
				$invoice_obj->$field = '';
			}
			$invoice_obj->name = '';
			$invoice_obj->location_name = '';

			return array($invoice_obj);
		}
	}

	/**
	 * Gets information about invoice as an array of rows
	 *
	 * @param array $invoice_ids array of invoice identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($invoice_ids)
	{
		$this->db->from('invoices');
		$this->db->where_in('invoice_id', $invoice_ids);
		$this->db->order_by('serieno', 'asc');

		return $this->db->get();
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search,$cash_book_id)
	{
		return $this->search($search, $cash_book_id, 0, 0, 'serieno', 'asc', TRUE);
	}

	/*
	Perform a search on invoice
	*/
	public function search($search, $cash_book_id, $rows = 0, $limit_from = 0, $sort = 'serieno', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(invoices.invoice_id) as count');
		}
		else
		{
			$this->db->select("
				invoices.*,
				(CASE WHEN customers.ruc IS NULL THEN people.dni ELSE customers.ruc END) AS ruc,
				customers.company_name,
				(CASE WHEN customers.company_name IS NULL THEN CONCAT(people.first_name,' ',people.last_name) ELSE customers.company_name END) AS name,
				CONCAT(user.first_name,' ',user.last_name,' (',cash_books.code,')') AS cash_book,
				(CASE WHEN invoices.movementtype = 'C' THEN invoices.totalamt ELSE 0 END) cash,
				(CASE WHEN invoices.movementtype = 'B' THEN invoices.totalamt ELSE 0 END) bank,
				(CASE WHEN invoices.cash_book_id = $cash_book_id AND DATE_FORMAT(invoices.documentdate,'%Y-%m-%d') = DATE_FORMAT(CURDATE(),'%Y-%m-%d') THEN 0 ELSE 1 END) readonly   
			");
		}

		$this->db->from('invoices AS invoices');
		$this->db->join('cash_books AS cash_books', 'cash_books.cash_book_id = invoices.cash_book_id');
		$this->db->join('people AS user', 'user.person_id = cash_books.user_id');
		$this->db->join('customers AS customers', 'customers.person_id = invoices.person_id');
		$this->db->join('people AS people', 'people.person_id = customers.person_id');
		$this->db->group_start();
			$this->db->like('invoices.serieno', $search);
			$this->db->or_like('customers.ruc', $search);
			$this->db->or_like('customers.company_name', $search);
			$this->db->or_like('people.dni', $search);
			$this->db->or_like('CONCAT(people.first_name,\' \',people.last_name)', $search);
			$this->db->or_like('CONCAT(user.first_name,\' \',user.last_name,\' (\',cash_books.code,\')\')', $search);
		$this->db->group_end();
		$this->db->where('invoices.deleted', 0);

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
	 * Get search suggestions to find invoice
	 *
	 * @param string $search string containing the term to search in the invoice table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('invoice_id');
		$this->db->from('invoices');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('serieno', $search);
			$this->db->group_end();
		$this->db->order_by('serieno', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->invoice_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Inserts or updates a invoice
	 *
	 * @param array $invoice_data array containing invoice information
	 *
	 * @param var $invoice_id identifier of the invoice to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$invoice_data, &$invoice_item_data, &$cash_daily_data, $invoice_id = FALSE)
	{
		$success = FALSE;

		$this->db->trans_start();

		$this->set_log("ID: ".$invoice_id);

		if(!$this->exists($invoice_id))
		{
			$this->set_log($this->db->last_query());
			$success = $this->db->insert('invoices', $invoice_data);
			$this->set_log($this->db->last_query());
			$invoice_data['invoice_id'] = $this->db->insert_id();
			$invoice_id = $invoice_data['invoice_id'];
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
			$this->db->where('invoice_id', $invoice_id);
			$success = $this->db->update('invoices', $invoice_data);
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
			$success = $this->db->delete('lineinvoices', array('invoice_id' => $invoice_id));
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
				foreach($invoice_item_data as $item)
				{
					$success = $this->db->insert('lineinvoices', 
						array(
							'detail' => $item['detail'], 
							'quantity' => $item['quantity'], 
							'price' => $item['price'], 
							'amount' => $item['amount'], 
							'invoice_id' => $invoice_id)
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
				$cash_daily['reference_id'] = $invoice_id;

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
	 * Deletes one invoice
	 *
	 * @param integer $invoice_id invoice identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($invoice_id,$currency)
	{
		$success = FALSE;

		$this->db->trans_start();
		$this->db->where('invoice_id', $invoice_id);

		if($this->db->update('invoices', array('deleted' => 1)))
		{
			$success = TRUE;
		}

		$this->set_log($this->db->last_query());

		if($success)
		{
			$this->db->where('invoice_id', $invoice_id);

			if($this->db->update('lineinvoices', array('deleted' => 1)))
			{
				$success = TRUE;
			}
		}

		if($success)
		{
			$this->db->where('table_reference', 'invoices');
			$this->db->where('reference_id', $invoice_id);
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
	 * Deletes a list of invoice
	 *
	 * @param array $invoice_ids list of invoice identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($invoice_ids)
	{
		$this->db->where_in('invoice_id', $invoice_ids);

		return $this->db->update('invoices', array('deleted' => 1));
 	}
}
?>
