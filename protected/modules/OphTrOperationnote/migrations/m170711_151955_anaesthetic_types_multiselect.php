<?php

class m170711_151955_anaesthetic_types_multiselect extends OEMigration
{
	public function up()
	{

        $this->createOETable('ophtroperationnote_anaesthetic_anaesthetic_type',array(
            'id' => 'pk',
            'et_ophtroperationnote_anaesthetic_id' => 'int(10) unsigned NOT NULL',
            'anaesthetic_type_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('ophtroperationnote_anaesthetic_type_to_anaest_type', 'ophtroperationnote_anaesthetic_anaesthetic_type','anaesthetic_type_id',
            'anaesthetic_type','id');

        $this->addForeignKey('ophtroperationnote_anaesthetic_type_to_el', 'ophtroperationnote_anaesthetic_anaesthetic_type', 'et_ophtroperationnote_anaesthetic_id',
            'et_ophtroperationnote_anaesthetic', 'id');

        $this->createOETable('ophtroperationnote_anaesthetic_anaesthetic_delivery',array(
            'id' => 'pk',
            'et_ophtroperationnote_anaesthetic_id' => 'int(10) unsigned NOT NULL',
            'anaesthetic_delivery_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('ophtroperationnote_anaesthetic_delivery_to_anae_delivery', 'ophtroperationnote_anaesthetic_anaesthetic_delivery',
            'anaesthetic_delivery_id', 'anaesthetic_delivery', 'id');

        $this->addForeignKey('ophtroperationnote_anaesthetic_delivery_to_el', 'ophtroperationnote_anaesthetic_anaesthetic_delivery',
            'et_ophtroperationnote_anaesthetic_id', 'et_ophtroperationnote_anaesthetic', 'id');


        // moving the data
        $transaction = $this->getDbConnection()->beginTransaction();
        try {

            $this->insert('anaesthetic_type',array(
                'name' => 'Sedation','code' => 'Sed', 'created_user_id' => 1, 'active' => 1, 'last_modified_user_id' => 1));
            $this->insert('anaesthetic_type',array(
                'name' => 'No Anaesthetic', 'code' => 'NoAn', 'created_user_id' => 1, 'active' => 1, 'last_modified_user_id' => 1));

            $this->update('anaesthetic_delivery', array('display_order' => 1), 'name = "Subtenons"');
            $this->update('anaesthetic_delivery', array('display_order' => 2), 'name = "Peribulbar"');
            $this->update('anaesthetic_delivery', array('display_order' => 3), 'name = "Retrobulbar"');
            $this->update('anaesthetic_delivery', array('display_order' => 4), 'name = "Subconjunctival"');
            $this->update('anaesthetic_delivery', array('display_order' => 5), 'name = "Topical"');
            $this->update('anaesthetic_delivery', array('display_order' => 6), 'name = "Topical and Intracameral"');
            $this->update('anaesthetic_delivery', array('display_order' => 7), 'name = "Other"');

            $anaesthetic_topical_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'Topical'))->queryScalar();
            $anaesthetic_LA_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LA'))->queryScalar();
            $anaesthetic_LAC_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LAC'))->queryScalar();
            $anaesthetic_LAS_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LAS'))->queryScalar();
            $anaesthetic_sedation_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LAS'))->queryScalar();

            $delivery_topical_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_delivery')->where('name=:name', array(':name' => 'Topical'))->queryScalar();

            $dataProvider = new CActiveDataProvider('Element_OphTrOperationnote_Anaesthetic');
            $iterator = new CDataProviderIterator($dataProvider);

            foreach ($iterator as $element) {

                //update any existing records with anaesthetic type "Topical" to type "LA" + delivery type of "Topical"
                if ($element->anaesthetic_type_id == $anaesthetic_topical_id) {

                    // adding LA
                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                        'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $anaesthetic_LA_id));

                    // adding the existing delivery type
                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticDelivery', array(
                        'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_delivery_id' => $element->anaesthetic_delivery_id));

                    // adding the extra (Delivery) Topical type - as the Type was Topical and it will be removed from there
                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticDelivery', array(
                        'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_delivery_id' => $delivery_topical_id));

                } else {

                    // Adding type
                    switch ($element->anaesthetic_type_id) {

                        // migrate existing Op Note with "LAC" to "LA"
                        case $anaesthetic_LAC_id:
                            $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $anaesthetic_LA_id));
                            break;

                        // migrate OpNote with "LAS" to "LA" + "Sedation"
                        case $anaesthetic_LAS_id:
                            $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $anaesthetic_LA_id));
                            $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $anaesthetic_sedation_id));
                            break;

                        // just add the Type and Delivery
                        default:
                            $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $element->anaesthetic_type_id));
                            break;
                    }

                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticDelivery', array('et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_delivery_id' => $element->anaesthetic_delivery_id));
                }
            }

            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback();
            \OELog::log($e->getMessage());
            throw $e;
        }

        //Good, let's check the result

        $dataProvider = new CActiveDataProvider('Element_OphTrOperationnote_Anaesthetic');
        $iterator = new CDataProviderIterator($dataProvider);

        var_dump("Checking Element_OphTrOperationnote_Anaesthetic elements...");
        foreach ($iterator as $element) {

            $fine = false;

            $element->refresh();
            $element->refreshMetaData();

            foreach($element->anaesthetic_type as $anaesthetic_type){

                //anaesthetic_type_id must be in $element->anaesthetic_type - except for Type Topical because it became LA
                if($element->anaesthetic_type_id == $anaesthetic_type->id){
                    $fine = true;
                } else if( $element->anaesthetic_type_id == $anaesthetic_topical_id ){

                    // remember, type Topical became Type LA
                    if($anaesthetic_type->id == $anaesthetic_LA_id){

                        // but we need Delivery Topical as well
                        $delivery_topical_exist = Yii::app()->db->createCommand()
                            ->select('id')
                            ->from('ophtroperationnote_anaesthetic_anaesthetic_delivery')
                            ->where('et_ophtroperationnote_anaesthetic_id=:element_id AND anaesthetic_delivery_id=:delivery_id', array(
                                ':element_id' => $element->id,
                                ':delivery_id' => $delivery_topical_id))
                            ->queryScalar();

                        if($delivery_topical_exist){
                            $fine = true;
                        }
                    }
                } else if($element->anaesthetic_type_id == $anaesthetic_LAC_id){

                    if($anaesthetic_type->id == $anaesthetic_LA_id){
                        $fine = true;
                    }
                } else if($element->anaesthetic_type_id == $anaesthetic_LAS_id){

                    $type_LA_exist = Yii::app()->db->createCommand()
                        ->select('id')
                        ->from('ophtroperationnote_anaesthetic_anaesthetic_type')
                        ->where('et_ophtroperationnote_anaesthetic_id=:element_id AND anaesthetic_type_id=:type_id', array(
                            ':element_id' => $element->id,':type_id' => $anaesthetic_LA_id))->queryScalar();

                    $type_sedation_exist = Yii::app()->db->createCommand()
                        ->select('id')
                        ->from('ophtroperationnote_anaesthetic_anaesthetic_type')
                        ->where('et_ophtroperationnote_anaesthetic_id=:element_id AND anaesthetic_type_id=:type_id', array(
                            ':element_id' => $element->id,':type_id' => $anaesthetic_sedation_id))->queryScalar();

                    if($type_LA_exist && $type_sedation_exist){
                        $fine = true;
                    }
                }
            }

            if($fine){

                $fine = false;
                foreach($element->anaesthetic_delivery as $anaesthetic_delivery){
                    if($element->anaesthetic_delivery_id == $anaesthetic_delivery->id){
                        $fine = true;
                    }
                }
            }

            $type_name = Yii::app()->db->createCommand()->select('name')->from('anaesthetic_type')->where('id=:id', array(':id' => $element->anaesthetic_type_id))->queryScalar();
            $delivery_name = Yii::app()->db->createCommand()->select('name')->from('anaesthetic_delivery')->where('id=:id', array(':id' => $element->anaesthetic_type_id))->queryScalar();

            $types = array();
            foreach($element->anaesthetic_type as $a_type){
                $types['anaesthetic_types'][] = Yii::app()->db->createCommand()->select('name')->from('anaesthetic_type')->where('id=:id', array(
                    ':id' => $a_type->id))->queryScalar();
            }

            foreach($element->anaesthetic_delivery as $a_delivery){
                $types['anaesthetic_delivery'][] = Yii::app()->db->createCommand()->select('name')->from('anaesthetic_delivery')->where('id=:id', array(
                    ':id' => $a_delivery->id))->queryScalar();
            }

            OELog::log("Element_OphTrOperationnote_Anaesthetic " . $element->id . " had anaesthetic Type: " . $type_name . ", anaesthetic Delivery: " . $delivery_name .
                ", new values are :" . print_r($types,true));

            var_dump($element->id . " is " . ($fine ? 'FINE' : 'NOT FINE'));

//huh, time to delete the $element->anaesthetic_type_id and $element->anaesthetic_delivery_id ?

//$this->dropForeignKey('et_ophtroperationnote_ana_anaesthetic_delivery_id_fk', 'et_ophtroperationnote_anaesthetic');
//$this->dropForeignKey('et_ophtroperationnote_ana_anaesthetic_type_id_fk', 'et_ophtroperationnote_anaesthetic');
//
//$this->dropColumn('et_ophtroperationnote_anaesthetic', 'anaesthetic_type_id');
//$this->dropColumn('et_ophtroperationnote_anaesthetic_version', 'anaesthetic_type_id');

//$this->dropColumn('et_ophtroperationnote_anaesthetic', 'anaesthetic_delivery_id');
//$this->dropColumn('et_ophtroperationnote_anaesthetic_version', 'anaesthetic_delivery_id');
//
//$this->delete("anaesthetic_type", "name = 'Topical'");
        }
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

	public function down()
	{


	      /** We will NOT support the migration down() here  */



//        echo "m170711_151955_anaesthetic_types_multiselect does not support migration down.\n";
//        return false;

		$this->dropForeignKey('ophtroperationnote_anaesthetic_type_to_anaest_type', 'ophtroperationnote_anaesthetic_anaesthetic_type');
		$this->dropForeignKey('ophtroperationnote_anaesthetic_type_to_el', 'ophtroperationnote_anaesthetic_anaesthetic_type');

		$this->dropOETable('ophtroperationnote_anaesthetic_anaesthetic_type', true);

        $this->dropForeignKey('ophtroperationnote_anaesthetic_delivery_to_anae_delivery', 'ophtroperationnote_anaesthetic_anaesthetic_delivery');
        $this->dropForeignKey('ophtroperationnote_anaesthetic_delivery_to_el', 'ophtroperationnote_anaesthetic_anaesthetic_delivery');

		$this->dropOETable('ophtroperationnote_anaesthetic_anaesthetic_delivery', true);

        $this->delete("anaesthetic_type", "name = 'Sedation'");
        $this->delete("anaesthetic_type", "name = 'No Anaesthetic'");
	}
}