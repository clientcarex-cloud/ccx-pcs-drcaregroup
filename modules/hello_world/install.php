<?php

defined('BASEPATH') or exit('No direct script access allowed');

function hello_world_install()
{
    $CI = &get_instance();

    if (!$CI->db->table_exists(db_prefix() . 'hello_world')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . "hello_world` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `message` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");
    }
}

function hello_world_uninstall()
{
    $CI = &get_instance();
    $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . "hello_world`;");
}
