<?php

use OEModule\PASAPI\resources\PatientMerge;

class m210705_102837_add_automerge_setting extends OEMigration
{
    private $setting_key = "pasapi_automerge";
    private const AUTO_MERGE_NEVER = 0;
    private const AUTO_MERGE_ON_MATCH = 1;
    private const AUTO_MERGE_ALWAYS = 2;

    public function safeUp()
    {
        $existing = $this->dbConnection->createCommand()
            ->select('id')->from('setting_metadata')
            ->where('`key` = :setting_key', array(':setting_key' => $this->setting_key))
            ->queryScalar();

        if ($existing === false) {
            $dropdown_list_id= $this->dbConnection->createCommand(
                "SELECT `id` FROM `setting_field_type` WHERE `name` = 'Dropdown list'"
            )->queryScalar();
            $this->insert('setting_metadata', [
                'element_type_id' => null,
                'display_order' => 0,
                'field_type_id' => $dropdown_list_id,
                'key' => $this->setting_key,
                'name' => "Automatic patient merge strategy",
                'data' => serialize([
                    self::AUTO_MERGE_NEVER => "Never merge",
                    self::AUTO_MERGE_ON_MATCH => "Merge if DOB and gender match",
                    self::AUTO_MERGE_ALWAYS => "Always merge",
                ]),
                'default_value' => self::AUTO_MERGE_ON_MATCH
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', "`key` =:skey", [":skey" => $this->setting_key]);
    }
}
