<?php

class m190321_141548_unbooked_worklist_data_migration extends OEMigration
{

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
        $this->addColumn('event', 'worklist_patient_id', 'INT(11) DEFAULT NULL');
        $this->addColumn('event_version', 'worklist_patient_id', 'INT(11) DEFAULT NULL');

        $dataProvider = new CActiveDataProvider('Event');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('t.pas_visit_id IS NOT NULL');
        $dataProvider->setCriteria($criteria);

        $iterator = new CDataProviderIterator($dataProvider);

        foreach ($iterator as $event) {
            \OELog::log("Event ID: {$event->id}, pas_visit_id: {$event->pas_visit_id}");
            $worklist_patient_id = $this->getWorklistPatientId($event);

            $event->saveAttributes(['worklist_patient_id' => $worklist_patient_id]);
        }

        $this->dropColumn('event', 'pas_visit_id');
        $this->dropColumn('event_version', 'pas_visit_id');

        //refresh event table schema
        Yii::app()->db->schema->getTable('event', true);
        Yii::app()->db->schema->getTable('event_version', true);

        $this->addForeignKey('event_ibfk_worklist_patient', 'event', 'worklist_patient_id', 'worklist_patient','id');
	}

	private function getWorklistPatientId($event)
    {
        $assignment = \OEModule\PASAPI\models\PasApiAssignment::model()->findByAttributes(['resource_id' => $event->pas_visit_id]);

        if ($assignment) {
            return $assignment->internal_id;
        }

        $message = "PasApiAssignment could not found resource_id (pas_visit_id): " . $event->pas_visit_id . ", Event ID: " . $event->event_id;
        \OELog::log($message);
        throw new Exception($message);
    }

	public function safeDown()
	{
        echo "m190321_141548_unbooked_worklist does not support migration down.\n";
        return false;
	}
}