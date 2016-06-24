<?php

class m160622_145947_contact_labels extends OEMigration {

    public function up() {
        $this->addColumn('contact_label', 'display', 'tinyint(1) unsigned not null default 0');
        $this->addColumn('contact_label_version', 'display', 'tinyint(1) unsigned not null default 0');


        if (!empty(Yii::app()->params['contact_labels']) && is_array(Yii::app()->params['contact_labels'])) {
            foreach (Yii::app()->params['contact_labels'] as $label) {


                if (preg_match('/{SPECIALTY}/', $label)) {
                    
                    $listItem = preg_replace('/{SPECIALTY}/', $specialty->adjective, $label);
                } else {
                    $listItem = $label;
                }

                 Yii::app()->db->createCommand()
                        ->update('contact_label', array('display' => '1'), 'name=:name', array(':name' => $listItem));
            }
        }
    }

    public function down() {
        $this->dropColumn('contact_label', 'display');
        $this->dropColumn('contact_label_version', 'display');
    }

}
