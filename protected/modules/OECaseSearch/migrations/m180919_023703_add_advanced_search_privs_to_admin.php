<?php

class m180919_023703_add_advanced_search_privs_to_admin extends OEMigration
{
    public function up()
    {
        try {
            $this->insert('authassignment', array('itemname' => 'Advanced Search', 'userid' => 1));
        } catch (CDbException $e) {
            //we don't really care if this fails because of a duplicate entry
        }
    }

    public function down()
    {
        $this->delete('authassignment', 'itemname = "Advanced Search" AND userid = 1');
    }
}