<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for overall_cash classes
 */

class Overall_cash extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}


	/**
	 * Determines whether the given overall_cash exists in the overall_cash database table
	 *
	 * @param integer $overall_cash_id identifier of the overall_cash to verify the existence
	 *
	 * @return boolean TRUE if the overall_cash exists, FALSE if not
	 */
	public function exists($overall_cash_id)
	{
		$this->db->from('overall_cashs');
		$this->db->where('overall_cash_id', $overall_cash_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Determines whether the given overall_cash exists in the overall_cash database table
	 *
	 * @param integer $overall_cash_id identifier of the overall_cash to verify the existence
	 *
	 * @return boolean TRUE if the overall_cash exists, FALSE if not
	 */
	public function exists_opened($today)
	{
		$this->db->from('overall_cashs');
		$this->db->where('DATE_FORMAT(opendate,\'%Y-%m-%d\')', $today);
		$this->db->where('state',0);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Determines whether the given overall_cash exists in the overall_cash database table
	 *
	 * @param integer $overall_cash_id identifier of the overall_cash to verify the existence
	 *
	 * @return boolean TRUE if the overall_cash exists, FALSE if not
	 */
	public function closed_all($today)
	{
		$this->db->select('oc.overall_cash_id,
			SUM(COALESCE((CASE WHEN cf.currency = \''. CURRENCY .'\' THEN cf.amount * (CASE WHEN cf.operation_type = 1 THEN 1 ELSE -1 END) ELSE 0 END),0)) AS endingbalance, 
			SUM(COALESCE((CASE WHEN cf.currency = \''. USDCURRENCY .'\' THEN cf.amount * (CASE WHEN cf.operation_type = 1 THEN 1 ELSE -1 END) ELSE 0 END),0)) AS usdendingbalance
			');
		$this->db->from('overall_cashs oc');
		$this->db->join('cash_flow AS cf','cf.overall_cash_id = oc.overall_cash_id AND cf.deleted = 0','LEFT');
		$this->db->where('DATE_FORMAT(oc.opendate,\'%Y-%m-%d\') <', $today);
		$this->db->where('oc.state', 0);
		$this->db->group_by('oc.overall_cash_id');
		$query = $this->db->get();
		//echo $this->db->last_query();

		if($query->num_rows() >= 1)
		{
			foreach ($query->result() as $row) {
				$this->db->where('overall_cash_id',$row->overall_cash_id);
				$this->db->update('overall_cashs',array('state' => 1,'closedate' => date('Y-m-d H:i:s'),'endingbalance' => $row->endingbalance,'usdendingbalance' => $row->usdendingbalance));
			}
		}
	}

	/**
	 * Gets all overall_cash from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of overall_cash table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('overall_cashs');
		$this->db->order_by('opendate', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of overall_cash database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('overall_cashs');

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a overall_cash as an array
	 *
	 * @param integer $overall_cash_id identifier of the overall_cash
	 *
	 * @return array containing all the fields of the table row
	 */
	public function last_balance($today)
	{

		$this->db->from('overall_cashs');
		$this->db->where('DATE_FORMAT(closedate,\'%Y-%m-%d\') <= ',$today);
		$this->db->where('state',1);
		$this->db->order_by('closedate','DESC');
		$this->db->limit(1);

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$overall_cash_obj = new stdClass;

			foreach($this->db->list_fields('overall_cashs') as $field)
			{
				$overall_cash_obj->$field = '';
			}

			return $overall_cash_obj;
		}
	}

	/**
	 * Gets information about a overall_cash as an array
	 *
	 * @param integer $overall_cash_id identifier of the overall_cash
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_endingbalance($overall_cash_id)
	{

		$this->db->select('oc.overall_cash_id,oc.opendate,MAX(oc.startbalance) AS startbalance,MAX(oc.usdstartbalance) AS usdstartbalance,
			SUM(COALESCE((CASE WHEN cf.operation_type = 1 AND cf.currency = \'' . CURRENCY . '\' THEN cf.amount ELSE 0 END),0)) AS income,
			SUM(COALESCE((CASE WHEN cf.operation_type <> 1 AND cf.currency = \'' . CURRENCY . '\' THEN cf.amount ELSE 0 END),0)) AS cost,
			SUM(COALESCE((CASE WHEN cf.operation_type = 1 AND cf.currency = \'' . USDCURRENCY . '\' THEN cf.amount ELSE 0 END),0)) AS usdincome,
			SUM(COALESCE((CASE WHEN cf.operation_type <> 1 AND cf.currency = \'' . USDCURRENCY . '\' THEN cf.amount ELSE 0 END),0)) AS usdcost,
			SUM(COALESCE((CASE WHEN cf.currency = \''. CURRENCY .'\' THEN cf.amount * (CASE WHEN cf.operation_type = 1 THEN 1 ELSE -1 END) ELSE 0 END),0)) AS endingbalance, 
			SUM(COALESCE((CASE WHEN cf.currency = \''. USDCURRENCY .'\' THEN cf.amount * (CASE WHEN cf.operation_type = 1 THEN 1 ELSE -1 END) ELSE 0 END),0)) AS usdendingbalance
			');
		$this->db->from('overall_cashs oc');
		$this->db->join('cash_flow AS cf','cf.overall_cash_id = oc.overall_cash_id AND cf.deleted = 0','LEFT');
		$this->db->where('oc.overall_cash_id', $overall_cash_id);
		$this->db->group_by(array('oc.overall_cash_id','oc.opendate'));
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$overall_cash_obj = new stdClass;

			foreach($this->db->list_fields('overall_cashs') as $field)
			{
				$overall_cash_obj->$field = '';
			}

			return $overall_cash_obj;
		}
	}

	/**
	 * Gets information about a overall_cash as an array
	 *
	 * @param integer $overall_cash_id identifier of the overall_cash
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($overall_cash_id)
	{
		$this->db->select('overall_cashs.overall_cash_id,overall_cashs.opendate,overall_cashs.closedate,
			overall_cashs.startbalance,overall_cashs.openingbalance,overall_cashs.endingbalance,
			overall_cashs.usdstartbalance,overall_cashs.usdopeningbalance,overall_cashs.usdendingbalance,
			overall_cashs.state,
			SUM(COALESCE((CASE WHEN cash_flow.operation_type = 1 AND cash_flow.currency = \'' . CURRENCY . '\' THEN cash_flow.amount ELSE 0 END),0)) AS income,
			SUM(COALESCE((CASE WHEN cash_flow.operation_type <> 1 AND cash_flow.currency = \'' . CURRENCY . '\' THEN cash_flow.amount ELSE 0 END),0)) AS cost,
			SUM(COALESCE((CASE WHEN cash_flow.operation_type = 1 AND cash_flow.currency = \'' . USDCURRENCY . '\' THEN cash_flow.amount ELSE 0 END),0)) AS usdincome,
			SUM(COALESCE((CASE WHEN cash_flow.operation_type <> 1 AND cash_flow.currency = \'' . USDCURRENCY . '\' THEN cash_flow.amount ELSE 0 END),0)) AS usdcost');

		$this->db->from('overall_cashs AS overall_cashs');
		$this->db->join('cash_flow AS cash_flow','overall_cashs.overall_cash_id = cash_flow.overall_cash_id AND cash_flow.deleted = 0','LEFT');
		$this->db->where('overall_cashs.overall_cash_id',$overall_cash_id);
		$this->db->group_by(array('overall_cashs.overall_cash_id','overall_cashs.opendate','overall_cashs.closedate',
			'overall_cashs.startbalance','overall_cashs.openingbalance','overall_cashs.endingbalance',
			'overall_cashs.usdstartbalance','overall_cashs.usdopeningbalance','overall_cashs.usdendingbalance',
			'overall_cashs.state'));

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$overall_cash_obj = new stdClass;

			foreach($this->db->list_fields('overall_cashs') as $field)
			{
				$overall_cash_obj->$field = '';
			}

			return $overall_cash_obj;
		}
	}

	/**
	 * Gets information about a overall_cash as an array
	 *
	 * @param integer $overall_cash_id identifier of the overall_cash
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_denominations($overall_cash_id,$currency)
	{
		$query = $this->db->get_where('overallcash_currencys', array('overall_cash_id' => $overall_cash_id,'currency' => $currency));

		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			//create object with empty properties.
			$overall_cash_obj = new stdClass;

			foreach($this->db->list_fields('overall_cashs') as $field)
			{
				$overall_cash_obj->$field = '';
			}

			return $overall_cash_obj;
		}
	}

	/**
	 * Gets information about a overall_cash as an array
	 *
	 * @param integer $overall_cash_id identifier of the overall_cash
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_overall_cash_open_info($today)
	{
		$query = $this->db->get_where('overall_cashs', array('DATE_FORMAT(opendate,\'%Y-%m-%d\')' => $today), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$overall_cash_obj = new stdClass;

			foreach($this->db->list_fields('overall_cashs') as $field)
			{
				$overall_cash_obj->$field = '';
			}

			return $overall_cash_obj;
		}
	}

	/**
	 * Gets information about overall_cash as an array of rows
	 *
	 * @param array $overall_cash_ids array of overall_cash identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($overall_cash_ids)
	{
		$this->db->from('overall_cashs');
		$this->db->where_in('overall_cash_id', $overall_cash_ids);
		$this->db->order_by('opendate', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a overall_cash
	 *
	 * @param array $overall_cash_data array containing overall_cash information
	 *
	 * @param var $overall_cash_id identifier of the overall_cash to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function opened(&$overall_cash_data, &$cash_flow_data, $overall_cash_id = FALSE)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->set_log("ID: ".$overall_cash_id);

		$success = $this->db->insert('overall_cashs', $overall_cash_data);
		
		$this->set_log($this->db->last_query());
		
		$overall_cash_id = $this->db->insert_id();

		$overall_cash_data['overall_cash_id'] = $overall_cash_id;

		if($success)
		{

			foreach($cash_flow_data as $cash_flow)
			{
				if(!$this->Cash_flow->exists_movement('overall_cashs',$overall_cash_id,$cash_flow['currency']))
				{
					$this->set_log($this->db->last_query());
					$cash_flow['overall_cash_id'] = $overall_cash_id;
					$cash_flow['reference_id'] = $overall_cash_id;

					$success = $this->Cash_flow->save($cash_flow,-1);
					$this->set_log($this->db->last_query());
				}
			}
		}

		$this->db->trans_complete();

		$success &= $this->db->trans_status();

		return $success;
	}

	/**
	 * Deletes one overall_cash
	 *
	 * @param integer $overall_cash_id overall_cash identificator
	 *
	 * @return boolean always TRUE
	 */
	public function closed(&$overall_cash_data,&$currency_data,$overall_cash_id)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->where('overall_cash_id', $overall_cash_id);

		if($this->db->update('overall_cashs', $overall_cash_data))
		{
			$success = TRUE;
		}

		$this->set_log($this->db->last_query());

		if($success)
		{
			//	Delete all
			$this->db->delete('overallcash_currencys',array('overall_cash_id'=>$overall_cash_id));
			$this->set_log($this->db->last_query());
			foreach($currency_data as $currency)
			{
				$success = $this->db->insert('overallcash_currencys',
					array('overall_cash_id'=>$overall_cash_id,
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
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'overall_cashs.opendate', 'desc', TRUE);
	}

	/*
	Perform a search on overall_cash
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'overall_cashs.opendate', $order='desc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(overall_cashs.overall_cash_id) as count');
		}
		else
		{
			$this->db->select('overall_cashs.overall_cash_id,overall_cashs.opendate,overall_cashs.state,
				SUM(COALESCE((CASE WHEN cash_flow.operation_type = 1 AND cash_flow.currency = \'' . CURRENCY . '\' THEN cash_flow.amount ELSE 0 END),0)) AS income,
				SUM(COALESCE((CASE WHEN cash_flow.operation_type <> 1 AND cash_flow.currency = \'' . CURRENCY . '\' THEN cash_flow.amount ELSE 0 END),0)) AS cost,
				SUM(COALESCE((CASE WHEN cash_flow.operation_type = 1 AND cash_flow.currency = \'' . USDCURRENCY . '\' THEN cash_flow.amount ELSE 0 END),0)) AS usdincome,
				SUM(COALESCE((CASE WHEN cash_flow.operation_type <> 1 AND cash_flow.currency = \'' . USDCURRENCY . '\' THEN cash_flow.amount ELSE 0 END),0)) AS usdcost');	
		}

		$this->db->from('overall_cashs AS overall_cashs');
		$this->db->join('cash_flow AS cash_flow','overall_cashs.overall_cash_id = cash_flow.overall_cash_id AND cash_flow.deleted = 0','LEFT');

		// get_found_rows case
		if($count_only == TRUE)
		{
			return $this->db->get()->row()->count;
		}
		
		$this->db->group_by(array('overall_cashs.overall_cash_id','overall_cashs.opendate','overall_cashs.state'));
		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query;
	}

	/**
	 * Get search suggestions to find overall_cash
	 *
	 * @param string $search string containing the term to search in the overall_cash table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('overall_cash_id');
		$this->db->from('overall_cashs');
		$this->db->order_by('opendate', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->overall_cash_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one overall_cash
	 *
	 * @param integer $overall_cash_id overall_cash identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($overall_cash_id)
	{
		$this->db->where('overall_cash_id', $overall_cash_id);

		$result &= $this->db->update('overall_cashs', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of overall_cash
	 *
	 * @param array $overall_cash_ids list of overall_cash identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($overall_cash_ids)
	{
		$this->db->where_in('overall_cash_id', $overall_cash_ids);

		return $this->db->update('overall_cashs', array('deleted' => 1));
 	}
}
?>
