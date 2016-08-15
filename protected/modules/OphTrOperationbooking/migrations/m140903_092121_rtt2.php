<?php

class m140903_092121_rtt2 extends OEMigration
{
    public function safeUp()
    {
        $this->createOeTable(
            'ophtroperationbooking_anaesthetic_choice',
            array(
                'id' => 'pk',
                'name' => 'string not null',
                'display_order' => 'integer not null',
            )
        );

        $this->addColumn('et_ophtroperationbooking_operation', 'organising_admission_user_id', 'integer unsigned');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'organising_admission_user_id', 'integer unsigned');

        $this->addColumn('et_ophtroperationbooking_operation', 'anaesthetist_preop_assessment', 'boolean');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'anaesthetist_preop_assessment', 'boolean');

        $this->addColumn('et_ophtroperationbooking_operation', 'anaesthetic_choice_id', 'integer');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'anaesthetic_choice_id', 'integer');

        $this->addForeignKey('et_ophtroperationbooking_operation_anaesthetic_choice_fk', 'et_ophtroperationbooking_operation', 'anaesthetic_choice_id', 'ophtroperationbooking_anaesthetic_choice', 'id');

        $this->addColumn('et_ophtroperationbooking_operation', 'stop_medication', 'boolean');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'stop_medication', 'boolean');

        $this->addColumn('et_ophtroperationbooking_operation', 'stop_medication_details', 'string');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'stop_medication_details', 'string');

        $this->addColumn('et_ophtroperationbooking_operation', 'special_equipment', 'boolean');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'special_equipment', 'boolean');

        $this->addColumn('et_ophtroperationbooking_operation', 'special_equipment_details', 'string');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'special_equipment_details', 'string');

        $this->addColumn('et_ophtroperationbooking_operation', 'senior_fellow_to_do', 'boolean');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'senior_fellow_to_do', 'boolean');

        $this->addColumn('et_ophtroperationbooking_operation', 'fast_track', 'boolean');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'fast_track', 'boolean');

        $this->addColumn('et_ophtroperationbooking_operation', 'fast_track_discussed_with_patient', 'boolean');
        $this->addColumn('et_ophtroperationbooking_operation_version', 'fast_track_discussed_with_patient', 'boolean');

        $this->alterColumn('et_ophtroperationbooking_scheduleope', 'schedule_options_id', 'integer unsigned not null');

        $this->initialiseData(__DIR__);
    }

    public function safeDown()
    {
        $this->alterColumn('et_ophtroperationbooking_scheduleope', 'schedule_options_id', "int(10) unsigned NOT NULL DEFAULT '1'");

        $this->dropColumn('et_ophtroperationbooking_operation', 'organising_admission_user_id');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'organising_admission_user_id');

        $this->dropColumn('et_ophtroperationbooking_operation', 'anaesthetist_preop_assessment');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'anaesthetist_preop_assessment');

        $this->dropForeignKey('et_ophtroperationbooking_operation_anaesthetic_choice_fk', 'et_ophtroperationbooking_operation');

        $this->dropColumn('et_ophtroperationbooking_operation', 'anaesthetic_choice_id');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'anaesthetic_choice_id');

        $this->dropColumn('et_ophtroperationbooking_operation', 'stop_medication');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'stop_medication');

        $this->dropColumn('et_ophtroperationbooking_operation', 'stop_medication_details');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'stop_medication_details');

        $this->dropColumn('et_ophtroperationbooking_operation', 'special_equipment');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'special_equipment');

        $this->dropColumn('et_ophtroperationbooking_operation', 'special_equipment_details');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'special_equipment_details');

        $this->dropColumn('et_ophtroperationbooking_operation', 'senior_fellow_to_do');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'senior_fellow_to_do');

        $this->dropColumn('et_ophtroperationbooking_operation', 'fast_track');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'fast_track');

        $this->dropColumn('et_ophtroperationbooking_operation', 'fast_track_discussed_with_patient');
        $this->dropColumn('et_ophtroperationbooking_operation_version', 'fast_track_discussed_with_patient');

        $this->dropOeTable('ophtroperationbooking_anaesthetic_choice');
    }
}
