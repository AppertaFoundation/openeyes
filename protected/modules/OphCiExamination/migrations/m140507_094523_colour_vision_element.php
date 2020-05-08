<?php

class m140507_094523_colour_vision_element extends OEMigration
{
    public function up()
    {
        $event_type_id = $this->insertOEEventType('Examination', 'OphCiExamination', 'Ci');
        $this->insertOEElementType(array('OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision' => array(
                                'name' => 'Colour Vision',
                                'parent_element_type_id' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity',
                                'display_order' => 10,
                                'required' => false,
                        )), $event_type_id);

        $this->createTable('ophciexamination_colourvision_method', array(
                'id' => 'pk',
                'name' => 'string NOT NULL',
                'active' => 'boolean NOT NULL DEFAULT true',
                'display_order' => 'integer NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned DEFAULT 1',
                'last_modified_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
                'created_user_id' => 'int(10) unsigned  DEFAULT 1',
                'created_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
        ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey(
            'ophciexamination_colourvision_method_lmui_fk',
            'ophciexamination_colourvision_method',
            'last_modified_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_colourvision_method_cui_fk',
            'ophciexamination_colourvision_method',
            'created_user_id',
            'user',
            'id'
        );

        $this->createTable('ophciexamination_colourvision_value', array(
                'id' => 'pk',
                'name' => 'string NOT NULL',
                'active' => 'boolean NOT NULL DEFAULT true',
                'display_order' => 'integer NOT NULL',
                'method_id' => 'integer NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned DEFAULT 1',
                'last_modified_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
                'created_user_id' => 'int(10) unsigned  DEFAULT 1',
                'created_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
        ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey(
            'ophciexamination_colourvision_value_lmui_fk',
            'ophciexamination_colourvision_value',
            'last_modified_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_colourvision_value_cui_fk',
            'ophciexamination_colourvision_value',
            'created_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_colourvision_value_mui_fk',
            'ophciexamination_colourvision_value',
            'method_id',
            'ophciexamination_colourvision_method',
            'id'
        );

        $this->createTable('et_ophciexamination_colourvision', array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3',
                'last_modified_user_id' => 'int(10) unsigned DEFAULT 1',
                'last_modified_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
                'created_user_id' => 'int(10) unsigned  DEFAULT 1',
                'created_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
        ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey(
            'et_ophciexamination_colourvision_lmui_fk',
            'et_ophciexamination_colourvision',
            'last_modified_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'et_ophciexamination_colourvision_cui_fk',
            'et_ophciexamination_colourvision',
            'created_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'et_ophciexamination_colourvision_event_id_fk',
            'et_ophciexamination_colourvision',
            'event_id',
            'event',
            'id'
        );
        $this->addForeignKey(
            'et_ophciexamination_colourvision_eye_id_fk',
            'et_ophciexamination_colourvision',
            'eye_id',
            'eye',
            'id'
        );

        $this->createTable('ophciexamination_colourvision_reading', array(
                'id' => 'pk',
                'element_id' => 'integer NOT NULL',
                'eye_id' => 'int(10) unsigned NOT NULL',
                'value_id' => 'integer NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned DEFAULT 1',
                'last_modified_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
                'created_user_id' => 'int(10) unsigned  DEFAULT 1',
                'created_date' => "datetime DEFAULT '1900-01-01 00:00:00'",
        ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey(
            'ophciexamination_colourvision_reading_lmui_fk',
            'ophciexamination_colourvision_reading',
            'last_modified_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_colourvision_reading_cui_fk',
            'ophciexamination_colourvision_reading',
            'created_user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_colourvision_reading_elui_fk',
            'ophciexamination_colourvision_reading',
            'element_id',
            'et_ophciexamination_colourvision',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_colourvision_reading_eye_id_fk',
            'ophciexamination_colourvision_reading',
            'eye_id',
            'eye',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_colourvision_reading_vui_fk',
            'ophciexamination_colourvision_reading',
            'value_id',
            'ophciexamination_colourvision_value',
            'id'
        );

        $this->versionExistingTable('ophciexamination_colourvision_method');
        $this->versionExistingTable('ophciexamination_colourvision_value');
        $this->versionExistingTable('et_ophciexamination_colourvision');
        $this->versionExistingTable('ophciexamination_colourvision_reading');

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);
    }

    public function down()
    {
        $this->dropTable('ophciexamination_colourvision_reading_version');
        $this->dropTable('et_ophciexamination_colourvision_version');
        $this->dropTable('ophciexamination_colourvision_value_version');
        $this->dropTable('ophciexamination_colourvision_method_version');
        $this->dropTable('ophciexamination_colourvision_reading');
        $this->dropTable('et_ophciexamination_colourvision');
        $this->dropTable('ophciexamination_colourvision_value');
        $this->dropTable('ophciexamination_colourvision_method');
        $this->delete('element_type', 'class_name = ?', array('OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision'));
    }
}
