<?php

class m210802_155331_add_additional_risks_table extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophtrconsent_additional_risk',
            array(
                'id' => 'pk',
                'name' => 'text not null',
                'institution_id' => 'int(10) unsigned not null',
                'active' => 'tinyint(1) default 1',
                'display_order' => 'int(10) unsigned not null default 1',
            ),
            true
        );

        $this->createOETable(
            'ophtrconsent_additional_risk_subspecialty_assignment',
            array(
                'id' => 'pk',
                'subspecialty_id' => 'int(10) unsigned not null',
                'additional_risk_id' => 'int(11) not null',
            ),
            true
        );

        $this->addForeignKey(
            'consent_risk_subspec_assignment_fk',
            'ophtrconsent_additional_risk_subspecialty_assignment',
            'additional_risk_id',
            'ophtrconsent_additional_risk',
            'id'
        );

        $this->addForeignKey(
            'consent_risk_subspecialty_fk',
            'ophtrconsent_additional_risk_subspecialty_assignment',
            'subspecialty_id',
            'subspecialty',
            'id'
        );

        $this->addForeignKey(
            'consent_risk_institution_fk',
            'ophtrconsent_additional_risk',
            'institution_id',
            'institution',
            'id'
        );

        // $this->createIndex() not working?
        $this->dbConnection->getCommandBuilder()->createSqlCommand("
            ALTER TABLE `ophtrconsent_additional_risk` 
            ADD UNIQUE INDEX `ophtrconsent_add_risk_institution_unique` (`name`, `institution_id`);
        ")->execute();

        $this->createIndex(
            'ophtrconsent_additional_risk_subspec_unique',
            'ophtrconsent_additional_risk_subspecialty_assignment',
            'subspecialty_id,additional_risk_id',
            true
        );
    }

    public function down()
    {
        $this->dropOETable('ophtrconsent_additional_risk_subspecialty_assignment', true);
        $this->dropOETable('ophtrconsent_additional_risk', true);
    }
}
