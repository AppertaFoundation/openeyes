<?php

class m170418_113522_glaucoma_management_shortcode extends CDbMigration
{
    private $shortGlaucomaManagement = array(
        array('code' => 'gom', 'method' => 'getGlaucomaManagement', 'description' => 'Glaucoma Overall Management Plan from latest Examination'),
    );

    public function up()
    {
        $eventTypeId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphCoCorrespondence'))
            ->queryScalar();
        foreach ($this->shortGlaucomaManagement as $short_code) {
            $this->insert('patient_shortcode', array(
                'event_type_id' => $eventTypeId,
                'default_code' => $short_code['code'],
                'code' => $short_code['code'],
                'method' => $short_code['method'],
                'description' => $short_code['description'],
            ));
        }
    }

    public function down()
    {
        foreach ($this->shortGlaucomaManagement as $short_code) {
            $this->delete('patient_shortcode', 'default_code = :code', array(':code' => $short_code['code']));
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
