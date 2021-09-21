<?php

class m200326_102952_create_patient_identifier_type_display_order_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('patient_identifier_type_display_order', [
            'id' => 'pk',
            'institution_id' => 'int(10) unsigned not null',
            'display_order' => 'int unsigned not null',
            'patient_identifier_type_id' => 'int(11) not null',
            'site_id' => 'int(10) unsigned',
            'search_protocol_prefix' => 'varchar(100)',
            'searchable' => 'boolean NOT NULL DEFAULT 1',
            'necessity' => "varchar(20) NOT NULL DEFAULT 'hidden'",
            'status_necessity' => "varchar(20) NOT NULL DEFAULT 'hidden'",
        ], true);

        $this->addForeignKey('fk_patient_identifier_display_order_institution', 'patient_identifier_type_display_order', 'institution_id', 'institution', 'id');
        $this->addForeignKey('fk_patient_identifier_display_order_site', 'patient_identifier_type_display_order', 'site_id', 'site', 'id');
        $this->addForeignKey('fk_patient_identifier_display_order_patient_ID_type', 'patient_identifier_type_display_order', 'patient_identifier_type_id', 'patient_identifier_type', 'id');

        $this->addOEColumn('institution', 'any_number_search_allowed', 'boolean default null', true);

        $primary_institution = $this->getDbConnection()->createCommand("SELECT * FROM institution WHERE remote_id = '" . Yii::app()->params['institution_code'] . "'")->queryScalar();

        $local_type_id = $this->getDbConnection()->createCommand("SELECT id FROM patient_identifier_type WHERE usage_type = 'local'")->queryScalar();
        $global_type_id = $this->getDbConnection()->createCommand("SELECT id FROM patient_identifier_type WHERE usage_type = 'global'")->queryScalar();

        $local_necessity = 'mandatory';
        $global_necessity = 'optional';

        $this->insertMultiple('patient_identifier_type_display_order', array_map(
            static function ($usage_type) use (
                $primary_institution,
                $local_type_id,
                $global_type_id,
                $local_necessity,
                $global_necessity
            ) {
                return array(
                    'institution_id' => $primary_institution,
                    'display_order' => 1,
                    'patient_identifier_type_id' => ${$usage_type . '_type_id'},
                    'necessity' => ${$usage_type . '_necessity'},
                );
            },
            ['local', 'global']
        ));
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_patient_identifier_display_order_institution', 'patient_identifier_type_display_order');
        $this->dropForeignKey('fk_patient_identifier_display_order_site', 'patient_identifier_type_display_order');
        $this->dropForeignKey('fk_patient_identifier_display_order_patient_ID_type', 'patient_identifier_type_display_order');
        $this->dropOETable('patient_identifier_type_display_order', true);

        $this->dropOEColumn('institution', 'any_number_search_allowed', true);
    }
}
