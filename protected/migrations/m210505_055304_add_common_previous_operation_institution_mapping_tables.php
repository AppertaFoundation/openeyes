<?php

class m210505_055304_add_common_previous_operation_institution_mapping_tables extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $institution_id = $this->dbConnection
            ->createCommand("SELECT * FROM institution WHERE remote_id = '" . Yii::app()->params['institution_code'] . "'")
            ->queryScalar();

        $this->createOETable(
            'common_previous_operation_institution',
            array(
                'id' => 'pk',
                'common_previous_operation_id' => 'int(10) unsigned NOT NULL',
                'institution_id' => 'int(11) unsigned NOT NULL',
            ),
            true
        );
        $this->createOETable(
            'common_previous_systemic_operation_institution',
            array(
                'id' => 'pk',
                'common_previous_systemic_operation_id' => 'int(11) NOT NULL',
                'institution_id' => 'int(11) unsigned NOT NULL',
            ),
            true
        );

        $this->addForeignKey(
            'common_previous_operation_institution_o_fk',
            'common_previous_operation_institution',
            'common_previous_operation_id',
            'common_previous_operation',
            'id'
        );
        $this->addForeignKey(
            'common_previous_operation_institution_i_fk',
            'common_previous_operation_institution',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'common_previous_systemic_operation_institution_o_fk',
            'common_previous_systemic_operation_institution',
            'common_previous_systemic_operation_id',
            'common_previous_systemic_operation',
            'id'
        );
        $this->addForeignKey(
            'common_previous_systemic_operation_institution_i_fk',
            'common_previous_systemic_operation_institution',
            'institution_id',
            'institution',
            'id'
        );

        $all_op_mappings = $this->dbConnection->createCommand()
            ->select('o.id AS common_previous_operation_id, i.id AS institution_id')
            ->from('common_previous_operation o')
            ->crossJoin('institution i')
            ->where('i.id = :institution_id')
            ->queryAll(true, array(':institution_id' => $institution_id));

        $all_systemic_mappings = $this->dbConnection->createCommand()
            ->select('o.id AS common_previous_systemic_operation_id, i.id AS institution_id')
            ->from('common_previous_systemic_operation o')
            ->crossJoin('institution i')
            ->where('i.id = :institution_id')
            ->queryAll(true, array(':institution_id' => $institution_id));

        if (!empty($all_op_mappings)) {
            $this->insertMultiple(
                'common_previous_operation_institution',
                $all_op_mappings
            );
        }

        if (!empty($all_systemic_mappings)) {
            $this->insertMultiple(
                'common_previous_systemic_operation_institution',
                $all_systemic_mappings
            );
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'common_previous_operation_institution_o_fk',
            'common_previous_operation_institution',
        );
        $this->dropForeignKey(
            'common_previous_operation_institution_i_fk',
            'common_previous_operation_institution',
        );

        $this->dropForeignKey(
            'common_previous_systemic_operation_institution_o_fk',
            'common_previous_systemic_operation_institution',
        );
        $this->dropForeignKey(
            'common_previous_systemic_operation_institution_i_fk',
            'common_previous_systemic_operation_institution',
        );
        $this->dropOETable(
            'common_previous_operation_institution',
            true
        );
        $this->dropOETable(
            'common_previous_systemic_operation_institution',
            true
        );
    }
}
