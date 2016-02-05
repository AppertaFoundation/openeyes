<?php

class m150527_110722_hidden_element_sets extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_element_set_item', 'is_hidden', 'tinyint default 0');
        $this->addColumn('ophciexamination_element_set_item_version', 'is_hidden', 'tinyint default 0');
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_element_set_item', 'is_hidden');
        $this->dropColumn('ophciexamination_element_set_item_version', 'is_hidden');
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
