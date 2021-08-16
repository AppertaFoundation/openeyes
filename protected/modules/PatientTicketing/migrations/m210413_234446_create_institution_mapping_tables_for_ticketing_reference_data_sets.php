<?php

class m210413_234446_create_institution_mapping_tables_for_ticketing_reference_data_sets extends OEMigration
{
    private $base_tables = array(
        'patientticketing_queueset' => array(
            'id' => 'pk',
            'queueset_id' => 'int(11) NOT NULL',
            'institution_id' => 'int(11) unsigned NOT NULL',
            'CONSTRAINT patientticketing_queueset_institution_q_fk FOREIGN KEY (queueset_id) REFERENCES patientticketing_queueset (id)',
        ),
        'patientticketing_queuesetcategory' => array(
            'id' => 'pk',
            'category_id' => 'int(11) NOT NULL',
            'institution_id' => 'int(11) unsigned NOT NULL',
            'CONSTRAINT patientticketing_queuesetcategory_institution_c_fk FOREIGN KEY (category_id) REFERENCES patientticketing_queuesetcategory (id)',
        ),
        'patientticketing_clinic_location' => array(
            'id' => 'pk',
            'clinic_location_id' => 'int(10) unsigned NOT NULL',
            'institution_id' => 'int(11) unsigned NOT NULL',
            'CONSTRAINT patientticketing_clinic_location_institution_l_fk FOREIGN KEY (clinic_location_id) REFERENCES patientticketing_clinic_location (id)',
        ),
        'patientticketing_ticketassignoutcomeoption' => array(
            'id' => 'pk',
            'outcome_option_id' => 'int(11) NOT NULL',
            'institution_id' => 'int(11) unsigned NOT NULL',
            'CONSTRAINT patientticketing_ticketassignoutcomeoption_institution_l_fk FOREIGN KEY (outcome_option_id) REFERENCES patientticketing_ticketassignoutcomeoption (id)',
        ),
    );

    public function safeUp()
    {
        foreach ($this->base_tables as $table_name => $columns) {
            $mapping_table = $table_name . '_institution';
            $this->createOETable(
                $mapping_table,
                $columns,
                true
            );
            $this->addForeignKey(
                $mapping_table . '_i_fk',
                $mapping_table,
                'institution_id',
                'institution',
                'id'
            );

            $reference_data_column = array_keys($columns)[1];

            $mapping_data = $this->dbConnection->createCommand()
                ->select("t.id AS $reference_data_column, i.id AS institution_id")
                ->from($table_name . ' t')
                ->crossJoin('institution i')
                ->queryAll();

            $this->insertMultiple(
                $mapping_table,
                $mapping_data
            );
        }
        $this->dropOEColumn('patientticketing_queueset', 'active', true);
        $this->dropOEColumn('patientticketing_queuesetcategory', 'active', true);
    }

    public function safeDown()
    {
        echo "This migration does not support down migration.\n";
        return false;
    }
}
