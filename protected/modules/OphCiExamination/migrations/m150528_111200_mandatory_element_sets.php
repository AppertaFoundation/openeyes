<?php

class m150528_111200_mandatory_element_sets extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_element_set_item', 'is_mandatory', 'tinyint default 0');
        $this->addColumn('ophciexamination_element_set_item_version', 'is_mandatory', 'tinyint default 0');
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_element_set_item', 'is_mandatory');
        $this->dropColumn('ophciexamination_element_set_item_version', 'is_mandatory');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
