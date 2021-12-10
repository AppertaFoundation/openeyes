<?php

class m210203_034030_add_institution_id_column_to_reference_tables extends OEMigration
{
    /**
     * @return bool|void
     * @throws CException
     */
    public function safeUp()
    {
        $institution_id = $this->dbConnection
            ->createCommand("SELECT * FROM institution WHERE remote_id = '" . Yii::app()->params['institution_code'] . "'")
            ->queryScalar();

        $institution_list = $this->dbConnection->createCommand()
            ->select('id')
            ->from('institution')
            ->order('id')
            ->where('id = :institution_id')
            ->queryColumn(array(':institution_id' => $institution_id));

        $this->addOEColumn(
            'ophtroperationbooking_whiteboard_settings_data',
            'institution_id',
            'int(10) unsigned',
            true
        );

        $this->addForeignKey(
            'ophtroperationbooking_whiteboard_settings_data_i_fk', // Using shorthand in FK name due to character length of some tables in OpenEyes.
            'ophtroperationbooking_whiteboard_settings_data',
            'institution_id',
            'institution',
            'id'
        );

        // Add the first institution_id to all pre-existing records, then clone these records for each different institution.
        $this->update(
            'ophtroperationbooking_whiteboard_settings_data',
            array('institution_id' => $institution_id),
        );

        $this->update(
            'ophtroperationbooking_whiteboard_settings_data_version',
            array('institution_id' => $institution_id),
        );

        $raw_data = $this->dbConnection->createCommand()
            ->select()
            ->from('ophtroperationbooking_whiteboard_settings_data')
            ->queryAll();

        if (count($raw_data) > 0) {
            // Only perform this if upgrading an existing installation.
            foreach ($institution_list as $institution) {
                foreach ($raw_data as $i => $row) {
                    // Set the new institution_id and set the versioning fields to their correct values.
                    $raw_data[$i]['institution_id'] = $institution;
                    unset($raw_data[$i]['id']);
                    $raw_data[$i]['created_user_id'] = 1;
                    $raw_data[$i]['created_date'] = date('Y-m-d h:i:s');
                    $raw_data[$i]['last_modified_date'] = date('Y-m-d h:i:s');
                    $raw_data[$i]['last_modified_user_id'] = 1;
                }
                if (!empty($raw_data)) {
                    $this->insertMultiple(
                        'ophtroperationbooking_whiteboard_settings_data',
                        $raw_data
                    );
                }
            }
        }

        $this->dropOEColumn(
            'ophtroperationbooking_scheduleope_patientunavailreason',
            'enabled',
            true
        );

        $this->createOETable(
            'ophtroperationbooking_patientunavailreason_institution',
            array(
                'id' => 'pk',
                'patientunavailreason_id' => 'int(11) NOT NULL',
                'institution_id' => 'int(10) unsigned NOT NULL'
            ),
            true
        );

        $this->addForeignKey(
            'ophtroperationbooking_patientunavailreason_institution_r_fk',
            'ophtroperationbooking_patientunavailreason_institution',
            'patientunavailreason_id',
            'ophtroperationbooking_scheduleope_patientunavailreason',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationbooking_patientunavailreason_institution_i_fk',
            'ophtroperationbooking_patientunavailreason_institution',
            'institution_id',
            'institution',
            'id'
        );

        $mappings = $this->dbConnection->createCommand()
            ->select('r.id as patientunavailreason_id, i.id as institution_id')
            ->from('ophtroperationbooking_scheduleope_patientunavailreason r')
            ->crossJoin('institution i')
            ->where('i.id = :institution_id')
            ->queryAll(true, array(':institution_id' => $institution_id));

        if (!empty($mappings)) {
            $this->insertMultiple(
                'ophtroperationbooking_patientunavailreason_institution',
                $mappings
            );
        }

        $this->dropOEColumn(
            'ophtroperationbooking_operation_session_unavailreason',
            'enabled',
            true
        );

        $this->createOETable(
            'ophtroperationbooking_sessionunavailreason_institution',
            array(
                'id' => 'pk',
                'unavailablereason_id' => 'int(11) NOT NULL',
                'institution_id' => 'int(10) unsigned NOT NULL'
            ),
            true
        );

        $this->addForeignKey(
            'ophtroperationbooking_sessionunavailreason_institution_r_fk',
            'ophtroperationbooking_sessionunavailreason_institution',
            'unavailablereason_id',
            'ophtroperationbooking_operation_session_unavailreason',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationbooking_sessionunavailreason_institution_i_fk',
            'ophtroperationbooking_sessionunavailreason_institution',
            'institution_id',
            'institution',
            'id'
        );

        $mappings = $this->dbConnection->createCommand()
            ->select('r.id as unavailablereason_id, i.id as institution_id')
            ->from('ophtroperationbooking_operation_session_unavailreason r')
            ->crossJoin('institution i')
            ->where('i.id = :institution_id')
            ->queryAll(true, array(':institution_id' => $institution_id));

        if (!empty($mappings)) {
            $this->insertMultiple(
                'ophtroperationbooking_sessionunavailreason_institution',
                $mappings
            );
        }
    }

    /**
     * @return bool|void
     * @throws CException
     */
    public function safeDown()
    {
        $institution_id = $this->dbConnection->createCommand()
            ->select('MIN(id)')
            ->from('institution')
            ->queryScalar();

        $this->delete(
            'ophtroperationbooking_whiteboard_settings_data',
            'institution_id != :id',
            array(':id' => $institution_id)
        );

        $this->dropForeignKey(
            'ophtroperationbooking_whiteboard_settings_data_i_fk',
            'ophtroperationbooking_whiteboard_settings_data'
        );

        $this->dropOEColumn(
            'ophtroperationbooking_whiteboard_settings_data',
            'institution_id',
            true
        );

        $this->dropForeignKey(
            'ophtroperationbooking_patientunavailreason_institution_i_fk',
            'ophtroperationbooking_patientunavailreason_institution'
        );

        $this->dropForeignKey(
            'ophtroperationbooking_patientunavailreason_institution_r_fk',
            'ophtroperationbooking_patientunavailreason_institution'
        );

        $this->dropOETable('ophtroperationbooking_patientunavailreason_institution', true);

        $this->addOEColumn(
            'ophtroperationbooking_scheduleope_patientunavailreason',
            'enabled',
            'tinyint(1)',
            true
        );

        $this->update(
            'ophtroperationbooking_scheduleope_patientunavailreason',
            array('enabled' => 1)
        );

        $this->dropForeignKey(
            'ophtroperationbooking_sessionunavailreason_institution_i_fk',
            'ophtroperationbooking_sessionunavailreason_institution'
        );

        $this->dropForeignKey(
            'ophtroperationbooking_sessionunavailreason_institution_r_fk',
            'ophtroperationbooking_sessionunavailreason_institution'
        );

        $this->dropOETable('ophtroperationbooking_sessionunavailreason_institution', true);

        $this->addOEColumn(
            'ophtroperationbooking_operation_session_unavailreason',
            'enabled',
            'tinyint(1)',
            true
        );

        $this->update(
            'ophtroperationbooking_operation_session_unavailreason',
            array('enabled' => 1)
        );
    }
}
