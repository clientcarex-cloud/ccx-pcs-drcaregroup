<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Voip_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_voip_settings()
    {
		
         $this->db->select(db_prefix() . 'voip_settings.*, staff.firstname, staff.lastname');
		$this->db->from(db_prefix() . 'voip_settings');
		$this->db->join(db_prefix() . 'staff as staff', db_prefix() . 'voip_settings.staffid = staff.staffid', 'left');
		return $this->db->get()->result_array();
    }

    public function add_voip_setting($data)
    {
        $this->db->insert(db_prefix() . 'voip_settings', $data);
        return $this->db->insert_id();
    }

    public function get_voip_setting_by_id($id)
    {
        return $this->db->get_where(db_prefix() . 'voip_settings', ['id' => $id])->row_array();
    }

    public function update_voip_setting($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . 'voip_settings', $data);
    }

    public function delete_voip_setting($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . 'voip_settings');
    }
}
