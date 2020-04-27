<?php

class m200313_122500_change_until_review_to_on_going_drug_duration extends CDbMigration
{
    public function up()
    {
        $this->update('drug_duration', array('name' => 'Ongoing'), 'name="Until review"');
    }

    public function down()
    {
        $this->update('drug_duration', array('name' => 'Until review'), 'name="Ongoing"');
    }
}
