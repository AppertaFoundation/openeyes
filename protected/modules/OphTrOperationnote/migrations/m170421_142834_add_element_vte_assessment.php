<?php

class m170421_142834_add_element_vte_assessment extends OEMigration
{
    public function safeUp()
    {
        $eventTypeId = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name',
            array(':class_name' => 'OphTrOperationnote'))->queryScalar();

        $this->insert("element_type", array(
            'name' => 'VTE Assessment',
            'class_name' => 'Element_OphTrOperationnote_VteAssessment',
            'event_type_id' => $eventTypeId,
            'display_order' => 55,
            'default' => 1,
            'required' => 1
        ));

        $this->createOETable('et_ophtroperationnote_vte_assessment',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned NOT NULL',
                'selected_option' => 'int(1) unsigned NOT NULL'
            ), true);

        $this->createOETable('ophtroperationnote_vte_assessment_option',
            array(
                'id' => 'pk',
                'name' => 'varchar(255) NOT NULL',
                'active' => 'int(2) unsigned NOT NULL',
                'display_order' => 'int(2) unsigned NOT NULL'
            ), true);

        $this->insert('ophtroperationnote_vte_assessment_option',
            array('name'=>'Yes', 'active'=>1, 'display_order'=>10));

        $this->insert('ophtroperationnote_vte_assessment_option',
            array('name'=>'No', 'active'=>1, 'display_order'=>20));

        $this->insert('ophtroperationnote_vte_assessment_option',
            array('name'=>'Patient stayed for less than 4 hours', 'active'=>1, 'display_order'=>30));

        $this->insert('setting_metadata', array(
            'field_type_id' => 3,
            'key' => 'vte_assessment_element_enabled',
            'name' => 'VTE Assessment element enabled',
            'default_value' => 'off',
            'data' => serialize(array('on'=>'On', 'off'=>'Off'))
        ));
    }

    public function safeDown()
    {
        $this->delete('element_type', 'class_name = :class_name',
            array(':class_name' => 'Element_OphTrOperationnote_VteAssessment'));

        $this->delete('setting_metadata', '`key`=:key', array(':key'=>'vte_assessment_element_enabled'));

        $this->dropOETable('et_ophtroperationnote_vte_assessment', true);
        $this->dropOETable('ophtroperationnote_vte_assessment_option', true);
    }
}