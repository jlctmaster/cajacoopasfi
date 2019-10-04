<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Analysis_labs extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('analysis_labs');
	}

	public function index()
	{
                
		$data['table_headers'] = $this->xss_clean(get_analysis_lab_manage_table_headers());
                    
		$this->load->view('analysis_labs/manage', $data);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest_supplier()
	{
		$suggestions = $this->xss_clean($this->Analysis_lab->get_delivery_document_supplier($this->input->post('codigo')));
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

                
		$models = $this->Analysis_lab->search($search, $limit, $offset, $sort, $order);
		
                //print_r($models);exit();
                
                $total_rows = $this->Analysis_lab->get_found_rows($search);

		$data_rows = array();
		foreach($models->result() as $model)
		{
			$data_rows[] = $this->xss_clean(get_analysis_lab_data_row($model));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_model_data_row($this->Analysis_lab->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function view($id = -1)
	{
		$data['analysis_lab_info'] = $this->Analysis_lab->get_info($id);
                //print_r($data['analysis_lab_info']);exit();
                
                if($data['analysis_lab_info']->id_analysis_lab > 0)
                {
                    $data['detail_analysis_lab_info']= $this->Analysis_lab->get_detail($data['analysis_lab_info']->id_analysis_lab);
                    $data['selected_seal'] = $data['detail_analysis_lab_info']->id_sello;
                }
                
                $seal = array('-1' => $this->lang->line('common_none_selected_text'));
                $all_row = $this->Seal->get_all()->result_array();
		//print_r($all_row);exit();
		for($i = 0;$i <count($all_row);$i++)
		{
			$seal[$all_row[$i]['id']] = $all_row[$i]['name'];
		}
                
                
		$data['seal'] = $seal;
                
                
		$this->load->view("analysis_labs/form", $data);
	}

	public function save($id = -1)
	{       
                $analysis = array(
                        'created'=>date('Y-m-d H:i:s'),
                        'document_delivery_id'=> $this->input->post('document_delivery_id'), 
                        'code_analysis_lab' =>$this->input->post('code_analysis_lab') ,
                        'sample_analysis_lab'=> $this->input->post('sample_analysis_lab'),
                        'status_analysis_lab'=>'1',
                        'deleted'=>'0'
                );
                
                
                
                
		$detalle = array(
                                              
                        'humedad_estatica'=> (empty($this->input->post('humedad')))?'0':$this->input->post('humedad'),
                        'factor_porcentual'=> (empty($this->input->post('factor_humedad')))?'0':$this->input->post('factor_humedad'),
                        'humedad_ingresada'=> (empty($this->input->post('humedad_ingresada')))?'0':$this->input->post('humedad_ingresada'),
                        'kilos_secos'=> (empty($this->input->post('kilos_secos')))?'0':$this->input->post('kilos_secos'),
                        'qqs_secos'=> (empty($this->input->post('qqs_secos')))?'0':$this->input->post('qqs_secos'),
                        'kilos_ingresados'=> (empty($this->input->post('kilos_ingresados')))?'0':$this->input->post('kilos_ingresados'),
                        'kilos_descontados'=> (empty($this->input->post('kilos_descontados')))?'0':$this->input->post('kilos_descontados'),
                        'gramos_exportacion'=> (empty($this->input->post('gramos_exportacion')))?'0':$this->input->post('gramos_exportacion'),
                        'porcentaje_exportacion'=> (empty($this->input->post('porcentaje_exportacion')))?'0':$this->input->post('porcentaje_exportacion'),
                        'gramos_descarte'=> (empty($this->input->post('gramos_descarte')))?'0':$this->input->post('gramos_descarte'),
                        'porcentaje_descarte'=> (empty($this->input->post('porcentaje_descarte')))?'0':$this->input->post('porcentaje_descarte'),
                        'gramos_impureza'=> (empty($this->input->post('gramos_impureza')))?'0':$this->input->post('gramos_impureza'),
                        'porcentaje_impureza'=> (empty($this->input->post('porcentaje_impureza')))?'0':$this->input->post('porcentaje_impureza'),
                        'kilos_exportacion'=> (empty($this->input->post('kilos_exportacion')))?'0':$this->input->post('kilos_exportacion'),
                        'kilos_descarte'=> (empty($this->input->post('kilos_descarte')))?'0':$this->input->post('kilos_descarte'),
                        'kilos_impureza'=> (empty($this->input->post('kilos_impureza')))?'0':$this->input->post('kilos_impureza'),
                        'modelo_ccc'=> $this->input->post('modelo_ccc'),
                        'porcentaje_exportacion_ccc'=>(empty($this->input->post('porcentaje_export_ccc')))?'0':$this->input->post('porcentaje_export_ccc'),
                        'porcentaje_descarte_ccc'=>(empty($this->input->post('poorcentaje_descarte_ccc')))?'0':$this->input->post('poorcentaje_descarte_ccc'),
                        'porcentaje_impureza_ccc'=> (empty($this->input->post('porcentaje_impureza_ccc')))?'0':$this->input->post('porcentaje_impureza_ccc'),
                        'kilos_exportacion_ccc'=> (empty($this->input->post('kilos_export_ccc')))?'0':$this->input->post('kilos_export_ccc'),
                        'kilos_descarte_ccc'=> (empty($this->input->post('kilos_descarte_ccc')))?'0':$this->input->post('kilos_descarte_ccc'),
                        'kilos_impureza_ccc'=> (empty($this->input->post('kilos_impureza_ccc')))?'0':$this->input->post('kilos_impureza_ccc'),
                        'parametro_cer'=> (empty($this->input->post('parametro_cer')))?'0':$this->input->post('parametro_cer'),
                        'impureza_cer'=> (empty($this->input->post('impureza_cer')))?'0':$this->input->post('impureza_cer'),
                        'parametro_real_cer'=> (empty($this->input->post('parametro_real_cer')))?'0':$this->input->post('parametro_real_cer'),
                        'gramos_cafeverde_cer'=> (empty($this->input->post('gramos_cafeverde_cer')))?'0':$this->input->post('gramos_cafeverde_cer'),
                        'porcentaje_cafeverde_cer'=> (empty($this->input->post('porcentaje_cafeverde_cer')))?'0':$this->input->post('porcentaje_cafeverde_cer'),
                        'kilos_cafeverde_cer'=> (empty($this->input->post('kilos_cafeverde_cer')))?'0':$this->input->post('kilos_cafeverde_cer'),
                        'gramos_cafepinton_cer'=> (empty($this->input->post('gramos_cafepinton_cer')))?'0':$this->input->post('gramos_cafepinton_cer'),
                        'porcentaje_cafepinton_cer'=> (empty($this->input->post('porcentaje_cafepinton_cer')))?'0':$this->input->post('porcentaje_cafepinton_cer'),
                        'kilos_cafepinton_cer'=> (empty($this->input->post('kilos_cafepinton_cer')))?'0':$this->input->post('kilos_cafepinton_cer'),
                        'gramos_cafemenudo_cer'=> (empty($this->input->post('gramos_cafemenudo_cer')))?'0':$this->input->post('gramos_cafemenudo_cer'),
                        'porcentaje_cafemenudo_cer'=> (empty($this->input->post('porcentaje_cafemenudo_cer')))?'0':$this->input->post('porcentaje_cafemenudo_cer'),
                        'kilos_cafemenudo_cer'=> (empty($this->input->post('kilos_cafemenudo_cer')))?'0':$this->input->post('kilos_cafemenudo_cer'),
                        'gramos_caferebalse_cer'=> (empty($this->input->post('gramos_caferebalse_cer')))?'0':$this->input->post('gramos_caferebalse_cer'),
                        'porcentaje_caferebalse_cer'=> (empty($this->input->post('porcentaje_caferebalse_cer')))?'0':$this->input->post('porcentaje_caferebalse_cer'),
                        'kilos_caferebalse_cer'=> (empty($this->input->post('kilos_caferebalse_cer')))?'0':$this->input->post('kilos_caferebalse_cer'),
                        'gramos_cafebueno_cer'=> (empty($this->input->post('gramos_cafebueno_cer')))?'0':$this->input->post('gramos_cafebueno_cer'),
                        'porcentaje_cafebueno_cer'=> (empty($this->input->post('porcentaje_cafebueno_cer')))?'0':$this->input->post('porcentaje_cafebueno_cer'),
                        'kilos_cafebueno_cer'=> (empty($this->input->post('kilos_cafebueno_cer')))?'0':$this->input->post('kilos_cafebueno_cer'),
                        'parametro_cer_seg'=> (empty($this->input->post('parametro_segunda_cer')))?'0':$this->input->post('parametro_segunda_cer'),
                        'impureza_cer_seg'=> (empty($this->input->post('impureza_segunda_cer')))?'0':$this->input->post('impureza_segunda_cer'),
                        'parametro_real_cer_seg'=> (empty($this->input->post('parametro_realsegunda_cer')))?'0':$this->input->post('parametro_realsegunda_cer'),
                        'gramos_cafebueno_cercal'=> (empty($this->input->post('gramos_cafebueno_cercal')))?'0':$this->input->post('gramos_cafebueno_cercal'),
                        'kilos_cafebueno_cercal'=> (empty($this->input->post('kilos_cafebueno_cercal')))?'0':$this->input->post('kilos_cafebueno_cercal'),
                        'gramos_cafesegunda_cercal'=> (empty($this->input->post('gramos_cafesegunda_cercal')))?'0':$this->input->post('gramos_cafesegunda_cercal'),
                        'kilos_cafesegunda_cercal'=> (empty($this->input->post('kilos_cafesegunda_cercal')))?'0':$this->input->post('kilos_cafesegunda_cercal'),
                        'kilos_cafebueno_cerqq'=> (empty($this->input->post('kilos_cafebueno_cerqq')))?'0':$this->input->post('kilos_cafebueno_cerqq'),
                        'kilos_cafesegunda_cerqq'=> (empty($this->input->post('kilos_cafesegunda_cerqq')))?'0':$this->input->post('kilos_cafesegunda_cerqq'),
                        'qqs_cafebueno_cerqq'=> (empty($this->input->post('qqs_cafebueno_cerqq')))?'0':$this->input->post('qqs_cafebueno_cerqq'),
                        'qqs_cafesegunda_cerqq'=> (empty($this->input->post('qqs_cafesegunda_cerqq')))?'0':$this->input->post('qqs_cafesegunda_cerqq'),
                        'fecha_creacion'=>date('Y-m-d H:i:s'),
                        'id_sello'=> $this->input->post('seal_id'),
                        'analysis_lab_id'=>'0'
			
		);
                
                //print_r($model_data);exit();
		$datos = array("maestro"=>$analysis,"detalle"=>$detalle);
                if($this->Analysis_lab->save($datos, $id))
		{
			$analysis = $this->xss_clean($analysis);
                                            
			// New id
			if($id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('models_successful_adding'), 'id' => $analysis['id_analysis_lab']));
			}
			else // Existing Model
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('models_successful_updating'), 'id' => $id));
			}
		}
		else//failure
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('models_error_adding_updating') . ' ' . $analysis['code_analysis_lab'], 'id' => -1));
		}
	}

	public function delete()
	{
		$model_to_delete = $this->input->post('ids');

		if($this->Model->delete_list($model_to_delete))
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
