<?php

class m210511_090600_create_pre_assessment_locations_table extends OEMigration
{
    /**
     * @return bool|void
     * @throws CException
     */
    public function safeUp()
    {
        $this->createOETable(
            'ophtroperationbooking_preassessment_location',
            array(
                'id' => 'pk',
                'name' => 'varchar(200) NOT NULL',
                'site_id' => 'INT(10) UNSIGNED NULL DEFAULT NULL',
                'active' => 'TINYINT NOT NULL DEFAULT 1',
            ),
            true
        );

        $this->addForeignKey(
            'fk_ophtroperationbooking_preassessment_location_site_id',
            'ophtroperationbooking_preassessment_location',
            'site_id',
            'site',
            'id'
        );

        $site_table = $this->dbConnection->schema->getTable('site', true);
        if (isset($site_table->columns['location_of_preassessment'])) {
            $this->execute("
                INSERT INTO ophtroperationbooking_preassessment_location (name, site_id)
                SELECT name, id FROM site WHERE location_of_preassessment = 1
            ");
            $this->dropColumn('site', 'location_of_preassessment');
        }
    }

    /**
     * @return bool|void
     * @throws CException
     */
    public function safeDown()
    {
        $site_table = $this->dbConnection->schema->getTable('site', true);

        if (!isset($site_table->columns['location_of_preassessment'])) {
            $this->addColumn('site', 'location_of_preassessment', 'boolean not null default false');
        }
        $this->dropForeignKey('fk_ophtroperationbooking_preassessment_location_site_id', 'ophtroperationbooking_preassessment_location');
        $this->dropOETable('ophtroperationbooking_preassessment_location', true);
    }
}
