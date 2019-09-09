<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('cash_concepts/save/'.$cash_concept_info->cash_concept_id, array('id'=>'cash_concept_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="cash_concepts">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cash_concepts_is_summary'), 'is_summary', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'is_summary',
						'id'=>'is_summary',
						'value'=>1,
						'checked'=>($cash_concept_info->is_summary) ? 1 : 0)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm" id="cash_concept_parent_display" style="display:none;">
			<?php echo form_label($this->lang->line('cash_concepts_cash_concept_parent_id'), 'cash_concept_parent_id', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('cash_concept_parent_id', $cash_concept_parent, $selected_cash_concept_parent, array('class'=>'form-control', 'id' => 'cash_concept_parent_id')); ?>
				<input type="hidden" name="hide_code" id="hide_code" value="03-">
			</div>
		</div>

		<div class="form-group form-group-sm" id="cash_general_used_display" style="display:none;">
			<?php echo form_label($this->lang->line('cash_concepts_is_cash_general_used'), 'is_cash_general_used', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'is_cash_general_used',
						'id'=>'is_cash_general_used',
						'value'=>1,
						'checked'=>($cash_concept_info->is_cash_general_used) ? 1 : 0)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<input type="hidden" name="concept_type" value="3">
			<?php echo form_label($this->lang->line('cash_concepts_code'), 'code', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'code',
						'id'=>'code',
						'class'=>'form-control input-sm',
						'value'=>(!empty($cash_concept_info->code) ? $cash_concept_info->code : "03-"))
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cash_concepts_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$cash_concept_info->name)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('cash_concepts_description'), 'description', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'description',
						'id'=>'description',
						'class'=>'form-control input-sm',
						'value'=>$cash_concept_info->description)
						);?>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	$(document).on('keydown', function (e) {
	  if (e.keyCode == 8 && $('#code').is(":focus") && $('#code').val().length <= $('#hide_code').val().length) {
	      e.preventDefault();
	  }
	});

	$('#cash_concept_edit_form').validate($.extend({
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
			code: 'required',
			name: 'required'
		},

		messages:
		{
			code: "<?php echo $this->lang->line('cash_concepts_code_required'); ?>",
			name: "<?php echo $this->lang->line('cash_concepts_name_required'); ?>"
		}
	}, form_support.error));

	$("#is_summary").click( function(){
		if( $(this).is(':checked') ){
			$('#cash_concept_parent_display').show();
			if($('#cash_concept_parent_id').val()=="-1"){
	   			$('#cash_general_used_display').show();
	   		}
	   		else{
	   			$('#cash_general_used_display').hide();
	   		}
	   	}
	   	else{
			$('#cash_concept_parent_display').hide();
	   	}
	});

	if( $("#is_summary").is(':checked') ){
		$('#cash_concept_parent_display').show();
		if($('#cash_concept_parent_id').val()=="-1"){
   			$('#cash_general_used_display').show();
   		}
   		else{
   			$('#cash_general_used_display').hide();
   		}
   	}
   	else{
		$('#cash_concept_parent_display').hide();
   	}

   	$('#cash_concept_parent_id').change(function(){
   		if($(this).val()=="-1"){
   			$('#cash_general_used_display').show();
   		}
   		else{
   			$('#cash_general_used_display').hide();
   		}
   		$.ajax({
			type: 'GET',
			url: "<?php echo site_url($controller_name.'/get_row/"+$(this).val()+"'); ?>",
			dataType: 'json',
			success: function(resp){
				$('#hide_code').val((resp.code.length == 0 ? "03-" : resp.code+"-"));
				$('#code').val((resp.code.length == 0 ? "03-" : resp.code+"-"));
	        },
	        error: function(jqXHR, textStatus, errorThrown){
	            console.log(jqXHR);
	        }
		});
   	});

});
</script>
