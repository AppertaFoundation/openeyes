<?php

class m210803_072112_deleted_event_display_settings extends OEMigration
{
    public function up()
    {
        $field_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('setting_field_type')
            ->where(
                'name = :name',
                array(':name' => 'Radio buttons'),
            )->queryScalar();

        $this->insert('setting_metadata', array(
            'key' => 'show_deleted_events',
            'name' => 'Show Deleted Events',
            'data' => serialize(
                [
                    0 => 'Hidden',
                    1 => 'All Users / timeline',
                    2 => 'All users / grouped category',
                    3 => 'Admin only / timeline',
                    4 => 'Admin only / grouped category'
                ]
            ),
            'field_type_id' => $field_type_id,
            'lowest_setting_level' => 'INSTITUTION',
            'default_value' => 0,
        ));

        $this->insert('setting_installation', [
            'key' => 'show_deleted_events',
            'value' => 0,
        ]);
        $institution_id = $this->getDbConnection()->createCommand()
        ->select('id')
        ->from('institution')
        ->where('remote_id = :institution_code')
        ->bindValues([':institution_code' => Yii::app()->params['institution_code']])
        ->queryScalar();
        $this->insert('setting_institution', [
            'key' => 'show_deleted_events',
            'institution_id' => $institution_id,
            'value' => 0,
        ]);
    }

    public function down()
    {
        $this->delete('setting_installation', '`key` = "show_deleted_events"');
        $this->delete('setting_institution', '`key` = "show_deleted_events"');
        $this->delete('setting_metadata', '`key` = "show_deleted_events"');
    }
}
