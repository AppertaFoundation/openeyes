<?php

class m210510_144500_create_pre_assessment_types_table extends OEMigration
{
    /**
     * @return bool|void
     * @throws CException
     */
    public function safeUp()
    {
        $this->createOETable(
            'ophtroperationbooking_preassessment_type',
            array(
                'id' => 'pk',
                'name' => 'varchar(200) NOT NULL',
                'use_location' => 'TINYINT NOT NULL DEFAULT 0',
                'active' => 'TINYINT NOT NULL DEFAULT 1',
            ),
            true
        );

        $this->insertMultiple('ophtroperationbooking_preassessment_type', [
            ['name' => 'None', 'use_location' => 0],
            ['name' => 'Telephone', 'use_location' => 0],
            ['name' => 'Self-Assessment', 'use_location' => 0],
            ['name' => 'Face-to-face', 'use_location' => 1],
        ]);
    }

    /**
     * @return bool|void
     * @throws CException
     */
    public function safeDown()
    {
        $this->dropOETable('ophtroperationbooking_preassessment_type', true);
    }
}
