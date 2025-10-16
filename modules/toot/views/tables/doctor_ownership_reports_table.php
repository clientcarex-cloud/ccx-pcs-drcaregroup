<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$CI->load->model('client_model');

$CI->db->select('roleid');
$CI->db->from(db_prefix() . 'roles');
$CI->db->where('LOWER(name)', 'doctor');
$query1 = $CI->db->get();
if ($query1->num_rows() > 0) {
	$roleid = $query1->row()->roleid;
	
}

$consulted_date = $_POST['consulted_date'] ?? null;

if (!empty($consulted_date)) {
    $sql_date = to_sql_date($consulted_date);
    $CI->db->group_start();
    $CI->db->like('appointments.consulted_date', $sql_date, 'after');
    $CI->db->or_like('appointments.appointment_date', $sql_date, 'after');
    $CI->db->group_end();
}

$CI->db->select([
   'doctor.staffid',
   'doctor.firstname',
   'doctor.lastname',
   '(SELECT COUNT(*) FROM tblappointment AS appointment WHERE appointment.enquiry_doctor_id = doctor.staffid) AS appointment_count',
   '(SELECT COUNT(*) FROM tblappointment AS appointment WHERE appointment.enquiry_doctor_id = doctor.staffid AND visit_status=1) AS visits_count',
   '(SELECT COUNT(*) FROM tblappointment AS appointment WHERE appointment.enquiry_doctor_id = doctor.staffid AND visit_status=1) AS registration_count',
   '(SELECT COUNT(*) FROM tblappointment AS appointment WHERE appointment.enquiry_doctor_id = doctor.staffid AND visit_status=0) AS missed_consultation',
]);
$CI->db->from(db_prefix() . 'staff as doctor');
if($roleid){
	$CI->db->where(array("doctor.role"=>$roleid));
}

$CI->db->order_by('appointment_count', 'DESC'); // optional

$query = $CI->db->get();
$results = $query->result_array();

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;

$output = [
    "draw" => $draw,
    "recordsTotal" => count($results),         // or total records count from DB
    "recordsFiltered" => count($results),      // if no filters are applied
    "data" => []
];


$doctor_patients = [];
$show_finance_for_both = 1;
$finance_query = $CI->db->get_where(db_prefix() . 'master_settings', array("title"=>'show_finance_for_all'))->row();
if($finance_query){
	if($finance_query->options == 'Yes'){
		$show_finance_for_both = 1;
	}else{
		$show_finance_for_both = 0;
	}
	
}else{
	$show_finance_for_both = 0;
}

if ($show_finance_for_both == 0) {
    // 🔒 Only first doctor per patient
    $CI->db->select('a.userid, a.enquiry_doctor_id');
    $CI->db->from('tblappointment a');
    $CI->db->join(
        '(SELECT MIN(appointment_id) as appointment_id
          FROM tblappointment
          GROUP BY userid) as b',
        'a.appointment_id = b.appointment_id',
        'inner'
    );
    $query = $CI->db->get();
    $result = $query->result_array();

    foreach ($result as $row) {
        $doctor_id = $row['enquiry_doctor_id'];
        $user_id = $row['userid'];

        if (!isset($doctor_patients[$doctor_id])) {
            $doctor_patients[$doctor_id] = [];
        }

        $doctor_patients[$doctor_id][] = $user_id;
    }
} else {
    // ✅ Allow all doctors a patient visited
    $CI->db->distinct();
    $CI->db->select('userid, enquiry_doctor_id');
    $CI->db->from('tblappointment');
    $CI->db->where('enquiry_doctor_id IS NOT NULL', null, false);
    $query = $CI->db->get();
    $result = $query->result_array();

    foreach ($result as $row) {
        $doctor_id = $row['enquiry_doctor_id'];
        $user_id = $row['userid'];

        if (!isset($doctor_patients[$doctor_id])) {
            $doctor_patients[$doctor_id] = [];
        }

        // Optional: avoid duplicates
        if (!in_array($user_id, $doctor_patients[$doctor_id])) {
            $doctor_patients[$doctor_id][] = $user_id;
        }
    }
}

// Sort user IDs for each doctor (optional)
foreach ($doctor_patients as &$user_ids) {
    sort($user_ids);
}



foreach ($results as $aRow) {
    
	$doctor_id = $aRow['staffid'];
	
	$total = 0;
	$paid = 0;
	$due  = 0;
	$registered  = 0;

	$seen_invoice_ids = [];

	if (isset($doctor_patients[$doctor_id])) {
		foreach ($doctor_patients[$doctor_id] as $userid) {
			$packageDetailsList = $CI->client_model->get_patient_package_details($userid);
			if($packageDetailsList){
				$registered +=1;
			}
			foreach ($packageDetailsList as $package) {
				$total += $package['total'];

				$invoice_id = $package['invoice_id'];

				if (!in_array($invoice_id, $seen_invoice_ids)) {
					$paid += $package['paid'];
					$due  += $package['due'];
					$seen_invoice_ids[] = $invoice_id;
				}
			}
		}
	}

	// Add results to row
	$row = [];
    $row[] = $aRow['firstname'] . ' ' . $aRow['lastname'];
    $type = 'appointment';
	$doctor_id = $aRow['staffid'];
	$link = admin_url("client/ownership_details/{$type}/{$doctor_id}");
	$row[] = '<a href="' . $link . '" style="color:blue;">' . $aRow['appointment_count'] . '</a>';

	$type = 'visit';
	$link = admin_url("client/ownership_details/{$type}/{$doctor_id}");
	$row[] = '<a href="' . $link . '" style="color:blue;">' . $aRow['visits_count'] . '</a>';

	
    $row[] = $registered;
	$row[] = format_money($total, '');
	$row[] = format_money($paid, '');
	$row[] = format_money($due, '');
	$row[] = $aRow['missed_consultation'];
	$row[] = $aRow['visits_count'] - $registered;

    $output['data'][] = $row;
}

echo json_encode($output);
exit;

