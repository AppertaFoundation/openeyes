<?php

class m171114_112518_drug_set_item_default_values extends CDbMigration
{
    public function up()
    {

        $transaction = $this->getDbConnection()->beginTransaction();
        try {

            $dataProvider = new CActiveDataProvider('DrugSetItem');
            $iterator = new CDataProviderIterator($dataProvider);

            foreach ($iterator as $model) {

                $data['original_attributes'] = $model->attributes;
                foreach (array('duration_id', 'frequency_id', 'dose', 'route_id') as $field) {
                    if(!$model->$field){
                        $default_field = "default_$field";
                        $model->$field = $model->drug->$default_field;
                    }
                }

                $data['new_attributes'] = $model->attributes;
                if($model->save()){
                    Audit::add('admin', 'update', serialize($data), 'Set DrugSetItem attributes based on drug\'s default values',
                        array('model' => 'DrugSetItem'));
                }
            }

            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback();
            \OELog::log($e->getMessage());
            throw $e;
        }
    }

    public function down()
    {
        echo "m171114_112518_drug_set_item_default_values does not support migration down.\n";
        return false;
    }
}