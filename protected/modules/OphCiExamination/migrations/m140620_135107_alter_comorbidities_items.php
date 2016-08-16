<?php

class m140620_135107_alter_comorbidities_items extends OEMigration
{
    public function up()
    {
        // Find "Ethnicity" item
        $ethnicity = $this->getDbConnection()->createCommand()
        ->select('id')
        ->from('ophciexamination_comorbidities_item')
        ->where('name=?', array('Ethnicity'))
        ->queryRow();

        // Remove Ethnicity item relations from elements
        $this->delete('ophciexamination_comorbidities_assignment', 'item_id=?', array($ethnicity['id']));
        // Remove Ethnicity item
        $this->delete('ophciexamination_comorbidities_item', "name='Ethnicity'");

        // Adjust names
        $this->update('ophciexamination_comorbidities_item', array('name' => 'Family history of Glaucoma'), "name='FOH'");
        $this->update('ophciexamination_comorbidities_item', array('name' => 'Shortness of breath'), "name='SOB'");

        // New item
        $this->insert('ophciexamination_comorbidities_item', array(
            'name' => 'Chronic airway disease',
            'display_order' => 45,
        ));
    }

    public function down()
    {
        echo "m140620_135107_alter_comorbidities_items does not support migration down.\n";

        return false;
    }
}
