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

        $this->addForeignKey('ophtroperationnote_anaesthetic_type_to_anaest_type',
            'ophtroperationnote_anaesthetic_anaesthetic_type',
            'anaesthetic_type_id',
            'anaesthetic_type',
            'id');

        $this->addForeignKey('ophtroperationnote_anaesthetic_type_to_el',
            'ophtroperationnote_anaesthetic_anaesthetic_type',
            'et_ophtroperationnote_anaesthetic_id',
            'et_ophtroperationnote_anaesthetic',
            'id');


        $this->createOETable('ophtroperationnote_anaesthetic_anaesthetic_delivery',array(
            'id' => 'pk',
            'et_ophtroperationnote_anaesthetic_id' => 'int(10) unsigned NOT NULL',
            'anaesthetic_delivery_id' => 'int(10) unsigned NOT NULL',
        ), true);


        $this->addForeignKey('ophtroperationnote_anaesthetic_delivery_to_anae_delivery',
            'ophtroperationnote_anaesthetic_anaesthetic_delivery',
            'anaesthetic_delivery_id',
            'anaesthetic_delivery',
            'id');

        $this->addForeignKey('ophtroperationnote_anaesthetic_delivery_to_el',
            'ophtroperationnote_anaesthetic_anaesthetic_delivery',
            'et_ophtroperationnote_anaesthetic_id',
            'et_ophtroperationnote_anaesthetic',
            'id');


        // moving the data
        //$transaction = $this->getDbConnection()->beginTransaction();

        $dataProvider = new CActiveDataProvider('Element_OphTrOperationnote_Anaesthetic');
        $iterator = new CDataProviderIterator($dataProvider);

        $anaesthetic_topical_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('anaesthetic_type')
            ->where('name=:name', array(':name' => 'Topical'))
            ->queryScalar();

        $anaesthetic_LA_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('anaesthetic_type')
            ->where('name=:name', array(':name' => 'LA'))
            ->queryScalar();


        $delivery_topical_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from('anaesthetic_delivery')
            ->where('name=:name', array(':name' => 'Topical'))
            ->queryScalar();


        foreach($iterator as $element) {

            //update any existing records with anaesthetic type "Topical" to type "LA" + delivery type of "Topical"
            if($element->anaesthetic_type_id == $anaesthetic_topical_id){
                OELog::log("To be updated: " . get_class($element) . " | id : " . $element->id);
            }
        }


	}

	public function down()
	{
		$this->dropForeignKey('ophtroperationnote_anaesthetic_type_to_anaest_type', 'ophtroperationnote_anaesthetic_anaesthetic_type');
		$this->dropForeignKey('ophtroperationnote_anaesthetic_type_to_el', 'ophtroperationnote_anaesthetic_anaesthetic_type');

		$this->dropOETable('ophtroperationnote_anaesthetic_anaesthetic_type', true);

        $this->dropForeignKey('ophtroperationnote_anaesthetic_delivery_to_anae_delivery', 'ophtroperationnote_anaesthetic_anaesthetic_delivery');
        $this->dropForeignKey('ophtroperationnote_anaesthetic_delivery_to_el', 'ophtroperationnote_anaesthetic_anaesthetic_delivery');

		$this->dropOETable('ophtroperationnote_anaesthetic_anaesthetic_delivery', true);
	}
}