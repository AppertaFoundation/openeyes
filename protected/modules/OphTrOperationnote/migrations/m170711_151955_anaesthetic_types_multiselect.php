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
            $anaesthetic_GA_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'GA'))->queryScalar();

            $delivery_topical_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_delivery')->where('name=:name', array(':name' => 'Topical'))->queryScalar();
            $delivery_other_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_delivery')->where('name=:name', array(':name' => 'Other'))->queryScalar();

            $dataProvider = new CActiveDataProvider('Element_OphTrOperationnote_Anaesthetic');
            $iterator = new CDataProviderIterator($dataProvider);

            var_dump("Migrating Anaesthetic options...");
            foreach ($iterator as $element) {

                //update any existing records with anaesthetic type "Topical" to type "LA" + delivery type of "Topical"
                if ($element->anaesthetic_type_id == $anaesthetic_topical_id) {

                    // adding LA
                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                        'et_ophtroperationnote_anaesthetic_id' => $element->id,
                        'anaesthetic_type_id' => $anaesthetic_LA_id
                    ));

                    // adding the existing delivery type
                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticDelivery', array(
                        'et_ophtroperationnote_anaesthetic_id' => $element->id,
                        'anaesthetic_delivery_id' => $element->anaesthetic_delivery_id
                    ));

                    // adding the extra (Delivery) Topical type - as the Type was Topical and it will be removed from there
                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticDelivery', array(
                        'et_ophtroperationnote_anaesthetic_id' => $element->id,
                        'anaesthetic_delivery_id' => $delivery_topical_id
                    ));

                       //When option GA is selected, set delivery method to (only) Other, set given by to Anaesthetist
                } else if ($element->anaesthetic_type_id == $anaesthetic_GA_id) {


                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                        'et_ophtroperationnote_anaesthetic_id' => $element->id,
                        'anaesthetic_type_id' => $anaesthetic_GA_id
                    ));

                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticDelivery', array(
                        'et_ophtroperationnote_anaesthetic_id' => $element->id,
                        'anaesthetic_delivery_id' => $delivery_other_id
                    ));

                    $anaesthetist_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetist')->where('name=:name', array(':name' => 'Anaesthetist'))->queryScalar();
                    $element->anaesthetist_id = $anaesthetist_id;

                    if( !$element->save(false) ){
                        throw new Exception('Unable to save anaesthetic agent assignment: '.print_r($element->getErrors(), true));
                    }

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

        /**
         * Not sure if we should update these  ??
         */

        //$this->update('et_ophtrconsent_procedure', array('anaesthetic_type_id' => $anaesthetic_LA_id), 'anaesthetic_type_id = ' . $anaesthetic_topical_id);
        //$this->update('et_ophtrintravitinjection_anaesthetic', array('left_anaesthetictype_id' => $anaesthetic_LA_id), 'left_anaesthetictype_id = ' . $anaesthetic_topical_id);
        //$this->update('et_ophtrintravitinjection_anaesthetic', array('right_anaesthetictype_id' => $anaesthetic_LA_id), 'right_anaesthetictype_id = ' . $anaesthetic_topical_id);

        /**
         * OE-6557
         * to resolve the the question of the et_ophtroperationbooking_operation FK
         */

          // after that we can drop the FKs and delete anaesthetic_type_id, anaesthetic_delivery_id

//        $this->dropForeignKey('et_ophtroperationnote_ana_anaesthetic_delivery_id_fk', 'et_ophtroperationnote_anaesthetic');
//        $this->dropForeignKey('et_ophtroperationnote_ana_anaesthetic_type_id_fk', 'et_ophtroperationnote_anaesthetic');
//
//        $this->dropColumn('et_ophtroperationnote_anaesthetic', 'anaesthetic_type_id');
//        $this->dropColumn('et_ophtroperationnote_anaesthetic_version', 'anaesthetic_type_id');
//
//        $this->dropColumn('et_ophtroperationnote_anaesthetic', 'anaesthetic_delivery_id');
//        $this->dropColumn('et_ophtroperationnote_anaesthetic_version', 'anaesthetic_delivery_id');
//
//        $this->delete("anaesthetic_type", "name = 'Topical'");
//        $this->delete("anaesthetic_type", "name = 'LAC'");
//        $this->delete("anaesthetic_type", "name = 'LAS'");

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


        echo "m170711_151955_anaesthetic_types_multiselect does not support migration down.\n";
        return false;

        //@TODO : remove the lines below when development finish until that we may want to revert sometines

		/*$this->dropForeignKey('ophtroperationnote_anaesthetic_type_to_anaest_type', 'ophtroperationnote_anaesthetic_anaesthetic_type');
		$this->dropForeignKey('ophtroperationnote_anaesthetic_type_to_el', 'ophtroperationnote_anaesthetic_anaesthetic_type');

		$this->dropOETable('ophtroperationnote_anaesthetic_anaesthetic_type', true);

        $this->dropForeignKey('ophtroperationnote_anaesthetic_delivery_to_anae_delivery', 'ophtroperationnote_anaesthetic_anaesthetic_delivery');
        $this->dropForeignKey('ophtroperationnote_anaesthetic_delivery_to_el', 'ophtroperationnote_anaesthetic_anaesthetic_delivery');

		$this->dropOETable('ophtroperationnote_anaesthetic_anaesthetic_delivery', true);

        $this->delete("anaesthetic_type", "name = 'Sedation'");
        $this->delete("anaesthetic_type", "name = 'No Anaesthetic'");*/
	}
}