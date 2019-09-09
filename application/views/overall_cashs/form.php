<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('overall_cashs/save/'.$overall_cash_info->overall_cash_id, array('id'=>'overall_cash_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="overall_cashs">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('overall_cashs_opendate'), 'opendate', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'opendate',
							'id'=>'opendate',
							'class'=>'form-control input-sm',
							'readonly' => TRUE,
							'value'=>to_datetime(strtotime($overall_cash_info->opendate)))
							);?>
				</div>
			</div>
		</div>

		<fieldset><?php echo CURRENCY_LABEL;?>
			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('overall_cashs_startbalance'), 'startbalance', array('class'=>'control-label col-xs-3')); ?>
				<div class='col-xs-8'>
					<?php echo form_input(array(
							'name'=>'startbalance',
							'id'=>'startbalance',
							'class'=>'form-control input-sm',
							'readonly' => TRUE,
							'value'=>$overall_cash_info->startbalance)
							);?>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('overall_cashs_openingbalance'), 'openingbalance', array('class'=>'control-label col-xs-3')); ?>
				<div class='col-xs-8'>
					<?php echo form_input(array(
							'name'=>'openingbalance',
							'id'=>'openingbalance',
							'class'=>'form-control input-sm',
							'type' => 'number',
							'step' => 0.01,
							'min' => $overall_cash_info->startbalance,
							'value'=>(!empty($overall_cash_info->openingbalance) ? $overall_cash_info->openingbalance : $overall_cash_info->startbalance))
							);?>
				</div>
			</div>
		</fieldset>

		<fieldset><?php echo USDCURRENCY_LABEL;?>
			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('overall_cashs_startbalance'), 'usdstartbalance', array('class'=>'control-label col-xs-3')); ?>
				<div class='col-xs-8'>
					<?php echo form_input(array(
							'name'=>'usdstartbalance',
							'id'=>'usdstartbalance',
							'class'=>'form-control input-sm',
							'readonly' => TRUE,
							'value'=>$overall_cash_info->usdstartbalance)
							);?>
				</div>
			</div>

			<div class="form-group form-group-sm">	
				<?php echo form_label($this->lang->line('overall_cashs_openingbalance'), 'usdopeningbalance', array('class'=>'control-label col-xs-3')); ?>
				<div class='col-xs-8'>
					<?php echo form_input(array(
							'name'=>'usdopeningbalance',
							'id'=>'usdopeningbalance',
							'class'=>'form-control input-sm',
							'type' => 'number',
							'step' => 0.01,
							'min' => $overall_cash_info->usdstartbalance,
							'value'=>(!empty($overall_cash_info->usdopeningbalance) ? $overall_cash_info->usdopeningbalance : $overall_cash_info->usdstartbalance))
							);?>
				</div>
			</div>
		</fieldset>
		
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	<?php $this->load->view('partial/datepicker_locale'); ?>

	$('#opendate').datetimepicker({
		format: "<?php echo dateformat_bootstrap($this->config->item('dateformat')) . ' ' . dateformat_bootstrap($this->config->item('timeformat'));?>",
		startDate: "<?php echo date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), mktime(0, 0, 0, 1, 1, 2010));?>",
		<?php
		$t = $this->config->item('timeformat');
		$m = $t[strlen($t)-1];
		if( strpos($this->config->item('timeformat'), 'a') !== false || strpos($this->config->item('timeformat'), 'A') !== false )
		{
		?>
			showMeridian: true,
		<?php
		}
		else
		{
		?>
			showMeridian: false,
		<?php
		}
		?>
		minuteStep: 1,
		autoclose: true,
		todayBtn: true,
		todayHighlight: true,
		bootcssVer: 3,
		language: '<?php echo current_language(); ?>'
	});

	$.validator.addMethod("lessThan",
	    function (value, element, param) {
	          var $otherElement = $(param);

	          $.validator.messages["lessThan"] = "<?php echo $this->lang->line('overall_cash_openbalance_greater_than_balance'); ?> "+$otherElement.val()+" <?php echo CURRENCY_LABEL;?>";

	          return parseFloat($otherElement.val()) <= parseFloat(value);
	    },''
	);

	$('#overall_cash_edit_form').validate($.extend({
		submitHandler: function(form) {
			$(form).ajaxSubmit({
				success: function(response)
				{
					dialog_support.hide();
					table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
					self.parent.location.reload();
				},
				dataType: 'json'
			});
		},

		errorLabelContainer: '#error_message_box',

		rules:
		{
			openingbalance: {
				required: true,
			    number: true,
			    lessThan: "#startbalance"
			}
		},

		messages:
		{
			openingbalance: {
				required: "<?php echo $this->lang->line('overall_cash_openbalance_required'); ?>"
			}
		}
	}, form_support.error));

});
</script>
