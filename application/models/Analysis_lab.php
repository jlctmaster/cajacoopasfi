<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for MODEL classes
 */

class analysis_lab extends CI_Model
{
	/**
	 * Determines whether the given MODEL exists in the model database table
	 *
	 * @param integer $model_id model_identifier of the MODEL to verify the existence
	 *
	 * @return boolean TRUE if the MODEL exists, FALSE if not
	 */
	public function exists($id)
	{
		$this->db->from('analysis_lab');
		$this->db->where('id_analysis_lab', $id);

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
		$this->db->from('analysis_lab');
		$this->db->order_by('created', 'asc');
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
		$this->db->from('analysis_lab');
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
		$query = $this->db->get_where('analysis_lab', array('id_analysis_lab' => $id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$model_obj = new stdClass;

			foreach($this->db->list_fields('analysis_lab') as $field)
			{
				$model_obj->$field = '';
			}

			return $model_obj;
		}
	}
        
        public function get_detail($id)
	{
		$query = $this->db->get_where('detail_analysis_lab', array('analysis_lab_id' => $id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$model_obj = new stdClass;

			foreach($this->db->list_fields('detail_analysis_lab') as $field)
			{
				$model_obj->$field = '';
			}

			return $model_obj;
		}
	}
        
        
        public function get_delivery_document_supplier($doc)
        {
           $query = $this->db->query("select dd.*, per.* from iom_delivery_documents as dd, iom_people as per "
                   . "where dd.supplier_id = per.person_id and dd.code ='".$doc."' group by dd.id_delivery_document");
                           
            foreach($query->result() as $row)
            {
                $suggestions[] = array('id' => $row->person_id,'name'=> strtoupper($row->first_name.' '.$row->last_name),
                    'amount_entered'=>$row->amount_entered,'document_delivery_id'=>$row->id_delivery_document) ;
            }
            //if(count($suggestions)>0)
                return $suggestions;
            //else
                //return $this->db->last_query;
        }
        
        
        
	public function get_type_suggestions($search)
	{
            
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('type');
		$this->db->from('models');
		$this->db->like('type', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by('type', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->type);
		}

		return $suggestions;
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
		$this->db->from('models');
		$this->db->where_in('model_id', $model_ids);
		$this->db->order_by('name', 'asc');

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
//	public function save(&$data, $id = FALSE)
//	{
//		if(!$id || !$this->exists($id))
//		{
//			if($this->db->insert('analysis_lab', $data))
//			{
//				$data['id_analysis_lab'] = $this->db->insert_id();
//
//				return TRUE;
//			}
//
//			return FALSE;
//		}
//
//		
//	}
        
        
        public function save($data, $id = FALSE)
	{
            $maestro = $data['maestro'];
            $detalle = $data['detalle'];
		if(!$id || !$this->exists($id))
		{
                                    
                    if($this->db->insert('analysis_lab', $maestro))
                    {
                        $maestro['id_analysis_lab'] = $this->db->insert_id();
                        $detalle['analysis_lab_id']=$maestro['id_analysis_lab'];
                        if($this->db->insert('detail_analysis_lab',$detalle))    
                        {
                            return TRUE;
                        }else
                            return false;
                            //print_r($this->db->last_query());

                    }
                    return FALSE;
                    //print_r($this->db->last_query());
                   // return $this->db->error();
		}
                
                $this->db->where('id_analysis_lab', $id);
                if($this->db->update('analysis_lab', $maestro))
                {
                    $this->db->where('id_detail_analysis_lab', $id);
                    return $this->db->update('detail_analysis_lab', $detalle);
                }        
		
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
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'name', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(id_analysis_lab) as count');
		}
                 $this->db->select('al.*,dal.*,people.* ');    
		$this->db->from('analysis_lab AS al');
                $this->db->join('delivery_documents AS dd', 'dd.id_delivery_document = al.document_delivery_id');
                $this->db->join('people AS people', 'dd.supplier_id = people.person_id');
                $this->db->join('detail_analysis_lab AS dal', 'dal.analysis_lab_id = al.id_analysis_lab');
		$this->db->group_start();
			$this->db->like('al.id_analysis_lab', $search);
			$this->db->or_like('al.document_delivery_id', $search);
		$this->db->group_end();
		$this->db->where('al.deleted', 0);

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
                //print_r($this->db->error();
                //$res = $this->db->last_query();
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
