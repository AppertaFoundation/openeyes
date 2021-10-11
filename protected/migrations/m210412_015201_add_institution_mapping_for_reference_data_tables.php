<?php

class m210412_015201_add_institution_mapping_for_reference_data_tables extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->addOEColumn('firm', 'institution_id', 'int(10) unsigned', true);
        $this->addForeignKey(
            'firm_institution_fk',
            'firm',
            'institution_id',
            'institution',
            'id'
        );

        $institution_id = $this->dbConnection->createCommand("SELECT id FROM institution WHERE remote_id = :code")->queryScalar(array(':code' => Yii::app()->params['institution_code']));

        $this->execute("UPDATE firm SET institution_id = :id", array(':id' => $institution_id));

        $this->execute("ALTER TABLE firm ADD CONSTRAINT firm_name_institution_service UNIQUE (`name`, institution_id, service_subspecialty_assignment_id)");
    }

    public function safeDown()
    {
        $institution_ids = $this->dbConnection->createCommand()
            ->select('id')
            ->from('institution')
            ->queryColumn();

        $first_institution_id = array_shift($institution_ids);

        $this->delete(
            'firm',
            'institution_id != :institution_id',
            array(':institution_id' => $first_institution_id)
        );

        $this->dropForeignKey('firm_institution_fk', 'firm');
        $this->dropOEColumn('firm', 'institution_id', true);
    }
}
