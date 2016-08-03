<?php

class m150518_111819_modifyCataractComplication extends CDbMigration
{
    protected $newComplications = array(
        'None' => '1',
        'Zonule rupture no vitreous loss' => '160',
        'Zonule rupture with vitreous loss' => '170',
        'PC rupture no vitreous loss' => '111',
        'PC rupture with vitreous loss' => '112',
        'Lens fragments into vitreous' => '85',
        'Other' => '250', );

    protected $inactivateComplications = array('Zonular rupture', 'PC rupture');

    protected $changeComplications = array('Wound burn' => 'Phaco wound burn', 'Choroidal haem' => 'Choroidal / expulsive haemorrhage');

    public function up()
    {
        // inserting new rows
        foreach ($this->newComplications as $newCompName => $newCompOrder) {
            $this->insert('ophtroperationnote_cataract_complications', array('name' => $newCompName, 'display_order' => $newCompOrder));
        }

        // inactivating rows
        foreach ($this->inactivateComplications as $inactivateComp) {
            $this->update('ophtroperationnote_cataract_complications', array('active' => 0), 'name = :name', array(':name' => $inactivateComp));
        }

        // updating rows
        foreach ($this->changeComplications as $changeCompKey => $changeCompValue) {
            $this->update('ophtroperationnote_cataract_complications', array('name' => $changeCompValue), "name = '".$changeCompKey."'");
        }
    }

    public function down()
    {
        foreach ($this->changeComplications as $changeCompKey => $changeCompValue) {
            $this->update('ophtroperationnote_cataract_complications', array('name' => $changeCompKey), "name = '".$changeCompValue."'");
        }

        foreach ($this->inactivateComplications as $inactivateComp) {
            $this->update('ophtroperationnote_cataract_complications', array('active' => 1), 'name = :name', array(':name' => $inactivateComp));
        }

        foreach ($this->newComplications as $newCompName => $order) {
            $this->delete('ophtroperationnote_cataract_complications', 'name = :name', array(':name' => $newCompName));
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
