<?php

class m190404_143100_create_general_surgical_history_element extends OEMigration
{
    public function safeUp()
    {
        $this->execute("UPDATE element_type SET `name` = 'Ophthalmic Surgical History' WHERE `class_name` = 'OEModule\\\\OphCiExamination\\\\models\\\\PastSurgery'");

        $this->createElementType('OphCiExamination', 'Systemic Surgical History', array(
            'class_name' => 'OEModule\OphCiExamination\models\SystemicSurgery',
            'display_order' => 50,
            'group_name' => 'History'
        ));

        $this->createOETable('et_ophciexamination_systemicsurgery', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
            'comments' => 'text'
        ), true);

        $this->addForeignKey(
            'et_ophciexamination_systemicsurgery_ev_fk',
            'et_ophciexamination_systemicsurgery',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable('et_ophciexamination_systemicsurgery_op', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'side_id' => 'int(10) unsigned',
            'operation' => 'varchar(1024) NOT NULL',
            'date' => 'varchar(10)',
            'had_operation' => 'tinyint(1) NOT NULL DEFAULT -9'
        ), true);

        $this->addForeignKey(
            'et_ophciexamination_systemicsurgery_op_el_fk',
            'et_ophciexamination_systemicsurgery_op',
            'element_id',
            'et_ophciexamination_systemicsurgery',
            'id'
        );

        $this->addForeignKey(
            'et_ophciexamination_systemicsurgery_op_side_fk',
            'et_ophciexamination_systemicsurgery_op',
            'side_id',
            'eye',
            'id'
        );

        $this->createOETable('common_previous_systemic_operation', [
            'id' => 'pk',
            'name' => 'varchar(1024) NOT NULL',
            'display_order' => 'tinyint(1) unsigned NOT NULL',

        ], true);

        foreach (['Heart bypass', 'Chemotherapy', 'Coronary artery bypass graft', 'Appendicectomy', 'Carotid enderterectomy', 'Mastectomy'] as $index => $operation) {
            $this->insert('common_previous_systemic_operation', ['name' => $operation, 'display_order' => $index]);
        }

        $this->createOETable('ophciexamination_systemic_surgery_set', [
            'id' => 'pk',
            'name' => 'varchar(255) NULL',
            'firm_id' => 'int(10) unsigned',
            'subspecialty_id' =>  'int(10) unsigned',
        ], true);

        $this->addForeignKey('systemic_surgery_set_subspecialty', 'ophciexamination_systemic_surgery_set', 'subspecialty_id', 'subspecialty', 'id');
        $this->addForeignKey('systemic_surgery_set_firm', 'ophciexamination_systemic_surgery_set', 'firm_id', 'firm', 'id');

        $this->createOETable('ophciexamination_systemic_surgery_set_entry', [
            'id' => 'pk',
            'set_id' => 'int(11)',
            'operation' => 'varchar(1024)',
            'gender' => 'varchar(1) NULL',
            'age_min' => 'int(3) unsigned',
            'age_max' => 'int(3) unsigned',
        ], true);

        $this->addForeignKey('systemic_surgery_set_entry_set_fk', 'ophciexamination_systemic_surgery_set_entry', 'set_id', 'ophciexamination_systemic_surgery_set', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('systemic_surgery_set_entry_set_fk', 'ophciexamination_systemic_surgery_set_entry');
        $this->dropOETable('ophciexamination_systemic_surgery_set_entry', true);
        $this->dropForeignKey('systemic_surgery_set_subspecialty', 'ophciexamination_systemic_surgery_set');
        $this->dropForeignKey('systemic_surgery_set_firm', 'ophciexamination_systemic_surgery_set');
        $this->dropOETable('ophciexamination_systemic_surgery_set', true);

        $this->dropOETable('common_previous_systemic_operation', true);
        $this->dropOETable('et_ophciexamination_systemicsurgery_op', true);
        $this->dropOETable('et_ophciexamination_systemicsurgery', true);
        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where(
            'class_name = :class_name',
            array(':class_name' => 'OphCiExamination')
        )->queryScalar();
        $element_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where(
                'class_name = :class_name AND event_type_id = :eid',
                array(':class_name' => 'OEModule\OphCiExamination\models\SystemicSurgery', ':eid' => $event_type_id)
            )
            ->queryScalar();
        $this->delete(
            'ophciexamination_element_set_item',
            'element_type_id = :element_type_id',
            array(':element_type_id' => $element_type_id)
        );
        $this->delete(
            'element_type',
            'id = :id',
            array(':id' => $element_type_id)
        );

        $this->execute("UPDATE element_type SET `name` = 'Surgical History' WHERE `class_name` = 'OEModule\\\\OphCiExamination\\\\models\\\\SystemicSurgery'");
    }
}
