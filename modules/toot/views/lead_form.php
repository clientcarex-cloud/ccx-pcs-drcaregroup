<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .swal2-popup { font-size: 1.6rem !important; }
    .form-section {
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 30px;
    }
    .form-heading {
        text-align: center;
        margin-bottom: 30px;
        font-size: 22px;
        font-weight: 600;
        padding-bottom: 10px;
    }
    .section-title {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 18px;
        margin: 0px 0 10px;
		border-bottom: 1px solid #ddd;
    }
    .section-title::before,
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        margin: 0 15px;
    }
    .btn-purple {
    }
</style>

<div id="wrapper">
<div class="content">
<div class="row">
<div class="col-md-12">
<div class="panel_s">
<div class="panel-body">
<div class="clearfix"></div>

<div style="font-size: 20px; font-weight: bold; padding: 10px 20px; text-align: left; border-radius: 4px 4px 0 0;">
    <?php echo _l('lead_form');?>
    <hr>
</div>

<?php
//print_r($patient_data);
if($master_data){
	extract($master_data);
}

//print_r($branch);
?>
<br>
<?php 
if(!$patient_data){
	

?>
<div class="row" style="margin-top: -30px">
   <div class="col-md-4"></div>
 <div class="col-md-3 me-2 position-relative">
			<label class="form-label"><?= _l('contact_number') ?>*</label>
			<input type="text" class="form-control" id="contact_number_search" name="contact_number"
				placeholder="<?= _l('enter_contact_number') ?>"
				value="<?= htmlspecialchars($patient['contact_number'] ?? '') ?>" autocomplete="off" required>
			
			<div id="contact_search_results" class=" search-results animated fadeIn no-mtop display-block" style="display:none; position:absolute; z-index:999;"></div>
		</div>
	</div>

<?php
}
if($patient_data){
    $patient = (array) $patient_data;
	
	?>
        <form method="post" action="<?= admin_url('client/save_client'); ?>">
            <input type="hidden" name="userid" value="<?php echo $patient['userid'];?>">
            <input type="hidden" name="leadid" value="<?php echo $patient['id'];?>">
   
	
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />


    <div class="form-section" style="margin-top: -50px">
        <div class="section-title"><?= _l('patient_information') ?></div>
			<?php
			//print_r($patient);
			?>
        <div class="row">
	<?php 
	// Make sure all values are trimmed to avoid comparison issues
	$patient_inactive_fields = array_map('trim', explode(",", $patient_inactive_fields));

	// Patient Name, Salutation, and Gender
	$patient_name_html = '';
	if (!in_array('salutation', $patient_inactive_fields) || !in_array('gender', $patient_inactive_fields) || !in_array('company', $patient_inactive_fields)) {
		ob_start(); ?>
		<label class="form-label"><?= _l('patient_name') ?>*</label>
		<div class="mb-3" style="display: flex; gap: 0;">
			<?php if (!in_array('salutation', $patient_inactive_fields)) { ?>
				<div style="flex: 1; max-width: 29%;">
					<select class="form-control" name="salutation">
						<option value="Mr." <?= ($patient['salutation'] ?? '') == 'Mr.' ? 'selected' : '' ?>>Mr.</option>
						<option value="Mrs." <?= ($patient['salutation'] ?? '') == 'Mrs.' ? 'selected' : '' ?>>Mrs.</option>
						<option value="Ms." <?= ($patient['salutation'] ?? '') == 'Ms.' ? 'selected' : '' ?>>Ms.</option>
					</select>
				</div>
			<?php } ?>
			<?php if (!in_array('company', $patient_inactive_fields)) { ?>
				<div style="flex: 1; max-width: 49%;">
					<input type="text" class="form-control" name="company" placeholder="<?= _l('enter_patient_name') ?>" value="<?= htmlspecialchars($patient['name'] ?? '') ?>" required>
				</div>
			<?php } ?>
			<?php if (!in_array('gender', $patient_inactive_fields)) { ?>
				<div style="flex: 1; max-width: 33%;">
					<select class="form-control" name="gender">
						<option value="Male" <?= ($patient['gender'] ?? '') == 'Male' ? 'selected' : '' ?>><?= _l('male') ?></option>
						<option value="Female" <?= ($patient['gender'] ?? '') == 'Female' ? 'selected' : '' ?>><?= _l('female') ?></option>
						<option value="Other" <?= ($patient['gender'] ?? '') == 'Other' ? 'selected' : '' ?>><?= _l('other') ?></option>
					</select>
				</div>
			<?php } ?>
		</div>
		<?php
		$patient_name_html = ob_get_clean();
	}

	// Field configurations
	$fields = [];

	if ($patient_name_html != '') {
		$fields[] = $patient_name_html;
	}

	if (!in_array('age', $patient_inactive_fields)) {
		$fields[] = '<label class="form-label">' . _l('age') . ' *</label>
			<input type="text" class="form-control" name="age" placeholder="' . _l('enter_age') . '" value="' . htmlspecialchars($patient['age'] ?? '') . '" required>';
	}

	if (!in_array('marital_status', $patient_inactive_fields)) {
		$fields[] = render_select(
			'marital_status',
			[
				['id' => 'Single', 'name' => _l('single')],
				['id' => 'Married', 'name' => _l('married')],
				['id' => 'Divorced', 'name' => _l('divorced')],
				['id' => 'Widowed', 'name' => _l('widowed')],
			],
			['id', ['name']],
			_l('marital_status') . ' *',
			$patient['marital_status'] ?? '',
			['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']
		);
	}

	if (!in_array('email_id', $patient_inactive_fields)) {
		$fields[] = '<label class="form-label">' . _l('email') . '</label>
			<input type="text" class="form-control" name="email_id" placeholder="' . _l('enter_email') . '" value="' . htmlspecialchars($patient['email'] ?? '') . '">';
	}

	if (!in_array('contact_number', $patient_inactive_fields)) {
		$fields[] = '<label class="form-label">' . _l('contact_number') . ' *</label>
			<input type="text" class="form-control" name="contact_number" placeholder="' . _l('enter_contact_number') . '"
			value="' . htmlspecialchars($patient['phonenumber'] ?? $this->input->post('contact_number')) . '" required readonly>';
	}

	if (!in_array('alt_number1', $patient_inactive_fields)) {
		$fields[] = '<label class="form-label">' . _l('alternative_number1') . '</label>
			<input type="text" class="form-control" name="alt_number1" placeholder="' . _l('enter_alt_number1') . '" value="' . htmlspecialchars($patient['alt_number1'] ?? '') . '">
			<input type="hidden"  name="alt_number2" value="0">';
	}

	if (!in_array('city', $patient_inactive_fields)) {
		$fields[] = '<label class="form-label">' . _l('city_name') . ' *</label>
			<input type="text" class="form-control" name="city" placeholder="' . _l('enter_city') . '" value="' . htmlspecialchars($patient['city_name'] ?? '') . '" required>';
	}

	if (!in_array('area', $patient_inactive_fields)) {
		$fields[] = '<label class="form-label">' . _l('area') . '</label>
			<input type="text" class="form-control" name="area" placeholder="' . _l('enter_area') . '" value="' . htmlspecialchars($patient['address'] ?? '') . '">';
	}

	if (!in_array('pincode', $patient_inactive_fields)) {
		$fields[] = '<label class="form-label">' . _l('pincode') . '</label>
			<input type="text" class="form-control" name="pincode" placeholder="' . _l('enter_pincode') . '" value="' . htmlspecialchars($patient['zip'] ?? '') . '">';
	}

	// Render the fields dynamically in rows of 3
	$row_count = 0;
	echo '<div class="row">';
	foreach ($fields as $fieldHtml) {
		if ($row_count > 0 && $row_count % 3 == 0) {
			echo '</div><div class="row">';
		}
		echo '<div class="col-md-4 mt-3">' . $fieldHtml . '</div>';
		$row_count++;
	}
	echo '</div>';
	?>
</div>


    </div>

     <div class="form-section">
        <div class="section-title"><?= _l('appointment_information') ?></div>
        <div class="row">
            <div class="col-md-4">
               <?= render_select(
				'groupid',
				$branch,
				['id', 'name'],
				_l('branch') . '*',
				isset($current_branch_id) ? $current_branch_id : ($patient['groupid'] ?? ''),
				['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']
			) ?>
            </div>

            <div class="col-md-4">
                <label class="form-label"><?= _l('appointment_date') ?>*</label>
                <input type="datetime-local" class="form-control" name="appointment_date" placeholder="YYYY-MM-DD" value="<?php echo date('Y-m-d H:i:s');?>" required>
            </div>

            <div class="col-md-4">
                <?= render_select('assign_doctor_id', $assign_doctor, ['staffid', ['firstname', 'lastname']], _l('assign_doctor') . '*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']) ?>
            </div>
        </div>

        <div class="row">
            <!--<div class="col-md-4">
                <?= render_select('slots_id', $slots, ['slots_id', 'slots_name'], _l('slots') . '*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']) ?>
            </div>-->

            <div class="col-md-4">
                <?= render_select('treatment_id', $treatment, ['treatment_id', 'treatment_name'], _l('treatment') . '*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']) ?>
            </div>
			
			<div class="col-md-4">
                <?= render_select('appointment_type_id', $appointment_type, ['appointment_type_id', 'appointment_type_name'], _l('appointment_type') . '*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']) ?>
            </div>

            <div class="col-md-4">
                <?= render_select('consultation_fee_id', $consultation_fee, ['consultation_fee_id', 'consultation_fee_name'], _l('consultation_fee') . '*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']) ?>
            </div>
			
            <div class="col-md-4">
			<label class="form-label"><?= _l('paying_amount') ?></label>
               <input type="text" class="form-control" id="paying_amount" name="paying_amount" placeholder="<?= _l('paying_amount') ?>">
			   <small id="paying_amount_error" class="text-danger" style="display: none;"></small>
            </div>
			 
        
		 <div class="col-md-4">
			<label class="form-label"><?= _l('due_amount') ?></label>
               <input type="text" class="form-control" id="due_amount" name="due_amount" placeholder="<?= _l('due_amount') ?>" readonly>
            </div>
		 <div class="col-md-4">
			<label class="form-label"><?= _l('payment_mode') ?></label>
              <select class="selectpicker" name="paymentmode" data-width="100%"
                            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                            <option value=""></option>
                            <?php foreach ($payment_modes as $mode) { ?>
                            
                            <option value="<?= e($mode['id']); ?>">
                                <?= e($mode['name']); ?>
                            </option>
                            
                            <?php } ?>
                        </select>
            </div>
		<div class="col-md-4">
			<label class="form-label"><?= _l('remarks') ?></label>
			<textarea class="form-control" name="remarks" placeholder="<?= _l('enter_remarks') ?>"><?= htmlspecialchars($patient['remarks'] ?? '') ?></textarea>
		</div>
		</div>
    </div>

    <div class="text-center mt-4">
        <button type="submit" name="Save" value="Save" class="btn btn-success"><?= _l('save') ?></button>
        <!--<a href="<?= admin_url('patient') ?>" class="btn btn-white"><?= _l('cancel') ?></a>-->
    </div>
</form>
<?php } ?>

</div>
</div>
</div>
</div>
</div>
</div>

<?php init_tail(); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // When consultation_fee is selected
    $('#consultation_fee_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var feeText = selectedOption.text();
        var feeValue = parseFloat(feeText.replace(/[^\d.]/g, '')) || 0;

        $('#due_amount').val(feeValue.toFixed(2));
        //$('#paying_amount').val(0);
    });

    // When paying_amount is typed
    $('#paying_amount').on('input', function() {
        var feeText = $('#consultation_fee_id option:selected').text();
        var totalFee = parseFloat(feeText.replace(/[^\d.]/g, '')) || 0;
        var payingAmount = parseFloat($(this).val()) || 0;

        if (payingAmount > totalFee) {
            alert_float('danger', '<?php echo _l('paying_amount_cannot_exceed_due_amount'); ?>');
            $(this).val('');
            $('#due_amount').val(totalFee.toFixed(2));
            return;
        }

        var due = totalFee - payingAmount;
        $('#due_amount').val(due.toFixed(2));
    });
});
</script>

<script>
$(document).ready(function () {
    $('#contact_number_search').on('input', function () {
        let query = $(this).val();
        if (query.length >= 3) {
            $.ajax({
                url: admin_url + 'client/client/search_contact_number',
                type: 'POST',
                data: { contact: query },
                dataType: 'json',
                success: function (response) {
                    $('#contact_search_results').html(response.results).show();
                }
            });
        } else {
            $('#contact_search_results').hide();
        }
    });

    // Optional: hide dropdown on click outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#contact_number_search, #contact_search_results').length) {
            $('#contact_search_results').hide();
        }
    });
});
</script>

</body>
</html>
