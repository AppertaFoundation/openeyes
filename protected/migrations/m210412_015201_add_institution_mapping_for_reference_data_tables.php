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

        $institution_ids = $this->dbConnection->createCommand()
            ->select('id')
            ->from('institution')
            ->queryColumn();

        $first_institution_id = array_shift($institution_ids);

        $existing_firms = $this->dbConnection->createCommand()
            ->select(
                'f.service_subspecialty_assignment_id,
                f.pas_code,
                f.name,
                f.service_email,
                f.context_email,
                f.consultant_id,
                f.active,
                f.can_own_an_episode,
                f.runtime_selectable,
                f.cost_code,
                i.id institution_id'
            )
            ->from('firm f')
            ->crossJoin('institution i')
            ->where('i.id != :first_id')
            ->bindValue(':first_id', $first_institution_id)
            ->queryAll();

        $this->update('firm', array('institution_id' => $first_institution_id));

        $this->insertMultiple('firm', $existing_firms);
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
