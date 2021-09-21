<?php
/**
 * to migrate the existing Consent->Other->”Patient's statement - Procedure list which I do not * wish to be carried out:” field into
 *  "Advanced decision" to refuse treatment - Copy comments
 *
 */
class m210727_123223_copy_MEHdata_comments_into_AdvancedDecision extends OEMigration
{
    public function safeup()
    {
        $sqlQueryColExistence = "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'openeyes'
                                 AND TABLE_NAME = 'et_ophtrconsent_other'
                                 AND COLUMN_NAME = 'denied_procedures'";
        $sqlQueryColResult = Yii::app()->db->createCommand($sqlQueryColExistence)->queryScalar();

        if ($sqlQueryColResult >= 1) {
                $sqlQuery = 'INSERT INTO et_ophtrconsent_advanceddecision (event_id, description, last_modified_user_id, last_modified_date, created_user_id, created_date)
                SELECT event_id,  denied_procedures, last_modified_user_id, last_modified_date, created_user_id, created_date
                FROM et_ophtrconsent_other';
                $sqlCommand = Yii::app()->db->createCommand($sqlQuery);
                $sqlCommand->execute();
        }
    }
    public function down()
    {
        echo "m210727_123223_copy_MEHdata_comments_into_AdvancedDecision does not support migration down.\n";
        return false;
    }
}
