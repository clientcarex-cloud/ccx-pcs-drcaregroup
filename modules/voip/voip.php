<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Voip Calling
Description: Voip Calling
Version: 1.0.0
*/

define('VOIP_MODULE_NAME', 'voip');

// Register activation hook
register_activation_hook(VOIP_MODULE_NAME, 'voip_module_activation_hook');
function voip_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

// Also call install on load (if needed)
$CI = &get_instance();
require_once(__DIR__ . '/install.php');

// Add permissions and menu
hooks()->add_action('admin_init', 'voip_register_permissions');
hooks()->add_action('admin_init', 'voip_module_init_menu_items');

function voip_module_init_menu_items()
{
    $CI = &get_instance(); 
   
	if (staff_can('view', 'voip')) {
			$CI->app_menu->add_sidebar_menu_item('voip', [
			'name'     => _l('voip'),
			'icon'     => 'fa fa-phone',
			'href'     => admin_url('voip'),
			'position' => 10,
		]);
	}
}

function voip_register_permissions()
{
    $capabilities['capabilities'] = [
        'view'     => _l('permission_view') . '(' . _l('permission_global') . ')',
        'view_own' => _l('permission_view_own'),
        'voip_settings'      => _l('voip_settings'),
    ];

    register_staff_capabilities('voip', $capabilities, _l('voip'));
}

hooks()->add_action('admin_navbar_start', 'voip_add_call_icon');

function voip_add_call_icon()
{
    if (staff_can('view', 'settings')) {
        echo '
        <li>
            <a href="javascript:void(0);" id="callToggleBtn" title="Call" onclick="toggleSIPWidget()">
                <i class="fa fa-phone"></i>
            </a>
        </li>
        ';
    }
}


hooks()->add_action('admin_init', 'voip_load_js_scripts');

function voip_load_js_scripts()
{
    $CI = &get_instance();

    // ✅ Skip for AJAX requests
    if ($CI->input->is_ajax_request()) {
        return;
    }

    $staff_id = get_staff_user_id();
    $staff_credentials = $CI->db->get_where(db_prefix() . 'voip_settings', ['staffid' => $staff_id])->row();

    if (!$staff_credentials) {
        return;
    }

    $username = html_escape($staff_credentials->username);
    $password = html_escape($staff_credentials->password);

    // ✅ Append only on full page load (footer)
    hooks()->add_action('app_admin_footer', function () use ($username, $password) {
        echo '<script type="text/javascript" src="https://wave.gdms.cloud/gsWaveH5.js"></script>';
        echo <<<JS
<script>
window.addEventListener("load", () => {
    var waveEmbeddedH5 = window.waveEmbeddedH5;
    if (waveEmbeddedH5) {
        waveEmbeddedH5.handleServerInfo({
            serverUrl: 'https://amrucm.scallerhost.com/',
            username: "{$username}",
            password: "{$password}"
        });

        waveEmbeddedH5.onConnectionStateChange = (state) => console.log("Connection Status:", state);
        waveEmbeddedH5.onError = (error) => console.error("Wave Error:", error);

        waveEmbeddedH5.onRecvP2PIncomingCall = (callInfo) => {
            var displayName = 'XXX';
            callInfo.ringingName = displayName;
            waveEmbeddedH5.modifyIncomingCallInfo(callInfo);
            console.log("Incoming call from:", displayName);
        };
    }
});

let isWidgetVisible = false;

function toggleSIPWidget() {
    const btn = document.getElementById('callToggleBtn');
    const icon = btn.querySelector('i');
    const screenOptionsBtn = document.querySelector('.screen-options-btn');
    const filter = document.querySelector('.dataTables_filter');

    if (!isWidgetVisible) {
        window.waveEmbeddedH5.loadWidget();
        icon.classList.replace('fa-phone', 'fa-phone-slash');
        btn.title = 'End Call';
        if (screenOptionsBtn) screenOptionsBtn.style.display = 'none';
        if (filter) filter.style.display = 'none';
    } else {
        window.waveEmbeddedH5.toggleDisplayWidget();
        icon.classList.replace('fa-phone-slash', 'fa-phone');
        btn.title = 'Call';
        if (screenOptionsBtn) screenOptionsBtn.style.display = '';
        if (filter) filter.style.display = '';
    }

    isWidgetVisible = !isWidgetVisible;
}
</script>
<style>
iframe[src*="wave.gdms.cloud"] {
    position: fixed !important;
    top: 90px !important;
    z-index: 9999 !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
    pointer-events: auto !important;
}

.screen-options-btn {
    z-index: 10 !important;
    position: relative !important;
}
.gs-wave-embedded-frame-header {
    outline: none !important;
    box-shadow: none !important;
}
</style>
JS;
    });
}


