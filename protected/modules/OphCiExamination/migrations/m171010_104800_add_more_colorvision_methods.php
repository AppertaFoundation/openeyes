<?php

class m171010_104800_add_more_colorvision_methods extends OEMigration
{
    public function up()
    {
        //$unit = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_visual_acuity_unit')->where('name = :name', array(':name' => 'Snellen Metre'))->queryRow();

        # Check that these values do not already exist
        $is13 = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_colourvision_method')->where('name = :name', array(':name' => 'Ishihara /13'))->queryRow();
            $is17 = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_colourvision_method')->where('name = :name', array(':name' => 'Ishihara /17'))->queryRow();
        $is24 = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_colourvision_method')->where('name = :name', array(':name' => 'Ishihara /24'))->queryRow();

        # Insert values if they don't already exist
        if ($is13['id'] == '') {
            $this->insert('ophciexamination_colourvision_method', array(
                          'name' => 'Ishihara /13',
                          'active' => '1',
                          'display_order' => '1',
                  ));
        # Add values
        $is13 = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_colourvision_method')->where('name = :name', array(':name' => 'Ishihara /13'))->queryRow();
        $method_id = $is13['id'];

        for ($i=0; $i<14; $i++) {
          $this->insert('ophciexamination_colourvision_value', array(
                            'name' => $i . '/13',
                            'active' => '1',
                            'display_order' => $i+1,
                            'method_id' => $method_id,
                    ));
        }
        }

    if ($is17['id'] == '') {
            $this->insert('ophciexamination_colourvision_method', array(
                          'name' => 'Ishihara /17',
                          'active' => '1',
                          'display_order' => '3',
                  ));

        # Add values
        $is17 = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_colourvision_method')->where('name = :name', array(':name' => 'Ishihara /17'))->queryRow();
        $method_id = $is17['id'];
        for ($i=0; $i < 18; $i++) {
          $this->insert('ophciexamination_colourvision_value', array(
                            'name' => $i . '/17',
                            'active' => '1',
                            'display_order' => $i+1,
                            'method_id' => $method_id,
                    ));
        }

        }

    if ($is24['id'] == '') {
            $this->insert('ophciexamination_colourvision_method', array(
                          'name' => 'Ishihara /24',
                          'active' => '1',
                          'display_order' => '5',
                  ));

        # Add values
        $is24 = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_colourvision_method')->where('name = :name', array(':name' => 'Ishihara /24'))->queryRow();
        $method_id = $is24['id'];
        for ($i=0; $i < 25; $i++) {
          $this->insert('ophciexamination_colourvision_value', array(
                            'name' => $i . '/24',
                            'active' => '1',
                            'display_order' => $i+1,
                            'method_id' => $method_id,
                    ));
        }
        }

    # update display order
    $this->update('ophciexamination_colourvision_method', array('display_order' => '2'), "`name` = 'Ishihara /15'");
    $this->update('ophciexamination_colourvision_method', array('display_order' => '4'), "`name` = 'Ishihara /21'");
    $this->update('ophciexamination_colourvision_method', array('display_order' => '6'), "`name` = 'Red desaturation'");

    }

    public function down()
    {

    }
}
