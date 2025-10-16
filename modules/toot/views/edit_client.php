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
        border-bottom: 1px solid #6a1b9a;
        margin-bottom: 30px;
        font-size: 22px;
        color: #6a1b9a;
        font-weight: 600;
        padding-bottom: 10px;
    }
    .section-title {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 20px;
        color: #6a1b9a;
        margin: 5px 0 20px;
    }
    .section-title::before,
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #6a1b9a;
        margin: 0 15px;
    }
    .btn-purple {
        background-color: #6a1b9a;
        color: white;
    }
</style>

<div id="wrapper">
<div class="content">
<div class="row">
<div class="col-md-12">
<div class="panel_s">
<div class="panel-body">
<div class="clearfix"></div>

<div style="background-color: #6f2dbd; color: white; font-size: 24px; font-weight: bold; padding: 10px 20px; text-align: left; border-radius: 4px 4px 0 0;">
    Enquiry Form
</div>
<?php
//print_r($master_data);
extract($master_data);
//print_r($client_data);
//print_r($enquiry_types);
$patient = json_decode(json_encode($client_data), true);
if($apponitment_data){
	$apponitment_data = $apponitment_data[0];
}

$apponitment_data = json_decode(json_encode($apponitment_data), true);
?>
<form method="post" action="<?= admin_url('client/update_client'); ?>">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
    <input type="hidden" name="userid" value="<?php echo $patient['userid']; ?>">
    <div class="form-section">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label"><?= _l('contact_number'); ?>*</label>
                <input type="text" class="form-control" name="contact_number" placeholder="<?= _l('enter_contact_number'); ?>" value="<?= htmlspecialchars($patient['phonenumber'] ?? '') ?>" required>
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="section-title"><?= _l('section_title_patient_information'); ?></div>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label"><?= _l('patient_name'); ?>*</label>
                <div class="mb-3" style="display: flex; gap: 0;">
                    <div style="flex: 1; max-width: 22%;">
                        <select class="form-control" name="salutation">
                            <option value="Mr." <?= ($patient['salutation'] ?? '') == 'Mr.' ? 'selected' : '' ?>>Mr.</option>
                            <option value="Mrs." <?= ($patient['salutation'] ?? '') == 'Mrs.' ? 'selected' : '' ?>>Mrs.</option>
                            <option value="Ms." <?= ($patient['salutation'] ?? '') == 'Ms.' ? 'selected' : '' ?>>Ms.</option>
                        </select>
                    </div>
                    <div style="flex: 1; max-width: 49%;">
                        <input type="text" class="form-control" name="company" placeholder="<?= _l('enter_patient_name'); ?>" value="<?= htmlspecialchars($patient['company'] ?? '') ?>" required>
                    </div>
                    <div style="flex: 1; max-width: 29%;">
                        <select class="form-control" name="gender">
                            <option value="Male" <?= ($patient['gender'] ?? '') == 'Male' ? 'selected' : '' ?>><?= _l('male'); ?></option>
                            <option value="Female" <?= ($patient['gender'] ?? '') == 'Female' ? 'selected' : '' ?>><?= _l('female'); ?></option>
                            <option value="Other" <?= ($patient['gender'] ?? '') == 'Other' ? 'selected' : '' ?>><?= _l('other'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label"><?= _l('age'); ?>*</label>
                <input type="text" class="form-control" name="age" placeholder="<?= _l('enter_age'); ?>" value="<?= htmlspecialchars($patient['age'] ?? '') ?>" required>
            </div>

            <div class="col-md-4">
                <?php
                    $marital_status_options = [
                        ['id' => 'Single', 'name' => _l('single')],
                        ['id' => 'Married', 'name' => _l('married')],
                        ['id' => 'Divorced', 'name' => _l('divorced')],
                        ['id' => 'Widowed', 'name' => _l('widowed')],
                    ];
                    $selected_status = $patient['marital_status'] ?? "";
                    $label_status = _l('marital_status') . ' *';

                    echo render_select(
                        'marital_status',
                        $marital_status_options,
                        ['id', ['name']],
                        $label_status,
                        $selected_status,
                        [
                            'data-none-selected-text' => _l('dropdown_non_selected_tex'),
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>

            <div class="col-md-4">
                <label class="form-label"><?= _l('email'); ?></label>
                <input type="text" class="form-control" name="email_id" value="<?= htmlspecialchars($patient['email_id'] ?? '') ?>" placeholder="<?= _l('enter_email'); ?>">
            </div>

            <div class="col-md-4 mt-3">
                <label class="form-label"><?= _l('city'); ?>*</label>
                <input type="text" class="form-control" name="city" placeholder="<?= _l('enter_city'); ?>" value="<?= htmlspecialchars($patient['city'] ?? '') ?>" required>
            </div>

            <div class="col-md-4 mt-3">
                <label class="form-label"><?= _l('whatsapp_number'); ?></label>
                <input type="text" class="form-control" name="whatsapp_number" placeholder="<?= _l('enter_whatsapp_number'); ?>" value="<?= htmlspecialchars($patient['whatsapp_number'] ?? '') ?>">
            </div>
            <div class="col-md-4 mt-3">
				<br>
                <?php
$selected_languages = isset($patient['default_language']) 
    ? array_map('trim', explode(',', $patient['default_language'])) 
    : [];

echo render_select(
    'default_language[]',
    array_map(function($lang) {
        return ['lang_key' => $lang, 'lang_name' => ucfirst($lang)];
    }, $this->app->get_available_languages()),
    ['lang_key', ['lang_name']],
    'select_language',
    $selected_languages,
    ['multiple' => true]
);
?>


            </div>

            <div class="col-md-4 mt-3">
			<br>
                <label class="form-label"><?= _l('alternative_number1'); ?></label>
                <input type="text" class="form-control" name="alt_number1" placeholder="<?= _l('enter_alt_number1'); ?>" value="<?= htmlspecialchars($patient['alt_number1'] ?? '') ?>">
            </div>

            <div class="col-md-4 mt-3">
			<br>
                <label class="form-label"><?= _l('alternative_number2'); ?></label>
                <input type="text" class="form-control" name="alt_number2" placeholder="<?= _l('enter_alt_number2'); ?>" value="<?= htmlspecialchars($patient['alt_number2'] ?? '') ?>">
            </div>

            <div class="col-md-4">
			<br>
                <label class="form-label"><?= _l('area'); ?></label>
                <input type="text" class="form-control" name="area" placeholder="<?= _l('enter_area'); ?>" value="<?= htmlspecialchars($patient['address'] ?? '') ?>">
            </div>

            <div class="col-md-4 mt-3">
			<br>
                <label class="form-label"><?= _l('pincode'); ?></label>
                <input type="text" class="form-control" name="pincode" placeholder="<?= _l('enter_pincode'); ?>" value="<?= htmlspecialchars($patient['pincode'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-purple"><?= _l('save'); ?></button>
        <a href="<?= admin_url('patient') ?>" class="btn btn-danger"><?= _l('cancel'); ?></a>
    </div>
</form>


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
