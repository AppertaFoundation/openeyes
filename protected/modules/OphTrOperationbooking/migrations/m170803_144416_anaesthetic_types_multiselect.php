<?php

class m170803_144416_anaesthetic_types_multiselect extends OEMigration
{
	public function up()
	{

	    $this->createOETable('ophtroperationbooking_anaesthetic_anaesthetic_type',array(
            'id' => 'pk',
            'et_ophtroperationbooking_operation_id' => 'int(10) unsigned NOT NULL',
            'anaesthetic_type_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('ophtroperationbook_anaesthetic_type_to_anaest_type', 'ophtroperationbooking_anaesthetic_anaesthetic_type','anaesthetic_type_id',
            'anaesthetic_type','id');

        $this->addForeignKey('ophtroperationbook_anaesthetic_type_to_el', 'ophtroperationbooking_anaesthetic_anaesthetic_type', 'et_ophtroperationbooking_operation_id',
            'et_ophtroperationbooking_operation', 'id');

        $this->dropForeignKey('et_ophtroperationbooking_operation_anaesthetic_type_id_fk', 'et_ophtroperationbooking_operation');

        $transaction = $this->getDbConnection()->beginTransaction();
        try {

            // migrate options
            $dataProvider = new CActiveDataProvider('Element_OphTrOperationbooking_Operation');
            $iterator = new CDataProviderIterator($dataProvider);

            foreach ($iterator as $element) {

                $anaesthetic_topical_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'Topical'))->queryScalar();
                $anaesthetic_LA_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LA'))->queryScalar();
                $anaesthetic_LAC_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LAC'))->queryScalar();
                $anaesthetic_LAS_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LAS'))->queryScalar();
                $anaesthetic_sedation_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'Sedation'))->queryScalar();

                //Topical or LAC -> LA
                if( $element->anaesthetic_type_id == $anaesthetic_topical_id || $element->anaesthetic_type_id == $anaesthetic_LAC_id){

                    // adding LA
                    $this->createOrUpdate('OphTrOperationbooking_AnaestheticAnaestheticType', array(
                        'et_ophtroperationbooking_operation_id' => $element->id,
                        'anaesthetic_type_id' => $anaesthetic_LA_id
                    ));

                } else

                //LAS -> LA + Sedation
                if( $element->anaesthetic_type_id == $anaesthetic_LAS_id){

                    // adding LA
                    $this->createOrUpdate('OphTrOperationbooking_AnaestheticAnaestheticType', array(
                        'et_ophtroperationbooking_operation_id' => $element->id,
                        'anaesthetic_type_id' => $anaesthetic_LA_id
                    ));

                    $this->createOrUpdate('OphTrOperationbooking_AnaestheticAnaestheticType', array(
                        'et_ophtroperationbooking_operation_id' => $element->id,
                        'anaesthetic_type_id' => $anaesthetic_sedation_id
                    ));
                } else {

                    $this->createOrUpdate('OphTrOperationbooking_AnaestheticAnaestheticType', array(
                        'et_ophtroperationbooking_operation_id' => $element->id,
                        'anaesthetic_type_id' => $element->anaesthetic_type_id
                    ));
                }
            }

            $this->delete("anaesthetic_type", "name = 'Topical'");
            $this->delete("anaesthetic_type", "name = 'LAC'");
            $this->delete("anaesthetic_type", "name = 'LAS'");

            $this->dropColumn('et_ophtroperationbooking_operation', 'anaesthetic_type_id');
            $this->dropColumn('et_ophtroperationbooking_operation_version', 'anaesthetic_type_id');

            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback();
            \OELog::log($e->getMessage());
            throw $e;
        }


	}

	public function down()
	{
		echo "m170803_144416_anaesthetic_types_multiselect does not support migration down.\n";
		return false;
	}

    private function createOrUpdate($model_name, $attributes)
    {
        if(!$model = $model_name::model()->findByAttributes($attributes)){
            $model = new $model_name;
        }

        foreach($attributes as $attribute => $value){
            $model->{$attribute} = $value;
        }

        if (!$model->save()) {
            throw new Exception("Unable to save : $model_name" . print_r($model->getErrors(), true));
        }
    }
}