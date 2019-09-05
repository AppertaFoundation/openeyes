<?php

class m180508_094400_add_stat_frequency extends CDbMigration
{
    public function up()
    {
        $other_display_order = $this->dbConnection->createCommand('SELECT display_order FROM drug_frequency WHERE name = "other"')->queryScalar();

        $this->insert('drug_frequency', array('name' => 'stat', 'long_name' => 'immediately', 'display_order' => $other_display_order));

        $this->update('drug_frequency', array('display_order' => $other_display_order + 1), 'name = "other"');

        $other_display_order = $this->dbConnection->createCommand('SELECT display_order FROM drug_duration WHERE name = "Other"')->queryScalar();

        $this->insert('drug_duration', array('name' => 'Once', 'display_order' => $other_display_order));

        $this->update('drug_duration', array('display_order' => $other_display_order + 1), 'name = "Other"');
    }

    public function down()
    {
        $other_display_order = $this->dbConnection->createCommand('SELECT display_order FROM drug_frequency WHERE name = "other"')->queryScalar();

        $this->delete('drug_frequency', 'name = "stat"');

        $this->update('drug_frequency', array('display_order' => $other_display_order - 1), 'name = "other"');

        $other_display_order = $this->dbConnection->createCommand('SELECT display_order FROM drug_duration WHERE name = "Other"')->queryScalar();

        $this->delete('drug_duration', 'name = "Once"');

        $this->update('drug_duration', array('display_order' => $other_display_order - 1), 'name = "Other"');
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
