<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for MODEL classes
 */

class Delivery_document extends CI_Model
{
	/**
	 * Determines whether the given MODEL exists in the model database table
	 *
	 * @param integer $model_id model_identifier of the MODEL to verify the existence
	 *
	 * @return boolean TRUE if the MODEL exists, FALSE if not
	 */
	public function exists($model_id)
	{
		$this->db->from('delivery_documents');
		$this->db->where('id_delivery_document', $model_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all model from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of model table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('delivery_documents');
		$this->db->order_by('name', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of model database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('delivery_documents');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Model as an array
	 *
	 * @param integer $model_id model_identifier of the Model
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($id)
	{
		$query = $this->db->get_where('delivery_documents', array('id_delivery_document' => $id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Delivery_document_obj = new stdClass;

			foreach($this->db->list_fields('delivery_documents') as $field)
			{
				$Delivery_document_obj->$field = '';
			}

			return $Delivery_document_obj;
		}
	}

	
	/**
	 * Gets information about model as an array of rows
	 *
	 * @param array $model_ids array of model model_identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($model_ids)
	{
		$this->db->from('delivery_documents');
		$this->db->where_in('id_delivery_document', $model_ids);
		$this->db->order_by('created', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Model
	 *
	 * @param array $model_data array containing Model information
	 *
	 * @param var $model_id model_identifier of the Model to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$model_data, $model_id = FALSE)
	{
		if(!$model_id || !$this->exists($model_id))
		{
			if($this->db->insert('delivery_documents', $model_data))
			{
				$model_data['id_delivery_document'] = $this->db->insert_id();

				return TRUE;
			}
                        //print_r($this->db->error());exit();
			return FALSE;
		}

		$this->db->where('id_delivery_document', $model_id);

		return $this->db->update('delivery_documents', $model_data);
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'name', 'asc', TRUE);
	}

	/*
	Perform a search on model
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'code', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(id_delivery_document) as count');
		}  
                $this->db->select('dd.*,people.*,periods.name,loc.location_name as deposito,items.name as producto,type.name as types');
		$this->db->from('delivery_documents AS dd');
                $this->db->join('people AS people', 'dd.supplier_id = people.person_id');
                $this->db->join('periods AS periods', 'dd.period = periods.id');
                $this->db->join('items AS items', 'items.item_id = dd.item_id');
                $this->db->join('item_types AS type', 'type.item_type_id = dd.type_item_id');
                $this->db->join('fee_deposit AS fd', 'dd.fee_deposit_id = fd.id_fee_deposit');
                $this->db->join('stock_locations AS loc', 'fd.location_id = loc.location_id');
		$this->db->group_start();
                $this->db->like('dd.supplier_id', $search);
		$this->db->or_like('dd.period', $search);
                $this->db->or_like('dd.item_id', $search);
                $this->db->or_like('dd.code', $search);
		$this->db->or_like('dd.period', $search);
		$this->db->group_end();
		$this->db->where('dd.deleted', 0);

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
                
                $res = $this->db->get();
		
                
                return $res; 
	}

	/**
	 * Get search suggestions to find Model
	 *
	 * @param string $search string containing the term to search in the model table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('model_id');
		$this->db->from('models');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('type', $search);
			$this->db->group_end();
		$this->db->order_by('name', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->model_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one model
	 *
	 * @param integer $model_id Model model_identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($model_id)
	{
		$this->db->where('model_id', $model_id);

		$result &= $this->db->update('models', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of model
	 *
	 * @param array $model_ids list of Model model_identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($model_ids)
	{
		$this->db->where_in('model_id', $model_ids);

		return $this->db->update('models', array('deleted' => 1));
 	}
}
?>
