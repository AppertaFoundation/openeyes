<?php

class m210601_010958_add_examination_pain_tables extends OEMigration
{
    public function up()
    {
        $this->createOETable('et_ophciexamination_pain', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'display_order' => 'int(10)',
        ), true);
        $this->addForeignKey(
            'et_ophciexamination_pain_event_fk',
            'et_ophciexamination_pain',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable('ophciexamination_pain_entry', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'pain_score' => 'int(3) unsigned NOT NULL',
            'comment' => 'varchar(255)',
            'datetime' => 'datetime NOT NULL',
        ), true);
        $this->addForeignKey(
            'ophciexamination_pain_entry_element_fk',
            'ophciexamination_pain_entry',
            'element_id',
            'et_ophciexamination_pain',
            'id'
        );

        $connection = $this->getDbConnection();
        $examination_event_type_id = $connection->createCommand("SELECT id FROM event_type WHERE name = 'Examination'")->queryScalar();
        $element_group_id = $connection->createCommand("SELECT id FROM element_group WHERE name = 'History'")->queryScalar();

        $this->insertOEElementType(array(
            'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_Pain' => array(
                'name' => 'Pain',
                'default' => 0,
                'required' => 0,
                'element_group_id' => $element_group_id,
            ),
        ), $examination_event_type_id);
    }

    public function down()
    {
        $this->delete('element_type', 'class_name = ? ', array('OEModule\\OphCiExamination\\models\\Element_OphCiExamination_Pain'));
        $this->dropForeignKey('ophciexamination_pain_entry_element_fk', 'ophciexamination_pain_entry');
        $this->dropOETable('ophciexamination_pain_entry', true);
        $this->dropForeignKey('et_ophciexamination_pain_event_fk', 'et_ophciexamination_pain');
        $this->dropOETable('et_ophciexamination_pain', true);
    }
}
