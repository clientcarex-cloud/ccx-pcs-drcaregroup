<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$CI->load->model('client_model');  // load your model, adjust name if needed

// You can get POST parameters for filtering, pagination, search etc. here if needed
// e.g. $search = $CI->input->post('search')['value'] ?? '';






        $CI->db->select('c.*, co.*, ct.*, new.*, cg.groupid' ); // c = clients, co = countries, ct = contacts

        $CI->db->from(db_prefix() . 'clients c');

        $CI->db->join(db_prefix() . 'countries co', 'co.country_id = c.country', 'left');
        $CI->db->join(db_prefix() . 'clients_new_fields new', 'new.userid = c.userid', 'left');
        $CI->db->join(db_prefix() . 'contacts ct', 'ct.userid = c.userid AND ct.is_primary = 1', 'left');
        $CI->db->join(db_prefix() . 'customer_groups cg', 'cg.customer_id = c.userid', 'left');

        if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $CI->db->where($where);
        }

$query = $CI->db->get();
$results = $query->result_array();

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;

$output = [
    "draw" => $draw,
    "recordsTotal" => count($results),   // ideally total records count from DB
    "recordsFiltered" => count($results),// adjust if filters/search used
    "data" => []
];

// Build data rows for DataTables
foreach ($results as $row) {
    $dataRow = [];

    $dataRow[] = $row['userid'];
    $dataRow[] = '<a href="' . admin_url('toot/patient_case_sheet/' . $row['userid']) . '">' . e($row['company']) . '</a>';

    $dataRow[] = $row['phonenumber'];
    $dataRow[] = $row['city'];
    $dataRow[] = $row['state'];
   

    $output['data'][] = $dataRow;
}

echo json_encode($output);
exit;
