<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: TOOT Dental
Description: TOOT Dental
Version: 1.0.0
*/

define('TOOT_MODULE_NAME', 'toot');
log_message('debug', 'TOOT module loaded');

hooks()->add_action('admin_init', TOOT_MODULE_NAME.'_init_menu_items');

function toot_init_menu_items()
{
    $CI = &get_instance();
	if (staff_can('view_own', 'toot') || staff_can('view', 'toot')) {
    $CI->app_menu->add_sidebar_menu_item('toot', [
        'name'     => _l('toot'),
        'icon'     => 'fa fa-tooth',
        //'href'     => admin_url('TOOT'),
        'position' => 2,
    ]);
	}
	if (staff_can('view_own', 'toot') || staff_can('view', 'toot')) {
    $CI->app_menu->add_sidebar_children_item('toot', [
        'slug'     => 'get_patient_list',
        'name'     => _l('get_patient_list'),
        'href'     => admin_url('toot/get_patient_list'),
        'position' => 1,
        ]);
	}
    
    
    

}

hooks()->add_filter('after_render_single_custom_field', 'override_staff_select_branch_field', 10, 2);

hooks()->add_filter('staff_permissions', function ($permissions) {
    $viewGlobalName = _l('permission_view') . ' (' . _l('permission_global') . ')';
    $allPermissionsArray = [
        'view' => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    // For customers, this variable should be defined as it is used in array_merge
    $withNotApplicableViewOwn = [
        'view_own' => _l('permission_view_own'),
        'view'     => $viewGlobalName,
        'create'   => _l('permission_create'),
        'edit'     => _l('permission_edit'),
        'delete'   => _l('permission_delete'),
    ];

    $permissions['doctor'] = [
        'name' => _l('doctor'),
        'capabilities' => $allPermissionsArray,
    ];

    $permissions['customers_toot'] = [
        'name' => _l('TOOT'),
        'capabilities' => [
			'view_own' => _l('permission_view_own'),
			'view'     => $viewGlobalName,
			'create'   => _l('permission_create'),
			'edit'     => _l('permission_edit'),
			'delete'   => _l('permission_delete'),
            'view_overview' => _l('permission_view_overview'),
            'create_prescription' => _l('permission_create_prescription'),
            'edit_prescription' => _l('permission_edit_prescription'),
            'view_prescription' => _l('permission_view_prescription'),
            'view_appointments_calendar' => _l('permission_view_appointments_calendar'),
            'view_appointments' => _l('permission_view_appointments'),
            'view_doctor_ownership' => _l('permission_view_doctor_ownership'),
            'view_invoice' => _l('permission_view_invoice'),
            'create_invoice' => _l('permission_create_invoice'),
            'edit_invoice' => _l('permission_edit_invoice'),
            'view_feedback' => _l('permission_view_feedback'),
            'create_feedback' => _l('permission_create_feedback'),
            'edit_feedback' => _l('permission_edit_feedback'),
            'view_payments' => _l('permission_view_payments'),
            'view_call_log' => _l('permission_view_call_log'),
            'create_call_log' => _l('permission_create_call_log'),
            'edit_call_log' => _l('permission_edit_call_log'),
            'view_activity_log' => _l('permission_view_activity_log'),
            'view_casesheet' => _l('view_casesheet'),
            'create_casesheet' => _l('create_casesheet'),
        ],
        'help' => [
            'view_own' => _l('permission_customers_based_on_admins'),
        ],
    ];

    return $permissions;
});




register_activation_hook(TOOT_MODULE_NAME, 'toot_module_activation_hook');

function toot_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
    toot_install();
}

register_deactivation_hook(TOOT_MODULE_NAME, 'toot_module_uninstall_hook');

function toot_module_uninstall_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
    toot_uninstall();
}

register_language_files(TOOT_MODULE_NAME, [TOOT_MODULE_NAME]);



hooks()->add_action('app_admin_footer', function () {
    ?>
    <script>
        $(function () {
            setTimeout(function () {
                var select = $("select[data-fieldto='staff'][data-fieldid='1']");
                if (select.length) {
                    $.ajax({
                        url: admin_url + 'TOOT/get_dynamic_options',
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            select.empty();
                            select.append('<option value=""></option>');
                            $.each(data, function (i, item) {
                                select.append($('<option>').val(item.value).text(item.label));
                            });

                            var currentVal = select.attr('data-custom-field-value');
                            if (currentVal) {
                                select.val(currentVal);
                                select.selectpicker('refresh');
                            } else {
                                select.selectpicker('refresh');
                            }
                        }
                    });
                }
            }, 500);
        });
    </script>
    <?php
});

