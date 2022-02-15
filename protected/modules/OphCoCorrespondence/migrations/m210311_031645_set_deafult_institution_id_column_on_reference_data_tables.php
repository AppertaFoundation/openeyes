<?php

class m210311_031645_set_deafult_institution_id_column_on_reference_data_tables extends OEMigration
{

    public function safeUp()
    {
        $institution_id = $this->dbConnection->createCommand("SELECT id FROM institution WHERE remote_id = :remote_id")->queryScalar([':remote_id' => Yii::app()->params['institution_code']]);

        $this->update('ophcocorrespondence_letter_string_group', ['institution_id' => $institution_id], 'institution_id IS NULL');
    }

    public function safeDown()
    {
        echo("down nit supported");
    }
}
