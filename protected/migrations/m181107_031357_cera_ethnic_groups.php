<?php

class m181107_031357_cera_ethnic_groups extends OEMigration
{

  // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {

        $this->insert('ethnic_group', array('name' => 'Indigenous Australian', 'code' => 'S', 'display_order' => '180'));
        $this->insert('ethnic_group', array('name' => 'Greek', 'code' => 'C', 'display_order' => '190'));
        $this->insert('ethnic_group', array('name' => 'Italian', 'code' => 'C', 'display_order' => '200'));

    }

    public function safeDown()
    {
        $this->delete('ethnic_group', 'name = "Indigenous Australian"');
        $this->delete('ethnic_group', 'name = "Greek"');
        $this->delete('ethnic_group', 'name = "Italian"');
    }

}