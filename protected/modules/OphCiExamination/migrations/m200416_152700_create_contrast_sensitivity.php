<?php

class m200416_152700_create_contrast_sensitivity extends OEMigration
{
    protected const GROUP_NAME = 'Visual Function';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\ContrastSensitivity';
    public function safeUp()
    {
        $this->createElementType(
            'OphCiExamination',
            'Contrast Sensitivity',
            [
                'class_name' => self::ELEMENT_CLS_NAME,
                'display_order' => 175,
                'group_name' => self::GROUP_NAME
            ]
        );

        $this->createOETable(
            'et_ophciexamination_contrastsensitivity',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'comments' => 'text',
            ],
            true
        );

        $this->addForeignKey(
            'et_ophciexamination_contrastsensitivity_ev_fk',
            'et_ophciexamination_contrastsensitivity',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable(
            'ophciexamination_contrastsensitivity_type',
            [
                'id' => 'pk',
                'name' => 'varchar(63)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ],
            true
        );

        $this->initialiseData(dirname(__FILE__));

        $this->createOETable(
            'ophciexamination_contrastsensitivity_result',
            [
                'id' => 'pk',
                'element_id' => 'int(11)',
                'eye_id' => 'int(11)',
                'contrastsensitivity_type_id' => 'int(11)',
                'value' => 'int(11)',
                'correctiontype_id' => 'int(11)'
            ],
            true
        );

        $this->addForeignKey(
            'ophciexamination_contrastsensitivity_result_el_fk',
            'ophciexamination_contrastsensitivity_result',
            'element_id',
            'et_ophciexamination_contrastsensitivity',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_contrastsensitivity_result_ct_fk',
            'ophciexamination_contrastsensitivity_result',
            'correctiontype_id',
            'ophciexamination_correctiontype',
            'id'
        );

        $this->addForeignKey(
            'ophciexamination_contrastsensitivity_result_ty_fk',
            'ophciexamination_contrastsensitivity_result',
            'contrastsensitivity_type_id',
            'ophciexamination_contrastsensitivity_type',
            'id'
        );

        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Contrast Sensitivity',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function safeDown()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropOETable('ophciexamination_contrastsensitivity_result', true);
        $this->dropOETable('ophciexamination_contrastsensitivity_type', true);
        $this->dropOETable('et_ophciexamination_contrastsensitivity', true);
        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);
    }
}
