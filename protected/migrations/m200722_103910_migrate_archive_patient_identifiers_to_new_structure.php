<?php

class m200722_103910_migrate_archive_patient_identifiers_to_new_structure extends CDbMigration
{
    public function safeUp()
    {
        if (Yii::app()->params['default_country'] === 'Australia') {
            $this->insert('contact', ['active' => 1]);
            $this->execute("
                INSERT INTO institution (name, remote_id, short_name, contact_id, last_modified_date, created_date)
                VALUES ('The Royal Victorian Eye and Ear Hospital'  , 'RVEEH_UR', 'RVEEH_UR', (SELECT id FROM contact ORDER BY id DESC LIMIT 1), '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "');
            ");
            $institution_id = $this->getDbConnection()->createCommand("SELECT * FROM institution WHERE remote_id = 'RVEEH_UR'")->queryScalar();
            $to_insert_into_patient_identifier_type = ['usage_type' => "'LOCAL'", 'short_title' => "'RVEEH_UR'", 'long_title' => "'RVEEH Code'", 'institution_id' => $institution_id, 'validate_regex' => "'/^.+$/'", 'suffix' => "'(RVEE)'", 'last_modified_date' => "'" . date('Y-m-d H:i:s') . "'", 'created_date' => "'" . date('Y-m-d H:i:s') . "'"];

            $this->execute("
                INSERT INTO patient_identifier_type (usage_type, short_title,long_title, institution_id, validate_regex, value_display_suffix, last_modified_date, created_date)
                VALUES (" . implode(",", $to_insert_into_patient_identifier_type) . ");
            ");

            $local_type_id = $this->getDbConnection()->createCommand("SELECT id FROM patient_identifier_type WHERE short_title = 'RVEEH_UR'")->queryScalar();

            $primary_institution = $this->getDbConnection()->createCommand("SELECT * FROM institution WHERE remote_id = '" . Yii::app()->params['institution_code'] . "'")->queryScalar();

            $archive_patient_identifiers = $this->dbConnection->createCommand('SELECT * FROM archive_patient_identifier WHERE code = "RVEEH_UR"')->queryAll();

            $this->insert('patient_identifier_type_display_order', array(
                'institution_id' => $primary_institution,
                'display_order' => 2,
                'patient_identifier_type_id' => $local_type_id,
                'searchable' => 0,
                'necessity' => 'optional'
            ));

            $this->insertMultiple('patient_identifier', array_map(
                static function ($record) use ($local_type_id) {
                    return array(
                        'patient_id' => $record['patient_id'],
                        'patient_identifier_type_id' => $local_type_id,
                        'value' => $record['value']
                    );
                },
                $archive_patient_identifiers
            ));
        }
    }

    public function safeDown()
    {
        echo "m200722_103910_migrate_archive_patient_identifiers_to_new_structure does not support migration down.\n";
        return false;
    }
}
