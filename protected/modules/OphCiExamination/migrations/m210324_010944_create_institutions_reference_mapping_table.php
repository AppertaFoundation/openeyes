<?php

class m210324_010944_create_institutions_reference_mapping_table extends OEMigration
{
    public function safeUp()
    {
        // IOP Instruments reference column for institution
        $this->createOETable('ophciexamination_instrument_institution', [
            'id' => 'pk',
            'instrument_id' => 'int(10) unsigned NOT NULL',
            'institution_id' => 'int(10) unsigned NOT NULL',
            'CONSTRAINT `ophciexamination_instrument_ref_instrument_fk` FOREIGN KEY (`instrument_id`) REFERENCES `ophciexamination_instrument` (`id`)',
            'CONSTRAINT `ophciexamination_instrument_ref_institution_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`)',
        ], true);

        //Risks reference column for institution
        $this->createOETable('ophciexamination_risk_institution', [
            'id' => 'pk',
            'risk_id' => 'int(11) NOT NULL',
            'institution_id' => 'int(10) unsigned NOT NULL',
            'CONSTRAINT `ophciexamination_risk_ref_risk_fk` FOREIGN KEY (`risk_id`) REFERENCES `ophciexamination_risk` (`id`)',
            'CONSTRAINT `ophciexamination_risk_ref_institution_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`)',
        ], true);
    }

    public function safeDown()
    {
        $this->dropOETable('ophciexamination_instrument_institution', true);
        $this->dropOETable('ophciexamination_risk_institution', true);
    }
}
