<?php

class m230809_111120_secondary_signatories extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('secondary_signatory', [
            'id' => 'pk',
            'name' => 'VARCHAR(50) NOT NULL',
            'institution_id' => 'INT(10) UNSIGNED',
            'display_order' => 'INT(10) UNSIGNED NOT NULL DEFAULT 1',
            'active' => 'INT(1) UNSIGNED NOT NULL DEFAULT 1',
        ]);

        $this->addForeignKey('secondary_signatory_institution_fk', 'secondary_signatory', 'institution_id', 'institution', 'id');

        $this->insertMultiple('secondary_signatory', [
            ['name' => 'Screened by',   'display_order' => 1],
            ['name' => 'Dispensed by',  'display_order' => 2],
            ['name' => 'Checked by',    'display_order' => 3],
            ['name' => 'Counselled by', 'display_order' => 4],
        ]);
    }

    public function safeDown()
    {
        $this->dropForeignKey('secondary_signatory_institution_fk', 'secondary_signatory');
        $this->dropOETable('secondary_signatory');
    }
}
