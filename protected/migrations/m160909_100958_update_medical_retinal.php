<?php

/**
 * Class m160909_100958_update_medical_retinal changed the medical retinal to medical retina
 */
class m160909_100958_update_medical_retinal extends CDbMigration
{
    public function up()
    {
        $this->update('subspecialty', array('name'=>'Medical Retina'), 'name="Medical Retinal"');
    }

    public function down()
    {
        $this->update('subspecialty', array('name'=>'Medical Retinal'), 'name="Medical Retina"');
    }

}
