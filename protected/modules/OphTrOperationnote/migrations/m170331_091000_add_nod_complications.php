<?php

class m170331_091000_add_nod_complications extends CDbMigration
{
    public function up()
    {
        // Use fixed IDs as this is hard-coded in NOD export. If this causes an error then the current table must be reviewed.

        $this->insert('ophtroperationnote_anaesthetic_anaesthetic_complications', array(
            'id' => 12,
            'name' => 'Sub-conjunctival haemorrhage',
            'display_order' => 12,
            'active' => 1,
        ));

        $this->insert('ophtroperationnote_anaesthetic_anaesthetic_complications', array(
            'id' => 13,
            'name' => 'Other',
            'display_order' => 13,
            'active' => 1,
        ));
    }

    public function down()
    {
        $this->delete('ophtroperationnote_anaesthetic_anaesthetic_complications', '`name` = "Sub-conjunctival haemorrhage"');
        $this->delete('ophtroperationnote_anaesthetic_anaesthetic_complications', '`name` = "Other"');
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
