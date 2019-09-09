<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for QUALITY_CERTIFICATE classes
 */

class Quality_certificate extends CI_Model
{

	private $log;

	function get_log(){
		return $this->log;
	}

	function set_log($log){
		return $this->log = $this->log."<br>".$log;
	}

	/**
	 * Determines whether the given QUALITY_CERTIFICATE exists in the quality_certificate database table
	 *
	 * @param integer $quality_certificate_id identifier of the QUALITY_CERTIFICATE to verify the existence
	 *
	 * @return boolean TRUE if the QUALITY_CERTIFICATE exists, FALSE if not
	 */
	public function exists($quality_certificate_id)
	{
		$this->db->from('quality_certificates');
		$this->db->where('quality_certificate_id', $quality_certificate_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all quality_certificate from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of quality_certificate table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('quality_certificates');
		$this->db->order_by('certificate_number', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of quality_certificate database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('quality_certificates');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Quality_certificate as an array
	 *
	 * @param integer $quality_certificate_id identifier of the Quality_certificate
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($quality_certificate_id)
	{
		$this->db->select("quality_certificates.*,
			people.dni,CONCAT(people.first_name,' ',people.last_name) AS name ");	

		$this->db->from('quality_certificates AS quality_certificates');
		$this->db->join('suppliers AS suppliers','quality_certificates.person_id = suppliers.person_id');
		$this->db->join('people AS people','people.person_id = suppliers.person_id');
		$this->db->join('stock_locations AS location','quality_certificates.location_id = location.location_id');
		$this->db->where('quality_certificate_id', $quality_certificate_id);

		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Quality_certificate_obj = new stdClass;

			foreach($this->db->list_fields('quality_certificates') as $field)
			{
				$quality_certificate_obj->$field = '';
			}

			return $quality_certificate_obj;
		}
	}

	/**
	 * Gets information about quality_certificate as an array of rows
	 *
	 * @param array $quality_certificate_ids array of quality_certificate identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($quality_certificate_ids)
	{
		$this->db->from('quality_certificates');
		$this->db->where_in('quality_certificate_id', $quality_certificate_ids);
		$this->db->order_by('certificate_number', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Quality_certificate
	 *
	 * @param array $quality_certificate_data array containing Quality_certificate information
	 *
	 * @param var $quality_certificate_id identifier of the Quality_certificate to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$quality_certificate_data, $quality_certificate_id = FALSE)
	{
		if(!$quality_certificate_id || !$this->exists($quality_certificate_id))
		{
			$this->set_log($this->db->last_query());
			if($this->db->insert('quality_certificates', $quality_certificate_data))
			{
				$this->set_log($this->db->last_query());
				$quality_certificate_data['quality_certificate_id'] = $this->db->insert_id();

				return TRUE;
			}
			$this->set_log($this->db->last_query());

			return FALSE;
		}

		$this->db->where('quality_certificate_id', $quality_certificate_id);

		return $this->db->update('quality_certificates', $quality_certificate_data);
	}

	/**
	*	Save Batch Quality_certificate Serie 01
	*/
	public function save_batch(&$batch_data)
	{
		return $this->db->insert_batch('quality_certificates', $batch_data);
	}

	/*
	Gets rows
	*/
	public function get_found_rows($serieno,$search)
	{
		return $this->search($search, 0, 0, 'certificate_number', 'asc', TRUE);
	}

	/*
	Perform a search on quality_certificate
	*/
	public function search($serieno,$search, $rows = 0, $limit_from = 0, $sort = 'certificate_number', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(quality_certificates.quality_certificate_id) as count');
		}
		else
		{
			$this->db->select("quality_certificates.*,
			people.dni,CONCAT(people.first_name,' ',people.last_name) AS name,
			location.location_name,
			voucher_operations.voucher_operation_number,
			voucher_operations.state AS voucher_state");	
		}

		$this->db->from('quality_certificates AS quality_certificates');
		$this->db->join('suppliers AS suppliers','quality_certificates.person_id = suppliers.person_id');
		$this->db->join('people AS people','people.person_id = suppliers.person_id');
		$this->db->join('stock_locations AS location','quality_certificates.location_id = location.location_id');
		$this->db->join('voucher_operations AS voucher_operations','quality_certificates.voucher_operation_id = voucher_operations.voucher_operation_id','LEFT');
		$this->db->where('quality_certificates.serieno', $serieno);
		$this->db->group_start();
			$this->db->like('quality_certificates.certificate_number', $search);
			$this->db->or_like('quality_certificates.quality', $search);
			$this->db->or_like('people.dni', $search);
			$this->db->or_like("CONCAT(people.first_name,' ',people.last_name)", $search);
			$this->db->or_like('location.location_name', $search);
		$this->db->group_end();
		$this->db->where('quality_certificates.deleted', 0);

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

	/*
	Gets rows
	*/
	public function get_allocated_found_rows($voucher_operation_id,$search)
	{
		return $this->search($search, 0, 0, 'certificate_number', 'asc', TRUE);
	}

	/*
	Perform a search on quality_certificate
	*/
	public function search_allocated($voucher_operation_id,$search, $rows = 0, $limit_from = 0, $sort = 'certificate_number', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(quality_certificates.quality_certificate_id) as count');
		}
		else
		{
			$this->db->select("quality_certificates.*,
			people.dni,CONCAT(people.first_name,' ',people.last_name) AS name,
			location.location_name");	
		}

		$this->db->from('quality_certificates AS quality_certificates');
		$this->db->join('suppliers AS suppliers','quality_certificates.person_id = suppliers.person_id');
		$this->db->join('people AS people','people.person_id = suppliers.person_id');
		$this->db->join('stock_locations AS location','quality_certificates.location_id = location.location_id');
		$this->db->where('quality_certificates.voucher_operation_id', $voucher_operation_id);
		$this->db->group_start();
			$this->db->like('quality_certificates.certificate_number', $search);
			$this->db->or_like('quality_certificates.quality', $search);
			$this->db->or_like('people.dni', $search);
			$this->db->or_like("CONCAT(people.first_name,' ',people.last_name)", $search);
			$this->db->or_like('location.location_name', $search);
		$this->db->group_end();
		$this->db->where('quality_certificates.deleted', 0);

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
	 * Get search suggestions to find Quality_certificate
	 *
	 * @param string $search string containing the term to search in the quality_certificate table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('quality_certificate_id');
		$this->db->from('quality_certificates');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('certificate_number', $search);
			$this->db->or_like('serieno', $search);
			$this->db->group_end();
		$this->db->order_by('certificate_number', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->quality_certificate_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	public function get_quality_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('UPPER(quality) AS quality');
		$this->db->from('quality_certificates');
		$this->db->like('quality', strtoupper($search));
		$this->db->where('deleted', 0);
		$this->db->order_by('quality', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->quality);
		}

		return $suggestions;
	}

	public function get_certificates_by_person($person_id,$serieno)
	{
		$suggestions = array();
		$this->db->from('quality_certificates');
		$this->db->where('person_id', $person_id);
		$this->db->where('serieno', $serieno);
		$this->db->where('deleted', 0);
		$this->db->where('voucher_operation_id IS NULL',NULL,FALSE);
		$this->db->order_by('depositdate', 'asc');
		$this->db->order_by('certificate_number', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array(
				'quality_certificate_id' => $row->quality_certificate_id,
				'certificate_number' => $row->certificate_number,
				'quality' => $row->quality,
				'kg_dry' => $row->kg_dry,
				'qq_dry' => $row->qq_dry,
				'price' => $row->price,
				'amount' => $row->amount);
		}

		return $suggestions;
	}

	public function get_certificates_allocated($voucher_operation_id)
	{
		$suggestions = array();
		$this->db->from('quality_certificates');
		$this->db->where('voucher_operation_id',$voucher_operation_id);
		$this->db->order_by('depositdate', 'asc');
		$this->db->order_by('certificate_number', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array(
				'quality_certificate_id' => $row->quality_certificate_id,
				'certificate_number' => $row->certificate_number,
				'quality' => $row->quality,
				'kg_dry' => $row->kg_dry,
				'qq_dry' => $row->qq_dry,
				'price' => $row->price,
				'amount' => $row->amount);
		}

		return $suggestions;
	}

	/**
	 * Deletes one quality_certificate
	 *
	 * @param integer $quality_certificate_id Quality_certificate identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($quality_certificate_id)
	{
		$this->db->where('quality_certificate_id', $quality_certificate_id);

		$result &= $this->db->update('quality_certificates', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of quality_certificate
	 *
	 * @param array $quality_certificate_ids list of Quality_certificate identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($quality_certificate_ids)
	{
		$this->db->where_in('quality_certificate_id', $quality_certificate_ids);

		return $this->db->update('quality_certificates', array('deleted' => 1));
 	}

 	/**
	 * Get data to import from other database
 	**/
 	public function get_import_data()
 	{
 		$coopafsi = $this->load->database('coopafsi', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

  		$query = $coopafsi->query("SELECT 
				ce.fecha_creacion AS depositdate,
				'01' AS serieno,
				ce.codigo AS certificate_number,
				TRIM(p.documento_identidad) AS dni,
				p.descripcion AS name,
				dce.kilos_secos AS kg_dry,
				dce.qqs_secos AS qq_dry,
				0 AS rate_profile,
				0 AS physical_performance,
				UPPER(CONCAT(pr.nombre,' ',pr.codigo)) AS quality,
				1 AS location_id,
				0 AS price,
				0 AS amount,
				CONCAT(dce.id_detalle_comprobante_entrada,dce.id_comprobante_entrada) AS reference_id,
				1 AS imported 
			FROM comprobante_entrada ce 
			JOIN detalle_comprobante_entrada dce ON ce.id_comprobante_entrada = dce.id_comprobante_entrada 
			JOIN proveedor p ON ce.id_proveedor = p.id_proveedor 
			JOIN producto pr ON dce.id_producto = pr.id_producto 
			WHERE ce.estado = 1");

  		return $query->result_array();
 	}

	/**
	 * Determines whether the given QUALITY_CERTIFICATE exists in the quality_certificate database table
	 *
	 * @param integer $quality_certificate_id identifier of the QUALITY_CERTIFICATE to verify the existence
	 *
	 * @return boolean TRUE if the QUALITY_CERTIFICATE exists, FALSE if not
	 */
	public function certificate_reference_exists($reference_id)
	{
		$this->db->from('quality_certificates');
		$this->db->where('serieno', '01');
		$this->db->where('deleted', 0);
		$this->db->where('reference_id', $reference_id);

		return ($this->db->get()->num_rows() == 1);
	}
}
?>
