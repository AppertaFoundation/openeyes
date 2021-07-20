<?php

class m210324_230728_create_drug_admin_event_tables extends OEMigration
{
    private const NEW_ELEMENT_NAME = 'Drug Administration';
    public function safeUp()
    {
        $exam_event_type_id = $this->getIdOfEventTypeByClassName('OphCiExamination');

        // ophdrpgdpsd_assignment_comment START
        $this->createOETable('ophdrpgdpsd_assignment_comment', array(
            'id' => 'pk',
            'comment' => 'text',
            'commented_by' => 'int(10) unsigned',
        ), true);
        $this->addForeignKey('ophdrpgdpsd_assignment_comment_user_id_fk', 'ophdrpgdpsd_assignment_comment', 'commented_by', 'user', 'id');
        // ophdrpgdpsd_assignment_comment END

        // ophdrpgdpsd_assignment START
        $this->createOETable('ophdrpgdpsd_assignment', array(
            'id' => 'pk',
            'patient_id' => 'int(10) unsigned NOT NULL',
            'visit_id' => 'int(10) NULL', // worklist_patient_id
            'pgdpsd_id' => 'int(11) DEFAULT NULL',
            'institution_id' => 'int(10) unsigned NOT NULL',
            'status' => 'tinyint(1) DEFAULT 1',
            'comment_id' => 'int(11) NULL',
        ), true);
        $this->addForeignKey('ophdrpgdpsd_assignment_patient_id_fk', 'ophdrpgdpsd_assignment', 'patient_id', 'patient', 'id');
        $this->addForeignKey('ophdrpgdpsd_assignment_pgdpsd_id_fk', 'ophdrpgdpsd_assignment', 'pgdpsd_id', 'ophdrpgdpsd_pgdpsd', 'id');
        $this->addForeignKey('ophdrpgdpsd_assignment_worklist_patient_id_fk', 'ophdrpgdpsd_assignment', 'visit_id', 'worklist_patient', 'id');
        $this->addForeignKey('ophdrpgdpsd_assignment_comment_id_fk', 'ophdrpgdpsd_assignment', 'comment_id', 'ophdrpgdpsd_assignment_comment', 'id');
        $this->addForeignKey('ophdrpgdpsd_assignment_institution_id_fk', 'ophdrpgdpsd_assignment', 'institution_id', 'institution', 'id');
        // ophdrpgdpsd_assignment END

        // ophdrpgdpsd_assignment_meds START
        $this->createOETable('ophdrpgdpsd_assignment_meds', array(
            'id' => 'pk',
            'pair_key' => 'varchar(20) DEFAULT NULL',
            'assignment_id' => 'int(11) NOT NULL',
            'medication_id' => 'int(11) NOT NULL',
            'dose' => 'smallint NOT NULL',
            'dose_unit_term' => 'varchar(255) NOT NULL',
            'route_id' => 'int(11) NOT NULL',
            'laterality' => 'int(11) NULL',
            'administered' => 'tinyint(1) DEFAULT 0',
            'administered_time' => 'datetime DEFAULT NULL',
            'administered_by' => 'int(10) unsigned DEFAULT NULL',
            'administered_id' => 'int(11) DEFAULT NULL',
        ), true);
        $this->addForeignKey('ophdrpgdpsd_assignment_meds_assignment_id_fk', 'ophdrpgdpsd_assignment_meds', 'assignment_id', 'ophdrpgdpsd_assignment', 'id');
        $this->addForeignKey('ophdrpgdpsd_assignment_meds_med_id_fk', 'ophdrpgdpsd_assignment_meds', 'medication_id', 'medication', 'id');
        $this->addForeignKey('ophdrpgdpsd_assignment_meds_route_id_fk', 'ophdrpgdpsd_assignment_meds', 'route_id', 'medication_route', 'id');
        $this->addForeignKey('ophdrpgdpsd_assignment_meds_laterality_fk', 'ophdrpgdpsd_assignment_meds', 'laterality', 'medication_laterality', 'id');
        $this->addForeignKey('ophdrpgdpsd_assignment_meds_administer_user_fk', 'ophdrpgdpsd_assignment_meds', 'administered_by', 'user', 'id');
        $this->addForeignKey('ophdrpgdpsd_assignment_meds_administered_id_fk', 'ophdrpgdpsd_assignment_meds', 'administered_id', 'event_medication_use', 'id');
        // ophdrpgdpsd_assignment_meds END

        // max display_order in element_group for exam
        $drop_ele_group_display_order = $this->dbConnection->createCommand()
            ->select('MAX(display_order)')
            ->from('element_group')
            ->where('event_type_id = :event_type_id AND name = :name', array(':event_type_id' => $exam_event_type_id, ':name' => 'Drops'))
            ->queryScalar();
        // max display_order in element_type for exam
        $drop_ele = $this->dbConnection->createCommand()
            ->select('id, display_order')
            ->from('element_type')
            ->where('event_type_id = :event_type_id AND name = :name', array(':event_type_id' => $exam_event_type_id, ':name' => 'Drops'))
            ->queryRow();

        // et_drug_administration START
        $this->createOETable('et_drug_administration', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'type' => 'varchar(10)'
        ), true);
        $this->addForeignKey('et_drug_admin_event_id_fk', 'et_drug_administration', 'event_id', 'event', 'id');
        // et_drug_administration END

        // et_drug_administration START
        $this->createOETable('et_drug_administration_assignments', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'assignment_id' => 'int(11) NOT NULL'
        ), true);
        $this->addForeignKey('et_drug_administration_assignments_element_id_fk', 'et_drug_administration_assignments', 'element_id', 'et_drug_administration', 'id');
        $this->addForeignKey('et_drug_administration_assignments_assignment_id_fk', 'et_drug_administration_assignments', 'assignment_id', 'ophdrpgdpsd_assignment', 'id');
        // et_drug_administration END

        $max_stop_reason_display_order = $this->dbConnection->createCommand()
            ->select('MAX(display_order)')
            ->from('ophciexamination_medication_stop_reason')
            ->queryScalar();

        $this->insert('ophciexamination_medication_stop_reason', array('name' => 'Single administration', 'display_order' => $max_stop_reason_display_order + 1));
        // insert new stop reason END

        // creat new event type
        $event_type_id = $this->insertOEEventType(self::NEW_ELEMENT_NAME, 'OphDrPGDPSD', 'Dr');

        $this->insert(
            'element_group',
            array(
                'name'=>self::NEW_ELEMENT_NAME,
                'event_type_id'=>$exam_event_type_id,
                'display_order'=>$drop_ele_group_display_order
            )
        );

        $this->insert(
            'element_group',
            array(
                'name'=>self::NEW_ELEMENT_NAME,
                'event_type_id'=>$event_type_id,
                'display_order'=>10
            )
        );


        $examination_drug_admin_element_group_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_group')
            ->where(
                'name = :name AND event_type_id = :event_type_id',
                array(':name' => self::NEW_ELEMENT_NAME,':event_type_id'=>$exam_event_type_id)
            )->queryScalar();

        $et_drug_admin_element_group_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_group')
            ->where(
                'name = :name AND event_type_id = :event_type_id',
                array(':name' => self::NEW_ELEMENT_NAME, ':event_type_id'=>$event_type_id)
            )->queryScalar();


        $da_exam_ele = $this->insertOEElementType(
            array(
                'OEModule\OphCiExamination\models\Element_OphCiExamination_DrugAdministration' =>
                array(
                    'name' => self::NEW_ELEMENT_NAME,
                    'display_order' => $drop_ele['display_order'],
                    'default' => 0,
                    'required' => 0,
                    'element_group_id'=>$examination_drug_admin_element_group_id
                )
            ),
            $exam_event_type_id
        );

        $this->insertOEElementType(
            array(
                'Element_DrugAdministration' =>
                array(
                    'name' => self::NEW_ELEMENT_NAME,
                    'display_order' => 10,
                    'default' => 1,
                    'required' => 1,
                    'element_group_id'=>$et_drug_admin_element_group_id
                )
            ),
            $this->getIdOfEventTypeByClassName('OphDrPGDPSD')
        );
        $this->update(
            'ophciexamination_element_set_item',
            array(
                'element_type_id' => $da_exam_ele[0],
            ),
            "element_type_id = :drop_ele",
            array(
                ':drop_ele' => $drop_ele['id'],
            )
        );
        $this->insert('index_search', [
            'event_type_id' => $exam_event_type_id,
            'primary_term' => 'Drug Administration',
            'secondary_term_list' => 'drops, oral meds',
            'open_element_class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_DrugAdministration',
        ]);
        $this->delete(
            'index_search',
            'open_element_class_name = :oecn',
            array(
                ':oecn' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Dilation',
            )
        );
    }

    public function safeDown()
    {

        $exam_event_type_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $exam_da_ele_id = $this->dbConnection->createCommand()
        ->select('id')
        ->from('element_type')
        ->where(
            'name = :name AND event_type_id = :event_type_id',
            array(':name' => self::NEW_ELEMENT_NAME, ':event_type_id'=>$exam_event_type_id)
        )->queryScalar();
        $drop_ele_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('event_type_id = :event_type_id AND name = :name', array(':event_type_id' => $exam_event_type_id, ':name' => 'Drops'))
            ->queryScalar();

        $this->update(
            'ophciexamination_element_set_item',
            array(
                'element_type_id' => $drop_ele_id,
            ),
            "element_type_id = :drop_ele",
            array(
                ':drop_ele' => $exam_da_ele_id,
            )
        );
        $this->delete(
            'index_search',
            'open_element_class_name = :oecn',
            array(
                ':oecn' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_DrugAdministration',
            )
        );
        $this->insert('index_search', [
            'event_type_id' => $exam_event_type_id,
            'primary_term' => 'Drops',
            'secondary_term_list' => 'Dilation',
            'open_element_class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Dilation',
            'goto_id' => 'dilation_drug_%position',
        ]);
        // delete Drug Administration element type, element group
        $this->delete('element_type', 'name = ?', array(self::NEW_ELEMENT_NAME));
        $this->delete('element_group', 'name = ?', array(self::NEW_ELEMENT_NAME));
        // delete pgdpsd event type
        $this->delete('event_type', 'class_name = ?', array('OphDrPGDPSD'));

        // delete Single administration stop reason
        $this->delete('ophciexamination_medication_stop_reason', 'LOWER(name) = ?', array('single administration'));
        // drop et_drug_administration -> event foreign key
        $this->dropForeignKey('et_drug_admin_event_id_fk', 'et_drug_administration');

        // drop ophdrpgdpsd_assignment_comment foreign keys
        $this->dropForeignKey('ophdrpgdpsd_assignment_comment_user_id_fk', 'ophdrpgdpsd_assignment_comment');

        // drop ophdrpgdpsd_assignment foreign keys
        $this->dropForeignKey('ophdrpgdpsd_assignment_patient_id_fk', 'ophdrpgdpsd_assignment');
        $this->dropForeignKey('ophdrpgdpsd_assignment_pgdpsd_id_fk', 'ophdrpgdpsd_assignment');
        $this->dropForeignKey('ophdrpgdpsd_assignment_worklist_patient_id_fk', 'ophdrpgdpsd_assignment');
        $this->dropForeignKey('ophdrpgdpsd_assignment_comment_id_fk', 'ophdrpgdpsd_assignment');
        $this->dropForeignKey('ophdrpgdpsd_assignment_institution_id_fk', 'ophdrpgdpsd_assignment');

        // drop ophdrpgdpsd_assignment_meds foreign keys
        $this->dropForeignKey('ophdrpgdpsd_assignment_meds_assignment_id_fk', 'ophdrpgdpsd_assignment_meds');
        $this->dropForeignKey('ophdrpgdpsd_assignment_meds_med_id_fk', 'ophdrpgdpsd_assignment_meds');
        $this->dropForeignKey('ophdrpgdpsd_assignment_meds_route_id_fk', 'ophdrpgdpsd_assignment_meds');
        $this->dropForeignKey('ophdrpgdpsd_assignment_meds_laterality_fk', 'ophdrpgdpsd_assignment_meds');
        $this->dropForeignKey('ophdrpgdpsd_assignment_meds_administer_user_fk', 'ophdrpgdpsd_assignment_meds');
        $this->dropForeignKey('ophdrpgdpsd_assignment_meds_administered_id_fk', 'ophdrpgdpsd_assignment_meds');

        // drop et_drug_administration_assignments foreign keys
        $this->dropForeignKey('et_drug_administration_assignments_element_id_fk', 'et_drug_administration_assignments');
        $this->dropForeignKey('et_drug_administration_assignments_assignment_id_fk', 'et_drug_administration_assignments');
        // drop et_drug_administration table
        $this->dropOETable('ophdrpgdpsd_assignment_comment', true);
        $this->dropOETable('et_drug_administration_assignments', true);
        $this->dropOETable('et_drug_administration', true);
        $this->dropOETable('ophdrpgdpsd_assignment_meds', true);
        $this->dropOETable('ophdrpgdpsd_assignment', true);
    }
}
