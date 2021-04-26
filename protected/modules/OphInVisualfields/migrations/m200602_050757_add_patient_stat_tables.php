<?php

class m200602_050757_add_patient_stat_tables extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable(
            'patient_statistic_type',
            array(
                'mnem' => 'varchar(10) NOT NULL', // used for quick lookup.
                'title' => 'varchar(255) NOT NULL', // Displayed on-screen and in menu options.
                'x_axis_label' => 'varchar(255)',
                'y_axis_label' => 'varchar(255)',
            ),
            true
        );

        $this->addPrimaryKey(
            'patient_statistic_type_pk',
            'patient_statistic_type',
            'mnem'
        );

        // This table will be filled as part of performing linear regression.
        $this->createOETable(
            'patient_statistic',
            array(
                'patient_id' => 'int(11) unsigned NOT NULL',
                'stat_type_mnem' => 'varchar(10) NOT NULL',
                'eye_id' => 'int(11) unsigned',
                'process_datapoints' => 'tinyint(1)', // Used by remodelling script to determine if statistic needs to be remodelled.
                'min_adjusted' => 'float', // Used for plotting linear regression
                'max_adjusted' => 'float', // Used for plotting linear regression
                'gradient' => 'float', // Used for rate-of-change statistics
                'y_intercept' => 'float', // May not be necessary as long as datapoints are present.
            ),
            true
        );

        // Using a composite key here to enforce unique stat types per patient per eye.
        $this->addPrimaryKey(
            'patient_statistic_pk',
            'patient_statistic',
            array('patient_id', 'stat_type_mnem', 'eye_id')
        );

        $this->addForeignKey(
            'patient_statistic_patient_fk',
            'patient_statistic',
            'patient_id',
            'patient',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'patient_statistic_stat_type_fk',
            'patient_statistic',
            'stat_type_mnem',
            'patient_statistic_type',
            'mnem'
        );

        $this->addForeignKey(
            'patient_statistic_eye_fk',
            'patient_statistic',
            'eye_id',
            'eye',
            'id'
        );

        // Add an index to the gradient column and process_datapoints column as
        // these will be used for lookup quite frequently.
        $this->execute('ALTER TABLE patient_statistic ADD INDEX gradient_idx (gradient)');
        $this->execute('ALTER TABLE patient_statistic ADD INDEX process_datapoints_idx (process_datapoints)');

        // This table can be filled from external sources
        $this->createOETable(
            'patient_statistic_datapoint',
            array(
                'id' => 'pk',
                'patient_id' => 'int(11) unsigned',
                'stat_type_mnem' => 'varchar(10) NOT NULL',
                'eye_id' => 'int(11) unsigned',
                'x_value' => 'float NOT NULL',
                'y_value' => 'float NOT NULL',
                'event_id' => 'int(10) unsigned', // Event ID is optional and primarily used as a back-reference.
            ),
            true
        );

        $this->addForeignKey(
            'patient_statistic_datapoint_stat_fk',
            'patient_statistic_datapoint',
            array('patient_id', 'stat_type_mnem', 'eye_id'),
            'patient_statistic',
            array('patient_id', 'stat_type_mnem', 'eye_id'),
            'CASCADE'
        );

        $this->addForeignKey(
            'patient_statistic_datapoint_event_fk',
            'patient_statistic_datapoint',
            'event_id',
            'event',
            'id'
        );

        $this->insert(
            'patient_statistic_type',
            array(
                'mnem' => 'md',
                'title' => 'Mean Deviation',
                'x_axis_label' => 'Age (y)',
                'y_axis_label' => 'Mean Deviation (dB)',
            )
        );

        $this->insert(
            'patient_statistic_type',
            array(
                'mnem' => 'mdr',
                'title' => 'Mean Deviation Rate',
                'x_axis_label' => 'Mean Deviation Rate (dB/year)',
                'y_axis_label' => 'Frequency',
            )
        );

        $this->insert(
            'patient_statistic_type',
            array(
                'mnem' => 'vfi',
                'title' => 'Visual Field Index',
                'x_axis_label' => 'Age (y)',
                'y_axis_label' => 'VFI (%)',
            )
        );
    }

    public function down()
    {
        $this->execute('ALTER TABLE patient_statistic DROP INDEX gradient_idx');
        $this->execute('ALTER TABLE patient_statistic DROP INDEX process_datapoints_idx');
        $this->dropOETable('patient_statistic_datapoint', true);
        $this->dropOETable('patient_statistic', true);
        $this->dropOETable('patient_statistic_type', true);
    }
}
