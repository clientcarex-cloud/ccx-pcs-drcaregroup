<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    /* Basic styling for demonstration - you'll need much more */
    .patient-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start; /* Align to top to prevent vertical stretching if one column is taller */
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
        margin-bottom: 20px;
        flex-wrap: wrap; /* Allow columns to wrap on smaller screens */
    }

    .patient-info, .branch-info, .consultation-info {
        flex: 1;
        min-width: 250px; /* Ensure columns don't get too squished before wrapping */
        padding-right: 15px; /* Add some spacing between columns */
    }

    .patient-header p {
        margin-bottom: 5px; /* Adjust spacing between lines in patient info */
    }

    .patient-name {
        white-space: nowrap;     /* Prevent text from wrapping */
        overflow: hidden;        /* Hide overflowed content */
        text-overflow: ellipsis; /* Add ellipsis for hidden content */
        display: inline-block;   /* Essential for overflow/ellipsis on non-block elements */
        max-width: 100%;         /* Ensure it respects parent width */
    }


    .qr-code {
        width: 100px;
        height: 100px;
        background-color: #f0f0f0; /* Placeholder */
        flex-shrink: 0; /* Prevent it from shrinking */
        margin-left: 15px; /* Space from other columns */
    }

    .patient-status {
        display: flex;
        flex-wrap: wrap; /* Allow badges to wrap */
        gap: 10px;
        margin-bottom: 20px;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        flex-shrink: 0; /* Prevent shrinking too much */
    }

    .status-registered { background-color: #d4edda; color: #155724; }
    .status-no-issues { background-color: #d1ecf1; color: #0c5460; }
    .status-accepted { background-color: #d4edda; color: #155724; }
    .status-not-accepted { background-color: #f8d7da; color: #721c24; }
    .status-pending { background-color: #fff3cd; color: #856404; }


    .tabs {
        display: flex;
        flex-wrap: wrap; /* ALLOW TABS TO WRAP TO THE NEXT LINE */
        border-bottom: 1px solid #ddd;
        margin-bottom: 20px;
    }

    .tab-item {
        padding: 10px 15px;
        cursor: pointer;
        border: 1px solid transparent;
        border-bottom: none;
        margin-bottom: -1px; /* Overlap border */
        white-space: nowrap; /* Prevent tab text from wrapping */
    }

    .tab-item.active {
        border-color: #ddd;
        border-bottom: 1px solid #fff;
        background-color: #fff;
        font-weight: bold;
    }

    .tab-content {
        display: none;
        padding: 20px 0;
    }

    .tab-content.active {
        display: block;
    }

    .chief-complaint-section {
        display: flex;
        gap: 20px;
		margin-top: -20px;
        flex-wrap: wrap; /* Allow columns to wrap on smaller screens */
    }

    .chief-complaint-form, .chief-complaint-list {
        flex: 1;
        min-width: 300px; /* Ensure each section has a minimum width */
        padding: 15px;
        border: 1px solid #eee;
        border-radius: 5px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 44px;
        box-sizing: border-box; /* Ensures padding doesn't expand width */
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .btn {
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .table th, .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    .table th {
        background-color: #f2f2f2;
    }

    /* Tooth Chart Specific Styling */
    .dChart {
        display: flex;
        flex-wrap: wrap; /* Allow quadrants to wrap */
        border: 1px solid #e0e0e0;
        !margin-bottom: 20px;
    }

    .dChart .col-lg-6 {
        display: flex;
        /* Use flex to align teeth horizontally within quadrant */
        justify-content: center;
        /* Center teeth horizontally */
        flex-wrap: wrap;
        /* Allow teeth within quadrant to wrap */
        !padding: 10px;
        !min-height: 200px;
        /* Give some height to quadrants */
    }

    /* Borders for quadrants */
    .dChart .border-right { border-right: 3px solid #007bff !important; }
    .dChart .border-bottom { border-bottom: 3px solid #007bff !important; }
    .dChart .border-3 { border-width: 3px !important; }
    .dChart .border-primary { border-color: #007bff !important; }


    .quadrant {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    height: auto;
    !flex-wrap: wrap; /* Allow teeth within quadrant to wrap */
    justify-content: center;
}


    .tooth {
        flex-direction: column;
        align-items: center;
        !padding: 5px;
        !margin: 5px;
        !border: 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .tooth:hover {
        background-color: #f0f8ff;
        border-color: #007bff;
    }

    .tooth .toothicon {
        width: 20px; /* Adjust size of tooth image */
        height: 20px;
		margin-top: 5px;
        object-fit: contain;
    }

    .tooth svg {
        width: 20px; /* Size of the SVG overlay for surfaces */
        height: 20px;
        margin-top: 5px; /* Adjust if needed to position over image */
    }

    .tooth svg .polygon {
        fill: #f9f9f9; /* Default fill for unmarked surfaces */
        stroke: #333;
        stroke-width: 0.5;
        transition: fill 0.1s ease-in-out;
    }

    .tooth svg .polygon:hover {
        fill: #cceeff; /* Highlight on hover */
    }

    .tooth svg .polygon.marked {
        fill: #ffc107; /* Color for marked surfaces */
    }

    /* Combined tooth selection states */
    .tooth.selected-tooth {
        border-color: #ffc107;
        box-shadow: 0 0 5px rgba(255, 193, 7, 0.5);
    }


    .tooth-label-container {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .tooth-label-container button {
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        padding: 5px 10px;
        cursor: pointer;
    }

    .tooth-label-container button.active {
        background-color: #cceeff;
        border-color: #007bff;
    }

.custom-badge {
  display: inline-block;
  padding: 0.25em 0.35em;
  font-size: 0.75rem;
  margin: 0.10em;
  font-weight: 600;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  border-radius: 50%;
  color: #81b8fc;
  background-color: #d0e5ff; /* blue background */
  user-select: none;
  box-shadow: 0 0 5px rgba(0,0,0,0.15);
}
.bg-label-danger {
  background-color: #dc3545; /* Bootstrap danger red */
  color: white;
}
#selectedToothData {
  font-size: 12px; /* adjust size as needed */
}

 .tooth-label-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .tooth-chart-type, .tooth-selection-type {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            !background-color: #f0f0f0;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .tooth-chart-type:hover, .tooth-selection-type:hover {
            !background-color: #e0e0e0;
        }
        
        .active {
            !background-color: #e0f7fa;
            color: #007bff;
            border-color: #007bff;
        }
        
        .checkmark {
            font-size: 1.2em;
            line-height: 1;
            margin-left: 5px;
        }
		


/*List*/
.menu-items {
  color: #fff;
  font-size: 9px;
  text-align: left;
  font-weight: 400;
  width: 100%;
  
  
  
}

.menu-items label {
  display: inline-flex;
  gap: 5px;
  width: 100%;
  margin: 1px;
  padding: 5px 10px;
  border-radius: 2px;
  cursor: pointer;
  color: #fff;
  display: flex;
    list-style: none;
   
    height: auto;
    !flex-wrap: wrap; /* Allow teeth within quadrant to wrap */
    justify-content: center;
}

.adult {
  background-color: #01CFDD;
  
}
.child {
  background-color: #01CFDD;
}
.full.mouth {
  background-color: #39DA8A;
}
.menu-item.jaw-upper,
.menu-item.jaw-lower {
  background-color: #FDAC41;
}
.menu-item.quadrant {
  background-color: #FF5B5C;
}
.menu-item.anterior {
  background-color: #69809A;
}




/* Tab container */
.med-records-tab-container {
  display: flex;
  margin-top: 20px;
}

/* Style the tab */
.med-records-tab {
  width: 200px;
  border: 1px solid #ccc;
  !background-color: #f8f9fa;
  border-radius: 5px 0 0 5px;
}

/* Style the buttons inside the tab */
.med-records-tab button {
  display: block;
  background-color: inherit;
  color: black;
  padding: 12px 16px;
  width: 100%;
  border: none;
  outline: none;
  text-align: left;
  cursor: pointer;
  transition: 0.3s;
  font-size: 15px;
  border-bottom: 1px solid #ddd;
}

/* Change background color of buttons on hover */
.med-records-tab button:hover {
  background-color: #e9ecef;
}

/* Create an active/current "tab button" class */
.med-records-tab button.med-records-active {
  background-color: #fff;
  font-weight: bold;
  border-right: 3px solid #0d6efd;
}

/* Style the tab content */
.med-records-tabcontent {
  padding: 20px;
  border: 1px solid #ccc;
  width: calc(100% - 200px);
  border-left: none;
  min-height: 500px;
  background-color: #fff;
  border-radius: 0 5px 5px 0;
}

/* Form styles */
.med-records-form-group {
  margin-bottom: 15px;
}

.med-records-form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.med-records-form-control {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
}

.med-records-select {
  height: 38px;
}

.med-records-textarea {
  min-height: 80px;
  resize: vertical;
}

.med-records-btn {
  padding: 8px 16px;
  background-color: #0d6efd;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.med-records-btn:hover {
  background-color: #0b5ed7;
}

/* Table styles */
.med-records-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.med-records-table th {
  background-color: #f8f9fa;
  text-align: left;
  padding: 10px;
  border-bottom: 1px solid #ddd;
}

.med-records-table td {
  padding: 10px;
  border-bottom: 1px solid #eee;
}

.med-records-text-muted {
  color: #6c757d;
}

/* File upload styles */
.med-records-file-upload {
  display: flex;
  align-items: center;
  gap: 10px;
}

.med-records-file-upload-btn {
  position: relative;
  overflow: hidden;
  padding: 6px 12px;
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.med-records-file-upload-input {
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
}

.med-records-file-name {
  color: #6c757d;
  font-size: 0.875rem;
}

.med-records-heading {
  margin-bottom: 20px;
}


/* Tab container */
.inv-records-tab-container {
  display: flex;
  margin-top: 20px;
}

/* Style the tab */
.inv-records-tab {
  width: 200px;
  border: 1px solid #ccc;
  background-color: #f8f9fa;
  border-radius: 5px 0 0 5px;
}

/* Style the buttons inside the tab */
.inv-records-tab button {
  display: block;
  background-color: inherit;
  color: black;
  padding: 12px 16px;
  width: 100%;
  border: none;
  outline: none;
  text-align: left;
  cursor: pointer;
  transition: 0.3s;
  font-size: 15px;
  border-bottom: 1px solid #ddd;
}

/* Change background color of buttons on hover */
.inv-records-tab button:hover {
  background-color: #e9ecef;
}

/* Create an active/current "tab button" class */
.inv-records-tab button.inv-records-active {
  background-color: #fff;
  font-weight: bold;
  border-right: 3px solid #0d6efd;
}

/* Style the tab content */
.inv-records-tabcontent {
  padding: 20px;
  border: 1px solid #ccc;
  width: calc(100% - 200px);
  border-left: none;
  min-height: 500px;
  background-color: #fff;
  border-radius: 0 5px 5px 0;
}

/* Form styles */
.inv-records-form-group {
  margin-bottom: 15px;
}

.inv-records-form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.inv-records-form-control {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
}

.inv-records-select {
  height: 38px;
}

.inv-records-textarea {
  min-height: 80px;
  resize: vertical;
}

.inv-records-btn {
  padding: 8px 16px;
  background-color: #0d6efd;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.inv-records-btn:hover {
  background-color: #0b5ed7;
}

/* Table styles */
.inv-records-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.inv-records-table th {
  background-color: #f8f9fa;
  text-align: left;
  padding: 10px;
  border-bottom: 1px solid #ddd;
}

.inv-records-table td {
  padding: 10px;
  border-bottom: 1px solid #eee;
}

.inv-records-text-muted {
  color: #6c757d;
}

/* File upload styles */
.inv-records-file-upload {
  display: flex;
  align-items: center;
  gap: 10px;
}

.inv-records-file-upload-btn {
  position: relative;
  overflow: hidden;
  padding: 6px 12px;
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.inv-records-file-upload-input {
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
}

.inv-records-file-name {
  color: #6c757d;
  font-size: 0.875rem;
}

.inv-records-heading {
  margin-bottom: 20px;
}



 /* Unique Class Prefix: lab-ui */
    .lab-ui-box {
      background: #fff;
      border-radius: 6px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      padding: 20px;
      margin-bottom: 20px;
    }

    .lab-ui-section-title {
      margin-bottom: 20px;
      font-size: 18px;
      font-weight: 600;
    }

    .lab-ui-form .form-group textarea {
      resize: vertical;
    }

    .lab-ui-form .btn {
      margin-top: 10px;
    }

    .lab-ui-table th,
    .lab-ui-table td {
      font-size: 13px;
      text-align: center;
      vertical-align: middle;
    }

    .lab-ui-row-gap {
      margin-bottom: 30px;
    }
	
	
	
	 /* Custom UI Classes */
    .presc-ui-box {
      background: #fff;
      border-radius: 6px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      padding: 20px;
      margin-bottom: 30px;
    }

    .presc-ui-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 15px;
    }

    .presc-ui-table th,
    .presc-ui-table td {
      text-align: center;
      font-size: 13px;
      vertical-align: middle;
    }

    .presc-ui-action-icons i {
      margin: 0 5px;
      cursor: pointer;
    }

    .presc-ui-btn {
      padding: 6px 16px;
      font-weight: bold;
    }

    .presc-ui-form-group {
      margin-bottom: 10px;
    }

    .presc-ui-table small {
      font-style: italic;
    }
	
	
	
	
	.upload-ui-box {
      background: #fff;
      border-radius: 6px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      padding: 20px;
      margin-bottom: 30px;
    }

    .upload-ui-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 15px;
    }

    .upload-ui-folder-list {
      background: #f5f6f8;
      padding: 10px 15px;
      border-radius: 6px;
    }

    .upload-ui-folder-list div {
      padding: 6px 0;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
      color: #4a5a6a;
    }

    .upload-ui-folder-list div i {
      color: #607d8b;
    }

    .upload-ui-upload-option {
      background: #f5f6f8;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      margin-bottom: 15px;
    }

    .upload-ui-upload-option i {
      font-size: 28px;
      color: #00b894;
      margin-bottom: 10px;
    }

    .upload-ui-upload-option span {
      display: block;
      color: #777;
      font-size: 12px;
    }

    .upload-ui-upload-option strong {
      color: #00b894;
      font-size: 14px;
      display: block;
      margin-bottom: 5px;
    }

    .upload-ui-selected-folder {
      text-align: right;
      font-size: 14px;
      color: #607d8b;
      font-weight: 600;
    }
	
	
	.billing-ui-box {
      background: #fff;
      padding: 20px;
      border-radius: 6px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      margin-bottom: 20px;
    }

    .billing-ui-heading {
      font-weight: 600;
      font-size: 18px;
      margin-bottom: 15px;
    }

    .billing-ui-table th,
    .billing-ui-table td {
      font-size: 14px;
      vertical-align: middle;
    }

    .billing-ui-table th {
      font-weight: bold;
      color: #4a5a6a;
    }

    .billing-ui-table td {
      color: #333;
    }

    .billing-ui-form-group {
      margin-bottom: 15px;
    }

    .billing-ui-actions i {
      font-size: 16px;
      color: #fff;
    }

    .billing-ui-actions .btn {
      background: #00cec9;
      padding: 5px 10px;
      border-radius: 4px;
      margin-right: 5px;
    }

    .billing-ui-total-row {
      font-weight: bold;
    }

    .billing-ui-save-icon {
      font-size: 20px;
      color: #4a5a6a;
      background: #f1f1f1;
      border-radius: 5px;
      padding: 6px 10px;
      cursor: pointer;
      display: inline-block;
    }
	
	
	.investigation-tabs {
  display: flex;
  flex-direction: column;
  gap: 10px;
  border-right: 1px solid #ddd;
  padding-right: 10px;
}

.investigation-tab {
  padding: 10px 15px;
  cursor: pointer;
  border-radius: 4px;
  color: #4a4a4a;
  background: #f9f9f9;
  font-weight: 500;
  transition: background 0.3s;
}

.investigation-tab:hover {
  background: #ebf0ff;
}

.investigation-tab.active {
  color: #2a55e5;
  font-weight: bold;
  border-left: 3px solid #2a55e5;
  background: #e8f0ff;
}

	----------------
	.medical-history-tabs {
  display: flex;
  flex-direction: column;
  gap: 10px;
  border-right: 1px solid #ddd;
  padding-right: 10px;
}

.medical-history-tab {
  padding: 10px 15px;
  cursor: pointer;
  border-radius: 4px;
  color: #4a4a4a;
  background: #f9f9f9;
  font-weight: 500;
  transition: background 0.3s;
}

.medical-history-tab:hover {
  background: #ebf0ff;
}

.medical-history-tab.active {
  color: #2a55e5;
  font-weight: bold;
  border-left: 3px solid #2a55e5;
  background: #e8f0ff;
}

.inv-box-shadow {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}

.inv-table-box {
  background: #fff;
  border-radius: 6px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>

                        <div class="form-section">
                            <div class="form-heading"><?= _l('patient_case_sheet'); ?></div>
                            <hr>
							<?php
							
								$patientid = $client_data->userid;
								
								$total_invoice_amount = 0;
								$total_amount_paid = 0;

								// Loop through all invoices and sum total amount
								foreach ($invoices as $inv) {
									$total_invoice_amount += floatval($inv['total']);
								}

								// Loop through payments and sum paid amount
								foreach ($invoice_payments as $pay) {
									$total_amount_paid += floatval($pay['amount']);
								}

								$balance = $total_invoice_amount - $total_amount_paid;
								?>

                            <div class="patient-header">
								<div class="patient-info">
									<p><strong><?= _l('patient_id'); ?>:</strong> <?= '01-' . date('YmdHis'); // Placeholder, replace with actual ID ?></p>
									<p><strong><?= _l('name'); ?>:</strong> <?= $client_data->company; ?></p>
									<p><strong><?= _l('dob'); ?>:</strong> <?= !empty($client_data->dob) ? date('d-m-Y', strtotime($client_data->dob)) . ' (' . (date('Y') - date('Y', strtotime($client_data->dob))) . ')' : ''; ?></p>
									<p><strong><?= _l('fir_number'); ?>:</strong> <?= !empty($client_data->mr_no) ? $client_data->mr_no : '-'; ?></p>
								</div>

								<div class="branch-info">
									<p><strong><?= _l('branch'); ?>:</strong> <?= !empty($client_data->area) ? $client_data->area : 'KPHB'; ?></p>
									<p><strong><?= _l('case_sheet'); ?>:</strong> <?= '01-' . $client_data->userid . '-1'; // Customize if needed ?></p>
									<p><strong><?= _l('registered_date'); ?>:</strong> <?= date('d-m-Y', strtotime($client_data->datecreated)); ?></p>
									<p><strong><?= _l('source'); ?>:</strong> <?= !empty($client_data->enquiry_type_id) ? 'REFERRAL' : 'SELF'; ?></p>
								</div>

								<div class="consultation-info">
									<p><strong><?= _l('consultation'); ?>:</strong> <?= count($consultation); ?></p>
									<p><strong><?= _l('bill_amount'); ?>:</strong> ₹<?= number_format($total_invoice_amount, 2); ?></p>
									<p><strong><?= _l('amount_paid'); ?>:</strong> ₹<?= number_format($total_amount_paid, 2); ?></p>
									<p><strong><?= _l('balance'); ?>:</strong> ₹<?= number_format($balance, 2); ?></p>
								</div>


								<div class="qr-code">
									<img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($client_data->firstname . ' ' . $client_data->lastname); ?>" alt="QR Code">
								</div>
							</div>


                            <div class="patient-status">
                                <div class="status-badge status-registered">REGISTERED</div>
                                <div class="status-badge status-no-issues">NO MEDICAL ISSUES</div>
                                <div class="status-badge status-accepted">ACCEPTED (<?php echo count($accepted_treatment_plans);?>)</div>
                                <!--<div class="status-badge status-not-accepted">NOT ACCEPTED (0)</div>-->
                                <div class="status-badge status-pending">ADVISED (<?php echo count($pending_plans) + count($accepted_treatment_plans);?>)</div>
                                <div class="status-badge status-pending">PENDING (<?php echo count($pending_plans);?>)</div>
                            </div>

                            <div class="tabs">
                                <div class="tab-item active" data-tab="chief-complaints">Chief Complaints</div>
                                <div class="tab-item" data-tab="examination-findings">Examination Findings</div>
                                <div class="tab-item" data-tab="medical-history">Medical History</div>
                                <div class="tab-item" data-tab="past-dental-history">Past Dental History</div>
                                <div class="tab-item" data-tab="investigation">Investigation</div>
                                <div class="tab-item" data-tab="treatment-plan">Treatment Plan</div>
                                <div class="tab-item" data-tab="procedure">Procedure</div>
                                <div class="tab-item" data-tab="lab">Lab</div>
                                <div class="tab-item" data-tab="prescription">Prescription</div>
                                <div class="tab-item" data-tab="upload-files">Upload Files</div>
                                <div class="tab-item" data-tab="billing">Billing</div>
                            </div>

                            <div id="chief-complaints" class="tab-content active">
                                <div class="chief-complaint-section">
                                    <div class="chief-complaint-form">
                                        <h4>Tooth Chart / Selector</h4>
                                        
									        
										<div class="row">
										<div class="col-md-2">
										  <div class="menu-items">
											<label class="menu-title adult">ADULT CHART&nbsp;<input type="checkbox" class="adultCheckbox" id="adultCheckbox" checked></label>
											<label class="menu-title child">CHILD CHART&nbsp;<input type="checkbox" class="childCheckbox" id="childCheckbox"></label>
											<label class="menu-item full mouth">FULL MOUTH&nbsp;<input type="checkbox" class="fullMouthCheckbox" id="fullMouthCheckbox"></label>
											<label class="menu-item jaw-upper">UPPER JAW&nbsp;<input type="checkbox" class="upperJaw" id="upperJaw"></label>
											<label class="menu-item jaw-lower">LOWER JAW&nbsp;<input type="checkbox" class="lowerJaw" id="lowerJaw"></label>
											<label class="menu-item quadrant quadrant-1">QUADRANT 1&nbsp;<input type="checkbox" class="quadrant-1" id="quadrant-1"></label>
											<label class="menu-item quadrant quadrant-2">QUADRANT 2&nbsp;<input type="checkbox" class="quadrant-2" id="quadrant-2"></label>
											<label class="menu-item quadrant quadrant-3">QUADRANT 3&nbsp;<input type="checkbox" class="quadrant-3" id="quadrant-3"></label>
											<label class="menu-item quadrant quadrant-4">QUADRANT 4&nbsp;<input type="checkbox" class="quadrant-4" id="quadrant-4"></label>
											<label class="menu-item anterior upper-anterior">UPPER ANTERIOR&nbsp;<input type="checkbox" class="upperAnterior" id="upperAnterior"></label>
											<label class="menu-item anterior lower-anterior">LOWER ANTERIOR&nbsp;<input type="checkbox" class="lowerAnterior" id="lowerAnterior"></label>
										  </div>
										</div>
										<div class="col-md-6">
											<div class="row dChart toothchartAdult" id="toothchartAdult">
                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-right border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="1">
													
													<?php foreach ($quadrant1_teeth as $tooth): ?>
														<li class="tooth" data-quadrant="<?= $tooth['quadrant'] ?>" data-id="<?= $tooth['id'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>">
															<div class="text-center">
																<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																	<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Palatal" alias="P" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																</svg>
															</div>
														</li>
													<?php endforeach; ?>

                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="2">
															<?php foreach ($quadrant2_teeth as $tooth): ?>
																	<li class="tooth" data-quadrant="<?= $tooth['quadrant'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>" data-id="<?= $tooth['id'] ?>">
																		<div class="text-center">
																			<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																			<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																				<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																				<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																				<polygon value="Palatal" alias="P" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																				<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																				<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																			</svg>
																		</div>
																	</li>
															<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-right border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="4">
                                                       <?php foreach ($quadrant4_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="4" data-displayid="<?= $tooth['tooth_number'] ?>" data-id="<?= $tooth['id'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="3">
                                                        <?php foreach ($quadrant3_teeth as $tooth): ?>
														<li class="tooth" data-quadrant="3" data-displayid="<?= $tooth['tooth_number'] ?>" data-id="<?= $tooth['id'] ?>">
															<div class="text-center">
																<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																	<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																</svg>
															</div>
														</li>
													<?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
											
											<div class="row dChart toothchartChild" id="toothchartChild" style="display: none;">
                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-right border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="5">
                                                        <?php foreach ($quadrant5_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="5" data-id="<?= $tooth['id'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="6">
                                                        <?php foreach ($quadrant6_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="6" data-id="<?= $tooth['id'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-right border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="8">
                                                        <?php foreach ($quadrant8_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="8" data-id="<?= $tooth['id'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="7">
                                                        <?php foreach ($quadrant7_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="7" data-id="<?= $tooth['id'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
										</div>
										
										
										<div class="col-md-4" >
										
												<div class="chief-complaint-box mt-4" style="border: 1px solid #fff; padding-left: 10px; padding-right: 10px; box-shadow: 0 4px 10px #00000033; padding-top: 5px;">
												<h4 style="margin-top: 2px;">Chief Complaint</h4>
												
												<span id="selectedToothData" class="selectedToothData" name="selectedToothData"></span>
													<div class="tooth-selection-status mt-3 text-left">
													<h6><span class="selected-teeth-display" id="selected-teeth-display"></span></h6>
													<h6><span class="selected-surfaces-display" id="selected-surfaces-display"></span></h6>
												</div>
												<div class="form-group">
												
													<?php 
													echo render_select('chief_complaint_id', $chief_complaints, ['chief_complaint_id', 'chief_complaint_name'], 'Chief Complaint', '', ['id' => 'chiefComplaintText1']); ?>
													
													
												</div>
												<div class="form-group">
													<label for="notesText">Notes:</label>
													<textarea class="form-control" id="notesText" rows="3"></textarea>
												</div>
												<button type="button" class="btn btn-primary" id="addChiefComplaint">Add Complaint</button>
												<br>
												<br>
											</div>
											
											
										</div>
										
										
										</div>
										<br>
										<div class="chief-complaint-list">
										<h4><?= _l('chief_complaints_list'); ?></h4>
										<table class="table table-bordered">
											<thead>
												<tr>
													<th><?= _l('tooth_no'); ?></th>
													<th><?= _l('surface'); ?></th>
													<th><?= _l('chief_complaint'); ?></th>
													<th><?= _l('notes'); ?></th>
													<th><?= _l('date'); ?></th>
													<th><?= _l('actions'); ?></th>
												</tr>
											</thead>
											<tbody id="complaintsTableBody">
												<?php if (!empty($chief_complaint)) : ?>
													<?php foreach ($chief_complaint as $cc) : ?>
														<tr>
															<td><?= htmlspecialchars($cc['display_id']); ?></td>
															<td><?= htmlspecialchars($cc['surfaces']); ?></td>
															<td><?= htmlspecialchars($cc['complaint']); ?></td>
															<td><?= htmlspecialchars($cc['notes']); ?></td>
															<td><?= date('d-m-Y', strtotime($cc['created_at'])); ?></td>
															<td>
																<a href="javascript:void(0);" class="btn btn-sm btn-danger delete-complaint" data-id="<?= $cc['id']; ?>" title="<?= _l('delete'); ?>">
																	<i class="fa fa-trash"></i>
																</a>

															</td>
														</tr>
													<?php endforeach; ?>
												<?php else : ?>
													<tr>
														<td colspan="6" class="text-center"><?= _l('no_data_available'); ?></td>
													</tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>

                                            
                                            
                                        
                                    
                                </div>
                            </div>
                            </div>

                            <div id="examination-findings" class="tab-content">
								<div class="chief-complaint-section">
                                    <div class="chief-complaint-form">
                                        <h4>Examination Findings Content</h4>
                                        
									        
										<div class="row">
										<div class="col-md-2">
										  <div class="menu-items">
											<label class="menu-title adult">ADULT CHART&nbsp;<input type="checkbox" class="adultCheckbox" id="adultCheckbox" checked></label>
											<label class="menu-title child">CHILD CHART&nbsp;<input type="checkbox" class="childCheckbox" id="childCheckbox"></label>
											<label class="menu-item full mouth">FULL MOUTH&nbsp;<input type="checkbox" class="fullMouthCheckbox" id="fullMouthCheckbox"></label>
											<label class="menu-item jaw-upper">UPPER JAW&nbsp;<input type="checkbox" class="upperJaw" id="upperJaw"></label>
											<label class="menu-item jaw-lower">LOWER JAW&nbsp;<input type="checkbox" class="lowerJaw" id="lowerJaw"></label>
											<label class="menu-item quadrant quadrant-1">QUADRANT 1&nbsp;<input type="checkbox" class="quadrant-1" id="quadrant-1"></label>
											<label class="menu-item quadrant quadrant-2">QUADRANT 2&nbsp;<input type="checkbox" class="quadrant-2" id="quadrant-2"></label>
											<label class="menu-item quadrant quadrant-3">QUADRANT 3&nbsp;<input type="checkbox" class="quadrant-3" id="quadrant-3"></label>
											<label class="menu-item quadrant quadrant-4">QUADRANT 4&nbsp;<input type="checkbox" class="quadrant-4" id="quadrant-4"></label>
											<label class="menu-item anterior upper-anterior">UPPER ANTERIOR&nbsp;<input type="checkbox" class="upperAnterior" id="upperAnterior"></label>
											<label class="menu-item anterior lower-anterior">LOWER ANTERIOR&nbsp;<input type="checkbox" class="lowerAnterior" id="lowerAnterior"></label>
										  </div>
										</div>
										<div class="col-md-6">
											<div class="row dChart toothchartAdult" id="toothchartAdult2">
                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-right border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="1">
													
													<?php foreach ($quadrant1_teeth as $tooth): ?>
														<li class="tooth" data-quadrant="<?= $tooth['quadrant'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>">
															<div class="text-center">
																<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																	<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Palatal" alias="P" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																</svg>
															</div>
														</li>
													<?php endforeach; ?>

                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="2">
                                                        <?php foreach ($quadrant2_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="<?= $tooth['quadrant'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Palatal" alias="P" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
													<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-right border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="4">
                                                        <?php foreach ($quadrant4_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="4" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="3">
                                                        <?php foreach ($quadrant3_teeth as $tooth): ?>
														<li class="tooth" data-quadrant="3" data-displayid="<?= $tooth['tooth_number'] ?>">
															<div class="text-center">
																<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																	<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																</svg>
															</div>
														</li>
													<?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
											
											<div class="row dChart toothchartChild" id="toothchartChild" style="display: none;">
                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-right border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="5">
                                                        <?php foreach ($quadrant5_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="5" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="6">
                                                        <?php foreach ($quadrant6_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="6" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-right border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="8">
                                                        <?php foreach ($quadrant8_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="8" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="7">
                                                        <?php foreach ($quadrant7_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="7" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
										</div>
										
										
										<div id="examinationFindingsBox" class="col-md-4">
											<div class="chief-complaint-box mt-4" style="border: 1px solid #fff; padding-left: 10px; padding-right: 10px; box-shadow: 0 4px 10px #00000033;">
												<h4 style="margin-top: 2px;">Examination Findings</h4>
												<span id="selectedToothData" class="selectedToothData" name="selectedToothData"></span>
												<div class="tooth-selection-status mt-3 text-left">
													<h6><span class="selected-teeth-display" id="examinationFindingsTeeth"></span></h6>
													<h6><span class="selected-surfaces-display" id="examinationFindingsSurfaces"></span></h6>
												</div>

										<input type="hidden" name="patient_id" value="<?= $patientid ?>">
										<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
										<div class="form-group">
										
										<?php 
											echo render_select('examinationFindingSelect', $chief_complaints, ['chief_complaint_id', 'chief_complaint_name'], 'Examination Findings', '', ['id' => 'examinationFindingSelect']); ?>
										
										</div>

										<div class="form-group">
											<label for="notesText">Notes:</label>
											<textarea class="form-control notesText" name="examinationFindingsNotesText" rows="3"></textarea>
										</div>
										<div class="form-group">
											<label for="notesText">Add Images:</label>
											  <input type="file" id="examImages" name="images[]" multiple accept="image/*">
										</div>

										<button type="button" class="btn btn-primary" id="addExaminationFinding">Add Examination Findings</button>

										<br><br>
									</div>
								</div>


										
										
										</div>
										<br>
										<div class="chief-complaint-list">
										<h4>Patient Examination Findings</h4>
										<table class="table table-bordered">
											<thead>
												<tr>
													<th>Tooth Info</th>
													<th>Complaint</th>
													<th>Notes</th>
													<th>Date</th>
													<th>Images</th>
													<th>Actions</th>
												</tr>
											</thead>
											<tbody id="ExaminationFindingsTableBody">
											</tbody>
										</table>
									</div>

                                            
                                            
                                        
                                    
                                </div>
                            </div>
								
								
                            </div>

                            <div id="medical-history" class="tab-content">
							  <div class="row">
								<div class="col-md-3">
								  <div class="medical-history-tabs">
									<div class="medical-history-tab active" data-tab="MedicalProblems">Medical Problems</div>
									<div class="medical-history-tab" data-tab="PresentMedication">Present Medication</div>
								  </div>
								</div>

								<div class="col-md-9" style="box-shadow: 0 4px 10px #00000033; padding-top: 10px">
								  <!-- Medical Problems -->
								  <div class="medical-history-content" id="MedicalProblems" style="display: block;">
									<div class="inv-box-shadow p-3">
									  <div class="row">
										<div class="col-md-4">
										 
										  
										  <?php 
											echo render_select('medical-problems', $medical_problem, ['medical_problem_id', 'medical_problem_name'], 'medical_problem', '', ['id' => 'medical-problems']); ?>
										</div>
										<div class="col-md-6">
										  <label><strong>Notes</strong></label>
										  <input type="text" class="form-control" id="medical-notes" placeholder="Enter Notes">
										</div>
										<div class="col-md-2 d-flex align-items-end">
										<br>
										  <button class="btn btn-primary w-100" id="add-medical-problems-btn">Add</button>
										</div>
									  </div>

									  <div class="inv-table-box mt-4 p-3">
										<h5><strong>Medical Problems List</strong></h5>
										<table class="table table-bordered mt-3">
										  <thead>
											<tr>
											  <th>Medical Problem</th>
											  <th>Notes</th>
											  <th>Actions</th>
											</tr>
										  </thead>
										  <tbody id="medical-table-body">
											<?php if (!empty($medical_problems)) : ?>
											  <?php foreach ($medical_problems as $problem) : ?>
												<tr>
												  <td><?= htmlspecialchars($problem['problem_name']) ?></td>
												  <td><?= htmlspecialchars($problem['notes']) ?></td>
												  <td>
													<button class="btn btn-sm btn-danger" onclick="deleteProblem(<?= $problem['id'] ?>)">Delete</button>
												  </td>
												</tr>
											  <?php endforeach; ?>
											<?php else : ?>
											  <tr>
												<td colspan="3" class="text-center">No records</td>
											  </tr>
											<?php endif; ?>
										  </tbody>
										</table>
									  </div>
									</div>
								  </div>

								  <!-- Present Medication -->
								  <div class="medical-history-content" id="PresentMedication" style="display: none;">
									<div class="inv-box-shadow p-3">
									  <form id="presentMedicationForm" enctype="multipart/form-data" onsubmit="return false;">
										<div class="row mb-3">
										  <div class="col-md-3">
											<label><strong>Upload Medication Slip</strong></label>
											<div class="d-flex align-items-center">
											  <input type="file" class="form-control" name="file">
											</div>
										  </div>
										  <div class="col-md-6">
										  <label><strong>Notes</strong></label>
										  <input type="text" class="form-control" id="med-notes" name="notes"placeholder="Enter Notes">
										</div>
										<div class="col-md-3">
										<br>
											<button type="button" class="btn btn-primary" id="add-medication-btn">Add</button>
										</div>
										</div>
										 
										<input type="hidden" name="patient_id" value="<?= $patientid ?>">
										<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

										
									  </form>

									  <div class="inv-table-box mt-4 p-3">
										<h5><strong>Present Medications List</strong></h5>
										<table class="table table-bordered mt-3">
										  <thead>
											<tr>
											  <th>Image</th>
											  <th>Notes</th>
											  <th>Actions</th>
											</tr>
										  </thead>
										  <tbody id="medication-table-body">
											<?php if (!empty($present_medications)) : ?>
											  <?php foreach ($present_medications as $med) : ?>
												<tr>
												  <td>
													<?php if (!empty($med['file'])) : ?>
													  <a href="<?= base_url('uploads/medications/' . $med['file']) ?>" target="_blank">
														<img src="<?= base_url('uploads/medications/' . $med['file']) ?>" alt="Slip" width="50" height="50">
													  </a>
													<?php else : ?>
													  <span class="text-muted">No Image</span>
													<?php endif; ?>
												  </td>
												  <td><?= htmlspecialchars($med['notes']) ?></td>
												  <td>
													<button class="btn btn-sm btn-danger" onclick="deleteMedication(<?= $med['id'] ?>)">Delete</button>
												  </td>
												</tr>
											  <?php endforeach; ?>
											<?php else : ?>
											  <tr>
												<td colspan="3" class="text-center">No records</td>
											  </tr>
											<?php endif; ?>
										  </tbody>
										</table>
									  </div>
									</div>
								  </div>

								</div>
							  </div>
							</div>


                            <div id="past-dental-history" class="tab-content">
                                <div class="chief-complaint-section">
                                    <div class="chief-complaint-form">
                                        <h4>Past Dental History Content</h4>
                                        
									        
										<div class="row">
										<div class="col-md-2">
										  <div class="menu-items">
											<label class="menu-title adult">ADULT CHART&nbsp;<input type="checkbox" class="adultCheckbox" id="adultCheckbox" checked></label>
											<label class="menu-title child">CHILD CHART&nbsp;<input type="checkbox" class="childCheckbox" id="childCheckbox"></label>
											<label class="menu-item full mouth">FULL MOUTH&nbsp;<input type="checkbox" class="fullMouthCheckbox" id="fullMouthCheckbox"></label>
											<label class="menu-item jaw-upper">UPPER JAW&nbsp;<input type="checkbox" class="upperJaw" id="upperJaw"></label>
											<label class="menu-item jaw-lower">LOWER JAW&nbsp;<input type="checkbox" class="lowerJaw" id="lowerJaw"></label>
											<label class="menu-item quadrant quadrant-1">QUADRANT 1&nbsp;<input type="checkbox" class="quadrant-1" id="quadrant-1"></label>
											<label class="menu-item quadrant quadrant-2">QUADRANT 2&nbsp;<input type="checkbox" class="quadrant-2" id="quadrant-2"></label>
											<label class="menu-item quadrant quadrant-3">QUADRANT 3&nbsp;<input type="checkbox" class="quadrant-3" id="quadrant-3"></label>
											<label class="menu-item quadrant quadrant-4">QUADRANT 4&nbsp;<input type="checkbox" class="quadrant-4" id="quadrant-4"></label>
											<label class="menu-item anterior upper-anterior">UPPER ANTERIOR&nbsp;<input type="checkbox" class="upperAnterior" id="upperAnterior"></label>
											<label class="menu-item anterior lower-anterior">LOWER ANTERIOR&nbsp;<input type="checkbox" class="lowerAnterior" id="lowerAnterior"></label>
										  </div>
										</div>
										<div class="col-md-6">
											<div class="row dChart toothchartAdult" id="toothchartAdult3">
                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-right border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="1">
													
													<?php foreach ($quadrant1_teeth as $tooth): ?>
														<li class="tooth" data-quadrant="<?= $tooth['quadrant'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>">
															<div class="text-center">
																<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																	<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Palatal" alias="P" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																</svg>
															</div>
														</li>
													<?php endforeach; ?>

                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="2">
                                                        <?php foreach ($quadrant2_teeth as $tooth): ?>
														<li class="tooth" data-quadrant="<?= $tooth['quadrant'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>">
															<div class="text-center">
																<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																	<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Palatal" alias="P" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																</svg>
															</div>
														</li>
												<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-right border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="4">
                                                        <?php foreach ($quadrant4_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="4" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="3">
                                                        <?php foreach ($quadrant3_teeth as $tooth): ?>
														<li class="tooth" data-quadrant="3" data-displayid="<?= $tooth['tooth_number'] ?>">
															<div class="text-center">
																<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																	<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																</svg>
															</div>
														</li>
													<?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
											
											<div class="row dChart toothchartChild" id="toothchartChild" style="display: none;">
                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-right border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="5">
                                                        <?php foreach ($quadrant5_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="5" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="6">
                                                        <?php foreach ($quadrant6_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="6" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-right border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="8">
                                                        <?php foreach ($quadrant8_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="8" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="7">
                                                        <?php foreach ($quadrant7_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="7" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
										</div>
										
										
										<div id="pastDentalHistory" class="col-md-4" style="margin-top: -32px;">
										<h4 style="margin-top: 2px;">Past Dental History</h4>
												<div class="chief-complaint-box mt-4" style="border: 1px solid #fff; padding-left: 10px; padding-right: 10px; box-shadow: 0 4px 10px #00000033;">
													
													<span id="selectedToothData" class="selectedToothData" name="selectedToothData"></span>
													<div class="tooth-selection-status mt-3 text-left">
														<h6><span class="selected-teeth-display" id="examinationFindingsTeeth"></span></h6>
														<h6><span class="selected-surfaces-display" id="examinationFindingsSurfaces"></span></h6>
													</div>

											<input type="hidden" name="patient_id" value="<?= $patientid ?>">
											<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
											<div class="form-group">
												
												 <?php 
											echo render_select('pastDentalHistorySelect', $treatments, ['treatment_id', 'treatment_name'], _l('past_treatment_done'), '', ['id' => 'pastDentalHistorySelect']); ?>
											</div>

											<div class="form-group">
												<label for="notesText">Notes</label>
												<textarea class="form-control pastDentalHistoryNotesText" name="pastDentalHistoryNotesText" rows="3"></textarea>
											</div>
											<div class="form-group">
												<label for="notesText">Place of Treatment Done</label>
												<textarea class="form-control placeDentalHistory" name="placeDentalHistory" rows="3"></textarea>
											</div>
											<div class="form-group">
												<label for="notesText">Our Opinion</label>
												<textarea class="form-control ourOpinion" name="ourOpinion" rows="3"></textarea>
											</div>
										
											<button type="button" class="btn btn-primary" id="addPastDentalHistory">Add </button>

											<br><br>
										</div>
									</div>
										
										
										</div>
										<br>
										<div class="chief-complaint-list">
											<h4>Patient Examination Findings </h4>
											<table class="table table-bordered">
												<thead>
													<tr>
														<th>Teeth Info</th>
														<th>Complaint</th>
														<th>Notes</th>
														<th>Place</th>
														<th>Our Opinion</th>
														<th>Date</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody id="PastDentalHistoryTableBody"></tbody>
											</table>

										</div>
                                            
                                            
                                        
                                    
                                </div>
                            </div>
                            </div>

                            <div id="investigation" class="tab-content">
							  <div class="investigation-container row">
								<div class="col-md-3">
								  <div class="investigation-tabs">
									<div class="investigation-tab active" data-tab="medical">Medical Investigation</div>
									<div class="investigation-tab" data-tab="dental">Dental Investigation</div>
								  </div>
								</div>
								<div class="col-md-9" style="box-shadow: 0 4px 10px #00000033; padding-top: 10px">

								  <!-- Medical -->
								  <div class="investigation-content" id="medical" style="display:block;">
									<div class="inv-box-shadow p-3">
									  <div class="row">
										<div class="col-md-4">
										 
										  <?php 
											echo render_select(
												'medical-investigations-problem', 
												$medical_investigations, 
												['medical_investigation_id', 'medical_investigation_name'], 
												_l('medical_investigations'), 
												'', 
												['id' => 'medical-investigations-problem']
											); 
											?>
										</div>
										<div class="col-md-6">
										  <label><strong>Notes</strong></label>
										  <input type="text" id="medical-investigations-notes" class="form-control" placeholder="Enter Notes">
										</div>
										<div class="col-md-2 d-flex align-items-end"><br>
										  <button id="add-medical-btn" class="btn btn-primary w-100">Add</button>
										</div>
									  </div>

									  <div class="inv-table-box mt-4 p-3">
										<h5><strong>Medical Investigation List</strong></h5>
										<table class="table table-bordered mt-3" id="medical-table">
										  <thead>
											<tr>
											  <th>Medical Problem</th>
											  <th>Notes</th>
											  <th>Actions</th>
											</tr>
										  </thead>
										  <tbody>
											<tr><td colspan="3" class="text-center">No records</td></tr>
										  </tbody>
										</table>
									  </div>
									</div>
								  </div>

								  <!-- Dental -->
								  <div class="investigation-content" id="dental" style="display:none;">
									<div class="inv-box-shadow p-3">
									  <div class="row">
										<div class="col-md-4">
										
										  <?php 
											echo render_select(
												'dental-problem', 
												$dental_investigation, 
												['dental_investigation_id', 'dental_investigation_name'], 
												_l('dental_investigations'), 
												'', 
												['id' => 'dental-problem']
											); 
											?>
										</div>
										<div class="col-md-6">
										  <label><strong>Notes</strong></label>
										  <input type="text" id="dental-notes" class="form-control" placeholder="Enter Notes">
										</div>
										<div class="col-md-2 d-flex align-items-end"><br>
										  <button id="add-dental-btn" class="btn btn-primary w-100">Add</button>
										</div>
									  </div>

									  <div class="inv-table-box mt-4 p-3">
										<h5><strong>Dental Investigation List</strong></h5>
										<table class="table table-bordered mt-3" id="dental-table">
										  <thead>
											<tr>
											  <th>Dental Problem</th>
											  <th>Notes</th>
											  <th>Actions</th>
											</tr>
										  </thead>
										  <tbody>
											<tr><td colspan="3" class="text-center">No records</td></tr>
										  </tbody>
										</table>
									  </div>
									</div>
								  </div>

								</div>
							  </div>
							</div>

                            <div id="treatment-plan" class="tab-content">
                                <div class="chief-complaint-section">
                                    <div class="chief-complaint-form">
                                        <h4>Treatment Plan Content</h4>
                                        
									        
										<div class="row">
										<div class="col-md-2">
										  <div class="menu-items">
											<label class="menu-title adult">ADULT CHART&nbsp;<input type="checkbox" class="adultCheckbox" id="adultCheckbox" checked></label>
											<label class="menu-title child">CHILD CHART&nbsp;<input type="checkbox" class="childCheckbox" id="childCheckbox"></label>
											<label class="menu-item full mouth">FULL MOUTH&nbsp;<input type="checkbox" class="fullMouthCheckbox" id="fullMouthCheckbox"></label>
											<label class="menu-item jaw-upper">UPPER JAW&nbsp;<input type="checkbox" class="upperJaw" id="upperJaw"></label>
											<label class="menu-item jaw-lower">LOWER JAW&nbsp;<input type="checkbox" class="lowerJaw" id="lowerJaw"></label>
											<label class="menu-item quadrant quadrant-1">QUADRANT 1&nbsp;<input type="checkbox" class="quadrant-1" id="quadrant-1"></label>
											<label class="menu-item quadrant quadrant-2">QUADRANT 2&nbsp;<input type="checkbox" class="quadrant-2" id="quadrant-2"></label>
											<label class="menu-item quadrant quadrant-3">QUADRANT 3&nbsp;<input type="checkbox" class="quadrant-3" id="quadrant-3"></label>
											<label class="menu-item quadrant quadrant-4">QUADRANT 4&nbsp;<input type="checkbox" class="quadrant-4" id="quadrant-4"></label>
											<label class="menu-item anterior upper-anterior">UPPER ANTERIOR&nbsp;<input type="checkbox" class="upperAnterior" id="upperAnterior"></label>
											<label class="menu-item anterior lower-anterior">LOWER ANTERIOR&nbsp;<input type="checkbox" class="lowerAnterior" id="lowerAnterior"></label>
										  </div>
										</div>
										<div class="col-md-6">
											<div class="row dChart toothchartAdult" id="toothchartAdult4">
                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-right border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="1">
													
													<?php foreach ($quadrant1_teeth as $tooth): ?>
														<li class="tooth" data-quadrant="<?= $tooth['quadrant'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>">
															<div class="text-center">
																<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																	<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Palatal" alias="P" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																</svg>
															</div>
														</li>
													<?php endforeach; ?>

                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="2">
                                                        <?php foreach ($quadrant2_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="<?= $tooth['quadrant'] ?>" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Palatal" alias="P" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
													<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-right border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="4">
                                                        <?php foreach ($quadrant4_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="4" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="3">
                                                        <?php foreach ($quadrant3_teeth as $tooth): ?>
														<li class="tooth" data-quadrant="3" data-displayid="<?= $tooth['tooth_number'] ?>">
															<div class="text-center">
																<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																	<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																</svg>
															</div>
														</li>
													<?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
											
											<div class="row dChart toothchartChild" id="toothchartChild" style="display: none;">
                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-right border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="5">
                                                        <?php foreach ($quadrant5_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="5" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>

                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pb-2 border-bottom border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="6">
                                                        <?php foreach ($quadrant6_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="6" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>

                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-right border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-right quadrant" data-quadrant="8">
                                                        <?php foreach ($quadrant8_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="8" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>

                                                <div class="col-lg-6 col-xl-6 col-sm-6 col-6 pt-2 border-3 border-primary">
                                                    <ul class="list-group list-group-horizontal float-left quadrant" data-quadrant="7">
                                                        <?php foreach ($quadrant7_teeth as $tooth): ?>
															<li class="tooth" data-quadrant="7" data-displayid="<?= $tooth['tooth_number'] ?>">
																<div class="text-center">
																	<span class="custom-badge"><?= $tooth['tooth_number'] ?></span>
																	<img class="py-2 toothicon" src="https://tootpro.drcareams.com/images/tooth/<?= $tooth['tooth_type'] ?>" alt="Tooth <?= $tooth['tooth_number'] ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
																		<polygon value="Buccal" alias="B" points="0,0 30,0 20,10 10,10" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Distal" alias="D" points="0,0 10,10 10,20 0,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Lingual" alias="L" points="0,30 10,20 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Mesial" alias="M" points="30,0 20,10 20,20 30,30" fill="none" stroke="black" class="polygon unmarked"></polygon>
																		<polygon value="Center" alias="C" points="10,10 20,10 20,20 10,20" fill="none" stroke="black" class="polygon unmarked"></polygon>
																	</svg>
																</div>
															</li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
										</div>
										
										
										<div id="treatmentPlanDiv" class="col-md-4" style="margin-top: -32px;">
										<h4 style="margin-top: 2px;">Treatment Plan</h4>
												<div class="chief-complaint-box mt-4" style="border: 1px solid #fff; padding-left: 10px; padding-right: 10px; box-shadow: 0 4px 10px #00000033;">
													
													<span id="selectedToothData" class="selectedToothData" name="selectedToothData"></span>
													<div class="tooth-selection-status mt-3 text-left">
														<h6><span class="selected-teeth-display" id="examinationFindingsTeeth"></span></h6>
														<h6><span class="selected-surfaces-display" id="examinationFindingsSurfaces"></span></h6>
													</div>

											<input type="hidden" name="patient_id" value="<?= $patientid ?>">
											<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
											<div class="form-group">
												<?= render_select('treatmentPlanSelect', $treatment_type, ['treatment_type_id', 'treatment_type_name'], _l('treatment_type'), '', ['id' => 'treatmentPlanSelect']); ?>
											</div>

											<div class="form-group">
												<label for="treatmentSelect">Treatment</label>
												<select class="form-control" id="treatmentSelect" name="treatmentSelect">
													<option value="">-- Select Treatment --</option>
												</select>
											</div>

											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="price">Price</label>
														<input type="text" class="form-control" id="price" name="companyPrice" readonly>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="units">No. Of Units</label>
														<input type="number" class="form-control" id="units" name="units" min="1" value="1">
													</div>
												</div>

												<div class="col-md-6">
													<div class="form-group">
														<label for="companyCost">Company Cost</label>
														<input type="text" class="form-control" id="companyCost" name="companyCost" readonly>
													</div>
												</div>

												<div class="col-md-6">
													<div class="form-group">
														<label for="finalAmount">
															Final Amount <span id="amountChangeIcon"></span>
														</label>
														<input type="number" class="form-control" id="finalAmount" name="finalAmount">
													</div>
												</div>
											</div>

											<label><input type="checkbox" name="plan_a"> Plan A</label><br>
											<label><input type="checkbox" name="plan_b"> Plan B</label><br>
											<label><input type="checkbox" name="plan_c"> Plan C</label><br>
										
											<button type="button" class="btn btn-primary" id="addTreatmentPlan">Add </button>

											<br><br>
										</div>
									</div>
										
										
										</div>
										<br>
										<h4>Plan A</h4>
										<table class="table" id="plan_a_table"><thead><tr><th>Tooth</th><th>Plan</th><th>Treatment</th><th>Amount</th><th>Action</th></tr></thead><tbody></tbody></table>

										<h4>Plan B</h4>
										<table class="table" id="plan_b_table"><thead><tr><th>Tooth</th><th>Plan</th><th>Treatment</th><th>Amount</th><th>Action</th></tr></thead><tbody></tbody></table>

										<h4>Plan C</h4>
										<table class="table" id="plan_c_table"><thead><tr><th>Tooth</th><th>Plan</th><th>Treatment</th><th>Amount</th><th>Action</th></tr></thead><tbody></tbody></table>

										<h4>Accepted Plans</h4>
										<table class="table" id="accepted_table"><thead><tr><th>Tooth</th><th>Plan</th><th>Treatment</th><th>Amount</th><th>Action</th></tr></thead><tbody></tbody></table>

                                            
                                            
                                        
                                    
                                </div>
                            </div>
                            </div>

                            <div id="procedure" class="tab-content">
                                <div class="row" style="margin-top: -10px;">

      <!-- Panel -->
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            

            <div class="row" style=" display: flex;
			justify-content: center;
			flex-wrap: wrap;">
					  <!-- Left Column -->
			
			  <div class="col-md-6" style="box-shadow: 0 4px 10px #00000033; margin-right: 25px;">
			  <form id="updateProcedureForm" enctype="multipart/form-data">
				<h4>Update Procedure</h4>
					<input type="hidden" name="patient_id" value="<?= $patientid ?>">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

				<div class="form-group">
				  <label for="treatment">Treatments</label>
				  <select id="treatment" name="treatment" class="form-control">
					<option value="">--Select--</option>
					<!-- options loaded dynamically -->
				  </select>
				</div>

				<div class="form-group">
				  <label for="tooth_info">Tooth Info</label>
				  <select id="tooth_info" name="treatment_plan" class="form-control">
					<option value="">--Select--</option>
				  </select>
				</div>

				<div class="form-group">
				  <label for="treatment_procedure">Treatment Procedure</label>
				  <select id="treatment_procedure" name="procedure" class="form-control">
					<option value="">--Select--</option>
				  </select>
				</div>

				<div class="form-group">
				  <label for="procedure_notes">Procedure Notes</label>
				  <textarea id="procedure_notes" name="procedure_notes" class="form-control" rows="2" placeholder="Enter Procedure Notes..."></textarea>
				</div>

				<div class="form-group">
				  <label for="further_procedure">Further Procedure</label>
				  <textarea id="further_procedure" name="further_procedure" class="form-control" rows="2" placeholder="Enter Further Procedure..."></textarea>
				</div>

				<div class="form-group">
				
				  <?php
					echo render_select(
						'treatment_doctor',       // name attribute
						$doctors,                 // options array
						['staffid', ['firstname', 'lastname']],      // value field, label field
						'Treatment Doctor',       
						['id' => 'treatment_doctor'] // custom attributes like id
					);
					?>


				</div>

				<div class="form-group">
				  <label for="xray_file">Add X-Ray</label>
				  <input type="file" id="xray_file" name="xray_file" class="form-control">
				</div>

				<div class="form-group">
				  <label for="next_appointment_date">Next Appointment Date</label>
				  <input type="datetime-local" id="next_appointment_date" name="next_appointment_date" class="form-control">
				</div>

				<div class="form-group">
				  <?php
					echo render_select(
						'next_appointment_doctor',       // name attribute
						$doctors,                 // options array
						['staffid', ['firstname', 'lastname']],      // value field, label field
						_l('next_appointment_doctor'),
						['id' => 'next_appointment_doctor'] // custom attributes like id
					);
					?>
				</div>

				<div class="form-group">
				  <label for="lab_followup_date">Lab Followup Date</label>
				  <input type="datetime-local" id="lab_followup_date" name="lab_followup_date" class="form-control">
				</div>

				<button class="btn btn-primary btn-block" type="submit">Add</button>
				<br>
			  
				</form>
			</div>

              <!-- Middle Column -->
              <div class="col-md-5" style="box-shadow: 0 4px 10px #00000033; margin-right: 25px;">
				<h4><strong>Treatment Status</strong></h4>
				<div class="treatment-box">
					<?php foreach ($approved_treatments as $plan): ?>
						<p>
							<strong><?= $plan['treatment_plan']; ?> > <?= $plan['treatment']; ?></strong><br>
							Tooth Info: <?= $plan['tooth_info']; ?><br>

							<select class="form-control form-control-sm treatment-status-dropdown"
									style="width: auto; display: inline-block;"
									data-plan-id="<?= $plan['id']; ?>">
								<option value="Not Started" <?= $plan['treatment_status'] == 'Not Started' ? 'selected' : '' ?>>Not Started</option>
								<option value="Started" <?= $plan['treatment_status'] == 'Started' ? 'selected' : '' ?>>Started</option>
								<option value="Progress" <?= $plan['treatment_status'] == 'Progress' ? 'selected' : '' ?>>Progress</option>
								<option value="Completed" <?= $plan['treatment_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
							</select>
						</p>
						<hr>
					<?php endforeach; ?>
				</div>
			</div>



              <!-- Right Column -->
              <!--<div class="col-md-3"  style="box-shadow: 0 4px 10px #00000033; margin-right: 25px;">
                <h4><strong>Treatment History</strong></h4>
                <div class="panel panel-default">
                  <div class="panel-body">
                    No records
                  </div>
                </div>
              </div>-->
            </div>

            <!-- History Table -->
            <div class="row mtop30">
			  <div class="col-md-12">
				<h4 class="bold">History</h4>
				<div class="table-responsive">
				  <table id="treatmentHistoryTable" class="table table-bordered">
					<thead>
					  <tr>
						<th>Tooth Info</th>
						<th>Treatment</th>
						<th>Procedure</th>
						<th>Treatment Done</th>
						<th>Further Treatment</th>
						<th>Treatment Done By</th>
						<th>Treatment Done On</th>
						<th>Next Appt Date & Time</th>
						<th>Next Appt Doctor</th>
					  </tr>
					</thead>
					<tbody id="treatmentHistoryTableBody">
					  <!-- Rows will be dynamically injected here by JS -->
					</tbody>
				  </table>
				</div>
			  </div>
			</div>


          </div>
        </div>
      </div>

    </div>
                            </div>

                            <div id="lab" class="tab-content">
  <div class="row" style="margin-top: -10px;">
    <div class="col-md-12">
      <div class="panel_s">
        <div class="panel-body">
          <div class="row lab-ui-row-gap">
            <!-- Left Panel -->
            <div class="col-md-12">
              <div class="lab-ui-box">
                <h4 class="lab-ui-section-title">Add New Lab Work</h4>
                <form class="lab-ui-form" id="labWorkForm" enctype="multipart/form-data">
                   <div class="col-md-4">
					  <div class="form-group">
					  
						<label>Treatments</label>
						<select class="form-control" name="treatment_id" id="treatment_id">
						  <option value="">--Select--</option>
						  <?php foreach ($approved_treatments as $t){ ?>
						  <option value="<?= $t['id'] ?>" data-units="<?= $t['units'] ?>" data-tooth="<?= $t['tooth_info'] ?>">
							<?php echo $t['treatment_plan']; ?>
						  </option>
						  <?php }?>
						</select>
					  </div>
                  </div>
                  <!--<div class="form-group">
                    <label>Tooth Info</label>
                    <select class="form-control" id="tooth_info" name="tooth_info">
                      <option value="">--Select--</option>
                    </select>
                  </div>-->
				  <div class="col-md-4">
                  <div class="form-group">
                    <label>Tooth Details</label>
                    <input type="text" class="form-control" id="tooth_details" name="tooth_details" placeholder="Enter Tooth Details...">
                  </div>
                  </div>
				  <div class="col-md-4">
                  <div class="form-group">
                    <label>No. of Units</label>
                    <input type="text" class="form-control" id="tooth_units" name="units" placeholder="Enter No.Of Units...">
                  </div>
                  </div>
				<div class="col-md-4">
                  <div class="form-group">
                    <label>Lab</label>
                    <select class="form-control" name="lab_id">
                      <option value="">--Select--</option>
                      <?php foreach ($labs as $l): ?>
						  <option value="<?= $l['lab_id'] ?>"><?= $l['lab_name'] ?></option>
						<?php endforeach; ?>
                    </select>
                  </div>
                  </div>
				   <div class="col-md-4">
                  <div class="form-group">
                    <label>Lab Work</label>
                    <select class="form-control" name="lab_work_id">
                      <option value="">--Select--</option>
                      <?php foreach ($lab_works as $w): ?>
                      <option value="<?= $w['lab_work_id'] ?>"><?= $w['lab_work_name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  </div>
				   <div class="col-md-4">
                  <div class="form-group">
                    <label>Lab Followup</label>
                    <select class="form-control" name="lab_followup_id">
                      <option value="">--Select--</option>
                      <?php foreach ($lab_followups as $f): ?>
                      <option value="<?= $f['lab_followup_id'] ?>"><?= $f['lab_followup_name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  </div>
				   <div class="col-md-4">
                  <div class="form-group">
                    <label>Case Remarks</label>
                    <select class="form-control" name="case_remarks_id">
                      <option value="">--Select--</option>
                      <?php foreach ($case_remarks as $r): ?>
                      <option value="<?= $r['case_remark_id'] ?>"><?= $r['case_remark_name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  </div>
				   <div class="col-md-4">
                  <div class="form-group">
                    <label>Add Photos</label>
                    <input type="file" class="form-control" name="photo">
                  </div>
                  </div>
				   <div class="col-md-4">
                  <div class="form-group">
                    <label>Notes</label>
                    <textarea class="form-control" name="notes" rows="2" placeholder="Enter Notes..."></textarea>
                  </div>
                  </div>
				  <input type="hidden" name="patient_id" value="<?= $patientid ?>">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                  <button type="submit" class="btn btn-primary btn-block">Add</button>
                </form>
              </div>
            </div>

            <!-- Right Panel -->
            
          </div>
		  <div class="row">
				<div class="col-md-12">
				  <div class="lab-ui-box">
					<h4 class="lab-ui-section-title">Lab Work Status</h4>
					<div class="table-responsive">
					  <table class="table table-bordered lab-ui-table" id="labWorkStatusTable">
						<thead>
						  <tr>
							<th>Tooth Info</th>
							<th>Lab</th>
							<th>Lab Work</th>
							<th>Lab Followup</th>
							<th>No. of Units</th>
							<th>Lab Status</th>
							<th>Treatment Status</th>
							<th>Photos</th>
							<th>Requested Date</th>
						  </tr>
						</thead>
						<tbody></tbody>
					  </table>
					</div>
				  </div>
				</div>
            </div>
          <!--<div class="row">
            <div class="col-md-12">
              <div class="lab-ui-box">
                <h4 class="lab-ui-section-title">Lab Work Status History</h4>
                <div class="table-responsive">
                  <table class="table table-bordered lab-ui-table" id="labWorkHistoryTable">
                    <thead>
                      <tr>
                        <th>Tooth Info</th>
                        <th>Lab</th>
                        <th>Lab Work</th>
                        <th>Lab Followup</th>
                        <th>No. of Units</th>
                        <th>Status Type</th>
                        <th>Old Status</th>
                        <th>New Status</th>
                        <th>Notes</th>
                        <th>Change Date</th>
                        <th>Changed By</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>-->

        </div>
      </div>
    </div>
  </div>
</div>

                            <div id="prescription" class="tab-content">
    <div class="row" style="margin-top: -10px;">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="no-margin">Prescription Content</h4>
                    <hr>
                    <div class="row">
                        <!-- Left: Previous Prescriptions -->
                        <div class="col-md-4">
                            <div class="presc-ui-box">
                                <div class="presc-ui-title">
                                    Previous Prescriptions
                                    
                                </div>
                                <table class="table table-bordered presc-ui-table" id="previousPrescriptionTable">
									<thead>
										<tr>
											<th>Code</th>
											<th>Date</th>
											<th>Doctor</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<!-- Dynamic rows will be appended here -->
									</tbody>
								</table>

                            </div>
                        </div>

                        <!-- Right: Add Medicine -->
                        <div class="col-md-8">
                            <div class="presc-ui-box">
                                <div class="presc-ui-title">Add Medicine</div>
                                <form id="prescriptionForm">

                                    <div class="row">
                                        <div class="col-md-3 presc-ui-form-group">
											<?php
										echo render_select(
											'medicine_id',       // name attribute
											$medicine,                 // options array
											['medicine_id', ['medicine_name']],      // value field, label field
											_l('medicine'),       
											['id' => 'medicine_id'] // custom attributes like id
										);
										?>
                                        </div>
                                        <div class="col-md-2 presc-ui-form-group">
										<label>Frequency</label>
										<select class="form-control" name="frequency_id">
											<option value="">--Select--</option>
											<option value="1">1-0-1</option>
											<option value="2">0-1-1</option>
											<option value="3">1-1-1</option>
											<option value="4">1-1-0</option>
											<option value="5">1-0-0</option>
											<option value="6">0-1-0</option>
											<option value="7">0-0-1</option>
											<option value="8">2-0-2</option>
											<option value="9">2-1-2</option>
											<option value="10">1-2-1</option>
											<option value="11">0-0-0</option>
											<option value="12">SOS</option>
											<option value="13">HS</option>
										</select>
									</div>

                                        <div class="col-md-2 presc-ui-form-group">
                                            <label>Duration</label>
                                            <input type="text" class="form-control" name="duration" placeholder="In Days">
                                        </div>
                                        <div class="col-md-3 presc-ui-form-group">
											<label>Usage</label>
											<select class="form-control" name="usage_id">
												<option value="">--Select--</option>
												<option value="1">After Food</option>
												<option value="2">Before Food</option>
												<option value="3">With Food</option>
												<option value="4">Empty Stomach</option>
												<option value="5">At Bedtime</option>
												<option value="6">Morning Only</option>
												<option value="7">Night Only</option>
											</select>
										</div>

                                        <div class="col-md-2 presc-ui-form-group" style="padding-top: 25px;">
                                            <button type="button" class="btn btn-primary presc-ui-btn" id="addMedicineBtn">Add</button>
                                        </div>
                                    </div>

                                    <table class="table table-bordered presc-ui-table" id="medicineListTable">
                                        <thead>
                                            <tr>
                                                <th>Medicine</th>
                                                <th>Frequency</th>
                                                <th>Duration</th>
                                                <th>Usage</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td colspan="5"><small>No Medicines Added</small></td></tr>
                                        </tbody>
                                    </table>

                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea class="form-control" name="prescriptionFormnotes" rows="2" placeholder="Enter Notes..."></textarea>
                                    </div>

                                    <div class="form-group">
										<?php
										echo render_select(
											'prescriptionFormdoctor_id',       // name attribute
											$doctors,                 // options array
											['staffid', ['firstname', 'lastname']],      // value field, label field
											_l('prescription_by'),       
											['id' => 'prescriptionFormdoctor_id'] // custom attributes like id
										);
										?>
                                    </div>
										<input type="hidden" name="patient_id" value="<?= $patientid ?>">
										<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                                    <button type="submit" class="btn btn-primary presc-ui-btn">Save Prescription</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>


						
						
						 <div id="upload-files" class="tab-content">
									
									
									<div class="row">

      <!-- Left: Folder List -->
      <div class="col-md-9">
	  
<div class="upload-ui-folder-list" id="folder-list">
    <div class="folder" data-folder="Documents">
        <i class="glyphicon glyphicon-folder-close"></i> Documents (<span class="folder-count" data-folder="Documents">0</span>)
        <div class="subfolder-list" style="display:none; margin-left: 20px;">
            <div class="subfolder" data-folder="Documents > Reviews">
                <i class="glyphicon glyphicon-folder-close"></i> Reviews (<span class="folder-count" data-folder="Documents > Reviews">0</span>)
            </div>
        </div>
    </div>

    <!--<div class="folder" data-folder="Medical Reports">
        <i class="glyphicon glyphicon-folder-close"></i> Medical Reports (<span class="folder-count" data-folder="Medical Reports">0</span>)
        <div class="subfolder-list" style="display:none; margin-left: 20px;">
            <div class="subfolder" data-folder="Medical Reports > Past">
                <i class="glyphicon glyphicon-folder-close"></i> Past (<span class="folder-count" data-folder="Medical Reports > Past">0</span>)
            </div>
        </div>
    </div>-->

    <div class="folder" data-folder="Medication">
        <i class="glyphicon glyphicon-folder-close"></i> Medication (<span class="folder-count" data-folder="Medication">0</span>)
        <div class="subfolder-list" style="display:none; margin-left: 20px;">
            <div class="subfolder" data-folder="Medication > Present">
                <i class="glyphicon glyphicon-folder-close"></i> Present (<span class="folder-count" data-folder="Medication > Present">0</span>)
            </div>
        </div>
    </div>

    <div class="folder" data-folder="Treatment Procedure" data-is-leaf="true">
        <i class="glyphicon glyphicon-folder-close"></i> Treatment Procedure (<span class="folder-count" data-folder="Treatment Procedure">0</span>)
    </div>

    <div class="folder" data-folder="Examination Findings" data-is-leaf="true">
        <i class="glyphicon glyphicon-folder-close"></i> Examination Findings (<span class="folder-count" data-folder="Examination Findings">0</span>)
    </div>
</div>

<div id="image-display" class="row" style="margin-top:20px;"></div>


	</div>


      <!-- Right: Upload Section -->
      <div class="col-md-3">
        <div class="upload-ui-box">
          <div class="upload-ui-title">Upload Files</div>

          <div class="upload-ui-upload-option">
            <i class="glyphicon glyphicon-cloud-upload"></i>
            <strong>Browse</strong>
            <span>folders here</span>
            <span style="margin-top: 5px;">Supports JPG, PNG</span>
          </div>

          <div class="upload-ui-upload-option">
            <i class="glyphicon glyphicon-camera"></i>
            <strong>Capture Image</strong>
          </div>
        </div>
      </div>

    </div>
						 
						 </div>
						 <div id="billing" class="tab-content">
						  <div class="billing-ui-box">
							<div class="billing-ui-heading">Payment Details</div>
							<div class="row">
							  <div class="col-md-12">
								<table class="table table-bordered billing-ui-table">
								  <thead>
									<tr>
									  <th>Tooth Info</th>
									  <th>Treatment</th>
									  <th>Progress</th>
									  <th>Amount</th>
									  <th>Invoice No</th>
									  <th>Pay Now</th>
									</tr>
								  </thead>
								  <tbody id="billing-details-body">
									<!-- Loaded via JS -->
								  </tbody>
								</table>
							  </div>
							</div>

							<hr>
							<div class="billing-ui-heading" style="font-size:16px;">Payment History</div>
							<table class="table table-bordered billing-ui-table">
							  <thead>
								<tr>
								  <th>Receipt No</th>
								  <th>Amount</th>
								  <th>Date & Time</th>
								  <th>Invoice No</th>
								  <th>Actions</th>
								</tr>
							  </thead>
							  <tbody id="payment-history-body">
								<!-- Loaded via JS -->
							  </tbody>
							</table>
						  </div>
						</div>


						
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
let selectedSurfaces = {};
let selectedTeeth = new Set();

document.addEventListener('DOMContentLoaded', function () {
	
    const adultChart = wrapper.querySelectorAll('.toothchartAdult');
    const childChart = wrapper.querySelectorAll('.toothchartChild');
    const chartTypeButtons = document.querySelectorAll('.tooth-chart-type');
    const selectedToothData = wrapper.querySelector('.selectedToothData');
    const singleSelectCheckboxes = wrapper.querySelectorAll('.menu-item input[type="checkbox"]:not(.adultCheckbox):not(.childCheckbox)');


    function getToothImage(toothId) {
		const toothElement = wrapper.querySelector(`li.tooth[data-displayId="${toothId}"]`);
		if (!toothElement) return 'default-tooth.svg';
		const img = toothElement.querySelector('img.toothicon');
		return img && img.src ? img.src.split('/').pop() : 'default-tooth.svg';
	}
	singleSelectCheckboxes.forEach(cb => {
	  cb.addEventListener('change', function () {
		if (this.checked) {
			
	  
    if (this.checked) {
      // Deselect adult and child checkboxes
      wrapper.querySelector('.adultCheckbox').checked = false;
      wrapper.querySelector('.childCheckbox').checked = false;

      // Hide child chart div
      wrapper.querySelector('.toothchartChild').style.display = 'none';

      // Show adult chart div
       wrapper.querySelector('.toothchartAdult').style.display = 'block';

      // Deselect all other single-select checkboxes except this one
      singleSelectCheckboxes.forEach(otherCb => {
        if (otherCb !== this) otherCb.checked = false;
      });

      // Logic for selecting teeth checkboxes by category
      const allAdultTeeth = document.querySelectorAll('.adult-tooth-checkbox');
      const upperJawTeeth = document.querySelectorAll('.upper-jaw-tooth-checkbox');
      const lowerJawTeeth = document.querySelectorAll('.lower-jaw-tooth-checkbox');

      // First, uncheck all adult teeth to reset selection
      allAdultTeeth.forEach(cb => cb.checked = false);
		//alert(this.id);
      if (this.id === 'fullMouthCheckbox') {
		// Show adult chart
		 wrapper.querySelector('.toothchartAdult').style.display = 'block';

		// Deselect other charts and checkboxes
		 wrapper.querySelector('.toothchartChild').style.display = 'none';
		 wrapper.querySelector('.adultCheckbox').checked = false;
		 wrapper.querySelector('.childCheckbox').checked = false;

		// Clear current selections
		selectedTeeth.clear();
		selectedSurfaces = {};
		var toothId = "Full Mouth";
		selectedTeeth.add(toothId);
		selectedSurfaces[toothId] = ['Full Mouth'];
		// Select all adult teeth (store in set, no need to simulate clicks)
		document.querySelectorAll('.toothchartAdult .tooth').forEach(toothEl => {
			const toothId = toothEl.getAttribute('data-id');
			//selectedTeeth.add(toothId);
			//selectedSurfaces[toothId] = ['Center', 'Mesial', 'Distal', 'Palatal', 'Buccal']; // default surfaces

			toothEl.classList.add('selected-tooth');
			toothEl.querySelectorAll('.polygon').forEach(p => p.classList.add('marked'));
		});

		// Render only "Full Mouth" in summary
		renderSelectedTeethData();
	} else if (this.id === 'upperJaw') {
		//alert(this.value);
		// Show adult chart
		 wrapper.querySelector('.toothchartAdult').style.display = 'block';

		// Hide child chart and uncheck other checkboxes
		 wrapper.querySelector('.toothchartChild').style.display = 'none';
		 wrapper.querySelector('.adultCheckbox').checked = false;
		 wrapper.querySelector('.childCheckbox').checked = false;

		// Clear previous selections
		selectedTeeth.clear();
		selectedSurfaces = {};
		clearAllSelectedTeeth();

		// Add "Upper Jaw" as selected with 'Full Mouth' surfaces
		const jawId = "upperJaw";
		selectedTeeth.add(jawId);
		selectedSurfaces[jawId] = ['Upper Jaw'];

		// Select all teeth in quadrant 1 inside toothchartAdult
		 document.querySelectorAll('.toothchartAdult .tooth[data-quadrant="1"]').forEach(toothEl => {
			toothEl.classList.add('selected-tooth');
			toothEl.querySelectorAll('.polygon').forEach(p => p.classList.add('marked'));
		});
		 document.querySelectorAll('.toothchartAdult .tooth[data-quadrant="2"]').forEach(toothEl => {
			toothEl.classList.add('selected-tooth');
			toothEl.querySelectorAll('.polygon').forEach(p => p.classList.add('marked'));
		});

		// Render summary showing only "Upper Jaw"
		renderSelectedTeethData();
	} else if (this.id === 'lowerJaw') {
		//alert(this.value);
		// Show adult chart
		 wrapper.querySelector('.toothchartAdult').style.display = 'block';

		// Hide child chart and uncheck other checkboxes
		 wrapper.querySelector('.toothchartChild').style.display = 'none';
		 wrapper.querySelector('.adultCheckbox').checked = false;
		 wrapper.querySelector('.childCheckbox').checked = false;

		// Clear previous selections
		selectedTeeth.clear();
		selectedSurfaces = {};
		clearAllSelectedTeeth();

		// Add "Upper Jaw" as selected with 'Full Mouth' surfaces
		const jawId = "lowerJaw";
		selectedTeeth.add(jawId);
		selectedSurfaces[jawId] = ['Lower Jaw'];

		// Select all teeth in quadrant 1 inside toothchartAdult
		document.querySelectorAll('.toothchartAdult .tooth[data-quadrant="3"]').forEach(toothEl => {
			toothEl.classList.add('selected-tooth');
			toothEl.querySelectorAll('.polygon').forEach(p => p.classList.add('marked'));
		});
		
		document.querySelectorAll('.toothchartAdult .tooth[data-quadrant="4"]').forEach(toothEl => {
			toothEl.classList.add('selected-tooth');
			toothEl.querySelectorAll('.polygon').forEach(p => p.classList.add('marked'));
		});

		// Render summary showing only "Upper Jaw"
		renderSelectedTeethData();
	} else if (this.id === 'quadrant-1') {
		//alert(this.value);
		// Show adult chart
		 wrapper.querySelector('.toothchartAdult').style.display = 'block';

		// Hide child chart and uncheck other checkboxes
		 wrapper.querySelector('.toothchartChild').style.display = 'none';
		 wrapper.querySelector('.adultCheckbox').checked = false;
		 wrapper.querySelector('.childCheckbox').checked = false;

		// Clear previous selections
		selectedTeeth.clear();
		selectedSurfaces = {};
		clearAllSelectedTeeth();

		// Add "Upper Jaw" as selected with 'Full Mouth' surfaces
		const jawId = "quadrant-1";
		selectedTeeth.add(jawId);
		selectedSurfaces[jawId] = ['Quadrant-1'];

		// Select all teeth in quadrant 1 inside toothchartAdult
		document.querySelectorAll('.toothchartAdult .tooth[data-quadrant="1"]').forEach(toothEl => {
			toothEl.classList.add('selected-tooth');
			toothEl.querySelectorAll('.polygon').forEach(p => p.classList.add('marked'));
		});
		

		// Render summary showing only "Upper Jaw"
		renderSelectedTeethData();
	} else if (this.id === 'quadrant-2') {
		//alert(this.value);
		// Show adult chart
		 wrapper.querySelector('.toothchartAdult').style.display = 'block';

		// Hide child chart and uncheck other checkboxes
		 wrapper.querySelector('.toothchartChild').style.display = 'none';
		 wrapper.querySelector('.adultCheckbox').checked = false;
		 wrapper.querySelector('.childCheckbox').checked = false;

		// Clear previous selections
		selectedTeeth.clear();
		selectedSurfaces = {};
		clearAllSelectedTeeth();

		// Add "Upper Jaw" as selected with 'Full Mouth' surfaces
		const jawId = "quadrant-2";
		selectedTeeth.add(jawId);
		selectedSurfaces[jawId] = ['Quadrant-2'];

		// Select all teeth in quadrant 1 inside toothchartAdult
		document.querySelectorAll('.toothchartAdult .tooth[data-quadrant="2"]').forEach(toothEl => {
			toothEl.classList.add('selected-tooth');
			toothEl.querySelectorAll('.polygon').forEach(p => p.classList.add('marked'));
		});
		

		// Render summary showing only "Upper Jaw"
		renderSelectedTeethData();
	} else if (this.id === 'quadrant-3') {
		//alert(this.value);
		// Show adult chart
		 wrapper.querySelector('.toothchartAdult').style.display = 'block';

		// Hide child chart and uncheck other checkboxes
		 wrapper.querySelector('.toothchartChild').style.display = 'none';
		 wrapper.querySelector('.adultCheckbox').checked = false;
		 wrapper.querySelector('.childCheckbox').checked = false;

		// Clear previous selections
		selectedTeeth.clear();
		selectedSurfaces = {};
		clearAllSelectedTeeth();

		// Add "Upper Jaw" as selected with 'Full Mouth' surfaces
		const jawId = "quadrant-3";
		selectedTeeth.add(jawId);
		selectedSurfaces[jawId] = ['Quadrant-3'];

		// Select all teeth in quadrant 1 inside toothchartAdult
		document.querySelectorAll('.toothchartAdult .tooth[data-quadrant="3"]').forEach(toothEl => {
			toothEl.classList.add('selected-tooth');
			toothEl.querySelectorAll('.polygon').forEach(p => p.classList.add('marked'));
		});
		

		// Render summary showing only "Upper Jaw"
		renderSelectedTeethData();
	} else if (this.id === 'quadrant-4') {
		//alert(this.value);
		// Show adult chart
		 wrapper.querySelector('.toothchartAdult').style.display = 'block';

		// Hide child chart and uncheck other checkboxes
		 wrapper.querySelector('.toothchartChild').style.display = 'none';
		 wrapper.querySelector('.adultCheckbox').checked = false;
		 wrapper.querySelector('.childCheckbox').checked = false;

		// Clear previous selections
		selectedTeeth.clear();
		selectedSurfaces = {};
		clearAllSelectedTeeth();

		// Add "Upper Jaw" as selected with 'Full Mouth' surfaces
		const jawId = "quadrant-4";
		selectedTeeth.add(jawId);
		selectedSurfaces[jawId] = ['Quadrant-4'];

		// Select all teeth in quadrant 1 inside toothchartAdult
		document.querySelectorAll('.toothchartAdult .tooth[data-quadrant="4"]').forEach(toothEl => {
			toothEl.classList.add('selected-tooth');
			toothEl.querySelectorAll('.polygon').forEach(p => p.classList.add('marked'));
		});
		

		// Render summary showing only "Upper Jaw"
		renderSelectedTeethData();
	} else if (this.id === 'upperAnterior') {
		// Show adult chart
		 wrapper.querySelector('.toothchartAdult').style.display = 'block';

		// Deselect other charts and checkboxes
		 wrapper.querySelector('.toothchartChild').style.display = 'none';
		 wrapper.querySelector('.adultCheckbox').checked = false;
		 wrapper.querySelector('.childCheckbox').checked = false;

		// Clear current selections
		selectedTeeth.clear();
		selectedSurfaces = {};
		clearAllSelectedTeeth();
		var toothId = "upperAnterior";
		selectedTeeth.add(toothId);
		selectedSurfaces[toothId] = ['Upper Anterior'];
		
		const anteriorDisplayIds = ['11', '12', '13', '21', '22', '23'];
		
		document.querySelectorAll('.toothchartAdult .tooth').forEach(toothEl => {
			const displayId = toothEl.getAttribute('data-displayid');
			if (anteriorDisplayIds.includes(displayId)) {
				toothEl.classList.add('selected-tooth');
				toothEl.querySelectorAll('.polygon').forEach(p => p.classList.add('marked'));
			}
		});
		
		// Render only "Full Mouth" in summary
		renderSelectedTeethData();
	}else if (this.id === 'lowerAnterior') {
		// Show adult chart
		 wrapper.querySelector('.toothchartAdult').style.display = 'block';

		// Deselect other charts and checkboxes
		 wrapper.querySelector('.toothchartChild').style.display = 'none';
		 wrapper.querySelector('.adultCheckbox').checked = false;
		 wrapper.querySelector('.childCheckbox').checked = false;

		// Clear current selections
		selectedTeeth.clear();
		selectedSurfaces = {};
		clearAllSelectedTeeth();
		var toothId = "lowerAnterior";
		selectedTeeth.add(toothId);
		selectedSurfaces[toothId] = ['Lower Anterior'];

		const anteriorDisplayIds = ['31', '32', '33', '41', '42', '43'];

		document.querySelectorAll('.toothchartAdult .tooth').forEach(toothEl => {
			const displayId = toothEl.getAttribute('data-displayid');
			if (anteriorDisplayIds.includes(displayId)) {
				toothEl.classList.add('selected-tooth');
				toothEl.querySelectorAll('.polygon').forEach(p => p.classList.add('marked'));
			}
		});

		// Render only "Full Mouth" in summary
		renderSelectedTeethData();
	}



      // You can add more else if for quadrants or anterior selections similarly
    }else{
		//alert(this.id);
		clearAllSelectedTeeth();
		document.getElementById('toothchartAdult').style.display = 'none';
	}
		  this.classList.add('selected-checkbox');
		} else {
		  this.classList.remove('selected-checkbox');
		}
	  });
	});
	


function clearAllSelectedTeeth() {
	// Clear tracking objects
	selectedTeeth.clear();
	selectedSurfaces = {};

	// Deselect in chart
	document.querySelectorAll('.tooth.selected-tooth').forEach(toothEl => {
		toothEl.classList.remove('selected-tooth');
		toothEl.querySelectorAll('.polygon.marked').forEach(p => p.classList.remove('marked'));
	});

	// Clear UI selected data list
	renderSelectedTeethData();
}


    function renderSelectedTeethData() {
  // Clear all display containers
  document.querySelectorAll('.selectedToothData').forEach(container => {
    container.innerHTML = '';
  });

  const teethList = [];
  const surfaceList = [];

  [...selectedTeeth].forEach(toothId => {
    const element = document.querySelector(`li.tooth[data-displayId="${toothId}"]`);
    const displayId = element ? element.getAttribute('data-displayid') : toothId;
    const img = getToothImage(toothId);
    const surfacesArr = selectedSurfaces[toothId] || [];
    const surfaces = surfacesArr.join(', ');

    // Only push if valid
    if (displayId) {
      teethList.push(displayId);
    }

    if (surfacesArr.length > 0) {
      surfaceList.push(surfaces);
    }

    const div = document.createElement('div');
    div.className = 'toothRecord';
    div.setAttribute('data-toothid', toothId);

    const isGroup = [
      'Full Mouth', 'upperJaw', 'lowerJaw',
      'quadrant-1', 'quadrant-2', 'quadrant-3', 'quadrant-4',
      'upperAnterior', 'lowerAnterior'
    ].includes(toothId);

    div.innerHTML = isGroup
      ? `<span class="custom-badge">${displayId}</span> ${surfaces}`
      : `
        <img src="https://tootpro.drcareams.com/images/tooth/${img}" alt="Tooth Icon" width="24" height="24"> - 
        <span class="custom-badge">${displayId}</span> 
        ${surfaces} -  
        <span class="badge badge-center rounded-pill bg-danger">
          <span class="deleteIcon" data-toothid="${toothId}">
            <i class="fa fa-trash"></i>
          </span>
        </span>`;

    // Append to all display containers
    document.querySelectorAll('.selectedToothData').forEach(container => {
      container.appendChild(div.cloneNode(true));
    });
  });

  // ✅ Set cleaned text without trailing commas
  $('#examinationFindingsTeeth').text(teethList.join(', ').trim());
  $('#examinationFindingsSurfaces').text(surfaceList.join(', ').trim());

  // Attach delete events
  document.querySelectorAll('.deleteIcon').forEach(icon => {
    icon.addEventListener('click', function () {
      const toothId = this.getAttribute('data-toothid');
      selectedTeeth.delete(toothId);
      delete selectedSurfaces[toothId];

      const element = document.querySelector(`.tooth[data-displayId="${toothId}"]`);
      if (element) {
        element.classList.remove('selected-tooth');
        element.querySelectorAll('.polygon.marked').forEach(p => p.classList.remove('marked'));
      }

      renderSelectedTeethData();
    });
  });
}






    // Handle surface marking
    wrapper.querySelectorAll('.tooth svg .polygon').forEach(polygon => {
		polygon.addEventListener('click', function (e) {
			e.stopPropagation();
			const toothEl = this.closest('.tooth');
			const toothId = toothEl.getAttribute('data-displayId');
			const surface = this.getAttribute('value');
			this.classList.toggle('marked');

			if (!selectedSurfaces[toothId]) {
				selectedSurfaces[toothId] = [];
			}

			const index = selectedSurfaces[toothId].indexOf(surface);
			if (index >= 0) {
				selectedSurfaces[toothId].splice(index, 1);
			} else {
				selectedSurfaces[toothId].push(surface);
			}

			if (selectedSurfaces[toothId].length > 0) {
				selectedTeeth.add(toothId);
				toothEl.classList.add('selected-tooth');
			} else {
				delete selectedSurfaces[toothId];
				toothEl.classList.remove('selected-tooth');
			}

			renderSelectedTeethData();
		});
	});

    // Tooth base click
	document.querySelectorAll('.tooth').forEach(tooth => {
		tooth.addEventListener('click', function (e) {
			if (e.target.tagName.toLowerCase() === 'polygon') return;
			
			const toothId = this.getAttribute('data-displayId');
			
			if (!selectedTeeth.has(toothId)) {
				selectedTeeth.add(toothId);
				this.classList.add('selected-tooth');
				if (!selectedSurfaces[toothId]) selectedSurfaces[toothId] = [];
				renderSelectedTeethData();
			}
		});
	});


    // Chart switcher (adult vs child)
    chartTypeButtons.forEach(button => {
        button.addEventListener('click', function () {
            chartTypeButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const chartType = this.getAttribute('data-chart-type');
            if (chartType === 'adult') {
                adultChart.style.display = 'flex';
                childChart.style.display = 'none';
            } else {
                adultChart.style.display = 'none';
                childChart.style.display = 'flex';
            }
            clearAllSelections();
        });
    });

    // Reset chart selections
    function clearAllSelections() {
        selectedTeeth.forEach(toothId => {
            const element = document.querySelector(`.tooth[data-displayid="${toothId}"]`);
            if (element) {
                element.classList.remove('selected-tooth');
                element.querySelectorAll('.polygon.marked').forEach(p => p.classList.remove('marked'));
            }
        });
        selectedTeeth.clear();
        selectedSurfaces = {};
        renderSelectedTeethData();
    }
	
	
	
	const adultCheckboxEl =  wrapper.querySelector('.adultCheckbox');
  const childCheckboxEl =  wrapper.querySelector('.childCheckbox');

  function toggleToothCharts() {
     wrapper.querySelector('.toothchartAdult').style.display = adultCheckboxEl.checked ? 'block' : 'none';
    wrapper.querySelector('.toothchartChild').style.display = childCheckboxEl.checked ? 'block' : 'none';
  }

  if (adultCheckboxEl && childCheckboxEl) {
    adultCheckboxEl.addEventListener('change', toggleToothCharts);
    childCheckboxEl.addEventListener('change', toggleToothCharts);
    toggleToothCharts(); // initial load
  }
  
  


});
</script>

<script>

$(document).ready(function() {
    $('.tab-item').click(function() {
		
        var tabId = $(this).data('tab');

        // Remove 'active' class from all tab items
        $('.tab-item').removeClass('active');

        // Add 'active' class to clicked tab
        $(this).addClass('active');

        // Hide all tab contents
        $('.tab-content').hide();

        // Show the selected tab's content
        $('#' + tabId).show();
    });

    // Initialize: hide all and show the first active tab
    $('.tab-content').hide();
    var defaultTab = $('.tab-item.active').data('tab');
    $('#' + defaultTab).show();
});
</script>


<script>
function medRecordsOpenTab(evt, tabName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("med-records-tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("med-records-tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" med-records-active", "");
  }
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " med-records-active";
}

// File upload name display
document.querySelector('.med-records-file-upload-input').addEventListener('change', function(e) {
  const fileName = e.target.files.length > 0 ? e.target.files[0].name : 'No file chosen';
  document.querySelector('.med-records-file-name').textContent = fileName;
});

// Get the element with id="defaultOpen" and click on it
document.getElementById("medRecordsDefaultOpen").click();
</script>

<script>
function InvRecordsOpenTab(evt, tabName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("inv-records-tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("inv-records-tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" inv-records-active", "");
  }
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " inv-records-active";
}

// File upload name display
document.querySelector('.inv-records-file-upload-input').addEventListener('change', function(e) {
  const fileName = e.target.files.length > 0 ? e.target.files[0].name : 'No file chosen';
  document.querySelector('.inv-records-file-name').textContent = fileName;
});

// Get the element with id="defaultOpen" and click on it
document.getElementById("invRecordsDefaultOpen").click();
</script>

<script>
  document.querySelectorAll('.investigation-tab').forEach(tab => {
    tab.addEventListener('click', function () {
      // Toggle active class
      document.querySelectorAll('.investigation-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');

      // Show/hide content
      const tabId = this.getAttribute('data-tab');
      document.querySelectorAll('.investigation-content').forEach(c => c.style.display = 'none');
      document.getElementById(tabId).style.display = 'block';
    });
  });
  
  document.getElementById('addChiefComplaint').addEventListener('click', function () {
   //const complaint = document.getElementById('chiefComplaintText').value.trim();
	//const complaintSelect = document.getElementById('chiefComplaintText');
	//const complaint = complaintSelect.options[complaintSelect.selectedIndex].text;
	const complaintSelect = document.querySelector('select[name="chief_complaint_id"]');
	
	const complaint = complaintSelect.options[complaintSelect.selectedIndex].text;
	
    const notes = document.getElementById('notesText').value.trim();
    const patientId = <?php echo $patientid?>; // Change to dynamic later

    if (!complaint || Object.keys(selectedSurfaces).length === 0) {
        //alert('Please enter complaint and select at least one tooth.');
        //return;
    }

    const teethData = Object.entries(selectedSurfaces).map(([id, surfaces]) => {
        const displayEl = document.querySelector(`.tooth[data-id="${id}"]`);
        const displayId = displayEl ? displayEl.getAttribute('data-displayid') : id;
        return { id, displayId, surfaces };
    });

    $.ajax({
        url: admin_url + 'toot/add_chief_complaint',
        method: 'POST',
        data: {
            patient_id: patientId,
            complaint,
            notes,
            teeth_data: JSON.stringify(teethData),
        },
        success: function (response) {
            let res;
            try {
                res = JSON.parse(response);
            } catch (e) {
                alert('Invalid response from server.');
                return;
            }

            if (res.status) {
                alert_float("success", res.message || "success.");
				
				setTimeout(function() {
                    location.reload();
                }, 1000); // Wait 1.5 seconds to let user see the message
                // Optionally, refresh a table/list of complaints
            } else {
                alert(res.message || 'Failed to save complaint.');
            }
        },
        error: function () {
            alert('An error occurred while saving complaint.');
        }
    });
});


$(document).on('click', '.delete-complaint', function () {
    if (!confirm('Are you sure you want to delete this complaint?')) return;

    const complaintId = $(this).data('id');

    $.post(admin_url + 'toot/delete_chief_complaints', {
        id: complaintId
    }, function (response) {
        let res = JSON.parse(response);
        if (res.status) {
			alert_float("success", res.message || "success.");
            setTimeout(function() {
                    location.reload();
                }, 1000); // Wait 1.5 seconds to let user see the message
        } else {
			alert_float("danger", response.message || "Failed.");
        }
    });
});

function medRecordsOpenTab(evt, tabName) {
    let tabcontent = document.querySelectorAll(".med-records-tabcontent");
    tabcontent.forEach(tc => tc.style.display = "none");

    let tablinks = document.querySelectorAll(".med-records-tablinks");
    tablinks.forEach(tl => tl.classList.remove("med-records-active"));

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.classList.add("med-records-active");
}

</script>


<script>
$(document).ready(function () {
    const patient_id = <?= $patientid ?>;

    // Switch between medical and dental investigation tabs
    $('.investigation-tab').on('click', function () {
        $('.investigation-tab').removeClass('active');
        $(this).addClass('active');

        const selectedTab = $(this).data('tab');
        $('.investigation-content').hide();
        $('#' + selectedTab).show();
    });

    // Helper: Load investigation list
    function loadInvestigationList(type) {
        $.ajax({
            url: admin_url + 'toot/get_investigations_by_type',
            method: 'GET',
            data: { patient_id: patient_id, type: type },
            success: function (res) {
                const response = JSON.parse(res);
                const tbody = $('#' + type + '-table tbody');
                tbody.empty();

                if (response.success && response.data.length > 0) {
                    $.each(response.data, function (_, item) {
                        tbody.append(`
                            <tr>
                                <td>${escapeHtml(item.problem)}</td>
                                <td>${escapeHtml(item.notes || '')}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-investigation" data-id="${item.id}" data-type="${type}">Delete</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="3" class="text-center">No records</td></tr>');
                }
            }
        });
    }

    // Helper: Escape HTML to prevent XSS
    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    // Add Medical Investigation
    $('#add-medical-btn').on('click', function () {
        const problem = $('#medical-investigations-problem option:selected').text();
        const notes = $('#medical-investigations-notes').val().trim();
        if (!problem) {
           // alert('Please enter a medical problem.');
           // return;
        }
		//alert(problem);
        $.post(admin_url + 'toot/add_investigation', {
            patient_id: patient_id,
            type: 'medical',
            problem: problem,
            notes: notes
        }, function (res) {
            const response = JSON.parse(res);
            if (response.success) {
                $('#medical-investigations-problem, #medical-investigations-notes').val('');
                loadInvestigationList('medical');
				alert_float("success", res.message || "success.");
            } else {
                //alert('Failed to add medical investigation.');
            }
        });
    });

    // Add Dental Investigation
    $('#add-dental-btn').on('click', function () {
		const problem = $('#dental-problem option:selected').text();
        const notes = $('#dental-notes').val().trim();

        if (!problem) {
            alert('Please enter a dental problem.');
            return;
        }

        $.post(admin_url + 'toot/add_investigation', {
            patient_id: patient_id,
            type: 'dental',
            problem: problem,
            notes: notes
        }, function (res) {
            const response = JSON.parse(res);
            if (response.success) {
                $('#dental-problem, #dental-notes').val('');
                loadInvestigationList('dental');
				alert_float("success", res.message || "success.");
            } else {
                alert('Failed to add dental investigation.');
            }
        });
    });

    // Delete Investigation
    $(document).on('click', '.delete-investigation', function () {
        const id = $(this).data('id');
        const type = $(this).data('type');

        if (confirm('Are you sure you want to delete this entry?')) {
            $.post(admin_url + 'toot/delete_investigation/' + id, function (res) {
                const response = JSON.parse(res);
                if (response.success) {
                    loadInvestigationList(type);
					alert_float("success", res.message || "success.");
                } else {
                    alert('Failed to delete investigation.');
                }
            });
        }
    });

    // Initial load for both tabs
    loadInvestigationList('medical');
    loadInvestigationList('dental');
});
</script>
<script>
//Medical Problems
$(document).ready(function () {
    const patient_id = <?= $patientid ?>;

    // Tab switching
    $('.medical-history-tab').on('click', function () {
        $('.medical-history-tab').removeClass('active');
        $(this).addClass('active');

        const selectedTab = $(this).data('tab');
        $('.medical-history-content').hide();
        $('#' + selectedTab).show();
    });

    // Escape HTML
    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    // Load Medical Problems
    function loadMedicalProblems() {
        $.get(admin_url + 'toot/get_medical_problems', { patient_id: patient_id }, function (res) {
            const response = JSON.parse(res);
            const tbody = $('#medical-table-body');
            tbody.empty();

            if (response.success && response.data.length > 0) {
                response.data.forEach(item => {
                    tbody.append(`
                        <tr>
                            <td>${escapeHtml(item.problem_name)}</td>
                            <td>${escapeHtml(item.notes || '')}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deleteProblem(${item.id})">Delete</button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="3" class="text-center">No records</td></tr>');
            }
        });
    }

    // Load Present Medications
    function loadPresentMedications() {
        $.get(admin_url + 'toot/get_present_medications', { patient_id: patient_id }, function (res) {
            const response = JSON.parse(res);
            const tbody = $('#medication-table-body');
            tbody.empty();
            if (response.success && response.data.length > 0) {
                response.data.forEach(item => {
                    const imgTag = item.file
							? `<a href="/uploads/medications/${item.file}" target="_blank"><img src="/uploads/medications/${item.file}" width="50" height="50"></a>`
							: `<span class="text-muted">No Image</span>`;


                    tbody.append(`
                        <tr>
                            <td>${imgTag}</td>
                            <td>${escapeHtml(item.notes || '')}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deleteMedication(${item.id})">Delete</button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                tbody.append('<tr><td colspan="3" class="text-center">No records</td></tr>');
            }
        });
    }

    // Add Medical Problem
    $('#add-medical-problems-btn').on('click', function () {
        const problem = $('#medical-problems').text();
        const notes = $('#medical-notes').val().trim();

        if (!problem || problem === '--Select--') {
            alert('Please select a medical problem.');
            return;
        }

        $.post(admin_url + 'toot/add_medical_problems', {
            patient_id: patient_id,
            problem_name: problem,
            notes: notes
        }, function (res) {
            const response = JSON.parse(res);
            if (response.success) {
                $('#medical-notes').val('');
                loadMedicalProblems();
				alert_float("success", res.message || "success.");
            } else {
                alert('Failed to add medical problem.');
            }
        });
    });

    // Add Present Medication
    $('#add-medication-btn').on('click', function () {
        const formData = new FormData($('#presentMedicationForm')[0]);

        $.ajax({
            url: admin_url + 'toot/add_present_medications',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                const response = JSON.parse(res);
                if (response.success) {
                    $('#presentMedicationForm')[0].reset();
                    loadPresentMedications();
					alert_float("success", res.message || "success.");
                } else {
                    alert('Failed to add medication.');
                }
            }
        });
    });

    // Delete Problem
    window.deleteProblem = function (id) {
        if (confirm('Are you sure you want to delete this entry?')) {
            $.post(admin_url + 'toot/delete_medical_problems/' + id, function (res) {
                const response = JSON.parse(res);
                if (response.success) {
                    loadMedicalProblems();
					alert_float("success", res.message || "success.");
                } else {
                    alert('Failed to delete entry.');
                }
            });
        }
    };

    // Delete Medication
    window.deleteMedication = function (id) {
        if (confirm('Are you sure you want to delete this entry?')) {
            $.post(admin_url + 'toot/delete_present_medications/' + id, function (res) {
                const response = JSON.parse(res);
                if (response.success) {
                    loadPresentMedications();
					alert_float("success", res.message || "success.");
                } else {
                    alert('Failed to delete entry.');
                }
            });
        }
    };

    // Initial Load
    loadMedicalProblems();
    loadPresentMedications();
});
</script>
<script>
// JS for dynamic medicine prescription functionality
$(document).ready(function () {
	const patient_id = <?= $patientid ?>;
	loadPreviousPrescriptions(patient_id);
	loadExaminationFindings(patient_id);
	loadTreatmentPlans(patient_id);
	loadTreatmentHistory(patient_id);
	loadPastDentalHistory(patient_id);
	loadBillingData(patient_id);
	
	 
    let medicines = [];

    // Add medicine row
    $('#addMedicineBtn').on('click', function () {
        const medicine = $('select[name="medicine_id"] option:selected').text();
        const medicine_id = $('select[name="medicine_id"]').val();
        const frequency = $('select[name="frequency_id"] option:selected').text();
        const frequency_id = $('select[name="frequency_id"]').val();
        const duration = $('input[name="duration"]').val();
        const usage = $('select[name="usage_id"] option:selected').text();
        const usage_id = $('select[name="usage_id"]').val();

        if (!medicine_id || !frequency_id || !duration || !usage_id) {
            alert('Please fill all fields');
            return;
        }

        const index = medicines.length;
        medicines.push({ medicine, frequency, duration, usage });

        const newRow = `<tr data-index="${index}">
            <td>${medicine}</td>
            <td>${frequency}</td>
            <td>${duration}</td>
            <td>${usage}</td>
            <td><button type="button" class="btn btn-danger btn-sm deleteMedicine">Delete</button></td>
        </tr>`;

        $('#medicineListTable tbody').append(newRow);

        // Reset fields
        $('select[name="medicine_id"]').val('');
        $('select[name="frequency_id"]').val('');
        $('input[name="duration"]').val('');
        $('select[name="usage_id"]').val('');
    });

    // Delete medicine row
    $('#medicineListTable').on('click', '.deleteMedicine', function () {
        const row = $(this).closest('tr');
        const index = row.data('index');
        medicines.splice(index, 1);
        row.remove();
    });

    // Save prescription
    $('#prescriptionForm').on('submit', function (e) {
        e.preventDefault();

        if (medicines.length === 0) {
            alert('Please add at least one medicine.');
            return;
        }

        const formData = {
			patient_id: $('input[name="patient_id"]').val(),
			notes: $('textarea[name="prescriptionFormnotes"]').val(),
			prescription_by: $('select[name="prescriptionFormdoctor_id"]').val(),
			medicines: medicines,
			'<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
		};
		

        $.ajax({
			url: admin_url + 'toot/add_toot_prescription',
			method: 'POST',
			data: formData,
			success: function (res) {
				const response = JSON.parse(res);
				if (response.success) {
					alert_float("success", response.message || "Prescription saved successfully.");
					loadPreviousPrescriptions(patient_id);
					$('#prescriptionForm')[0].reset();
				} else {
					alert_float("danger", response.message || "Failed to save prescription.");
				}
			},
			error: function () {
				alert_float("danger", "Error occurred while saving prescription.");
			}
		});
    });

    // Optional: Load previous prescriptions (demo only, replace with real AJAX)
    function loadPreviousPrescriptions(patient_id) {
		$.ajax({
			url: admin_url + 'toot/get_previous_prescriptions',
			method: 'GET',
			data: { patient_id: patient_id },
			success: function (res) {
				const response = JSON.parse(res);
				const tbody = $('#previousPrescriptionTable tbody');
				tbody.empty();

				if (response.success && response.data.length > 0) {
					response.data.forEach(function (item) {
						const row = `
							<tr>
								<td>${item.prescription_code}</td>
								<td>${item.date}</td>
								<td>${item.doctor_name}</td>
								<td class="presc-ui-action-icons">
									<i class="glyphicon glyphicon-trash text-danger" onclick="deletePrescription(${item.id})"></i>
									<i class="glyphicon glyphicon-print text-primary" onclick="printPrescription(${item.id})"></i>
								</td>
							</tr>
						`;
						tbody.append(row);
					});
				} else {
					tbody.append('<tr><td colspan="4" class="text-center">No previous prescriptions found.</td></tr>');
				}
			},
			error: function () {
				alert('Failed to load previous prescriptions.');
			}
		});
	}
	
	
window.deletePrescription = function(id) {
        if (!confirm("Are you sure you want to delete this prescription?")) return;

        $.ajax({
            url: admin_url + 'toot/delete_prescriptions/' + id,
            type: 'POST',
            data: {
                [$('#csrf_token_name').val()]: $('#csrf_token_hash').val()
            },
            success: function (response) {
                let res = JSON.parse(response);
                if (res.success) {
                    alert_float("success", res.message || "Prescription deleted successfully.");
                    loadPreviousPrescriptions(patient_id); // Reload updated table
                } else {
                    alert_float("danger", res.message || "Failed to delete prescription.");
                }
            },
            error: function () {
                alert_float("danger", "An error occurred while deleting the prescription.");
            }
        });
    };




$('#addExaminationFinding').on('click', function () {
	
	const patient_id = $('input[name="patient_id"]').val();
	const complaint = $('#examinationFindingSelect').text();
	const notes = $('[name="examinationFindingsNotesText"]').val();

	const teethData = []; // array of {tooth_id, surfaces}

	selectedTeeth.forEach(toothId => {
		const surfaceList = selectedSurfaces[toothId] || [];
		teethData.push({
			tooth_id: toothId,
			surfaces: surfaceList.join(', ') // comma-separated surfaces
		});
	});

	// Convert to FormData for file upload support
	const formData = new FormData();
	formData.append('patient_id', patient_id);
	formData.append('complaint', complaint);
	formData.append('notes', notes);
	formData.append('teethData', JSON.stringify(teethData));

	// Append CSRF token
	formData.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());

	// Append image files
	const imageFiles = $('#examImages')[0].files;
	for (let i = 0; i < imageFiles.length; i++) {
		formData.append('images[]', imageFiles[i]);
	}

	$.ajax({
		url: admin_url + 'toot/add_examination_findings',
		type: 'POST',
		data: formData,
		contentType: false,
		processData: false,
		success: function (res) {
			const response = JSON.parse(res);
			if (response.success) {
				alert_float("success", response.message);
				loadExaminationFindings(patient_id);

				// Optionally reset form
				$('#examinationFindingSelect').val('');
				$('[name="examinationFindingsNotesText"]').val('');
				$('#examImages').val('');
			} else {
				alert_float("danger", response.message || "Failed to save examination findings.");
			}
		},
		error: function () {
			alert_float("danger", "Error occurred while saving.");
		}
	});

});




$('#addPastDentalHistory').on('click', function () {
	
	const patient_id = $('input[name="patient_id"]').val();
	const complaint = $('#pastDentalHistorySelect option:selected').text();
	const notes = $('[name="pastDentalHistoryNotesText"]').val();
	const place = $('[name="placeDentalHistory"]').val();
	const opinion = $('[name="ourOpinion"]').val();

	const teethData = []; // array of {tooth_id, surfaces}

	selectedTeeth.forEach(toothId => {
		const surfaceList = selectedSurfaces[toothId] || [];
		teethData.push({
			tooth_id: toothId,
			surfaces: surfaceList.join(', ') // comma-separated surfaces
		});
	});

	// Convert to FormData for file upload support
	const formData = new FormData();
	formData.append('patient_id', patient_id);
	formData.append('complaint', complaint);
	formData.append('notes', notes);
	formData.append('place', place);
	formData.append('opinion', opinion);
	formData.append('teethData', JSON.stringify(teethData));

	// Append CSRF token
	formData.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());

	$.ajax({
		url: admin_url + 'toot/add_past_dental_history',
		type: 'POST',
		data: formData,
		contentType: false,
		processData: false,
		success: function (res) {
			const response = JSON.parse(res);
			if (response.success) {
				alert_float("success", response.message);
				loadPastDentalHistory(patient_id);

				// Optionally reset form
				$('#pastDentalHistory').val('');
				$('[name="pastDentalHistoryNotesText"]').val('');
				$('[name="placeDentalHistory"]').val('');
				$('[name="ourOpinion"]').val('');
				  selectedTeeth.clear();
				selectedSurfaces = {};
			} else {
				alert_float("danger", response.message || "Failed to save examination findings.");
			}
		},
		error: function () {
			alert_float("danger", "Error occurred while saving.");
		}
	});

});



});

function loadExaminationFindings(patient_id) {
    $.ajax({
        url: admin_url + 'toot/get_all_examination_findings',
        method: 'GET',
        data: { patient_id: patient_id },
        success: function (res) {
            const response = JSON.parse(res);
            const tbody = $('#ExaminationFindingsTableBody');
            tbody.empty();

            if (response.success && response.data.length > 0) {
                response.data.forEach(function (item) {
                    let imageHtml = '';

                    if (item.images) {
                        const imageArray = item.images.split('|'); // split using pipe
                        imageArray.forEach(imgPath => {
                            const trimmedPath = imgPath.trim();
                            if (trimmedPath) {
                                imageHtml += `
                                    <a href="${site_url + trimmedPath}" target="_blank">
                                        <img src="${site_url + trimmedPath}" height="40" width="40" style="margin-right: 5px;margin-top: 5px;" />
                                    </a>`;
                            }
                        });
                    }

                    const row = `
                        <tr>
                            <td>${item.tooth_info || ''}</td>
                            <td>${item.complaint || ''}</td>
                            <td>${item.notes || ''}</td>
                            <td>${imageHtml}</td>
                            <td>${item.created_at}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deleteExaminationFinding(${item.id})">Delete</button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                tbody.append('<tr><td colspan="6" class="text-center">No examination findings found.</td></tr>');
            }
        },
        error: function () {
            alert('Failed to load examination findings.');
        }
    });
}


function loadPastDentalHistory(patient_id) {
    $.ajax({
        url: admin_url + 'toot/get_all_past_dental_history',
        method: 'GET',
        data: { patient_id: patient_id },
        success: function (res) {
            const response = JSON.parse(res);
            const tbody = $('#PastDentalHistoryTableBody');
            tbody.empty();

            if (response.success && response.data.length > 0) {
                response.data.forEach(function (item) {
                    let teethDisplay = '';
                    try {
                        const teethArray = JSON.parse(item.teeth_data);
                        teethDisplay = teethArray.map(t => `${t.tooth_id} (${t.surfaces})`).join(', ');
                    } catch (e) {
                        teethDisplay = item.teeth_data || '';
                    }

                    const row = `
                        <tr>
                            <td>${teethDisplay}</td>
                            <td>${item.complaint || ''}</td>
                            <td>${item.notes || ''}</td>
                            <td>${item.place || ''}</td>
                            <td>${item.opinion || ''}</td>
                            <td>${item.created_at || ''}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deletePastDentalHistory(${item.id})">Delete</button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                tbody.append('<tr><td colspan="7" class="text-center">No past dental history found.</td></tr>');
            }
        },
        error: function () {
            alert('Failed to load past dental history.');
        }
    });
}




	function printPrescription(id) {
		const url = admin_url + 'toot/print_prescription/' + id;
		window.open(url, '_blank', 'width=800,height=600');
	}
	
	
const patient_id = <?= $patientid ?>;
function deleteExaminationFinding(id) {
    if (!confirm('Are you sure you want to delete this examination finding?')) return;

    $.ajax({
        url: admin_url + 'toot/delete_examination_findings',
        method: 'POST',
        data: { id: id },
        success: function(res) {
            const response = JSON.parse(res);
            if (response.success) {
                loadExaminationFindings(patient_id);
				alert_float("success", res.message || "Examination Findings deleted successfully.");
            } else {
                alert('Failed to delete examination finding.');
            }
        },
        error: function() {
            alert('Error occurred while deleting.');
        }
    });
}	

function deletePastDentalHistory(id) {
    if (!confirm('Are you sure you want to delete this past dental history record?')) return;

    $.ajax({
        url: admin_url + 'toot/delete_past_dental_history',
        method: 'POST',
        data: { id: id },
        success: function(res) {
            const response = JSON.parse(res);
            if (response.success) {
                loadPastDentalHistory(patient_id);
                alert_float("success", response.message || "Past dental history deleted successfully.");
            } else {
                alert_float("danger", response.message || "Failed to delete past dental history.");
            }
        },
        error: function() {
            alert_float("danger", "Error occurred while deleting.");
        }
    });
}


$('#addTreatmentPlan').on('click', function () {
	
	
	const teethData = []; // array of {tooth_id, surfaces}

	selectedTeeth.forEach(toothId => {
		const surfaceList = selectedSurfaces[toothId] || [];
		teethData.push({
			tooth_id: toothId,
			surfaces: surfaceList.join(', ') // comma-separated surfaces
		});
	});

    const data = {
        patient_id: $('input[name="patient_id"]').val(),
        treatmentPlanSelect: $('#treatmentPlanSelect option:selected').text(),
        treatmentSelect: $('#treatmentSelect option:selected').text(),
        companyPrice: $('[name="companyPrice"]').val(),
        tooth_info: JSON.stringify(teethData),
        units: $('[name="units"]').val(),
        companyCost: $('[name="companyCost"]').val(),
        finalAmount: $('[name="finalAmount"]').val(),
        plan_a: $('[name="plan_a"]').is(':checked') ? 1 : null,
        plan_b: $('[name="plan_b"]').is(':checked') ? 1 : null,
        plan_c: $('[name="plan_c"]').is(':checked') ? 1 : null,
        csrf_token_name: $('input[name="csrf_token_name"]').val()
    };

    $.post(admin_url + 'toot/add_treatment_plan', data, function (res) {
        const response = JSON.parse(res);
        if (response.success) {
            alert_float('success', response.message);
            loadTreatmentPlans(data.patient_id);
        }
    });
});

function deleteTreatmentPlan(id) {
  if (!confirm('Are you sure you want to delete this treatment plan?')) return;

  $.post(admin_url + 'toot/delete_treatment_plan', { id: id }, function (res) {
    const response = JSON.parse(res);
    if (response.success) {
      alert_float('success', response.message);
      loadTreatmentPlans(patient_id); // Reload all 4 tables
    } else {
      alert_float('danger', response.message || 'Delete failed.');
    }
  }).fail(function () {
    alert_float('danger', 'Error occurred while deleting.');
  });
}

function loadTreatmentPlans(patient_id) {
    $.get(admin_url + 'toot/get_treatment_plans', { patient_id }, function (res) {
        const response = JSON.parse(res);
        ['plan_a', 'plan_b', 'plan_c', 'accepted'].forEach(plan => {
            const table = $('#' + plan + '_table tbody');
            table.empty();

            response.data[plan].forEach(item => {
                const isAccepted = item.is_accepted == 1;

                const actionIcons = !isAccepted && plan !== 'accepted'
                    ? `
                        <i class="glyphicon glyphicon-ok text-success" title="Accept Plan"
                           style="cursor:pointer; font-size:16px;"
                           onclick="acceptPlan(${item.id})"></i>
                        <i class="glyphicon glyphicon-trash text-danger" title="Delete Plan"
                           style="cursor:pointer; margin-left:10px; font-size:16px;"
                           onclick="deleteTreatmentPlan(${item.id})"></i>
                      `
                    : ''; // No icons if accepted

                const row = `<tr>
					<td>${item.tooth_info}</td>
                    <td>${item.treatment_plan}</td>
                    <td>${item.treatment}</td>
                    <td>${item.final_amount}</td>
                    <td>${actionIcons}</td>
                </tr>`;

                table.append(row);
            });
        });
    });
}




function acceptPlan(id) {
    if (!confirm('Are you sure to accept this plan?')) return;
    $.post(admin_url + 'toot/accept_treatment_plan', { id }, function (res) {
        const response = JSON.parse(res);
        if (response.success) {
            alert_float('success', 'Plan accepted successfully');
            loadTreatmentPlans($('input[name="patient_id"]').val());
        }
    });
}




$(document).ready(function () {
   // const patient_id = $('input[name="patient_id"]').val();
	const patient_id = <?= $patientid ?>;

    // Load accepted treatments
    $.get(admin_url + 'toot/get_accepted_treatments/' + patient_id, function (res) {
        const response = JSON.parse(res);
        const treatmentSelect = $('select#treatment');
        treatmentSelect.empty().append('<option value="">--Select--</option>');
        response.data.forEach(item => {
            treatmentSelect.append(`<option value="${item.treatment}">${item.treatment_plan}>${item.treatment}</option>`);
        });
    });

    // When treatment changes, load teeth
    $('select#treatment').change(function () {
        const treatment = $(this).val();
        if (!treatment) return;

        $.get(admin_url + 'toot/get_teeth_by_treatment', { patient_id, treatment }, function (res) {
            const response = JSON.parse(res);
            const toothSelect = $('select#tooth_info');
            toothSelect.empty().append('<option value="">--Select--</option>');
            response.data.forEach(item => {
                toothSelect.append(`<option value="${item.id}">${item.tooth_info}</option>`);
            });
        });
    });

    // When tooth changes, load procedures
    $('select#tooth_info').change(function () {
        const treatment_plan = $('select#treatment_plan').val();
        const tooth_info = $(this).val();
		//alert(treatment_plan);
		//alert(tooth_info);
        //if (!treatment_plan || !tooth_info) return;

        $.get(admin_url + 'toot/get_procedures', { treatment_plan, tooth_info }, function (res) {
            const response = JSON.parse(res);
            const procedureSelect = $('select#treatment_procedure');
            procedureSelect.empty().append('<option value="">--Select--</option>');
            response.data.forEach(item => {
                procedureSelect.append(`<option value="${item.treatment_procedure_name}">${item.treatment_procedure_name}</option>`);
            });
        });
    });

    // Submit form
    $('#updateProcedureForm').submit(function (e) {
		e.preventDefault();
		let formData = new FormData(this);
		
		// Append selected text for any specific select fields
		const selectedDoctorText = $('#next_appointment_doctor option:selected').text();
		formData.append('next_appointment_doctor', selectedDoctorText);
		

		// Append selected text for any specific select fields
		const treatment_doctor = $('#treatment_doctor option:selected').text();
		formData.append('treatment_doctor', treatment_doctor);	

		$.ajax({
			url: admin_url + 'toot/add_treatment_procedure',
			method: 'POST',
			data: formData,
			contentType: false,
			processData: false,
			success: function (res) {
				const response = JSON.parse(res);
				if (response.success) {
					loadTreatmentHistory(patient_id);
					alert_float('success', response.message);
					$('#updateProcedureForm')[0].reset();
				} else {
					alert_float('danger', response.message);
				}
			},
			error: function () {
				alert_float('danger', 'Error occurred.');
			}
		});
	});

});
function loadTreatmentHistory(patient_id) {
    fetch(admin_url + 'toot/get_treatment_history?patient_id=' + patient_id)
    .then(res => res.json())
    .then(response => {
        const tbody = document.getElementById('treatmentHistoryTableBody');
        tbody.innerHTML = ''; // Clear table body

        if (response.success && response.data.length > 0) {
            response.data.forEach(item => {
                const tr = document.createElement('tr');

                tr.innerHTML = `
                    <td>${item.treatment_tooth_info || ''}</td>
                    <td>${item.treatment || ''}</td>
                    <td>${item.procedure || ''}</td>
                    <td>${item.procedure_notes || ''}</td>
                    <td>${item.further_procedure || ''}</td>
                    <td>${item.treatment_doctor || ''}</td>
                    <td>${item.created_at || ''}</td>
                    <td>${item.next_appointment_date || ''}</td>
                    <td>${item.next_appointment_doctor || ''}</td>
                `;

                tbody.appendChild(tr);
            });
        } else {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="9" class="text-center">No treatment history found.</td>';
            tbody.appendChild(tr);
        }
    });
}



</script>

<script>
let originalAmount = 0;

$('#treatmentPlanSelect').on('change', function () {
    const typeId = $(this).val();
    if (!typeId) return;
	
	 $('#price').val('');
    $('#units').val(1);
    $('#companyCost').val('');
    $('#finalAmount').val('');
    $('#amountChangeIcon').html('');  // Also clear the arrow icon if any
	
    $('#treatmentSelect').empty().append('<option value="">-- Select Treatment --</option>');
    $.getJSON(admin_url + 'toot/get_treatment_subtypes/' + typeId, function (data) {
        data.forEach(function (item) {
            $('#treatmentSelect').append(`<option value="${item.treatment_sub_type_id}" data-price="${item.treatment_sub_type_price}">${item.treatment_sub_type_name}</option>`);
        });
    });
});

$('#treatmentSelect').on('change', function () {
    const selected = $(this).find('option:selected');
    const price = parseFloat(selected.data('price')) || 0;

    $('#price').val(price.toFixed(2));
    $('#units').val(1);
    $('#companyCost').val(price.toFixed(2));
    $('#finalAmount').val(price.toFixed(2));
    originalAmount = price;
    $('#amountChangeIcon').html('');
});

$('#units').on('input', function () {
    const price = parseFloat($('#price').val()) || 0;
    const units = parseInt($(this).val()) || 1;
    const cost = price * units;

    $('#companyCost').val(cost.toFixed(2));
    $('#finalAmount').val(cost.toFixed(2));
    originalAmount = cost;
    $('#amountChangeIcon').html('');
});

$('#finalAmount').on('input', function () {
    const newAmount = parseFloat($(this).val());
    let icon = '';

    if (newAmount > originalAmount) {
        icon = '<i class="fa fa-arrow-up" style="color:green;"></i>';
    } else if (newAmount < originalAmount) {
        icon = '<i class="fa fa-arrow-down" style="color:red;"></i>';
    }

    $('#amountChangeIcon').html(icon);
});


</script>

<script>

$(document).on('click', '.btn-pay-now', function () {
  const row = $(this).closest('tr');
  const invoiceId = $(this).data('invoice');
  const paymentDate = row.find('.payment-date').val();
  const paymentType = row.find('.payment-type').val();
  const amountPaid = row.find('.amount-paid').val();
  const txnId = row.find('.txn-id').val();

  if (!paymentDate || !paymentType || !amountPaid) {
    alert('Please fill all fields.');
    return;
  }

  $.post(admin_url + 'toot/insert_payment', {
    invoice_id: invoiceId,
    payment_date: paymentDate,
    payment_type: paymentType,
    amount_paid: amountPaid,
    txn_id: txnId
  }, function (res) {
    const result = JSON.parse(res);
    if (result.success) {
      alert_float('success', 'Payment recorded successfully');
	  const patient_id = <?= $patientid ?>;
      loadBillingData(patient_id); // Reload data
    } else {
	  const patient_id = <?= $patientid ?>;
	  alert_float('success', 'Payment recorded successfully');
      loadBillingData(patient_id); // Reload data
    }
  });
});


function loadBillingData(patientId) {
  $.ajax({
    url: admin_url + 'toot/get_invoice_data',
    type: 'GET',
    data: { patient_id: patientId },
    success: function (res) {
      const data = JSON.parse(res);

      $('#billing-details-body').empty();
      data.treatments.forEach(function (item) {
		  const paymentForm = `
			<input type="date" class="form-control payment-date" value="${item.today}" />
			<select class="form-control payment-type">
			  <option value="">--Select--</option>
			  <option value="Cash">Cash</option>
			  <option value="Card">Card</option>
			</select>
			<input type="text" class="form-control amount-paid" placeholder="Amount" />
			<input type="text" class="form-control txn-id" placeholder="Txn ID" />
			<button class="btn btn-success btn-pay-now" data-invoice="${item.invoice_id}" style="margin-top:5px;">
			  <i class="glyphicon glyphicon-floppy-disk"></i>
			</button>
		  `;

		  $('#billing-details-body').append(`
			<tr>
			  <td>${item.tooth_info || ''}</td>
			  <td>${item.treatment}</td>
			  <td>${item.progress}</td>
			  <td>${item.amount}</td>
			  <td>${item.invoice_number}</td>
			  <td>${paymentForm}</td>
			</tr>
		  `);
	});


      $('#payment-history-body').empty();
      data.payments.forEach(function (p) {
        $('#payment-history-body').append(`
          <tr>
            <td>${p.receipt_no}</td>
            <td>${p.amount}</td>
            <td>${p.date_time}</td>
            <td>${p.invoice_number}</td>
            <td>
              <a href="${admin_url}payments/payment/${p.id}" target="_blank" class="btn btn-default">
					<i class="glyphicon glyphicon-print"></i>
				</a>
            </td>
          </tr>
        `);
      });
    }
  });
}


$(document).on('change', '.treatment-status-dropdown', function () {
    const planId = $(this).data('plan-id');
    const newStatus = $(this).val();

    $.ajax({
        url: admin_url + 'toot/update_status',
        type: 'POST',
        data: {
            plan_id: planId,
            status: newStatus
        },
        success: function (res) {
            const response = JSON.parse(res);
            if (response.success) {
                alert_float('success', response.message);
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function () {
            alert_float('danger', 'Something went wrong');
        }
    });
});



$(document).ready(function () {
    const patient_id = <?= $patientid ?>;

    // Toggle folders
    $('.folder').click(function () {
        if ($(this).attr('data-is-leaf')) {
            loadImages($(this).attr('data-folder'));
        } else {
            $(this).find('.subfolder-list').slideToggle();
        }
    });

    $('.subfolder').click(function (e) {
        e.stopPropagation();
        loadImages($(this).attr('data-folder'));
    });

    function loadImages(folder) {
        $('.upload-ui-selected-folder').html(`Selected Folder: <strong>${folder}</strong>`);
        $.post(admin_url + 'toot/get_folder_contents', {
            folder: folder,
            patient_id: patient_id
        }, function (response) {
            const data = JSON.parse(response);
            let html = '';
			data.forEach(item => {
				let fullPath = '';
				if (item.xray_file) {
					fullPath = site_url + item.xray_file;
				} else if (item.images) {
					fullPath = site_url + item.images;
				} else if (item.file) {
					fullPath = site_url + 'uploads/medications/' + item.file; // adjust if your actual path is different
				}

				if (fullPath) {
					html += `
						<div class="col-md-3 image-thumb" style="margin-bottom:15px;">
							<a href="${fullPath}" target="_blank">
								<img style="width:100px;height:100px;border-radius:4px;" 
									 src="${fullPath}" 
									 class="img-thumbnail" />
							</a>
							<!--<div style="text-align:center; margin-top:5px;">
								<button class="btn btn-xs btn-info"><i class="glyphicon glyphicon-eye-open"></i></button>
								<button class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i></button>
							</div>-->
						</div>
					`;
				}
			});


            $('#image-display').html(html);
        });
    }

    // Set dynamic counts
    $.getJSON(admin_url + 'toot/get_folder_counts/' + patient_id, function (counts) {
        $('.folder-count').each(function () {
            const folderKey = $(this).data('folder');
            $(this).text(counts[folderKey] || 0);
        });
    });
});

</script>

<script>
$('#treatment_id').on('change', function () {
  const toothInfo = $(this).find(':selected').data('tooth');
  const units = $(this).find(':selected').data('units');
  const options = toothInfo?.split(';').map(t => `<option>${t}</option>`).join('');

  $('#tooth_info').html(options);
  $('#tooth_details').val(toothInfo?.split(';').join(', '));
  $('#tooth_units').val(units);
});

$('#labWorkForm').on('submit', function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  $.ajax({
    url: '<?= admin_url('toot/save_lab') ?>',
    type: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    success: function (res) {
      alert('Lab Work Added');
      $('#labWorkForm')[0].reset();
      loadLabWorkTables();
    }
  });
});

function loadLabWorkTables() {
	const baseUrl = "<?= site_url(); ?>"; // echo the base URL in your JS section

  $.getJSON('<?= admin_url('toot/fetch_lab_status/'.$patientid) ?>', function (data) {
    const rows = data.map(item => `
      <tr>
        <td>${item.tooth_details}</td>
        <td>${item.lab_name}</td>
        <td>${item.lab_work_name}</td>
        <td>${item.lab_followup_name}</td>
        <td>${item.units}</td>
        <td>${item.lab_status}</td>
        <td>
  <span class="label" style="background-color: ${
    item.treatment_status === 'Not Started' ? '#f44336' : 
    item.treatment_status === 'Started' ? '#ff9800' : 
    item.treatment_status === 'Progress' ? '#03a9f4' : 
    item.treatment_status === 'Completed' ? '#4caf50' : 
    '#9e9e9e'
  }; color: #fff; padding: 5px 10px; border-radius: 4px;">
    ${item.treatment_status}
  </span>
</td>

        <td>
  ${item.photo 
    ? `<a href="${baseUrl}${item.photo}" target="_blank">
         <img src="${baseUrl}${item.photo}" alt="Photo" style="width:40px;height:40px;border-radius:4px;" />
       </a>` 
    : 'N/A'}
</td>


        <td>${item.created_at}</td>
      </tr>`);
    $('#labWorkStatusTable tbody').html(rows.join(''));
  });

  $.getJSON('<?= admin_url('toot/fetch_lab_history/'.$patientid) ?>', function (data) {
    const rows = data.map(item => `
      <tr>
        <td>${item.tooth_details}</td>
        <td>${item.lab}</td>
        <td>${item.lab_work}</td>
        <td>${item.lab_followup}</td>
        <td>${item.units}</td>
        <td>${item.status_type}</td>
        <td>${item.old_status}</td>
        <td>${item.new_status}</td>
        <td>${item.notes}</td>
        <td>${item.change_date}</td>
        <td>${item.changed_by}</td>
      </tr>`);
    $('#labWorkHistoryTable tbody').html(rows.join(''));
  });
}

$(document).ready(loadLabWorkTables);
</script>
