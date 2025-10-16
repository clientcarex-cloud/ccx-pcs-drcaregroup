<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'appointments.appointment_id',
    'appointments.visit_id',
    'appointments.userid',
    'new.mr_no',
    'patients.company as patient_name',
    'patients.phonenumber as patient_mobile',
    'appointments.appointment_date',
];

$sIndexColumn = 'appointments.appointment_id';
$sTable       = db_prefix() . 'appointment appointments';

$join = [
    'LEFT JOIN ' . db_prefix() . 'clients patients ON patients.userid = appointments.userid',
    'LEFT JOIN ' . db_prefix() . 'clients_new_fields new ON new.userid = patients.userid'
];

$where = [];

// ✅ Force default ordering by ID in DESC order
//$order = 'appointments.appointment_id DESC';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['appointments.appointment_id']);
$output  = $result['output'];
$rResult = $result['rResult'];


foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['visit_id'];
    $row[] = $aRow['mr_no'];
    $row[] = $aRow['patient_name'];
    $row[] = $aRow['patient_mobile'];
    $row[] = _d($aRow['appointment_date']);

    $id = $aRow['appointment_id'];
    $edit = "open_edit_modal('$id')";
    $delete = "delete_appointment('" . admin_url("appointments/delete/$id") . "')";
	
	$url = admin_url('client/index/' . $aRow['userid']);

	$row[] = '
		<a href="' . $url . '" class="btn btn-success btn-sm" style="color: #fff" title="View Client">
			<i class="fa fa-plus"></i>
		</a>';

    /* $row[] = '
        <a href="javascript:void(0);" onclick="' . $edit . '" class="btn btn-warning btn-sm">
            Edit
        </a>
        <a href="javascript:void(0);" onclick="' . $delete . '" class="btn btn-danger btn-sm">
            Delete
        </a>'; */

    $output['aaData'][] = $row;
}
