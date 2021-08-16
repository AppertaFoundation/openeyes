<?php

class m200818_130923_add_patient_identifier_type_id_to_worklist_definition extends OEMigration
{

    public function safeUp()
    {
        $this->addOEColumn('worklist_definition', 'patient_identifier_type_id', 'int(11) NOT NULL', true);

        $institution_id = $this->getDbConnection()->createCommand("SELECT * FROM institution WHERE remote_id = '" . Yii::app()->params['institution_code'] . "'")->queryScalar();
        $local_identifier_type_id = $this->getDbConnection()->createCommand(
            "SELECT id FROM patient_identifier_type WHERE institution_id = '" . $institution_id . "' AND site_id IS NULL")->queryScalar();

        $this->update('worklist_definition', ['patient_identifier_type_id' => $local_identifier_type_id]);

        $this->addForeignKey(
            'worklist_definition_fk_patient_identifier_type', 'worklist_definition',
            'patient_identifier_type_id', 'patient_identifier_type', 'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('worklist_definition_fk_patient_identifier_type', 'worklist_definition');
        $this->dropOEColumn('worklist_definition', 'patient_identifier_type_id', true);
    }
}
