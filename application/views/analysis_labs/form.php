<div class="col-lg-12">
<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open($controller_name.'/save/'.$analysis_lab_info->id_analysis_lab, array('id'=>'analisis_lab_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="analysis_labs">
            <div class="panel panel-default">
                <div class="panel panel-body">
                    
                        <div class="form-group  col-md-4">
                                <?php echo form_label($this->lang->line('analysis_labs_delivery_document'), 'delivery_document', array('class'=>'required control-label ')); ?>
                                
                                        <?php echo form_input(array(
                                                'name'=>'code',
                                                'id'=>'code',
                                                'class'=>'form-control input-sm',
                                                'size'=>'8',
                                                'value'=>$analysis_lab_info->document_delivery_id)
                                        );?>
                                        <?php echo form_input(array(
                                                'name'=>'document_delivery_id',
                                                'id'=>'document_delivery_id',
                                                'class'=>'form-control input-sm',
                                                'type'=>'hidden',
                                                'value'=>$analysis_lab_info->document_delivery_id)
                                        );?>
                              
                        </div>
                        <div class="form-group  col-md-8">
                                <?php echo form_label($this->lang->line('analysis_labs_supplier'), 'supplier', array('class'=>'control-label')); ?>
                                           <?php echo form_input(array(
                                                'name'=>'supplier',
                                                'id'=>'supplier',
                                                'size'=>'40',
                                                'class'=>'form-control input-sm',
                                                'value'=>$analysis_lab_info->name_supplier)
                                        );?>                               
                                
                        </div>
                      
                        <div class="form-group  col-md-4">
                                <?php echo form_label($this->lang->line('analysis_labs_code_analysis_lab'), 'code', array('class'=>'required control-label  ')); ?>
                                
                                        <?php echo form_input(array(
                                                'name'=>'code_analysis_lab',
                                                'id'=>'code_analysis_lab',
                                                'class'=>'form-control input-sm',
                                                'size'=>'8',
                                                'value'=>$analysis_lab_info->code_analysis_lab)
                                        );?>                               
                                
                        </div>
                        <div class="form-group  col-md-4">
                                <?php echo form_label($this->lang->line('analysis_labs_sample_analysis_lab'), 'sample', array('class'=>'required control-label ')); ?>
                                
                                        <?php echo form_input(array(
                                                'name'=>'sample_analysis_lab',
                                                'id'=>'sample_analysis_lab',
                                                'class'=>'form-control input-sm',
                                                'size'=>'8',
                                                'value'=>$analysis_lab_info->sample_analysis_lab)
                                        );?>                               
                                
                        </div>
                         <div class="form-group col-md-4">
                            <?php echo form_label($this->lang->line('analysis_labs_sello'), 'seal', array('class'=>'required control-label ')); ?>
                            
                                <?php echo form_dropdown('seal_id',$seal,$selected_seal, array('class'=>'form-control input-sm', 'id' => 'seal_id')); ?>
                            
                        </div>
                        
                </div>    
            </div>    
       
            
            <div class="panel panel-info">
               
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home">Humedad</a></li>
                        <li><a data-toggle="tab" href="#menu1">Seleccion</a></li>
                        <li><a data-toggle="tab" href="#menu2">CCC</a></li>
                        <li><a data-toggle="tab" href="#menu3">Cer</a></li>
                        <li><a data-toggle="tab" href="#menu4">Cercal</a></li>
                        <li><a data-toggle="tab" href="#menu5">Cerqq</a></li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                    <div id="home" class="tab-pane fade in active" >
                        <br>
                        <div class="form-group col-md-4">
                            <label >Humedad Estatica</label>
                            <input name="humedad" id="humedad" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($detail_analysis_lab_info->humedad_estatica))echo $detail_analysis_lab_info->humedad_estatica ?>"  >                        
                        </div>
                        
                        <div class="form-group col-md-4">
                            <label >Factor Porcentual</label>
                                <input name="factor_humedad" id="factor_humedad" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($detail_analysis_lab_info->factor_porcentual))echo $detail_analysis_lab_info->factor_porcentual ?>"   >                        
                            
                        </div>
                    
                        <div class="form-group col-md-4">
                            <label >Humedad Ingresada</label>
                                <input name="humedad_ingresada" id="humedad_ingresada" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($detail_analysis_lab_info->humedad_ingresada))echo $detail_analysis_lab_info->humedad_ingresada ?>"  >                        
                        
                        </div>
                        <br>
                        <div class="form-group col-md-4">
                            <label >Kilos Secos</label>
                            <input name="kilos_secos" id="kilos_secos" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($detail_analysis_lab_info->kilos_secos))echo $detail_analysis_lab_info->kilos_secos ?>"  >                        
                        </div>
                        
                        <div class="form-group col-md-4">
                            <label >Qqs Secos</label>
                                <input name="qqs_secos" id="qqs_secos" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($detail_analysis_lab_info->qqs_secos))echo $detail_analysis_lab_info->qqs_secos ?>"  >                        
                            
                        </div>
                    
                        
                    </div>
                    <div id="menu1" class="tab-pane fade">
                        <br>
                        <div class="form-group col-md-4">
                            <label >Kilos Ingresados</label>
                                <input name="kilos_ingresados" id="kilos_ingresados" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($detail_analysis_lab_info->kilos_ingresados))echo $detail_analysis_lab_info->kilos_ingresados ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Kilos Descontados</label>
                                <input name="kilos_descontados" id="kilos_descontados" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($detail_analysis_lab_info->kilos_descontados))echo $detail_analysis_lab_info->kilos_descontados ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Gramos Exportacion</label>
                                <input name="gramos_exportacion" id="gramos_exportacion" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($detail_analysis_lab_info->gramos_exportacion))echo $detail_analysis_lab_info->gramos_exportacion ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Porcentaje Exportacion</label>
                                <input name="porcentaje_exportacion" id="porcentaje_exportacion" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($detail_analysis_lab_info->porcentaje_exportacion))echo $detail_analysis_lab_info->porcentaje_exportacion ?>" >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Gramos Descarte</label>
                                <input name="gramos_descarte" id="gramos_descarte" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($detail_analysis_lab_info->gramos_descarte))echo $detail_analysis_lab_info->gramos_descarte ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Porcentaje Descarte</label>
                                <input name="porcentaje_descarte" id="porcentaje_descarte" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->porcentaje_descarte))echo $analysis_lab_info->porcentaje_descarte ?>" >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Gramos Impureza</label>
                                <input name="gramos_impureza" id="gramos_impureza" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->gramos_impureza))echo $analysis_lab_info->gramos_impureza ?>" >
                        </div>                      
                        <div class="form-group col-md-4">
                            <label >Porcentaje Impureza</label>
                                <input name="porcentaje_impureza" id="porcentaje_impureza" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->porcentaje_impureza))echo $analysis_lab_info->porcentaje_impureza ?>" >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Kilos Exportacion</label>
                                <input name="kilos_exportacion" id="kilos_exportacion" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->kilos_exportacion))echo $analysis_lab_info->kilos_exportacion ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Kilos Descarte</label>
                                <input name="kilos_descarte" id="kilos_descarte" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->kilos_descarte))echo $analysis_lab_info->kilos_descarte ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Kilos Impureza</label>
                                <input name="kilos_impureza" id="kilo_impureza" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->kilo_impureza))echo $analysis_lab_info->kilo_impureza ?>"  >
                        </div>
                        
                    </div>
                    <div id="menu2" class="tab-pane fade">
                        <br>
                        <div class="form-group col-md-4">
                            <label >Modelo CCC</label>
                                <input name="modelo_ccc" id="modelo_ccc" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->modelo_ccc))echo $analysis_lab_info->modelo_ccc ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Porcentaje Export. CCC</label>
                                <input name="porcentaje_export_ccc" id="porcentaje_export_ccc" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->porcentaje_export_ccc))echo $analysis_lab_info->porcentaje_export_ccc ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Porcentaje Descarte CCC</label>
                                <input name="poorcentaje_descarte_ccc" id="porcentaje_descarte_ccc" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->poorcentaje_descarte_ccc))echo $analysis_lab_info->poorcentaje_descarte_ccc ?>"  >
                        </div>    
                        <div class="form-group col-md-4">
                            <label >Porcentaje Impureza CCC</label>
                                <input name="porcentaje_impureza_ccc" id="porcentaje_impureza_ccc" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->porcentaje_impureza_ccc))echo $analysis_lab_info->porcentaje_impureza_ccc ?>" >
                        </div>    
                        <div class="form-group col-md-4">
                            <label >Kilos Export. CCC</label>
                                <input name="kilos_export_ccc" id="kilos_export_ccc" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->kilos_export_ccc))echo $analysis_lab_info->kilos_export_ccc ?>" >
                        </div>    
                        <div class="form-group col-md-4">
                            <label >Kilos Descarte CCC</label>
                                <input name="kilos_descarte_ccc" id="kilos_descarte_ccc" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->kilos_descarte_ccc))echo $analysis_lab_info->kilos_descarte_ccc ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Kilos Impureza CCC</label>
                                <input name="kilos_impureza_ccc" id="kilos_impureza_ccc" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->kilos_impureza_ccc))echo $analysis_lab_info->kilos_impureza_ccc ?>" >
                        </div>
                        
                    </div>
                    <div id="menu3" class="tab-pane fade">
                        <br>
                        <div class="form-group col-md-4">
                            <label >Parametro Cer.</label>
                                <input name="parametro_cer" id="parametro_cer" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->parametro_cer))echo $analysis_lab_info->parametro_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Impureza Cer.</label>
                                <input name="impureza_cer" id="impureza_cer" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->impureza_cer))echo $analysis_lab_info->impureza_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Parametro Real Cer.</label>
                                <input name="parametro_real_cer" id="parametro_real_cer" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->parametro_real_cer))echo $analysis_lab_info->parametro_real_cer ?>" >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Gramos Cafe verde Cer.</label>
                                <input name="gramos_cafeverde_cer" id="gramos_cafeverde_cer" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->gramos_cafeverde_cer))echo $analysis_lab_info->gramos_cafeverde_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Porcentaje Cafe verde Cer.</label>
                                <input name="porcentaje_cafeverde_cer" id="porcentaje_cafeverde_cer" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->porcentaje_cafeverde_cer))echo $analysis_lab_info->porcentaje_cafeverde_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Kilos Cafe verde Cer.</label>
                                <input name="kilos_cafeverde_cer" id="kilos_cafeverde_cer" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->kilos_cafeverde_cer))echo $analysis_lab_info->kilos_cafeverde_cer ?>" >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Gramos Cafe pinton Cer.</label>
                                <input name="gramos_cafepinton_cer" id="gramos_cafepinton_cer" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->gramos_cafepinton_cer))echo $analysis_lab_info->gramos_cafepinton_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Porcentaje Cafe pinton Cer.</label>
                                <input name="porcentaje_cafepinton_cer" id="porcentaje_cafepinton_cer" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->porcentaje_cafepinton_cer))echo $analysis_lab_info->porcentaje_cafepinton_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Kilos Cafe pinton Cer.</label>
                                <input name="kilos_cafepinton_cer" id="kilos_cafepinton_cer" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->kilos_cafepinton_cer))echo $analysis_lab_info->kilos_cafepinton_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Gramos Cafe menudo Cer.</label>
                                <input name="gramos_cafemenudo_cer" id="gramos_cafemenudo_cer" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->gramos_cafemenudo_cer))echo $analysis_lab_info->gramos_cafemenudo_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Porcentaje Cafe menudo Cer.</label>
                                <input name="porcentaje_cafemenudo_cer" id="porcentaje_cafemenudo_cer" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->porcentaje_cafemenudo_cer))echo $analysis_lab_info->porcentaje_cafemenudo_cer ?>" >
                        </div>
                        <div class="form-group col-md-4">
                            <label >kilos Cafe menudo Cer.</label>
                                <input name="kilos_cafemenudo_cer" id="kilos_cafemenudo_cer" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->kilos_cafemenudo_cer))echo $analysis_lab_info->kilos_cafemenudo_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Gramos Cafe rebalse Cer.</label>
                                <input name="gramos_caferebalse_cer" id="gramos_caferebalse_cer" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->gramos_caferebalse_cer))echo $analysis_lab_info->gramos_caferebalse_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Porcentaje Cafe rebalse Cer.</label>
                                <input name="porcentaje_caferebalse_cer" id="porcentaje_caferebalse_cer" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->porcentaje_caferebalse_cer))echo $analysis_lab_info->porcentaje_caferebalse_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Kilos Cafe rebalse Cer.</label>
                                <input name="kilos_caferebalse_cer" id="kilos_caferebalse_cer" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->kilos_caferebalse_cer))echo $analysis_lab_info->kilos_caferebalse_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Gramos Cafe bueno Cer.</label>
                                <input name="gramos_cafebueno_cer" id="gramos_cafebueno_cer" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->gramos_cafebueno_cer))echo $analysis_lab_info->gramos_cafebueno_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Porcentaje Cafe bueno Cer.</label>
                                <input name="porcentaje_cafebueno_cer" id="porcentaje_cafebueno_cer" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->porcentaje_cafebueno_cer))echo $analysis_lab_info->porcentaje_cafebueno_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Kilos Cafe bueno Cer.</label>
                                <input name="kilos_cafebueno_cer" id="kilos_cafebueno_cer" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->kilos_cafebueno_cer))echo $analysis_lab_info->kilos_cafebueno_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Parametro Segunda Cer.</label>
                            <input name="parametro_segunda_cer" id="parametro_segunda_cer" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->parametro_segunda_cer))echo $analysis_lab_info->parametro_segunda_cer ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Impureza Segunda Cer.</label>
                            <input name="impureza_segunda_cer" id="impureza_segunda_cer" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->impureza_segunda_cer))echo $analysis_lab_info->impureza_segunda_cer ?>" >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Parametro Real Segunda Cer.</label>
                            <input name="parametro_realsegunda_cer" id="parametro_realsegunda_cer" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->parametro_realsegunda_cer))echo $analysis_lab_info->parametro_realsegunda_cer ?>"  >
                        </div>
                    </div>
                    <div id="menu4" class="tab-pane fade">
                        <br> 
                        <div class="form-group col-md-4">
                            <label >Gramos Cafe bueno CERCAL.</label>
                                <input name="gramos_cafebueno_cercal" id="gramos_cafebueno_cercal" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->gramos_cafebueno_cercal))echo $analysis_lab_info->gramos_cafebueno_cercal ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Kilos Cafe bueno CERCAL.</label>
                                <input name="kilos_cafebueno_cercal" id="kilos_cafebueno_cercal" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->kilos_cafebueno_cercal))echo $analysis_lab_info->kilos_cafebueno_cercal ?>"  >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Gramos Cafe segunda CERCAL.</label>
                                <input name="gramos_cafesegunda_cercal" id="gramos_cafesegunda_cercal" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->gramos_cafesegunda_cercal))echo $analysis_lab_info->gramos_cafesegunda_cercal ?>" >
                        </div>
                        <div class="form-group col-md-4">
                            <label >Kilos Cafe segunda CERCAL.</label>
                                <input name="kilos_cafesegunda_cercal" id="kilos_cafesegunda_cercal" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->kilos_cafesegunda_cercal))echo $analysis_lab_info->kilos_cafesegunda_cercal ?>"  >
                        </div>
                        
                    </div>
                        <div id="menu5" class="tab-pane fade">
                            <br>
                            <div class="form-group col-md-4">
                                <label >Kilos Cafe bueno Cerqq.</label>
                                <input name="kilos_cafebueno_cerqq" id="kilos_cafebueno_cerqq" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->kilos_cafebueno_cerqq))echo $analysis_lab_info->kilos_cafebueno_cerqq ?>"  >
                            </div>
                            <div class="form-group col-md-4">
                                <label >Kilos Cafe segunda Cerqq.</label>
                                <input name="kilos_cafesegunda_cerqq" id="kilos_cafesegunda_cerqq" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->kilos_cafesegunda_cerqq))echo $analysis_lab_info->kilos_cafesegunda_cerqq ?>"  >
                            </div>
                            <div class="form-group col-md-4">
                                <label >Qqs Cafe bueno Cerqq.</label>
                                <input name="qqs_cafebueno_cerqq" id="qqs_cafebueno_cerqq" type="text" class="form-control  text-right input-sm" size="5"  value="<?php if(isset($analysis_lab_info->qqs_cafebueno_cerqq))echo $analysis_lab_info->qqs_cafebueno_cerqq ?>"  >
                            </div>
                            <div class="form-group col-md-4">
                                <label >Qqs Cafe segunda Cerqq.</label>
                                <input name="qqs_cafesegunda_cerqq" id="qqs_cafesegunda_cerqq" type="text" class="form-control  text-right input-sm" size="5" value="<?php if(isset($analysis_lab_info->qqs_cafesegunda_cerqq))echo $analysis_lab_info->qqs_cafesegunda_cerqq ?>"  >
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            
            
	</fieldset>
<?php echo form_close(); ?>
</div>
<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{       
        $(document).on('change','#code',function(e){
	var  valor = this.value
	  // alert(id);
        $.ajax( {  
                url: "<?php echo site_url($controller_name.'/suggest_supplier'); ?>",
                type: 'POST',
                dataType : 'json',
                async: true,
                data: 'codigo='+valor,
                success:function(datos){
                    if(datos)
                    {
                         $('#supplier').val(datos[0].name);       
                         $('#document_delivery_id').val(datos[0].document_delivery_id);   
                    }

                                },
                error: function(xhr, status) {
                                alert('Disculpe, existiÃ³ un problema');
                                }
                });

    });

	$('#analysis_lab_edit_form').validate($.extend({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)
				{
					dialog_support.hide();
					table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
				},
				dataType: 'json'
			});
		},

		errorLabelContainer: '#error_message_box',

		rules:
		{
			document_delivery_id: 'required',
			code_analysis_lab: 'required',
			sample_analysis_lab: 'required'
		},

		messages:
		{
			document_delivery_id: "<?php echo $this->lang->line($controller_name.'_document_delivery_id_required'); ?>",
			code_analysis_lab: "<?php echo $this->lang->line($controller_name.'_code_analysis_lab_required'); ?>",
			sample_analysis_lab: "<?php echo $this->lang->line($controller_name.'_sample_analysis_lab'); ?>"
		}
	}, form_support.error));
});
</script>
