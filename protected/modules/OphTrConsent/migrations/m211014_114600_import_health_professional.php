<?php
class m211014_114600_import_health_professional extends OEMigration
{
    const CONSULTANT_SIGNATURE_TABLE = "et_consultant_signature";
    const CONSULTANT_SIGNATURE_ELEMENT_TYPE = "OEModule\\OphTrConsent\\models\\Element_OphTrConsent_ConsultantSignature";
    const CONSENT_TAKEN_BY_TABLE = "et_ophtrconsent_consenttakenby";

    function up() {
        //check old table if exists
        if(\Yii::app()->db->schema->getTable($this::CONSULTANT_SIGNATURE_TABLE)) {
        
            //copy data
            $this->dbConnection->createCommand(
                "INSERT INTO ".$this::CONSENT_TAKEN_BY_TABLE." "
                ."(event_id, consultant_id, name_hp, second_op, created_user_id, created_date, last_modified_user_id, last_modified_date)"
                ."("
                ."SELECT event_id, signed_by_user_id, concat(user.title, ' ', user.first_name, ' ', user.last_name), 0, t.created_user_id, t.created_date, t.last_modified_user_id, t.last_modified_date"
                ." FROM ".$this::CONSULTANT_SIGNATURE_TABLE." t left join user on user.id = signed_by_user_id)"
            )->execute();

            //remove from element_type
            $this->dbConnection->createCommand(
                "DELETE FROM element_type where class_name='".$this::CONSULTANT_SIGNATURE_ELEMENT_TYPE."'"
            )->execute();
        }
    }

    function down() {
        //cannot reverse this migration
    }

    function safeUp()
    {
        
    }

    function safeDown()
    {
        
    }
}
