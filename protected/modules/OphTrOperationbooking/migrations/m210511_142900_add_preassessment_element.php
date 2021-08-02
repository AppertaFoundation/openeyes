<?php

class m210511_142900_add_preassessment_element extends OEMigration
{
    public function up()
    {
        $this->createElementType('OphTrOperationbooking', 'Pre-Assessment', array(
            'class_name' => 'Element_OphTrOperationbooking_PreAssessment',
            'display_order' => 50,
            'default' => 1,
            'required' => 1
        ));

        $this->createOETable('et_ophtroperationbooking_preassessment', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'type_id' => 'int(10) DEFAULT NULL',
            'location_id' => 'int(10) DEFAULT NULL',
        ), true);

        $this->addForeignKey('et_ophtroperationbooking_preassessment_event_fk', 'et_ophtroperationbooking_preassessment', 'event_id', 'event', 'id');
        $this->addForeignKey('et_ophtroperationbooking_preassessment_type_fk', 'et_ophtroperationbooking_preassessment', 'type_id', 'ophtroperationbooking_preassessment_type', 'id');
        $this->addForeignKey('et_ophtroperationbooking_preassessment_location_fk', 'et_ophtroperationbooking_preassessment', 'location_id', 'ophtroperationbooking_preassessment_location', 'id');

        $operation_table = $this->dbConnection->schema->getTable('et_ophtroperationbooking_operation', true);
        if (isset($operation_table->columns['preassessment_booking_required'])) {
            $this->execute("
                INSERT INTO et_ophtroperationbooking_preassessment (event_id, type_id)
                SELECT
                    event_id,
                    CASE preassessment_booking_required
                    WHEN 0 THEN (SELECT id FROM ophtroperationbooking_preassessment_type WHERE name = 'None')
                    ELSE (SELECT id FROM ophtroperationbooking_preassessment_type WHERE name = 'Face-to-face')
                END as type_id
                FROM et_ophtroperationbooking_operation

            ");
            $this->dropOEColumn('et_ophtroperationbooking_operation', 'preassessment_booking_required', true);
        }
    }

    public function down()
    {
        if ($pre_assessments = $this->dbConnection->createCommand("SELECT event_id, type_id  FROM et_ophtroperationbooking_preassessment")->queryAll()) {
            $this->addOEColumn('et_ophtroperationbooking_operation', 'preassessment_booking_required', 'TINYINT NOT NULL DEFAULT 0');

            $none_pre_assessment_type_id = $this->dbConnection->createCommand()->select('id')->from('ophtroperationbooking_preassessment_type')->where('name=:name', array(':name' => 'None'))->queryScalar();

            $this->execute("
                UPDATE et_ophtroperationbooking_operation as op
                lEFT JOIN et_ophtroperationbooking_preassessment as pa ON op.event_id = pa.event_id
                SET preassessment_booking_required = (
                    CASE
                        WHEN pa.type_id = $none_pre_assessment_type_id then 0 ELSE 1
                    END
                )
                WHERE event_id = pa.event_id
            ");
        }

        $this->dropForeignKey('et_ophtroperationbooking_preassessment_location_fk', 'et_ophtroperationbooking_preassessment');
        $this->dropForeignKey('et_ophtroperationbooking_preassessment_type_fk', 'et_ophtroperationbooking_preassessment');
        $this->dropForeignKey('et_ophtroperationbooking_preassessment_event_fk', 'et_ophtroperationbooking_preassessment');
        $this->dropOETable('et_ophtroperationbooking_preassessment');
        $this->dropOETable('et_ophtroperationbooking_preassessment_version');
        $this->deleteElementType('OphTrOperationbooking', 'Element_OphTrOperationbooking_PreAssessment');
    }
}
