<?php

class m220712_003918_add_primary_institution_id_column_to_patient extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('patient', 'primary_institution_id', 'int(10) unsigned', true);
        $this->addForeignKey(
            'patient_primary_i_fk',
            'patient',
            'primary_institution_id',
            'institution',
            'id'
        );

        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'field_type_id' => 4,
            'key' => 'primary_institution_label',
            'name' => 'Primary Institution demographics label',
            'default_value' => 'Primary Institution',
            'lowest_setting_level' => 'INSTALLATION'
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = \'primary_institution_label\'');
        $this->dropForeignKey(
            'patient_primary_i_fk',
            'patient'
        );
        $this->dropOEColumn('patient', 'primary_institution_id', true);
    }
}
