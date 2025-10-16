<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Dr. Care System
Description: BPA & RPA for Dr. Care
Version: 1.0.0
Author: Fahad Ahmed
*/

define('HELLO_WORLD_MODULE_NAME', 'hello_world');

hooks()->add_action('admin_init', HELLO_WORLD_MODULE_NAME.'_init_menu_items');

function hello_world_init_menu_items()
{
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item('hello_world', [
        'name'     => _l('hello_world'),
        'icon'     => 'fa fa-globe',
        'href'     => admin_url('hello_world'),
        'position' => 20,
    ]);
}

register_activation_hook(HELLO_WORLD_MODULE_NAME, 'hello_world_module_activation_hook');

function hello_world_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

register_language_files(HELLO_WORLD_MODULE_NAME, [HELLO_WORLD_MODULE_NAME]);


hooks()->add_action('app_init', function () {
    hooks()->add_filter('customer_profile_tabs', 'add_custom_tabs');
});

/**
 * Function to add custom tabs in client profile
 */
function add_custom_tabs($tabs) {
    $tabs[] = [
        'slug'     => 'prescription',
        'name'     => 'Prescription',
        'url'      => admin_url('hello_world/custom_tab1'),
        'icon'     => 'fa fa-file-medical', // Medical document icon
        'position' => 11
    ];

    $tabs[] = [
        'slug'     => 'case_sheet',
        'name'     => 'Case Sheet',
        'url'      => admin_url('hello_world/custom_tab2'),
        'icon'     => 'fa fa-notes-medical', // Medical notes icon
        'position' => 10
    ];
    
    $tabs[] = [
        'slug'     => 'review_visits',
        'name'     => 'Review Visits',
        'url'      => admin_url('hello_world/custom_tab2'),
        'icon'     => 'fa fa-calendar-check', // Appointment review icon
        'position' => 12
    ];

    return $tabs;
}

