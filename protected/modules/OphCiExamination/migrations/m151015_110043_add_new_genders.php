<?php

class m151015_110043_add_new_genders extends CDbMigration
{
    protected $genders = array('Unknown', 'Other');

    public function up()
    {
        foreach ($this->genders as $gender) {
            $this->insert('gender', array('name' => $gender));
        }
    }

    public function down()
    {
        foreach ($this->genders as $gender) {
            $this->delete('gender', 'name ='.$gender);
        }
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
