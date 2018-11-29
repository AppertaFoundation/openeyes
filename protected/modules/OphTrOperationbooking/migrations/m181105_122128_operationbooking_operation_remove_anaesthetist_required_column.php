<?php

class m181105_122128_operationbooking_operation_remove_anaesthetist_required_column extends CDbMigration
{
    public function up()
    {
        try {
            $this->dropColumn('et_ophtroperationbooking_operation_version' , 'anaesthetist_required');
            $this->dropColumn('et_ophtroperationbooking_operation' , 'anaesthetist_required');
        } catch (Exception $e){
            echo 'Column anaethetist_required in table (and version) '.
                'et_ophtropereration_operation_booking didn\'t exist and therefore wasn\'t removed';
        }
    }
    public function down()
    {
        $this->addColumn('et_ophtroperationbooking_operation' , 'anaesthetist_required' ,"tinyint(1) unsigned DEFAULT 0");
        $this->addColumn('et_ophtroperationbooking_operation_version' , 'anaesthetist_required' ,"tinyint(1) unsigned DEFAULT 0");
    }
}