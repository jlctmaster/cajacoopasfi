<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('cash_books/save/'.$cash_book_info->cash_book_id, array('id'=>'cash_book_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="cash_books">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cash_books_code'), 'code', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'code',
						'id'=>'code',
						'class'=>'form-control input-sm',
						'readonly' => TRUE,
						'value'=>$cash_book_info->code)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cash_books_stock_location_id'), 'stock_location_id', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('stock_location_id', $stock_location, $selected_stock_location, array('class'=>'form-control', 'id' => 'stock_location_id')); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cash_books_user_id'), 'user_id', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_dropdown('user_id', $user, $selected_user, array('class'=>'form-control', 'id' => 'user_id')); ?>
			</div>
		</div>

		<div class="form-group form-group-sm">	
			<?php echo form_label($this->lang->line('cash_books_address'), 'address', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'address',
						'id'=>'address',
						'class'=>'form-control input-sm',
						'value'=>$cash_book_info->address)
						);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cash_books_is_cash_general'), 'is_cash_general', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-1'>
				<?php echo form_checkbox(array(
						'name'=>'is_cash_general',
						'id'=>'is_cash_general',
						'value'=>1,
						'checked'=>($cash_book_info->is_cash_general) ? 1 : 0)
						);?>
			</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	// add the rule here
	jQuery.validator.addMethod(
		"notEqualTo",
		function(elementValue,element,param) {
			return elementValue != param;
		},
		"<?php echo $this->lang->line('common_none_selected_text'); ?>"
	);

	$('#cash_book_edit_form').validate($.extend({
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
			stock_location_id: {
				required: true,
				notEqualTo: -1
			},
			user_id: {
				required: true,
				notEqualTo: -1
			}
		},

		messages:
		{
			code: "<?php echo $this->lang->line('cash_book_code_required'); ?>",
			stock_location_id: "<?php echo $this->lang->line('cash_book_stock_location_id_required'); ?>",
			user_id: "<?php echo $this->lang->line('cash_book_user_id_required'); ?>"
		}
	}, form_support.error));

	$('#stock_location_id').change(function(){
		$.ajax({
			type: 'GET',
			url: "<?php echo site_url($controller_name.'/get_user_by_location/"+$(this).val()+"'); ?>",
			dataType: 'json',
			success: function(resp){
				if(resp.length>0){
					var str = resp[0].location_name;
					var matches = str.match(/\b(\w)/g); // ['J','S','O','N']
					var acronym = matches.join(''); // JSON
		            $('#code').val(resp[0].location_code+"-"+acronym.toUpperCase());
		            $('#user_id').empty();
	            	var text = "<?php echo $this->lang->line('common_none_selected_text'); ?>";
	            	var value = -1;
	            	$('#user_id').empty().append(new Option(text, value));
		            for(var i=0;i<resp.length;i++)
		            {
		            	var text = resp[i].first_name+" "+resp[i].last_name+" ("+resp[i].username+")";
		            	var value = resp[i].person_id;
		            	$("#user_id").append(new Option(text, value));
		            }
				}
				else{
	            	$('#code').val("");
	            	var text = "<?php echo $this->lang->line('common_none_selected_text'); ?>";
	            	var value = -1;
	            	$('#user_id').empty().append(new Option(text, value));
				}

	        },
	        error: function(jqXHR, textStatus, errorThrown){
	            console.log(jqXHR);
	        }
		});
	});

	$('#user_id').change(function(){
		var text= $(this).find('option:selected').text().split('(');
		var str = text[0];
		var matches = str.match(/\b(\w)/g); // ['J','S','O','N']
		var acronym = matches.join(''); // JSON
		var actualcode = $('#code').val();
		if(actualcode.split('-').length > 2)
		{
			actualcode = actualcode.slice(0,-3);
		}
		$('#code').val(actualcode+"-"+acronym.toUpperCase());
	});
	
	$('#is_cash_general').click( function(e){
		if( $(this).is(':checked') ){
			$.ajax({
				type: 'GET',
				url: "<?php echo site_url($controller_name.'/check_cash_general/'.$cash_book_info->cash_book_id); ?>",
				dataType: 'json',
				success: function(resp){
					if(resp.length>0){
						$("#is_cash_general").prop("checked", false);
						alert("<?php echo $this->lang->line('cash_book_cash_general_unique'); ?> "+resp[0].code);
					}
		        },
		        error: function(jqXHR, textStatus, errorThrown){
		            console.log(jqXHR);
		        }
			});
		}
	});

});
</script>
