<?php

class m221103_101700_register_current_address_shortcode extends OEMigration
{
    protected const SHORTCODE = 'pad';
    protected const LABEL = 'Patient\'s Home Address';

    public function safeUp()
    {
        if($shortcode_id = $this->dbConnection->createCommand()->select('id')->from('patient_shortcode')->where(
            'code = :code',
            [':code' => strtolower(self::SHORTCODE)]
        )->queryScalar()) {
            $this->update('patient_shortcode', ['description' => self::LABEL], 'id = ' . $shortcode_id);
        } else {
            $event_type_id = $this->getIdOfEventTypeByClassName('OphCoCorrespondence');
            $this->registerShortcode($event_type_id, self::SHORTCODE, 'getCurrentPatientAddress', self::LABEL);
        }
    }

    public function safeDown()
    {
        $this->delete('patient_shortcode', 'code = :sc', [':sc' => self::SHORTCODE]);
    }
}
