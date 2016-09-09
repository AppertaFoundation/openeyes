<?php

class m160825_092738_add_patient_details_element extends OEMigration
{
    public function up()
    {
        $cviEvent = $this->insertOEEventType('CVI', 'OphCoCvi', 'Co');

        $this->insertOEElementType(array(
            'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_Demographics' => array(
                'name' => 'Demographics',
                'required' => 1,
            ),
        ), $cviEvent);

        $this->createOETable(
            'et_ophcocvi_demographics',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'name' => 'varchar(255)',
                'date_of_birth' => 'date',
                'nhs_number' => 'varchar(40)',
                'address' => 'text',
                'email' => 'varchar(255)',
                'telephone' => 'varchar(20)',
                'gender' => 'varchar(20)',
                'gp_name' => 'varchar(255)',
                'gp_address' => 'text',
                'gp_telephone' => 'varchar(20)',
            ),
            true
        );
    }

    public function down()
    {
        $this->delete('element_type', 'class_name = ? ', array('OEModule\\OphCoCvi\\models\\Element_OphCoCvi_Demographics'));
        $this->dropOETable('et_ophcocvi_demographics', true);
    }
}