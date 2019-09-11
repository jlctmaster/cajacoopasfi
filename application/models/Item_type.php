<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Base class for ITEM_TYPE classes
 */

class Item_type extends CI_Model
{
	/**
	 * Determines whether the given ITEM_TYPE exists in the item_type database table
	 *
	 * @param integer $item_type_id item_type_identifier of the ITEM_TYPE to verify the existence
	 *
	 * @return boolean TRUE if the ITEM_TYPE exists, FALSE if not
	 */
	public function exists($item_type_id)
	{
		$this->db->from('item_types');
		$this->db->where('item_type_id', $item_type_id);

		return ($this->db->get()->num_rows() == 1);
	}

	/**
	 * Gets all item_type from the database table
	 *
	 * @param integer $limit limits the query return rows
	 *
	 * @param integer $offset offset the query
	 *
	 * @return array array of item_type table rows
	 */
	public function get_all($limit = 10000, $offset = 0)
	{
		$this->db->from('item_types');
		$this->db->order_by('name', 'asc');
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();
	}

	/**
	 * Gets total of rows of item_type database table
	 *
	 * @return integer row counter
	 */
	public function get_total_rows()
	{
		$this->db->from('item_types');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}

	/**
	 * Gets information about a Item_type as an array
	 *
	 * @param integer $item_type_id item_type_identifier of the Item_type
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_info($item_type_id)
	{
		$query = $this->db->get_where('item_types', array('item_type_id' => $item_type_id), 1);

		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$Item_type_obj = new stdClass;

			foreach($this->db->list_fields('item_types') as $field)
			{
				$item_type_obj->$field = '';
			}

			return $item_type_obj;
		}
	}

	public function get_family_suggestions($search)
	{
		$suggestions = array();
		/*$this->db->distinct();
		$this->db->select('family');
		$this->db->from('item_types');
		$this->db->like('family', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by('family', 'asc');*/

		$query = $this->db->query("SELECT DISTINCT family FROM 
			(SELECT DISTINCT category AS family FROM ".$this->db->dbprefix('items')." WHERE category LIKE '%".$search."%' AND deleted = 0 
			UNION ALL 
			SELECT DISTINCT family FROM ".$this->db->dbprefix('item_types')." WHERE family LIKE '%".$search."%' AND deleted = 0) fam 
			ORDER BY family ASC");

		foreach($query->result() as $row)
		{
			$suggestions[] = array('label' => $row->family);
		}

		return $suggestions;
	}

	/**
	 * Gets information about item_type as an array of rows
	 *
	 * @param array $item_type_ids array of item_type item_type_identifiers
	 *
	 * @return array containing all the fields of the table row
	 */
	public function get_multiple_info($item_type_ids)
	{
		$this->db->from('item_types');
		$this->db->where_in('item_type_id', $item_type_ids);
		$this->db->order_by('name', 'asc');

		return $this->db->get();
	}

	/**
	 * Inserts or updates a Item_type
	 *
	 * @param array $item_type_data array containing Item_type information
	 *
	 * @param var $item_type_id item_type_identifier of the Item_type to update the information
	 *
	 * @return boolean TRUE if the save was successful, FALSE if not
	 */
	public function save(&$item_type_data, $item_type_id = FALSE)
	{
		if(!$item_type_id || !$this->exists($item_type_id))
		{
			if($this->db->insert('item_types', $item_type_data))
			{
				$item_type_data['item_type_id'] = $this->db->insert_id();

				return TRUE;
			}

			return FALSE;
		}

		$this->db->where('item_type_id', $item_type_id);

		return $this->db->update('item_types', $item_type_data);
	}

	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		return $this->search($search, 0, 0, 'name', 'asc', TRUE);
	}

	/*
	Perform a search on item_type
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'name', $order='asc', $count_only = FALSE)
	{
		// get_found_rows case
		if($count_only == TRUE)
		{
			$this->db->select('COUNT(item_type_id) as count');
		}

		$this->db->from('item_types');
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('family', $search);
		$this->db->group_end();
		$this->db->where('deleted', 0);

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
	 * Get search suggestions to find Item_type
	 *
	 * @param string $search string containing the term to search in the item_type table
	 *
	 * @param integer $limit limit the search
	 *
	 * @return array array with the suggestion strings
	 */
	public function get_search_suggestions($search, $limit = 25)
	{
		$suggestions = array();

		$this->db->select('item_type_id');
		$this->db->from('item_types');
		$this->db->where('deleted', 0);
		$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('family', $search);
			$this->db->group_end();
		$this->db->order_by('name', 'asc');

		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('label' => $row->item_type_id);
		}

		//only return $limit suggestions
		if(count($suggestions) > $limit)
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

	/**
	 * Deletes one item_type
	 *
	 * @param integer $item_type_id Item_type item_type_identificator
	 *
	 * @return boolean always TRUE
	 */
	public function delete($item_type_id)
	{
		$this->db->where('item_type_id', $item_type_id);

		$result &= $this->db->update('item_types', array('deleted' => 1));
		
		return $result;
	}

	/**
	 * Deletes a list of item_type
	 *
	 * @param array $item_type_ids list of Item_type item_type_identificators
	 *
	 * @return boolean always TRUE
	 */
	public function delete_list($item_type_ids)
	{
		$this->db->where_in('item_type_id', $item_type_ids);

		return $this->db->update('item_types', array('deleted' => 1));
 	}
}
?>
