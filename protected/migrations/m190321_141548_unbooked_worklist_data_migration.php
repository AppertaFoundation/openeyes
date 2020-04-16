<?php
use \OEModule\PASAPI\models\PasApiAssignment;

class m190321_141548_unbooked_worklist_data_migration extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->addColumn('event', 'worklist_patient_id', 'INT(11) DEFAULT NULL');
        $this->addColumn('event_version', 'worklist_patient_id', 'INT(11) DEFAULT NULL');

        $events = $this->dbConnection->createCommand('SELECT * FROM event WHERE pas_visit_id IS NOT NULL')
            ->queryAll();

        foreach ($events as $event) {
            \OELog::log("Event ID: {$event['id']}, pas_visit_id: {$event['pas_visit_id']}");
            $worklist_patient_id = $this->getWorklistPatientId($event);

            $this->update(
                'event',
                array('worklist_patient_id' => $worklist_patient_id),
                'id = :id',
                array(':id' => $event['id'])
            );
        }

        $this->dropColumn('event', 'pas_visit_id');
        $this->dropColumn('event_version', 'pas_visit_id');

        //refresh event table schema
        $this->dbConnection->schema->getTable('event', true);
        $this->dbConnection->schema->getTable('event_version', true);

        $this->addForeignKey('event_ibfk_worklist_patient', 'event', 'worklist_patient_id', 'worklist_patient', 'id');

        $this->insert(
            'setting_metadata',
            array(
                'element_type_id' => null,
                'field_type_id' => 4,
                'key' => 'worklist_search_appt_within',
                'name' => 'Search worklist appointment within (days)',
                'default_value' => '30',
                'data' => ''
            )
        );
        $this->insert('setting_installation', array('key' => 'worklist_search_appt_within', 'value' => '30'));
    }

    private function getWorklistPatientId($event)
    {
        $assignment = $this->dbConnection->createCommand('SELECT * FROM pasapi_assignment WHERE resource_id = :resource_id')
            ->bindValue(':resource_id', $event['pas_visit_id'])
            ->queryRow();

        if ($assignment) {
            return $assignment['internal_id'];
        }

        //could have been deleted by an appointment cancellation
        $message = 'PasApiAssignment could not found resource_id (pas_visit_id): ' . $event['pas_visit_id'] . ', Event ID: ' . $event['id'];
        \OELog::log($message);
        return null;
    }

    public function safeDown()
    {
        echo "m190321_141548_unbooked_worklist does not support migration down.\n";
        return false;
    }
}
