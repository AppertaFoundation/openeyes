<?php

class m210801_134940_remove_signature_elements extends OEMigration
{
    private const RETIRED_ELEMENTS = [
        'et_ophcocvi_patient_signature' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_PatientSignature',
        'et_ophcocvi_consultant_signature' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_ConsultantSignature'
    ];

    public function safeUp()
    {
        foreach (self::RETIRED_ELEMENTS as $table => $class) {
            $this->deleteElementType('OphCoCvi', $class);
        }
    }

    public function safeDown()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName("OphCoCvi");
        foreach (self::RETIRED_ELEMENTS as $table => $class) {
            $this->createElementType($event_type_id, $class, [
                'display_order' => 20,
                'required' => 1,
            ]);
        }
    }
}
