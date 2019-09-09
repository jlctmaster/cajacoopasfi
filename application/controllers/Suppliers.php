<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Persons.php");

class Suppliers extends Persons
{
	public function __construct()
	{
		parent::__construct('suppliers');
	}

	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_suppliers_manage_table_headers());

		$this->load->view('people/manage', $data);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function get_row_by_dni($dni)
	{
		$data_row = $this->Person->get_info_by_dni($dni);
		foreach(get_object_vars($data_row) as $property => $value)
		{
			$data_row->$property = $this->xss_clean($value);
		}
		echo json_encode($data_row);
	}

	/*
	Gets one row for a supplier manage table. This is called using AJAX to update one row.
	*/
	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_supplier_data_row($this->Supplier->get_info($row_id)));

		echo json_encode($data_row);
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	public function get_growing_area_locale($growing_area_id)
	{
		$data_row = $this->Growing_area->get_info($growing_area_id);
		foreach(get_object_vars($data_row) as $property => $value)
		{
			$data_row->$property = $this->xss_clean($value);
		}
		echo json_encode($data_row);
	}
	
	/*
	Returns Supplier table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$suppliers = $this->Supplier->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Supplier->get_found_rows($search);

		$data_rows = array();
		foreach($suppliers->result() as $supplier)
		{
			$data_rows[] = $this->xss_clean(get_supplier_data_row($supplier));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest()
	{
		$suggestions = $this->xss_clean($this->Supplier->get_search_suggestions($this->input->get('term'), TRUE));

		echo json_encode($suggestions);
	}

	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Supplier->get_search_suggestions($this->input->post('term'), FALSE));

		echo json_encode($suggestions);
	}
	
	/*
	Loads the supplier edit form
	*/
	public function view($supplier_id = -1)
	{
		$info = $this->Supplier->get_info($supplier_id);
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}
		$data['person_info'] = $info;

		$growing_area = array('-1' => $this->lang->line('suppliers_none'));
		foreach($this->Growing_area->get_all()->result_array() as $row)
		{
			$growing_area[$this->xss_clean($row['growing_area_id'])] = $this->xss_clean($row['name']);
		}
		$data['growing_area'] = $growing_area;
		$data['selected_growing_area'] = $info->growing_area_id;

		$this->load->view("suppliers/form", $data);
	}
	
	/*
	Inserts/updates a supplier
	*/
	public function save($supplier_id = -1)
	{

		$this->Supplier->set_log("<< Start Log >>");

		$this->Supplier->set_log("ID: ".$supplier_id);

		$first_name = $this->xss_clean($this->input->post('first_name'));
		$last_name = $this->xss_clean($this->input->post('last_name'));
		$email = $this->xss_clean(strtolower($this->input->post('email')));

		// format first and last name properly
		$first_name = $this->nameize($first_name);
		$last_name = $this->nameize($last_name);

		$person_data = array(
			'first_name' => $first_name,
			'last_name' => $last_name,
			'dni' => $this->input->post('dni'),
			'gender' => $this->input->post('gender'),
			'email' => $email,
			'phone_number' => $this->input->post('phone_number'),
			'address_1' => $this->input->post('address_1'),
			'address_2' => $this->input->post('address_2'),
			'city' => $this->input->post('city'),
			'state' => $this->input->post('state'),
			'zip' => $this->input->post('zip'),
			'country' => $this->input->post('country'),
			'comments' => $this->input->post('comments')
		);

		$association_date = $this->input->post('association_date');
		$association_date_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $association_date);

		$supplier_data = array(
			'ruc' => (!empty($this->input->post('ruc')) ? $this->input->post('ruc') : NULL),
			'company_name' => $this->input->post('company_name'),
			'agency_name' => $this->input->post('agency_name'),
			'account_number' => $this->input->post('account_number') == '' ? NULL : $this->input->post('account_number'),
			'ispartner' => $this->input->post('ispartner') != NULL,
			'growing_area_id' => ($this->input->post('growing_area_id')=="-1" ? NULL : $this->input->post('growing_area_id')),
			'association_date' => $association_date_formatter->format('Y-m-d H:i:s'),
		);

		if($this->Supplier->save_supplier($person_data, $supplier_data, $supplier_id))
		{
			$supplier_data = $this->xss_clean($supplier_data);
			$this->Supplier->set_log("<< End Log >>");
			//New supplier
			if($supplier_id == -1)
			{
				echo json_encode(array('success' => TRUE,
								'message' => $this->lang->line('suppliers_successful_adding') . ' ' . $supplier_data['company_name'],
								'id' => $supplier_data['person_id']));
			}
			else //Existing supplier
			{
				echo json_encode(array('success' => TRUE,
								'message' => $this->lang->line('suppliers_successful_updating') . ' ' . $supplier_data['company_name'],
								'id' => $supplier_id));
			}
		}
		else//failure
		{
			$this->Supplier->set_log("<< End Log >>");
			//	Use get_log method for debugg
			// --> $this->Supplier->get_log().'<br>'.
			$supplier_data = $this->xss_clean($supplier_data);

			echo json_encode(array('success' => FALSE,
							'message' => $this->lang->line('suppliers_error_adding_updating') . ' ' . 	$supplier_data['company_name'],
							'id' => -1));
		}
	}
	
	/*
	This deletes suppliers from the suppliers table
	*/
	public function delete()
	{
		$suppliers_to_delete = $this->xss_clean($this->input->post('ids'));

		if($this->Supplier->delete_list($suppliers_to_delete))
		{
			echo json_encode(array('success' => TRUE,'message' => $this->lang->line('suppliers_successful_deleted').' '.
							count($suppliers_to_delete).' '.$this->lang->line('suppliers_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE,'message' => $this->lang->line('suppliers_cannot_be_deleted')));
		}
	}

	/*
	Suppliers import from excel spreadsheet
	*/
	public function excel()
	{
		$name = 'import_suppliers.csv';
		$data = file_get_contents('../' . $name);
		force_download($name, $data);
	}

	public function excel_import()
	{
		$this->load->view('suppliers/form_excel_import', NULL);
	}

	public function do_excel_import()
	{
		if($_FILES['file_path']['error'] != UPLOAD_ERR_OK)
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('suppliers_excel_import_failed')));
		}
		else
		{
			if(($handle = fopen($_FILES['file_path']['tmp_name'], 'r')) !== FALSE)
			{
				// Skip the first row as it's the table description
				fgetcsv($handle);
				$i = 1;

				$failCodes = array();

				while(($data = fgetcsv($handle)) !== FALSE)
				{
					// XSS file data sanity check
					$data = $this->xss_clean($data);

					if(sizeof($data) >= 19)
					{
						$email = strtolower($data[4]);
						$person_data = array(
							'dni'		=> $data[0],
							'first_name'	=> $data[1],
							'last_name'		=> $data[2],
							'gender'		=> $data[3],
							'email'			=> $email,
							'phone_number'	=> $data[5],
							'address_1'		=> $data[6],
							'address_2'		=> $data[7],
							'city'			=> $data[8],
							'state'			=> $data[9],
							'zip'			=> $data[10],
							'country'		=> $data[11],
							'comments'		=> $data[12]
						);

						$ispartner = $data[17];

						$supplier_data = array(
							'ruc'				=> $data[13],
							'company_name'		=> $data[14],
							'agency_name'		=> $data[15],
							'account_number'	=> ($data[16] == '' ? NULL : $data[16]),
							'ispartner'			=> $ispartner
						);

						if($ispartner==1)
						{
							$growing_area = $this->Growing_area->get_info_by_name(strtoupper($data[18]));
							if(!empty($growing_area->growing_area_id))
							{
								$supplier_data['growing_area_id'] = $growing_area->growing_area_id;
							}
						}

					}
					else
					{
						$invalidated = TRUE;
					}

					if($invalidated)
					{
						$failCodes[] = $i;
					}
					elseif($this->Supplier->save_supplier($person_data, $supplier_data))
					{
						// Save Success
					}
					else
					{
						$failCodes[] = $i;
					}

					++$i;
				}

				if(count($failCodes) > 0)
				{
					$message = $this->lang->line('suppliers_excel_import_partially_failed') . ' (' . count($failCodes) . '): ' . implode(', ', $failCodes);

					echo json_encode(array('success' => FALSE, 'message' => $message));
				}
				else
				{
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('suppliers_excel_import_success')));
				}
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('suppliers_excel_import_nodata_wrongformat')));
			}
		}
	}
	
}
?>
