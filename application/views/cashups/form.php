<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"><?php echo ($cash_book_notfound==TRUE ? $this->lang->line('cashup_cashbook_notfound') : "");?></ul>

<?php echo form_open('cashups/save/'.$cash_ups_info->cashup_id, array('id'=>'cashups_edit_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="item_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_info'), 'cash_ups_info', array('class'=>'control-label col-xs-4')); ?>
			<?php echo form_label('('.$cash_book_info->code . ') '. $user_info->first_name .' '.$user_info->last_name, 'cashup_id', array('class'=>'control-label col-xs-8', 'style'=>'text-align:left')); ?>
			<?php echo form_input(array(
				'name'=>'cash_book_id',
				'id'=>'cash_book_id',
				'type' => 'hidden',
				'value'=>$cash_book_info->cash_book_id)
				);?>
			<?php echo form_input(array(
				'name'=>'open_employee_id',
				'id'=>'open_employee_id',
				'type' => 'hidden',
				'value'=>$user_info->person_id)
				);?>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_open_date'), 'open_date', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'open_date',
							'id'=>'open_date',
							'class'=>'form-control input-sm datepicker',
							'value'=>to_datetime(strtotime($cash_ups_info->open_date)))
							);?>
				</div>
			</div>
		</div>

		<!--<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_cash_book_id'), 'cash_book_id', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<?php echo form_dropdown('cash_book_id', $cash_books, $cash_ups_info->cash_book_id, 'id="cash_book_id" class="form-control"');?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_open_employee'), 'open_employee', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<?php echo form_dropdown('open_employee_id', $employees, $cash_ups_info->open_employee_id, 'id="open_employee_id" class="form-control" readonly');?>
			</div>
		</div>-->

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_open_amount_cash'), 'open_amount_cash', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-4'>
				<div class="input-group input-group-sm">
					<?php if (!currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
					<?php echo form_input(array(
							'name'=>'open_amount_cash',
							'id'=>'open_amount_cash',
							'class'=>'form-control input-sm',
							'value'=>to_currency_no_money($cash_ups_info->open_amount_cash))
							);?>
					<?php if (currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!--<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_transfer_amount_cash'), 'transfer_amount_cash', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-4'>
				<div class="input-group input-group-sm">
					<?php if (!currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
					<?php echo form_input(array(
							'name'=>'transfer_amount_cash',
							'id'=>'transfer_amount_cash',
							'class'=>'form-control input-sm',
							'value'=>to_currency_no_money($cash_ups_info->transfer_amount_cash))
							);?>
					<?php if (currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_close_date'), 'close_date', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<div class="input-group">
					<span class="input-group-addon input-sm"><span class="glyphicon glyphicon-calendar"></span></span>
					<?php echo form_input(array(
							'name'=>'close_date',
							'id'=>'close_date',
							'class'=>'form-control input-sm datepicker',
							'value'=>to_datetime(strtotime($cash_ups_info->close_date)))
							);?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_close_employee'), 'close_employee', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<?php echo form_dropdown('close_employee_id', $employees, $cash_ups_info->close_employee_id, 'id="close_employee_id" class="form-control" readonly');?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_closed_amount_cash'), 'closed_amount_cash', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-4'>
				<div class="input-group input-group-sm">
					<?php if (!currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
					<?php echo form_input(array(
							'name'=>'closed_amount_cash',
							'id'=>'closed_amount_cash',
							'class'=>'form-control input-sm',
							'value'=>to_currency_no_money($cash_ups_info->closed_amount_cash))
							);?>
					<?php if (currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_note'), 'note', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<?php echo form_checkbox(array(
					'name'=>'note',
					'id'=>'note',
					'value'=>0,
					'checked'=>($cash_ups_info->note) ? 1 : 0)
				);?>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_closed_amount_due'), 'closed_amount_due', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-4'>
				<div class="input-group input-group-sm">
					<?php if (!currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
					<?php echo form_input(array(
							'name'=>'closed_amount_due',
							'id'=>'closed_amount_due',
							'class'=>'form-control input-sm',
							'value'=>to_currency_no_money($cash_ups_info->closed_amount_due))
							);?>
					<?php if (currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_closed_amount_card'), 'closed_amount_card', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-4'>
				<div class="input-group input-group-sm">
					<?php if (!currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
					<?php echo form_input(array(
							'name'=>'closed_amount_card',
							'id'=>'closed_amount_card',
							'class'=>'form-control input-sm',
							'value'=>to_currency_no_money($cash_ups_info->closed_amount_card))
							);?>
					<?php if (currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_closed_amount_check'), 'closed_amount_check', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-4'>
				<div class="input-group input-group-sm">
					<?php if (!currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
					<?php echo form_input(array(
							'name'=>'closed_amount_check',
							'id'=>'closed_amount_check',
							'class'=>'form-control input-sm',
							'value'=>to_currency_no_money($cash_ups_info->closed_amount_check))
							);?>
					<?php if (currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_closed_amount_total'), 'closed_amount_total', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-4'>
				<div class="input-group input-group-sm">
					<?php if (!currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
					<?php echo form_input(array(
							'name'=>'closed_amount_total',
							'id'=>'closed_amount_total',
							'readonly'=>'true',
							'class'=>'form-control input-sm',
							'value'=>to_currency_no_money($cash_ups_info->closed_amount_total)
							));?>
					<?php if (currency_side()): ?>
						<span class="input-group-addon input-sm"><b><?php echo $this->config->item('currency_symbol'); ?></b></span>
					<?php endif; ?>
				</div>
			</div>
		</div>-->

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('cashups_description'), 'description', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-6'>
				<?php echo form_textarea(array(
					'name'=>'description',
					'id'=>'description',
					'class'=>'form-control input-sm',
					'value'=>$cash_ups_info->description)
					);?>
			</div>
		</div>

		<?php
		if(!empty($cash_ups_info->cashup_id))
		{
		?>
			<div class="form-group form-group-sm">
				<?php echo form_label($this->lang->line('cashups_is_deleted').':', 'deleted', array('class'=>'control-label col-xs-3')); ?>
				<div class='col-xs-5'>
					<?php echo form_checkbox(array(
						'name'=>'deleted',
						'id'=>'deleted',
						'value'=>1,
						'checked'=>($cash_ups_info->deleted) ? 1 : 0)
					);?>
				</div>
			</div>
		<?php
		}
		?>
	</fieldset>
<?php echo form_close(); ?>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{

	<?php if($cash_book_notfound): ?>
		$("#cashups_edit_form :input").prop("disabled", true);
	<?php endif;?>

	<?php $this->load->view('partial/datepicker_locale'); ?>

	$('#open_date').datetimepicker({
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

	var submit_form = function()
	{
		$(this).ajaxSubmit(
		{
			success: function(response)
			{
				dialog_support.hide();
				table_support.handle_submit('<?php echo site_url('cashups'); ?>', response);
				setTimeout(function(){
					self.parent.location.reload();
				},2000)
			},
			dataType: 'json'
		});
	};

	// add the rule here
	jQuery.validator.addMethod(
		"notEqualTo",
		function(elementValue,element,param) {
			return elementValue != param;
		},
		"<?php echo $this->lang->line('cashup_open_amount_required'); ?>"
	);

	$('#cashups_edit_form').validate($.extend(
	{
		submitHandler: function(form)
		{
			submit_form.call(form);
		},
		rules:
		{
			open_date: 'required'
		},
		messages:
		{
			open_date:
			{
				required: '<?php echo $this->lang->line('cashups_date_required'); ?>'

			}
		}
	}, form_support.error));
});
</script>
