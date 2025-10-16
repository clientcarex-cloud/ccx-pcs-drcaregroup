<?php

defined('BASEPATH') or exit('No direct script access allowed');

function toot_install()
{
    $CI = &get_instance();

	if (!$CI->db->table_exists(db_prefix() . 'tooth_chart')) {
		$CI->db->query("CREATE TABLE `" . db_prefix() . "tooth_chart` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`tooth_number` varchar(10) NOT NULL,
			`tooth_type` varchar(50) NOT NULL,
			`quadrant` int(1) NOT NULL,
			`display_order` int(2) NOT NULL,
			`dentition_type` enum('adult','child') NOT NULL DEFAULT 'adult',
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	}
	if (!$CI->db->table_exists(db_prefix() . 'chief_complaint')) {
		$CI->db->query("
			CREATE TABLE `" . db_prefix() . "chief_complaint` (
				`chief_complaint_id` INT(11) NOT NULL AUTO_INCREMENT,
				`chief_complaint_name` VARCHAR(255) NOT NULL,
				`chief_complaint_status` TINYINT(1) DEFAULT 1,
				PRIMARY KEY (`chief_complaint_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
		");
	}
	
	if (!$CI->db->table_exists(db_prefix() . 'tooth_chief_complaints')) {
		$CI->db->query("
			CREATE TABLE `" . db_prefix() . "tooth_chief_complaints` (
				`tooth_chief_complaint_id` INT(11) NOT NULL AUTO_INCREMENT,
				`patient_id` INT(11) NOT NULL,
				`tooth_id` VARCHAR(10) NOT NULL,
				`display_id` VARCHAR(10) DEFAULT NULL,
				`surfaces` TEXT DEFAULT NULL,
				`complaint` TEXT DEFAULT NULL,
				`notes` TEXT DEFAULT NULL,
				`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`tooth_chief_complaint_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
		");
	}



	if (!$CI->db->table_exists(db_prefix() . 'tooth_examination_findings')) {
		$CI->db->query("CREATE TABLE `" . db_prefix() . "tooth_examination_findings` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`patient_id` int(11) NOT NULL,
			`tooth_info` text NOT NULL,
			`complaint` text NOT NULL,
			`notes` text NOT NULL,
			`created_at` datetime DEFAULT current_timestamp(),
			`images` tinytext DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	}
	
	if (!$CI->db->table_exists(db_prefix() . 'tooth_investigations')) {
		$CI->db->query("CREATE TABLE `" . db_prefix() . "tooth_investigations` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`patient_id` int(11) NOT NULL,
			`type` enum('medical','dental') NOT NULL,
			`problem` varchar(255) NOT NULL,
			`notes` text DEFAULT NULL,
			`created_at` datetime DEFAULT current_timestamp(),
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	}
	
	if (!$CI->db->table_exists(db_prefix() . 'tooth_past_dental_history')) {
		$CI->db->query("CREATE TABLE `" . db_prefix() . "tooth_past_dental_history` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`patient_id` int(11) NOT NULL,
			`complaint` varchar(255) DEFAULT NULL,
			`notes` text DEFAULT NULL,
			`place` varchar(255) DEFAULT NULL,
			`opinion` text DEFAULT NULL,
			`teeth_data` text DEFAULT NULL,
			`created_at` datetime DEFAULT current_timestamp(),
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	}
	
	if (!$CI->db->table_exists(db_prefix() . 'tooth_prescription_medicines')) {
		$CI->db->query("CREATE TABLE `" . db_prefix() . "tooth_prescription_medicines` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`prescription_id` int(11) NOT NULL,
			`medicine_name` varchar(255) NOT NULL,
			`frequency` varchar(50) DEFAULT NULL,
			`duration` varchar(50) DEFAULT NULL,
			`usage` varchar(100) DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	}
	
	if (!$CI->db->table_exists(db_prefix() . 'tooth_prescriptions')) {
		$CI->db->query("CREATE TABLE `" . db_prefix() . "tooth_prescriptions` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`patient_id` int(11) NOT NULL,
			`prescription_code` varchar(100) NOT NULL,
			`notes` text DEFAULT NULL,
			`prescription_by` int(11) NOT NULL,
			`created_at` datetime DEFAULT current_timestamp(),
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	}
	
	if (!$CI->db->table_exists(db_prefix() . 'tooth_present_medications')) {
		$CI->db->query("CREATE TABLE `" . db_prefix() . "tooth_present_medications` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`patient_id` int(11) NOT NULL,
			`file` varchar(255) DEFAULT NULL,
			`notes` text DEFAULT NULL,
			`created_at` datetime DEFAULT current_timestamp(),
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	}
	
	if (!$CI->db->table_exists(db_prefix() . 'tooth_medical_problems')) {
		$CI->db->query("CREATE TABLE `" . db_prefix() . "tooth_medical_problems` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`patient_id` int(11) NOT NULL,
			`problem_name` varchar(255) NOT NULL,
			`notes` text DEFAULT NULL,
			`created_at` datetime DEFAULT current_timestamp(),
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	}
	
	if (!$CI->db->table_exists(db_prefix() . 'tooth_treatment_plans')) {
		$CI->db->query("CREATE TABLE `" . db_prefix() . "tooth_treatment_plans` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`patient_id` int(11) NOT NULL,
			`treatment_plan` varchar(50) DEFAULT NULL,
			`treatment` varchar(100) DEFAULT NULL,
			`company_price` decimal(10,2) DEFAULT NULL,
			`units` int(11) DEFAULT NULL,
			`company_cost` decimal(10,2) DEFAULT NULL,
			`final_amount` decimal(10,2) DEFAULT NULL,
			`tooth_info` text DEFAULT NULL,
			`treatment_status` varchar(50) DEFAULT NULL,
			`plan_type` enum('A','B','C') NOT NULL,
			`is_accepted` tinyint(1) DEFAULT 0,
			`created_at` datetime DEFAULT current_timestamp(),
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	}
	
	if (!$CI->db->field_exists('treatment_status', db_prefix() . 'tooth_treatment_plans')) {
		$CI->db->query("ALTER TABLE `" . db_prefix() . "tooth_treatment_plans` ADD COLUMN `treatment_status` VARCHAR(50) DEFAULT NULL AFTER `tooth_info`;");
	}

	
	if (!$CI->db->table_exists(db_prefix() . 'tooth_treatment_procedures')) {
		$CI->db->query("CREATE TABLE `" . db_prefix() . "tooth_treatment_procedures` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`patient_id` int(11) NOT NULL,
			`treatment_plan` varchar(100) NOT NULL,
			`treatment` varchar(100) NOT NULL,
			`tooth_info` varchar(50) NOT NULL,
			`procedure` varchar(100) NOT NULL,
			`procedure_notes` text DEFAULT NULL,
			`further_procedure` text DEFAULT NULL,
			`treatment_doctor` varchar(100) DEFAULT NULL,
			`xray_file` varchar(255) DEFAULT NULL,
			`next_appointment_date` datetime DEFAULT NULL,
			`next_appointment_doctor` varchar(100) DEFAULT NULL,
			`lab_followup_date` datetime DEFAULT NULL,
			`created_at` timestamp NOT NULL DEFAULT current_timestamp(),
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	}
	
	if (!$CI->db->table_exists(db_prefix() . 'tooth_lab_work_history')) {
		$CI->db->query("
			CREATE TABLE `" . db_prefix() . "tooth_lab_work_history` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`lab_work_id` int(11) DEFAULT NULL,
				`patient_id` int(11) DEFAULT NULL,
				`status_type` varchar(50) DEFAULT NULL,
				`old_status` varchar(50) DEFAULT NULL,
				`new_status` varchar(50) DEFAULT NULL,
				`notes` text DEFAULT NULL,
				`changed_by` int(11) DEFAULT NULL,
				`changed_at` datetime DEFAULT NULL,
				`lab_id` int(11) DEFAULT NULL,
				`lab_work_id_ref` int(11) DEFAULT NULL,
				`lab_followup_id` int(11) DEFAULT NULL,
				`case_remark_id` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
		");
	}

	if (!$CI->db->table_exists(db_prefix() . 'tooth_lab_works')) {
		$CI->db->query("
			CREATE TABLE `" . db_prefix() . "tooth_lab_works` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`treatment_id` int(11) DEFAULT NULL,
				`tooth_info` varchar(255) DEFAULT NULL,
				`tooth_details` text DEFAULT NULL,
				`units` int(11) DEFAULT NULL,
				`patient_id` int(11) DEFAULT NULL,
				`lab_id` int(11) DEFAULT NULL,
				`lab_work_id` int(11) DEFAULT NULL,
				`lab_followup_id` int(11) DEFAULT NULL,
				`case_remark_id` int(11) DEFAULT NULL,
				`notes` text DEFAULT NULL,
				`photo` varchar(255) DEFAULT NULL,
				`created_by` int(11) DEFAULT NULL,
				`created_at` datetime DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
		");
	}




	if ($CI->db->table_exists(db_prefix() . 'tooth_chart')) {
		$CI->db->query("TRUNCATE TABLE `" . db_prefix() . "tooth_chart`;");


		// Insert data for Adult Teeth
		$CI->db->query("
			INSERT INTO `" . db_prefix() . "tooth_chart` (`id`, `tooth_number`, `tooth_type`, `quadrant`, `display_order`, `dentition_type`) VALUES
			(1,'18','upper-molar-tooth.svg',1,1,'adult'),
			(2,'17','upper-molar-tooth.svg',1,2,'adult'),
			(3,'16','upper-molar-tooth.svg',1,3,'adult'),
			(4,'15','upper-premolar.svg',1,4,'adult'),
			(5,'14','upper-premolar.svg',1,5,'adult'),
			(6,'13','upper-canine-tooth.svg',1,6,'adult'),
			(7,'12','upper-incisors-tooth.svg',1,7,'adult'),
			(8,'11','upper-incisors-tooth.svg',1,8,'adult');
		");

		$CI->db->query("
			INSERT INTO `" . db_prefix() . "tooth_chart` (`id`, `tooth_number`, `tooth_type`, `quadrant`, `display_order`, `dentition_type`) VALUES
			(9,'21','upper-incisors-tooth.svg',2,1,'adult'),
			(10,'22','upper-incisors-tooth.svg',2,2,'adult'),
			(11,'23','upper-canine-tooth.svg',2,3,'adult'),
			(12,'24','upper-premolar.svg',2,4,'adult'),
			(13,'25','upper-premolar.svg',2,5,'adult'),
			(14,'26','upper-molar-tooth.svg',2,6,'adult'),
			(15,'27','upper-molar-tooth.svg',2,7,'adult'),
			(16,'28','upper-molar-tooth.svg',2,8,'adult');
		");

		$CI->db->query("
			INSERT INTO `" . db_prefix() . "tooth_chart` (`id`, `tooth_number`, `tooth_type`, `quadrant`, `display_order`, `dentition_type`) VALUES
			(17,'48','tooth.svg',4,1,'adult'),
			(18,'47','tooth.svg',4,2,'adult'),
			(19,'46','tooth.svg',4,3,'adult'),
			(20,'45','premolar.svg',4,4,'adult'),
			(21,'44','premolar.svg',4,5,'adult'),
			(22,'43','canine-tooth.svg',4,6,'adult'),
			(23,'42','incisors-tooth.svg',4,7,'adult'),
			(24,'41','incisors-tooth.svg',4,8,'adult');
		");

		$CI->db->query("
			INSERT INTO `" . db_prefix() . "tooth_chart` (`id`, `tooth_number`, `tooth_type`, `quadrant`, `display_order`, `dentition_type`) VALUES
			(25,'31','incisors-tooth.svg',3,1,'adult'),
			(26,'32','incisors-tooth.svg',3,2,'adult'),
			(27,'33','canine-tooth.svg',3,3,'adult'),
			(28,'34','premolar.svg',3,4,'adult'),
			(29,'35','premolar.svg',3,5,'adult'),
			(30,'36','tooth.svg',3,6,'adult'),
			(31,'37','tooth.svg',3,7,'adult'),
			(32,'38','tooth.svg',3,8,'adult');
		");

		// Insert data for Child Teeth (using the IDs and order provided in your last query)
		$CI->db->query("
			INSERT INTO `" . db_prefix() . "tooth_chart` (`id`,`tooth_number`,`tooth_type`,`quadrant`,`display_order`,`dentition_type`) VALUES
			(53,'A','upper-incisors-tooth.svg',2,1,'child'),
			(54,'B','upper-incisors-tooth.svg',2,2,'child'),
			(55,'C','upper-canine-tooth.svg',2,3,'child'),
			(56,'D','upper-premolar.svg',2,4,'child'),
			(57,'E','upper-premolar.svg',2,5,'child');
		");

		$CI->db->query("
			INSERT INTO `" . db_prefix() . "tooth_chart` (`id`,`tooth_number`,`tooth_type`,`quadrant`,`display_order`,`dentition_type`) VALUES
			(58,'A','incisors-tooth.svg',4,5,'child'),
			(59,'B','incisors-tooth.svg',4,4,'child'),
			(60,'C','canine-tooth.svg',4,3,'child'),
			(61,'D','premolar.svg',4,2,'child'),
			(62,'E','premolar.svg',4,1,'child');
		");

		$CI->db->query("
			INSERT INTO `" . db_prefix() . "tooth_chart` (`id`,`tooth_number`,`tooth_type`,`quadrant`,`display_order`,`dentition_type`) VALUES
			(63,'A','incisors-tooth.svg',3,1,'child'),
			(64,'B','incisors-tooth.svg',3,2,'child'),
			(65,'C','canine-tooth.svg',3,3,'child'),
			(66,'D','premolar.svg',3,4,'child'),
			(67,'E','premolar.svg',3,5,'child');
		");

		$CI->db->query("
			INSERT INTO `" . db_prefix() . "tooth_chart` (`id`,`tooth_number`,`tooth_type`,`quadrant`,`display_order`,`dentition_type`) VALUES
			(68,'E','upper-premolar.svg',1,1,'child'),
			(69,'D','upper-premolar.svg',1,2,'child'),
			(70,'C','upper-canine-tooth.svg',1,3,'child'),
			(71,'B','upper-incisors-tooth.svg',1,4,'child'),
			(72,'A','upper-incisors-tooth.svg',1,5,'child');
		");
	}




}

function toot_uninstall()
{
    $CI = &get_instance();
	

}
