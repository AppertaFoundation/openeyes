<?php

class m190325_113630_rename_common_subspecialty_meds extends CDbMigration
{
    /**
     * @return bool|void
     * @throws CException
     */
    public function up()
    {
        /** @var CDbTransaction $transaction */
        $transaction = $this->dbConnection->beginTransaction();
        try {
            $common_oph_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('medication_usage_code')
                ->where('usage_code = :usage_code', [':usage_code' => 'COMMON_OPH'])
                ->queryScalar();
            $rules = $this->dbConnection->createCommand('SELECT * FROM medication_set_rule WHERE usage_code_id = :usage_code_id')
                ->bindValue(':usage_code_id', $common_oph_id)
                ->queryAll();
            foreach ($rules as $rule) {
                /** @var MedicationSetRule $rule */
                $site = $this->dbConnection->createCommand('SELECT name FROM site WHERE id = :id')
                    ->bindValue(':id', $rule['site_id'])
                    ->queryScalar();
                $subspec = $this->dbConnection->createCommand('SELECT name FROM subspecialty WHERE id = :id')
                    ->bindValue(':id', $rule['subspecialty_id'])
                    ->queryScalar();

                $this->update(
                    'medication_set',
                    array('name' => "Common $site $subspec medications"),
                    'id = :id',
                    array(':id' => $rule['medication_set_id'])
                );
            }
        } catch (Exception $e) {
            $transaction->rollback();
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function down()
    {
        /** @var CDbTransaction $transaction */
        $transaction = $this->dbConnection->beginTransaction();
        try {
            $common_oph_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('medication_usage_code')
                ->where('usage_code = :usage_code', [':usage_code' => 'COMMON_OPH'])
                ->queryScalar();
            $rules = $this->dbConnection->createCommand('SELECT * FROM medication_set_rule WHERE usage_code_id = :usage_code_id')
                ->bindValue(':usage_code_id', $common_oph_id)
                ->queryAll();
            foreach ($rules as $rule) {
                $this->update(
                    'medication_set',
                    array('name' => 'Common subspecialty medications'),
                    'id = :id',
                    array(':id', $rule['medication_set_id'])
                );
            }
        } catch (Exception $e) {
            $transaction->rollback();
            return false;
        }

        $transaction->commit();
        return true;
    }
}
