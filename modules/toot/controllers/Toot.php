<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Toot extends AdminController
{
	private $current_branch_id;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('client_model');
        $this->load->model('master_model');
        $this->load->model('doctor_model');
        $this->load->helper('custom'); // loads custom_helper.php
		$this->current_branch_id = $this->client_model->get_logged_in_staff_branch_id();
		error_reporting(0);

    }
	public function patient_case_sheet($patientid=4){
		$data['title']  = _l('patient_case_sheet');
		$data['quadrant1_teeth'] = $this->client_model->get_teeth_by_quadrant(1, 'adult');
		$data['quadrant2_teeth'] = $this->client_model->get_teeth_by_quadrant(2, 'adult');
		$data['quadrant3_teeth'] = $this->client_model->get_teeth_by_quadrant(3, 'adult');
		$data['quadrant4_teeth'] = $this->client_model->get_teeth_by_quadrant(4, 'adult');
		$data['quadrant5_teeth'] = $this->client_model->get_teeth_by_quadrant(1, 'child');
		$data['quadrant6_teeth'] = $this->client_model->get_teeth_by_quadrant(2, 'child');
		$data['quadrant7_teeth'] = $this->client_model->get_teeth_by_quadrant(3, 'child');
		$data['quadrant8_teeth'] = $this->client_model->get_teeth_by_quadrant(4, 'child');
		$data['client_data'] = $this->client_model->get($patientid);
		$data['invoices'] = $this->client_model->get_invoices($patientid);
        $data['invoice_payments'] = $this->client_model->get_invoice_payments($patientid);
        $data['chief_complaint'] = $this->client_model->get_chief_complaint($patientid);
        $data['present_medications'] = $this->client_model->get_present_medications($patientid);
        $data['consultation'] = $this->client_model->get_visits($patientid);
        
		$data['medical_problem'] = $this->master_model->get_all('medical_problem');
		$data['chief_complaints'] = $this->master_model->get_all('chief_complaint');
		$data['treatments'] = $this->master_model->get_all('treatment');
		$data['medical_investigations'] = $this->master_model->get_all('medical_investigation');
		$data['dental_investigation'] = $this->master_model->get_all('dental_investigation');
		$data['treatment_type'] = $this->master_model->get_all('treatment_type');
		$data['labs'] = $this->master_model->get_all('lab');
		$data['lab_works'] = $this->master_model->get_all('lab_work');
		$data['lab_followups'] = $this->master_model->get_all('lab_followup');
		$data['case_remarks'] = $this->master_model->get_all('case_remark');
		$data['approved_treatments'] = $this->client_model->get_approved_treatments($patientid);
		$data['medicine'] = $this->master_model->get_all('medicine');
		$data['accepted_treatment_plans'] = $this->client_model->get_accepted_treatment_plans($patientid);
		$data['pending_plans'] = $this->client_model->get_treatment_plans_by_plan_type($patientid, "", 1);
		
		$data['doctors'] = $this->doctor_model->get_doctors();
		
		
		
		
		$this->load->view('patient_case_sheet', $data);
	}
	
	public function get_patient_list()
    {
       if (staff_can('view', 'customers')) {
            //access_denied('appointments');
        } 
        $data['title'] = "Patients";
		
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('toot', 'tables/get_patient_list'), $data);
        }
       $this->load->view('patients_list', $data);
    }
	
	public function get_folder_contents()
	{
		$patient_id = $this->input->post('patient_id');
		$folder = $this->input->post('folder');

		$images = [];

		switch ($folder) {
			case 'Documents > Reviews':
				$images = []; // Example placeholder
				break;
			case 'Medical Reports > Past':
				$images = []; // Add logic if available
				break;
			case 'Medication > Present':
				$images = $this->client_model->get_present_medication_images($patient_id);
				break;
			case 'Examination Findings':
				$images = $this->client_model->get_examination_images($patient_id);
				break;
			case 'Treatment Procedure':
				$images = $this->client_model->get_treatment_procedure_images($patient_id);
				break;
		}

		echo json_encode($images);
	}

	public function get_folder_counts($patient_id)
	{
		$counts = $this->client_model->get_folder_counts($patient_id);
		echo json_encode($counts);
	}
	
	public function get_treatment_subtypes($treatment_type_id)
	{
		$query = $this->db->get_where(db_prefix() . 'treatment_sub_type', ['treatment_type_id' => $treatment_type_id]);
		echo json_encode($query->result_array());
	}
	
	public function add_chief_complaint()
    {
        if ($this->input->post()) {
            $patient_id = $this->input->post('patient_id');
            $complaint = $this->input->post('complaint');
            $notes = $this->input->post('notes');
            $teeth_data = json_decode($this->input->post('teeth_data'), true);

            $insert_data = [];
            foreach ($teeth_data as $tooth) {
                $insert_data[] = [
                    'patient_id' => $patient_id,
                    'tooth_id'   => $tooth['id'],
                    'display_id' => $tooth['displayId'],
                    'surfaces'   => implode(',', $tooth['surfaces']),
                    'complaint'  => $complaint,
                    'notes'      => $notes,
                ];
            }

            $this->client_model->add_chief_complaint($insert_data);
            echo json_encode(['status' => true, 'message' => 'Complaint added successfully']);
        }
    }
	
	public function delete_chief_complaints()
	{
		$id = $this->input->post('id');

		if ($id && $this->client_model->delete_chief_complaints($id)) {
			echo json_encode(['status' => true, 'message' => _l('deleted_successfully')]);
		} else {
			echo json_encode(['status' => false, 'message' => _l('delete_failed')]);
		}
	}
	
	
    public function add_present_medications()
    {
        $data = $this->input->post();
        if (!empty($_FILES['file']['name'])) {
			$uploadDir = FCPATH . 'uploads/medications/';

			// Create the directory if it doesn't exist
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			$config['upload_path']   = $uploadDir;
			$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx';
			$config['max_size']      = 2048;
			$config['encrypt_name']  = true;

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('file')) {
				echo json_encode(['success' => false, 'error' => $this->upload->display_errors()]);
				return;
			} else {
				$upload_data = $this->upload->data();
				$data['file'] = $upload_data['file_name']; // Save file name only
			}
		}


        $insert_id = $this->client_model->add_present_medications($data);
        echo json_encode(['success' => true]);
    }
	public function get_present_medications()
    {
        $patient_id = $this->input->get('patient_id');

        $data = $this->client_model->get_present_medications($patient_id);

        echo json_encode(['success' => true, 'data' => $data]);
    }

    public function delete_present_medications($id)
    {
        $deleted = $this->client_model->delete_present_medications($id);

        echo json_encode(['success' => $deleted]);
    }
	
	
	public function add_medical_problems()
    {
        $this->load->helper('security');

        $patient_id = $this->input->post('patient_id');
        $problem_name = $this->input->post('problem_name');
        $notes = $this->input->post('notes');

        if (!$patient_id || !$problem_name) {
            echo json_encode(['success' => false, 'message' => 'Required fields missing']);
            return;
        }

        $data = [
            'patient_id' => $patient_id,
            'problem_name' => $problem_name,
            'notes' => $notes,
        ];

        $inserted = $this->client_model->add_medical_problems($data);

        if ($inserted) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Insert failed']);
        }
    }
	
	
    public function get_medical_problems()
    {
        $patient_id = $this->input->get('patient_id');

        $data = $this->client_model->get_medical_problems($patient_id);

        echo json_encode(['success' => true, 'data' => $data]);
    }

    public function delete_medical_problems($id)
    {
        $deleted = $this->client_model->delete_medical_problems($id);

        echo json_encode(['success' => $deleted]);
    }
	
    
	public function add_investigation()
    {
        $this->load->helper('security');

        $patient_id = $this->input->post('patient_id');
        $type = $this->input->post('type'); // 'medical' or 'dental'
        $problem = $this->input->post('problem');
        $notes = $this->input->post('notes');

        if (!$patient_id || !$type || !$problem) {
            echo json_encode(['success' => false, 'message' => 'Required fields missing']);
            return;
        }

        $data = [
            'patient_id' => $patient_id,
            'type' => $type,
            'problem' => $problem,
            'notes' => $notes,
        ];

        $inserted = $this->client_model->add_investigation($data);

        if ($inserted) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Insert failed']);
        }
    }

    public function get_investigations_by_type()
    {
        $patient_id = $this->input->get('patient_id');
        $type = $this->input->get('type');

        $data = $this->client_model->get_investigations_by_type($patient_id, $type);

        echo json_encode(['success' => true, 'data' => $data]);
    }

    public function delete_investigation($id)
    {
        $deleted = $this->client_model->delete_investigation($id);

        echo json_encode(['success' => $deleted]);
    }
	
	public function delete_prescriptions($id)
	{

		if ($this->client_model->delete_prescriptions($id)) {
			echo json_encode(['success' => true, 'message' => 'Prescription deleted successfully.']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Deletion failed or record not found.']);
		}
	}
	
	public function add_toot_prescription()
    {
        $data = $this->input->post();
        $result = $this->client_model->add_toot_prescription($data);
        echo json_encode(['success' => $result]);
    }

    public function get_toot_prescriptions($patient_id)
    {
        $data = $this->client_model->get_toot_prescriptions($patient_id);
        echo json_encode($data);
    }
	
	
	public function get_previous_prescriptions()
	{
		$patient_id = $this->input->get('patient_id');

		$data = $this->client_model->get_prescriptions_by_patient($patient_id);

		echo json_encode([
			'success' => true,
			'data' => $data
		]);
	}

	public function print_prescription($id)
	{
		$data['prescription'] = $this->client_model->get_prescription_details($id);

		$this->load->view('prescription_print', $data);
	}
	
	
	
	public function add_examination_findings()
    {
		$post = $this->input->post();
		$teethData = json_decode($post['teethData'], true);

		// ---------- Image Upload ----------
		$uploaded_images = [];
		if (!empty($_FILES['images']['name'][0])) {
			$files = $_FILES['images'];
			$count = count($files['name']);

			for ($i = 0; $i < $count; $i++) {
				$_FILES['file']['name']     = $files['name'][$i];
				$_FILES['file']['type']     = $files['type'][$i];
				$_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
				$_FILES['file']['error']    = $files['error'][$i];
				$_FILES['file']['size']     = $files['size'][$i];

				$upload_path = 'uploads/examination_images/';
				if (!file_exists(FCPATH . $upload_path)) {
					mkdir(FCPATH . $upload_path, 0755, true);
				}

				$config['upload_path']   = FCPATH . $upload_path;
				$config['allowed_types'] = 'jpg|jpeg|png|gif';
				$config['max_size']      = 2048;
				$config['file_name']     = uniqid('exam_img_');

				$this->load->library('upload', $config);

				if ($this->upload->do_upload('file')) {
					$upload_data = $this->upload->data();
					$uploaded_images[] = $upload_path . $upload_data['file_name'];
				}
			}
		}

		$image_paths = implode('|', $uploaded_images); // delimiter to store multiple paths

		$tooth_info = "";
		
		foreach ($teethData as $item) {
			$tooth_info .= $item['tooth_id']."-".$item['surfaces'].";";
		}
		$data = [
			'patient_id' => $post['patient_id'],
			'tooth_info' => $tooth_info,
			'complaint' => $post['complaint'],
			'notes' => $post['notes'],
			'images'      => $image_paths,
			'created_at' => date('Y-m-d H:i:s'),
		];
		$inserted_id = $this->client_model->add_examination_findings($data);
		echo json_encode([
			'success' => true,
			'message' => 'Examination findings saved successfully.',
			'inserted_ids' => $inserted_id
		]);


    }

    // Get complaints for a patient
    public function get_all_examination_findings()
    {
        $patient_id = $this->input->get('patient_id');
        if (!$patient_id) {
            echo json_encode(['success' => false, 'message' => 'Patient ID missing']);
            return;
        }

        $complaints = $this->client_model->get_all_examination_findings($patient_id);

        echo json_encode(['success' => true, 'data' => $complaints]);
    }

    // Delete a complaint by id
    public function delete_examination_findings()
    {
        $id = $this->input->post('id');

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID missing']);
            return;
        }

        $deleted = $this->client_model->delete_examination_findings($id);

        echo json_encode(['success' => $deleted]);
    }
	
	
    // Get past_dental_history
	public function add_past_dental_history()
    {
		$post = $this->input->post();
		$teethData = json_decode($post['teethData'], true);
		$tooth_info = "";
		
		foreach ($teethData as $item) {
			$tooth_info .= $item['tooth_id']."-".$item['surfaces'].";";
		}
		$data = [
			'patient_id' => $post['patient_id'],
			'teeth_data' => $tooth_info,
			'complaint' => $post['complaint'],
			'notes' => $post['notes'],
			'place' => $post['place'],
			'opinion' => $post['opinion'],
			'created_at' => date('Y-m-d H:i:s'),
		];
		$inserted_id = $this->client_model->add_past_dental_history($data);
		echo json_encode([
			'success' => true,
			'message' => 'Past Dental History saved successfully.',
			'inserted_ids' => $inserted_id
		]);
	}
	
    public function get_all_past_dental_history()
    {
        $patient_id = $this->input->get('patient_id');
        if (!$patient_id) {
            echo json_encode(['success' => false, 'message' => 'Patient ID missing']);
            return;
        }

        $complaints = $this->client_model->get_all_past_dental_history($patient_id);

        echo json_encode(['success' => true, 'data' => $complaints]);
    }

    // Delete a complaint by id
    public function delete_past_dental_history()
    {
        $id = $this->input->post('id');

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID missing']);
            return;
        }

        $deleted = $this->client_model->delete_past_dental_history($id);

        echo json_encode(['success' => $deleted]);
    }
	
	public function add_treatment_plan() {
		$post = $this->input->post();
		$plans = [];

		// Ensure that only the checked plans are selected
		if ($this->input->post('plan_a')) $plans[] = 'A';
		if ($this->input->post('plan_b')) $plans[] = 'B';
		if ($this->input->post('plan_c')) $plans[] = 'C';

		if (empty($plans)) {
			echo json_encode(['success' => false, 'message' => 'Please select at least one plan.']);
			return;
		}
		$teethData = json_decode($post['tooth_info'], true);
		$tooth_info = "";
		
		foreach ($teethData as $item) {
			$tooth_info .= $item['tooth_id']."-".$item['surfaces'].";";
		}
		foreach ($plans as $planType) {
			$data = [
				'patient_id'     => $post['patient_id'],
				'treatment_plan' => $post['treatmentPlanSelect'],
				'treatment'      => $post['treatmentSelect'],
				'company_price'  => $post['companyPrice'],
				'units'          => $post['units'],
				'company_cost'   => $post['companyCost'],
				'final_amount'   => $post['finalAmount'],
				'tooth_info'     => $tooth_info,
				'plan_type'      => $planType,
			];
			$this->client_model->add_treatment_plan($data);
		}

		echo json_encode(['success' => true, 'message' => 'Treatment Plan(s) added successfully.']);
	}


	public function get_treatment_plans()
	{
		$patient_id = $this->input->get('patient_id');

		$data = [
			'plan_a' => $this->client_model->get_treatment_plans_by_plan_type($patient_id, 'A', false),
			'plan_b' => $this->client_model->get_treatment_plans_by_plan_type($patient_id, 'B', false),
			'plan_c' => $this->client_model->get_treatment_plans_by_plan_type($patient_id, 'C', false),
			'accepted' => $this->client_model->get_accepted_treatment_plans($patient_id),
		];

		echo json_encode(['success' => true, 'data' => $data]);
	}


	public function accept_treatment_plan() {
		$id = $this->input->post('id');
		$success = $this->client_model->accept_treatment_plan($id);
		echo json_encode(['success' => $success]);
	}
	
	public function get_billing_details()
	{
		$patient_id = $this->input->get('patient_id');
		$this->db->select('tblitemable.*, tblinvoices.datecreated, tblinvoices.total');
		$this->db->from(db_prefix() . 'itemable');
		$this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id = ' . db_prefix() . 'itemable.rel_id');
		$this->db->where(db_prefix() . 'invoices.clientid', $patient_id);
		$this->db->where(db_prefix() . 'itemable.rel_type', 'invoice');
		$this->db->where(db_prefix() . 'itemable.description', 'Treatment Plan');
		$query = $this->db->get();
		$items = $query->result();

		$data = [];
		foreach ($items as $item) {
			$desc = json_decode($item->long_description);
			if (!$desc) continue;

			$data[] = [
				'tooth_info' => $desc->tooth_info ?? '',
				'treatment' => $desc->treatment ?? '',
				'progress' => 'Not Started', // Static for now
				'amount' => $item->rate,
				'datecreated' => $item->datecreated,
				'invoice_id' => $item->rel_id
			];
		}

		echo json_encode(['success' => true, 'data' => $data]);
	}
	public function get_invoice_data()
	{
		$patient_id = $this->input->get('patient_id');

		$data = [
			'treatments' => $this->client_model->get_invoice_treatments($patient_id),
			'payments'   => $this->client_model->get_toot_invoice_payments($patient_id)
		];
		echo json_encode($data);
	}
	public function get_approved_treatments($patient_id)
    {
        $treatments = $this->client_model->get_approved_treatments($patient_id);
        echo json_encode($treatments);
    }

    // Get lab work table data
    public function fetch_lab_status($patient_id)
    {
        $statusData = $this->client_model->get_lab_work_status($patient_id);
        echo json_encode($statusData);
    }

    // Get lab work history table data
    public function fetch_lab_history($patient_id)
    {
        $historyData = $this->client_model->get_lab_work_history($patient_id);
        echo json_encode($historyData);
    }
	
	
	public function insert_payment()
	{
		if ($this->input->is_ajax_request()) {
			$invoiceid     = $this->input->post('invoice_id');
			$amount        = $this->input->post('amount_paid');
			$paymentmode   = $this->input->post('payment_type');
			$transactionid = $this->input->post('txn_id');
			$date          = $this->input->post('payment_date');

			// Validation (optional)
			if (!$invoiceid || !$amount || !$paymentmode || !$date) {
				//echo json_encode(['status' => false, 'message' => 'Missing required fields']);
				//return;
			}

			// Verify invoice includes Treatment Plan
			$this->db->where('rel_id', $invoiceid);
			$this->db->where('description', 'Treatment Plan');
			$this->db->where('rel_type', 'invoice');
			$check = $this->db->get(db_prefix() . 'itemable')->row();

			if (!$check) {
				echo json_encode(['status' => false, 'message' => 'This invoice is not linked to a Treatment Plan.']);
				return;
			}

			// Insert payment
			$payment_data = [
				'invoiceid'     => $invoiceid,
				'amount'        => $amount,
				'paymentmode'   => $paymentmode,
				'transactionid' => $transactionid,
				'date'          => $date,
			];

			$insert_id = $this->client_model->insert_payment($payment_data);

			if ($insert_id) {
				echo json_encode(['status' => true, 'message' => 'Payment added successfully']);
			} else {
				echo json_encode(['status' => false, 'message' => 'Failed to insert payment']);
			}
		}
	}
	
	public function save_lab()
    {
        $data = $this->input->post();
        $photo = null;

        if (!empty($_FILES['photo']['name'])) {
			$upload_path = 'uploads/lab_photos/';
				if (!file_exists(FCPATH . $upload_path)) {
					mkdir(FCPATH . $upload_path, 0755, true);
				}
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf';
            $config['file_name']     = uniqid('lab_');
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('photo')) {
                $uploadData = $this->upload->data();
                $photo = 'uploads/lab_photos/' . $uploadData['file_name'];
            }
        }

        $data['photo'] = $photo;
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');

        $this->client_model->insert_lab_work($data);
        echo json_encode(['status' => 'success']);
    }


	



	public function delete_treatment_plan()
	{
		$id = $this->input->post('id');
		if (!$id) {
			echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
			return;
		}

		$deleted = $this->client_model->delete_treatment_plan($id);

		echo json_encode([
			'success' => $deleted,
			'message' => $deleted ? 'Treatment plan deleted successfully.' : 'Delete failed.'
		]);
	}
	
	
	public function get_accepted_treatments($patient_id)
    {
        $treatments = $this->client_model->get_accepted_treatments($patient_id);
        echo json_encode(['success' => true, 'data' => $treatments]);
    }

    public function get_teeth_by_treatment()
    {
        $treatment = $this->input->get('treatment');
        $patient_id = $this->input->get('patient_id');
        $teeth = $this->client_model->get_teeth_by_treatment($patient_id, $treatment);
        echo json_encode(['success' => true, 'data' => $teeth]);
    }

    public function get_procedures()
    {
        $treatment_plan = $this->input->get('treatment_plan');
        $tooth_info = $this->input->get('tooth_info');
        $procedures = $this->client_model->get_procedures_by_plan_and_tooth($treatment_plan, $tooth_info);
        echo json_encode(['success' => true, 'data' => $procedures]);
    }

    public function add_treatment_procedure()
    {
        $data = $this->input->post();
        // You might want to add validation here

        // Handle file upload for xray
        if (!empty($_FILES['xray_file']['name'])) {
			$upload_path = 'uploads/xrays/';
				if (!file_exists(FCPATH . $upload_path)) {
					mkdir(FCPATH . $upload_path, 0755, true);
				}
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf';
            $config['max_size'] = 2048;
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('xray_file')) {
                echo json_encode(['success' => false, 'message' => $this->upload->display_errors()]);
                return;
            }
            $upload_data = $this->upload->data();
            $data['xray_file'] = 'uploads/xrays/' . $upload_data['file_name'];
        }

        $insert_id = $this->client_model->add_treatment_procedure($data);

        if ($insert_id) {
            echo json_encode(['success' => true, 'message' => 'Treatment procedure added successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add treatment procedure.']);
        }
    }
	
	// Controller method
	public function get_treatment_history()
	{
		$patient_id = $this->input->get('patient_id');
		$data = $this->client_model->get_treatment_history($patient_id);
		echo json_encode(['success' => true, 'data' => $data]);
	}
	public function update_status()
	{
		$plan_id = $this->input->post('plan_id');
		$status = $this->input->post('status');

		if ($plan_id && $status) {
			$this->db->where('id', $plan_id);
			$updated = $this->db->update(db_prefix() . 'tooth_treatment_plans', ['treatment_status' => $status]);

			if ($updated) {
				echo json_encode(['success' => true, 'message' => 'Status updated']);
			} else {
				echo json_encode(['success' => false, 'message' => 'Update failed']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'Invalid data']);
		}
	}


	
    /* List all clients */
    public function index($id = '', $tab = '')
    {   
        if (staff_cant('view', 'customers')) {
            //if (!have_assigned_customers() && staff_cant('create', 'customers')) {
                access_denied('patients');
           // }
        }

        $this->load->model('contracts_model');
        $data['contract_types'] = $this->contracts_model->get_contract_types();
        $data['groups']         = $this->client_model->get_groups();
        $data['title']          = _l('clients');

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $this->load->model('invoices_model');
        $data['invoice_statuses'] = $this->invoices_model->get_statuses();

        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('projects_model');
        $data['project_statuses'] = $this->projects_model->get_project_statuses();

        $data['customer_admins'] = $this->client_model->get_customers_admin_unique_ids();

        $whereContactsLoggedIn = '';
        if (staff_cant('view', 'customers')) {
            $whereContactsLoggedIn = ' AND userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';
        }

        $data['contacts_logged_in_today'] = $this->client_model->get_contacts('', 'last_login LIKE "' . date('Y-m-d') . '%"' . $whereContactsLoggedIn);

        $data['countries'] = $this->client_model->get_clients_distinct_countries();
        $data['table'] = App_table::find('clients');
        
        $data['clientid'] = $id;
        
        if ($id) {
            // Fetch the existing patient data
            $client = $this->client_model->get($id);
            $customer_new_fields = $this->client_model->get_customer_new_fields($id);
            $apponitment_data = $this->client_model->get_apponitment_data($id);
            $patient_activity_log = $this->client_model->get_patient_activity_log($id);
            $patient_prescription = $this->client_model->get_patient_prescription($id);
            $casesheet = $this->client_model->get_casesheet($id);
            // Fetch patient call logs
            $patient_call_logs = $this->client_model->get_patient_call_logs($id); // NEW
            $invoices = $this->client_model->get_invoices($id); // NEW
            $invoice_payments = $this->client_model->get_invoice_payments($id); // NEW

            // Fetch medicine data (names, potencies, doses, timings)
            $medicines = $this->master_model->get_all('medicine');
            $potencies = $this->master_model->get_all('medicine_potency');
            $doses = $this->master_model->get_all('medicine_dose');
            $timings = $this->master_model->get_all('medicine_timing');
            $appointment_type = $this->master_model->get_all('appointment_type');
            $criteria = $this->master_model->get_all('criteria');
            $treatments = $this->master_model->get_all('treatment');
            $patient_status = $this->master_model->get_all('patient_status');
            
            // Pass the data to the view
            $data['client_modal'] = $this->load->view('client_model_popup', [
                'client' => $client,
                'casesheet' => $casesheet,
                'customer_new_fields' => $customer_new_fields,
                'apponitment_data' => $apponitment_data,
                'patient_activity_log' => $patient_activity_log,
                'patient_call_logs' => $patient_call_logs, // NEW
                'patient_prescriptions' => $patient_prescription, // NEW
                'medicines' => $medicines, // NEW
                'potencies' => $potencies, // NEW
                'appointment_type' => $appointment_type, // NEW
                'criteria' => $criteria, // NEW
                'doses' => $doses, // NEW
                'treatments' => $treatments, // NEW
                'patient_status' => $patient_status, // NEW
                'invoices' => $invoices, // NEW
                'invoice_payments' => $invoice_payments, // NEW
                'timings' => $timings // NEW
            ], true);
        }
        
        $this->load->view('manage', $data);
    }

    public function table()
    {
        if (staff_cant('view', 'customers')) {
            if (!have_assigned_customers() && staff_cant('create', 'customers')) {
                ajax_access_denied();
            }
        }

        App_table::find('clients')->output();
    }

    public function all_contacts()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('all_contacts');
        }

        if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }

        $data['title'] = _l('customer_contacts');
        $this->load->view('admin/clients/all_contacts', $data);
    }

    /*add new client*/
    public function add_client($id = '', $type = '')
    {
        if (staff_cant('view', 'customers')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
		$data['patient_inactive_fields'] = $this->client_model->patient_inactive_fields();
		$data['current_branch_id'] = $this->current_branch_id;
		
		$this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
		if($id != NULL){
			if($type != NULL){
				if($type == "Patient"){
					$existing_patient = $this->client_model->get($id);
					$data['patient_data'] = $existing_patient;
					$data['master_data'] = $this->load_master_data();
					
					$data['title'] = 'Enquiry Form';
					$this->load->view('client_form', $data);
				}else if($type == "Lead"){
					$existing_patient = $this->client_model->get_lead($id);
					$data['patient_data'] = $existing_patient;
					$data['master_data'] = $this->load_master_data();
					$data['title'] = 'Lead Form';
					$this->load->view('lead_form', $data);
				}
				
			}
			
            
		}else{
			$data['title'] = 'Enquiry Form';
			$this->load->view('client_form', $data);
		}
		

    }
	
	public function new_patient($id = '', $type = '')
    {
        if (staff_cant('view', 'customers')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
		
		$this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
		
		$data['master_data'] = $this->load_master_data();
		$data['title'] = 'Enquiry Form';
		$data['current_branch_id'] = $this->current_branch_id;
		$this->load->view('new_patient_form', $data);
		

    }

    private function load_master_data()
    {
        return $this->client_model->get_master_data();
    }

    public function export($contact_id)
    {
        if (is_admin()) {
            $this->load->library('gdpr/gdpr_contact');
            $this->gdpr_contact->export($contact_id);
        }
    }

    // Used to give a tip to the user if the company exists when new company is created
    public function check_duplicate_customer_name()
    {
        if (staff_can('create',  'customers')) {
            $companyName = trim($this->input->post('company'));
            $response    = [
                'exists'  => (bool) total_rows(db_prefix() . 'clients', ['company' => $companyName]) > 0,
                'message' => _l('company_exists_info', '<b>' . $companyName . '</b>'),
            ];
            echo json_encode($response);
        }
    }

    public function save_longitude_and_latitude($client_id)
    {
        if (staff_cant('edit', 'customers')) {
            if (!is_customer_admin($client_id)) {
                ajax_access_denied();
            }
        }

        $this->db->where('userid', $client_id);
        $this->db->update(db_prefix() . 'clients', [
            'longitude' => $this->input->post('longitude'),
            'latitude'  => $this->input->post('latitude'),
        ]);
        if ($this->db->affected_rows() > 0) {
            echo 'success';
        } else {
            echo 'false';
        }
    }

    public function form_contact($customer_id, $contact_id = '')
    {
        if (staff_cant('view', 'customers')) {
            if (!is_customer_admin($customer_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['customer_id'] = $customer_id;
        $data['contactid']   = $contact_id;

        if (is_automatic_calling_codes_enabled()) {
            $clientCountryId = $this->db->select('country')
                ->where('userid', $customer_id)
                ->get('clients')->row()->country ?? null;

            $clientCountry = get_country($clientCountryId);
            
            $callingCode   = $clientCountry->calling_code ? 
                ($clientCountry ? '+' . ltrim($clientCountry->calling_code, '+') : null) : 
                null;
        } else {
            $callingCode = null;
        }

        if ($this->input->post()) {
            $data             = $this->input->post();
            $data['password'] = $this->input->post('password', false);

            if ($callingCode && !empty($data['phonenumber']) && $data['phonenumber'] == $callingCode) {
                $data['phonenumber'] = '';
            }

            unset($data['contactid']);

            if ($contact_id == '') {
                if (staff_cant('create', 'customers')) {
                    if (!is_customer_admin($customer_id)) {
                        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');
                        echo json_encode([
                            'success' => false,
                            'message' => _l('access_denied'),
                        ]);
                        die;
                    }
                }
                $id      = $this->client_model->add_contact($data, $customer_id);
                $message = '';
                $success = false;
                if ($id) {
                    handle_contact_profile_image_upload($id);
                    $success = true;
                    $message = _l('added_successfully', _l('contact'));
                }
                echo json_encode([
                    'success'             => $success,
                    'message'             => $message,
                    'has_primary_contact' => (total_rows(db_prefix() . 'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),
                    'is_individual'       => is_empty_customer_company($customer_id) && total_rows(db_prefix() . 'contacts', ['userid' => $customer_id]) == 1,
                ]);
                die;
            }
            if (staff_cant('edit', 'customers')) {
                if (!is_customer_admin($customer_id)) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');
                    echo json_encode([
                            'success' => false,
                            'message' => _l('access_denied'),
                        ]);
                    die;
                }
            }
            $original_contact = $this->client_model->get_contact($contact_id);
            $success          = $this->client_model->update_contact($data, $contact_id);
            $message          = '';
            $proposal_warning = false;
            $original_email   = '';
            $updated          = false;
            if (is_array($success)) {
                if (isset($success['set_password_email_sent'])) {
                    $message = _l('set_password_email_sent_to_client');
                } elseif (isset($success['set_password_email_sent_and_profile_updated'])) {
                    $updated = true;
                    $message = _l('set_password_email_sent_to_client_and_profile_updated');
                }
            } else {
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('contact'));
                }
            }
            if (handle_contact_profile_image_upload($contact_id) && !$updated) {
                $message = _l('updated_successfully', _l('contact'));
                $success = true;
            }
            if ($updated == true) {
                $contact = $this->client_model->get_contact($contact_id);
                if (total_rows(db_prefix() . 'proposals', [
                        'rel_type' => 'customer',
                        'rel_id' => $contact->userid,
                        'email' => $original_contact->email,
                    ]) > 0 && ($original_contact->email != $contact->email)) {
                    $proposal_warning = true;
                    $original_email   = $original_contact->email;
                }
            }
            echo json_encode([
                    'success'             => $success,
                    'proposal_warning'    => $proposal_warning,
                    'message'             => $message,
                    'original_email'      => $original_email,
                    'has_primary_contact' => (total_rows(db_prefix() . 'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),
                ]);
            die;
        }


        $data['calling_code'] = $callingCode;

        if ($contact_id == '') {
            $title = _l('add_new', _l('contact'));
        } else {
            $data['contact'] = $this->client_model->get_contact($contact_id);

            if (!$data['contact']) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');
                echo json_encode([
                    'success' => false,
                    'message' => 'Contact Not Found',
                ]);
                die;
            }
            $title = $data['contact']->firstname . ' ' . $data['contact']->lastname;
        }

        $data['customer_permissions'] = get_contact_permissions();
        $data['title']                = $title;
        $this->load->view('admin/clients/modals/contact', $data);
    }

    public function confirm_registration($client_id)
    {
        if (!is_admin()) {
            access_denied('Customer Confirm Registration, ID: ' . $client_id);
        }
        $this->client_model->confirm_registration($client_id);
        set_alert('success', _l('customer_registration_successfully_confirmed'));
        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
    }

    public function update_file_share_visibility()
    {
        if ($this->input->post()) {
            $file_id           = $this->input->post('file_id');
            $share_contacts_id = [];

            if ($this->input->post('share_contacts_id')) {
                $share_contacts_id = $this->input->post('share_contacts_id');
            }

            $this->db->where('file_id', $file_id);
            $this->db->delete(db_prefix() . 'shared_customer_files');

            foreach ($share_contacts_id as $share_contact_id) {
                $this->db->insert(db_prefix() . 'shared_customer_files', [
                    'file_id'    => $file_id,
                    'contact_id' => $share_contact_id,
                ]);
            }
        }
    }

    public function delete_contact_profile_image($contact_id)
    {
        $this->client_model->delete_contact_profile_image($contact_id);
    }

    public function mark_as_active($id)
    {
        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'clients', [
            'active' => 1,
        ]);
        redirect(admin_url('clients/client/' . $id));
    }

    public function consents($id)
    {
        if (staff_cant('view', 'customers')) {
            if (!is_customer_admin(get_user_id_by_contact_id($id))) {
                echo _l('access_denied');
                die;
            }
        }

        $this->load->model('gdpr_model');
        $data['purposes']   = $this->gdpr_model->get_consent_purposes($id, 'contact');
        $data['consents']   = $this->gdpr_model->get_consents(['contact_id' => $id]);
        $data['contact_id'] = $id;
        $this->load->view('admin/gdpr/contact_consent', $data);
    }

    public function update_all_proposal_emails_linked_to_customer($contact_id)
    {
        $success = false;
        $email   = '';
        if ($this->input->post('update')) {
            $this->load->model('proposals_model');

            $this->db->select('email,userid');
            $this->db->where('id', $contact_id);
            $contact = $this->db->get(db_prefix() . 'contacts')->row();

            $proposals = $this->proposals_model->get('', [
                'rel_type' => 'customer',
                'rel_id'   => $contact->userid,
                'email'    => $this->input->post('original_email'),
            ]);
            $affected_rows = 0;

            foreach ($proposals as $proposal) {
                $this->db->where('id', $proposal['id']);
                $this->db->update(db_prefix() . 'proposals', [
                    'email' => $contact->email,
                ]);
                if ($this->db->affected_rows() > 0) {
                    $affected_rows++;
                }
            }

            if ($affected_rows > 0) {
                $success = true;
            }
        }
        echo json_encode([
            'success' => $success,
            'message' => _l('proposals_emails_updated', [
                _l('contact_lowercase'),
                $contact->email,
            ]),
        ]);
    }

    public function assign_admins($id)
    {
        if (staff_cant('create', 'customers') && staff_cant('edit', 'customers')) {
            access_denied('customers');
        }
        $success = $this->client_model->assign_admins($this->input->post(), $id);
        if ($success == true) {
            set_alert('success', _l('updated_successfully', _l('client')));
        }

        redirect(admin_url('clients/client/' . $id . '?tab=customer_admins'));
    }

    public function delete_customer_admin($customer_id, $staff_id)
    {
        if (staff_cant('create', 'customers') && staff_cant('edit', 'customers')) {
            access_denied('customers');
        }

        $this->db->where('customer_id', $customer_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->delete(db_prefix() . 'customer_admins');
        redirect(admin_url('clients/client/' . $customer_id) . '?tab=customer_admins');
    }

    public function delete_contact($customer_id, $id)
    {
        if (staff_cant('delete', 'customers')) {
            if (!is_customer_admin($customer_id)) {
                access_denied('customers');
            }
        }
        $contact      = $this->client_model->get_contact($id);
        $hasProposals = false;
        if ($contact && is_gdpr()) {
            if (total_rows(db_prefix() . 'proposals', ['email' => $contact->email]) > 0) {
                $hasProposals = true;
            }
        }

        $this->client_model->delete_contact($id);
        if ($hasProposals) {
            $this->session->set_flashdata('gdpr_delete_warning', true);
        }
        redirect(admin_url('clients/client/' . $customer_id . '?group=contacts'));
    }

    public function contacts($client_id)
    {
        $this->app->get_table_data('contacts', [
            'client_id' => $client_id,
        ]);
    }

    public function upload_attachment($id)
    {
        handle_client_attachments_upload($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database($this->input->post('clientid'), 'customer', $this->input->post('files'), $this->input->post('external'));
        }
    }

    public function delete_attachment($customer_id, $id)
    {
        if (staff_can('delete',  'customers') || is_customer_admin($customer_id)) {
            $this->client_model->delete_attachment($id);
        }
        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
    }

    /* Delete client */
    public function delete($id)
    {
        if (staff_cant('delete', 'customers')) {
            access_denied('customers');
        }
        if (!$id) {
            redirect(admin_url('clients'));
        }
        $response = $this->client_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('customer_delete_transactions_warning', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('credit_notes')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('client')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('client_lowercase')));
        }
        redirect(admin_url('client'));
    }

    /* Staff can login as client */
    public function login_as_client($id)
    {
        if (is_admin()) {
            login_as_client($id);
        }
        hooks()->do_action('after_contact_login');
        redirect(site_url());
    }

    public function get_customer_billing_and_shipping_details($id)
    {
        echo json_encode($this->client_model->get_customer_billing_and_shipping_details($id));
    }

    /* Change client status / active / inactive */
    public function change_contact_status($id, $status)
    {
        if (staff_can('edit',  'patients') || is_customer_admin(get_user_id_by_contact_id($id))) {
            if ($this->input->is_ajax_request()) {
                $this->client_model->change_contact_status($id, $status);
            }
        }
    }

    /* Change client status / active / inactive */
    public function change_client_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->client_model->change_client_status($id, $status);
        }
    }

    /* Zip function for credit notes */
    public function zip_credit_notes($id)
    {
        $has_permission_view = staff_can('view',  'credit_notes');

        if (!$has_permission_view && staff_cant('view_own', 'credit_notes')) {
            access_denied('Zip Customer Credit Notes');
        }

        if ($this->input->post()) {
            $this->load->library('app_bulk_pdf_export', [
                'export_type'       => 'credit_notes',
                'status'            => $this->input->post('credit_note_zip_status'),
                'date_from'         => $this->input->post('zip-from'),
                'date_to'           => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=credit_notes'),
            ]);

            $this->app_bulk_pdf_export->set_client_id($id);
            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
            $this->app_bulk_pdf_export->export();
        }
    }

    public function zip_invoices($id)
    {
        $has_permission_view = staff_can('view',  'invoices');
        if (!$has_permission_view && staff_cant('view_own', 'invoices')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            access_denied('Zip Customer Invoices');
        }

        if ($this->input->post()) {
            $this->load->library('app_bulk_pdf_export', [
                'export_type'       => 'invoices',
                'status'            => $this->input->post('invoice_zip_status'),
                'date_from'         => $this->input->post('zip-from'),
                'date_to'           => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=invoices'),
            ]);

            $this->app_bulk_pdf_export->set_client_id($id);
            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
            $this->app_bulk_pdf_export->export();
        }
    }

    /* Since version 1.0.2 zip client estimates */
    public function zip_estimates($id)
    {
        $has_permission_view = staff_can('view',  'estimates');
        if (!$has_permission_view && staff_cant('view_own', 'estimates')
            && get_option('allow_staff_view_estimates_assigned') == '0') {
            access_denied('Zip Customer Estimates');
        }

        if ($this->input->post()) {
            $this->load->library('app_bulk_pdf_export', [
                'export_type'       => 'estimates',
                'status'            => $this->input->post('estimate_zip_status'),
                'date_from'         => $this->input->post('zip-from'),
                'date_to'           => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=estimates'),
            ]);

            $this->app_bulk_pdf_export->set_client_id($id);
            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
            $this->app_bulk_pdf_export->export();
        }
    }

    public function zip_payments($id)
    {
        $has_permission_view = staff_can('view',  'payments');

        if (!$has_permission_view && staff_cant('view_own', 'invoices')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            access_denied('Zip Customer Payments');
        }

        $this->load->library('app_bulk_pdf_export', [
                'export_type'       => 'payments',
                'payment_mode'      => $this->input->post('paymentmode'),
                'date_from'         => $this->input->post('zip-from'),
                'date_to'           => $this->input->post('zip-to'),
                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=payments'),
            ]);

        $this->app_bulk_pdf_export->set_client_id($id);
        $this->app_bulk_pdf_export->set_client_id_column(db_prefix() . 'clients.userid');
        $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));
        $this->app_bulk_pdf_export->export();
    }

    public function import()
    {
        if (staff_cant('create', 'customers')) {
            access_denied('customers');
        }

        $dbFields = $this->db->list_fields(db_prefix() . 'contacts');
        foreach ($dbFields as $key => $contactField) {
            if ($contactField == 'phonenumber') {
                $dbFields[$key] = 'contact_phonenumber';
            }
        }

        $dbFields = array_merge($dbFields, $this->db->list_fields(db_prefix() . 'clients'));

        $this->load->library('import/import_customers', [], 'import');

        $this->import->setDatabaseFields($dbFields)
                     ->setCustomFields(get_custom_fields('patients'));

        if ($this->input->post('download_sample') === 'true') {
            $this->import->downloadSample();
        }

        if ($this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
            $this->import->setSimulation($this->input->post('simulate'))
                          ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
                          ->setFilename($_FILES['file_csv']['name'])
                          ->perform();


            $data['total_rows_post'] = $this->import->totalRows();

            if (!$this->import->isSimulation()) {
                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
            }
        }

        $data['groups']    = $this->client_model->get_groups();
        $data['title']     = _l('import');
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('admin/clients/import', $data);
    }

    public function groups()
    {
        if (!is_admin()) {
            access_denied('Customer Groups');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('customers_groups');
        }
        $data['title'] = _l('customer_groups');
        $this->load->view('admin/clients/groups_manage', $data);
    }

    public function group()
    {
        if (!is_admin() && get_option('staff_members_create_inline_customer_groups') == '0') {
            access_denied('Customer Groups');
        }

        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                $id      = $this->client_model->add_group($data);
                $message = $id ? _l('added_successfully', _l('customer_group')) : '';
                echo json_encode([
                    'success' => $id ? true : false,
                    'message' => $message,
                    'id'      => $id,
                    'name'    => $data['name'],
                ]);
            } else {
                $success = $this->client_model->edit_group($data);
                $message = '';
                if ($success == true) {
                    $message = _l('updated_successfully', _l('customer_group'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
        }
    }

    public function delete_group($id)
    {
        if (!is_admin()) {
            access_denied('Delete Customer Group');
        }
        if (!$id) {
            redirect(admin_url('clients/groups'));
        }
        $response = $this->client_model->delete_group($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('customer_group')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('customer_group_lowercase')));
        }
        redirect(admin_url('clients/groups'));
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_customers');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids    = $this->input->post('ids');
            $groups = $this->input->post('groups');

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($this->client_model->delete($id)) {
                            $total_deleted++;
                        }
                    } else {
                        if (!is_array($groups)) {
                            $groups = false;
                        }
                        $this->client_groups_model->sync_customer_groups($id, $groups);
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_clients_deleted', $total_deleted));
        }
    }

    public function vault_entry_create($customer_id)
    {
        $data = $this->input->post();

        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }

        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        unset($data['id']);
        $data['creator']      = get_staff_user_id();
        $data['creator_name'] = get_staff_full_name($data['creator']);
        $data['description']  = nl2br($data['description']);
        $data['password']     = $this->encryption->encrypt($this->input->post('password', false));

        if (empty($data['port'])) {
            unset($data['port']);
        }

        $this->client_model->vault_entry_create($data, $customer_id);
        set_alert('success', _l('added_successfully', _l('vault_entry')));
        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
    }

    public function vault_entry_update($entry_id)
    {
        $entry = $this->client_model->get_vault_entry($entry_id);

        if ($entry->creator == get_staff_user_id() || is_admin()) {
            $data = $this->input->post();

            if (isset($data['fakeusernameremembered'])) {
                unset($data['fakeusernameremembered']);
            }
            if (isset($data['fakepasswordremembered'])) {
                unset($data['fakepasswordremembered']);
            }

            $data['last_updated_from'] = get_staff_full_name(get_staff_user_id());
            $data['description']       = nl2br($data['description']);

            if (!empty($data['password'])) {
                $data['password'] = $this->encryption->encrypt($this->input->post('password', false));
            } else {
                unset($data['password']);
            }

            if (empty($data['port'])) {
                unset($data['port']);
            }

            $this->client_model->vault_entry_update($entry_id, $data);
            set_alert('success', _l('updated_successfully', _l('vault_entry')));
        }
        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
    }

    public function vault_entry_delete($id)
    {
        $entry = $this->client_model->get_vault_entry($id);
        if ($entry->creator == get_staff_user_id() || is_admin()) {
            $this->client_model->vault_entry_delete($id);
        }
        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
    }

    public function vault_encrypt_password()
    {
        $id            = $this->input->post('id');
        $user_password = $this->input->post('user_password', false);
        $user          = $this->staff_model->get(get_staff_user_id());

        if (!app_hasher()->CheckPassword($user_password, $user->password)) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error_msg' => _l('vault_password_user_not_correct')]);
            die;
        }

        $vault    = $this->client_model->get_vault_entry($id);
        $password = $this->encryption->decrypt($vault->password);

        $password = html_escape($password);

        // Failed to decrypt
        if (!$password) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');
            echo json_encode(['error_msg' => _l('failed_to_decrypt_password')]);
            die;
        }

        echo json_encode(['password' => $password]);
    }

    public function get_vault_entry($id)
    {
        $entry = $this->client_model->get_vault_entry($id);
        unset($entry->password);
        $entry->description = clear_textarea_breaks($entry->description);
        echo json_encode($entry);
    }

    public function statement_pdf()
    {
        $customer_id = $this->input->get('customer_id');

        if (staff_cant('view', 'invoices') && staff_cant('view', 'payments')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('clients/client/' . $customer_id));
        }

        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        $data['statement'] = $this->client_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));

        try {
            $pdf = statement_pdf($data['statement']);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(slug_it(_l('customer_statement') . '-' . $data['statement']['client']->company) . '.pdf', $type);
    }

    public function send_statement()
    {
        $customer_id = $this->input->get('customer_id');

        if (staff_cant('view', 'invoices') && staff_cant('view', 'payments')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('clients/client/' . $customer_id));
        }

        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        $send_to = $this->input->post('send_to');
        $cc      = $this->input->post('cc');

        $success = $this->client_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);
        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('statement_sent_to_client_success'));
        } else {
            set_alert('danger', _l('statement_sent_to_client_fail'));
        }

        redirect(admin_url('clients/client/' . $customer_id . '?group=statement'));
    }

    public function statement()
    {
        if (staff_cant('view', 'invoices') && staff_cant('view', 'payments')) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');
            echo _l('access_denied');
            die;
        }

        $customer_id = $this->input->get('customer_id');
        $from        = $this->input->get('from');
        $to          = $this->input->get('to');

        $data['statement'] = $this->client_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));

        $data['from'] = $from;
        $data['to']   = $to;

        $viewData['html'] = $this->load->view('admin/clients/groups/_statement', $data, true);

        echo json_encode($viewData);
    }

    /* Customised code*/


    public function save_client()
    {
        if (staff_cant('create', 'customers')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }
        if($this->input->post('Save')){
            $res = $this->client_model->save_client();
            if($res==0){
                set_alert('danger', _l('something_went_wrong'));
            }else if($res==1){
                set_alert('success', _l('customer_registration_successfully_confirmed'));
            }else if($res==2){
                set_alert('success', _l('appointment_created'));
            }
        }
        
        redirect('client/');
    }
    private function check_customer_permissions()
    {
        if (staff_cant('view', 'customers')) {
            if (!have_assigned_customers() && staff_cant('create', 'customers')) {
                access_denied('customers');
            }
        }
    }

    public function enquiry_type()
    {
        if (!is_admin()) {
            access_denied('enquiry_type');
        }
        $this->_handle_crud('enquiry_type');
    }

    public function specialization()
    {
        if (!is_admin()) {
            access_denied('specialization');
        }
        $this->_handle_crud('specialization');
    }

    public function shift()
    {
        if (!is_admin()) {
            access_denied('shift');
        }
        $this->_handle_crud('shift');
    }

    public function medicine()
    {
        if (!is_admin()) {
            access_denied('medicine');
        }
        $this->_handle_crud('medicine');
    }

    public function medicine_potency()
    {
        if (!is_admin()) {
            access_denied('medicine_potency');
        }
        $this->_handle_crud('medicine_potency');
    }

    public function medicine_dose()
    {
        if (!is_admin()) {
            access_denied('medicine_dose');
        }
        $this->_handle_crud('medicine_dose');
    }

    public function medicine_timing()
    {
        if (!is_admin()) {
            access_denied('medicine_timing');
        }
        $this->_handle_crud('medicine_timing');
    }

    public function patient_response()
    {
        if (!is_admin()) {
            access_denied('enquiry_type');
        }
        $this->_handle_crud('patient_response');
    }

    public function patient_priority()
    {
        if (!is_admin()) {
            access_denied('enquiry_type');
        }
        $this->_handle_crud('patient_priority');
    }

    public function slots()
    {
        if (!is_admin()) {
            access_denied('enquiry_type');
        }
        $this->_handle_crud('slots');
    }

    public function treatment()
    {
        if (!is_admin()) {
            access_denied('enquiry_type');
        }
        $this->_handle_crud('treatment');
    }

    public function consultation_fee()
    {
        if (!is_admin()) {
            access_denied('enquiry_type');
        }
        $this->_handle_crud('consultation_fee');
    }

    public function patient_status()
    {
        if (!is_admin()) {
            access_denied('patient_status');
        }
        $this->_handle_crud('patient_status');
    }

    private function _handle_crud($table)
    {
        
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data($table);
        } else {
            if ($this->input->post()) {
                if (!is_admin()) {
                    access_denied($table);
                }
                $data = $this->input->post();
                $id_field = $table . '_id';

                if (isset($data[$id_field]) && $data[$id_field] != '') {
                    $success = $this->master_model->update($table, $data[$id_field], $data);
                    if ($success) {
                        set_alert('success', _l('updated_successfully'));
                    }
                } else {
                    $id = $this->master_model->add($table, $data);
                    if ($id) {
                        set_alert('success', _l('added_successfully'));
                    }
                }

                redirect(admin_url('client/' . $table));
            }

            $data['title'] = _l($table);
            $data['slug'] = $table;
            $data['field_name'] = $table . '_name';
            $data['records'] = $this->master_model->get_all($table);
            $data['table'] = App_table::find('clients');
            
            $this->load->view('master', $data);
        }
    }

    public function get_record_by_id($slug)
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            $this->load->model('master_model');
            $record = $this->master_model->get_by_id($slug, $id);

            echo json_encode($record);
        } else {
            show_404();
        }
    }

    public function master_delete($table, $id)
    {
        $this->check_customer_permissions();
        if (!$id) {
            redirect(admin_url('client/' . $table));
        }
        $success = $this->master_model->delete($table, $id);
        if ($success) {
            set_alert('success', _l('deleted'));
        }
        redirect(admin_url('client/' . $table));
    }

    public function edit_client($id = '')
    {
        if (staff_cant('view', 'customers')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('customers');
            }
        }

        $data['master_data'] = $this->load_master_data();

        if($id){
            $data['client_data'] = $this->client_model->get($id);
            $data['apponitment_data'] = $this->client_model->get_apponitment_data($id);
        }
        
        $this->load->view('edit_client', $data);

    }

    public function update_client()
    {
        if (staff_can('edit', 'customers')) {
            if (!have_assigned_customers() && staff_cant('edit', 'customers')) {
                access_denied('customers');
            }
        }
        $this->client_model->update_client();
        set_alert('success', _l('customer_registration_successfully_confirmed'));
        redirect('client/');
    }


    public function client($id = '')
    {
    if (staff_cant('view', 'customers')) {
        if ($id != '' && !is_customer_admin($id)) {
            access_denied('customers');
        }
    }

    if ($this->input->post() && !$this->input->is_ajax_request()) {
        if ($id == '') {
            if (staff_cant('create', 'customers')) {
                access_denied('customers');
            }

            $data = $this->input->post();

            $save_and_add_contact = false;
            if (isset($data['save_and_add_contact'])) {
                unset($data['save_and_add_contact']);
                $save_and_add_contact = true;
            }
            $id = $this->clients_model->add($data);
            if (staff_cant('view', 'customers')) {
                $assign['customer_admins']   = [];
                $assign['customer_admins'][] = get_staff_user_id();
                $this->clients_model->assign_admins($assign, $id);
            }
            if ($id) {
                set_alert('success', _l('added_successfully', _l('client')));
                if ($save_and_add_contact == false) {
                    redirect(admin_url('clients/client/' . $id));
                } else {
                    redirect(admin_url('clients/client/' . $id . '?group=contacts&new_contact=true'));
                }
            }
        } else {
            if (staff_cant('edit', 'customers')) {
                if (!is_customer_admin($id)) {
                    access_denied('customers');
                }
            }
            $success = $this->clients_model->update($this->input->post(), $id);
            if ($success == true) {
                set_alert('success', _l('updated_successfully', _l('client')));
            }
            redirect(admin_url('clients/client/' . $id));
        }
    }

    $group         = !$this->input->get('group') ? 'profile' : $this->input->get('group');
    $data['group'] = $group;

    if ($group != 'contacts' && $contact_id = $this->input->get('contactid')) {
        redirect(admin_url('clients/client/' . $id . '?group=contacts&contactid=' . $contact_id));
    }

    // Customer groups
    $data['groups'] = $this->clients_model->get_groups();

    if ($id == '') {
        $title = _l('add_new', _l('client'));
    } else {
        $client                = $this->clients_model->get($id);
        $data['customer_tabs'] = get_customer_profile_tabs($id);
        
        $data['customer_tabs']['prescription'] = array(
            'slug' => 'prescription',
            'name' => 'Prescription',
            'icon' => 'fa fa-prescription-bottle', // FontAwesome icon for prescription
            'view' => 'admin/clients/groups/prescription', // Path to the corresponding view
            'position' => 6, // Position of the tab
            'badge' => array(), // Add badge information if required
            'href' => '#', // URL or link
            'children' => array(), // Add submenu items if needed
        );

        if (!$client) {
            show_404();
        }

        $data['contacts'] = $this->clients_model->get_contacts($id);
        $data['tab']      = isset($data['customer_tabs'][$group]) ? $data['customer_tabs'][$group] : null;

        if (!$data['tab']) {
            show_404();
        }

        // Fetch data based on groups
        if ($group == 'profile') {
            $data['customer_groups'] = $this->clients_model->get_customer_groups($id);
            $data['customer_admins'] = $this->clients_model->get_admins($id);
        } elseif ($group == 'attachments') {
            $data['attachments'] = get_all_customer_attachments($id);
        } elseif ($group == 'vault') {
            $data['vault_entries'] = hooks()->apply_filters('check_vault_entries_visibility', $this->clients_model->get_vault_entries($id));

            if ($data['vault_entries'] === -1) {
                $data['vault_entries'] = [];
            }
        } elseif ($group == 'estimates') {
            $this->load->model('estimates_model');
            $data['estimate_statuses'] = $this->estimates_model->get_statuses();
        } elseif ($group == 'invoices') {
            $this->load->model('invoices_model');
            $data['invoice_statuses'] = $this->invoices_model->get_statuses();
        } elseif ($group == 'credit_notes') {
            $this->load->model('credit_notes_model');
            $data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();
            $data['credits_available']     = $this->credit_notes_model->total_remaining_credits_by_customer($id);
        } elseif ($group == 'payments') {
            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get();
        } elseif ($group == 'notes') {
            $data['user_notes'] = $this->misc_model->get_notes($id, 'customer');
        }elseif ($group == 'prescription') {
            $this->load->model('srini_model');
           $data['user_prescription'] = $this->srini_model->get_prescription();
        } elseif ($group == 'projects') {
            $this->load->model('projects_model');
            $data['project_statuses'] = $this->projects_model->get_project_statuses();
        } elseif ($group == 'statement') {
            if (staff_cant('view', 'invoices') && staff_cant('view', 'payments')) {
                set_alert('danger', _l('access_denied'));
                redirect(admin_url('clients/client/' . $id));
            }

            $data = array_merge($data, prepare_mail_preview_data('customer_statement', $id));
        } elseif ($group == 'map') {
            if (get_option('google_api_key') != '' && !empty($client->latitude) && !empty($client->longitude)) {
                $this->app_scripts->add('map-js', base_url($this->app_scripts->core_file('assets/js', 'map.js')) . '?v=' . $this->app_css->core_version());

                $this->app_scripts->add('google-maps-api-js', [
                    'path'       => 'https://maps.googleapis.com/maps/api/js?key=' . get_option('google_api_key') . '&callback=initMap',
                    'attributes' => [
                        'async',
                        'defer',
                        'latitude'       => "$client->latitude",
                        'longitude'      => "$client->longitude",
                        'mapMarkerTitle' => "$client->company",
                    ],
                    ]);
            }
        }

        $data['staff'] = $this->staff_model->get('', ['active' => 1]);

        $data['client'] = $client;
        $title          = $client->company;

        // Get all active staff members (used to add reminder)
        $data['members'] = $data['staff'];

        if (!empty($data['client']->company)) {
            // Check if is realy empty client company so we can set this field to empty
            // The query where fetch the client auto populate firstname and lastname if company is empty
            if (is_empty_customer_company($data['client']->userid)) {
                $data['client']->company = '';
            }
        }
    }

    $this->load->model('currencies_model');
    $data['currencies'] = $this->currencies_model->get();

    if ($id != '') {
        $customer_currency = $data['client']->default_currency;

        foreach ($data['currencies'] as $currency) {
            if ($customer_currency != 0) {
                if ($currency['id'] == $customer_currency) {
                    $customer_currency = $currency;

                    break;
                }
            } else {
                if ($currency['isdefault'] == 1) {
                    $customer_currency = $currency;

                    break;
                }
            }
        }

        if (is_array($customer_currency)) {
            $customer_currency = (object) $customer_currency;
        }

        $data['customer_currency'] = $customer_currency;

        $slug_zip_folder = (
            $client->company != ''
            ? $client->company
            : get_contact_full_name(get_primary_contact_user_id($client->userid))
        );

        $data['zip_in_folder'] = slug_it($slug_zip_folder);
    }

    $data['bodyclass'] = 'customer-profile dynamic-create-groups';
    $data['title']     = $title;

    $this->load->view('admin/client/client', $data);
}


//Add patient activity log manually
public function add_patient_activity()
{
    if ($this->input->is_ajax_request()) {
        $this->load->model('client_model');

        $patientid = $this->input->post('patientid');
        $activity = $this->input->post('activity');

        if (!$patientid || !$activity) {
            echo json_encode(['success' => false, 'message' => 'Missing patient ID or activity.']);
            return;
        }

        // Save the activity
        $this->client_model->log_patient_activity($patientid, $activity, $custom_activity = 1);

        // ✅ Send redirect URL in response
        echo json_encode([
            'success' => true,
            'redirect' => admin_url('client/index/' . $patientid . '/tab_activity'),
        ]);
    }
}

// Add patient call log manually via AJAX
public function add_patient_call_log()
{
    if ($this->input->is_ajax_request()) {
        $this->load->model('client_model');

        $data = $this->input->post();
        $patientid = $data['patientid'];

        if (!$patientid || empty($data['comments'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            return;
        }

        // Save call log in your model (create method accordingly)
        $this->client_model->add_patient_call_log($data);

        // ✅ Send redirect URL in response to reload modal with tab_calls active
        set_alert('success', _l('added_successfully'));
        echo json_encode([
            'success'  => true,
            'redirect' => admin_url('client/index/' . $patientid . '/tab_calls'),
        ]);
    }
}

public function save_prescription()
{
    // Check if the request is an AJAX request
    if ($this->input->is_ajax_request()) {
        // Load the model where we will save the prescription data
        $this->load->model('client_model');

        // Get the form data sent via POST
        $data = $this->input->post();
        $patientid = $data['patientid'];

        // Validate the data: Ensure patientid, prescription data, and other required fields are provided
        if (!$patientid || empty($data['medicine_name']) || empty($data['medicine_potency']) || empty($data['medicine_dose'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            return;
        }

        // Save prescription in your model (create a method accordingly)
        $this->client_model->save_prescription($data, $patientid);

        // Send a success response with the redirect URL to view the prescription details
        echo json_encode([
            'success'  => true,
            'redirect' => admin_url('client/index/' . $patientid . '/tab_prescription'),
        ]);
    }
    else {
        // If not an AJAX request, show an error message
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }
}

    public function appointments()
    {
        if (staff_can('view_activity_log', 'customers')) {
            //access_denied('appointments');
        } 
		
       //$data['appointments'] = $this->client_model->get_appointments();
        $data['title'] = "Appointments";
		
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('client', 'tables/appointments_table'),$data);
        }
       $this->load->view('appointments', $data);
	  
    }
    public function visits($consulted_date = NULL)
    {
        if (staff_can('view_activity_log', 'customers')) {
            //access_denied('appointments');
        } 
		
       //$data['appointments'] = $this->client_model->get_appointments();
        $data['title'] = "Visits";
        $data['consulted_date'] = $consulted_date;
		
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('client', 'tables/visits_table'), $data);
        }
       $this->load->view('visits', $data);
	  
    }
	
    
	
	public function confirm_booking()
	{
		$id = $this->input->post('id');

		if ($id) {
			$this->db->from(db_prefix() . 'appointment');
			$this->db->where(array("visit_status"=>1));
			$this->db->like('DATE(appointment_date)', date('Y-m-d'));
			$count = $this->db->count_all_results();
			
			$branch_id = $this->current_branch_id; // or fetch from session/context if not already available

			$get_branch_code = $this->db->get_where(db_prefix() . 'master_settings', [
				'title'     => 'branch_code',
				'branch_id' => $branch_id
			])->row();
			$branch_code = $get_branch_code ? $get_branch_code->options : '';

			$get_branch_short_code = $this->db->get_where(db_prefix() . 'master_settings', [
				'title'     => 'branch_short_code',
				'branch_id' => $branch_id
			])->row();
			$branch_short_code = $get_branch_short_code ? $get_branch_short_code->options : '';
			
			if($count){
				$number = $branch_code.'-'.$branch_short_code.'-'.$count + 1;
			}else{
				$number = $branch_code.'-'.$branch_short_code.'-1';
			}
			
			$formatted_number = str_pad($number, 4, '0', STR_PAD_LEFT);
			$visit_id = "V-".$formatted_number;
			
			
			$this->db->where('appointment_id', $id);
			$this->db->update(db_prefix() . 'appointment', ['visit_status' => '1', "visit_id"=>$visit_id, "consulted_date"=>date('Y-m-d H:i:s')]);

			echo json_encode(['success' => true, 'message' => _l('visit_successfully_confirmed')]);
		} else {
			echo json_encode(['success' => false, 'message' => _l('something_went_wrong')]);
		}
	}




    public function save_casesheet()
    {
        // Check if the request is an AJAX request
        if ($this->input->is_ajax_request()) {
    
            // Get the form data sent via POST
            $data = $this->input->post();
            $patientid = $data['patientid'];
    
            // Validate the data: Ensure patientid, prescription data, and other required fields are provided
            /*if (!$patientid || empty($data['medicine_name']) || empty($data['medicine_potency']) || empty($data['medicine_dose'])) {
                echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
                return;
            }*/
    
            // Save prescription in your model (create a method accordingly)
            $this->client_model->save_casesheet($patientid);
    
            // Send a success response with the redirect URL to view the prescription details
            echo json_encode([
                'success'  => true,
                'redirect' => admin_url('client/index/' . $patientid . '/tab_casesheet'),
            ]);
        }
        else {
            // If not an AJAX request, show an error message
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        }
    }

    

    public function casesheet_view($id)
	{
		$case = $this->client_model->get_casesheet_by_id($id);
		if (!$case) {
			show_404();
		}

		// Handle PDF download
		if ($this->input->post('casesheetpdf')) {
			try {
				// You need to implement this similar to invoice_pdf()
				$pdf = casesheet_pdf($case);
			} catch (Exception $e) {
				echo $e->getMessage();
				die;
			}

			$case_number = 'CASE-' . $case->id;
			$companyname = get_option('invoice_company_name'); // reuse same config
			if ($companyname != '') {
				$case_number .= '-' . mb_strtoupper(slug_it($companyname), 'UTF-8');
			}

			$pdf->Output(mb_strtoupper(slug_it($case_number), 'UTF-8') . '.pdf', 'D');
			die();
		}

		// Normal view loading
		$data['case'] = $case;
		$this->load->view('view_case_sheet_modal', $data);
	}
    public function edit_casesheet($id, $patientid)
	{
		$case = $this->client_model->get_casesheet_by_id($id);
		if (!$case) {
			show_404();
		}

		// Handle PDF download
		if ($this->input->post('casesheetpdf')) {
			
		}

		// Normal view loading
		$data['case'] = $case;
		$data['prev_treatments'] = $this->client_model->prev_treatments($patientid);
		$data['prev_documents'] = $this->client_model->prev_documents($patientid);
		$data['treatments'] = $this->master_model->get_all('treatment');
		$data['patient_status'] = $this->master_model->get_all('patient_status');
		$this->load->view('edit_casesheet', $data);
	}
	public function update_casesheet()
    {
        // Check if the request is an AJAX request
        if ($this->input->is_ajax_request()) {
    
            // Get the form data sent via POST
            $data = $this->input->post();
            $patientid = $data['patientid'];
    
            // Save prescription in your model (create a method accordingly)
            $this->client_model->update_casesheet();
    
            // Send a success response with the redirect URL to view the prescription details
            echo json_encode([
                'success'  => true,
                'redirect' => admin_url('client/index/' . $patientid . '/tab_casesheet'),
            ]);
        }
        else {
            // If not an AJAX request, show an error message
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        }
    }
    
   public function get_dynamic_options()
	{
		log_message('debug', 'Dynamic options AJAX request received');
		$this->load->database();

		$result = $this->db->get('tblcustomers_groups')->result();

		$options = [];
		foreach ($result as $row) {
			$options[] = [
				'value' => $row->id,
				'label' => $row->name
			];
		}

		echo json_encode($options);
	}
	
	public function search_contact_number()
	{
		$contact = $this->input->post('contact');
		$results = $this->client_model->search_by_contact_number($contact);

		$html = '<ul class="dropdown-menu search-results animated fadeIn no-mtop display-block">';
		if (!empty($results)) {
			$html .= '<li class="dropdown-header">Matching Contacts</li>';
			foreach ($results as $row) {
				if($row['type'] == "Patient"){
					$label_class = "success";
				}else{
					$label_class = "warning";
				}
				
				$type_label = '<span class="label label-' . $label_class . '">' . e($row['type']) . '</span>';

			$html .= '<li style="width: 300px;"> 
            <a href="' . admin_url('client/client/add_client/' . $row['id'] . '/' . $row['type']) . '">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong>' . e($row['company']) . '</strong>
                    ' . $type_label . '
                </div>
                <div style="margin-top: 4px; color: #555;">' . e($row['phonenumber']) . '</div>
            </a>
          </li>';

			}
		} else {
			$html .= '<li><a href="#">No results found</a></li>';
		}
		$html .= '</ul>';

		echo json_encode(['results' => $html]);
		die;
	}
	
	
	//Reports
	public function appointment_reports($consulted_date = NULL)
    {
        if (staff_cant('appointment_reports', 'reports')) {
            access_denied('appointment_reports');
        } 
        $data['title'] = _l("appointment_reports");
		$data['consulted_date'] = $consulted_date;
		
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('client', 'tables/appointment_reports_table'), $data);
        }
       $this->load->view('reports/appointment_reports', $data);
	  
    }
	
	public function doctor_ownership_reports($consulted_date = NULL)
    {
        if (staff_cant('doctor_ownership_reports', 'reports')) {
            access_denied('doctor_ownership_reports');
        } 
        $data['title'] = _l("doctor_ownership_reports");
		$data['consulted_date'] = $consulted_date;
		
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('client', 'tables/doctor_ownership_reports_table'), $data);
        }
       $this->load->view('reports/doctor_ownership_reports', $data);
	  
    }

	public function ownership_details($type, $doctor_id = NULL)
    {
        if (staff_cant('ownership_details', 'reports')) {
            access_denied('ownership_details');
        } 
        $data['title'] = _l("doctor_ownership_details_details");
        $data['type'] = $type;
        $data['doctor_id'] = $doctor_id;
		$data['ownership_details'] =  $this->client_model->ownership_details($type, $doctor_id);
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('client', 'tables/ownership_details_table'), $data);
        }
       $this->load->view('reports/ownership_details', $data);
	  
    }


	

}
