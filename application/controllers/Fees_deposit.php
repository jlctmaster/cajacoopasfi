<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Fees_deposit extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('fees_deposit');
	}

	public function index()
	{
		
		$data['table_headers'] = $this->xss_clean(get_fees_deposit_manage_table_headers());
         
		//print_r($data);exit();		 
		$this->load->view('fees_deposit/manage', $data);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_supplier()
	{
		$suggestions = $this->xss_clean($this->Fee_deposit->get_supplier_suggestions($this->input->get('term')));
                
                //print_r($suggestions);exit();
                
		echo json_encode($suggestions);
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
		
				
		$models = $this->Fee_deposit->search($search, $limit, $offset, $sort, $order);
		//print_r($models);exit();
		
		$total_rows = $this->Fee_deposit->get_found_rows($search);
		//print_r($models);exit();
		$data_rows = array();
		foreach($models->result() as $model)
		{
			$data_rows[] = $this->xss_clean(get_fees_deposit_data_row($model));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_model_data_row($this->Model->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($id = -1)
	{
            $data['fee_deposit_info'] = $this->Fee_deposit->get_info($id);
                
            //periods    
            $period = array('-1' => $this->lang->line('common_none_selected_text'));
            $all_row = $this->Period->get_all()->result_array();
            //print_r($all_row);exit();
            for($i = 0;$i <count($all_row);$i++)
            {
                    $period[$all_row[$i]['id']] = $all_row[$i]['name'];
            }
                            //print_r($data);exit();

            $data['period'] = $period;
            $data['selected_period'] = $data['fee_deposit_info']->period;

            //doposit
            $locations = array('-1' => $this->lang->line('common_none_selected_text'));
            $all_row = $this->Stock_location->get_all()->result_array();
            for($i = 0;$i <count($all_row);$i++)
            {
                    $locations[$all_row[$i]['location_id']] = $all_row[$i]['location_name'];
            }
            $data['locations'] = $locations;
            $data['selected_location']= $data['fee_deposit_info']->location_id;


            $type_item = array('-1' => $this->lang->line('common_none_selected_text'));
            $all_row = $this->Item_type->get_all()->result_array();			
            //print_r($all_row);exit();			
            for($i = 0;$i <count($all_row);$i++)
            {
                    $type_item[$all_row[$i]['item_type_id']] = $all_row[$i]['name'];
            }			
            $data['item_type'] = $type_item;
            $data['selected_item_type']= $data['fee_deposit_info']->type_item_id;

            $item = array('-1' => $this->lang->line('common_none_selected_text'));
            $all_row = $this->Item->get_all()->result_array();

            //print_r($all_row);exit();

            for($i = 0;$i <count($all_row);$i++)
            {
                    $item[$all_row[$i]['item_id']] = $all_row[$i]['name'];
            }			
            $data['item'] = $item;
            $data['selected_item'] = $data['fee_deposit_info']->item_id;

            $this->load->view("fees_deposit/form", $data);
	}

	public function save($model_id = -1)
	{
		//print_r($_POST);exit();
                $model_data = array(
                        
			'supplier_id' => $this->input->post('supplier_id'),
			'period'      => $this->input->post('period'),
			'item_id'   => $this->input->post('item_id'),
                        'type_item_id'     => $this->input->post('type_item_id'),
                        'created'     => date('Y-m-d H:i:s'),
                        'input' => '0',
                        'fee_deposit'=> $this->input->post('fee_deposit'),
                        'output'=> '0',
                        'status'=>'1',
                        'location_id' => $this->input->post('location'),
                        'delete'=>'0'
		);
                
                //print_r($model_data);exit();
		if($this->Fee_deposit->save($model_data, $model_id))
		{
			$model_data = $this->xss_clean($model_data);

			// New id
			if($model_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('fees_deposit_successful_adding'), 'id' => $model_data['id_fee_deposit']));
			}
			else // Existing Model
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('fees_deposit_successful_updating'), 'id' => $model_id));
			}
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('fees_deposit_error_adding_updating') . ' ' . $model_data['supplier_id'], 'id' => -1));
		}
	}

	public function delete()
	{
                print_r($_POST);exit();
		$model_to_delete = $this->input->post('ids');

		if($this->Fee_deposit->delete_list($model_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('fees_deposit_successful_deleted') . ' ' . count($model_to_delete) . ' ' . $this->lang->line('fees_deposit_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('models_cannot_be_deleted')));
		}
	}
}
?>
