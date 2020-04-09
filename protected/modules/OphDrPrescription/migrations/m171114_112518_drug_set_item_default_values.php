<?php

class m171114_112518_drug_set_item_default_values extends CDbMigration
{
    public function up()
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
           /*$dataProvider = new CActiveDataProvider('DrugSetItem');
            $iterator = new CDataProviderIterator($dataProvider);*/
            $iterator = $this->dbConnection->createCommand('SELECT * FROM drug_set_item')
                ->queryAll();

            foreach ($iterator as $model) {
                $data['original_attributes'] = $model;
                $values = array();
                foreach (array('duration_id', 'frequency_id', 'dose', 'route_id') as $field) {
                    if (!$model[$field]) {
                        $default_field = "default_$field";
                        $values[$field] = $this->dbConnection->createCommand("SELECT $default_field FROM drug WHERE id = :id")
                            ->bindValue(':id', $model['drug_id'])
                            ->queryScalar();
                    }
                }

                $this->insert(
                    'drug_set_item',
                    $values
                );

                $data['new_attributes'] = $values;
                Audit::add(
                    'admin',
                    'update',
                    serialize($data),
                    'Set DrugSetItem attributes based on drug\'s default values',
                    array('model' => 'DrugSetItem')
                );
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
