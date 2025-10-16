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
    <?php echo _l('enquiry_form');?>
    <hr>
</div>

<?php
//print_r($master_data);
extract($master_data);
//print_r($branch);
?>
<br>
<?php 
$form_submitted = $this->input->post('check'); 

?>

<!-- First Form (Search by Contact) -->
<form method="post" action="" id="get-patient-form" style="margin-top: -40px; <?= $form_submitted ? 'display: none;' : '' ?>">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
    <div class="col-md-4"></div>
    <div class="d-flex align-items-center">
        <div class="col-md-3 me-2">
            <label class="form-label"><?= _l('contact_number') ?>*</label>
            <input type="text" class="form-control" name="contact_number" placeholder="<?= _l('enter_contact_number') ?>" value="<?= htmlspecialchars($patient['contact_number'] ?? '') ?>" required>
        </div>
        <div style="display: flex; align-items: center; height: 80px;">
            <div>
                <button type="submit" name="check" value="check" class="btn btn-success"><?= _l('submit') ?></button>
            </div>
        </div>
    </div>
</form>
<?php

	if($patients_count == 1){
		
	?>

<!-- Second Form (Full Registration) -->
<?php if ($form_submitted): 
    $patient = (array) $patient_data;
	$patient = $patient[0];
    if($patient){ ?>
        <form method="post" action="<?= admin_url('client/update_client'); ?>">
            <input type="hidden" name="userid" value="<?php echo $patient['userid'];?>">
    <?php } else { ?>
        <form method="post" action="<?= admin_url('client/save_client'); ?>">
    <?php } 
	?>
	
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />

    <!--<div class="form-section">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label"><?= _l('contact_number') ?>*</label>
                <input type="text" class="form-control" name="contact_number" placeholder="<?= _l('enter_contact_number') ?>"
                    value="<?= isset($patient['phonenumber']) ? htmlspecialchars($patient['phonenumber']) : htmlspecialchars($this->input->post('contact_number')) ?>" required readonly>
            </div>

            <div class="col-md-4">
			
			<?= render_select('enquiry_type_id', $enquiry_type, ['enquiry_type_id', ['enquiry_type_name']], _l('enquiry_type').'*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex')]) ?>
			
            </div>

            <div class="col-md-4">
			
			<?= render_select('patient_response_id', $patient_response, ['patient_response_id', ['patient_response_name']], _l('patient_response').'*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex')]) ?>
                
            </div>
        </div>
    </div>-->

    <div class="form-section" style="margin-top: -50px">
        <div class="section-title"><?= _l('patient_information') ?></div>
		
        <div class="row">
            <div class="col-md-4">
                <label class="form-label"><?= _l('patient_name') ?>*</label>
                <div class="mb-3" style="display: flex; gap: 0;">
                    <div style="flex: 1; max-width: 22%;">
                        <select class="form-control" name="salutation">
                            <option value="Mr." <?= ($patient['salutation'] ?? '') == 'Mr.' ? 'selected' : '' ?>>Mr.</option>
                            <option value="Mrs." <?= ($patient['salutation'] ?? '') == 'Mrs.' ? 'selected' : '' ?>>Mrs.</option>
                            <option value="Ms." <?= ($patient['salutation'] ?? '') == 'Ms.' ? 'selected' : '' ?>>Ms.</option>
                        </select>
                    </div>
                    <div style="flex: 1; max-width: 49%;">
                        <input type="text" class="form-control" name="company" placeholder="<?= _l('enter_patient_name') ?>" value="<?= htmlspecialchars($patient['company'] ?? '') ?>" required>
                    </div>
                    <div style="flex: 1; max-width: 29%;">
                        <select class="form-control" name="gender">
                            <option value="Male" <?= ($patient['gender'] ?? '') == 'Male' ? 'selected' : '' ?>><?= _l('male') ?></option>
                            <option value="Female" <?= ($patient['gender'] ?? '') == 'Female' ? 'selected' : '' ?>><?= _l('female') ?></option>
                            <option value="Other" <?= ($patient['gender'] ?? '') == 'Other' ? 'selected' : '' ?>><?= _l('other') ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label"><?= _l('age') ?>*</label>
                <input type="text" class="form-control" name="age" placeholder="<?= _l('enter_age') ?>" value="<?= htmlspecialchars($patient['age'] ?? '') ?>" required>
            </div>

            <div class="col-md-4">
                <?php
                    $marital_status_options = [
                        ['id' => 'Single', 'name' => _l('single')],
                        ['id' => 'Married', 'name' => _l('married')],
                        ['id' => 'Divorced', 'name' => _l('divorced')],
                        ['id' => 'Widowed', 'name' => _l('widowed')],
                    ];
                    echo render_select('marital_status', $marital_status_options, ['id', ['name']], _l('marital_status') . ' *', '', [
                        'data-none-selected-text' => _l('dropdown_non_selected_tex'),
                        'required' => 'required'
                    ]);
                ?>
            </div>

            <div class="col-md-4">
                <label class="form-label"><?= _l('email') ?></label>
                <input type="text" class="form-control" name="email_id" placeholder="<?= _l('enter_email') ?>">
            </div>

            <div class="col-md-4 mt-3">
                <label class="form-label"><?= _l('city_name') ?>*</label>
                <input type="text" class="form-control" name="city" placeholder="<?= _l('enter_city') ?>" value="<?= htmlspecialchars($patient['city'] ?? '') ?>" required>
            </div>
			<div class="col-md-4">
                <label class="form-label"><?= _l('contact_number') ?>*</label>
                <input type="text" class="form-control" name="contact_number" placeholder="<?= _l('enter_contact_number') ?>"
                    value="<?= isset($patient['phonenumber']) ? htmlspecialchars($patient['phonenumber']) : htmlspecialchars($this->input->post('contact_number')) ?>" required readonly>
            </div>
            <div class="col-md-4 mt-3">
                <label class="form-label"><?= _l('whatsapp_number') ?></label>
                <input type="text" class="form-control" name="whatsapp_number" placeholder="<?= _l('enter_whatsapp_number') ?>" value="<?= htmlspecialchars($patient['whatsapp_number'] ?? '') ?>">
            </div>

            <div class="col-md-4 mt-3">
			<br>
                <label class="form-label"><?= _l('alternative_number1') ?></label>
                <input type="text" class="form-control" name="alt_number1" placeholder="<?= _l('enter_alt_number1') ?>" value="<?= htmlspecialchars($patient['alt_number1'] ?? '') ?>">
            </div>

            <div class="col-md-4 mt-3">
			<br>
                <label class="form-label"><?= _l('alternative_number2') ?></label>
                <input type="text" class="form-control" name="alt_number2" placeholder="<?= _l('enter_alt_number2') ?>" value="<?= htmlspecialchars($patient['alt_number2'] ?? '') ?>">
            </div>

            <div class="col-md-4 mt-3">
			<br>
                <?= render_select('patient_priority_id', $patient_priority, ['patient_priority_id', ['patient_priority_name']], _l('patient_priority'), '', ['data-none-selected-text' => _l('dropdown_non_selected_tex')]) ?>
            </div>

            <div class="col-md-4">
                <label class="form-label"><?= _l('area') ?></label>
                <input type="text" class="form-control" name="area" placeholder="<?= _l('enter_area') ?>" value="<?= htmlspecialchars($patient['area'] ?? '') ?>">
            </div>

            <div class="col-md-4 mt-3">
                <label class="form-label"><?= _l('pincode') ?></label>
                <input type="text" class="form-control" name="pincode" placeholder="<?= _l('enter_pincode') ?>" value="<?= htmlspecialchars($patient['pincode'] ?? '') ?>">
            </div>

            <div class="col-md-4 mt-3">
            <?php
                echo render_select(
                    'default_language[]', // Name (with multiple)
                    array_map(function($lang) { return ['lang_key' => $lang, 'lang_name' => ucfirst($lang)]; }, $this->app->get_available_languages()), // Options array
                    ['lang_key', ['lang_name']], // Keys
                    'select_language', // Label (will use _l('select_language'))
                    isset($client) ? explode(',', $client->default_language) : [], // Selected values (as array)
                    ['multiple' => true] // Extra attributes
                );
                ?>
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="section-title"><?= _l('appointment_information') ?></div>
        <div class="row">
            <div class="col-md-4">
                <?= render_select('groupid', $branch, ['id', 'name'], _l('branch') . '*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']) ?>
            </div>

            <div class="col-md-4">
                <label class="form-label"><?= _l('appointment_date') ?>*</label>
                <input type="datetime-local" class="form-control" name="appointment_date" placeholder="YYYY-MM-DD" value="<?= htmlspecialchars($patient['appointment_date'] ?? '') ?>" required>
            </div>

            <div class="col-md-4">
                <?= render_select('assign_doctor_id', $assign_doctor, ['staffid', ['firstname', 'lastname']], _l('assign_doctor') . '*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']) ?>
            </div>
        </div>

        <div class="row mt-3">
            <!--<div class="col-md-4">
                <?= render_select('slots_id', $slots, ['slots_id', 'slots_name'], _l('slots') . '*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']) ?>
            </div>-->

            <div class="col-md-4">
                <?= render_select('treatment_id', $treatment, ['treatment_id', 'treatment_name'], _l('treatment') . '*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']) ?>
            </div>

            <div class="col-md-4">
                <?= render_select('consultation_fee_id', $consultation_fee, ['consultation_fee_id', 'consultation_fee_name'], _l('consultation_fee') . '*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']) ?>
            </div>
			 <div class="col-md-4">
                <label class="form-label"><?= _l('remarks') ?></label>
                <textarea class="form-control" name="remarks" placeholder="<?= _l('enter_remarks') ?>"><?= htmlspecialchars($patient['remarks'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="row mt-3">
            <!--<div class="col-md-4">
                <?= render_select('patient_source_id', $patient_source, ['id', 'name'], _l('patient_source') . '*', '', ['data-none-selected-text' => _l('dropdown_non_selected_tex'), 'required' => 'required']) ?>
            </div>-->

           

            <!--<div class="col-md-4">
                <label class="form-label"><?= _l('next_calling_date') ?></label>
                <input type="date" class="form-control" name="next_calling_date" placeholder="YYYY-MM-DD" value="<?= htmlspecialchars($patient['next_calling_date'] ?? '') ?>">
            </div>-->
        </div>
    </div>

    <div class="text-center mt-4">
        <button type="submit" name="Save" value="Save" class="btn btn-success"><?= _l('save') ?></button>
        <!--<a href="<?= admin_url('patient') ?>" class="btn btn-white"><?= _l('cancel') ?></a>-->
    </div>
</form>
<?php endif; ?>

<?php
}else if($patients_count > 1){
	//print_r($patient_data);
	?>
	 <style>
     .table-container {
      width: 80%;
      margin: 2px auto;
    }

    .add-button {
      float: right;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table, th, td {
      border: 1px solid #333;
    }

    th, td {
      padding: 10px;
      text-align: center;
    }

    th {
      background-color: #f2f2f2;
    }
  </style>
		<div class="table-container" style="margin-top: -40px">
  <button class="btn add-button btn-success">+ Add</button>

		<table border="1" cellpadding="10" cellspacing="0">
		  <thead>
			<tr>
			  <th>MR No</th>
			  <th>Name</th>
			  <th>Gender</th>
			  <th>Action</th>
			</tr>
		  </thead>
		  <tbody>
		  <?php
		  foreach($patient_data as $p){
		  ?>
			<tr>
			  <td><?php echo $p['userid'];?></td>
			  <td><?php echo $p['company'];?></td>
			  <td>Male</td>
			  <td>
				<button class="btn btn-success">Book Appointment</button>
				<button class="btn btn-warning">Edit</button>
				<button class="btn btn-danger">Delete</button>
			  </td>
			</tr>
			<?php
		  }
		  ?>
		  </tbody>
		</table>
		</div>
	<?php
}
?>
</div>
</div>
</div>
</div>
</div>
</div>

<?php init_tail(); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
