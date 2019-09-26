<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Delivery_documents extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('delivery_documents');
	}

	public function index()
	{
		 $data['table_headers'] = $this->xss_clean(get_delivery_document_manage_table_headers());
                 //$datos =$this->Delivery_document->get_all();   
                 //print_r($datos); 
		 $this->load->view('delivery_documents/manage', $data);
                 
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_period()
	{
		$suggestions = $this->xss_clean($this->Delivery_document->get_type_suggestions($this->input->get('term')));

		echo json_encode($suggestions);
	}
        
        /*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_supplier()
	{
		$suggestions = $this->xss_clean($this->Fee_deposit->get_fee_supplier($this->input->get('supplier'),$this->input->get('period'),$this->input->get('type'),$this->input->get('item')));
                
                //print_r($suggestions);exit();
                
		echo json_encode($suggestions);
	}
        
        public function suggest_uom()
	{
		$suggestions = $this->xss_clean($this->Uom->get_uom_item_suggestions($this->input->get('item')));

		echo json_encode($suggestions);
	}
        
        
        public function get_fee_deposit($id)
        {
            $suggestions = $this->xss_clean($this->Fee_deposit->get_info($this->input->get('fee')));

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

		$models = $this->Delivery_document->search($search, $limit, $offset, $sort, $order);
		//print_r($models); exit();
                $total_rows = $this->Delivery_document->get_found_rows($search);
                    
                 
		
                $data_rows = array();
		foreach($models->result() as $model)
		{
			$data_rows[] = $this->xss_clean(get_delivery_document_data_row($model));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_model_data_row($this->Model->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($delivery_document_id = -1)
	{
                $data['delivery_document_info'] = $this->Delivery_document->get_info($delivery_document_id);

                if($delivery_document_id > 0)
                {
                    $fee = array('-1' => $this->lang->line('common_none_selected_text'));
                    $all_row = $this->Fee_deposit->get_fee_supplier($data['delivery_document_info']->supplier_id,$data['delivery_document_info']->period,
                            $data['delivery_document_info']->type_item_id,$data['delivery_document_info']->item_id);
                   // print_r($all_row);exit();
                    for($i = 0;$i <count($all_row);$i++)
                    {
                        $cadena = strtoupper($all_row[$i]['deposito']).'(CUOTA: '.$all_row[$i]['cuota'].' SALDO: '.$all_row[$i]['saldo'].')';
                            $fee[$all_row[$i]['id']] = $cadena;
                    }
                    $data['fee']=$fee;
                    $data['fee_selected']= $data['delivery_document_info']->fee_deposit_id;
                    
                    $uom_item = $this->Uom->get_uom_item_suggestions($data['delivery_document_info']->item_id);
                    $data['uom_selected'] = $uom_item[0]['name'];
                    
                    
                    $supplier = $this->Supplier->get_info($data['delivery_document_info']->supplier_id);
                    $data['supplier_selected'] = $supplier;
                    
                }   
                                
                $period = array('-1' => $this->lang->line('common_none_selected_text'));
                $all_row = $this->Period->get_all()->result_array();
		//print_r($all_row);exit();
		for($i = 0;$i <count($all_row);$i++)
		{
			$period[$all_row[$i]['id']] = $all_row[$i]['name'];
		}
                //print_r($period);exit();
                
		$data['period'] = $period;
                $data['selected_period'] = $data['delivery_document_info']->period;
                
                $type_item = array('-1' => $this->lang->line('common_none_selected_text'));
                $all_row = $this->Item_type->get_all()->result_array();			
                //print_r($all_row);exit();			
                for($i = 0;$i <count($all_row);$i++)
                {
                        $type_item[$all_row[$i]['item_type_id']] = $all_row[$i]['name'];
                }			
                $data['item_type'] = $type_item;
                $data['selected_type_item']= $data['delivery_document_info']->type_item_id;
                
                $item = array('-1' => $this->lang->line('common_none_selected_text'));
                $all_row = $this->Item->get_all()->result_array();

                //print_r($all_row);exit();

                for($i = 0;$i <count($all_row);$i++)
                {
                        $item[$all_row[$i]['item_id']] = $all_row[$i]['name'];
                }			
                $data['item'] = $item;
                $data['selected_item'] = $data['delivery_document_info']->item_id;
                
                $uom = array('-1' => $this->lang->line('common_none_selected_text'));
                $all_row = $this->Uom->get_all()->result_array();

                //print_r($all_row);exit();

                for($i = 0;$i <count($all_row);$i++)
                {
                        $uom[$all_row[$i]['uom_id']] = $all_row[$i]['name'];
                }			
                $data['uom'] = $uom;
                //$data['uom_selec']
                
                $certifier = array('-1' => $this->lang->line('common_none_selected_text'));
                $all_row = $this->Certifier->get_all()->result_array();

                //print_r($all_row);exit();

                for($i = 0;$i <count($all_row);$i++)
                {
                        $certifier[$all_row[$i]['id']] = $all_row[$i]['name'];
                }			
                $data['certifier'] = $certifier;
                $data['certifier_selected']= $data['delivery_document_info']->certifier_id;
                
                //print_r($data);exit();
		$this->load->view("delivery_documents/form", $data);
	}

	public function save($model_id = -1)
	{
                //print_r($_POST);exit();
		$model_data = array(
                        'created'     => date('Y-m-d H:i:s'),
			'supplier_id' => $this->input->post('supplier_id'),
			'code' => $this->input->post('code'),
			'certifier_id' => $this->input->post('certifier_id'),
                        'period'=>$this->input->post('period_id'),
                        'item_id'=>$this->input->post('item_id'),
                        'type_item_id'=>$this->input->post('type_item_id'),
                        'amount_entered'=>$this->input->post('amount'),
                        'fee_deposit_id'=>$this->input->post('id_fee_deposit'),
                        'status'=>'1',
                        'deleted'=>'0',
                        'observation'=>$this->input->post('observation')
		);
                //print_r($model_data);exit();
                
		if($this->Delivery_document->save($model_data, $model_id))
		{
			$model_data = $this->xss_clean($model_data);

			// New id
			if($model_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('models_successful_adding'), 'id' => $model_data['id_delivery_document']));
			}
			else // Existing Model
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('models_successful_updating'), 'id' => $model_id));
			}
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('models_error_adding_updating') . ' ' . $model_data['id_delivery_document'], 'id' => -1));
		}
	}

	public function delete()
	{
		$model_to_delete = $this->input->post('ids');

		if($this->Delivery_document->delete_list($model_to_delete))
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('models_successful_deleted') . ' ' . count($model_to_delete) . ' ' . $this->lang->line('models_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('models_cannot_be_deleted')));
		}
	}
}
?>
