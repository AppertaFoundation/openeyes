<?php

class m210801_134940_remove_patient_signature_element extends OEMigration
{
    private const RETIRED_ET_TABLE = 'et_ophcocvi_patient_signature';
    private const RETIRED_ET_CLASS = 'OEModule\OphCoCvi\models\Element_OphCoCvi_PatientSignature';

    public function safeUp()
    {
        $this->deleteElementType('OphCoCvi', self::RETIRED_ET_CLASS);

        $this->migrateData();

        $this->dropOETable(self::RETIRED_ET_TABLE, true);
    }

    private function migrateData()
    {
        echo "\n\n !! Element_OphCoCvi_PatientSignature data migration is nit implemented yet !! \n\n";
    }

    public function safeDown()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName("OphCoCvi");
        $this->createElementType($event_type_id, self::RETIRED_ET_CLASS, [
            'display_order' => 20,
            'required' => 1,
        ]);
    }
}