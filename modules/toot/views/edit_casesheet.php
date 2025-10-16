<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>


<div id="wrapper">
<div class="content">
<div class="row">
<div class="col-md-12">
<div class="panel_s">
<div class="panel-body">
<?php

$case = $case[0];
?>
<form id="casesheetForm">
  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" id="csrf_token">
  <input type="hidden" name="patientid" value="<?php echo $case['userid'];?>">
  <input type="hidden" name="casesheet_id" value="<?php echo $case['id'];?>">

  <!-- Accordion Tabs -->
  <div class="accordion" id="casesheetAccordion">

    <!-- Preliminary Data Tab -->
    <div class="card">
        <h4>
          
            <strong><?php echo _l('preliminary_data'); ?></strong> 
        </h4>
		<hr>
        <div class="card-body">
			<div class="form-group">
    <!-- Initial Row -->
	<?php
	foreach($prev_treatments as $prev_treatment){
		?>
		<input type="hidden" name="patient_treatment_id[]" value="<?php echo $prev_treatment['id'];?>">
		<div class="edit_treatment_rows">
		<div class="row treatment-row">
        <div class="col-md-2">
		<label>Treatment Type</label>
		<input type="textbox" value="<?php echo $prev_treatment['treatment_name'];?>" class="form-control" readonly>
        </div>

       <div class="col-md-2">
		<label>Duration</label>
		
			<input type="number" name="duration_value_<?php echo $prev_treatment['id'];?>" value="<?php echo $prev_treatment['duration_value'];?>" class="form-control" min="1" placeholder="Number">
			
		</div>


			<div class="col-md-2">
				<label>Improvement(%)</label>
				<input type="number" name="improvement_<?php echo $prev_treatment['id'];?>" class="form-control improvement-input" value="<?php echo $prev_treatment['improvement'];?>" min="0" max="100" placeholder="Enter Percentage" >
			</div>

			<div class="col-md-3">
				<label>Overall Progress</label>
				<div class="progress">
				
					<div class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
				</div>
			</div>

			<div class="col-md-3">
			<label>Status</label>
			<select name="treatment_status_<?php echo $prev_treatment['id'];?>" class="form-control">
				<option value="treatment_started" <?php if($prev_treatment['treatment_status'] == "treatment_started"){ echo "Selected"; }?>>Started</option>
				<option value="treatment_inprogress" <?php if($prev_treatment['treatment_status'] == "treatment_inprogress"){ echo "Selected"; }?>>Inprogress</option>
				<option value="treatment_completed" <?php if($prev_treatment['treatment_status'] == "treatment_completed"){ echo "Selected"; }?>>Completed</option>
			</select>
				
			</div>
		</div>
		</div>
		<?php
		}
	?>
	<br>
	<hr>
	<div id="treatment_rows">
    <div class="row treatment-row">
        <div class="col-md-3">
            <?php
			$selected = "";
			echo render_select('treatment_type[]', $treatments, ['treatment_id', 'treatment_name'], 'treatment_type', $selected, ['data-none-selected-text' => _l('dropdown_non_selected_tex')]);
			?>
        </div>

       <div class="col-md-3">
		<label>Duration(In Months)</label>
		
				
				<input type="number" name="duration_value[]" class="form-control" min="1" placeholder="Number">
			
			
	</div>


        <div class="col-md-2">
            <label>Improvement(%)</label>
            <input type="number" name="improvement[]" class="form-control improvement-input" min="0" max="100" placeholder="Enter Percentage" >
        </div>

        <div class="col-md-3">
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
		<div class="row">
			
            <div class="col-md-4">
              <label for="medicine_days"><?php echo _l('medicine_days'); ?></label>
              <input type="number" name="medicine_days" id="medicine_days" value="<?php echo $case['medicine_days'];?>" class="form-control" min="1">
            </div>
			<div class="col-md-4">
              <label for="followup_date"><?php echo _l('followup_date'); ?></label>
              <input type="date" name="followup_date" id="followup_date"  value="<?php echo $case['followup_date'];?>"  class="form-control">
            </div>
            <div class="col-md-4">
			  
			  <?php
			$selected = isset($case['patient_status']) ? $case['patient_status'] : '';
			echo render_select(
				'patient_status',
				$patient_status,
				['patient_status_id', 'patient_status_name'],
				_l('patient_status'),
				$selected,
				['data-none-selected-text' => _l('dropdown_non_selected_tex')]
			);
			?>
            </div>
			<div class="col-md-4">
              <label for="documents"><?php echo _l('documents'); ?></label>
              <input type="file" name="documents[]" id="documents" class="form-control" multiple>
            </div>
			
			<div class="col-md-8">
             <?php
				$documents = [];

				// Loop through each entry and extract non-empty 'documents'
				foreach ($prev_documents as $entry) {
					if (!empty($entry['documents'])) {
						$decoded = json_decode($entry['documents'], true);
						if (is_array($decoded)) {
							$documents = array_merge($documents, $decoded);
						}
					}
				}
			?>
			<br>
			<h4>Documents</h4>
			<table class="table table-bordered">
			
			  <thead>
				<tr>
				  <th>#</th>
				  <th>File Name</th>
				  <th>Type</th>
				  <th>Action</th>
				</tr>
			  </thead>
			  <tbody>
				<?php foreach ($documents as $index => $file): ?>
				  <tr>
					<td><?= $index + 1 ?></td>
					<td><?= basename($file) ?></td>
					<td><?= pathinfo($file, PATHINFO_EXTENSION) ?></td>
					<td>
					  <a href="<?= base_url($file) ?>" target="_blank" class="btn btn-sm btn-primary">
						View
					  </a>
					</td>
				  </tr>
				<?php endforeach; ?>
			  </tbody>
			</table>
            </div>
			
			
			
			
		</div>
			
		<!-- Presenting Complaints -->
		<div class="form-group mtop20">
			<label for="presenting_complaints" class="control-label">
				<?php echo _l('presenting_complaints'); ?>
			</label>
			<textarea id="presenting_complaints" name="presenting_complaints" class="form-control tinymce" rows="6"><?php echo $case['presenting_complaints'];?></textarea>
		</div>
        </div>
     
    </div>

    <!-- Clinical Observation Tab -->
    <div class="card">
	
	   <h4>
	   <br>
          <strong><?php echo _l('clinical_observation'); ?></strong> 
        </h4>
		<hr>
        <div class="card-body">
          <!-- Clinical Observation Content -->
          <div class="row mtop10">
           
            <div class="col-md-12">
              <label for="clinical_observation"><?php echo _l('clinical_observation'); ?></label>
              <textarea name="clinical_observation" id="clinical_observation" class="form-control tinymce" rows="6"><?php echo $case['clinical_observation'];?></textarea>
            </div>
          </div>

        </div>
      
    </div>

    <!-- Personal History Tab -->
    <div class="card">
	
	  <h4>
	  <br>
          <strong><?php echo _l('personal_history'); ?></strong> 
        </h4>
		<hr>
        <div class="card-body">
          <!-- Personal History Content -->
					  <div class="row">
			  <!-- Row 1 -->
			  <div class="col-md-4 mb-3">
				<label><?php echo _l('appetite'); ?>:</label>
				<input type="text" name="appetite" class="form-control" value="<?php echo $case['appetite'];?>"  placeholder="<?php echo _l('appetite'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
				<label><?php echo _l('desires'); ?>:</label>
				<input type="text" name="desires" class="form-control" value="<?php echo $case['desires'];?>"  placeholder="<?php echo _l('desires'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
				<label><?php echo _l('aversion'); ?>:</label>
				<input type="text" name="aversion" class="form-control" value="<?php echo $case['aversion'];?>"  placeholder="<?php echo _l('aversion'); ?>">
			  </div>

			  <!-- Row 2 -->
			  <div class="col-md-4 mb-3">
				<br>
				<label><?php echo _l('tongue'); ?>:</label>
				<input type="text" name="tongue" class="form-control" value="<?php echo $case['tongue'];?>"  placeholder="<?php echo _l('tongue'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('urine'); ?>:</label>
				<input type="text" name="urine" class="form-control" value="<?php echo $case['urine'];?>"  placeholder="<?php echo _l('urine'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('bowels'); ?>:</label>
				<input type="text" name="bowels" class="form-control" value="<?php echo $case['bowels'];?>"  placeholder="<?php echo _l('bowels'); ?>">
			  </div>

			  <!-- Row 3 -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('sweat'); ?>:</label>
				<input type="text" name="sweat" class="form-control" value="<?php echo $case['sweat'];?>"  placeholder="<?php echo _l('sweat'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('sleep'); ?>:</label>
				<input type="text" name="sleep" class="form-control" value="<?php echo $case['sleep'];?>"  placeholder="<?php echo _l('sleep'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('sun_headache'); ?>:</label>
				<input type="text" name="sun_headache" class="form-control" value="<?php echo $case['sun_headache'];?>"  placeholder="<?php echo _l('sun_headache'); ?>">
			  </div>

			  <!-- Row 4 -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('thermals'); ?>:</label>
				<input type="text" name="thermals" class="form-control" value="<?php echo $case['thermals'];?>"  placeholder="<?php echo _l('thermals'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('habits'); ?>:</label>
				<input type="text" name="habits" class="form-control" value="<?php echo $case['habits'];?>"  placeholder="<?php echo _l('habits'); ?>">
			  </div>

			  <!-- Row 5 -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('addiction'); ?>:</label>
				<input type="text" name="addiction" class="form-control" value="<?php echo $case['addiction'];?>"  placeholder="<?php echo _l('addiction'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('side'); ?>:</label>
				<input type="text" name="side" class="form-control" value="<?php echo $case['side'];?>"  placeholder="<?php echo _l('side'); ?>">
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('dreams'); ?>:</label>
				<textarea name="dreams" class="form-control" placeholder="<?php echo _l('dreams'); ?>"><?php echo $case['dreams'];?></textarea>
			  </div>

			  <!-- Row 6 -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('diabetes'); ?>:</label>
				<textarea name="diabetes" class="form-control" placeholder="<?php echo _l('diabetes'); ?>"><?php echo $case['diabetes'];?></textarea>
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('thyroid'); ?>:</label>
				<textarea name="thyroid" class="form-control" placeholder="<?php echo _l('thyroid'); ?>"><?php echo $case['thyroid'];?></textarea>
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('hypertension'); ?>:</label>
				<textarea name="hypertension" class="form-control" placeholder="<?php echo _l('hypertension'); ?>"><?php echo $case['hypertension'];?></textarea>
			  </div>

			  <!-- Row 7 -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('hyperlipidemia'); ?>:</label>
				<textarea name="hyperlipidemia" class="form-control" placeholder="<?php echo _l('hyperlipidemia'); ?>"><?php echo $case['hyperlipidemia'];?></textarea>
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('menstrual_obstetric_history'); ?>:</label>
				<textarea name="menstrual_obstetric_history" class="form-control" placeholder="<?php echo _l('menstrual_obstetric_history'); ?>"><?php echo $case['menstrual_obstetric_history'];?></textarea>
			  </div>
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('family_history'); ?>:</label>
				<textarea name="family_history" class="form-control" placeholder="<?php echo _l('family_history'); ?>"><?php echo $case['family_history'];?></textarea>
			  </div>

			  <!-- Final Row -->
			  <div class="col-md-4 mb-3">
			  <br>
				<label><?php echo _l('past_treatment_history'); ?>:</label>
				<textarea name="past_treatment_history" class="form-control" placeholder="<?php echo _l('past_treatment_history'); ?>"><?php echo $case['past_treatment_history'];?></textarea>
			  </div>
			</div>
        </div>
    </div>

    <!-- General Examination Tab -->
    <div class="card">
	   <h4>
	   <br>
          <strong><?php echo _l('general_examination'); ?></strong> 
        </h4>
		<hr>
        <div class="card-body">
          <!-- General Examination Content -->
          <div class="row">
			<div class="col-md-2">
				<label><?php echo _l('bp'); ?>:</label>
				<input type="text" name="bp" value="<?php echo $case['bp'];?>"  class="form-control" placeholder="120/80">
			</div>
			<div class="col-md-2">
				<label><?php echo _l('pulse'); ?>:</label>
				<input type="text" name="pulse" value="<?php echo $case['pulse'];?>"  class="form-control" placeholder="Pulse">
			</div>
			<div class="col-md-2">
				<label><?php echo _l('weight'); ?>:</label>
				<input type="text" name="weight" value="<?php echo $case['weight'];?>"  class="form-control" placeholder="WT.(KG)">
			</div>
			<div class="col-md-2">
				<label><?php echo _l('height'); ?>:</label>
				<input type="text" name="height" value="<?php echo $case['height'];?>"  class="form-control" placeholder="HT.">
			</div>
			<div class="col-md-2">
				<label><?php echo _l('temperature'); ?>:</label>
				<input type="text" name="temperature" value="<?php echo $case['temperature'];?>"  class="form-control" placeholder="TEMP.">
			</div>
			<div class="col-md-2">
				<label><?php echo _l('bmi'); ?>:</label>
				<input type="text" name="bmi" value="<?php echo $case['bmi'];?>"  class="form-control" placeholder="BMI">
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
							<option value="normal" <?php if($case['nutrition'] == "normal"){ echo "Selected"; }?>>Normal</option>
							<option value="poor" <?php if($case['nutrition'] == "poor"){ echo "Selected"; }?>>Poor</option>
							<option value="excessive" <?php if($case['nutrition'] == "excessive"){ echo "Selected"; }?>>Excessive</option>
						</select>
					<?php else: ?>
						<textarea name="<?php echo $field; ?>" class="form-control" rows="2" placeholder="<?php echo $labels[$field]; ?>"><?php echo isset($case[$field]) ? $case[$field] : ''; ?></textarea>


					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php endforeach; ?>
        </div>
    </div>

    <!-- Mind Tab -->
    <div class="card">
	
	  <h4>
	  <br>
          <strong><?php echo _l('mind'); ?></strong> 
        </h4>
		<hr>
        <div class="card-body">
          <!-- Mind Content -->
          <div class="form-group mtop20">
			<label for="mind" class="control-label">
				<?php echo _l('mind'); ?>
			</label>
			<textarea id="mind" name="mind" class="form-control tinymce" rows="6"><?php echo $case['mind'];?></textarea>
		</div>
        </div>
    </div>

  </div> <!-- End of Accordion -->

  <!-- Form Actions -->
  <div class="form-actions">
    <button type="submit" class="btn btn-primary"><?= _l('update_casesheet'); ?></button>
    <button type="button" class="btn btn-secondary" onclick="toggleCaseSheetForm()"><?= _l('cancel'); ?></button>
  </div>

</form>


</div>
</div>
</div>
</div>
</div>
</div>

<?php init_tail(); ?>
<script>
// Remove blur when second modal closes
$('#caseSheetModal').on('hidden.bs.modal', function () {
  $('.modal .modal-content').removeClass('blurred');
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


</script>

<script>
    document.querySelectorAll('.improvement-input').forEach(function(input) {
        input.addEventListener('input', function () {
            if (this.value > 100) this.value = 100;
            if (this.value < 0) this.value = 0;
        });
    });
</script>
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

             <div class="col-md-3">
				
						<input type="number" name="duration_value[]" class="form-control" min="1" placeholder="Number">
					
					
			</div>

            <div class="col-md-2">
                <input type="number" name="improvement[]" class="form-control improvement-input" min="0" max="100" placeholder="Enter Percentage">
            </div>

            <div class="col-md-3">
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
	
	$('.edit_treatment_rows').on('input', '.improvement-input', function () {
        var value = $(this).val();
        value = Math.max(0, Math.min(100, value)); // Ensure it's between 0 and 100
        var progressBar = $(this).closest('.treatment-row').find('.progress-bar');
		if(value == 0 || value == ''){
			value = 0;
		}
		
        progressBar.css('width', value + '%').attr('aria-valuenow', value).text(value + '%');
    });
});

$(document).ready(function () {
    $('.edit_treatment_rows .improvement-input').each(function () {
        var value = $(this).val();
        value = Math.max(0, Math.min(100, value)); // Clamp between 0–100
        var progressBar = $(this).closest('.treatment-row').find('.progress-bar');

        if (value == 0 || value === '') {
            value = 0;
        }

        progressBar.css('width', value + '%')
                   .attr('aria-valuenow', value)
                   .text(value + '%');
    });
});


$("body").on("submit", "#casesheetForm", function (e) {
  e.preventDefault();

  var form = $(this)[0]; // Get raw DOM element
  var formData = new FormData(form); // Create FormData object (includes files automatically)

  $.ajax({
    url: admin_url + "client/update_casesheet",
    type: "POST",
    data: formData,
    processData: false, // Important: prevent jQuery from processing the data
    contentType: false, // Important: prevent jQuery from setting content type
    success: function (response) {
      let res = JSON.parse(response);
      if (res.success) {
        if (res.redirect) {
          window.location.href = res.redirect;
        }
      } else {
        alert("Error: " + res.message);
      }
    },
    error: function () {
      alert("Something went wrong while saving the prescription.");
    }
  });
});

</script>

</body>

</html>
