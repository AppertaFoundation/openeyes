<?php

class m190604_044942_examination_history_phrase_attribute_option_typo_fix extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Rename "Pharases" to "Phrases"
        $this->update('ophciexamination_attribute', array('label' => 'Phrases'), 'label = \'Pharases\'');
        // fix typo 'UNhappy' to 'unhappy'
        $this->update('ophciexamination_attribute_option', array('value' => 'Patient unhappy with first eye'), 'value = \'Patient UNhappy with first eye\'');

    }

    public function safeDown()
    {
        // revert back
        $this->update('ophciexamination_attribute', array('label' => 'Pharases'), 'label = \'Phrases\'');
        $this->update('ophciexamination_attribute_option', array('value' => 'Patient UNhappy with first eye'), 'value = \'Patient unhappy with first eye\'');
    }
}
