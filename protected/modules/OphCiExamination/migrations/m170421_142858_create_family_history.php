<?php

class m170421_142858_create_family_history extends OEMigration
{
	public function up()
	{
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name',
            array(':class_name' => 'OphCiExamination'))->queryScalar();

        $element_types = array(
            'OEModule\OphCiExamination\models\FamilyHistory' => array(
                'name' => 'Family History',
                'display_order' => 23,
                'parent_element_type_id' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_History',
            ),
        );

        $this->insertOEElementType($element_types, $event_type_id);

        $this->createOETable('et_ophciexamination_familyhistory', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'no_family_history_date' => 'datetime',
        ), true);

        $this->addForeignKey('et_ophciexamination_familyhistory_ev_fk',
            'et_ophciexamination_familyhistory', 'event_id', 'event', 'id');

        $this->createOETable('ophciexamination_familyhistory_entry', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'relative_id' => 'int(10) unsigned',
            'side_id' => 'int(10) unsigned',
            'condition_id' => 'int(10) unsigned',
            'comments' => 'varchar(1024)',
            'other_relative' => 'varchar(255)',
            'other_condition' => 'varchar(255)'
        ), true);

        $this->addForeignKey('ophciexamination_familyhistory_entry_el_fk',
            'ophciexamination_familyhistory_entry', 'element_id', 'et_ophciexamination_familyhistory', 'id');

        $this->addForeignKey('ophciexamination_familyhistory_entry_rel_fk',
            'ophciexamination_familyhistory_entry', 'relative_id', 'family_history_relative', 'id');

        $this->addForeignKey('ophciexamination_familyhistory_entry_side_fk',
            'ophciexamination_familyhistory_entry', 'side_id', 'family_history_side', 'id');

        $this->addForeignKey('ophciexamination_familyhistory_entry_con_fk',
            'ophciexamination_familyhistory_entry', 'condition_id', 'family_history_condition', 'id');

    }

    public function down()
    {
        $this->dropOETable('ophciexamination_familyhistory_entry', true);
        $this->dropOETable('et_ophciexamination_familyhistory', true);
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name',
            array(':class_name' => 'OphCiExamination'))->queryScalar();
        $this->delete('element_type', 'class_name = :class_name AND event_type_id = :eid',
            array(':class_name' => 'OEModule\OphCiExamination\models\FamilyHistory', ':eid' => $event_type_id));
    }

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}