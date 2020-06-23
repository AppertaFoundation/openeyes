<?php

class m140506_161243_visual_fields extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('ophinvisualfields_strategy', array('id' => 'pk', 'name' => 'string not null'), true);
        $this->createOETable('ophinvisualfields_pattern', array('id' => 'pk', 'name' => 'string not null'), true);

        $this->createOETable(
            'ophinvisualfields_result_assessment',
            array(
            'id' => 'pk',
            'name' => 'varchar(128) NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'default' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
            'deleted' => 'tinyint(1) unsigned not null',
            'active' => 'int(11) unsigned not null',
            ),
            true
        );

        $this->createOETable('ophinvisualfields_condition_ability', array(
            'id' => 'pk',
            'name' => 'varchar(128) NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'default' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
            'deleted' => 'tinyint(1) unsigned not null',
        ), true);

        $this->createOETable(
            'ophinvisualfields_field_measurement',
            array(
                'id' => 'pk',
                'patient_measurement_id' => 'integer not null',
                'eye_id' => 'integer unsigned not null',
                'image_id' => 'integer unsigned not null',
                'cropped_image_id' => 'integer unsigned not null',
                'strategy_id' => 'integer not null',
                'pattern_id' => 'integer not null',
                'study_datetime' => 'datetime not null',
                'source' => 'text',
                'constraint ophinvisualfields_field_measurement_pm_id_fk foreign key (patient_measurement_id) references patient_measurement (id)',
                'constraint ophinvisualfields_field_measurement_im_id_fk foreign key (image_id) references protected_file (id)',
                'constraint ophinvisualfields_field_measurement_ci_id_fk foreign key (cropped_image_id) references protected_file (id)',
                'constraint ophinvisualfields_field_measurement_st_id_fk foreign key (strategy_id) references ophinvisualfields_strategy (id)',
                'constraint ophinvisualfields_field_measurement_pa_id_fk foreign key (pattern_id) references ophinvisualfields_pattern (id)',
            ),
            true
        );

        $this->createOETable(
            'et_ophinvisualfields_image',
            array(
                'id' => 'pk',
                'event_id' => 'integer unsigned not null',
                'eye_id' => 'integer unsigned not null',
                'left_field_id' => 'integer',
                'right_field_id' => 'integer',
                'constraint et_ophinvisualfields_image_event_id_fk foreign key (event_id) references event (id)',
                'constraint et_ophinvisualfields_image_lf_id_fk foreign key (left_field_id) references ophinvisualfields_field_measurement (id)',
                'constraint et_ophinvisualfields_image_rf_id_fk foreign key (right_field_id) references ophinvisualfields_field_measurement (id)',
            ),
            true
        );

        $this->createOETable(
            'et_ophinvisualfields_condition',
            array(
                'id' => 'pk',
                'event_id' => 'integer unsigned not null',
                'other' => 'text',
                'glasses' => 'boolean not null',
                'constraint et_ophinvisualfields_condition_event_id_fk foreign key (event_id) references event (id)',
            ),
            true
        );

        $this->createOETable(
            'et_ophinvisualfields_condition_ability_assignment',
            array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'ophinvisualfields_condition_ability_id' => 'int(11) NOT NULL',
            'deleted' => 'tinyint(1) unsigned not null',
            'KEY `et_ophinvisualfields_condition_ability_assignment_lmui_fk` (`last_modified_user_id`)',
            'KEY `et_ophinvisualfields_condition_ability_assignment_cui_fk` (`created_user_id`)',
            'KEY `et_ophinvisualfields_condition_ability_assignment_ele_fk` (`element_id`)',
            'KEY `et_ophinvisualfields_condition_ability_assignment_lku_fk` (`ophinvisualfields_condition_ability_id`)',
            'CONSTRAINT `et_ophinvisualfields_condition_ability_assignment_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophinvisualfields_condition` (`id`)',
            'CONSTRAINT `et_ophinvisualfields_condition_ability_assignment_lku_fk` FOREIGN KEY (`ophinvisualfields_condition_ability_id`) REFERENCES `ophinvisualfields_condition_ability` (`id`)',
            ),
            true
        );

        $this->createOETable(
            'et_ophinvisualfields_comments',
            array(
                'id' => 'pk',
                'event_id' => 'integer unsigned not null',
                'comments' => 'text not null',
                'constraint et_ophinvisualfields_comments_event_id_fk foreign key (event_id) references event (id)',
            ),
            true
        );

        $this->createOETable(
            'et_ophinvisualfields_result',
            array(
                'id' => 'pk',
                'event_id' => 'integer unsigned not null',
                'other' => 'text',
                'constraint et_ophinvisualfields_result_event_id_fk foreign key (event_id) references event (id)',
            ),
            true
        );

        $this->createOETable(
            'et_ophinvisualfields_result_assessment_assignment',
            array(
                'id' => 'pk',
                'element_id' => 'int(11) NOT NULL',
                'ophinvisualfields_result_assessment_id' => 'int(11) NOT NULL',
                'deleted' => 'tinyint(1) unsigned not null',
                'KEY `et_ophinvisualfields_result_assessment_assignment_ele_fk` (`element_id`)',
                'KEY `et_ophinvisualfields_result_assessment_assignment_lku_fk` (`ophinvisualfields_result_assessment_id`)',
                'CONSTRAINT `et_ophinvisualfields_result_ass_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophinvisualfields_result` (`id`)',
                'CONSTRAINT `et_ophinvisualfields_result_ass_lku_fk` FOREIGN KEY (`ophinvisualfields_result_assessment_id`) REFERENCES `ophinvisualfields_result_assessment` (`id`)',
            ),
            true
        );

        $event_type_id = $this->insertOEEventType('Visual Fields', 'OphInVisualfields', 'In');
        $this->insertOEElementType(
            array(
                'Element_OphInVisualfields_Image' => array('name' => 'Image', 'required' => true),
                'Element_OphInVisualfields_Condition' => array('name' => 'Condition', 'required' => true),
                'Element_OphInVisualfields_Comments' => array('name' => 'Comments', 'required' => true),
                'Element_OphInVisualfields_Result' => array('name' => 'Result', 'required' => true),
            ),
            $event_type_id
        );

        $this->insert('episode_summary_item', array('event_type_id' => $event_type_id, 'name' => 'Visual Fields History'));

        $this->initialiseData(__DIR__);
    }

    public function safeDown()
    {
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = ?', array('OphInVisualFields'))->queryScalar();

        $this->delete('episode_summary_item', 'event_type_id = ? and name = ?', array($event_type_id, 'Visual Fields History'));

        $this->delete('element_type', 'event_type_id = ?', array($event_type_id));
        $this->delete('event_type', 'id = ?', array($event_type_id));

        $this->dropTable('et_ophinvisualfields_image');
        $this->dropTable('et_ophinvisualfields_image_version');
        $this->dropTable('et_ophinvisualfields_condition');
        $this->dropTable('et_ophinvisualfields_condition_version');
        $this->dropTable('et_ophinvisualfields_comments');
        $this->dropTable('et_ophinvisualfields_comments_version');
        $this->dropTable('et_ophinvisualfields_result');
        $this->dropTable('et_ophinvisualfields_result_version');

        $this->dropTable('ophinvisualfields_field_measurement');
        $this->dropTable('ophinvisualfields_field_measurement_version');

        $this->dropTable('ophinvisualfields_strategy');
        $this->dropTable('ophinvisualfields_strategy_version');
        $this->dropTable('ophinvisualfields_pattern');
        $this->dropTable('ophinvisualfields_pattern_version');
        $this->dropTable('ophinvisualfields_ability');
        $this->dropTable('ophinvisualfields_ability_version');
        $this->dropTable('ophinvisualfields_assessment');
        $this->dropTable('ophinvisualfields_assessment_version');
    }
}
