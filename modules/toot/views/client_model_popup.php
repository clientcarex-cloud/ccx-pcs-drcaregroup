<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="client-model-auto" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
    <style>
.dataTables_filter {
  text-align: right !important;
}
.modal-body {
  max-height: 90vh;
  overflow-y: auto;
}

        
/* Scoped styles for table and cell centering */
.medicine-table-container {
  margin-top: 20px;
}

.medicine-table {
  width: 100%;
  border-collapse: collapse;
}

.medicine-table th, .medicine-table td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: left;
  vertical-align: middle; /* Ensures vertical alignment in the cell */
}

.medicine-table th {
  background-color: #f8f9fa;
}

.medicine-table td {
  vertical-align: middle; /* Vertically centers content in td */
}

.medicine-select-container {
  position: relative;
  width: 100%;
}

.medicine-select-input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.medicine-select-options {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  max-height: 150px;
  overflow-y: auto;
  background-color: white;
  border: 1px solid #ccc;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  display: none;
  z-index: 999;
}

.medicine-select-option {
  padding: 8px;
  cursor: pointer;
}

.medicine-select-option:hover {
  background-color: #f1f1f1;
}

.medicine-btn {
  padding: 6px 12px;
  background-color: #007bff;
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 4px;
}

.medicine-btn:hover {
  background-color: #0056b3;
}

.medicine-textarea {
  width: 100%;
  padding: 4px;
  box-sizing: border-box;
  border-radius: 4px;
  border: 1px solid #ccc;
}
</style>
<style>
    /* Container for Prescription Form */
    .medicine-table-container {
        margin-top: 20px;
        border: 1px solid #ccc;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    /* Prescription Table */
    .medicine-table {
        width: 100%;
        border-collapse: collapse;
    }

    /* Table Header Styling */
    .medicine-table th {
        padding: 12px;
        text-align: left;
        background-color: #007bff;  /* A blue color for the table header */
        color: #fff;                /* White text color */
        font-size: 16px;
        font-weight: bold;
    }

    .medicine-table tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Table Data Styling */
    .medicine-table td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
    }

    /* Heading Styling */
    .patient-section-title h4 {
        color: #343a40;         /* Dark gray for headings */
        font-size: 1.5rem;       /* Slightly larger font size */
        font-weight: 600;        /* Bold text for headings */
        margin-bottom: 20px;
    }

    /* Form Buttons */
    .form-actions {
        margin-top: 20px;
        text-align: right;
    }

    .form-actions button {
        margin-left: 10px;
    }

    /* Add Medicine Button */
    .medicine-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .medicine-btn {
        background-color: #28a745;  /* Green for the "Add Medicine" button */
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .medicine-btn:hover {
        background-color: #218838;  /* Darker green on hover */
    }

    /* Prescription Form Layout */
    .medicine-table th, .medicine-table td {
        padding: 10px;
        border: 1px solid #ddd;
    }

    .medicine-table-container h4 {
        margin-bottom: 20px;
    }

    /* Prescription Form Input Styling */
    .medicine-table input, .medicine-table textarea {
        width: 100%;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .medicine-table select {
        width: 100%;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    /* Action Button Styling */
    .medicine-btn {
        padding: 8px 15px;
        background-color: #007bff;
        color: white;
        border-radius: 5px;
        font-size: 14px;
    }

    .medicine-btn:hover {
        background-color: #0056b3;
    }
</style>
<style>
          .patient-section-title {
            font-weight: bold;
            background: #f2f9ff;
            font-size: 16px;
            color: #fff;
            text-align: center;
            color: #007bff; 
            padding: 8px; 
            border-radius: 5px; 
            border: 1px solid #ccc;
            margin: 0;
          }

          .patient-info-row {
            margin-bottom: 10px;
          }

          .patient-label {
            font-weight: 600;
            color: #555;
          }

          .patient-value {
            color: #000;
          }

          .note-text {
            color: #007bff;
            font-weight: 500;
          }
          .blurred {
  filter: blur(3px);
  transition: filter 0.3s;
}

          .patient-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-size: 14px;
  }

  .patient-table td {
    border: 1px solid #ccc;
    padding: 8px 12px;
    vertical-align: top;
  }

  .patient-label {
    !font-weight: bold;
    color: #333;
    display: inline-block;
    min-width: 150px;
  }

  .text-success {
    color: green;
  }
  


/* Card Header Styling */
.card-header {
    background-color: #f1f1f1; /* Light gray background */
    color: #333; /* Dark text color for better contrast */
    !padding: 12px 20px; /* Padding on both sides */
    !border: 1px solid #ccc; /* Light gray border */
    cursor: pointer; /* Pointer cursor on hover */
    display: flex;
    justify-content: space-between; /* Space between title and icon */
    align-items: center;
    !border-radius: 5px 5px 0 0; /* Rounded corners at the top */
    transition: background-color 0.3s ease; /* Smooth background color change */
}

/* Card Header Hover Effect */
.card-header:hover {
    !background-color: #e0e0e0; /* Slightly darker gray when hovering */
}

/* Accordion Header Text */
.card-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600; /* Slightly bolder text */
}

/* Arrow Icon Styling */
.toggle-icon {
    transition: transform 0.3s; /* Smooth rotation for the arrow */
    font-size: 18px;
   
}

/* When the section is open, rotate the icon */
.toggle-icon.open {
    transform: rotate(180deg);
}

/* Card Body Styling */
.card-body {
    padding: 20px;
    !border-top: 1px solid #e0e0e0; /* Light gray border at the top */
    background-color: #fafafa; /* Very light gray background for card content */
}

/* General Form Layout */
.mtop10 {
    margin-top: 10px;
}

.mtop20 {
    margin-top: 20px;
}

.form-actions {
    margin-top: 20px;
    text-align: right;
}

.form-actions .btn {
    margin-left: 10px;
}

/* Adjust for Card Body if needed */
.card {
    margin-bottom: 15px;
}

/* Styling for Form Elements */
select.form-control, input.form-control, textarea.form-control {
    !border-radius: 5px;
    !border: 1px solid #ccc; /* Light gray border for form elements */
    padding: 10px;
}


  </style>

      <!-- Modal Header -->
      <div class="modal-header d-flex justify-content-between align-items-center">
      <div class="row align-items-center">
			<div class="col-md-10">
				<h4 class="modal-title m-0" style="display: inline-block;">
					#<?= $client->userid; ?> - <?= $client->company; ?>
				</h4>
			</div>
			<div class="col-md-2 text-end" style="display: flex; align-items: center; justify-content: flex-end; gap: 5px;">
			<?PHP
			if (staff_can('edit', 'customers')) {
			?>
				<a href="<?= admin_url('client/edit_client/'.$client->userid); ?>">
					<button type="button" class="btn btn-warning btn-sm edit-button">
						<i class="fas fa-pencil-alt"></i> Edit
					</button>
				</a>
			<?PHP
			}
			?>
				<button type="button" class="btn btn-secondary btn-sm close close-button" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		</div>

      <!-- Modal Body -->
      <div class="modal-body">
        <div class="top-lead-menu">
          <div class="horizontal-scrollable-tabs tw-mb-10">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
              <ul class="nav nav-tabs nav-tabs-horizontal nav-tabs-segmented" role="tablist">
                <?php
                  if (staff_can('view_overview', 'customers')) {
                ?>
                <li role="presentation" class="active">
                  <a href="#tab_overview" aria-controls="tab_overview" role="tab" data-toggle="tab">
                    <i class="fa-solid fa-circle-info menu-icon"></i>
                    <?= _l('overview'); ?>
                  </a>
                </li>
                <?php
                  }if (staff_can('view_prescription', 'customers')) {
                ?>
                <li role="presentation">
                  <a href="#tab_prescription" aria-controls="tab_prescription" role="tab" data-toggle="tab">
                    <i class="fa-solid fa-prescription-bottle-medical menu-icon"></i>
                    <?= _l('prescription'); ?>
                  </a>
                </li>
                <?php
                  }if (staff_can('view_casesheet', 'customers')) {
                ?>
                <li role="presentation">
                  <a href="#tab_casesheet" aria-controls="tab_casesheet" role="tab" data-toggle="tab">
                  <i class="fa-solid fa-notes-medical menu-icon"></i>
                    <?= _l('casesheet'); ?>
                  </a>
                </li>
                <?php
                  }if (staff_can('view_appointments', 'customers')) {
                ?>
                <li role="presentation">
                  <a href="#tab_visits" aria-controls="tab_visits" role="tab" data-toggle="tab">
                    <i class="fa-solid fa-calendar-check menu-icon"></i>
                    <?= _l('visits'); ?>
                  </a>
                </li>
                <?php
                  }if (staff_can('view_payments', 'customers')) {
                ?>
                <li role="presentation">
                  <a href="#tab_payments" aria-controls="tab_payments" role="tab" data-toggle="tab">
                    <i class="fa-solid fa-credit-card menu-icon"></i>
                    <?= _l('payments'); ?>
                  </a>
                </li>
                <?php
                  }if (staff_can('view_feedback', 'customers')) {
                ?>
                <li role="presentation">
                  <a href="#tab_feedback" aria-controls="tab_feedback" role="tab" data-toggle="tab">
                    <i class="fa-regular fa-comments menu-icon"></i>
                    <?= _l('feedback'); ?>
                  </a>
                </li>
                <?php
                  }if (staff_can('view_call_log', 'customers')) {
                ?>
                <li role="presentation">
                  <a href="#tab_calls" aria-controls="tab_calls" role="tab" data-toggle="tab">
                    <i class="fa-solid fa-phone menu-icon"></i>
                    <?= _l('call_logs'); ?>
                  </a>
                </li>
                <?php
                  }if (staff_can('view_activity_log', 'customers')) {
                ?>
                <li role="presentation">
                  <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                    <i class="fa-solid fa-list menu-icon"></i>
                    <?= _l('activity_logs'); ?>
                  </a>
                </li>
                  <?php
                  }
                  ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
		 <?php
		  if (staff_can('view_overview', 'customers')) {
		?>
          <div role="tabpanel" class="tab-pane active" id="tab_overview">
          
        
        <div class="container-fluid">
          <!-- Section 1: Patient Details -->
          <div class="patient-section-title"><?= _l('patient_details'); ?></div>
        
          <table class="patient-table"> 
          <tr>
            <td><span class="patient-value"><strong><?= _l('mr_no'); ?>:</strong> <?php echo $customer_new_fields->mr_no;?></span></td>
            <td><span class="patient-value"><strong><?= _l('city_state_country'); ?>:</strong> <?php echo $client->city;?>, <?php echo $client->state;?></span></td>
            
          </tr>
          <tr>
            <td><span class="patient-value"><strong><?= _l('patient_name'); ?>:</strong> <?php echo $client->company;?></span></td>
            <td><span class="patient-value"><strong><?= _l('pincode'); ?>:</strong> <?php echo $client->zip;?></span></td>
            
          </tr>
          <tr>
            <td><span class="patient-value"><strong><?= _l('age_gender'); ?>:</strong> <?php echo $customer_new_fields->age?>, <?php echo $customer_new_fields->gender?></span></td>
            <td><span class="patient-value"><strong><?= _l('registration_date'); ?>:</strong> <?php echo _d($client->datecreated);?></span></td>
            
          </tr>
          <tr>
            <td><span class="patient-value"><strong><?= _l('contact_number'); ?>:</strong> <?php echo $client->phonenumber;?></span></td>
            <td><span class="patient-value"><strong><?= _l('marital_status'); ?>:</strong> <?php echo $client->marital_status;?> </span></td>
            
          </tr>
          <tr>
            <td><span class="patient-value"><strong><?= _l('email_id'); ?>:</strong> <?php echo $client->email_id;?></span></td>
            <td><span class="patient-value"><strong><?= _l('language_known'); ?>:</strong> <?php echo $client->default_language;?></span></td>
            
          </tr>
          <tr>
            <td><span class="patient-value"><strong><?= _l('area'); ?>:</strong> <?php echo $client->area;?></span></td>
            <td><span class="patient-value"><strong><?= _l('lead_source'); ?>:</strong></span></td>
            
          </tr>
          <tr>
            <td><span class="patient-value"><strong><?= _l('address'); ?>:</strong> <?php echo $client->address;?></span></td>
            <td><span class="patient-value"><strong><?= _l('patient_type'); ?>:</strong> </span></td>
            
          </tr>
          
        </table>

          <!-- Section 2: Patient Payment Summary -->
          <div class="patient-section-title mt-4"><?= _l('patient_treatment_summary'); ?></div>
          <br>
          <?php
          //print_r($invoices);
          foreach($invoices as $invoice){
            $patient_package = get_patient_package($invoice['id']);
            if(count($patient_package) > 0){
              $total_package = 0;
              foreach($patient_package as $package){
                $total_package += $package['qty'] * $package['rate'];
              }
              echo '<b>#' . $invoice['formatted_number'] . '</b>';
              ?>
              <table class="patient-table">
              <tr>
              <td>
                  <span class="patient-value">
                    <strong><?= _l('treatment'); ?>:</strong> 
                    <?php 
                      echo !empty($invoice['recurring']) && $invoice['recurring'] != 0 
                        ? $package['description']
                        : '-'; 
                    ?>
                  </span>
                </td>
                <td>
                  <span class="patient-value">
                    <strong><?= _l('treatment_duration_period'); ?>:</strong> 
                    <?php 
                      echo !empty($invoice['recurring']) && $invoice['recurring'] != 0 
                        ? $invoice['recurring'] . ' ' . $invoice['recurring_type'] 
                        : '-'; 
                    ?>
                  </span>
                </td>
                <td>
                  <span class="patient-value">
                    <strong><?= _l('treatment_completed_duration'); ?>:</strong> 
                    <?php 
                      echo !empty($invoice['recurring']) && $invoice['recurring'] != 0 
                        ? get_duration_from_date($invoice['date']) 
                        : '-'; 
                    ?>
                  </span>
                </td>
                
              </tr>
              <tr>
              <td>
                  <span class="patient-value">
                    <strong><?= _l('remaining_duration'); ?>:</strong> 
                    <?php 
                      echo !empty($invoice['recurring']) && $invoice['recurring'] != 0 
                        ? get_invoice_remaining_duration($invoice['date'], $invoice['recurring'], $invoice['recurring_type']) 
                        : '-'; 
                    ?>
                  </span>
                </td>
                <td>
                  <span class="patient-value">
                    <strong><?= _l('total_package'); ?>:</strong> 
                    <?= e(app_format_money_custom($total_package, $invoice['currency'])); ?>
                  </span>
                </td>
                <td>
                  <span class="patient-value">
                    <strong><?= _l('total_paid'); ?>:</strong> 
                    <?php 
                      $total_paid = 0;
                      foreach ($invoice_payments as $payments) {
                        if ($payments['invoiceid'] == $invoice['id']) {
                          $total_paid += $payments['amount'];
                        }
                      }
                      echo e(app_format_money_custom($total_paid, $invoice['currency']));
                    ?>
                  </span>
                </td>
                
              </tr>
              <tr>
              <td>
                  <span class="patient-value">
                    <strong><?= _l('total_dues'); ?>:</strong> 
                    <?= e(app_format_money_custom(($total_package - $total_paid), $invoice['currency'])); ?>
                  </span>
                </td>
                <td><span class="patient-value"><strong><?= _l('cob_with_refund'); ?>:</strong> -</span></td>
                <td><span class="patient-value"><strong><?= _l('normal_refund'); ?>:</strong> -</span></td>
                
              </tr>
            </table>

    <?php
  }
}
?>
</div>



          </div>
<?PHP
}if (staff_can('view_prescription', 'customers')) {
?>
          <div role="tabpanel" class="tab-pane" id="tab_prescription">
          <?php
        if(staff_can('create_prescription', 'customers')){
        ?>
          <button class="btn btn-primary btn-sm" onclick="togglePrescriptionForm()" style="float: right; margin-top: 6px; margin-right: 3px;">
            <?= _l('add_prescription'); ?>
          </button>
      <?php
        }?>
  <!-- Title Section -->
  <div class="patient-section-title mt-4"><?= _l('doctor_prescription'); ?></div>

  <br>

  <!-- Prescription Form Section -->
  <div class="medicine-table-container" id="prescription-form" style="display:none;">
    <div class="medicine-actions" style="margin-top:-15px;margin-bottom: 5px;">
      <button type="button" class="medicine-btn" onclick="addMedicineRow()" style="background-color: #28a745;">
        <?= _l('add_more_medicines'); ?>
      </button>
    </div>

    <form id="prescriptionForm">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" id="csrf_token">
      <input type="hidden" name="patientid" value="<?= $client->userid; ?>">

      <table class="medicine-table" id="medicineTable">
        <thead>
          <tr>
            <th><?= _l('medicine_name'); ?></th>
            <th><?= _l('potency'); ?></th>
            <th><?= _l('dose'); ?></th>
            <th><?= _l('timings'); ?></th>
            <th><?= _l('remarks'); ?></th>
            <th><?= _l('action'); ?></th>
          </tr>
        </thead>
        <tbody id="medicineBody">
          <!-- Rows will be dynamically added here -->
        </tbody>
      </table>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= _l('save_prescription'); ?></button>
        <button type="button" class="btn btn-secondary" onclick="togglePrescriptionForm()"><?= _l('cancel'); ?></button>
      </div>
    </form>
  </div>

    <br>

    <!-- Prescription List -->
    <table id="doctor-prescription-table" class="table table-bordered table-striped" style="table-layout: fixed;" width="100%">
    <thead>
        <tr>
            <th style="width: 5%;"><?= _l('s_no'); ?></th>
            <th style="width: 45%;"><?= _l('total_prescription'); ?></th>
            <th style="width: 25%;"><?= _l('created_by'); ?></th>
            <th style="width: 25%;"><?= _l('created_date'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
        if(staff_can('view_prescription', 'customers')){
        ?>
          <?php $i = 1; foreach ($patient_prescriptions as $prescription) { ?>
              <tr>
                  <td><?= $i++; ?></td>
                  <td><?= e($prescription['prescription_data']); ?></td>
                  <td><?= e(get_staff_full_name($prescription['created_by'])); ?></td>
                  <td><?= _d($prescription['created_datetime']); ?></td>
              </tr>
          <?php } ?>
          <?php
        }
        ?>
      </tbody>
  </table>

</div>

<?php
	  }
	?>
<script>
    function togglePrescriptionForm() {
        const form = document.getElementById('prescription-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
</script>


<?PHP
if (staff_can('view_casesheet', 'customers')) {
?>
<div role="tabpanel" class="tab-pane" id="tab_casesheet">
          <?php
        if(staff_can('create_casesheet', 'customers')){
        ?>
          <button class="btn btn-primary btn-sm" onclick="toggleCaseSheetForm()" style="float: right; margin-top: 6px; margin-right: 3px;">
            <?= _l('add_casesheet'); ?>
          </button>
      <?php
        }?>
  <!-- Title Section -->
  <div class="patient-section-title mt-4"><?= _l('casesheet'); ?></div>

  <br>

  <!-- Prescription Form Section -->
  <div class="medicine-table-container" id="casesheet-form" style="display:none;">
    

   <form id="casesheetForm">
  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" id="csrf_token">
  <input type="hidden" name="patientid" value="<?= $client->userid; ?>">

  <!-- Accordion Tabs -->
  <div class="accordion" id="casesheetAccordion">

    <!-- Preliminary Data Tab -->
    <div class="card">
	<button class="btn collapsed" type="button" data-toggle="collapse" data-target="#collapsePreliminaryData"  aria-expanded="false" aria-controls="collapsePreliminaryData" style="width: 100%">
      <div class="card-header" id="headingPreliminaryData">
        <h5 class="mb-0">
          
            <strong><?php echo _l('preliminary_data'); ?></strong> <i class="fa fa-chevron-down toggle-icon" id="icon-preliminaryData"></i>
          
        </h5>
      </div>
	  </button>
      <div id="collapsePreliminaryData" class="collapse " aria-labelledby="headingPreliminaryData" data-parent="#casesheetAccordion">
        <div class="card-body">
          <!-- Preliminary Data Content -->
			  <div class="row mtop10">
			<!-- Treatment Dropdown -->
			<div class="form-group">
			<!--<label class="control-label"><?php echo _l('treatment_details'); ?></label>-->
			<div id="treatment_rows">
    <!-- Initial Row -->
    <div class="row treatment-row align-items-end mb-3">
        <div class="col-md-3">
            <?php
			$selected = "";
			echo render_select('treatment_type[]', $treatments, ['treatment_id', 'treatment_name'], 'treatment_type', $selected, ['data-none-selected-text' => _l('dropdown_non_selected_tex')]);
			?>
        </div>

       <div class="col-md-2">
		<label>Duration(In Months)</label>
		
				
				<input type="number" name="duration_value[]" class="form-control" min="1" placeholder="Number">
			
			
	</div>


        <div class="col-md-2">
            <label>Improvement(%)</label>
            <input type="number" name="improvement[]" class="form-control improvement-input" min="0" max="100" placeholder="Enter Percentage" >
        </div>

        <div class="col-md-4">
            <label>Overall Progress</label>
            <div class="progress">
			
                <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
        </div>

        <div class="col-md-1">
		<br>
            <button type="button" class="btn btn-success add-row"><i class="fa fa-plus"></i></button>
        </div>
    </div>
</div>





</div>


		<!-- JavaScript to Add/Clone Rows and Update Progress -->
		<script>
		
		$(document).ready(function () {

    // Add new row
    $('#treatment_rows').on('click', '.add-row', function () {
        var newRow = `
        <div class="row treatment-row align-items-end mb-3">
            <div class="col-md-3">
                <?php
				$selected = "";
				echo render_select('treatment_type[]', $treatments, ['treatment_id', 'treatment_name'], '', $selected, ['data-none-selected-text' => _l('dropdown_non_selected_tex')]);
				?>
            </div>

             <div class="col-md-2">
				
						<input type="number" name="duration_value[]" class="form-control" min="1" placeholder="Number">
					
					
			</div>

            <div class="col-md-2">
                <input type="number" name="improvement[]" class="form-control improvement-input" min="0" max="100" placeholder="Enter Percentage">
            </div>

            <div class="col-md-4">
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
            </div>

            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-row"><i class="fa fa-minus"></i> </button>
            </div>
        </div>`;
        
        // Append the new row
        $('#treatment_rows').append(newRow);

        // Refresh selectpicker
        $('.selectpicker').selectpicker('refresh');
		 $('#treatment_rows .improvement-input').last().on('input', function () {
        if (this.value > 100) this.value = 100;
        if (this.value < 0) this.value = 0;
    });
    });

    // Remove row
    $('#treatment_rows').on('click', '.remove-row', function () {
        // Ensure that there is at least one row left
        if ($('.treatment-row').length > 1) {
            $(this).closest('.treatment-row').remove();
        }
    });

    // Update progress bar based on percentage improvement input
    $('#treatment_rows').on('input', '.improvement-input', function () {
        var value = $(this).val();
        value = Math.max(0, Math.min(100, value)); // Ensure it's between 0 and 100
        var progressBar = $(this).closest('.treatment-row').find('.progress-bar');
		if(value == 0 || value == ''){
			value = 0;
		}
		
        progressBar.css('width', value + '%').attr('aria-valuenow', value).text(value + '%');
    });
});


		</script>




		</div>
		
		<div class="row">
			<div class="col-md-4">
              <label for="documents"><?php echo _l('documents'); ?></label>
              <input type="file" name="documents[]" id="documents" class="form-control" multiple>
            </div>
            <div class="col-md-4">
              <label for="medicine_days"><?php echo _l('medicine_days'); ?> <span class="text-danger">*</span></label>
              <input type="number" name="medicine_days" id="medicine_days" class="form-control" min="1">
            </div>
			<div class="col-md-4">
              <label for="followup_date"><?php echo _l('followup_date'); ?></label>
              <input type="date" name="followup_date" id="followup_date" class="form-control">
            </div>
            <div class="col-md-4">
			<br>
               <?php
			$selected = "";
			echo render_select('patient_status', $patient_status, ['patient_status_id', 'patient_status_name'], ''. _l('patient_status').'', $selected, ['data-none-selected-text' => _l('dropdown_non_selected_tex')]);
			?>
            </div>
		</div>

		<!-- Presenting Complaints -->
		<div class="form-group mtop20">
			<label for="presenting_complaints" class="control-label">
				<?php echo _l('presenting_complaints'); ?>
			</label>
			<textarea id="presenting_complaints" name="presenting_complaints" class="form-control tinymce" rows="6"></textarea>
		</div>
        </div>
      </div>
    </div>

    <!-- Clinical Observation Tab -->
    <div class="card">
	<button class="btn collapsed" type="button" data-toggle="collapse" data-target="#collapseClinicalObservation" aria-expanded="false" aria-controls="collapseClinicalObservation" style="width: 100%">
      <div class="card-header" id="headingClinicalObservation">
        <h5 class="mb-0">
          
            <strong><?php echo _l('clinical_observation'); ?></strong> <i class="fa fa-chevron-down toggle-icon" id="icon-clinicalObservation"></i>
         
        </h5>
      </div>
	   </button>
      <div id="collapseClinicalObservation" class="collapse" aria-labelledby="headingClinicalObservation" data-parent="#casesheetAccordion">
        <div class="card-body">
          <!-- Clinical Observation Content -->
          <div class="row mtop10">
           
            <div class="col-md-12">
              <label for="clinical_observation"><?php echo _l('clinical_observation'); ?> <span class="text-danger">*</span></label>
              <textarea name="clinical_observation" id="clinical_observation" class="form-control tinymce" rows="6"></textarea>
            </div>
          </div>

          

        </div>
      </div>
    </div>

    <!-- Personal History Tab -->
    <div class="card">
	<button class="btn collapsed" type="button" data-toggle="collapse" data-target="#collapsePersonalHistory" aria-expanded="false" aria-controls="collapsePersonalHistory" style="width: 100%">
      <div class="card-header" id="headingPersonalHistory">
        <h5 class="mb-0">
          
            <strong><?php echo _l('personal_history'); ?></strong> <i class="fa fa-chevron-down toggle-icon" id="icon-personalHistory"></i>
          
        </h5>
      </div>
	  </button>
      <div id="collapsePersonalHistory" class="collapse" aria-labelledby="headingPersonalHistory" data-parent="#casesheetAccordion">
        <div class="card-body">
          <!-- Personal History Content -->
					  <div class="row">
			  <!-- Row 1 -->
			  <div class="col-md-4 mb-3">
				<label><?php echo _l('appetite'); ?>:</label>
				<input type="text" name="appetite" class="form-control" placeholder="<?php echo _l('appetite'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
				<label><?php echo _l('desires'); ?>:</label>
				<input type="text" name="desires" class="form-control" placeholder="<?php echo _l('desires'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
				<label><?php echo _l('aversion'); ?>:</label>
				<input type="text" name="aversion" class="form-control" placeholder="<?php echo _l('aversion'); ?>">
			  </div>

			  <!-- Row 2 -->
			  <div class="col-md-4 mb-3">
				<br>
				<label><?php echo _l('tongue'); ?>:</label>
				<input type="text" name="tongue" class="form-control" placeholder="<?php echo _l('tongue'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('urine'); ?>:</label>
				<input type="text" name="urine" class="form-control" placeholder="<?php echo _l('urine'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('bowels'); ?>:</label>
				<input type="text" name="bowels" class="form-control" placeholder="<?php echo _l('bowels'); ?>">
			  </div>

			  <!-- Row 3 -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('sweat'); ?>:</label>
				<input type="text" name="sweat" class="form-control" placeholder="<?php echo _l('sweat'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('sleep'); ?>:</label>
				<input type="text" name="sleep" class="form-control" placeholder="<?php echo _l('sleep'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('sun_headache'); ?>:</label>
				<input type="text" name="sun_headache" class="form-control" placeholder="<?php echo _l('sun_headache'); ?>">
			  </div>

			  <!-- Row 4 -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('thermals'); ?>:</label>
				<input type="text" name="thermals" class="form-control" placeholder="<?php echo _l('thermals'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('habits'); ?>:</label>
				<input type="text" name="habits" class="form-control" placeholder="<?php echo _l('habits'); ?>">
			  </div>

			  <!-- Row 5 -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('addiction'); ?>:</label>
				<input type="text" name="addiction" class="form-control" placeholder="<?php echo _l('addiction'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('side'); ?>:</label>
				<input type="text" name="side" class="form-control" placeholder="<?php echo _l('side'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('dreams'); ?>:</label>
				<textarea name="dreams" class="form-control" placeholder="<?php echo _l('dreams'); ?>"></textarea>
			  </div>

			  <!-- Row 6 -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('diabetes'); ?>:</label>
				<textarea name="diabetes" class="form-control" placeholder="<?php echo _l('diabetes'); ?>"></textarea>
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('thyroid'); ?>:</label>
				<textarea name="thyroid" class="form-control" placeholder="<?php echo _l('thyroid'); ?>"></textarea>
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('hypertension'); ?>:</label>
				<textarea name="hypertension" class="form-control" placeholder="<?php echo _l('hypertension'); ?>"></textarea>
			  </div>

			  <!-- Row 7 -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('hyperlipidemia'); ?>:</label>
				<textarea name="hyperlipidemia" class="form-control" placeholder="<?php echo _l('hyperlipidemia'); ?>"></textarea>
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('menstrual_obstetric_history'); ?>:</label>
				<textarea name="menstrual_obstetric_history" class="form-control" placeholder="<?php echo _l('menstrual_obstetric_history'); ?>"></textarea>
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('family_history'); ?>:</label>
				<textarea name="family_history" class="form-control" placeholder="<?php echo _l('family_history'); ?>"></textarea>
			  </div>

			  <!-- Final Row -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('past_treatment_history'); ?>:</label>
				<textarea name="past_treatment_history" class="form-control" placeholder="<?php echo _l('past_treatment_history'); ?>"></textarea>
			  </div>
			</div>
        </div>
      </div>
    </div>

    <!-- General Examination Tab -->
    <div class="card">
	<button class="btn collapsed" type="button" data-toggle="collapse" data-target="#collapseGeneralExamination" aria-expanded="false" aria-controls="collapseGeneralExamination" style="width: 100%">
      <div class="card-header" id="headingGeneralExamination">
        <h5 class="mb-0">
            <strong><?php echo _l('general_examination'); ?></strong> <i class="fa fa-chevron-down toggle-icon" id="icon-generalExamination"></i>
         
        </h5>
      </div>
	   </button>
      <div id="collapseGeneralExamination" class="collapse" aria-labelledby="headingGeneralExamination" data-parent="#casesheetAccordion">
        <div class="card-body">
          <!-- General Examination Content -->
          <div class="row">
			<div class="col-md-2">
				<label><?php echo _l('bp'); ?>:</label>
				<input type="text" name="bp" class="form-control" placeholder="120/80">
			</div>
			<div class="col-md-2">
				<label><?php echo _l('pulse'); ?>:</label>
				<input type="text" name="pulse" class="form-control" placeholder="Pulse">
			</div>
			<div class="col-md-2">
				<label><?php echo _l('weight'); ?>:</label>
				<input type="text" name="weight" class="form-control" placeholder="WT.(KG)">
			</div>
			<div class="col-md-2">
				<label><?php echo _l('height'); ?>:</label>
				<input type="text" name="height" class="form-control" placeholder="HT.">
			</div>
			<div class="col-md-2">
				<label><?php echo _l('temperature'); ?>:</label>
				<input type="text" name="temperature" class="form-control" placeholder="TEMP.">
			</div>
			<div class="col-md-2">
				<label><?php echo _l('bmi'); ?>:</label>
				<input type="text" name="bmi" class="form-control" placeholder="BMI">
			</div>
		</div>


		<?php
		$fields = [
			['mental_generals', 'pg', 'particulars'],
			['miasmatic_diagnosis', 'analysis_evaluation', 'reportorial_result'],
			['management', 'diet', 'exercise'],
			['critical', 'level_of_assent', 'dos_and_donts'],
			['level_of_assurance', 'criteria_future_plan_rx', 'nutrition']
		];

		$labels = [];
		foreach (array_merge(...$fields) as $field) {
			$labels[$field] = _l($field);
		}
		?>


		<?php foreach ($fields as $row): ?>
		<div class="row mtop15">
			<?php foreach ($row as $field): ?>
				<div class="col-md-4">
					<label><?php echo _l($labels[$field]); ?>:</label>
					<?php if ($field == 'nutrition'): ?>
						<select name="nutrition" class="form-control">
							<option value=""><?php echo _l('select'); ?></option>
							<option value="normal">Normal</option>
							<option value="poor">Poor</option>
							<option value="excessive">Excessive</option>
						</select>
					<?php else: ?>
						<textarea name="<?php echo $field; ?>" class="form-control" rows="2" placeholder="<?php echo $labels[$field]; ?>"></textarea>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Mind Tab -->
    <div class="card">
	<button class="btn collapsed" type="button" data-toggle="collapse" data-target="#collapseMind" aria-expanded="false" aria-controls="collapseMind" style="width: 100%">
      <div class="card-header" id="headingMind">
        <h5 class="mb-0">
            <strong>Mind</strong> <i class="fa fa-chevron-down toggle-icon" id="icon-mind"></i>
          
        </h5>
      </div>
	  </button>
      <div id="collapseMind" class="collapse" aria-labelledby="headingMind" data-parent="#casesheetAccordion">
        <div class="card-body">
          <!-- Mind Content -->
          <div class="form-group mtop20">
			<label for="mind" class="control-label">
				<?php echo _l('mind'); ?>
			</label>
			<textarea id="mind" name="mind" class="form-control tinymce" rows="6"></textarea>
		</div>
        </div>
      </div>
    </div>

  </div> <!-- End of Accordion -->

  <!-- Form Actions -->
  <div class="form-actions">
    <button type="submit" class="btn btn-primary"><?= _l('save_casesheet'); ?></button>
    <button type="button" class="btn btn-secondary" onclick="toggleCaseSheetForm()"><?= _l('cancel'); ?></button>
  </div>

</form>



  </div>

    <br>
    
    <!-- Prescription List -->
    <table id="doctor-prescription-table" class="table table-bordered table-striped" style="table-layout: fixed;" width="100%">
    <thead>
      <tr>
        <th>S.no</th>
        <!--<th>Action</th>-->
        <th>Consulted Date</th>
        <th>Doctor Name</th>
        <th>Clinical Observation</th>
        <th>Follow Up Progress</th>
        <th>Appointment Type</th>
        <th>Medicine Days</th>
        <th>Prescription</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $i=1;
    foreach($casesheet as $c){
      ?>
<tr>
      <td><?php echo $i++;?></td>
      <!--<td>
      <a href="javascript:void(0);" class="view-case" data-id="<?= $c['id']; ?>">
        <i class="fa fa-eye text-purple"></i>
      </a>

      </td>-->
      <td><?php echo _d($c['date']);?></td>
      <td><?php echo e(get_staff_full_name($c['staffid']));?></td>
      <td><?php echo $c['clinical_observation'];?></td>
      <td><?php echo $c['progress'];?></td>
      <td></td>
      <td><?php echo $c['medicine_days'];?></td>
      <td></td>
      <td>
       <?PHP
			if (staff_can('edit', 'casesheet')) {
			?>
				<a href="<?= admin_url('client/edit_casesheet/'.$c['id'].'/'.$client->userid); ?>">
					<button type="button" class="btn btn-warning btn-sm edit-button">
						<i class="fas fa-pencil-alt"></i>
					</button>
				</a>
			<?PHP
			}
			?>
      </td>
    </tr>
      <?php
    }
    ?>
    
      </tbody>
  </table>

</div>

<?PHP
}
?>
<script>
    function toggleCaseSheetForm() {
        const form = document.getElementById('casesheet-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
</script>



<?PHP
if (staff_can('view_appointments', 'customers')) {
?>

<div role="tabpanel" class="tab-pane" id="tab_visits">
    <div class="table-responsive">
        <div class="patient-section-title mt-4"><?= _l('appointment_history'); ?></div><br>

        <table id="appointment-history-table" class="table table-bordered table-striped" style="table-layout: fixed;" width="100%">
            <thead>
                <tr>
                    <th style="width: 2%;"><?= _l('s_no'); ?></th>
                    <th style="width: 15%;"><?= _l('visit_id'); ?></th>
                    <th style="width: 15%;"><?= _l('appointment_date'); ?></th>
                    <th style="width: 15%;"><?= _l('visit_status'); ?></th>
                    <th style="width: 15%;"><?= _l('consulted_date'); ?></th>
                    <th style="width: 15%;"><?= _l('appointment_type'); ?></th>
                    <th style="width: 15%;"><?= _l('medicine_given_days'); ?></th>
                    <th style="width: 23%;"><?= _l('consulted_doctor'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
        if(staff_can('view_appointments', 'customers')){
        ?>
              <?php $i = 1; foreach ($apponitment_data as $apponitment) { ?>
                  <tr>
                      <td><?= $i++; ?></td>
                      <td><?= e($apponitment['visit_id']); ?></td>
                      <td><?= _d($apponitment['appointment_date']); ?></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td><?= e(get_staff_full_name($apponitment['unit_doctor_id'])); ?></td>
                  </tr>
              <?php } ?>
          <?php } ?>
            </tbody>
        </table>

        <br>
        <div class="patient-section-title mt-4"><?= _l('doctor_ownership_history'); ?></div><br>

        <table id="doctor-ownership-table" class="table table-bordered table-striped" style="table-layout: fixed;" width="100%">
            <thead>
                <tr>
                    <th style="width: 2%;"><?= _l('s_no'); ?></th>
                    <th style="width: 25%;"><?= _l('old_ownership'); ?></th>
                    <th style="width: 25%;"><?= _l('new_ownership'); ?></th>
                    <th style="width: 25%;"><?= _l('created_by'); ?></th>
                    <th style="width: 23%;"><?= _l('created_date'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(staff_can('view_doctor_ownership', 'customers')){
            ?>
                <tr>
                    <td>1</td>
                    <td>Dr. Smith</td>
                    <td>Dr. Meera</td>
                    <td>Admin</td>
                    <td>2025-04-01 10:15 AM</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Dr. Meera</td>
                    <td>Dr. Rahul</td>
                    <td>Receptionist</td>
                    <td>2025-04-03 02:30 PM</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Dr. Rahul</td>
                    <td>Dr. Anita</td>
                    <td>System</td>
                    <td>2025-04-07 11:00 AM</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Dr. Anita</td>
                    <td>Dr. Smith</td>
                    <td>Admin</td>
                    <td>2025-04-10 09:45 AM</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<?PHP
}if (staff_can('view_payments', 'customers')) {
?>

<div role="tabpanel" class="tab-pane" id="tab_payments">
    <!--<p><?= _l('no_payments_recorded'); ?></p>-->
    <div class="table-responsive">
        <div class="patient-section-title mt-4"><?= _l('invoice'); ?></div><br>

        <table id="invoice-table" class="table table-bordered table-striped" style="table-layout: fixed;" width="100%">
            <thead>
                <tr>
                    <th><?= _l('invoice_number'); ?></th>
                    <th><?= _l('amount'); ?></th>
                    <th><?= _l('total_tax'); ?></th>
                    <th><?= _l('date'); ?></th>
                    <th><?= _l('patient'); ?></th>
                    <th><?= _l('due_date'); ?></th>
                    <th><?= _l('status'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(staff_can('view_invoice', 'customers')){
            $i = 1; foreach ($invoices as $invoice) { ?>
                    <tr>
                        <td><?= e($invoice['formatted_number']); ?></td>
                        <td><?= app_format_money_custom($invoice['subtotal'], $invoice['currency']); ?></td>
                        <td><?= app_format_money_custom($invoice['total_tax'], $invoice['currency']); ?></td>
                        <td><?= _d($invoice['date']); ?></td>
                        <td><?= e($invoice['company']); ?></td>
                        <td><?= _d($invoice['duedate']); ?></td>
                        <td><?= format_invoice_status_custom($invoice['status']); ?></td>
                    </tr>
                <?php } 
               } ?>
            </tbody>
        </table>

        <br>
        <div class="patient-section-title mt-4"><?= _l('payment_receipts'); ?></div><br>

        <table id="payment-table" class="table table-bordered table-striped" style="table-layout: fixed;" width="100%">
            <thead>
                <tr>
                    <th><?= _l('payment_number'); ?></th>
                    <th><?= _l('payment_mode'); ?></th>
                    <th><?= _l('date'); ?></th>
                    <th><?= _l('employee'); ?></th>
                    <th><?= _l('amount'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(staff_can('view_payments', 'customers')){
                $i = 1; foreach ($invoice_payments as $payments) { ?>
                    <tr>
                        <td><?= e($payments['id']); ?></td>
                        <td><?= e($payments['payment_mode']); ?></td>
                        <td><?= _d($payments['date']); ?></td>
                        <td></td>
                        <td><?= app_format_money_custom($payments['amount'], '1'); ?></td>
                    </tr>
                <?php } 
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?PHP

}if (staff_can('view_feedback', 'customers')) {
?>
          <div role="tabpanel" class="tab-pane" id="tab_feedback">
            <p>No feedback submitted.</p>
          </div>
<?PHP
}
if (staff_can('view_call_log', 'customers')) {
	?>
          <div role="tabpanel" class="tab-pane" id="tab_calls">
              <div class="table-responsive">
                <?php
                if(staff_can('create_call_log', 'customers')){
                  ?>
                <button class="btn btn-primary btn-sm" onclick="toggleCallLogForm()" style="float: right; margin-top: 6px; margin-right: 3px;">+ Add Call</button>
                  <?php
                }
                ?>
                <!-- Title Section -->
              <div class="patient-section-title mt-4">Call Logs</div>
                <br>

                <!-- Hidden Form -->
                <div id="call-log-form" class="card p-3 mb-4" style="display: none;">
                <form id="callLogEntryForm">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                  <input type="hidden" name="patientid" value="<?= $client->userid; ?>">
                  <div class="row">
                    
                    <div class="col-md-3">
                      <label>Criteria</label>
                      <select class="form-control" name="criteria_id" required>
                      <option value="">Select Enquiry Type</option>
                      <?php foreach ($criteria as $type): ?>
                          <option value="<?= $type['criteria_id'] ?>">
                              <?= $type['criteria_name'] ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
                      
                    </div>
                    <div class="col-md-3">
                      <label>Next Calling Date</label>
                      <input type="date" name="next_calling_date" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                      <label>Appointment Type</label>
                      <select class="form-control" name="appointment_type_id" required>
                      <option value="">Select Enquiry Type</option>
                      <?php foreach ($appointment_type as $type): ?>
                          <option value="<?= $type['appointment_type_id'] ?>">
                              <?= $type['appointment_type_name'] ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
                    </div>
                    <div class="col-md-3">
                      <label>Appointment Date</label>
                      <input type="date" name="appointment_date" class="form-control" required>
                    </div>
                  </div>

                  <br>

                  <div class="row">
                    <!--<div class="col-md-3">
                      <label>Created Date</label>
                      <input type="date" name="created_date" class="form-control" required>
                    </div>-->
                    <div class="col-md-6">
                      <label>Comments</label>
                      <textarea name="comments" class="form-control" rows="2" placeholder="<?= _l('enter_comments'); ?>" required></textarea>
                    </div>
                  
                  <div class="col-md-3">
                  <label>&nbsp;<br></label>
                    <button type="submit" class="btn btn-success" style="margin-top: 25px;"><?= _l('submit'); ?></button>
                  </div>
				  </div>
                </form>
              </div>

                <br>
                <!-- Table -->
                <table id="my-custom-table" class="table table-bordered table-striped" style="table-layout: fixed;" width="100%">
              <thead>
                <tr>
                  <th style="width: 2%;"><?= _l('s_no'); ?></th>
                  <th style="width: 8%;"><?= _l('called_by'); ?></th>
                  <th style="width: 8%;"><?= _l('criteria'); ?></th>
                  <th style="width: 12%;"><?= _l('next_calling_date'); ?></th>
                  <th style="width: 12%;"><?= _l('appointment_type'); ?></th>
                  <th style="width: 12%;"><?= _l('appointment_date'); ?></th>
                  <th style="width: 8%;"><?= _l('created_date'); ?></th>
                  <th style="width: 40%;"><?= _l('comments'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php 
                if(staff_can('view_call_log', 'customers')){
                $i = 1; foreach ($patient_call_logs as $log) { ?>
                  <tr>
                    <td><?= $i++; ?></td>
                    <td><?= e(get_staff_full_name($log['called_by'])); ?></td>
                    <td><?= e($log['criteria_name']); ?></td>
                    <td><?= _d($log['next_calling_date']); ?></td>
                    <td><?= e($log['appointment_type_name']); ?></td>
                    <td><?= _d($log['appointment_date']); ?></td>
                    <td><?= _d($log['created_date']); ?></td>
                    <td><?= e($log['comments']); ?></td>
                  </tr>
                <?php }
                } ?>
              </tbody>
            </table>

              </div>
            </div>
<?PHP
}if (staff_can('view_activity_log', 'customers')) {
?>

            <div role="tabpanel" class="tab-pane" id="tab_activity">
            <div class="activity-feed">
              <div class="patient-section-title mt-4"><?= _l('patient_activity_logs'); ?></div><br>
              <?php 
              if(staff_can('view_activity_log', 'customers')){
              foreach ($patient_activity_log as $log) { ?>
                <div class="feed-item">
                  <div class="date">
                    <span class="text-has-action" data-toggle="tooltip" data-title="<?= _dt($log['date']); ?>">
                      <?= time_ago($log['date']); ?>
                    </span>
                  </div>
                  <div class="text">
                    <?php if ($log['staffid'] != 0) { ?>
                      <a href="<?= admin_url('profile/' . $log['staffid']); ?>">
                        <?= staff_profile_image($log['staffid'], ['staff-profile-xs-image pull-left mright5']); ?>
                      </a>
                    <?php } ?>
                    <?= e($log['full_name']) . ' - '; ?>
                    <?= ($log['custom_activity'] == 0) ? _l($log['description']) : process_text_content_for_display($log['description']); ?>
                  </div>
                </div>
              <?php }
              }
              ?>
            </div>

            <div class="col-md-12">
              <input type="hidden" name="patientid" value="<?php echo $client->userid; ?>">
              <?= render_textarea('patient_activity_textarea', '', '', ['placeholder' => _l('enter_activity')], [], 'mtop15'); ?>
              <div class="text-right mtop10">
                <button id="patient_enter_activity" class="btn btn-primary"><?= _l('submit'); ?></button>
              </div>
            </div>
          </div>
<?PHP
}
?>

        </div>
        
<div class="modal fade child-modal" id="caseSheetModal" tabindex="-1" role="dialog" aria-labelledby="caseSheetModalLabel" style="margin-top: 35px;" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #f2f9ff">
        <h5 class="modal-title"><?php echo _l('case_sheet_details'); ?></h5>
        
      </div>
      <div class="modal-body" id="caseSheetModalBody"></div>
    </div>
  </div>
</div>



<!-- DataTables core -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
  $(document).ready(function () {
  $('#my-custom-table').DataTable({
    "ordering": false,
    dom:
      "<'row align-items-center mb-2'" +
        "<'col-md-6'l><'col-md-6 text-end'f>" +
      ">" +
      "<'row'<'col-md-12'tr>>" +
      "<'row'<'col-md-5'i><'col-md-7'p>>"
  });

  $('#appointment-history-table').DataTable({
      paging: true,
      searching: true,
      ordering: false,
      info: true
    });

    $('#doctor-ownership-table').DataTable({
      paging: true,
      searching: true,
      ordering: false,
      info: true
    });

  $('#invoice-table').DataTable({
      paging: true,
      searching: true,
      ordering: false,
      info: true
    });

    $('#payment-table').DataTable({
      paging: true,
      searching: true,
      ordering: false,
      info: true
    });
  

    $('#doctor-prescription-table').DataTable({
      paging: true,
      searching: true,
      ordering: false,
      info: true
    });
  
});

</script>

<script>

$(document).ready(function () {
  const pathParts = window.location.pathname.split('/');
  const lastPart = pathParts[pathParts.length - 1];

  if (lastPart.startsWith('tab_')) {
    const tabId = lastPart;

    // Activate nav tab link
    $('.nav-tabs a[href="#' + tabId + '"]').tab('show');

    // Activate tab content pane
    $('#' + tabId).addClass('active').siblings('.tab-pane').removeClass('active');

    // Optionally, scroll to the tab
    setTimeout(() => {
      const $tab = $('#' + tabId);
      if ($tab.length) {
        $('html, body').animate({
          scrollTop: $tab.offset().top - 100
        }, 300);
      }
    }, 200);
  }
});

  function toggleCallLogForm() {
    const form = document.getElementById('call-log-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
  }

 
  // Add patient activity via AJAX
  $("body").on("click", "#patient_enter_activity", function () {
    var message = $("#patient_activity_textarea").val();
    var patientId = $('input[name="patientid"]').val();

    if (message === "") return;

    $.post(admin_url + "client/add_patient_activity", {
        patientid: patientId,
        activity: message,
    })
    .done(function (response) {
        response = JSON.parse(response);
        if (response.success) {
            // ✅ Redirect after success
            window.location.href = response.redirect;
        } else {
            alert_float("danger", response.message);
        }
    })
    .fail(function (data) {
        alert_float("danger", data.responseText);
    });
});


// Add patient call log via AJAX
$("body").on("submit", "#callLogEntryForm", function (e) {
  e.preventDefault(); // Prevent the default form submission

  var $form = $(this);
  var formData = $form.serialize(); // Serialize all form fields including hidden CSRF and patientid

  $.post(admin_url + "client/add_patient_call_log", formData)
    .done(function (response) {
      response = JSON.parse(response);

      if (response.success) {
        window.location.href = response.redirect;
      } else {
        alert_float("danger", response.message || "Failed to save call log.");
      }
    })
    .fail(function (xhr) {
      alert_float("danger", xhr.responseText || "Error occurred while submitting.");
    });
});



function togglePrescriptionForm() {
    const form = document.getElementById('prescription-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
  }

  $("body").on("submit", "#prescriptionForm", function (e) {
  e.preventDefault();

  var form = $(this);
  var formData = form.serializeArray();
  console.log("Serialized array:", formData);

  // Optional: Convert to object for easier manipulation (if needed)
  let payload = {};
  formData.forEach(field => {
    if (payload[field.name]) {
      // If already exists, convert to array or push to it
      if (!Array.isArray(payload[field.name])) {
        payload[field.name] = [payload[field.name]];
      }
      payload[field.name].push(field.value);
    } else {
      payload[field.name] = field.value;
    }
  });

  console.log("Payload object:", payload);

  // Submit to the controller
  $.post(admin_url + "client/save_prescription", form.serialize())
    .done(function (response) {
      let res = JSON.parse(response);
      if (res.success) {
       // alert(res.message);
        if (res.redirect) {
          window.location.href = res.redirect;
        }
      } else {
        alert("Error: " + res.message);
      }
    })
    .fail(function () {
      alert("Something went wrong while saving the prescription.");
    });
});



$("body").on("submit", "#casesheetForm", function (e) {
  e.preventDefault();

  var form = $(this);
  var formData = form.serializeArray();
  console.log("Serialized array:", formData);

  // Optional: Convert to object for easier manipulation (if needed)
  let payload = {};
  formData.forEach(field => {
    if (payload[field.name]) {
      // If already exists, convert to array or push to it
      if (!Array.isArray(payload[field.name])) {
        payload[field.name] = [payload[field.name]];
      }
      payload[field.name].push(field.value);
    } else {
      payload[field.name] = field.value;
    }
  });

  console.log("Payload object:", payload);

  // Submit to the controller
  $.post(admin_url + "client/save_casesheet", form.serialize())
    .done(function (response) {
      let res = JSON.parse(response);
      if (res.success) {
       // alert(res.message);
        if (res.redirect) {
          window.location.href = res.redirect;
        }
      } else {
        alert("Error: " + res.message);
      }
    })
    .fail(function () {
      alert("Something went wrong while saving the prescription.");
    });
});

function _patient_init_data(data, id) {
  var hash = window.location.hash;

  var $modal = $("#client-modal"); // Assuming your patient modal ID is #client-modal

  $modal.find(".data").html(data.patientView.data);
  $modal.modal({
    show: true,
    backdrop: "static",
  });

  init_tags_inputs();
  init_selectpicker();
  init_datepicker();
  init_color_pickers();
  custom_fields_hyperlink();
  validate_client_form(); // Use correct validator if it's not leads

  var hashes = [
    "#tab_client_profile",
    "#tab_contacts",
    "#tab_activity",
    "#tab_calls",
    "#attachments",
  ];

  if (hashes.indexOf(hash) > -1) {
    window.location.hash = hash;
  }

  // Example: refresh call log datatable if present
  if ($.fn.DataTable && $.fn.DataTable.isDataTable("#my-custom-table")) {
    $("#my-custom-table").DataTable().ajax.reload();
  }

  // Set latest activity
  var latest_activity = $modal
    .find("#tab_activity .feed-item:last-child .text")
    .html();
  if (typeof latest_activity != "undefined") {
    $modal.find("#patient-latest-activity").html(latest_activity);
  } else {
    $modal
      .find(".patient-latest-activity > .info-heading")
      .addClass("hide");
  }
}

</script>


<script>
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById('prescription-form');
  form.style.display = 'none';

  // Fetch medicine and related data from PHP
  const medicineObjects = <?php echo json_encode($medicines); ?>;
  const potencyObjects = <?php echo json_encode($potencies); ?>;
  const doseObjects = <?php echo json_encode($doses); ?>;
  const timingObjects = <?php echo json_encode($timings); ?>;

  // Extract only active medicine names
  const medicineOptions = medicineObjects
    .filter(m => m.medicine_status === "1")
    .map(m => m.medicine_name);

  const potencyOptions = potencyObjects.map(p => p.medicine_potency_name);
  const doseOptions = doseObjects.map(d => d.medicine_dose_name);
  const timingOptions = timingObjects.map(t => t.medicine_timing_name);

  // Add one row on initial load
  addMedicineRow();

  // Global exposure for addMedicineRow so onclick can access it
  window.addMedicineRow = addMedicineRow;

  function addMedicineRow() {
    const tbody = document.getElementById("medicineBody");
    const tr = document.createElement("tr");

    // Create Medicine dropdown
    const tdMedicine = document.createElement("td");
    tdMedicine.appendChild(createSearchableSelect(medicineOptions, "medicine-name"));

    // Create Potency dropdown
    const tdPotency = document.createElement("td");
    tdPotency.appendChild(createSearchableSelect(potencyOptions, "medicine-potency"));

    // Create Dose dropdown
    const tdDose = document.createElement("td");
    tdDose.appendChild(createSearchableSelect(doseOptions, "medicine-dose"));

    // Create Timing dropdown
    const tdTiming = document.createElement("td");
    tdTiming.appendChild(createSearchableSelect(timingOptions, "medicine-timing"));

    // Create Remarks textarea
    const tdRemarks = document.createElement("td");
    const remarks = document.createElement("textarea");
    remarks.className = "medicine-textarea";
    remarks.rows = 2;
    remarks.setAttribute('name', 'medicine_remarks[]');  // Add remarks name
    tdRemarks.appendChild(remarks);

    // Create Remove Button
    const tdAction = document.createElement("td");
    const delBtn = document.createElement("button");
    delBtn.textContent = "Remove";
    delBtn.className = "medicine-btn";
    delBtn.style.backgroundColor = "#e74c3c";
    delBtn.onclick = () => tr.remove();
    tdAction.appendChild(delBtn);

    // Append all td elements to tr
    tr.appendChild(tdMedicine);
    tr.appendChild(tdPotency);
    tr.appendChild(tdDose);
    tr.appendChild(tdTiming);
    tr.appendChild(tdRemarks);
    tr.appendChild(tdAction);

    // Append tr to tbody
    tbody.appendChild(tr);
  }

  // Toggle visibility of the prescription form
  window.togglePrescriptionForm = function () {
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
  };

  // Reusable searchable select
  function createSearchableSelect(options, className) {
    const container = document.createElement('div');
    container.className = `medicine-select-container`;

    const input = document.createElement('input');
    input.className = `medicine-select-input ${className}`;
    input.setAttribute("placeholder", "Search...");
    input.setAttribute("autocomplete", "off");

    // Set proper name based on className
    if (className === 'medicine-name') {
      input.setAttribute('name', 'medicine_name[]');
    } else if (className === 'medicine-potency') {
      input.setAttribute('name', 'medicine_potency[]');
    } else if (className === 'medicine-dose') {
      input.setAttribute('name', 'medicine_dose[]');
    } else if (className === 'medicine-timing') {
      input.setAttribute('name', 'medicine_timing[]');
    }

    const optionsList = document.createElement('div');
    optionsList.className = 'medicine-select-options';

    input.addEventListener('input', () => {
      filterOptions(input.value, options, optionsList);
    });

    options.forEach(opt => {
      const option = document.createElement('div');
      option.className = 'medicine-select-option';
      option.textContent = opt;
      option.onclick = () => {
        input.value = opt;
        optionsList.style.display = 'none';
      };
      optionsList.appendChild(option);
    });

    container.appendChild(input);
    container.appendChild(optionsList);
    return container;
  }

  function filterOptions(query, options, optionsList) {
    optionsList.innerHTML = '';
    const filtered = options.filter(opt => opt.toLowerCase().includes(query.toLowerCase()));

    optionsList.style.display = filtered.length > 0 ? 'block' : 'none';

    filtered.forEach(opt => {
      const option = document.createElement('div');
      option.className = 'medicine-select-option';
      option.textContent = opt;
      option.onclick = () => {
        optionsList.previousElementSibling.value = opt;
        optionsList.style.display = 'none';
      };
      optionsList.appendChild(option);
    });
  }
});


</script>


<script>
$(document).on('click', '.view-case', function () {
  var caseId = $(this).data('id');

  $.ajax({
    url: '<?= site_url('admin/client/casesheet_view'); ?>/' + caseId,
    type: 'GET',
    success: function (response) {
      // Add blur to the first modal content
      $('.modal.show .modal-content').addClass('blurred');

      // Show second modal
      $('#caseSheetModalBody').html(response);
      $('#caseSheetModal').modal('show');
    },
    error: function () {
      alert('Failed to load case sheet details.');
    }
  });
});

// Remove blur when second modal closes
$('#caseSheetModal').on('hidden.bs.modal', function () {
  $('.modal .modal-content').removeClass('blurred');
});

$(document).on('show.bs.modal', '.modal', function () {
  var zIndex = 1040 + 10 * $('.modal:visible').length;
  $(this).css('z-index', zIndex);
  setTimeout(function () {
    $('.modal-backdrop').not('.modal-stack')
      .css('z-index', zIndex - 1)
      .addClass('modal-stack');
  }, 0);
});

$(document).on('hidden.bs.modal', '.modal', function () {
  if ($('.modal:visible').length) {
    $('body').addClass('modal-open');
  }
});

  document.getElementById('medicine_days').addEventListener('input', function () {
    let days = parseInt(this.value);
    if (!isNaN(days) && days > 0) {
      let today = new Date();
      today.setDate(today.getDate() + days);
      let followUp = today.toISOString().split('T')[0]; // Format to YYYY-MM-DD
      document.getElementById('followup_date').value = followUp;
    } else {
      document.getElementById('followup_date').value = '';
    }
  });



$(document).ready(function() {
    // Initialize all collapsible sections as collapsed by default
    $('.collapse').collapse('hide');
    
    // Toggle the icon when a section is opened/closed
    $('#casesheetAccordion .collapse').on('show.bs.collapse', function () {
        var icon = $(this).prev().find('.toggle-icon');
        icon.addClass('open');
    }).on('hide.bs.collapse', function () {
        var icon = $(this).prev().find('.toggle-icon');
        icon.removeClass('open');
    });
});


</script>

<script>
    document.querySelectorAll('.improvement-input').forEach(function(input) {
        input.addEventListener('input', function () {
            if (this.value > 100) this.value = 100;
            if (this.value < 0) this.value = 0;
        });
    });
</script>
      </div>
    </div>
  </div>
</div>
