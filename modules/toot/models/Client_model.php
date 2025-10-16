<?php

use app\services\utilities\Arr;

defined('BASEPATH') or exit('No direct script access allowed');

class Client_model extends App_Model
{
    private $contact_columns;
    private $current_branch_id;

    public function __construct()
    {
        parent::__construct();

        $this->contact_columns = hooks()->apply_filters('contact_columns', ['firstname', 'lastname', 'email', 'phonenumber', 'title', 'password', 'send_set_password_email', 'donotsendwelcomeemail', 'permissions', 'direction', 'invoice_emails', 'estimate_emails', 'credit_note_emails', 'contract_emails', 'task_emails', 'project_emails', 'ticket_emails', 'is_primary']);

        $this->load->model(['client_vault_entries_model', 'client_groups_model', 'statement_model']);
		
		$this->current_branch_id = $this->get_logged_in_staff_branch_id();
    }

    /**
     * Get client object based on passed clientid if not passed clientid return array of all clients
     * @param  mixed $id    client id
     * @param  array  $where
     * @return mixed
     */

    public function get_master_data()
    {
        $tables = [
            'appointment_type' => 'tblappointment_type',
            'enquiry_type' => 'tblenquiry_type',
            'patient_response' => 'tblpatient_response',
            'speaking_language' => 'tblspeaking_language',
            'patient_priority' => 'tblpatient_priority',
            'branch' => 'tblcustomers_groups',
            'assign_doctor' => 'tblstaff',
            'slots' => 'tblslots',
            'treatment' => 'tbltreatment',
            'consultation_fee' => 'tblconsultation_fee',
            'patient_source' => 'tblleads_sources',
        ];

        $result = [];
        foreach ($tables as $key => $table) {
            if($table == "tblstaff"){
                $this->db->select('roleid');
				$this->db->from(db_prefix() . 'roles');
				$this->db->where('LOWER(name)', 'doctor');
				$query = $this->db->get();
				if ($query->num_rows() > 0) {
					$role = $query->row()->roleid;
					$this->db->where('role', $role);
				} else {
					// Optional: Handle case when 'Doctor' role is not found
					$this->db->where('role', 0); // or some fallback
				}
            }
            $result[$key] = $this->db->table_exists($table) ? $this->db->get($table)->result_array() : [];
        }
        return $result;
    }
    public function get($id = '', $where = [])
    {
        $this->db->select('c.*, co.*, ct.*, new.*, cg.groupid' ); // c = clients, co = countries, ct = contacts

        $this->db->from(db_prefix() . 'clients c');

        $this->db->join(db_prefix() . 'countries co', 'co.country_id = c.country', 'left');
        $this->db->join(db_prefix() . 'clients_new_fields new', 'new.userid = c.userid', 'left');
        $this->db->join(db_prefix() . 'contacts ct', 'ct.userid = c.userid AND ct.is_primary = 1', 'left');
        $this->db->join(db_prefix() . 'customer_groups cg', 'cg.customer_id = c.userid', 'left');

        if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }

        if (is_numeric($id)) {
            $this->db->select('c.*');
            $this->db->where('c.userid', $id);
            $client = $this->db->get()->row();

            if ($client && get_option('company_requires_vat_number_field') == 0) {
                $client->vat = null;
            }

            $GLOBALS['client'] = $client;

            return $client;
        }

        $this->db->order_by('c.company', 'asc');

        return $this->db->get()->result_array();
    }


    /**
     * Get customers contacts
     * @param  mixed $customer_id
     * @param  array $where       perform where query
     * @param  array $whereIn     perform whereIn query
     * @return array
     */
    public function get_contacts($customer_id = '', $where = ['active' => 1], $whereIn = [])
    {
        $this->db->where($where);
        if ($customer_id != '') {
            $this->db->where('userid', $customer_id);
        }

        foreach ($whereIn as $key => $values) {
            if (is_string($key) && is_array($values)) {
                $this->db->where_in($key, $values);
            }
        }

        $this->db->order_by('is_primary', 'DESC');

        return $this->db->get(db_prefix() . 'contacts')->result_array();
    }

    /**
     * Get single contacts
     * @param  mixed $id contact id
     * @return object
     */
    public function get_contact($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'contacts')->row();
    }

    /**
     * Get contact by given email
     *
     * @since 2.8.0
     *
     * @param  string $email
     *
     * @return \strClass|null
     */
    public function get_contact_by_email($email)
    {
        $this->db->where('email', $email);
        $this->db->limit(1);

        return $this->db->get('contacts')->row();
    }

    /**
     * @param array $_POST data
     * @param withContact
     *
     * @return integer Insert ID
     *
     * Add new client to database
     */
    public function add($data, $withContact = false)
    {
        $contact_data = [];
        // From Lead Convert to client
        if (isset($data['send_set_password_email'])) {
            $contact_data['send_set_password_email'] = true;
        }

        if (isset($data['donotsendwelcomeemail'])) {
            $contact_data['donotsendwelcomeemail'] = true;
        }

        $data = $this->check_zero_columns($data);

        $data = hooks()->apply_filters('before_client_added', $data);

        foreach ($this->contact_columns as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            $contact_data[$field] = $data[$field];

            // Phonenumber is also used for the company profile
            if ($field != 'phonenumber') {
                unset($data[$field]);
            }
        }

        $groups_in     = Arr::pull($data, 'groups_in') ?? [];
        $custom_fields = Arr::pull($data, 'custom_fields') ?? [];

        // From customer profile register
        if (isset($data['contact_phonenumber'])) {
            $contact_data['phonenumber'] = $data['contact_phonenumber'];
            unset($data['contact_phonenumber']);
        }

        $this->db->insert(db_prefix() . 'clients', array_merge($data, [
            'datecreated' => date('Y-m-d H:i:s'),
            'addedfrom'   => is_staff_logged_in() ? get_staff_user_id() : 0,
        ]));

        $client_id = $this->db->insert_id();

        if ($client_id) {
            if (count($custom_fields) > 0) {
                $_custom_fields = $custom_fields;
                // Possible request from the register area with 2 types of custom fields for contact and for comapny/customer
                if (count($custom_fields) == 2) {
                    unset($custom_fields);
                    $custom_fields['customers']                = $_custom_fields['customers'];
                    $contact_data['custom_fields']['contacts'] = $_custom_fields['contacts'];
                } elseif (count($custom_fields) == 1) {
                    if (isset($_custom_fields['contacts'])) {
                        $contact_data['custom_fields']['contacts'] = $_custom_fields['contacts'];
                        unset($custom_fields);
                    }
                }

                handle_custom_fields_post($client_id, $custom_fields);
            }

            /**
             * Used in Import, Lead Convert, Register
             */
            if ($withContact == true) {
                $contact_id = $this->add_contact($contact_data, $client_id, $withContact);
            }

            foreach ($groups_in as $group) {
                $this->db->insert('customer_groups', [
                        'customer_id' => $client_id,
                        'groupid'     => $group,
                    ]);
            }

            $log = 'ID: ' . $client_id;

            if ($log == '' && isset($contact_id)) {
                $log = get_contact_full_name($contact_id);
            }

            $isStaff = null;

            if (!is_client_logged_in() && is_staff_logged_in()) {
                $log .= ', From Staff: ' . get_staff_user_id();
                $isStaff = get_staff_user_id();
            }

            do_action_deprecated('after_client_added', [$client_id], '2.9.4', 'after_client_created');

            hooks()->do_action('after_client_created', [
                'id'            => $client_id,
                'data'          => $data,
                'contact_data'  => $contact_data,
                'custom_fields' => $custom_fields,
                'groups_in'     => $groups_in,
                'with_contact'  => $withContact,
            ]);

            log_activity('New Client Created [' . $log . ']', $isStaff);
        }

        return $client_id;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update client informations
     */
    public function update($data, $id, $client_request = false)
    {
        $updated = false;
        $data    = $this->check_zero_columns($data);

        $data = hooks()->apply_filters('before_client_updated', $data, $id);

        $update_all_other_transactions = (bool) Arr::pull($data, 'update_all_other_transactions');
        $update_credit_notes           = (bool) Arr::pull($data, 'update_credit_notes');
        $custom_fields                 = Arr::pull($data, 'custom_fields') ?? [];
        $groups_in                     = Arr::pull($data, 'groups_in') ?? false;

        if (handle_custom_fields_post($id, $custom_fields)) {
            $updated = true;
        }

        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'clients', $data);

        if ($this->db->affected_rows() > 0) {
            $updated = true;
        }

        if ($update_all_other_transactions || $update_credit_notes) {
            $transactions_update = [
                'billing_street'   => $data['billing_street'],
                'billing_city'     => $data['billing_city'],
                'billing_state'    => $data['billing_state'],
                'billing_zip'      => $data['billing_zip'],
                'billing_country'  => $data['billing_country'],
                'shipping_street'  => $data['shipping_street'],
                'shipping_city'    => $data['shipping_city'],
                'shipping_state'   => $data['shipping_state'],
                'shipping_zip'     => $data['shipping_zip'],
                'shipping_country' => $data['shipping_country'],
            ];

            if ($update_all_other_transactions) {
                // Update all invoices except paid ones.
                $this->db->where('clientid', $id)
                ->where('status !=', 2)
                ->update('invoices', $transactions_update);

                if ($this->db->affected_rows() > 0) {
                    $updated = true;
                }

                // Update all estimates
                $this->db->where('clientid', $id)
                    ->update('estimates', $transactions_update);
                if ($this->db->affected_rows() > 0) {
                    $updated = true;
                }
            }

            if ($update_credit_notes) {
                $this->db->where('clientid', $id)
                    ->where('status !=', 2)
                    ->update('creditnotes', $transactions_update);

                if ($this->db->affected_rows() > 0) {
                    $updated = true;
                }
            }
        }

        if ($this->client_groups_model->sync_customer_groups($id, $groups_in)) {
            $updated = true;
        }

        do_action_deprecated('after_client_updated', [$id], '2.9.4', 'client_updated');

        hooks()->do_action('client_updated', [
            'id'                            => $id,
            'data'                          => $data,
            'update_all_other_transactions' => $update_all_other_transactions,
            'update_credit_notes'           => $update_credit_notes,
            'custom_fields'                 => $custom_fields,
            'groups_in'                     => $groups_in,
            'updated'                       => &$updated,
        ]);

        if ($updated) {
            log_activity('Customer Info Updated [ID: ' . $id . ']');
        }

        return $updated;
    }

    /**
     * Update contact data
     * @param  array  $data           $_POST data
     * @param  mixed  $id             contact id
     * @param  boolean $client_request is request from customers area
     * @return mixed
     */
    public function update_contact($data, $id, $client_request = false)
    {
        $affectedRows = 0;
        $contact      = $this->get_contact($id);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password']             = app_hash_password($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }

        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;
        $set_password_email_sent = false;

        $permissions        = isset($data['permissions']) ? $data['permissions'] : [];
        $data['is_primary'] = isset($data['is_primary']) ? 1 : 0;

        // Contact cant change if is primary or not
        if ($client_request == true) {
            unset($data['is_primary']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if ($client_request == false) {
            $data['invoice_emails']     = isset($data['invoice_emails']) ? 1 :0;
            $data['estimate_emails']    = isset($data['estimate_emails']) ? 1 :0;
            $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 :0;
            $data['contract_emails']    = isset($data['contract_emails']) ? 1 :0;
            $data['task_emails']        = isset($data['task_emails']) ? 1 :0;
            $data['project_emails']     = isset($data['project_emails']) ? 1 :0;
            $data['ticket_emails']      = isset($data['ticket_emails']) ? 1 :0;
        }

        $data = hooks()->apply_filters('before_update_contact', $data, $id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if (isset($data['is_primary']) && $data['is_primary'] == 1) {
                $this->db->where('userid', $contact->userid);
                $this->db->where('id !=', $id);
                $this->db->update(db_prefix() . 'contacts', [
                    'is_primary' => 0,
                ]);
            }
        }

        if ($client_request == false) {
            $customer_permissions = $this->roles_model->get_contact_permissions($id);
            if (sizeof($customer_permissions) > 0) {
                foreach ($customer_permissions as $customer_permission) {
                    if (!in_array($customer_permission['permission_id'], $permissions)) {
                        $this->db->where('userid', $id);
                        $this->db->where('permission_id', $customer_permission['permission_id']);
                        $this->db->delete(db_prefix() . 'contact_permissions');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
                foreach ($permissions as $permission) {
                    $this->db->where('userid', $id);
                    $this->db->where('permission_id', $permission);
                    $_exists = $this->db->get(db_prefix() . 'contact_permissions')->row();
                    if (!$_exists) {
                        $this->db->insert(db_prefix() . 'contact_permissions', [
                            'userid'        => $id,
                            'permission_id' => $permission,
                        ]);
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            } else {
                foreach ($permissions as $permission) {
                    $this->db->insert(db_prefix() . 'contact_permissions', [
                        'userid'        => $id,
                        'permission_id' => $permission,
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if ($send_set_password_email) {
                $set_password_email_sent = $this->authentication_model->set_password_email($data['email'], 0);
            }
        }

        if (($client_request == true) && $send_set_password_email) {
            $set_password_email_sent = $this->authentication_model->set_password_email($data['email'], 0);
        }

        if ($affectedRows > 0) {
            hooks()->do_action('contact_updated', $id, $data);
        }

        if ($affectedRows > 0 && !$set_password_email_sent) {
            log_activity('Contact Updated [ID: ' . $id . ']');

            return true;
        } elseif ($affectedRows > 0 && $set_password_email_sent) {
            return [
                'set_password_email_sent_and_profile_updated' => true,
            ];
        } elseif ($affectedRows == 0 && $set_password_email_sent) {
            return [
                'set_password_email_sent' => true,
            ];
        }

        return false;
    }

    /**
     * Add new contact
     * @param array  $data               $_POST data
     * @param mixed  $customer_id        customer id
     * @param boolean $not_manual_request is manual from admin area customer profile or register, convert to lead
     */
    public function add_contact($data, $customer_id, $not_manual_request = false)
    {
        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }

        $data['email_verified_at'] = date('Y-m-d H:i:s');

        $send_welcome_email = true;

        if (isset($data['donotsendwelcomeemail'])) {
            $send_welcome_email = false;
        }

        if (defined('CONTACT_REGISTERING')) {
            $send_welcome_email = true;

            // Do not send welcome email if confirmation for registration is enabled
            if (get_option('customers_register_require_confirmation') == '1') {
                $send_welcome_email = false;
            }

            // If client register set this contact as primary
            $data['is_primary'] = 1;

            if (is_email_verification_enabled() && !empty($data['email'])) {
                // Verification is required on register
                $data['email_verified_at']      = null;
                $data['email_verification_key'] = app_generate_hash();
            }
        }

        if (isset($data['is_primary'])) {
            $data['is_primary'] = 1;
            $this->db->where('userid', $customer_id);
            $this->db->update(db_prefix() . 'contacts', [
                'is_primary' => 0,
            ]);
        } else {
            $data['is_primary'] = 0;
        }

        $password_before_hash = '';
        $data['userid']       = $customer_id;
        if (isset($data['password'])) {
            $password_before_hash = $data['password'];
            $data['password']     = app_hash_password($data['password']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');

        if (!$not_manual_request) {
            $data['invoice_emails']     = isset($data['invoice_emails']) ? 1 :0;
            $data['estimate_emails']    = isset($data['estimate_emails']) ? 1 :0;
            $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 :0;
            $data['contract_emails']    = isset($data['contract_emails']) ? 1 :0;
            $data['task_emails']        = isset($data['task_emails']) ? 1 :0;
            $data['project_emails']     = isset($data['project_emails']) ? 1 :0;
            $data['ticket_emails']      = isset($data['ticket_emails']) ? 1 :0;
        }

        $data['email'] = trim($data['email']);

        $data = hooks()->apply_filters('before_create_contact', $data);

        $this->db->insert(db_prefix() . 'contacts', $data);
        $contact_id = $this->db->insert_id();

        if ($contact_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($contact_id, $custom_fields);
            }
            // request from admin area
            if (!isset($permissions) && $not_manual_request == false) {
                $permissions = [];
            } elseif ($not_manual_request == true) {
                $permissions         = [];
                $_permissions        = get_contact_permissions();
                $default_permissions = @unserialize(get_option('default_contact_permissions'));
                if (is_array($default_permissions)) {
                    foreach ($_permissions as $permission) {
                        if (in_array($permission['id'], $default_permissions)) {
                            array_push($permissions, $permission['id']);
                        }
                    }
                }
            }

            if ($not_manual_request == true) {
                // update all email notifications to 0
                $this->db->where('id', $contact_id);
                $this->db->update(db_prefix() . 'contacts', [
                    'invoice_emails'     => 0,
                    'estimate_emails'    => 0,
                    'credit_note_emails' => 0,
                    'contract_emails'    => 0,
                    'task_emails'        => 0,
                    'project_emails'     => 0,
                    'ticket_emails'      => 0,
                ]);
            }
            foreach ($permissions as $permission) {
                $this->db->insert(db_prefix() . 'contact_permissions', [
                    'userid'        => $contact_id,
                    'permission_id' => $permission,
                ]);

                // Auto set email notifications based on permissions
                if ($not_manual_request == true) {
                    if ($permission == 6) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['project_emails' => 1, 'task_emails' => 1]);
                    } elseif ($permission == 3) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['contract_emails' => 1]);
                    } elseif ($permission == 2) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['estimate_emails' => 1]);
                    } elseif ($permission == 1) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['invoice_emails' => 1, 'credit_note_emails' => 1]);
                    } elseif ($permission == 5) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contacts', ['ticket_emails' => 1]);
                    }
                }
            }

            if ($send_welcome_email == true && !empty($data['email'])) {
                send_mail_template(
                    'customer_created_welcome_mail',
                    $data['email'],
                    $data['userid'],
                    $contact_id,
                    $password_before_hash
                );
            }

            if ($send_set_password_email) {
                $this->authentication_model->set_password_email($data['email'], 0);
            }

            if (defined('CONTACT_REGISTERING')) {
                $this->send_verification_email($contact_id);
            } else {
                // User already verified because is added from admin area, try to transfer any tickets
                $this->load->model('tickets_model');
                $this->tickets_model->transfer_email_tickets_to_contact($data['email'], $contact_id);
            }

            log_activity('Contact Created [ID: ' . $contact_id . ']');

            hooks()->do_action('contact_created', $contact_id);

            return $contact_id;
        }

        return false;
    }

    /**
     * Add new contact via customers area
     *
     * @param array  $data
     * @param mixed  $customer_id
     */
    public function add_contact_via_customers_area($data, $customer_id)
    {
        $send_welcome_email      = isset($data['donotsendwelcomeemail']) && $data['donotsendwelcomeemail'] ? false : true;
        $send_set_password_email = isset($data['send_set_password_email']) && $data['send_set_password_email'] ? true : false;
        $custom_fields           = $data['custom_fields'];
        unset($data['custom_fields']);

        if (!is_email_verification_enabled()) {
            $data['email_verified_at'] = date('Y-m-d H:i:s');
        } elseif (is_email_verification_enabled() && !empty($data['email'])) {
            // Verification is required on register
            $data['email_verified_at']      = null;
            $data['email_verification_key'] = app_generate_hash();
        }

        $password_before_hash = $data['password'];

        $data = array_merge($data, [
            'datecreated' => date('Y-m-d H:i:s'),
            'userid'      => $customer_id,
            'password'    => app_hash_password(isset($data['password']) ? $data['password'] : time()),
        ]);

        $data = hooks()->apply_filters('before_create_contact', $data);
        $this->db->insert(db_prefix() . 'contacts', $data);

        $contact_id = $this->db->insert_id();

        if ($contact_id) {
            handle_custom_fields_post($contact_id, $custom_fields);

            // Apply default permissions
            $default_permissions = @unserialize(get_option('default_contact_permissions'));

            if (is_array($default_permissions)) {
                foreach (get_contact_permissions() as $permission) {
                    if (in_array($permission['id'], $default_permissions)) {
                        $this->db->insert(db_prefix() . 'contact_permissions', [
                            'userid'        => $contact_id,
                            'permission_id' => $permission['id'],
                        ]);
                    }
                }
            }

            if ($send_welcome_email === true) {
                send_mail_template(
                    'customer_created_welcome_mail',
                    $data['email'],
                    $customer_id,
                    $contact_id,
                    $password_before_hash
                );
            }

            if ($send_set_password_email === true) {
                $this->authentication_model->set_password_email($data['email'], 0);
            }

            log_activity('Contact Created [ID: ' . $contact_id . ']');
            hooks()->do_action('contact_created', $contact_id);

            return $contact_id;
        }

        return false;
    }

    /**
     * Used to update company details from customers area
     * @param  array $data $_POST data
     * @param  mixed $id
     * @return boolean
     */
    public function update_company_details($data, $id)
    {
        $affectedRows = 0;
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }
        if (isset($data['billing_country']) && $data['billing_country'] == '') {
            $data['billing_country'] = 0;
        }
        if (isset($data['shipping_country']) && $data['shipping_country'] == '') {
            $data['shipping_country'] = 0;
        }

        // From v.1.9.4 these fields are textareas
        $data['address'] = trim($data['address']);
        $data['address'] = nl2br($data['address']);
        if (isset($data['billing_street'])) {
            $data['billing_street'] = trim($data['billing_street']);
            $data['billing_street'] = nl2br($data['billing_street']);
        }
        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }

        $data = hooks()->apply_filters('customer_update_company_info', $data, $id);

        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'clients', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        if ($affectedRows > 0) {
            hooks()->do_action('customer_updated_company_info', $id);
            log_activity('Customer Info Updated From Clients Area [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Get customer staff members that are added as customer admins
     * @param  mixed $id customer id
     * @return array
     */
    public function get_admins($id)
    {
        $this->db->where('customer_id', $id);

        return $this->db->get(db_prefix() . 'customer_admins')->result_array();
    }

    /**
     * Get unique staff id's of customer admins
     * @return array
     */
    public function get_customers_admin_unique_ids()
    {
        return $this->db->query('SELECT DISTINCT(staff_id) FROM ' . db_prefix() . 'customer_admins')->result_array();
    }

    /**
     * Assign staff members as admin to customers
     * @param  array $data $_POST data
     * @param  mixed $id   customer id
     * @return boolean
     */
    public function assign_admins($data, $id)
    {
        $affectedRows = 0;

        if (count($data) == 0) {
            $this->db->where('customer_id', $id);
            $this->db->delete(db_prefix() . 'customer_admins');
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        } else {
            $current_admins     = $this->get_admins($id);
            $current_admins_ids = [];
            foreach ($current_admins as $c_admin) {
                array_push($current_admins_ids, $c_admin['staff_id']);
            }
            foreach ($current_admins_ids as $c_admin_id) {
                if (!in_array($c_admin_id, $data['customer_admins'])) {
                    $this->db->where('staff_id', $c_admin_id);
                    $this->db->where('customer_id', $id);
                    $this->db->delete(db_prefix() . 'customer_admins');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            foreach ($data['customer_admins'] as $n_admin_id) {
                if (total_rows(db_prefix() . 'customer_admins', [
                    'customer_id' => $id,
                    'staff_id' => $n_admin_id,
                ]) == 0) {
                    $this->db->insert(db_prefix() . 'customer_admins', [
                        'customer_id'   => $id,
                        'staff_id'      => $n_admin_id,
                        'date_assigned' => date('Y-m-d H:i:s'),
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete client, also deleting rows from, dismissed client announcements, ticket replies, tickets, autologin, user notes
     */
    public function delete($id)
    {
        $affectedRows = 0;

        if (!is_gdpr() && is_reference_in_table('clientid', db_prefix() . 'invoices', $id)) {
            return [
                'referenced' => true,
            ];
        }

        if (!is_gdpr() && is_reference_in_table('clientid', db_prefix() . 'estimates', $id)) {
            return [
                'referenced' => true,
            ];
        }

        if (!is_gdpr() && is_reference_in_table('clientid', db_prefix() . 'creditnotes', $id)) {
            return [
                'referenced' => true,
            ];
        }

        hooks()->do_action('before_client_deleted', $id);

        $last_activity = get_last_system_activity_id();
        $company       = get_company_name($id);

        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'clients');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            // Delete all user contacts
            $this->db->where('userid', $id);
            $contacts = $this->db->get(db_prefix() . 'contacts')->result_array();
            foreach ($contacts as $contact) {
                $this->delete_contact($contact['id']);
            }

            // Delete all tickets start here
            $this->db->where('userid', $id);
            $tickets = $this->db->get(db_prefix() . 'tickets')->result_array();
            $this->load->model('tickets_model');
            foreach ($tickets as $ticket) {
                $this->tickets_model->delete($ticket['ticketid']);
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'customer');
            $this->db->delete(db_prefix() . 'notes');

            if (is_gdpr() && get_option('gdpr_on_forgotten_remove_invoices_credit_notes') == '1') {
                $this->load->model('invoices_model');
                $this->db->where('clientid', $id);
                $invoices = $this->db->get(db_prefix() . 'invoices')->result_array();
                foreach ($invoices as $invoice) {
                    $this->invoices_model->delete($invoice['id'], true);
                }

                $this->load->model('credit_notes_model');
                $this->db->where('clientid', $id);
                $credit_notes = $this->db->get(db_prefix() . 'creditnotes')->result_array();
                foreach ($credit_notes as $credit_note) {
                    $this->credit_notes_model->delete($credit_note['id'], true);
                }
            } elseif (is_gdpr()) {
                $this->db->where('clientid', $id);
                $this->db->update(db_prefix() . 'invoices', ['deleted_customer_name' => $company]);

                $this->db->where('clientid', $id);
                $this->db->update(db_prefix() . 'creditnotes', ['deleted_customer_name' => $company]);
            }

            $this->db->where('clientid', $id);
            $this->db->update(db_prefix() . 'creditnotes', [
                'clientid'   => 0,
                'project_id' => 0,
            ]);

            $this->db->where('clientid', $id);
            $this->db->update(db_prefix() . 'invoices', [
                'clientid'                 => 0,
                'recurring'                => 0,
                'recurring_type'           => null,
                'custom_recurring'         => 0,
                'cycles'                   => 0,
                'last_recurring_date'      => null,
                'project_id'               => 0,
                'subscription_id'          => 0,
                'cancel_overdue_reminders' => 1,
                'last_overdue_reminder'    => null,
                'last_due_reminder'        => null,
            ]);

            if (is_gdpr() && get_option('gdpr_on_forgotten_remove_estimates') == '1') {
                $this->load->model('estimates_model');
                $this->db->where('clientid', $id);
                $estimates = $this->db->get(db_prefix() . 'estimates')->result_array();
                foreach ($estimates as $estimate) {
                    $this->estimates_model->delete($estimate['id'], true);
                }
            } elseif (is_gdpr()) {
                $this->db->where('clientid', $id);
                $this->db->update(db_prefix() . 'estimates', ['deleted_customer_name' => $company]);
            }

            $this->db->where('clientid', $id);
            $this->db->update(db_prefix() . 'estimates', [
                'clientid'           => 0,
                'project_id'         => 0,
                'is_expiry_notified' => 1,
            ]);

            $this->load->model('subscriptions_model');
            $this->db->where('clientid', $id);
            $subscriptions = $this->db->get(db_prefix() . 'subscriptions')->result_array();
            foreach ($subscriptions as $subscription) {
                $this->subscriptions_model->delete($subscription['id'], true);
            }
            // Get all client contracts
            $this->load->model('contracts_model');
            $this->db->where('client', $id);
            $contracts = $this->db->get(db_prefix() . 'contracts')->result_array();
            foreach ($contracts as $contract) {
                $this->contracts_model->delete($contract['id']);
            }
            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'customers');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            // Get customer related tasks
            $this->db->where('rel_type', 'customer');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();

            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id'], false);
            }

            $this->db->where('rel_type', 'customer');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'reminders');

            $this->db->where('customer_id', $id);
            $this->db->delete(db_prefix() . 'customer_admins');

            $this->db->where('customer_id', $id);
            $this->db->delete(db_prefix() . 'vault');

            $this->db->where('customer_id', $id);
            $this->db->delete(db_prefix() . 'customer_groups');

            $this->load->model('proposals_model');
            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'customer');
            $proposals = $this->db->get(db_prefix() . 'proposals')->result_array();
            foreach ($proposals as $proposal) {
                $this->proposals_model->delete($proposal['id']);
            }
            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'customer');
            $attachments = $this->db->get(db_prefix() . 'files')->result_array();
            foreach ($attachments as $attachment) {
                $this->delete_attachment($attachment['id']);
            }

            $this->db->where('clientid', $id);
            $expenses = $this->db->get(db_prefix() . 'expenses')->result_array();

            $this->load->model('expenses_model');
            foreach ($expenses as $expense) {
                $this->expenses_model->delete($expense['id'], true);
            }

            $this->db->where('client_id', $id);
            $this->db->delete(db_prefix() . 'user_meta');

            $this->db->where('client_id', $id);
            $this->db->update(db_prefix() . 'leads', ['client_id' => 0]);

            // Delete all projects
            $this->load->model('projects_model');
            $this->db->where('clientid', $id);
            $projects = $this->db->get(db_prefix() . 'projects')->result_array();
            foreach ($projects as $project) {
                $this->projects_model->delete($project['id']);
            }
        }
        if ($affectedRows > 0) {
            hooks()->do_action('after_client_deleted', $id);

            // Delete activity log caused by delete customer function
            if ($last_activity) {
                $this->db->where('id >', $last_activity->id);
                $this->db->delete(db_prefix() . 'activity_log');
            }

            log_activity('Client Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete customer contact
     * @param  mixed $id contact id
     * @return boolean
     */
    public function delete_contact($id)
    {
        hooks()->do_action('before_delete_contact', $id);

        $this->db->where('id', $id);
        $result      = $this->db->get(db_prefix() . 'contacts')->row();
        $customer_id = $result->userid;

        $last_activity = get_last_system_activity_id();

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'contacts');

        if ($this->db->affected_rows() > 0) {
            if (is_dir(get_upload_path_by_type('contact_profile_images') . $id)) {
                delete_dir(get_upload_path_by_type('contact_profile_images') . $id);
            }

            $this->db->where('contact_id', $id);
            $this->db->delete(db_prefix() . 'consents');

            $this->db->where('contact_id', $id);
            $this->db->delete(db_prefix() . 'shared_customer_files');

            $this->db->where('userid', $id);
            $this->db->where('staff', 0);
            $this->db->delete(db_prefix() . 'dismissed_announcements');

            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'contacts');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('userid', $id);
            $this->db->delete(db_prefix() . 'contact_permissions');

            $this->db->where('user_id', $id);
            $this->db->where('staff', 0);
            $this->db->delete(db_prefix() . 'user_auto_login');

            $this->db->select('ticketid');
            $this->db->where('contactid', $id);
            $this->db->where('userid', $customer_id);
            $tickets = $this->db->get(db_prefix() . 'tickets')->result_array();

            $this->load->model('tickets_model');
            foreach ($tickets as $ticket) {
                $this->tickets_model->delete($ticket['ticketid']);
            }

            $this->load->model('tasks_model');

            $this->db->where('addedfrom', $id);
            $this->db->where('is_added_from_contact', 1);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();

            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id'], false);
            }

            // Added from contact in customer profile
            $this->db->where('contact_id', $id);
            $this->db->where('rel_type', 'customer');
            $attachments = $this->db->get(db_prefix() . 'files')->result_array();

            foreach ($attachments as $attachment) {
                $this->delete_attachment($attachment['id']);
            }

            // Remove contact files uploaded to tasks
            $this->db->where('rel_type', 'task');
            $this->db->where('contact_id', $id);
            $filesUploadedFromContactToTasks = $this->db->get(db_prefix() . 'files')->result_array();

            foreach ($filesUploadedFromContactToTasks as $file) {
                $this->tasks_model->remove_task_attachment($file['id']);
            }

            $this->db->where('contact_id', $id);
            $tasksComments = $this->db->get(db_prefix() . 'task_comments')->result_array();
            foreach ($tasksComments as $comment) {
                $this->tasks_model->remove_comment($comment['id'], true);
            }

            $this->load->model('projects_model');

            $this->db->where('contact_id', $id);
            $files = $this->db->get(db_prefix() . 'project_files')->result_array();
            foreach ($files as $file) {
                $this->projects_model->remove_file($file['id'], false);
            }

            $this->db->where('contact_id', $id);
            $discussions = $this->db->get(db_prefix() . 'projectdiscussions')->result_array();
            foreach ($discussions as $discussion) {
                $this->projects_model->delete_discussion($discussion['id'], false);
            }

            $this->db->where('contact_id', $id);
            $discussionsComments = $this->db->get(db_prefix() . 'projectdiscussioncomments')->result_array();
            foreach ($discussionsComments as $comment) {
                $this->projects_model->delete_discussion_comment($comment['id'], false);
            }

            $this->db->where('contact_id', $id);
            $this->db->delete(db_prefix() . 'user_meta');

            $this->db->where('(email="' . $result->email . '" OR bcc LIKE "%' . $result->email . '%" OR cc LIKE "%' . $result->email . '%")');
            $this->db->delete(db_prefix() . 'mail_queue');
           
            if (is_gdpr()) {
                if(table_exists('listemails')) {
                    $this->db->where('email', $result->email);
                    $this->db->delete(db_prefix() . 'listemails');
                }
                
                if (!empty($result->last_ip)) {
                    $this->db->where('ip', $result->last_ip);
                    $this->db->delete(db_prefix() . 'knowedge_base_article_feedback');
                }

                $this->db->where('email', $result->email);
                $this->db->delete(db_prefix() . 'tickets_pipe_log');

                $this->db->where('email', $result->email);
                $this->db->delete(db_prefix() . 'tracked_mails');

                $this->db->where('contact_id', $id);
                $this->db->delete(db_prefix() . 'project_activity');

                $this->db->where('(additional_data LIKE "%' . $result->email . '%" OR full_name LIKE "%' . $result->firstname . ' ' . $result->lastname . '%")');
                $this->db->where('additional_data != "" AND additional_data IS NOT NULL');
                $this->db->delete(db_prefix() . 'sales_activity');

                $contactActivityQuery = false;
                if (!empty($result->email)) {
                    $this->db->or_like('description', $result->email);
                    $contactActivityQuery = true;
                }
                if (!empty($result->firstname)) {
                    $this->db->or_like('description', $result->firstname);
                    $contactActivityQuery = true;
                }
                if (!empty($result->lastname)) {
                    $this->db->or_like('description', $result->lastname);
                    $contactActivityQuery = true;
                }

                if (!empty($result->phonenumber)) {
                    $this->db->or_like('description', $result->phonenumber);
                    $contactActivityQuery = true;
                }

                if (!empty($result->last_ip)) {
                    $this->db->or_like('description', $result->last_ip);
                    $contactActivityQuery = true;
                }

                if ($contactActivityQuery) {
                    $this->db->delete(db_prefix() . 'activity_log');
                }
            }

            // Delete activity log caused by delete contact function
            if ($last_activity) {
                $this->db->where('id >', $last_activity->id);
                $this->db->delete(db_prefix() . 'activity_log');
            }

            hooks()->do_action('contact_deleted', $id, $result);

            return true;
        }

        return false;
    }

    /**
     * Get customer default currency
     * @param  mixed $id customer id
     * @return mixed
     */
    public function get_customer_default_currency($id)
    {
        $this->db->select('default_currency');
        $this->db->where('userid', $id);
        $result = $this->db->get(db_prefix() . 'clients')->row();
        if ($result) {
            return $result->default_currency;
        }

        return false;
    }

    /**
     *  Get customer billing details
     * @param   mixed $id   customer id
     * @return  array
     */
    public function get_customer_billing_and_shipping_details($id)
    {
        $this->db->select('billing_street,billing_city,billing_state,billing_zip,billing_country,shipping_street,shipping_city,shipping_state,shipping_zip,shipping_country');
        $this->db->from(db_prefix() . 'clients');
        $this->db->where('userid', $id);

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $result[0]['billing_street']  = clear_textarea_breaks($result[0]['billing_street']);
            $result[0]['shipping_street'] = clear_textarea_breaks($result[0]['shipping_street']);
        }

        return $result;
    }

    /**
     * Get customer files uploaded in the customer profile
     * @param  mixed $id    customer id
     * @param  array  $where perform where
     * @return array
     */
    public function get_customer_files($id, $where = [])
    {
        $this->db->where($where);
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'customer');
        $this->db->order_by('dateadded', 'desc');

        return $this->db->get(db_prefix() . 'files')->result_array();
    }

    /**
     * Delete customer attachment uploaded from the customer profile
     * @param  mixed $id attachment id
     * @return boolean
     */
    public function delete_attachment($id)
    {
        $this->db->where('id', $id);
        $attachment = $this->db->get(db_prefix() . 'files')->row();
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                $relPath  = get_upload_path_by_type('customer') . $attachment->rel_id . '/';
                $fullPath = $relPath . $attachment->file_name;
                unlink($fullPath);
                $fname     = pathinfo($fullPath, PATHINFO_FILENAME);
                $fext      = pathinfo($fullPath, PATHINFO_EXTENSION);
                $thumbPath = $relPath . $fname . '_thumb.' . $fext;
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                $this->db->where('file_id', $id);
                $this->db->delete(db_prefix() . 'shared_customer_files');
                log_activity('Customer Attachment Deleted [ID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('customer') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('customer') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    delete_dir(get_upload_path_by_type('customer') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update contact status Active/Inactive
     */
    public function change_contact_status($id, $status)
    {
        $status = hooks()->apply_filters('change_contact_status', $status, $id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', [
            'active' => $status,
        ]);
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('contact_status_changed', [
                'id'     => $id,
                'status' => $status,
            ]);

            log_activity('Contact Status Changed [ContactID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update client status Active/Inactive
     */
    public function change_client_status($id, $status)
    {
        $this->db->where('userid', $id);
        $this->db->update('clients', [
            'active' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('client_status_changed', [
                'id'     => $id,
                'status' => $status,
            ]);

            log_activity('Customer Status Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }

    /**
     * Change contact password, used from client area
     * @param  mixed $id          contact id to change password
     * @param  string $oldPassword old password to verify
     * @param  string $newPassword new password
     * @return boolean
     */
    public function change_contact_password($id, $oldPassword, $newPassword)
    {
        // Get current password
        $this->db->where('id', $id);
        $client = $this->db->get(db_prefix() . 'contacts')->row();

        if (!app_hasher()->CheckPassword($oldPassword, $client->password)) {
            return [
                'old_password_not_match' => true,
            ];
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', [
            'last_password_change' => date('Y-m-d H:i:s'),
            'password'             => app_hash_password($newPassword),
        ]);

        if ($this->db->affected_rows() > 0) {
            log_activity('Contact Password Changed [ContactID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Get customer groups where customer belongs
     * @param  mixed $id customer id
     * @return array
     */
    public function get_customer_groups($id)
    {
        return $this->client_groups_model->get_customer_groups($id);
    }

    /**
     * Get all customer groups
     * @param  string $id
     * @return mixed
     */
    public function get_groups($id = '')
    {
        return $this->client_groups_model->get_groups($id);
    }

    /**
     * Delete customer groups
     * @param  mixed $id group id
     * @return boolean
     */
    public function delete_group($id)
    {
        return $this->client_groups_model->delete($id);
    }

    /**
     * Add new customer groups
     * @param array $data $_POST data
     */
    public function add_group($data)
    {
        return $this->client_groups_model->add($data);
    }

    /**
     * Edit customer group
     * @param  array $data $_POST data
     * @return boolean
     */
    public function edit_group($data)
    {
        return $this->client_groups_model->edit($data);
    }

    /**
    * Create new vault entry
    * @param  array $data        $_POST data
    * @param  mixed $customer_id customer id
    * @return boolean
    */
    public function vault_entry_create($data, $customer_id)
    {
        return $this->client_vault_entries_model->create($data, $customer_id);
    }

    /**
     * Update vault entry
     * @param  mixed $id   vault entry id
     * @param  array $data $_POST data
     * @return boolean
     */
    public function vault_entry_update($id, $data)
    {
        return $this->client_vault_entries_model->update($id, $data);
    }

    /**
     * Delete vault entry
     * @param  mixed $id entry id
     * @return boolean
     */
    public function vault_entry_delete($id)
    {
        return $this->client_vault_entries_model->delete($id);
    }

    /**
     * Get customer vault entries
     * @param  mixed $customer_id
     * @param  array  $where       additional wher
     * @return array
     */
    public function get_vault_entries($customer_id, $where = [])
    {
        return $this->client_vault_entries_model->get_by_customer_id($customer_id, $where);
    }

    /**
     * Get single vault entry
     * @param  mixed $id vault entry id
     * @return object
     */
    public function get_vault_entry($id)
    {
        return $this->client_vault_entries_model->get($id);
    }

    /**
    * Get customer statement formatted
    * @param  mixed $customer_id customer id
    * @param  string $from        date from
    * @param  string $to          date to
    * @return array
    */
    public function get_statement($customer_id, $from, $to)
    {
        return $this->statement_model->get_statement($customer_id, $from, $to);
    }

    /**
    * Send customer statement to email
    * @param  mixed $customer_id customer id
    * @param  array $send_to     array of contact emails to send
    * @param  string $from        date from
    * @param  string $to          date to
    * @param  string $cc          email CC
    * @return boolean
    */
    public function send_statement_to_email($customer_id, $send_to, $from, $to, $cc = '')
    {
        return $this->statement_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);
    }

    /**
     * When customer register, mark the contact and the customer as inactive and set the registration_confirmed field to 0
     * @param  mixed $client_id  the customer id
     * @return boolean
     */
    public function require_confirmation($client_id)
    {
        $contact_id = get_primary_contact_user_id($client_id);
        $this->db->where('userid', $client_id);
        $this->db->update(db_prefix() . 'clients', ['active' => 0, 'registration_confirmed' => 0]);

        $this->db->where('id', $contact_id);
        $this->db->update(db_prefix() . 'contacts', ['active' => 0]);

        return true;
    }

    public function confirm_registration($client_id)
    {
        $contact_id = get_primary_contact_user_id($client_id);
        $this->db->where('userid', $client_id);
        $this->db->update(db_prefix() . 'clients', ['active' => 1, 'registration_confirmed' => 1]);

        $this->db->where('id', $contact_id);
        $this->db->update(db_prefix() . 'contacts', ['active' => 1]);

        $contact = $this->get_contact($contact_id);

        if ($contact) {
            send_mail_template('customer_registration_confirmed', $contact);

            return true;
        }

        return false;
    }

    public function send_verification_email($id)
    {
        $contact = $this->get_contact($id);

        if (empty($contact->email)) {
            return false;
        }

        $success = send_mail_template('customer_contact_verification', $contact);

        if ($success) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'contacts', ['email_verification_sent_at' => date('Y-m-d H:i:s')]);
        }

        return $success;
    }

    public function mark_email_as_verified($id)
    {
        $contact = $this->get_contact($id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', [
            'email_verified_at'          => date('Y-m-d H:i:s'),
            'email_verification_key'     => null,
            'email_verification_sent_at' => null,
        ]);

        if ($this->db->affected_rows() > 0) {

            // Check for previous tickets opened by this email/contact and link to the contact
            $this->load->model('tickets_model');
            $this->tickets_model->transfer_email_tickets_to_contact($contact->email, $contact->id);

            return true;
        }

        return false;
    }

    public function get_clients_distinct_countries()
    {
        return $this->db->query('SELECT DISTINCT(country_id), short_name FROM ' . db_prefix() . 'clients JOIN ' . db_prefix() . 'countries ON ' . db_prefix() . 'countries.country_id=' . db_prefix() . 'clients.country')->result_array();
    }

    public function send_notification_customer_profile_file_uploaded_to_responsible_staff($contact_id, $customer_id)
    {
        $staff         = $this->get_staff_members_that_can_access_customer($customer_id);
        $merge_fields  = $this->app_merge_fields->format_feature('client_merge_fields', $customer_id, $contact_id);
        $notifiedUsers = [];


        foreach ($staff as $member) {
            mail_template('customer_profile_uploaded_file_to_staff', $member['email'], $member['staffid'])
            ->set_merge_fields($merge_fields)
            ->send();

            if (add_notification([
                    'touserid' => $member['staffid'],
                    'description' => 'not_customer_uploaded_file',
                    'link' => 'clients/client/' . $customer_id . '?group=attachments',
                ])) {
                array_push($notifiedUsers, $member['staffid']);
            }
        }
        pusher_trigger_notification($notifiedUsers);
    }

    public function get_staff_members_that_can_access_customer($id)
    {
        $id = $this->db->escape_str($id);

        return $this->db->query('SELECT * FROM ' . db_prefix() . 'staff
            WHERE (
                    admin=1
                    OR staffid IN (SELECT staff_id FROM ' . db_prefix() . "customer_admins WHERE customer_id='.$id.')
                    OR staffid IN(SELECT staff_id FROM " . db_prefix() . 'staff_permissions WHERE feature = "customers" AND capability="view")
                )
            AND active=1')->result_array();
    }

    private function check_zero_columns($data)
    {
        if (!isset($data['show_primary_contact'])) {
            $data['show_primary_contact'] = 0;
        }

        if (isset($data['default_currency']) && $data['default_currency'] == '' || !isset($data['default_currency'])) {
            $data['default_currency'] = 0;
        }

        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }

        if (isset($data['billing_country']) && $data['billing_country'] == '' || !isset($data['billing_country'])) {
            $data['billing_country'] = 0;
        }

        if (isset($data['shipping_country']) && $data['shipping_country'] == '' || !isset($data['shipping_country'])) {
            $data['shipping_country'] = 0;
        }

        return $data;
    }

    public function delete_contact_profile_image($id)
    {
        hooks()->do_action('before_remove_contact_profile_image');
        if (file_exists(get_upload_path_by_type('contact_profile_images') . $id)) {
            delete_dir(get_upload_path_by_type('contact_profile_images') . $id);
        }
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', [
            'profile_image' => null,
        ]);
    }

    /**
     * @param $projectId
     * @param  string  $tasks_email
     *
     * @return array[]
     */
    public function get_contacts_for_project_notifications($projectId, $type)
    {
        $this->db->select('clientid,contact_notification,notify_contacts');
        $this->db->from(db_prefix() . 'projects');
        $this->db->where('id', $projectId);
        $project = $this->db->get()->row();

        if (!in_array($project->contact_notification, [1, 2])) {
            return [];
        }

        $this->db
            ->where('userid', $project->clientid)
            ->where('active', 1)
            ->where($type, 1);

        if ($project->contact_notification == 2) {
            $projectContacts = unserialize($project->notify_contacts);
            $this->db->where_in('id', $projectContacts);
        }

        return $this->db->get(db_prefix() . 'contacts')->result_array();
    }

    public function save_client(){

        //Get Visit id
        $this->db->from(db_prefix() . 'appointment');
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
		//$visit_id = "V-".$formatted_number;
		$visit_id = "";
		$mr_no = $formatted_number;
		
		
        $default_language = $this->input->post('default_language');
        $default_language_string = is_array($default_language) ? implode(',', $default_language) : $default_language;
        
        $data = array(
            "company" => $this->input->post('company'),
            "phonenumber" => $this->input->post('contact_number'),
            "address" => $this->input->post('area'),
            "default_language" => $default_language_string,
            "city" => $this->input->post('city'),
            "address" => $this->input->post('area'),
            "state" => $this->input->post('area'),
            //"country" => 102,
            "datecreated" => date('Y-m-d H:i:s'),
        );
        $table_suffix = "clients";

        //Check User
        $company = $this->input->post('company');
        $check_user = $this->db->get_where(db_prefix() . $table_suffix, array("phonenumber"=>$this->input->post('contact_number'), "company"=>$company))->row();
        $return = 0;
        if($check_user){
            $client_id = $check_user->userid;

            //Get MR NO
            $get_mr_no = $this->db->get_where(db_prefix() . 'clients_new_fields', array("userid"=>$client_id))->row();
            if($get_mr_no){
                $mr_no = $get_mr_no->mr_no;
            }else{
				$this->db->where(array("userid"=>$client_id));
				$this->db->update(db_prefix() . $table_suffix, $data);
				
                //Inserting patient other fields
                $clients_new_fields_data = array(
                    'userid'      => $client_id,
                    'mr_no'       => $mr_no,
                    'marital_status'  => $this->input->post('marital_status'),
                    'email_id'  	=> $this->input->post('email_id'),
                    'pincode'   	=> $this->input->post('pincode'),
                    'area'  		=> $this->input->post('area'),
                    'salutation'  	=> $this->input->post('salutation'),
                    'age'         	=> $this->input->post('age'),
                    'gender'        => $this->input->post('gender'),
                    'patient_status'=> 'Active',
                    'whatsapp_number'=> $this->input->post('contact_number'),
                    'alt_number1'=> $this->input->post('alt_number1'),
                    'alt_number2'=> $this->input->post('alt_number2'),
                
                );
            
                $this->db->insert(db_prefix() . 'clients_new_fields', $clients_new_fields_data);
            }
        }else{
			$data['leadid'] = $this->input->post('leadid');
            $this->db->insert(db_prefix() . $table_suffix, $data);
            $client_id = $this->db->insert_id();
			
			$update_lead = array(
			"date_converted" => date('Y-m-d H:i:s')
			);
			$get_master_settings = $this->db->get_where(db_prefix() . 'master_settings', array("table" => 'leads_status'))->row();
			if ($get_master_settings) {
				$setting = $get_master_settings->options;
				$get_id = $this->db->get_where(db_prefix() . 'leads_status', array("name" => $setting))->row();
				if ($get_id) {
					$update_lead['status'] = $get_id->id;
				}
			}
			$this->db->where(array("id"=>$this->input->post('leadid')));
			$this->db->update(db_prefix() . 'leads', $update_lead);
			
			
            $return = 1;
            $description = "new_patient_added";
            $this->log_patient_activity($client_id, $description);
            
            //Get MR NO
            $get_mr_no = $this->db->get_where(db_prefix() . 'clients_new_fields', array("userid"=>$client_id))->row();
            if($get_mr_no){
                $mr_no = $get_mr_no->mr_no;
            }else{
                //Inserting patient other fields
                $clients_new_fields_data = array(
					'userid'      => $client_id,
                    'mr_no'       => $mr_no,
                    'marital_status'  => $this->input->post('marital_status'),
                    'email_id'  	=> $this->input->post('email_id'),
                    'pincode'   	=> $this->input->post('pincode'),
                    'area'  		=> $this->input->post('area'),
                    'salutation'  	=> $this->input->post('salutation'),
                    'age'         	=> $this->input->post('age'),
                    'gender'        => $this->input->post('gender'),
                    'patient_status'=> 'Active',
                    'whatsapp_number'=> $this->input->post('contact_number'),
                    'alt_number1'=> $this->input->post('alt_number1'),
                    'alt_number2'=> $this->input->post('alt_number2'),
                
                );
            
                $this->db->insert(db_prefix() . 'clients_new_fields', $clients_new_fields_data);
            }
           
        }
       
        if($client_id AND $this->input->post('groupid')){
            $group_data = array(
                "groupid" => $this->input->post('groupid'),
                "customer_id"=> $client_id
            );
            $table_suffix = "customer_groups";
            $this->db->insert(db_prefix() . $table_suffix, $group_data);
        }

        //if ($client_id) {
            $appointment_data = array(
                'visit_id'                => $visit_id,
                'userid'                => $client_id,
                'enquiry_type_id'       => $this->input->post('enquiry_type_id'),
                'appointment_type_id'       => $this->input->post('appointment_type_id'),
                'patient_response_id'   => $this->input->post('patient_response_id'),
                'patient_priority_id'   => $this->input->post('patient_priority_id'),
                'patient_source_id'   => $this->input->post('patient_source_id'),
                'slots_id'              => $this->input->post('slots_id'),
                'treatment_id'          => $this->input->post('treatment_id'),
                'consultation_fee_id'   => $this->input->post('consultation_fee_id'),
                'enquiry_doctor_id'   => $this->input->post('assign_doctor_id'),
                'unit_doctor_id'   => $this->input->post('assign_doctor_id'),
                'remarks'   => $this->input->post('remarks'),
                'next_calling_date'   => date('Y-m-d', strtotime($this->input->post('next_calling_date'))),
                'appointment_date'   => date('Y-m-d H:i:s', strtotime($this->input->post('appointment_date'))),
            );
        
            $this->db->insert(db_prefix() . 'appointment', $appointment_data);
            $insert_id = $this->db->insert_id();
			
			if ($insert_id) {
				$mobile  = $this->input->post('contact_number');
				$company = $this->input->post('company');
				$date    = date('d-m-Y H:i', strtotime($this->input->post('appointment_date')));
				$vars    = [$company, $date, $branch_code];

				// Trigger the hook, passing mobile and params
				hooks()->do_action('appointment_confirmation_triggered', [
					'channel' => 'whatsapp',
					'mobile' => $mobile,
					'params' => $vars,
				]);
				
				// Trigger the hook, passing mobile and params
				hooks()->do_action('appointment_confirmation_triggered', [
					'channel' => 'sms',
					'mobile' => $mobile,
					'params' => $vars,
				]);
				
			}

			
			
			$this->load->model('invoices_model');
		
			$year = date('Y');

			$this->db->from('tblinvoices');
			$this->db->where('YEAR(date)', $year);
			$count = $this->db->count_all_results();

			$next_number = $count + 1;
			$invoice_number = 'INV-' . str_pad($next_number, 6, '0', STR_PAD_LEFT);
			
			
			if($this->input->post('paying_amount')){
				$paying_amount = $this->input->post('paying_amount');
			}else{
				$paying_amount = 0;
			}

			if($this->input->post('due_amount')){
				$due_amount = $this->input->post('due_amount');
			}else{
				$due_amount = 0;
			}

			$invoice_data['formatted_number'] = $invoice_number;
			$invoice_data['number'] = $next_number;
			$invoice_data['clientid'] = $client_id;
			$invoice_data['show_shipping_on_invoice'] = 1;
			$invoice_data['date'] = date('Y-m-d');
			$invoice_data['duedate'] = date('Y-m-d');
			$invoice_data['currency'] = 1;
			$invoice_data['addedfrom'] = get_staff_user_id();
			$invoice_data['subtotal'] = $paying_amount + $due_amount;
			$invoice_data['total'] = $paying_amount + $due_amount;
			$invoice_data['prefix'] = "INV-";
			$invoice_data['number_format'] = 1;
			
			
			$invoice_data['allowed_payment_modes'] = 'a:1:{i:0;s:1:"1";}';
			
			$invoice_data['datecreated'] = date('Y-m-d H:i:s');
			
			$id = $this->invoices_model->add($invoice_data); 
			
			
			if($this->input->post('paying_amount')>0){
				if($this->input->post('due_amount') == 0){
					$status = 2;
				}else{
					$status = 3;
				}
			}else{
				$status = 1;
			}
			
			$update = array(
			'allowed_payment_modes' => 'a:1:{i:0;s:1:"1";}',
			'status' => $status,
			);
			$this->db->where(array("id"=>$id));
			$this->db->update(db_prefix() . 'invoices', $update);
			
		   $itemable= array(
			"rel_id" => $id,
			"rel_type" => "invoice",
			"description" => "Consultation Fee",
			"qty" => 1,
			"item_order" => 1,
			"rate"=>$paying_amount + $due_amount
			);
			
			$this->db->insert(db_prefix() . 'itemable', $itemable);
			
			if($this->input->post('paying_amount')>0){
			   $invoicepaymentrecords= array(
				"invoiceid" => $id,
				"amount" => $this->input->post('paying_amount'),
				"paymentmode" => $this->input->post('paymentmode'),
				"date" => date('Y-m-d'),
				"daterecorded" => date('Y-m-d H:i:s'),
				);
				
				$this->db->insert(db_prefix() . 'invoicepaymentrecords', $invoicepaymentrecords);
			}

			
			
            $description = "new_appointment_added";
            $this->log_patient_activity($insert_id, $description);
            $return = 2;
        //}
        
        return $return;
    }

    public function update_client()
    {
        $client_id = $this->input->post('userid');
        // Build data for main clients table
        $data = array(
            "company"           => $this->input->post('company'),
            "phonenumber"       => $this->input->post('contact_number'),
            "address"           => $this->input->post('area'),
            "default_language"  => is_array($this->input->post('default_language')) ? implode(',', $this->input->post('default_language')) : '',
            "city"              => $this->input->post('city'),
            "state"             => $this->input->post('area'),
            //"country"           => 102,
            "datecreated"       => date('Y-m-d H:i:s')
        );
    
        // Update main client table
        $this->db->where('userid', $client_id);
        $this->db->update(db_prefix() . 'clients', $data);
    
        // Build data for clients_new_fields table
        $clients_new_fields_data = array(
            'userid'        => $client_id,
            'marital_status'=> $this->input->post('marital_status'),
			'email_id'  	=> $this->input->post('email_id'),
			'pincode'   	=> $this->input->post('pincode'),
			'area'  		=> $this->input->post('area'),
			'salutation'  	=> $this->input->post('salutation'),
			'age'         	=> $this->input->post('age'),
			'gender'        => $this->input->post('gender'),
			'patient_status'=> 'Active',
			'whatsapp_number'=> $this->input->post('contact_number'),
			'alt_number1'=> $this->input->post('alt_number1'),
			'alt_number2'=> $this->input->post('alt_number2'),
        );
    
        // Check if record exists in clients_new_fields
        $exists = $this->db->get_where(db_prefix() . 'clients_new_fields', ['userid' => $client_id])->row();
    
        if ($exists) {
            $this->db->where('userid', $client_id);
            $this->db->update(db_prefix() . 'clients_new_fields', $clients_new_fields_data);
        } else {
            $this->db->insert(db_prefix() . 'clients_new_fields', $clients_new_fields_data);
        }
		$this->db->from(db_prefix() . 'appointment');
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
		//$visit_id = "V-".$formatted_number;
		$visit_id = "";
		
		
		$appointment_data = array(
                'visit_id'                => $visit_id,
                'userid'                => $client_id,
                'enquiry_type_id'       => $this->input->post('enquiry_type_id'),
                'appointment_type_id'   => $this->input->post('appointment_type_id'),
                'patient_response_id'   => $this->input->post('patient_response_id'),
                'patient_priority_id'   => $this->input->post('patient_priority_id'),
                'patient_source_id'   => $this->input->post('patient_source_id'),
                'slots_id'              => $this->input->post('slots_id'),
                'treatment_id'          => $this->input->post('treatment_id'),
                'consultation_fee_id'   => $this->input->post('consultation_fee_id'),
                'enquiry_doctor_id'   => $this->input->post('assign_doctor_id'),
                'unit_doctor_id'   => $this->input->post('assign_doctor_id'),
                'remarks'   => $this->input->post('remarks'),
                'next_calling_date'   => date('Y-m-d', strtotime($this->input->post('next_calling_date'))),
                'appointment_date'   => date('Y-m-d H:i:s', strtotime($this->input->post('appointment_date'))),
            );
        
            $this->db->insert(db_prefix() . 'appointment', $appointment_data);
            $insert_id = $this->db->insert_id();
			
			if ($insert_id) {
				$mobile  = $this->input->post('contact_number');
				$company = $this->input->post('company');
				$date    = date('d-m-Y H:i', strtotime($this->input->post('appointment_date')));
				$vars    = [$company, $date, $branch_code];

				// Trigger the hook, passing mobile and params
				hooks()->do_action('appointment_confirmation_triggered', [
					'channel' => 'whatsapp',
					'mobile' => $mobile,
					'params' => $vars,
				]);
				
				// Trigger the hook, passing mobile and params
				hooks()->do_action('appointment_confirmation_triggered', [
					'channel' => 'sms',
					'mobile' => $mobile,
					'params' => $vars,
				]);
			}



			
            $description = "new_appointment_added";
            $this->log_patient_activity($insert_id, $description);
			
			$this->load->model('invoices_model');
		
			$year = date('Y');

			$this->db->from('tblinvoices');
			$this->db->where('YEAR(date)', $year);
			$count = $this->db->count_all_results();

			$next_number = $count + 1;
			$invoice_number = 'INV-' . str_pad($next_number, 6, '0', STR_PAD_LEFT);
			
			if($this->input->post('paying_amount')){
				$paying_amount = $this->input->post('paying_amount');
			}else{
				$paying_amount = 0;
			}

			if($this->input->post('due_amount')){
				$due_amount = $this->input->post('due_amount');
			}else{
				$due_amount = 0;
			}

			$invoice_data['formatted_number'] = $invoice_number;
			$invoice_data['number'] = $next_number;
			$invoice_data['clientid'] = $client_id;
			$invoice_data['show_shipping_on_invoice'] = 1;
			$invoice_data['date'] = date('Y-m-d');
			$invoice_data['duedate'] = date('Y-m-d');
			$invoice_data['currency'] = 1;
			$invoice_data['addedfrom'] = get_staff_user_id();
			$invoice_data['subtotal'] = $paying_amount + $due_amount;
			$invoice_data['total'] = $paying_amount + $due_amount;
			$invoice_data['prefix'] = "INV-";
			$invoice_data['number_format'] = 1;
			
			
			$invoice_data['allowed_payment_modes'] = 'a:1:{i:0;s:1:"1";}';
			
			$invoice_data['datecreated'] = date('Y-m-d H:i:s');
			
			$id = $this->invoices_model->add($invoice_data); 
			
			
			if($this->input->post('paying_amount')>0){
				if($this->input->post('due_amount') == 0){
					$status = 2;
				}else{
					$status = 3;
				}
			}else{
				$status = 1;
			}
			
			$update = array(
			'allowed_payment_modes' => 'a:1:{i:0;s:1:"1";}',
			'status' => $status,
			);
			$this->db->where(array("id"=>$id));
			$this->db->update(db_prefix() . 'invoices', $update);
			
		   $itemable= array(
			"rel_id" => $id,
			"rel_type" => "invoice",
			"description" => "Consultation Fee",
			"qty" => 1,
			"item_order" => 1,
			"rate"=>$paying_amount + $due_amount
			);
			
			$this->db->insert(db_prefix() . 'itemable', $itemable);
			
			if($this->input->post('paying_amount')>0){
			   $invoicepaymentrecords= array(
				"invoiceid" => $id,
				"amount" => $this->input->post('paying_amount'),
				"paymentmode" => $this->input->post('paymentmode'),
				"date" => date('Y-m-d'),
				"daterecorded" => date('Y-m-d H:i:s'),
				);
				
				$this->db->insert(db_prefix() . 'invoicepaymentrecords', $invoicepaymentrecords);
			}
    
        return true;
    }
    

    public function get_apponitment_data($id = '', $where = [])
    {
        $this->db->select('a.*, t.*, s.*'); // Add more fields if needed
        $this->db->from(db_prefix() . 'appointment a');

        // Join master tables
        $this->db->join(db_prefix() . 'treatment t', 't.treatment_id = a.treatment_id', 'left');
        $this->db->join(db_prefix() . 'slots s', 's.slots_id = a.slots_id', 'left'); 

        // Where condition
        $this->db->where('a.userid', $id);

        // Optional additional where filter
        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db->get()->result_array();
    }

    public function get_customer_new_fields($id)
    {
        return $this->db->get_where(db_prefix() . 'clients_new_fields', array("userid"=>$id))->row();
    }

    public function get_patient_activity_log($id)
    {
        //$sorting = hooks()->apply_filters('lead_activity_log_default_sort', 'ASC');

        $this->db->where('patientid', $id);
       // $this->db->order_by('date', $sorting);

        return $this->db->get(db_prefix() . 'patient_activity_log')->result_array();
    }

    public function get_patient_prescription($id)
    {
        $sorting = hooks()->apply_filters('lead_activity_log_default_sort', 'DESC');

        $this->db->where('userid', $id);
        $this->db->order_by('created_datetime', $sorting);

        return $this->db->get(db_prefix() . 'patient_prescription')->result_array();
    }

    public function add_patient_call_log($data)
    {
        $insert = [
            'patientid'         => $data['patientid'],
            'called_by'         => get_staff_user_id(),
            'criteria_id'          => $data['criteria_id'],
            'next_calling_date' => $data['next_calling_date'],
            'appointment_type_id'  => $data['appointment_type_id'],
            'appointment_date'  => $data['appointment_date'],
            'created_date'      => !empty($data['created_date']) ? $data['created_date'] : date('Y-m-d'),
            'comments'          => $data['comments'],
        ];

        $this->db->insert(db_prefix() . 'patient_call_logs', $insert);
        $insert_id = $this->db->insert_id();
        $description = "new_patient_call_log_added";
        $this->log_patient_activity($data['patientid'], $description);
        return $insert_id;
    }

    public function get_patient_call_logs($patientid)
    {
        $this->db->select('p.*, type.*, criteria.*'); // Add more fields if needed
        $this->db->from(db_prefix() . 'patient_call_logs p');
        $this->db->join(db_prefix() . 'appointment_type type', 'type.appointment_type_id = p.appointment_type_id', 'left');
       $this->db->join(db_prefix() . 'criteria criteria', 'criteria.criteria_id = p.criteria_id', 'left');
       
        // Optional additional where filter
        if (!empty($where)) {
            $this->db->where($where);
        }
        $this->db->where(array("patientid"=>$patientid));
        //$this->db->order_by('created_date', 'DESC');
        return $this->db->get()->result_array();
    }

    public function save_prescription($data, $patientid)
    {
        // Combine medicine details into a single string (prescription_data)
        $prescription_data = [];
        $i = 1;
        foreach ($data['medicine_name'] as $index => $medicine) {
            $prescription_data[] = $i++.'.'.$medicine . ' ' . $data['medicine_potency'][$index] . ', ' . $data['medicine_dose'][$index] . ' ' . $data['medicine_timing'][$index] . ' ' . $data['medicine_remarks'][$index];
        }
        $prescription_data = implode('; ', $prescription_data);  // Join all the entries with a semicolon

        // Prepare data to save
        $prescription = [
            'userid'              => $patientid,
            'prescription_data'      => $prescription_data,
            'created_by'             => get_staff_user_id(),  // Assuming user is logged in
            'created_datetime'       => date('Y-m-d H:i:s'),
            'patient_prescription_status' => 1  // Set default status, change if needed
        ];


        $this->db->insert(db_prefix() . 'patient_prescription', $prescription);
        $insert_id = $this->db->insert_id();
        $description = "new_prescription_added";
        $this->log_patient_activity($patientid, $description);
        return $insert_id;
    }

    public function log_patient_activity($id, $description, $custom_activity = 0, $additional_data = '')
    {
        $log = [
            'date'            => date('Y-m-d H:i:s'),
            'description'     => $description,
            'patientid'       => $id,
            'staffid'         => get_staff_user_id(),
            'additional_data' => $additional_data,
            'custom_activity' => $custom_activity,
            'full_name'       => get_staff_full_name(get_staff_user_id()),
        ];
        $this->db->insert(db_prefix() . 'patient_activity_log', $log);

        return $this->db->insert_id();
    }

    public function get_invoices($patientid)
    {
        $this->db->select('inv.*, c.*'); // Add more fields if needed
        $this->db->from(db_prefix() . 'invoices inv');

        // Join master tables
        $this->db->join(db_prefix() . 'clients c', 'c.userid = inv.clientid', 'left');
        $this->db->join(db_prefix() . 'itemable item', 'item.rel_id = inv.id', 'left');

        // Where condition
        $this->db->where('inv.clientid', $patientid);
        $this->db->where('item.rel_type', 'invoice');
        $this->db->where('item.description', 'Treatment Plan');

        // Optional additional where filter
        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db->get()->result_array();
    }

    public function get_invoice_payments($patientid)
    {
        $this->db->select('inv.*, payment.*, mode.name as payment_mode'); // Add more fields if needed
        $this->db->from(db_prefix() . 'invoicepaymentrecords payment');

        // Join master tables
        $this->db->join(db_prefix() . 'invoices inv', 'inv.id = payment.invoiceid', 'left');
        $this->db->join(db_prefix() . 'payment_modes mode', 'mode.id = payment.paymentmode', 'left');
		$this->db->join(db_prefix() . 'itemable item', 'item.rel_id = inv.id', 'left');

        // Where condition
        $this->db->where('inv.clientid', $patientid);
		$this->db->where('item.rel_type', 'invoice');
        $this->db->where('item.description', 'Treatment Plan');

        // Optional additional where filter
        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db->get()->result_array();
    }

    public function get_patient_package($invoice_id)
    {
        $this->db->select('inv.id, inv.date, inv.recurring, inv.recurring_type, itemable.qty, itemable.rate, itemable.description'); // Add more fields if needed
        $this->db->from(db_prefix() . 'invoices inv');

        // Join master tables
        $this->db->join(db_prefix() . 'itemable itemable', 'itemable.rel_id = inv.id', 'left');
        $this->db->join(db_prefix() . 'items items', 'items.description = itemable.description', 'left');
        $this->db->join(db_prefix() . 'items_groups items_groups', 'items_groups.id = items.group_id', 'left');

        // Where condition
        $this->db->where('inv.id', $invoice_id);
        $this->db->where('itemable.rel_type', 'invoice');
        $this->db->where('items_groups.name', 'Package');

        // Optional additional where filter
        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db->get()->result_array();
    }

    public function get_patient_by_contact($phonenumber, $where = [])
    {
        $this->db->select('c.*, co.*, ct.*, new.*'); // c = clients, co = countries, ct = contacts

        $this->db->from(db_prefix() . 'clients c');

        $this->db->join(db_prefix() . 'countries co', 'co.country_id = c.country', 'left');
        $this->db->join(db_prefix() . 'clients_new_fields new', 'new.userid = c.userid', 'left');
        $this->db->join(db_prefix() . 'contacts ct', 'ct.userid = c.userid AND ct.is_primary = 1', 'left');

        if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }

        if (is_numeric($phonenumber)) {
            $this->db->select('c.*');
            $this->db->where('c.phonenumber', $phonenumber);
            $client = $this->db->get()->row();

            if ($client && get_option('company_requires_vat_number_field') == 0) {
                $client->vat = null;
            }

            $GLOBALS['client'] = $client;

            return $client;
        }

        $this->db->order_by('c.company', 'asc');

        return $this->db->get()->row();
    }

    public function get_appointments()
    {
        $this->db->select('patients.*, appointments.*, new.*'); // Add more fields if needed
        $this->db->from(db_prefix() . 'appointment appointments');

        // Join master tables
        $this->db->join(db_prefix() . 'clients patients', 'patients.userid = appointments.userid', 'left');
        $this->db->join(db_prefix() . 'clients_new_fields new', 'patients.userid = new.userid', 'left');
       
        // Optional additional where filter
        if (!empty($where)) {
            $this->db->where($where);
        }
        $this->db->order_by("appointments.appointment_id", "DESC");
        return $this->db->get()->result_array();
    }

    public function save_casesheet($patientid)
	{
		// Handle file uploads
		$uploaded_files = [];
		if (!empty($_FILES['documents']['name'][0])) {
			$this->load->library('upload');
			$upload_path = 'uploads/clinical_docs/';
			if (!is_dir($upload_path)) {
				mkdir($upload_path, 0755, true);
			}

			$filesCount = count($_FILES['documents']['name']);
			for ($i = 0; $i < $filesCount; $i++) {
				$_FILES['file']['name']     = $_FILES['documents']['name'][$i];
				$_FILES['file']['type']     = $_FILES['documents']['type'][$i];
				$_FILES['file']['tmp_name'] = $_FILES['documents']['tmp_name'][$i];
				$_FILES['file']['error']    = $_FILES['documents']['error'][$i];
				$_FILES['file']['size']     = $_FILES['documents']['size'][$i];

				$config['upload_path']   = $upload_path;
				$config['allowed_types'] = '*';
				$config['file_name']     = uniqid();

				$this->upload->initialize($config);
				if ($this->upload->do_upload('file')) {
					$upload_data = $this->upload->data();
					$uploaded_files[] = $upload_path . $upload_data['file_name'];
				}
			}
		}
		
		
		// Unified insert data for tblcasesheet
		$data = [
			'userid'                   => $patientid,
			'presenting_complaints'   => $this->input->post('presenting_complaints'),

			// Personal History
			'appetite'                => $this->input->post('appetite'),
			'desires'                 => $this->input->post('desires'),
			'aversion'                => $this->input->post('aversion'),
			'tongue'                  => $this->input->post('tongue'),
			'urine'                   => $this->input->post('urine'),
			'bowels'                  => $this->input->post('bowels'),
			'sweat'                   => $this->input->post('sweat'),
			'sleep'                   => $this->input->post('sleep'),
			'sun_headache'            => $this->input->post('sun_headache'),
			'thermals'                => $this->input->post('thermals'),
			'habits'                  => $this->input->post('habits'),
			'addiction'               => $this->input->post('addiction'),
			'side'                    => $this->input->post('side'),
			'dreams'                  => $this->input->post('dreams'),
			'diabetes'                => $this->input->post('diabetes'),
			'thyroid'                 => $this->input->post('thyroid'),
			'hypertension'            => $this->input->post('hypertension'),
			'hyperlipidemia'          => $this->input->post('hyperlipidemia'),
			'menstrual_obstetric_history' => $this->input->post('menstrual_obstetric_history'),
			'family_history'          => $this->input->post('family_history'),
			'past_treatment_history'  => $this->input->post('past_treatment_history'),
			'staffid'                 => get_staff_user_id(),

			// General Examination
			'bp'                      => $this->input->post('bp'),
			'pulse'                   => $this->input->post('pulse'),
			'weight'                  => $this->input->post('weight'),
			'height'                  => $this->input->post('height'),
			'temperature'             => $this->input->post('temperature'),
			'bmi'                     => $this->input->post('bmi'),
			'mental_generals'         => $this->input->post('mental_generals'),
			'pg'                      => $this->input->post('pg'),
			'particulars'             => $this->input->post('particulars'),
			'miasmatic_diagnosis'     => $this->input->post('miasmatic_diagnosis'),
			'analysis_evaluation'     => $this->input->post('analysis_evaluation'),
			'reportorial_result'      => $this->input->post('reportorial_result'),
			'management'              => $this->input->post('management'),
			'diet'                    => $this->input->post('diet'),
			'exercise'                => $this->input->post('exercise'),
			'critical'                => $this->input->post('critical'),
			'level_of_assent'         => $this->input->post('level_of_assent'),
			'dos_and_donts'           => $this->input->post('dos_and_donts'),
			'level_of_assurance'      => $this->input->post('level_of_assurance'),
			'criteria_future_plan_rx' => $this->input->post('criteria_future_plan_rx'),
			'nutrition'               => $this->input->post('nutrition'),

			// Clinical Observation
			'progress'                => $this->input->post('progress'),
			'clinical_observation'    => $this->input->post('clinical_observation'),
			'suggested_duration'      => $this->input->post('suggested_duration'),
			'documents'               => !empty($uploaded_files) ? json_encode($uploaded_files) : null,
			'medicine_days'           => $this->input->post('medicine_days'),
			'followup_date'           => $this->input->post('followup_date'),
			'patient_status'          => $this->input->post('patient_status'),

			// Mind Section
			'mind'                    => $this->input->post('mind'),

			'date'                    => date('Y-m-d'),
			'created_at'              => date('Y-m-d H:i:s'),
		];

		$this->db->insert(db_prefix() . 'casesheet', $data);
		$insert_id = $this->db->insert_id();
		
		$treatment_type  = $this->input->post('treatment_type');
		$duration_value  = $this->input->post('duration_value');
		$improvement          = $this->input->post('improvement');
		
		$count = count($treatment_type); // Assumes all arrays are the same length

		for ($i = 0; $i < $count; $i++) {
			$treatment_data = array(
				'casesheet_id' => $insert_id,
				'treatment_type_id' => $treatment_type[$i],
				'duration_value' => $duration_value[$i],
				'improvement'    => $improvement[$i],
				'userid'         => $patientid,
				'treatment_status'=>'treatment_started',
				'created_at'     => date("Y-m-d H:i:s"),
			);
			// Insert into DB
			$this->db->insert(db_prefix() . 'patient_treatment', $treatment_data);
		}

		// Log activity
		$this->log_patient_activity($patientid, "new_casesheet_added");

		return $insert_id;
	}
	
	public function update_casesheet()
	{	
		// Handle file uploads
		$uploaded_files = [];
		if (!empty($_FILES['documents']['name'][0])) {
			$this->load->library('upload');
			$upload_path = 'uploads/clinical_docs/';
			if (!is_dir($upload_path)) {
				mkdir($upload_path, 0755, true);
			}

			$filesCount = count($_FILES['documents']['name']);
			for ($i = 0; $i < $filesCount; $i++) {
				$_FILES['file']['name']     = $_FILES['documents']['name'][$i];
				$_FILES['file']['type']     = $_FILES['documents']['type'][$i];
				$_FILES['file']['tmp_name'] = $_FILES['documents']['tmp_name'][$i];
				$_FILES['file']['error']    = $_FILES['documents']['error'][$i];
				$_FILES['file']['size']     = $_FILES['documents']['size'][$i];

				$config['upload_path']   = $upload_path;
				$config['allowed_types'] = '*';
				$config['file_name']     = uniqid();

				$this->upload->initialize($config);
				if ($this->upload->do_upload('file')) {
					$upload_data = $this->upload->data();
					$uploaded_files[] = $upload_path . $upload_data['file_name'];
				}
			}
		}

		// Get existing files from DB if any
		$casesheet_id = $this->input->post('casesheet_id');
		$patientid = $this->input->post('patientid');
		
		$this->db->where('id', $casesheet_id);
		$existing_row = $this->db->get(db_prefix() . 'casesheet')->row();

		$existing_files = [];
		if (!empty($existing_row->documents)) {
			$existing_files = json_decode($existing_row->documents, true);
		}

		// Merge existing files with newly uploaded ones
		$all_files = array_merge($existing_files, $uploaded_files);
		

		// Prepare update data
		$data = [
			'userid'                   => $patientid,
			'presenting_complaints'   => $this->input->post('presenting_complaints'),
			'appetite'                => $this->input->post('appetite'),
			'desires'                 => $this->input->post('desires'),
			'aversion'                => $this->input->post('aversion'),
			'tongue'                  => $this->input->post('tongue'),
			'urine'                   => $this->input->post('urine'),
			'bowels'                  => $this->input->post('bowels'),
			'sweat'                   => $this->input->post('sweat'),
			'sleep'                   => $this->input->post('sleep'),
			'sun_headache'            => $this->input->post('sun_headache'),
			'thermals'                => $this->input->post('thermals'),
			'habits'                  => $this->input->post('habits'),
			'addiction'               => $this->input->post('addiction'),
			'side'                    => $this->input->post('side'),
			'dreams'                  => $this->input->post('dreams'),
			'diabetes'                => $this->input->post('diabetes'),
			'thyroid'                 => $this->input->post('thyroid'),
			'hypertension'            => $this->input->post('hypertension'),
			'hyperlipidemia'          => $this->input->post('hyperlipidemia'),
			'menstrual_obstetric_history' => $this->input->post('menstrual_obstetric_history'),
			'family_history'          => $this->input->post('family_history'),
			'past_treatment_history'  => $this->input->post('past_treatment_history'),
			'bp'                      => $this->input->post('bp'),
			'pulse'                   => $this->input->post('pulse'),
			'weight'                  => $this->input->post('weight'),
			'height'                  => $this->input->post('height'),
			'temperature'             => $this->input->post('temperature'),
			'bmi'                     => $this->input->post('bmi'),
			'mental_generals'         => $this->input->post('mental_generals'),
			'pg'                      => $this->input->post('pg'),
			'particulars'             => $this->input->post('particulars'),
			'miasmatic_diagnosis'     => $this->input->post('miasmatic_diagnosis'),
			'analysis_evaluation'     => $this->input->post('analysis_evaluation'),
			'reportorial_result'      => $this->input->post('reportorial_result'),
			'management'              => $this->input->post('management'),
			'diet'                    => $this->input->post('diet'),
			'exercise'                => $this->input->post('exercise'),
			'critical'                => $this->input->post('critical'),
			'level_of_assent'         => $this->input->post('level_of_assent'),
			'dos_and_donts'           => $this->input->post('dos_and_donts'),
			'level_of_assurance'      => $this->input->post('level_of_assurance'),
			'criteria_future_plan_rx' => $this->input->post('criteria_future_plan_rx'),
			'nutrition'               => $this->input->post('nutrition'),
			'progress'                => $this->input->post('progress'),
			'clinical_observation'    => $this->input->post('clinical_observation'),
			'suggested_duration'      => $this->input->post('suggested_duration'),
			'medicine_days'           => $this->input->post('medicine_days'),
			'followup_date'           => $this->input->post('followup_date'),
			'patient_status'          => $this->input->post('patient_status'),
			'mind'                    => $this->input->post('mind'),
		];


		// If there's anything to save, update the documents field
		if (!empty($all_files)) {
			$data['documents'] = json_encode($all_files);
		}
		$this->db->where('id', $casesheet_id);
		$this->db->update(db_prefix() . 'casesheet', $data);

		$treatment_type  = $this->input->post('treatment_type');
		$duration_value  = $this->input->post('duration_value');
		$improvement     = $this->input->post('improvement');

		$count = count($treatment_type);
		for ($i = 0; $i < $count; $i++) {
			$treatment_data = array(
				'casesheet_id'      => $casesheet_id,
				'treatment_type_id' => $treatment_type[$i],
				'duration_value'    => $duration_value[$i],
				'improvement'       => $improvement[$i],
				'userid'            => $patientid,
				'treatment_status'  => 'treatment_started',
				'created_at'        => date("Y-m-d H:i:s"),
			);
			if($treatment_type[$i]>0){
				$this->db->insert(db_prefix() . 'patient_treatment', $treatment_data);
			}
			
		}
		
		$patient_treatment_ids = $this->input->post('patient_treatment_id');
		
		foreach($patient_treatment_ids as $patient_treatment_id){
			$data = array(
			"duration_value" => $this->input->post('duration_value_'.$patient_treatment_id),
			"improvement" => $this->input->post('improvement_'.$patient_treatment_id),
			"treatment_status" => $this->input->post('treatment_status_'.$patient_treatment_id),
			);
			
			$this->db->where('id', $patient_treatment_id);
			$this->db->update(db_prefix() . 'patient_treatment', $data);
		}

		$this->log_patient_activity($patientid, "casesheet_updated");

		return true;
	}


    public function get_casesheet($id){
        $this->db->select('c.*');
        $this->db->from(db_prefix() . 'casesheet c');
       /*  $this->db->join(db_prefix() . 'general_examination ge', 'ge.personal_history_id = history.id', 'left');
        $this->db->join(db_prefix() . 'preliminary_data pd', 'pd.personal_history_id = history.id', 'left');
        $this->db->join(db_prefix() . 'clinical_observation co', 'co.personal_history_id = history.id', 'left');
        $this->db->join(db_prefix() . 'mind mind', 'mind.personal_history_id = history.id', 'left'); */
        $this->db->where(array("c.userid"=>$id));
        return $this->db->get()->result_array();
    }
    public function get_casesheet_by_id($id){
        $this->db->select('c.*');
        $this->db->from(db_prefix() . 'casesheet c');
        /* $this->db->join(db_prefix() . 'general_examination ge', 'ge.personal_history_id = history.id', 'left');
        $this->db->join(db_prefix() . 'preliminary_data pd', 'pd.personal_history_id = history.id', 'left');
        $this->db->join(db_prefix() . 'clinical_observation co', 'co.personal_history_id = history.id', 'left'); */
        $this->db->where(array("c.id"=>$id));
        return $this->db->get()->result_array();
    }
    public function prev_treatments($patientid){
        $this->db->select('pt.*, t.treatment_name');
        $this->db->from(db_prefix() . 'patient_treatment pt');
        $this->db->join(db_prefix() . 'treatment t', 't.treatment_id = pt.treatment_type_id', 'left');
        /* $this->db->join(db_prefix() . 'preliminary_data pd', 'pd.personal_history_id = history.id', 'left');
        $this->db->join(db_prefix() . 'clinical_observation co', 'co.personal_history_id = history.id', 'left'); */
        $this->db->where(array("pt.userid"=>$patientid));
        return $this->db->get()->result_array();
    }
    public function prev_documents($patientid){
        $this->db->select('c.documents');
        $this->db->from(db_prefix() . 'casesheet c');
        $this->db->where(array("c.userid"=>$patientid));
        return $this->db->get()->result_array();
    }

    public function search_by_contact_number($contact)
	{
		// Search in clients
		$this->db->select('userid as id, phonenumber, company');
		$this->db->like('phonenumber', $contact);
		//$this->db->limit(10);
		$clients = $this->db->get(db_prefix() . 'clients')->result_array();

		foreach ($clients as &$c) {
			$c['source'] = 'patient';
			$c['type'] = 'Patient';
		}

		// Search in leads
		$this->db->select('id, phonenumber, name as company');
		$this->db->like('phonenumber', $contact);
		$this->db->where('date_converted IS NULL');
		//$this->db->limit(10);
		$leads = $this->db->get(db_prefix() . 'leads')->result_array();

		foreach ($leads as &$l) {
			$l['source'] = 'lead';
			$l['type'] = 'Lead';
		}

		// Merge both and return a max of 10 results
		$results = array_merge($clients, $leads);
		return array_slice($results, 0, 10);
	}
	
	 public function get_lead($id = '', $where = [])
    {
        $this->db->select('*,' . db_prefix() . 'leads.name, ' . db_prefix() . 'leads.id,' . db_prefix() . 'leads_status.name as status_name,' . db_prefix() . 'leads_sources.name as source_name');
        $this->db->join(db_prefix() . 'leads_status', db_prefix() . 'leads_status.id=' . db_prefix() . 'leads.status', 'left');
		$this->db->join(db_prefix() . 'city city', 'city.city_id = ' . db_prefix() . 'leads.city', 'left');
        $this->db->join(db_prefix() . 'leads_sources', db_prefix() . 'leads_sources.id=' . db_prefix() . 'leads.source', 'left');

        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'leads.id', $id);
            $lead = $this->db->get(db_prefix() . 'leads')->row();
            if ($lead) {
                if ($lead->from_form_id != 0) {
                    $lead->form_data = $this->get_form([
                        'id' => $lead->from_form_id,
                    ]);
                }
                //$lead->attachments = $this->get_lead_attachments($id);
                //$lead->public_url  = leads_public_url($id);
            }

            return $lead;
        }

        return $this->db->get(db_prefix() . 'leads')->result_array();
    }
	
	public function patient_inactive_fields(){
		$res =  $this->db->get_where(db_prefix() . 'master_settings', array("title"=>'patient_inactive_fields'))->row();
		if($res){
			return $res->options;
		}else{
			return "";
		}
	}
	
	public function get_logged_in_staff_branch_id()
    {
        $staff_id = get_staff_user_id();
        if ($staff_id) {
            $this->db->select('branch_id');
            $this->db->from(db_prefix() . 'staff');
            $this->db->where('staffid', $staff_id);
            $row = $this->db->get()->row();
            return $row ? $row->branch_id : null;
        }
        return null;
    }
	
	public function get_patient_package_details($client_id)
	{
		$invoice_package_details = [];

		// Get all invoices for the client
		$invoices = $this->db->get_where(db_prefix() . 'invoices', ['clientid' => $client_id])->result_array();

		foreach ($invoices as $invoice) {
			$invoice_id = $invoice['id'];

			// Select package items under this invoice
			$this->db->select('inv.id as invoice_id, itemable.description, itemable.qty, itemable.rate');
			$this->db->from(db_prefix() . 'invoices inv');
			$this->db->join(db_prefix() . 'itemable itemable', 'itemable.rel_id = inv.id', 'left');
			$this->db->join(db_prefix() . 'items items', 'items.description = itemable.description', 'left');
			$this->db->join(db_prefix() . 'items_groups items_groups', 'items_groups.id = items.group_id', 'left');
			$this->db->where('inv.id', $invoice_id);
			$this->db->where('itemable.rel_type', 'invoice');
			$this->db->where('items_groups.name', 'Package');

			$packages = $this->db->get()->result_array();

			foreach ($packages as $package) {
				$total = $package['qty'] * $package['rate'];
				$paid = 0;

				// Get total paid against this invoice
				$payments = $this->db->select_sum('amount')
					->get_where(db_prefix() . 'invoicepaymentrecords', [
						'invoiceid' => $package['invoice_id']
					])->row();

				if ($payments && isset($payments->amount)) {
					$paid = $payments->amount;
				}

				$invoice_package_details[] = [
					'invoice_id'   => $package['invoice_id'],
					'description'  => $package['description'],
					'qty'          => $package['qty'],
					'rate'         => $package['rate'],
					'total'        => $total,
					'paid'         => $paid,
					'due'          => $total - $paid,
				];
			}
		}

		return $invoice_package_details;
	}
	
	public function ownership_details($type, $doctor_id=NULL){
		if($type == "visit"){
			$visit = 1;
			$this->db->where(array("visit_status"=>$visit));
		}
		
		$this->db->select('patients.*, appointments.*, new.*, treatment.treatment_name'); // Add more fields if needed
		$this->db->from(db_prefix() . 'appointment appointments');

		// Join master tables
		$this->db->join(db_prefix() . 'clients patients', 'patients.userid = appointments.userid', 'left');
		$this->db->join(db_prefix() . 'clients_new_fields new', 'patients.userid = new.userid', 'left');
		$this->db->join(db_prefix() . 'treatment treatment', 'treatment.treatment_id = appointments.treatment_id', 'left');
	   
		// Optional additional where filter
		if (!empty($where)) {
			$this->db->where($where);
		}
		
		if($doctor_id){
			$this->db->where(array("enquiry_doctor_id"=>$doctor_id));
		}
		
		$this->db->order_by("appointments.appointment_id", "DESC");
		return $this->db->get()->result_array();
	}

	public function get_package_html($packages)
	{
	  if (empty($packages)) return 'No packages found.';
	  $html = '<table class="table table-bordered small">';
	  $html .= '<thead><tr>
				  <th>Invoice</th><th>Description</th><th>Qty</th>
				  <th>Rate</th><th>Total</th><th>Paid</th><th>Due</th>
			   </tr></thead><tbody>';

	  foreach ($packages as $p) {
		$html .= '<tr>
					<td>' . $p['invoice_id'] . '</td>
					<td>' . $p['description'] . '</td>
					<td>' . $p['qty'] . '</td>
					<td>' . app_format_money_custom($p['rate'], 1) . '</td>
					<td>' . app_format_money_custom($p['total'], 1) . '</td>
					<td>' . app_format_money_custom($p['paid'], 1) . '</td>
					<td>' . app_format_money_custom($p['due'], 1) . '</td>
				  </tr>';
	  }

	  $html .= '</tbody></table>';
	  return $html;
	}
	public function get_teeth_by_quadrant($quadrant = 1, $dentition_type=NULL)
	{
		
		return $this->db->where(array('quadrant'=>$quadrant, "dentition_type"=>$dentition_type))
						->order_by('display_order', 'ASC')
						->get('tbltooth_chart')
						->result_array();
	}
	
	 public function add_chief_complaint($data)
    {
        return $this->db->insert_batch(db_prefix() . 'tooth_chief_complaints', $data);
    }

    public function get_chief_complaint($patientid)
    {
        $this->db->where('patient_id', $patientid);
        return $this->db->get(db_prefix() . 'tooth_chief_complaints')->result_array();
    }

  
	
	/* Investigation */
	public function add_investigation($data)
    {
        return $this->db->insert(db_prefix() . 'tooth_investigations', $data);
    }

    public function get_investigations_by_type($patient_id, $type)
    {
        return $this->db->where([
            'patient_id' => $patient_id,
            'type' => $type
        ])->get(db_prefix() . 'tooth_investigations')->result_array();
    }

    public function delete_investigation($id)
    {
        return $this->db->delete(db_prefix() . 'tooth_investigations', ['id' => $id]);
    }
	
	/* End Investigation */
	
	/* medical_problems */
	public function add_medical_problems($data)
    {
		return $this->db->insert(db_prefix() . 'tooth_medical_problems', $data);
    }
    public function get_medical_problems($patientid)
    {
        $this->db->where('patient_id', $patientid);
        return $this->db->get(db_prefix() . 'tooth_medical_problems')->result_array();
    }
	
	public function delete_medical_problems($id)
    {
        return $this->db->delete(db_prefix() . 'tooth_medical_problems', ['id' => $id]);
    }
	/* End medical_problems */

    public function delete_chief_complaints($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete(db_prefix() . 'tooth_chief_complaints');
		
	}
	
    public function add_present_medications($data)
    {
        return $this->db->insert(db_prefix() . 'tooth_present_medications', [
            'patient_id' => $data['patient_id'],
            'file' => $data['file'] ?? null,
            'notes' => $data['notes']
        ]);
    }
	
    public function get_present_medications($patientid)
    {
        $this->db->where('patient_id', $patientid);
        return $this->db->get(db_prefix() . 'tooth_present_medications')->result_array();
    }
	
	public function delete_present_medications($id)
    {
        return $this->db->delete(db_prefix() . 'tooth_present_medications', ['id' => $id]);
    }
	
	 public function add_toot_prescription($data)
    {
        $prescription = [
            'prescription_code' => "P-".rand(11111, 99999),
            'patient_id' => $data['patient_id'],
            'notes' => $data['notes'],
            'prescription_by' => $data['prescription_by'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert(db_prefix() . 'tooth_prescriptions', $prescription);
        $prescription_id = $this->db->insert_id();

        foreach ($data['medicines'] as $med) {
            $this->db->insert(db_prefix() . 'tooth_prescription_medicines', [
                'prescription_id' => $prescription_id,
                'medicine_name' => $med['medicine'],
                'frequency' => $med['frequency'],
                'duration' => $med['duration'],
                'usage' => $med['usage']
            ]);
			

        }

        return true;
    }

    public function get_toot_prescriptions($patient_id)
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . 'tooth_prescriptions');
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('created_at', 'DESC');
        $prescriptions = $this->db->get()->result_array();

        foreach ($prescriptions as &$presc) {
            $this->db->where('prescription_id', $presc['id']);
            $presc['medicines'] = $this->db->get(db_prefix() . 'tooth_prescription_medicines')->result_array();
        }

        return $prescriptions;
    }
	
	public function get_prescriptions_by_patient($patient_id)
    {
        $this->db->select('p.*, s.firstname as doctor_name, DATE_FORMAT(p.created_at, "%d-%m-%Y") as date');
        $this->db->from(db_prefix() . 'tooth_prescriptions p');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = p.prescription_by', 'left');
        $this->db->where('patient_id', $patient_id);
        return $this->db->get()->result_array();
    }

    public function get_prescription_details($id)
    {
        $this->db->select('p.*, s.firstname as doctor_name');
        $this->db->from(db_prefix() . 'tooth_prescriptions p');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = p.prescription_by', 'left');
        $this->db->where('p.id', $id);
        $prescription = $this->db->get()->row_array();

        $this->db->where('prescription_id', $id);
        $prescription['medicines'] = $this->db->get(db_prefix() . 'tooth_prescription_medicines')->result_array();

        return $prescription;
    }
	
	public function delete_prescriptions($id)
	{
		// First, delete associated medicines
		$this->db->where('prescription_id', $id)->delete(db_prefix() . 'tooth_prescription_medicines');

		// Then delete the prescription itself
		$this->db->where('id', $id);
		return $this->db->delete(db_prefix() . 'tooth_prescriptions');
	}
	
	public function add_examination_findings($data)
    {
        $this->db->insert(db_prefix() . 'tooth_examination_findings', $data);
        return $this->db->insert_id();
    }

    public function get_all_examination_findings($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get(db_prefix() . 'tooth_examination_findings');
        return $query->result_array();
    }

    public function delete_examination_findings($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tooth_examination_findings');
        return ($this->db->affected_rows() > 0);
    }
	
	
	public function add_past_dental_history($data)
	{
		$this->db->insert(db_prefix() . 'tooth_past_dental_history', $data);
		return $this->db->insert_id();
	}


    public function get_all_past_dental_history($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get(db_prefix() . 'tooth_past_dental_history');
        return $query->result_array();
    }

    public function delete_past_dental_history($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tooth_past_dental_history');
        return ($this->db->affected_rows() > 0);
    }
	
	public function add_treatment_plan($data) {
		$data['treatment_status'] = "Not Started";
		$this->db->insert(db_prefix() . 'tooth_treatment_plans', $data);
		return $this->db->insert_id();
	}

	public function get_treatment_plans_by_plan_type($patient_id, $planType, $onlyUnaccepted = false)
	{
		$this->db->where('patient_id', $patient_id);

		// Map plan type
		if ($planType === 'A') {
			$this->db->where('plan_type', 'A');
		} elseif ($planType === 'B') {
			$this->db->where('plan_type', 'B');
		} elseif ($planType === 'C') {
			$this->db->where('plan_type', 'C');
		}

		if ($onlyUnaccepted) {
			$this->db->where('is_accepted', 0);
		}

		return $this->db->get(db_prefix() . 'tooth_treatment_plans')->result_array();
	}


	public function get_accepted_treatment_plans($patient_id)
	{
		$this->db->where('patient_id', $patient_id);
		$this->db->where('is_accepted', 1);
		return $this->db->get(db_prefix() . 'tooth_treatment_plans')->result_array();
	}


	public function accept_treatment_plan($id) {
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'tooth_treatment_plans', ['is_accepted' => 1]);
		
		$get_data = $this->db->get_where(db_prefix() . 'tooth_treatment_plans', array('id'=>$id))->row();
		
		if($get_data){
			$this->load->model('invoices_model');
	
			$year = date('Y');
			
			$due_amount = $get_data->final_amount;
			$client_id = $get_data->patient_id;
			$units = $get_data->units;

			$this->db->from('tblinvoices');
			$this->db->where('YEAR(date)', $year);
			$count = $this->db->count_all_results();

			$next_number = $count + 1;
			$invoice_number = 'INV-' . str_pad($next_number, 6, '0', STR_PAD_LEFT);
			
			$paying_amount = 0;
			

			$invoice_data['formatted_number'] = $invoice_number;
			$invoice_data['number'] = $next_number;
			$invoice_data['clientid'] = $client_id;
			$invoice_data['show_shipping_on_invoice'] = 1;
			$invoice_data['date'] = date('Y-m-d');
			$invoice_data['duedate'] = date('Y-m-d');
			$invoice_data['currency'] = 1;
			$invoice_data['addedfrom'] = get_staff_user_id();
			$invoice_data['subtotal'] = $paying_amount + $due_amount;
			$invoice_data['total'] = $paying_amount + $due_amount;
			$invoice_data['prefix'] = "INV-";
			$invoice_data['number_format'] = 1;
			
			
			$invoice_data['allowed_payment_modes'] = 'a:1:{i:0;s:1:"1";}';
			
			$invoice_data['datecreated'] = date('Y-m-d H:i:s');
			
			$id = $this->invoices_model->add($invoice_data);
			
			if($paying_amount>0){
				if($due_amount == 0){
					$status = 2;
				}else{
					$status = 3;
				}
			}else{
				$status = 1;
			}
			
			$update = array(
			'allowed_payment_modes' => 'a:1:{i:0;s:1:"1";}',
			'status' => $status,
			);
			$this->db->where(array("id"=>$id));
			$this->db->update(db_prefix() . 'invoices', $update);
			
		   $itemable= array(
			"rel_id" => $id,
			"rel_type" => "invoice",
			"description" => "Treatment Plan",
			"qty" => $units,
			"long_description"=> json_encode($get_data),
			"item_order" => $units,
			"rate"=>$paying_amount + $due_amount
			);
			
			$this->db->insert(db_prefix() . 'itemable', $itemable);
			/* 
			if($paying_amount>0){
			   $invoicepaymentrecords= array(
				"invoiceid" => $id,
				"amount" => $this->input->post('paying_amount'),
				"paymentmode" => $this->input->post('paymentmode'),
				"date" => date('Y-m-d'),
				"daterecorded" => date('Y-m-d H:i:s'),
				);
				
				$this->db->insert(db_prefix() . 'invoicepaymentrecords', $invoicepaymentrecords);
			} */
		}
		
		return true;
	}
	
	public function delete_treatment_plan($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete(db_prefix() . 'tooth_treatment_plans');
	}
	
	public function get_accepted_treatments($patient_id)
	{
		$this->db->select('DISTINCT treatment_plan, treatment', false);
		$this->db->where('patient_id', $patient_id);
		$this->db->where('is_accepted', 1);
		return $this->db->get(db_prefix() . 'tooth_treatment_plans')->result_array();
	}

    public function get_teeth_by_treatment($patient_id, $treatment)
    {
        $this->db->select('*');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('treatment', $treatment);
        $this->db->where('is_accepted', 1);
        return $this->db->get(db_prefix() . 'tooth_treatment_plans')->result_array();
    }

    public function get_procedures_by_plan_and_tooth($treatment_plan, $tooth_info)
    {
		return $this->db->get_where(db_prefix() . 'treatment_procedure')->result_array();
    }

    public function add_treatment_procedure($data)
    {
        return $this->db->insert(db_prefix() . 'tooth_treatment_procedures', $data) ? $this->db->insert_id() : false;
    }
	
	public function get_treatment_history($patient_id)
	{
		$this->db->select('
			p.tooth_info as treatment_tooth_info,
			pr.*
		');
		$this->db->from(db_prefix() . 'tooth_treatment_plans p');
		$this->db->join(db_prefix() . 'tooth_treatment_procedures pr', 'pr.treatment_plan = p.id', 'right');
		$this->db->where('p.patient_id', $patient_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	
	public function get_invoice_treatments($patient_id)
	{
		$this->db->select('i.id as invoice_id, i.hash, i.date as invoice_date, it.description, it.long_description, it.qty, it.rate');
		$this->db->from(db_prefix() . 'invoices i');
		$this->db->join(db_prefix() . 'itemable it', 'i.id = it.rel_id AND it.rel_type = "invoice"');
		$this->db->where('i.clientid', $patient_id);
		$this->db->where('it.description', 'Treatment Plan');
		$query = $this->db->get();

		$result = [];
		foreach ($query->result() as $row) {
			$entry = json_decode($row->long_description, true);

			if (!is_array($entry)) {
				continue;
			}

			$result[] = [
				'invoice_id'     => $row->invoice_id,
				'invoice_number' => format_invoice_number($row->invoice_id),
				'tooth_info'     => $entry['tooth_info'] ?? '',
				'treatment'      => $entry['treatment_plan'] ?? '',
				'progress'       => $entry['progress'] ?? 'Not Started',
				'amount'         => $entry['final_amount'] ?? '0',
				'today'          => date('Y-m-d'),
			];
		}

		return $result;
	}


    public function get_toot_invoice_payments($patient_id)
	{
		$this->db->select('p.id, p.amount, p.date, p.invoiceid, p.transactionid, i.clientid');
		$this->db->from(db_prefix() . 'invoicepaymentrecords p');
		$this->db->join(db_prefix() . 'invoices i', 'p.invoiceid = i.id');
		$this->db->join(db_prefix() . 'itemable it', 'it.rel_id = i.id AND it.rel_type = "invoice"');
		$this->db->where('i.clientid', $patient_id);
		$this->db->where('it.description', 'Treatment Plan');
		$this->db->group_by('p.id'); // To avoid duplicates if multiple items per invoice
		$query = $this->db->get();

		$data = [];
		foreach ($query->result() as $row) {
			$data[] = [
				'receipt_no'     => 'R-' . strtoupper(get_option('invoice_prefix')) . '-' . $row->invoiceid . $row->transactionid,
				'id'         => $row->id,
				'amount'         => $row->amount,
				'date_time'      => _dt($row->date),
				'invoice_number' => format_invoice_number($row->invoiceid),
			];
		}

		return $data;
	}
	
	public function insert_payment($data)
	{
		// Required fields: invoiceid, amount, paymentmode, transactionid, date
		$payment = [
			'invoiceid'     => $data['invoiceid'],
			'amount'        => $data['amount'],
			'paymentmode'   => $data['paymentmode'], // should match id from tblpayment_modes
			'transactionid' => $data['transactionid'],
			'date'          => to_sql_date($data['date'], true),
			'daterecorded'  => date('Y-m-d H:i:s'),
			'note'          => 'Payment for Treatment Plan',
			//'staffid'       => get_staff_user_id(), // Optional: track who added the payment
		];

		$this->db->insert(db_prefix() . 'invoicepaymentrecords', $payment);
		$insert_id = $this->db->insert_id();
		
		$this->db->where(array('id'=>$data['invoiceid']));
		$this->db->update(db_prefix() . 'invoices', array("status"=>3));
		

		// Update invoice status if necessary
		//$this->load->model('invoices_model');
		//$this->invoices_model->update_invoice_status($data['invoiceid']);

		return $insert_id;
	}
	
	//Lab
	public function insert_lab_work($data)
    {
        $this->db->insert(db_prefix() . 'tooth_lab_works', [
            'treatment_id'    => $data['treatment_id'],
            'tooth_info'      => $data['tooth_info'],
            'tooth_details'   => $data['tooth_details'],
            'units'           => $data['units'],
            'patient_id'      => $data['patient_id'],
            'lab_id'          => $data['lab_id'],
            'lab_work_id'     => $data['lab_work_id'],
            'lab_followup_id' => $data['lab_followup_id'],
            'case_remark_id'  => $data['case_remark_id'],
            'notes'           => $data['notes'],
            'photo'           => $data['photo'],
            'created_by'      => $data['created_by'],
            'created_at'      => $data['created_at'],
        ]);
    }

    public function get_approved_treatments($patient_id)
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . 'tooth_treatment_plans');
        $this->db->where('is_accepted', 1);
        return $this->db->get()->result_array();
    }

    public function get_lab_work_status($patient_id)
    {
        $this->db->select('lw.*, l.*, lf.*, cr.*, w.*, t.*');
        $this->db->from(db_prefix() . 'tooth_lab_works lw');
        $this->db->join(db_prefix() . 'lab l', 'l.lab_id = lw.lab_id', 'left');
        $this->db->join(db_prefix() . 'tooth_treatment_plans t', 't.id = lw.treatment_id', 'left');
        $this->db->join(db_prefix() . 'lab_work w', 'w.lab_work_id = lw.lab_work_id', 'left');
        $this->db->join(db_prefix() . 'lab_followup lf', 'lf.lab_followup_id = lw.lab_followup_id', 'left');
        $this->db->join(db_prefix() . 'case_remark cr', 'cr.case_remark_id = lw.case_remark_id', 'left');
        return $this->db->get()->result_array();
    }

    public function get_lab_work_history($patient_id)
    {
        $this->db->select('lh.*, l.*, lf.*, cr.*, s.*');
        $this->db->from(db_prefix() . 'tooth_lab_work_history lh');
        $this->db->join(db_prefix() . 'lab l', 'l.lab_id = lh.lab_id', 'left');
        $this->db->join(db_prefix() . 'lab_followup lf', 'lf.lab_followup_id = lh.lab_followup_id', 'left');
        $this->db->join(db_prefix() . 'case_remark cr', 'cr.case_remark_id = lh.case_remark_id', 'left');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = lh.changed_by', 'left');
        return $this->db->get()->result_array();
    }

	public function get_visits($patient_id){
		return $this->db->get_where(db_prefix() . 'appointment', array("visit_status"=>0))->result_array();
	}
	
	public function get_folder_counts($patient_id)
	{
		return [
        'Documents' => 0,
        'Documents > Reviews' => 6,
        'Medical Reports' => 4,
        'Medical Reports > Past' => 4,
        'Medication' => $this->db->where('patient_id', $patient_id)->count_all_results(db_prefix() . 'tooth_present_medications'),
        'Medication > Present' => $this->db->where('patient_id', $patient_id)->count_all_results(db_prefix() . 'tooth_present_medications'),
        'Treatment Procedure' => $this->db->where('patient_id', $patient_id)->where('xray_file IS NOT NULL')->count_all_results(db_prefix() . 'tooth_treatment_procedures'),
        'Examination Findings' => $this->db->where('patient_id', $patient_id)->where('images IS NOT NULL')->count_all_results(db_prefix() . 'tooth_examination_findings'),
    ];
	}

	public function get_examination_images($patient_id)
	{
		return $this->db->select('images')->from(db_prefix() . 'tooth_examination_findings')->where('patient_id', $patient_id)->get()->result_array();
	}

	public function get_present_medication_images($patient_id)
	{
		return $this->db->select('file')->from(db_prefix() . 'tooth_present_medications')->where('patient_id', $patient_id)->get()->result_array();
	}

	public function get_treatment_procedure_images($patient_id)
	{
		return $this->db->select('xray_file')->from(db_prefix() . 'tooth_treatment_procedures')->where('patient_id', $patient_id)->get()->result_array();
	}

}
