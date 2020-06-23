<?php

class m140811_104235_comorbidities_admin_subspecialty_assign extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophciexamination_comorbidities_item_options', array(
                    'id' => 'pk',
                    'comorbidities_item_id' => 'int(10) unsigned NOT NULL',
                    'subspecialty_id' => 'int(10) unsigned NOT NULL',
                ), true);

        $this->addForeignKey(
            'ophciexamination_comorbidities_item_options_ciid_fk',
            'ophciexamination_comorbidities_item_options',
            'comorbidities_item_id',
            'ophciexamination_comorbidities_item',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_comorbidities_item_options_ssid_fk',
            'ophciexamination_comorbidities_item_options',
            'subspecialty_id',
            'subspecialty',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('ophciexamination_combordities_item_options', true);
    }
}
