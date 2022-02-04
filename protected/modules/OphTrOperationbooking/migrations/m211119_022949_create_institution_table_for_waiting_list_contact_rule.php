<?php

class m211119_022949_create_institution_table_for_waiting_list_contact_rule extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('ophtropbooking_waiting_list_contact_rule_institution',
            [
                'id' => 'pk',
                'waiting_list_contact_rule_id' => 'int(10) unsigned',
                'institution_id' => 'int(10) unsigned',
            ],
            true
        );

        $this->addForeignKey(
            'ophtropbooking_waiting_list_contact_rule_rule_fk',
            'ophtropbooking_waiting_list_contact_rule_institution',
            'waiting_list_contact_rule_id',
            'ophtroperationbooking_waiting_list_contact_rule',
            'id'
        );

        $this->addForeignKey(
            'ophtropbooking_waiting_list_contact_rule_institution_fk',
            'ophtropbooking_waiting_list_contact_rule_institution',
            'institution_id',
            'institution',
            'id'
        );

        $institution_id = $this->dbConnection
            ->createCommand("SELECT id FROM institution WHERE remote_id = :code")
            ->bindValues(array(':code' => Yii::app()->params['institution_code']))
            ->queryScalar();

        $contact_rules = $this->dbConnection
            ->createCommand("SELECT id FROM ophtroperationbooking_waiting_list_contact_rule")
            ->queryAll();

        if ($institution_id && !empty($contact_rules)) {
            $institution_mapping = array_map(function ($rules) use ($institution_id) {
                return [
                    'institution_id' => $institution_id,
                    'waiting_list_contact_rule_id' => $rules['id'],
                ];
            }, $contact_rules);

            $this->insertMultiple('ophtropbooking_waiting_list_contact_rule_institution', $institution_mapping);
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'ophtropbooking_waiting_list_contact_rule_rule_fk',
            'ophtropbooking_waiting_list_contact_rule_institution'
        );
        $this->dropForeignKey(
            'ophtropbooking_waiting_list_contact_rule_institution_fk',
            'ophtropbooking_waiting_list_contact_rule_institution'
        );
        $this->dropOETable('ophtropbooking_waiting_list_contact_rule_institution', true);
    }
}
