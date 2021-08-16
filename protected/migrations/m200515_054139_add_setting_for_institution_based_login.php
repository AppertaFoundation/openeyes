<?php

class m200515_054139_add_setting_for_institution_based_login extends OEMigration
{
  public function up()
  {
    $this->insert('setting_metadata', [
      'field_type_id' => \SettingFieldType::model()->findByAttributes([ 'name' => 'Radio buttons' ])->id,
      'key' => 'institution_required',
      'name' => 'Require institution selection before login',
      'data' => serialize(['on' => 'On', 'off' => 'Off']),
      'default_value' => 'off',
    ]);
  }

  public function down()
  {
    $this->delete('setting_metadata', '`key` = "institution_required"');
  }
}
