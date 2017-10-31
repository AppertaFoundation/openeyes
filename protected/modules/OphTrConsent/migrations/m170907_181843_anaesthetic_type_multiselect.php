<?php

class m170907_181843_anaesthetic_type_multiselect extends OEMigration
{
	public function up()
	{

        $this->createOETable('ophtrconsent_procedure_anaesthetic_type',array(
            'id' => 'pk',
            'et_ophtrconsent_procedure_id' => 'int(10) unsigned NOT NULL',
            'anaesthetic_type_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('ophtrconsent_procedure_to_anaest_type', 'ophtrconsent_procedure_anaesthetic_type','anaesthetic_type_id',
            'anaesthetic_type','id');

        $this->addForeignKey('ophtrconsent_procedure_to_anaest_type_to_el', 'ophtrconsent_procedure_anaesthetic_type', 'et_ophtrconsent_procedure_id',
            'et_ophtrconsent_procedure', 'id');

        $this->dropForeignKey('et_ophtrconsent_procedure_anaesthetic_type_id_fk', 'et_ophtrconsent_procedure');

        $dataProvider = new CActiveDataProvider('Element_OphTrConsent_Procedure');
        $iterator = new CDataProviderIterator($dataProvider);

        $anaesthetic_topical_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'Topical'))->queryScalar();
        $anaesthetic_LA_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LA'))->queryScalar();
        $anaesthetic_LAC_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LAC'))->queryScalar();
        $anaesthetic_LAS_id = Yii::app()->db->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LAS'))->queryScalar();

        var_dump("Migrating Anaesthetic options...");

        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            foreach ($iterator as $element) {

                // if event was deleted
                if(!$event = $element->event) {
                    $event = Event::model()->disableDefaultScope()->findByPk($element->event_id);
                } else {
                    $event = $element->event;
                }

                if(!$episode = $event->episode) {
                    $episode = Episode::model()->disableDefaultScope()->findByPk($event->episode_id);
                } else {
                    $episode = $event->episode;
                }

                //update any existing records with anaesthetic type "Topical" to type "LA"
                //or
                //migrate all existing ConsentForm with "LAS" to "LA"
                if ($element->anaesthetic_type_id == $anaesthetic_topical_id ||
                    $element->anaesthetic_type_id == $anaesthetic_LAC_id ||
                    $element->anaesthetic_type_id == $anaesthetic_LAS_id)
                    {

                        // adding LA
                        $this->createOrUpdate('OphTrConsent_Procedure_AnaestheticType', array(
                            'et_ophtrconsent_procedure_id' => $element->id,
                            'anaesthetic_type_id' => $anaesthetic_LA_id
                        ));

                        $data = array(
                            'original_attributes' => array(
                                'Element_OphTrConsent_Procedure' => $element->attributes,
                            ),
                            'new_attributes' => array(
                                'OphTrConsent_Procedure_AnaestheticType' => array(
                                    array(
                                        'et_ophtrconsent_procedure_id' => $element->id,
                                        'anaesthetic_type_id' => $element->anaesthetic_type_id
                                    ),
                                ),
                            ),
                        );

                        $text_type = $element->anaesthetic_type_id == $anaesthetic_topical_id ? 'Topical' : '';
                        $text_type = $element->anaesthetic_type_id == $anaesthetic_LAC_id ? 'LAC' : $text_type;
                        $text_type = $element->anaesthetic_type_id == $anaesthetic_LAS_id ? 'LAS' : $text_type;

                        $data['text'] = "Anaesthetic type {$text_type} became LA";

                        Audit::add('admin', 'update', serialize($data),
                            'Remove redundant Anaesthetic options',
                            array('module' => 'OphTrConsent', 'model' => 'Element_OphTrConsent_Procedure', 'event_id' => $element->event_id,
                                'episode_id' => $event->episode_id, 'patient_id' => $episode->patient_id));

                } else {
                    $this->createOrUpdate('OphTrConsent_Procedure_AnaestheticType', array(
                        'et_ophtrconsent_procedure_id' => $element->id,
                        'anaesthetic_type_id' => $element->anaesthetic_type_id
                    ));

                    $data = array(
                        'original_attributes' => array(
                            'Element_OphTrConsent_Procedure' => $element->attributes,
                        ),
                        'new_attributes' => array(
                            'OphTrConsent_Procedure_AnaestheticType' => array(
                                array(
                                    'et_ophtrconsent_procedure_id' => $element->id,
                                    'anaesthetic_type_id' => $element->anaesthetic_type_id
                                ),
                            ),
                        ),

                        'text' => "Anaesthetic type moved to new table: ophtrconsent_procedure_anaesthetic_type"
                    );

                    Audit::add('admin', 'update', serialize($data),
                        'Remove redundant Anaesthetic options',
                        array('module' => 'OphTrConsent', 'model' => 'Element_OphTrConsent_Procedure', 'event_id' => $element->event_id,
                            'episode_id' => $event->episode_id, 'patient_id' => $episode->patient_id));
                }

            }
            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback();
            \OELog::log($e->getMessage());
            throw $e;
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
		echo "m170907_181843_anaesthetic_type_multiselect does not support migration down.\n";
		return false;
	}
}