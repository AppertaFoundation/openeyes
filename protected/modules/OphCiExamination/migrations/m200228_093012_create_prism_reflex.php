<?php

class m200228_093012_create_prism_reflex extends OEMigration
{
    protected const GROUP_NAME = 'Orthoptic Testing';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\PrismReflex';

    public function safeUp()
    {
        $this->createElementType('OphCiExamination', 'Prism Reflex Test', [
            'class_name' => self::ELEMENT_CLS_NAME,
            'display_order' => 402,
            'group_name' => self::GROUP_NAME
        ]);

        $this->createOETable(
            'et_ophciexamination_prismreflex',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'comments' => 'text',
            ], true);

        $this->addForeignKey('et_ophciexamination_prismreflex_ev_fk',
            'et_ophciexamination_prismreflex',
            'event_id',
            'event',
            'id');

        $this->createOETable(
            'ophciexamination_prismreflex_prismdioptre',
            [
                'id' => 'pk',
                'name' => 'varchar(31)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ], true);

        $this->createOETable(
            'ophciexamination_prismreflex_prismbase',
            [
                'id' => 'pk',
                'name' => 'varchar(31)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ], true);

        $this->createOETable(
            'ophciexamination_prismreflex_finding',
            [
                'id' => 'pk',
                'name' => 'varchar(63)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ], true);

        $this->initialiseData(dirname(__FILE__));

        $this->createOETable(
            'ophciexamination_prismreflex_entry',
            [
                'id' => 'pk',
                'element_id' => 'int(11)',
                'correctiontype_id' => 'int(11)',
                'prismdioptre_id' => 'int(11)',
                'prismbase_id' => 'int(11)',
                'finding_id' => 'int(11)',
                'with_head_posture' => 'boolean'
            ], true);

        $this->addForeignKey('et_ophciexamination_prismreflex_entry_el_fk',
            'ophciexamination_prismreflex_entry',
            'element_id',
            'et_ophciexamination_prismreflex',
            'id');

        $this->addForeignKey('et_ophciexamination_prismreflex_entry_ct_fk',
            'ophciexamination_prismreflex_entry',
            'correctiontype_id',
            'ophciexamination_correctiontype',
            'id');

        $this->addForeignKey('et_ophciexamination_prismreflex_entry_pd_fk',
            'ophciexamination_prismreflex_entry',
            'prismdioptre_id',
            'ophciexamination_prismreflex_prismdioptre',
            'id');

        $this->addForeignKey('et_ophciexamination_prismreflex_entry_pb_fk',
            'ophciexamination_prismreflex_entry',
            'prismbase_id',
            'ophciexamination_prismreflex_prismbase',
            'id');

        $this->addForeignKey('et_ophciexamination_prismreflex_entry_fd_fk',
            'ophciexamination_prismreflex_entry',
            'finding_id',
            'ophciexamination_prismreflex_finding',
            'id');

        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Prism Reflex',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function safeDown()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropOETable('ophciexamination_prismreflex_entry', true);

        $this->dropOETable('ophciexamination_prismreflex_finding', true);

        $this->dropOETable('ophciexamination_prismreflex_prismbase', true);

        $this->dropOETable('ophciexamination_prismreflex_prismdioptre', true);

        $this->dropOETable('et_ophciexamination_prismreflex', true);

        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);
    }
}