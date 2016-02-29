<?php

class m131105_095903_injectionmanagement_notreatment_correspondence extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_injectmanagecomplex_notreatmentreason', 'letter_str', 'text');
        $this->update('ophciexamination_injectmanagecomplex_notreatmentreason', array('letter_str' => 'The patient did not receive an intra-vitreal injection today as this was not clinically indicated at todays visit.'), 'name = :nm', array(':nm' => 'Not indicated today'));
        $this->update('ophciexamination_injectmanagecomplex_notreatmentreason', array('letter_str' => 'The patient did not receive an intra-vitreal injection today as there is potential infection in the eye.'), 'name = :nm', array(':nm' => 'Infection'));
        $this->update('ophciexamination_injectmanagecomplex_notreatmentreason', array('letter_str' => 'The patient did not receive an intra-vitreal injection today as there is recent history of a CVA.'), 'name = :nm', array(':nm' => 'CVA'));
        $this->update('ophciexamination_injectmanagecomplex_notreatmentreason', array('letter_str' => 'The patient did not receive an intra-vitreal injection today as there is recent history of an MI.'), 'name = :nm', array(':nm' => 'MI'));
        $this->update('ophciexamination_injectmanagecomplex_notreatmentreason', array('letter_str' => 'The patient did not receive an intra-vitreal injection today as there has been a spontaneous improvement in the condition.'), 'name = :nm', array(':nm' => 'Spontaneous improvement'));
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_injectmanagecomplex_notreatmentreason', 'letter_str');
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
