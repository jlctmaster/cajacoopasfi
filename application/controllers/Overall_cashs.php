<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Overall_cashs extends Secure_Controller
{

	public function __construct()
	{
		parent::__construct('overall_cashs');
	}

	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_overall_cash_manage_table_headers());

		$data['overall_cash_openend'] = $this->Overall_cash->exists_opened(date('Y-m-d'));
		//	Closed all opened overall cash distinct to today
		//$this->Overall_cash->closed_all(date('Y-m-d'));

		$this->load->view('overall_cashs/manage', $data);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function get_subconcept($cash_concept_id)
	{
		$suggestions = $this->xss_clean($this->Cash_concept->get_parent_all($cash_concept_id,1)->result_array());

		echo json_encode($suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_person()
	{
		$suggestions = $this->xss_clean($this->Person->get_search_person_suggestions($this->input->get('term'),TRUE));

		echo json_encode($suggestions);
	}

	/*
	Returns cost_category_manage table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = (!empty($this->input->get('sort')) ? $this->input->get('sort') : 'overall_cashs.opendate');
		$order  = (!empty($this->input->get('order')) ? $this->input->get('order') : 'DESC');

		$overall_cashs = $this->Overall_cash->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Overall_cash->get_found_rows($search);

		$data_rows = array();
		foreach($overall_cashs->result() as $overall_cash)
		{
			$data_rows[] = $this->xss_clean(get_overall_cash_data_row($overall_cash));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_overall_cash_data_row($this->Overall_cash->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($overall_cash_id = -1)
	{
		$data['overall_cash_info'] = $this->Overall_cash->get_info($overall_cash_id);

		if(empty($data['overall_cash_info']->opendate))
		{
			$data['overall_cash_info']->opendate = date('Y-m-d H:i:s');
		}
		if(empty($data['overall_cash_info']->startbalance))
		{
			$last_overall_cash = $this->Overall_cash->last_balance(date('Y-m-d'));
			$data['overall_cash_info']->startbalance = (!empty($last_overall_cash->endingbalance) ? $last_overall_cash->endingbalance : 0);
			$data['overall_cash_info']->usdstartbalance = (!empty($last_overall_cash->usdendingbalance) ? $last_overall_cash->usdendingbalance : 0);
		}

		$this->load->view("overall_cashs/form", $data);
	}

	public function close($overall_cash_id)
	{
		$data['overall_cash_info'] = $this->Overall_cash->get_endingbalance($overall_cash_id);

		$data['overall_cash_info']->closedate = date('Y-m-d H:i:s');

		$this->load->view("overall_cashs/close", $data);
	}

	public function detail($overall_cash_id)
	{
		$data['overall_cash_summary'] = $this->Overall_cash->get_endingbalance($overall_cash_id);

		$data['cash_book_info'] = $this->Cash_book->get_info_overall_cash();

		$data['denomination_currency'] = $this->Overall_cash->get_denominations($overall_cash_id,CURRENCY);
		$data['denomination_usd'] = $this->Overall_cash->get_denominations($overall_cash_id,USDCURRENCY);

		$this->load->view('overall_cashs/manage_detail', $data);
	}

	public function detail_income($overall_cash_id,$currency = 'all')
	{
		$data['table_headers'] = $this->xss_clean(get_cash_flow_manage_table_headers());

		$data['overall_cash_summary'] = $this->Overall_cash->get_endingbalance($overall_cash_id);

		$data['operation_types'] = array('all' => $this->lang->line('common_none_selected_text'), '1' => "INGRESO", '0' => "EGRESO");

		$data['operation_type'] = 1;

		$data['currencies'] = array('all' => $this->lang->line('common_none_selected_text'), CURRENCY => CURRENCY_LABEL, USDCURRENCY => USDCURRENCY_LABEL);

		$data['currency'] = $currency;

		$this->load->view('overall_cashs/manage_detailed', $data);
	}

	public function detail_cost($overall_cash_id,$currency = 'all')
	{
		$data['table_headers'] = $this->xss_clean(get_cash_flow_manage_table_headers());

		$data['overall_cash_summary'] = $this->Overall_cash->get_endingbalance($overall_cash_id);

		$data['operation_types'] = array('all' => $this->lang->line('common_none_selected_text'), '1' => "INGRESO", '0' => "EGRESO");

		$data['operation_type'] = 0;

		$data['currencies'] = array('all' => $this->lang->line('common_none_selected_text'), CURRENCY => CURRENCY_LABEL, USDCURRENCY => USDCURRENCY_LABEL);

		$data['currency'] = $currency;

		$this->load->view('overall_cashs/manage_detailed', $data);
	}

	/*
	Returns cost_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_detail($overall_cash_id)
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = (!empty($this->input->get('sort')) ? $this->input->get('sort') : 'cash_flow.movementdate');
		$order  = (!empty($this->input->get('order')) ? $this->input->get('order') : 'ASC');

		$filters = array('operation_type' => $this->input->get('operation_type'),'currency' => $this->input->get('currency'));

		$cash_flows = $this->Cash_flow->search($overall_cash_id,$search,$filters, $limit, $offset, $sort, $order);
		$total_rows = $this->Cash_flow->get_found_rows($overall_cash_id,$search,$filters);

		$data_rows = array();
		foreach($cash_flows->result() as $cash_flow)
		{
			$data_rows[] = $this->xss_clean(get_cash_flow_data_row($cash_flow));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function print_report($overall_cash_id)
	{
		$data['overall_cash_summary'] = $this->Overall_cash->get_endingbalance($overall_cash_id);

		$data['cash_book_info'] = $this->Cash_book->get_info_overall_cash();

		$data['denomination_currency'] = $this->Overall_cash->get_denominations($overall_cash_id,CURRENCY);
		$data['denomination_usd'] = $this->Overall_cash->get_denominations($overall_cash_id,USDCURRENCY);

		//$this->load->view('overall_cashs/report_cash_register', $data);

		$html = $this->load->view('overall_cashs/report_cash_register', $data, TRUE);
		
		// Cargamos la librería
		$this->load->library('pdfgenerator_lib');
		// definamos un nombre para el archivo. No es necesario agregar la extension .pdf
		$filename = 'arqueo_de_caja_general';
		// generamos el PDF. Pasemos por encima de la configuración general y definamos otro tipo de papel
		$this->pdfgenerator_lib->generate($html, $filename, true, 'Letter', 'portrait');
	}

	public function save($overall_cash_id = -1)
	{
		$this->Overall_cash->set_log("<< Start Log >>");

		$opendate = $this->input->post('opendate');
		$opendate_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $opendate);

		$overall_cash_data = array(
			'opendate' => $opendate_formatter->format('Y-m-d H:i:s'),
			'startbalance' => $this->input->post('startbalance'),
			'openingbalance' => $this->input->post('openingbalance'),
			'usdstartbalance' => $this->input->post('usdstartbalance'),
			'usdopeningbalance' => $this->input->post('usdopeningbalance')
		);

		$cash_concept = $this->Cash_concept->get_info_by_code('01-00');
		$cash_book = $this->Cash_book->get_info_overall_cash();

		if(!empty($cash_concept->cash_concept_id) && !empty($cash_book->cash_book_id))
		{
			$cash_flow_data[] = array(
				'cash_concept_id' => $cash_concept->cash_concept_id,
				'cash_book_id' => $cash_book->cash_book_id,
				'operation_type' => ($cash_concept->concept_type==1 ? 1 : 0),
				'movementdate' => date('Y-m-d H:i:s'),
				'description' => $this->lang->line('overall_cashs_cash_opening').' ('.CURRENCY_LABEL.')',
				'currency' => CURRENCY,
				'amount' => $this->input->post('openingbalance'),
				'table_reference' => 'overall_cashs'
			);

			if($this->input->post('usdopeningbalance')>0)
			{
				$cash_flow_data[] = array(
					'cash_concept_id' => $cash_concept->cash_concept_id,
					'cash_book_id' => $cash_book->cash_book_id,
					'operation_type' => ($cash_concept->concept_type==1 ? 1 : 0),
					'movementdate' => date('Y-m-d H:i:s'),
					'description' => $this->lang->line('overall_cashs_cash_opening').' ('.USDCURRENCY_LABEL.')',
					'currency' => USDCURRENCY,
					'amount' => $this->input->post('usdopeningbalance'),
					'table_reference' => 'overall_cashs'
				);
			}

			if($this->Overall_cash->opened($overall_cash_data, $cash_flow_data, $overall_cash_id))
			{
				$overall_cash_data = $this->xss_clean($overall_cash_data);
				// New overall_cash_id
				if($overall_cash_id == -1)
				{
					$this->Overall_cash->set_log("<< End Log >>");
					//	Use log 
					//	$this->Overall_cash->get_log()."<br>".
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('overall_cashs_successful_adding'), 'id' => $overall_cash_data['overall_cash_id']));
				}
				else // Existing Overall_cash
				{
					$this->Overall_cash->set_log($this->db->last_query());
					$this->Overall_cash->set_log("<< End Log >>");
					//	Use log 
					//	$this->Overall_cash->get_log()."<br>".
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('overall_cashs_successful_updating'), 'id' => $overall_cash_id));
				}
			}
			else//failure
			{
				if($overall_cash_id != -1)
				{
					$this->Overall_cash->set_log($this->db->last_query());
				}
				$this->Overall_cash->set_log("<< End Log >>");
				//	Use log 
				//	$this->Overall_cash->get_log()."<br>".
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('overall_cashs_error_adding_updating') . ' ' . $overall_cash_data['opendate'], 'id' => -1));
			}
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('overall_cashs_error_cash_concept_cash_book_notfound'), 'id' => -1));
		}
	}

	public function closed($overall_cash_id)
	{
		if($this->input->post('confirm') == "Y")
		{

			$closedate = $this->input->post('closedate');
			$closedate_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $closedate);

			$overall_cash_data = array(
				'closedate' => $closedate_formatter->format('Y-m-d H:i:s'),
				'endingbalance' => $this->input->post('endingbalance'),
				'usdendingbalance' => $this->input->post('usdendingbalance'),
				'state' => 1
			);
			//	For Currency
			for ($i=0; $i < count($this->input->post('denominations')); $i++) {
				$denominates_currency_data[] = array(
					'overall_cash_id' => $overall_cash_id,
					'currency' => CURRENCY,
					'denomination' => $this->input->post('denominations')[$i],
					'quantity' => $this->input->post('quantities')[$i],
					'amount' => $this->input->post('line_amounts')[$i]
				);
			}
			//	For USD
			for ($i=0; $i < count($this->input->post('usddenominations')); $i++) {
				$denominates_currency_data[] = array(
					'overall_cash_id' => $overall_cash_id,
					'currency' => USDCURRENCY,
					'denomination' => $this->input->post('usddenominations')[$i],
					'quantity' => $this->input->post('usdquantities')[$i],
					'amount' => $this->input->post('usdline_amounts')[$i]
				);
			}

			if($this->Overall_cash->closed($overall_cash_data, $denominates_currency_data, $overall_cash_id))
			{
				$overall_cash_data = $this->xss_clean($overall_cash_data);
				
				$this->Overall_cash->set_log($this->db->last_query());
				$this->Overall_cash->set_log("<< End Log >>");
				//	Use log 
				//	$this->Overall_cash->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('overall_cashs_successful_updating'), 'id' => $overall_cash_id));
			}
			else//failure
			{
				$this->Overall_cash->set_log("<< End Log >>");
				//	Use log 
				//	$this->Overall_cash->get_log()."<br>".
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('overall_cashs_error_adding_updating') . ' ' . $overall_cash_data['closedate'], 'id' => -1));
			}
		}
		else
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('overall_cashs_none_closed'), 'id' => $overall_cash_id));
		}
	}

	public function delete()
	{
		$overall_cash_to_delete = $this->input->post('ids');
		
		if($this->Overall_cash->delete_list($overall_cash_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('overall_cashs_successful_deleted') . ' ' . count($overall_cash_to_delete) . ' ' . $this->lang->line('overall_cashs_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('overall_cashs_cannot_be_deleted')));
		}
	}

	//	Bank Controller

	public function bank()
	{
		$data['table_headers'] = $this->xss_clean(get_bank_manage_table_headers());

		$this->load->view('overall_cashs/manage_bank', $data);
	}

	/*
	Returns cost_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_bank()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$banks = $this->Bank->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Bank->get_found_rows($search);

		$data_rows = array();
		foreach($banks->result() as $bank)
		{
			$data_rows[] = $this->xss_clean(get_bank_data_row($bank));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function view_bank($bank_id = -1)
	{
		$data['bank_info'] = $this->Bank->get_info($bank_id);
		$data['bankaccount_info'] = $this->Bank->get_accountinfo($bank_id);

		$this->load->view("overall_cashs/form_bank", $data);
	}

	public function save_bank($bank_id = -1)
	{
		$this->Bank->set_log("<< Start Log >>");

		$bank_data = array(
			'ruc' => $this->input->post('ruc'),
			'name' => $this->input->post('name')
		);

		for ($i=0; $i < count($this->input->post('accounts')); $i++) {
			$bankaccount_data[] = array(
				'currency' => $this->input->post('currencys')[$i],
				'account_number' => $this->input->post('accounts')[$i]
			);
		}

		if($this->Bank->save($bank_data, $bankaccount_data, $bank_id))
		{
			$bank_data = $this->xss_clean($bank_data);

			// New overall_cash_id
			if($bank_id == -1)
			{
				$this->Bank->set_log("<< End Log >>");
				//	Use log 
				//	$this->Bank->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('banks_successful_adding'), 'id' => $bank_data['bank_id']));
			}
			else // Existing Overall_cash
			{
				$this->Bank->set_log($this->db->last_query());
				$this->Bank->set_log("<< End Log >>");
				//	Use log 
				//	$this->Bank->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('banks_successful_updating'), 'id' => $bank_id));
			}
		}
		else//failure
		{
			if($bank_id != -1)
			{
				$this->Bank->set_log($this->db->last_query());
			}
			$this->Bank->set_log("<< End Log >>");
			//	Use log 
			//	$this->Bank->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('banks_error_adding_updating') . ' ' . $bank_data['name'], 'id' => -1));
		}
	}

	//	Income Controller

	public function income()
	{
		$data['table_headers'] = $this->xss_clean(get_income_manage_table_headers());

		$data['income_summary'] = $this->Income->get_summary_info();

		$this->load->view('overall_cashs/manage_income', $data);
	}

	/*
	Returns cost_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_income()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');
		$filters  = array(
					 'start_date' => $this->input->get('start_date'),
					 'end_date' => $this->input->get('end_date')
					);

		$incomes = $this->Income->search($search, $filters, 0, $limit, $offset, $sort, $order);
		$total_rows = $this->Income->get_found_rows($search,$filters, 0);

		$data_rows = array();
		foreach($incomes->result() as $income)
		{
			$data_rows[] = $this->xss_clean(get_income_data_row($income));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function view_income($income_id = -1)
	{
		$data['income_info'] = $this->Income->get_info($income_id);

		if(empty($data['income_info']->documentdate))
		{
			$data['income_info']->documentdate = date('Y-m-d H:i:s');
		}
		if(empty($data['income_info']->amount))
		{
			$data['income_info']->amount = 0;
		}

		$bankaccount = array('-1' => $this->lang->line('common_none_selected_text'));
		$bankaccount_cur = array();

		foreach($this->Bank->get_all_bankaccounts()->result_array() AS $bank)
		{
			$bankaccount[$this->xss_clean($bank['bankaccount_id'])] = $this->xss_clean($bank['name'].' '.$bank['account_number'].' ('.($bank['currency']==CURRENCY ? CURRENCY_LABEL : USDCURRENCY_LABEL).')');
			$bankaccount_cur[$this->xss_clean($bank['bankaccount_id'])] = $this->xss_clean($bank['currency']);
		}

		$data['bankaccounts'] = $bankaccount;
		$data['bankaccounts_cur'] = $bankaccount_cur;
		$data['selected_bankaccount'] = (!empty($data['income_info']->bankaccount_id) ? $data['income_info']->bankaccount_id : -1);

		$cash_concept = array('-1' => $this->lang->line('common_none_selected_text'));
		foreach($this->Cash_concept->get_all_summary(1)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}
		$data['cash_concepts'] = $cash_concept;
		$data['selected_cash_concept'] = $data['income_info']->cash_concept_id;

		if(!empty($data['income_info']->cash_subconcept_id))
		{
			$cash_subconcept = array('-1' => $this->lang->line('common_none_selected_text'));
			foreach ($this->Cash_concept->get_parent_all($data['income_info']->cash_concept_id,1)->result_array() as $row) {
				$cash_subconcept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
			}
			$data['cash_subconcepts'] = $cash_subconcept;
			$data['selected_cash_subconcept'] = $data['income_info']->cash_subconcept_id;
		}

		$this->load->view("overall_cashs/form_income", $data);
	}

	public function save_income($income_id = -1)
	{
		$this->Income->set_log("<< Start Log >>");

		$documentdate = $this->input->post('documentdate');
		$documentdate_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $documentdate);

		if($income_id != -1)
		{
			$documentno = $this->input->post('documentno');
		}
		else
		{
			$documentno = ($this->config->item('income_overallcash_number_automatic') == '0' ? $this->input->post('documentno') : $this->Appconfig->acquire_save_next_doctype_sequence($this->config->item('income_overallcash_doctype_sequence')));
		}

		$income_data = array(
			'documentno' => $documentno,
			'documentdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'person_id' => (!empty($this->input->post('person_id')) ? $this->input->post('person_id') : NULL),
			'person_name' => $this->input->post('person'),
			'bankaccount_id' => $this->input->post('bankaccount_id'),
			'cash_concept_id' => $this->input->post('cash_concept_id'),
			'cash_subconcept_id' => $this->input->post('cash_subconcept_id'),
			'detail' => $this->input->post('detail'),
			'movementtype' => $this->input->post('movementtype'),
			'trx_number' => $this->input->post('trx_number'),
			'currency' => $this->input->post('currency'),
			'amount' => $this->input->post('amount')
		);

		$cash_book = $this->Cash_book->get_info_overall_cash();
		$overall_cash_info = $this->Overall_cash->get_overall_cash_open_info(date('Y-m-d'));

		$cash_flow_data[] = array(
			'cash_concept_id' => $this->input->post('cash_subconcept_id'),
			'cash_book_id' => $cash_book->cash_book_id,
			'operation_type' => 1,
			'movementdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'description' => $this->input->post('detail'),
			'currency' => $this->input->post('currency'),
			'amount' => $this->input->post('amount'),
			'table_reference' => 'incomes',
			'overall_cash_id' => $overall_cash_info->overall_cash_id
		);

		if($this->Income->save($income_data, $cash_flow_data, $income_id))
		{
			$income_data = $this->xss_clean($income_data);

			// New overall_cash_id
			if($income_id == -1)
			{
				$this->Income->set_log("<< End Log >>");
				//	Use log 
				//	$this->Income->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('incomes_successful_adding'), 'id' => $income_data['income_id']));
			}
			else // Existing Overall_cash
			{
				$this->Income->set_log($this->db->last_query());
				$this->Income->set_log("<< End Log >>");
				//	Use log 
				//	$this->Income->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('incomes_successful_updating'), 'id' => $income_id));
			}
		}
		else//failure
		{
			if($income_id != -1)
			{
				$this->Income->set_log($this->db->last_query());
			}
			$this->Income->set_log("<< End Log >>");
			//	Use log 
			//	$this->Income->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('incomes_error_adding_updating') . ' ' . $income_data['documentno'], 'id' => -1));
		}
	}

	public function delete_income($income_id)
	{
		$data['income_info'] = $this->Income->get_info($income_id);

		$this->load->view("overall_cashs/delete_income", $data);
	}

	public function deleted_income()
	{

		if($this->input->post('confirm')=="Y")
		{
			$this->Income->set_log('<< START LOG >>');
			$income_to_delete = $this->input->post('ids');
			
			if($this->Income->delete($income_to_delete,$this->input->post('currency')))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('incomes_successful_deleted') . ' ' . count($income_to_delete) . ' ' . $this->lang->line('incomes_one_or_multiple')));
			}
			else
			{
				$this->Income->set_log('<< END LOG >>');
				echo json_encode(array('success' => FALSE, 'message' => $this->Income->get_log()."<br>".$this->lang->line('incomes_cannot_be_deleted')));
			}
		}
		else
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('incomes_none_to_be_deleted')));
		}
	}

	//	Cost Controller

	public function cost()
	{
		$data['table_headers'] = $this->xss_clean(get_cost_manage_table_headers());

		$data['cost_summary'] = $this->Cost->get_summary_info();

		$this->load->view('overall_cashs/manage_cost', $data);
	}

	/*
	Returns cost_category_manage table data rows. This will be called with AJAX.
	*/
	public function search_cost()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');
		$filters  = array(
					 'start_date' => $this->input->get('start_date'),
					 'end_date' => $this->input->get('end_date')
					);

		$costs = $this->Cost->search($search, $filters, 0, $limit, $offset, $sort, $order);
		$total_rows = $this->Cost->get_found_rows($search,$filters, 0);

		$data_rows = array();
		foreach($costs->result() as $cost)
		{
			$data_rows[] = $this->xss_clean(get_cost_data_row($cost));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function view_cost($cost_id = -1)
	{
		$data['cost_info'] = $this->Cost->get_info($cost_id);

		if(empty($data['cost_info']->documentdate))
		{
			$data['cost_info']->documentdate = date('Y-m-d H:i:s');
		}
		if(empty($data['cost_info']->amount))
		{
			$data['cost_info']->amount = 0;
		}

		$bankaccount = array('-1' => $this->lang->line('common_none_selected_text'));
		$bankaccount_cur = array();

		foreach($this->Bank->get_all_bankaccounts()->result_array() AS $bank)
		{
			$bankaccount[$this->xss_clean($bank['bankaccount_id'])] = $this->xss_clean($bank['name'].' '.$bank['account_number'].' ('.($bank['currency']==CURRENCY ? CURRENCY_LABEL : USDCURRENCY_LABEL).')');
			$bankaccount_cur[$this->xss_clean($bank['bankaccount_id'])] = $this->xss_clean($bank['currency']);
		}

		$data['bankaccounts'] = $bankaccount;
		$data['bankaccounts_cur'] = $bankaccount_cur;
		$data['selected_bankaccount'] = (!empty($data['cost_info']->bankaccount_id) ? $data['cost_info']->bankaccount_id : -1);

		$cash_concept = array('-1' => $this->lang->line('common_none_selected_text'));
		foreach($this->Cash_concept->get_all_summary(2)->result_array() as $row)
		{
			$cash_concept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
		}
		$data['cash_concepts'] = $cash_concept;
		$data['selected_cash_concept'] = $data['cost_info']->cash_concept_id;

		if(!empty($data['cost_info']->cash_subconcept_id))
		{
			$cash_subconcept = array('-1' => $this->lang->line('common_none_selected_text'));
			foreach ($this->Cash_concept->get_parent_all($data['cost_info']->cash_concept_id)->result_array() as $row) {
				$cash_subconcept[$this->xss_clean($row['cash_concept_id'])] = $this->xss_clean($row['name']);
			}
			$data['cash_subconcepts'] = $cash_subconcept;
			$data['selected_cash_subconcept'] = $data['cost_info']->cash_subconcept_id;
		}

		$data['overall_cash_info'] = $this->Overall_cash->get_endingbalance($this->Overall_cash->get_overall_cash_open_info(date('Y-m-d'))->overall_cash_id);

		$this->load->view("overall_cashs/form_cost", $data);
	}

	public function save_cost($cost_id = -1)
	{
		$this->Cost->set_log("<< Start Log >>");

		$documentdate = $this->input->post('documentdate');
		$documentdate_formatter = date_create_from_format($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), $documentdate);

		if($cost_id != -1)
		{
			$documentno = $this->input->post('documentno');
		}
		else
		{
			$documentno = ($this->config->item('cost_overallcash_number_automatic') == '0' ? $this->input->post('documentno') : $this->Appconfig->acquire_save_next_doctype_sequence($this->config->item('cost_overallcash_doctype_sequence')));
		}

		$cost_data = array(
			'documentno' => $documentno,
			'documentdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'person_id' => $this->input->post('person_id'),
			'bankaccount_id' => ($this->input->post('bankaccount_id') == -1 ? NULL : $this->input->post('bankaccount_id')),
			'cash_concept_id' => $this->input->post('cash_concept_id'),
			'cash_subconcept_id' => $this->input->post('cash_subconcept_id'),
			'detail' => $this->input->post('detail'),
			'movementtype' => $this->input->post('movementtype'),
			'trx_number' => $this->input->post('trx_number'),
			'currency' => $this->input->post('currency'),
			'amount' => $this->input->post('amount')
		);

		$cash_book = $this->Cash_book->get_info_overall_cash();

		$cash_flow_data[] = array(
			'cash_concept_id' => $this->input->post('cash_subconcept_id'),
			'cash_book_id' => $cash_book->cash_book_id,
			'operation_type' => 0,
			'movementdate' => $documentdate_formatter->format('Y-m-d H:i:s'),
			'description' => $this->input->post('detail'),
			'currency' => $this->input->post('currency'),
			'amount' => $this->input->post('amount'),
			'table_reference' => 'costs',
			'overall_cash_id' => $this->input->post('overall_cash_id')
		);

		if($this->Cost->save($cost_data, $cash_flow_data, $cost_id))
		{
			$cost_data = $this->xss_clean($cost_data);

			// New overall_cash_id
			if($cost_id == -1)
			{
				$this->Cost->set_log("<< End Log >>");
				//	Use log 
				//	$this->Cost->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('costs_successful_adding'), 'id' => $cost_data['cost_id']));
			}
			else // Existing Overall_cash
			{
				$this->Cost->set_log($this->db->last_query());
				$this->Cost->set_log("<< End Log >>");
				//	Use log 
				//	$this->Cost->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('costs_successful_updating'), 'id' => $cost_id));
			}
		}
		else//failure
		{
			if($cost_id != -1)
			{
				$this->Cost->set_log($this->db->last_query());
			}
			$this->Cost->set_log("<< End Log >>");
			//	Use log 
			//	$this->Cost->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->Cost->get_log()."<br>".$this->lang->line('costs_error_adding_updating') . ' ' . $cost_data['documentno'], 'id' => -1));
		}
	}

	public function delete_cost($cost_id)
	{
		$data['cost_info'] = $this->Cost->get_info($cost_id);

		$this->load->view("overall_cashs/delete_cost", $data);
	}

	public function deleted_cost()
	{

		if($this->input->post('confirm')=="Y")
		{
			$this->Cost->set_log('<< START LOG >>');
			$cost_to_delete = $this->input->post('ids');
			
			if($this->Cost->delete($cost_to_delete,$this->input->post('currency')))
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('costs_successful_deleted') . ' ' . count($cost_to_delete) . ' ' . $this->lang->line('costs_one_or_multiple')));
			}
			else
			{
				$this->Cost->set_log('<< END LOG >>');
				echo json_encode(array('success' => FALSE, 'message' => $this->Cost->get_log()."<br>".$this->lang->line('costs_cannot_be_deleted')));
			}
		}
		else
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('costs_none_to_be_deleted')));
		}
	}	

}
?>
