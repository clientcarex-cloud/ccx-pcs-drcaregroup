<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Voip extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Voip_model');
    }

    public function index()
    {
        $data['title'] = _l('voip_settings');
		$data['voip_settings'] = $this->Voip_model->get_all_voip_settings();
		$data['staff'] = $this->staff_model->get();
		
        $this->load->view('voip_settings', $data);
    }

    public function add()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $insert_id = $this->Voip_model->add_voip_setting($data);
            if ($insert_id) {
                set_alert('success', _l('added_successfully', _l('voip_setting')));
            }
            redirect(admin_url('voip'));
        }

        $data['title'] = _l('add_voip_setting');
        $this->load->view('voip_settings', $data);
    }

    public function edit($id)
	{
		if (!$id) {
			redirect(admin_url('voip'));
		}

		if ($this->input->post()) {
			$data = $this->input->post([
				'staffid',
				'username',
				'password',
			]);

			

			$updated = $this->Voip_model->update_voip_setting($id, $data);

			if ($updated) {
				set_alert('success', _l('updated_successfully', _l('voip_setting')));
			}

			redirect(admin_url('voip'));
		}

		$data['voip'] = $this->Voip_model->get_voip_setting_by_id($id);
		$data['title'] = _l('edit_voip_setting');
		$data['staff'] = $this->staff_model->get();
		$this->load->view('voip_settings_edit', $data);
	}

    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('voip'));
        }

        $deleted = $this->Voip_model->delete_voip_setting($id);
        if ($deleted) {
            set_alert('success', _l('deleted', _l('voip_setting')));
        }
        redirect(admin_url('voip'));
    }
	
	

}
