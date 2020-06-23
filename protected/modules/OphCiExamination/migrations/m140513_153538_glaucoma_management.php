<?php

class m140513_153538_glaucoma_management extends OEMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $element_types = array(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan' => array('name' => 'Overall Management Plan', 'parent_element_type_id' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Management'),
            'OEModule\OphCiExamination\models\Element_OphCiExamination_CurrentManagementPlan' => array('name' => 'Current Management Plan', 'parent_element_type_id' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Management'),
        );

        $this->insertOEElementType($element_types, $event_type['id']);
        $this->createOETable(
            'ophciexamination_overallperiod',
            array('id' => 'pk', 'name' => 'text', 'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1'),
            true
        );

        $this->createOETable(
            'ophciexamination_managementglaucomastatus',
            array('id' => 'pk', 'name' => 'text', 'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1'),
            true
        );

        $this->createOETable(
            'ophciexamination_managementrelproblem',
            array('id' => 'pk', 'name' => 'text', 'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1'),
            true
        );

        $this->createOETable(
            'ophciexamination_managementdrops',
            array('id' => 'pk', 'name' => 'text', 'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1'),
            true
        );

        $this->createOETable(
            'ophciexamination_managementsurgery',
            array('id' => 'pk', 'name' => 'text',    'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1'),
            true
        );

        $this->createOETable('et_ophciexamination_overallmanagementplan', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'left_target_iop' => 'int(10) DEFAULT NULL',

            'right_target_iop' => 'int(10) DEFAULT NULL',

            'left_gonio_id' => 'int DEFAULT NULL',

            'right_gonio_id' => 'int DEFAULT NULL',

            'clinic_internal_id' => 'int DEFAULT NULL',

            'photo_id' => 'int DEFAULT NULL',

            'oct_id' => 'int DEFAULT NULL',

            'hfa_id' => 'int DEFAULT NULL',

            'comments' => 'text COLLATE utf8_bin DEFAULT \'\'',

            'eye_id' => 'int(10) unsigned NOT NULL DEFAULT \'3\'',
            'KEY `et_ophciexam_overallmanagementplan_ev_fk` (`event_id`)',
            'KEY `et_ophciexam_overallmanagementplan_lgonio_id_fk` (`left_gonio_id`)',
            'KEY `et_ophciexam_overallmanagementplan_rgonio_id_fk` (`right_gonio_id`)',
            'KEY `et_ophciexam_overallmanagementplan_clinic_internal_id_fk` (`clinic_internal_id`)',
            'KEY `et_ophciexam_overallmanagementplan_photo_id_fk` (`photo_id`)',
            'KEY `et_ophciexam_overallmanagementplan_oct_id_fk` (`oct_id`)',
            'KEY `et_ophciexam_overallmanagementplan_hfa_id_fk` (`hfa_id`)',

            'CONSTRAINT `et_ophciexam_overallmanagementplan_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
            'CONSTRAINT `et_ophciexam_overallmanagementplan_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
            'CONSTRAINT `et_ophciexam_overallmanagementplan_lgonio_id_fk` FOREIGN KEY (`left_gonio_id`) REFERENCES `ophciexamination_overallperiod` (`id`)',
            'CONSTRAINT `et_ophciexam_overallmanagementplan_rgonio_id_fk` FOREIGN KEY (`right_gonio_id`) REFERENCES `ophciexamination_overallperiod` (`id`)',
            'CONSTRAINT `et_ophciexam_overallmanagementplan_clinic_internal_id_fk` FOREIGN KEY (`clinic_internal_id`) REFERENCES `ophciexamination_overallperiod` (`id`)',
            'CONSTRAINT `et_ophciexam_overallmanagementplan_photo_id_fk` FOREIGN KEY (`photo_id`) REFERENCES `ophciexamination_overallperiod` (`id`)',
            'CONSTRAINT `et_ophciexam_overallmanagementplan_oct_id_fk` FOREIGN KEY (`oct_id`) REFERENCES `ophciexamination_overallperiod` (`id`)',
            'CONSTRAINT `et_ophciexam_overallmanagementplan_hfa_id_fk` FOREIGN KEY (`hfa_id`) REFERENCES `ophciexamination_overallperiod` (`id`)',

        ), true);

        $this->createOETable('et_ophciexamination_currentmanagementplan', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',

            'left_glaucoma_status_id' => 'int DEFAULT NULL',

            'right_glaucoma_status_id' => 'int DEFAULT NULL',

            'left_drop-related_prob_id' => 'int DEFAULT NULL',

            'right_drop-related_prob_id' => 'int DEFAULT NULL',

            'left_drops_id' => 'int DEFAULT NULL',

            'right_drops_id' => 'int DEFAULT NULL',

            'left_surgery_id' => 'int DEFAULT NULL',

            'right_surgery_id' => 'int DEFAULT NULL',

            'left_other-service' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_other-service' => 'tinyint(1) unsigned DEFAULT NULL',

            'left_refraction' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_refraction' => 'tinyint(1) unsigned DEFAULT NULL',

            'left_lva' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_lva' => 'tinyint(1) unsigned DEFAULT NULL',

            'left_orthoptics' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_orthoptics' => 'tinyint(1) unsigned DEFAULT NULL',

            'left_cl_clinic' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_cl_clinic' => 'tinyint(1) unsigned DEFAULT NULL',

            'left_vf' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_vf' => 'tinyint(1) unsigned DEFAULT NULL',

            'left_us' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_us' => 'tinyint(1) unsigned DEFAULT NULL',

            'left_biometry' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_biometry' => 'tinyint(1) unsigned DEFAULT NULL',

            'left_oct' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_oct' => 'tinyint(1) unsigned DEFAULT NULL',

            'left_hrt' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_hrt' => 'tinyint(1) unsigned DEFAULT NULL',

            'left_disc_photos' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_disc_photos' => 'tinyint(1) unsigned DEFAULT NULL',

            'left_edt' => 'tinyint(1) unsigned DEFAULT NULL',

            'right_edt' => 'tinyint(1) unsigned DEFAULT NULL',

            'eye_id' => 'int(10) unsigned NOT NULL DEFAULT \'3\'',
            'KEY `et_ophciexam_currentmanagementplan_ev_fk` (`event_id`)',
            'KEY `et_ophciexam_currentmanagementplan_lglaucoma_status_id_fk` (`left_glaucoma_status_id`)',
            'KEY `et_ophciexam_currentmanagementplan_rglaucoma_status_id_fk` (`right_glaucoma_status_id`)',
            'KEY `et_ophciexam_currentmanagementplan_ldrop-related_prob_id_fk` (`left_drop-related_prob_id`)',
            'KEY `et_ophciexam_currentmanagementplan_rdrop-related_prob_id_fk` (`right_drop-related_prob_id`)',
            'KEY `et_ophciexam_currentmanagementplan_ldrops_id_fk` (`left_drops_id`)',
            'KEY `et_ophciexam_currentmanagementplan_rdrops_id_fk` (`right_drops_id`)',
            'KEY `et_ophciexam_currentmanagementplan_lsurgery_id_fk` (`left_surgery_id`)',
            'KEY `et_ophciexam_currentmanagementplan_rsurgery_id_fk` (`right_surgery_id`)',
            'CONSTRAINT `et_ophciexam_currentmanagementplan_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
            'CONSTRAINT `et_ophciexam_currentmanagementplan_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
            'CONSTRAINT `et_ophciexam_currentmanagementplan_lglaucoma_status_id_fk` FOREIGN KEY (`left_glaucoma_status_id`) REFERENCES `ophciexamination_managementglaucomastatus` (`id`)',
            'CONSTRAINT `et_ophciexam_currentmanagementplan_rglaucoma_status_id_fk` FOREIGN KEY (`right_glaucoma_status_id`) REFERENCES `ophciexamination_managementglaucomastatus` (`id`)',
            'CONSTRAINT `et_ophciexam_currentmanagementplan_ldrop-related_prob_id_fk` FOREIGN KEY (`left_drop-related_prob_id`) REFERENCES `ophciexamination_managementrelproblem` (`id`)',
            'CONSTRAINT `et_ophciexam_currentmanagementplan_rdrop-related_prob_id_fk` FOREIGN KEY (`right_drop-related_prob_id`) REFERENCES `ophciexamination_managementrelproblem` (`id`)',
            'CONSTRAINT `et_ophciexam_currentmanagementplan_ldrops_id_fk` FOREIGN KEY (`left_drops_id`) REFERENCES `ophciexamination_managementdrops` (`id`)',
            'CONSTRAINT `et_ophciexam_currentmanagementplan_rdrops_id_fk` FOREIGN KEY (`right_drops_id`) REFERENCES `ophciexamination_managementdrops` (`id`)',
            'CONSTRAINT `et_ophciexam_currentmanagementplan_lsurgery_id_fk` FOREIGN KEY (`left_surgery_id`) REFERENCES `ophciexamination_managementsurgery` (`id`)',
            'CONSTRAINT `et_ophciexam_currentmanagementplan_rsurgery_id_fk` FOREIGN KEY (`right_surgery_id`) REFERENCES `ophciexamination_managementsurgery` (`id`)',
        ), true);

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);
    }

    public function down()
    {
        $this->dropTable('et_ophciexamination_overallmanagementplan');
        $this->dropTable('et_ophciexamination_overallmanagementplan_version');
        $this->dropTable('et_ophciexamination_currentmanagementplan');
        $this->dropTable('et_ophciexamination_currentmanagementplan_version');
        $this->dropTable('ophciexamination_overallperiod');
        $this->dropTable('ophciexamination_overallperiod_version');
        $this->dropTable('ophciexamination_managementglaucomastatus');
        $this->dropTable('ophciexamination_managementglaucomastatus_version');
        $this->dropTable('ophciexamination_managementrelproblem');
        $this->dropTable('ophciexamination_managementrelproblem_version');
        $this->dropTable('ophciexamination_managementdrops');
        $this->dropTable('ophciexamination_managementdrops_version');
        $this->dropTable('ophciexamination_managementsurgery');
        $this->dropTable('ophciexamination_managementsurgery_version');

        $this->delete('element_type', 'class_name = ?', array('OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan'));
        $this->delete('element_type', 'class_name = ?', array('OEModule\OphCiExamination\models\Element_OphCiExamination_CurrentManagementPlan'));
    }
}
