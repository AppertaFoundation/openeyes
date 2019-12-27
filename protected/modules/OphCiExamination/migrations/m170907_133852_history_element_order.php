<?php

class m170907_133852_history_element_order extends CDbMigration
{
    protected $display_orders = array(
        'OEModule\OphCiExamination\models\Element_OphCiExamination_Comorbidities' => 5,
        'OEModule\OphCiExamination\models\SystemicDiagnoses' => 10,
        'OEModule\OphCiExamination\models\Allergies' => 15,
        'OEModule\OphCiExamination\models\PastSurgery' => 20,
        'OEModule\OphCiExamination\models\HistoryMedications' => 25,
        'OEModule\OphCiExamination\models\HistoryRisks' => 30,
        'OEModule\OphCiExamination\models\FamilyHistory' => 35,
        'OEModule\OphCiExamination\models\SocialHistory' => 40
    );

    public function up()
    {
        foreach ($this->display_orders as $cls => $display_order) {
            $this->update(
                'element_type',
                array('display_order' => $display_order),
                'class_name = :class',
                array(':class' => $cls)
            );
        }

    }

    public function down()
    {
        echo "WARNING: Old orders not preserved";
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