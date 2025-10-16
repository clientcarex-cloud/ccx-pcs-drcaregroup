<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = ['staffid', 'username', 'password'];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'voip_settings';

$where = [];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

usort($rResult, function ($a, $b) {
    return $a['id'] - $b['id'];
});

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = isset($aRow['staffid']) ? $aRow['staffid'] : '';
    $row[] = isset($aRow['username']) ? $aRow['username'] : '';
    $row[] = $aRow['password']; // Mask password for security

    $edit = "open_voip_modal('"
        . addslashes($aRow['id']) . "','"
        . addslashes($aRow['staffid']) . "','"
        . addslashes($aRow['username']) . "','"
        . addslashes($aRow['password']) . "')";

    $delete = "delete_voip_setting('" . admin_url('voip/settings/delete/' . $aRow['id']) . "')";

    $row[] = '
        <a href="javascript:void(0);" onclick="' . $edit . '" class="btn btn-warning btn-sm">Edit</a>
        <a href="javascript:void(0);" onclick="' . $delete . '" class="btn btn-danger btn-sm">Delete</a>
    ';

    $output['aaData'][] = $row;
}

