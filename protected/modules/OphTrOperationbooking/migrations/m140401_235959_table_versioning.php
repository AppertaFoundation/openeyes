<?php

class m140401_235959_table_versioning extends OEMigration
{
    public function up()
    {
        $this->alterColumn('ophtroperationbooking_operation_erod', 'session_id', 'int unsigned null');

        $this->addColumn('ophtroperationbooking_operation_cancellation_reason', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationbooking_operation_ward', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationbooking_scheduleope_schedule_options', 'active', 'boolean not null default true');

        $this->addColumn('ophtroperationbooking_operation_theatre', 'active', 'boolean not null default true');
        $this->update('ophtroperationbooking_operation_theatre', array('active' => new CDbExpression('not(deleted)')));
        $this->dropColumn('ophtroperationbooking_operation_theatre', 'deleted');

        $this->versionExistingTable('et_ophtroperationbooking_diagnosis');
        $this->versionExistingTable('et_ophtroperationbooking_operation');
        $this->versionExistingTable('et_ophtroperationbooking_scheduleope');
        $this->versionExistingTable('ophtroperationbooking_admission_letter_warning_rule');
        $this->versionExistingTable('ophtroperationbooking_admission_letter_warning_rule_type');
        $this->versionExistingTable('ophtroperationbooking_letter_contact_rule');
        $this->versionExistingTable('ophtroperationbooking_operation_booking');
        $this->versionExistingTable('ophtroperationbooking_operation_cancellation_reason');
        $this->versionExistingTable('ophtroperationbooking_operation_date_letter_sent');
        $this->versionExistingTable('ophtroperationbooking_operation_erod');
        $this->versionExistingTable('ophtroperationbooking_operation_erod_rule');
        $this->versionExistingTable('ophtroperationbooking_operation_erod_rule_item');
        $this->versionExistingTable('ophtroperationbooking_operation_name_rule');
        $this->versionExistingTable('ophtroperationbooking_operation_priority');
        $this->versionExistingTable('ophtroperationbooking_operation_procedures_procedures');
        $this->versionExistingTable('ophtroperationbooking_operation_sequence');
        $this->versionExistingTable('ophtroperationbooking_operation_sequence_interval');
        $this->versionExistingTable('ophtroperationbooking_operation_session');
        $this->versionExistingTable('ophtroperationbooking_operation_status');
        $this->versionExistingTable('ophtroperationbooking_operation_theatre');
        $this->versionExistingTable('ophtroperationbooking_operation_ward');
        $this->versionExistingTable('ophtroperationbooking_scheduleope_schedule_options');
        $this->versionExistingTable('ophtroperationbooking_waiting_list_contact_rule');
        $this->versionExistingTable('ophtroperationbooking_operation_session_unavailreason');

        $cb = new OECommandBuilder($this->dbConnection->schema);

        $deleted_session_ids = $this->dbConnection->createCommand()->select('id')->from('ophtroperationbooking_operation_session')->where('deleted = 1')->queryColumn();
        $batches = array_chunk($deleted_session_ids, 256);

        foreach ($batches as $batch) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('session_id', $batch);
            $cb->createInsertFromTableCommand('ophtroperationbooking_operation_booking_version', 'ophtroperationbooking_operation_booking', $criteria)->execute();
            $this->update('ophtroperationbooking_operation_booking', array('session_id' => null), $criteria->condition, $criteria->params);
            $cb->createInsertFromTableCommand('ophtroperationbooking_operation_erod_version', 'ophtroperationbooking_operation_erod', $criteria)->execute();
            $this->update('ophtroperationbooking_operation_erod', array('session_id' => null), $criteria->condition, $criteria->params);
        }

        $criteria = new CDbCriteria();
        $criteria->compare('deleted', true);
        $cb->createInsertFromTableCommand('ophtroperationbooking_operation_session_version', 'ophtroperationbooking_operation_session', $criteria)->execute();
        $cb->createInsertFromTableCommand('ophtroperationbooking_operation_sequence_version', 'ophtroperationbooking_operation_sequence', $criteria)->execute();
        $this->delete('ophtroperationbooking_operation_session', 'deleted = 1');
        $this->delete('ophtroperationbooking_operation_sequence', 'deleted = 1');

        $this->dropColumn('ophtroperationbooking_operation_sequence', 'deleted');
        $this->dropColumn('ophtroperationbooking_operation_sequence_version', 'deleted');
        $this->dropColumn('ophtroperationbooking_operation_session', 'deleted');
        $this->dropColumn('ophtroperationbooking_operation_session_version', 'deleted');
    }

    public function down()
    {
        $this->addColumn('ophtroperationbooking_operation_sequence', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");
        $this->addColumn('ophtroperationbooking_operation_session', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");

        $this->dropTable('et_ophtroperationbooking_diagnosis_version');
        $this->dropTable('et_ophtroperationbooking_operation_version');
        $this->dropTable('et_ophtroperationbooking_scheduleope_version');
        $this->dropTable('ophtroperationbooking_admission_letter_warning_rule_version');
        $this->dropTable('ophtroperationbooking_admission_letter_warning_rule_type_version');
        $this->dropTable('ophtroperationbooking_letter_contact_rule_version');
        $this->dropTable('ophtroperationbooking_operation_booking_version');
        $this->dropTable('ophtroperationbooking_operation_cancellation_reason_version');
        $this->dropTable('ophtroperationbooking_operation_date_letter_sent_version');
        $this->dropTable('ophtroperationbooking_operation_erod_version');
        $this->dropTable('ophtroperationbooking_operation_erod_rule_version');
        $this->dropTable('ophtroperationbooking_operation_erod_rule_item_version');
        $this->dropTable('ophtroperationbooking_operation_name_rule_version');
        $this->dropTable('ophtroperationbooking_operation_priority_version');
        $this->dropTable('ophtroperationbooking_operation_procedures_procedures_version');
        $this->dropTable('ophtroperationbooking_operation_sequence_version');
        $this->dropTable('ophtroperationbooking_operation_sequence_interval_version');
        $this->dropTable('ophtroperationbooking_operation_session_version');
        $this->dropTable('ophtroperationbooking_operation_status_version');
        $this->dropTable('ophtroperationbooking_operation_theatre_version');
        $this->dropTable('ophtroperationbooking_operation_ward_version');
        $this->dropTable('ophtroperationbooking_scheduleope_schedule_options_version');
        $this->dropTable('ophtroperationbooking_waiting_list_contact_rule_version');
        $this->dropTable('ophtroperationbooking_operation_session_unavailreason_version');

        $this->addColumn('ophtroperationbooking_operation_session', 'deleted', "tinyint(1) DEFAULT '0'");
        $this->update('ophtroperationbooking_operation_session', array('deleted' => new CDbExpression('not(active)')));
        $this->dropColumn('ophtroperationbooking_operation_session', 'active');

        $this->addColumn('ophtroperationbooking_operation_sequence', 'deleted', "tinyint(1) DEFAULT '0'");
        $this->update('ophtroperationbooking_operation_sequence', array('deleted' => new CDbExpression('not(active)')));
        $this->dropColumn('ophtroperationbooking_operation_sequence', 'active');

        $this->addColumn('ophtroperationbooking_operation_theatre', 'deleted', "tinyint(1) DEFAULT '0'");
        $this->update('ophtroperationbooking_operation_theatre', array('deleted' => new CDbExpression('not(active)')));
        $this->dropColumn('ophtroperationbooking_operation_theatre', 'active');

        $this->dropColumn('ophtroperationbooking_operation_cancellation_reason', 'active');
        $this->dropColumn('ophtroperationbooking_operation_ward', 'active');
        $this->dropColumn('ophtroperationbooking_scheduleope_schedule_options', 'active');
    }
}
