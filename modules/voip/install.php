<?php

defined('BASEPATH') or exit('No direct script access allowed');
$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'voip_settings')) {
    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "voip_settings` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `staffid` INT(11) DEFAULT NULL,
            `username` VARCHAR(100) DEFAULT NULL,
            `password` VARCHAR(100) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
    ");
}

    
   


