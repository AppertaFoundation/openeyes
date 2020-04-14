<?php

class m171213_171604_shortoce_optom_url extends CDbMigration
{
    public function up()
    {
        $event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphCoCorrespondence'))
            ->queryScalar();

        $this->insert('patient_shortcode', array(
            'event_type_id' => $event_type_id,
            'default_code' => 'pul',
            'code' => 'pul',
            'method' => 'getPortalUrl',
            'description' => 'Portal Url',
            'last_modified_user_id' => '1',
        ));
    }

    public function down()
    {
        $this->delete('patient_shortcode', '`default_code`="pul"');
    }
}
