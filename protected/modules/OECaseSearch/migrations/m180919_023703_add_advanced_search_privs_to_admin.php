<?php

class m180919_023703_add_advanced_search_privs_to_admin extends OEMigration
{
    public function up()
    {
        $this->insert('authassignment', array('itemname' => 'Advanced Search', 'userid' => 1));
    }

    public function down()
    {
        $this->delete('authassignment', 'itemname = "Advanced Search" AND userid = 1');
    }
}
