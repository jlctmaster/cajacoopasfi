<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Voucher_operations extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('voucher_operations');
	}

	public function index()
	{
		 $data['table_headers'] = $this->xss_clean(get_voucher_operation_manage_table_headers());

		 $data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		 $this->load->view('voucher_operations/manage', $data);
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$voucher_operations = $this->Voucher_operation->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Voucher_operation->get_found_rows($search);

		$data_rows = array();
		foreach($voucher_operations->result() as $voucher_operation)
		{
			$data_rows[] = $this->xss_clean(get_voucher_operation_data_row($voucher_operation));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_voucher_operation_data_row($this->Voucher_operation->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($voucher_operation_id = -1)
	{
		$data['voucher_operation_info'] = $this->Voucher_operation->get_info($voucher_operation_id);

		if(empty($data['voucher_operation_info']->voucherdate))
		{
			$data['voucher_operation_info']->voucherdate = date('Y-m-d');
		}

		$data['quality_certificates_from'] = $this->Quality_certificate->get_certificates_by_person($data['voucher_operation_info']->person_id,$data['voucher_operation_info']->serieno);
		$data['quality_certificates_to'] = $this->Quality_certificate->get_certificates_allocated($voucher_operation_id);

		$this->load->view("voucher_operations/form", $data);
	}

	public function view_detail($voucher_operation_id)
	{
		$data['voucher_operation_info'] = $this->Voucher_operation->get_info($voucher_operation_id);

		$data['table_headers'] = $this->xss_clean(get_certificate_manage_table_headers($data['voucher_operation_info']->serieno,$voucher_operation_id));

		$this->load->view('voucher_operations/manage_detail', $data);
	}

	public function payment($voucher_operation_id = -1)
	{
		$data['voucher_operation_info'] = $this->Voucher_operation->get_info($voucher_operation_id);

		if(empty($data['voucher_operation_info']->liquidatedate))
		{
			$data['voucher_operation_info']->liquidatedate = date('Y-m-d');
			$data['voucher_operation_info']->user_id = $this->User->get_logged_in_user_info()->person_id;
		}

		$user_info = $this->User->get_info($data['voucher_operation_info']->user_id);
		$data['user'] = $user_info->first_name . ' ' . $user_info->last_name;

		$data['documents_allocates_info'] = $this->Voucher_operation->get_documents_allocated($voucher_operation_id,$data['voucher_operation_info']->person_id);

		$data['quality_certificates_info'] = $this->Quality_certificate->get_certificates_allocated($voucher_operation_id);

		$data['cashups'] = $this->Cashup->get_cashup_employee_daily($this->User->get_logged_in_user_info()->person_id,date("Y-m-d"));

		$this->load->view("voucher_operations/payment", $data);
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_detail($voucher_operation_id)
	{
		$data['voucher_operation_info'] = $this->Voucher_operation->get_info($voucher_operation_id);
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$quality_certificates = $this->Quality_certificate->search_allocated($voucher_operation_id,$search, $limit, $offset, $sort, $order);
		$total_rows = $this->Quality_certificate->get_allocated_found_rows($voucher_operation_id,$search);

		$data_rows = array();
		foreach($quality_certificates->result() as $quality_certificate)
		{
			$data_rows[] = $this->xss_clean(get_certificate_data_row($data['voucher_operation_info']->serieno,$quality_certificate,$voucher_operation_id));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}


	public function save($voucher_operation_id = -1)
	{

		$this->Voucher_operation->set_log("<< START LOG >>");

		$voucherdate = $this->input->post('voucherdate');
		$voucherdate_formatter = date_create_from_format($this->config->item('dateformat'), $voucherdate);

		$voucher_operation_data = array(
			'serieno' => $this->input->post('serieno'),
			'voucher_operation_number' => $this->input->post('voucher_operation_number'),
			'voucherdate' => $voucherdate_formatter->format('Y-m-d'),
			'person_id' => $this->input->post('person_id'),
			'amount' => $this->input->post('amount')
		);

		$quality_certificates_data = $this->input->post('quality_certificates');

		if($this->Voucher_operation->save($voucher_operation_data, $quality_certificates_data, $voucher_operation_id))
		{
			$voucher_operation_data = $this->xss_clean($voucher_operation_data);
			$this->Voucher_operation->set_log("<< END LOG >>");
			// New voucher_operation_id
			if($voucher_operation_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('voucher_operations_successful_adding'), 'id' => $voucher_operation_data['voucher_operation_id']));
			}
			else // Existing Voucher_operation
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('voucher_operations_successful_updating'), 'id' => $voucher_operation_id));
			}
		}
		else//failure
		{
			$this->Voucher_operation->set_log("<< END LOG >>");
			// $this->Voucher_operation->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('voucher_operations_error_adding_updating') . ' ' . $voucher_operation_data['voucher_operation_number'], 'id' => -1));
		}
	}

	public function save_payment($voucher_operation_id = -1)
	{

		$this->Voucher_operation->set_log("<< START LOG >>");

		$liquidatedate = $this->input->post('liquidatedate');
		$liquidatedate_formatter = date_create_from_format($this->config->item('dateformat'), $liquidatedate);

		$voucher_operation_data = array(
			'cash_book_id' => $this->input->post('cash_book_id'),
			'liquidatedate' => $liquidatedate_formatter->format('Y-m-d'),
			'printed' => $this->input->post('printed') != NULL,
			'state' => 1
		);

		if($this->input->post('serieno')=="01")
		{
			$cash_concept = $this->Cash_concept->get_info_by_code('00-02-03');			
		}
		else
		{
			$cash_concept = $this->Cash_concept->get_info_by_code('00-02-04');
		}


		$cash_daily_data[] = array(
			'cash_concept_id' => $cash_concept->cash_concept_id,
			'cashup_id' => $this->input->post('cashup_id'),
			'cash_book_id' => $this->input->post('cash_book_id'),
			'operation_type' => ($cash_concept->concept_type==1 ? 1 : ($cash_concept->concept_type==2 ? 2 : 3)),
			'movementdate' => date('Y-m-d H:i:s'),
			'description' => $this->lang->line('voucher_operations_liquidate'),
			'isbankmovement' => ($this->input->post('movementtype') == "B" ? 1 : 0),
			'currency' => CURRENCY,
			'amount' => parse_decimals($this->input->post('total_amount')),
			'table_reference' => 'voucher_operations'
		);

		if($this->Voucher_operation->update($voucher_operation_data, $cash_daily_data, $voucher_operation_id))
		{
			$voucher_operation_data = $this->xss_clean($voucher_operation_data);
			$this->Voucher_operation->set_log("<< END LOG >>");
			// New voucher_operation_id
			if($voucher_operation_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('voucher_operations_successful_adding'), 'id' => $voucher_operation_data['voucher_operation_id']));
			}
			else // Existing Voucher_operation
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('voucher_operations_successful_updating'), 'id' => $voucher_operation_id));
			}
		}
		else//failure
		{
			$this->Voucher_operation->set_log($this->db->last_query());
			$this->Voucher_operation->set_log("<< END LOG >>");
			// $this->Voucher_operation->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('voucher_operations_error_adding_updating') . ' ' . $voucher_operation_data['voucher_operation_number'], 'id' => -1));
		}
	}

	public function confirm_delete($voucher_operation_id)
	{
		$data['voucher_operation_info'] = $this->Voucher_operation->get_info($voucher_operation_id);

		$this->load->view("voucher_operations/delete", $data);
	}

	public function delete()
	{
		$voucher_operation_to_delete = $this->input->post('ids');

		if($this->input->post('confirm')=="Y")
		{
			if($this->Voucher_operation->delete_list($voucher_operation_to_delete))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('voucher_operations_successful_deleted') . ' ' . count($voucher_operation_to_delete) . ' ' . $this->lang->line('voucher_operations_one_or_multiple')));
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('voucher_operations_cannot_be_deleted')));
			}
		}
		else
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('voucher_operations_none_be_deleted')));
		}
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_partner()
	{
		$suggestions = $this->xss_clean($this->Supplier->get_search_suggestions($this->input->get('term'),TRUE));

		echo json_encode($suggestions);
	}

	public function get_qualities_certificates($person_id,$serieno)
	{
		$certificates = $this->xss_clean($this->Quality_certificate->get_certificates_by_person($person_id,$serieno));

		echo json_encode($certificates);
	}

	//	Import Certificates 01
	public function import_view()
	{
		 $data['table_headers'] = $this->xss_clean(get_certificate_manage_table_headers("02"));

		 $data['serieno'] = "01";

		 $this->load->view('voucher_operations/manage_certificate', $data);
	}

	public function import_certificate($voucher_id)
	{
		$import_data = $this->Quality_certificate->get_import_data();

		$certificate_data = array();
		$fail_data = array();
		$count_certificate_data = 0;

		foreach($import_data as $row)
		{
			$person = $this->Person->get_info_by_dni($row['dni']);
			if(!empty($person->person_id) && $person->person_id > 0)
			{
				if(!$this->Quality_certificate->certificate_reference_exists($row['reference_id']))
				{
					$objData = new StdClass;

					$objData->depositdate = $row['depositdate'];
					$objData->serieno = $row['serieno'];
					$objData->certificate_number = $row['certificate_number'];
					$objData->person_id = $person->person_id;
					$objData->kg_dry = $row['kg_dry'];
					$objData->qq_dry = $row['qq_dry'];
					$objData->rate_profile = $row['rate_profile'];
					$objData->physical_performance = $row['physical_performance'];
					$objData->quality = $row['quality'];
					$objData->location_id = $row['location_id'];
					$objData->price = $row['price'];
					$objData->amount = $row['amount'];
					$objData->reference_id = $row['reference_id'];
					$objData->imported = $row['imported'];

					array_push($certificate_data, $objData);
					$count_certificate_data++;
				}
				else
				{
					continue;
				}
			}
			else
			{
				$obj = new StdClass;

				$obj->depositdate = $row['depositdate'];
				$obj->serieno = $row['serieno'];
				$obj->certificate_number = $row['certificate_number'];
				$obj->dni = $row['dni'];
				$obj->name = $row['name'];
				$obj->kg_dry = $row['kg_dry'];
				$obj->qq_dry = $row['qq_dry'];
				$obj->rate_profile = $row['rate_profile'];
				$obj->physical_performance = $row['physical_performance'];
				$obj->quality = $row['quality'];
				$obj->location_id = $row['location_id'];
				$obj->price = $row['price'];
				$obj->amount = $row['amount'];
				$obj->reference_id = $row['reference_id'];
				$obj->imported = $row['imported'];

				array_push($fail_data,$obj);
			}
		}

		$data['failed_data'] = $fail_data;
		$data['row_sended'] = $count_certificate_data;
		$data['data_sended'] = $certificate_data;

		$this->load->view("voucher_operations/import", $data);
	}

	public function save_import()
	{
		if($this->input->post('confirm')=='Y'){

			$success = TRUE;

			$depositdate = $this->input->post('depositdate');
			$serieno = $this->input->post('serieno');
			$certificate_number = $this->input->post('certificate_number');
			$person_id = $this->input->post('person_id');
			$kg_dry = $this->input->post('kg_dry');
			$qq_dry = $this->input->post('qq_dry');
			$rate_profile = $this->input->post('rate_profile');
			$physical_performance = $this->input->post('physical_performance');
			$quality = $this->input->post('quality');
			$location_id = $this->input->post('location_id');
			$price = $this->input->post('price');
			$amount = $this->input->post('amount');
			$reference_id = $this->input->post('reference_id');
			$imported = $this->input->post('imported');

			$row_inserted = 0;

			for($x=0;$x<count($certificate_number);$x++)
			{
				/*array_push($certificate_data, array(
					'depositdate' => $depositdate[$x],
					'serieno' => $serieno[$x],
					'certificate_number' => $certificate_number[$x],
					'person_id' => $person_id[$x],
					'kg_dry' => $kg_dry[$x],
					'qq_dry' => $qq_dry[$x],
					'rate_profile' => $rate_profile[$x],
					'physical_performance' => $physical_performance[$x],
					'quality' => $quality[$x],
					'location_id' => $location_id[$x],
					'price' => $price[$x],
					'amount' => $amount[$x],
					'reference_id' => $reference_id[$x],
					'imported' => $imported[$x]
				));*/

				$quality_certificate_data = array(
					'depositdate' => $depositdate[$x],
					'serieno' => $serieno[$x],
					'certificate_number' => $certificate_number[$x],
					'person_id' => $person_id[$x],
					'kg_dry' => $kg_dry[$x],
					'qq_dry' => $qq_dry[$x],
					'rate_profile' => $rate_profile[$x],
					'physical_performance' => $physical_performance[$x],
					'quality' => $quality[$x],
					'location_id' => $location_id[$x],
					'price' => $price[$x],
					'amount' => $amount[$x],
					'reference_id' => $reference_id[$x],
					'imported' => $imported[$x]
				);

				if($this->Quality_certificate->save($quality_certificate_data, $quality_certificate_id))
				{
					$row_inserted++;
				}
			}
			
			//$row_inserted = $this->Quality_certificate->save_batch($certificate_data);

			if($row_inserted==0)
			{
				$success = FALSE;
			}

			$message .= "<br>".$this->lang->line('quality_certificates_successful_imported').": ".$row_inserted;
			$message .= "<br>".$this->lang->line('quality_certificates_unsuccessful_imported').": ".($x-$row_inserted);
			//$message .= "<br>".$this->db->last_query();

			echo json_encode(array('success' => $success, 'message' => $message, 'id' => -1));
		}
		else{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('quality_certificates_none_to_be_imported')));
		}
	}

	//	Register Certificates 02

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_quality()
	{
		$suggestions = $this->xss_clean($this->Quality_certificate->get_quality_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}

	public function register_view()
	{
		 $data['table_headers'] = $this->xss_clean(get_certificate_manage_table_headers("02"));

		 $data['serieno'] = "02";

		 $this->load->view('voucher_operations/manage_certificate', $data);
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_certificate($serieno)
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$quality_certificates = $this->Quality_certificate->search($serieno,$search, $limit, $offset, $sort, $order);
		$total_rows = $this->Quality_certificate->get_found_rows($serieno,$search);

		$data_rows = array();
		foreach($quality_certificates->result() as $quality_certificate)
		{
			$data_rows[] = $this->xss_clean(get_certificate_data_row($serieno,$quality_certificate));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function view_certificate($quality_certificate_id = -1)
	{
		$data['quality_certificate_info'] = $this->Quality_certificate->get_info($quality_certificate_id);

		if(empty($data['quality_certificate_info']->depositdate))
		{
			$data['quality_certificate_info']->depositdate = date('Y-m-d');
		}

		if(empty($data['quality_certificate_info']->serieno))
		{
			$data['quality_certificate_info']->serieno = "02";
		}

		if(empty($data['quality_certificate_info']->kg_dry))
		{
			$data['quality_certificate_info']->kg_dry = 0;
		}

		if(empty($data['quality_certificate_info']->qq_dry))
		{
			$data['quality_certificate_info']->qq_dry = 0;
		}

		if(empty($data['quality_certificate_info']->price))
		{
			$data['quality_certificate_info']->price = 0;
		}

		if(empty($data['quality_certificate_info']->amount))
		{
			$data['quality_certificate_info']->amount = 0;
		}

		$stock_location = array('-1' => $this->lang->line('common_none_selected_text'));
		foreach($this->Stock_location->get_all()->result_array() as $row)
		{
			$stock_location[$this->xss_clean($row['location_id'])] = $this->xss_clean($row['location_name']);
		}
		$data['locations'] = $stock_location;
		$data['selected_location_id'] = (empty($data['quality_certificate_info']->location_id) ? -1 : $data['quality_certificate_info']->location_id);

		$this->load->view("voucher_operations/form_certificate", $data);
	}

	public function save_certificate($quality_certificate_id = -1)
	{

		$this->Quality_certificate->set_log('<< START LOG >>');

		$depositdate = $this->input->post('depositdate');
		$depositdate_formatter = date_create_from_format($this->config->item('dateformat'), $depositdate);

		$quality_certificate_data = array(
			'depositdate' => $depositdate_formatter->format('Y-m-d'),
			'serieno' => $this->input->post('serieno'),
			'certificate_number' => $this->input->post('certificate_number'),
			'person_id' => $this->input->post('person_id'),
			'kg_dry' => $this->input->post('kg_dry'),
			'qq_dry' => $this->input->post('qq_dry'),
			'rate_profile' => $this->input->post('rate_profile'),
			'physical_performance' => $this->input->post('physical_performance'),
			'quality' => strtoupper($this->input->post('quality')),
			'location_id' => $this->input->post('location_id'),
			'price' => $this->input->post('price'),
			'amount' => $this->input->post('amount'),
			'imported' => 0
		);

		if($this->Quality_certificate->save($quality_certificate_data, $quality_certificate_id))
		{
			$quality_certificate_data = $this->xss_clean($quality_certificate_data);

			// New voucher_operation_id
			if($quality_certificate_id == -1)
			{
				$this->Quality_certificate->set_log('<< END LOG >>');
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('quality_certificates_successful_adding'), 'id' => $quality_certificate_data['quality_certificate_id']));
			}
			else // Existing Voucher_operation
			{
				$this->Quality_certificate->set_log($this->db->last_query());
				$this->Quality_certificate->set_log('<< END LOG >>');
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('quality_certificates_successful_updating'), 'id' => $quality_certificate_id));
			}
		}
		else//failure
		{
			$this->Quality_certificate->set_log('<< END LOG >>');
			//	$this->Quality_certificate->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('quality_certificates_error_adding_updating') . ' ' . $quality_certificate_data['certificate_number'], 'id' => -1));
		}
	}
}
?>
