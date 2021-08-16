<?php

class m170711_151955_anaesthetic_types_multiselect extends OEMigration
{
    public function safeUp()
    {
        CActiveRecord::$db = $this->dbConnection;
        $this->createOETable('ophtroperationnote_anaesthetic_anaesthetic_type', array(
            'id' => 'pk',
            'et_ophtroperationnote_anaesthetic_id' => 'int(10) unsigned NOT NULL',
            'anaesthetic_type_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey(
            'ophtroperationnote_anaesthetic_type_to_anaest_type',
            'ophtroperationnote_anaesthetic_anaesthetic_type',
            'anaesthetic_type_id',
            'anaesthetic_type',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationnote_anaesthetic_type_to_el',
            'ophtroperationnote_anaesthetic_anaesthetic_type',
            'et_ophtroperationnote_anaesthetic_id',
            'et_ophtroperationnote_anaesthetic',
            'id'
        );

        $this->createOETable('ophtroperationnote_anaesthetic_anaesthetic_delivery', array(
            'id' => 'pk',
            'et_ophtroperationnote_anaesthetic_id' => 'int(10) unsigned NOT NULL',
            'anaesthetic_delivery_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey(
            'ophtroperationnote_anaesthetic_delivery_to_anae_delivery',
            'ophtroperationnote_anaesthetic_anaesthetic_delivery',
            'anaesthetic_delivery_id',
            'anaesthetic_delivery',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationnote_anaesthetic_delivery_to_el',
            'ophtroperationnote_anaesthetic_anaesthetic_delivery',
            'et_ophtroperationnote_anaesthetic_id',
            'et_ophtroperationnote_anaesthetic',
            'id'
        );

        $this->alterColumn('et_ophtroperationnote_anaesthetic', 'anaesthetist_id', 'int(10) unsigned DEFAULT NULL');


        // moving the data
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            $this->insert('anaesthetic_type', array(
                'name' => 'Sedation','code' => 'Sed', 'created_user_id' => 1, 'active' => 1, 'last_modified_user_id' => 1));
            $this->insert('anaesthetic_type', array(
                'name' => 'No Anaesthetic', 'code' => 'NoAn', 'created_user_id' => 1, 'active' => 1, 'last_modified_user_id' => 1));

            $this->update('anaesthetic_delivery', array('display_order' => 1), 'name = "Subtenons"');
            $this->update('anaesthetic_delivery', array('display_order' => 2), 'name = "Peribulbar"');
            $this->update('anaesthetic_delivery', array('display_order' => 3), 'name = "Retrobulbar"');
            $this->update('anaesthetic_delivery', array('display_order' => 4), 'name = "Subconjunctival"');
            $this->update('anaesthetic_delivery', array('display_order' => 5), 'name = "Topical"');
            $this->update('anaesthetic_delivery', array('display_order' => 6), 'name = "Topical and Intracameral"');
            $this->update('anaesthetic_delivery', array('display_order' => 7), 'name = "Other"');

            $this->update('anaesthetic_type', array('display_order' => 1), 'name = "LA"');
            $this->update('anaesthetic_type', array('display_order' => 2), 'name = "Sedation"');
            $this->update('anaesthetic_type', array('display_order' => 3), 'name = "GA"');
            $this->update('anaesthetic_type', array('display_order' => 4), 'name = "No Anaesthetic"');

            $anaesthetic_topical_id = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'Topical'))->queryScalar();
            $anaesthetic_LA_id = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LA'))->queryScalar();
            $anaesthetic_LAC_id = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LAC'))->queryScalar();
            $anaesthetic_LAS_id = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'LAS'))->queryScalar();
            $anaesthetic_sedation_id = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'Sedation'))->queryScalar();
            $anaesthetic_GA_id = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_type')->where('name=:name', array(':name' => 'GA'))->queryScalar();

            $delivery_topical_id = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_delivery')->where('name=:name', array(':name' => 'Topical'))->queryScalar();
            $delivery_other_id = $this->dbConnection->createCommand()->select('id')->from('anaesthetic_delivery')->where('name=:name', array(':name' => 'Other'))->queryScalar();

            $dataProvider = new CActiveDataProvider('Element_OphTrOperationnote_Anaesthetic');
            $iterator = new CDataProviderIterator($dataProvider);

            var_dump("Migrating Anaesthetic options...");
            foreach ($iterator as $element) {
                // if event was deleted
                if (!$event = $element->event) {
                    $event = Event::model()->disableDefaultScope()->findByPk($element->event_id);
                } else {
                    $event = $element->event;
                }

                if (!$episode = $event->episode) {
                    $episode = Episode::model()->disableDefaultScope()->findByPk($event->episode_id);
                } else {
                    $episode = $event->episode;
                }

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

                    $anaesthetic_delivery_name = $this->dbConnection->createCommand()->select('name')->from('anaesthetic_delivery')->where('id=:id', array(':id' => $element->anaesthetic_delivery_id))->queryScalar();
                    $data = array(

                        'original_attributes' => array(
                            'Element_OphTrOperationnote_Anaesthetic' => $element->attributes,
                        ),
                        'new_attributes' => array(
                            'OphTrOperationnote_OperationAnaestheticType' => array(
                                array(
                                    'et_ophtroperationnote_anaesthetic_id' => $element->id,
                                    'anaesthetic_type_id' => $anaesthetic_LA_id
                                ),
                            ),
                            'OphTrOperationnote_OperationAnaestheticDelivery' => array(
                                array(
                                    'et_ophtroperationnote_anaesthetic_id' => $element->id,
                                    'anaesthetic_delivery_id' => $element->anaesthetic_delivery_id
                                ),
                            ),
                        ),
                        'text' => "Anaesthetic type Topical became Anaesthetic type LA, Delivery type: " . $anaesthetic_delivery_name .
                            ( $element->anaesthetic_delivery_id != $delivery_topical_id ? ', added extra Delivery type Topical' : '' ),
                    );
                    if ($element->anaesthetic_delivery_id != $delivery_topical_id) {
                        $data['new_attributes']['OphTrOperationnote_OperationAnaestheticDelivery'][] = array(
                            'et_ophtroperationnote_anaesthetic_id' => $element->id,
                            'anaesthetic_delivery_id' => $delivery_topical_id
                        );
                    }

                    Audit::add(
                        'admin',
                        'update',
                        serialize($data),
                        'Remove redundant Anaesthetic options',
                        array('module' => 'OphTrOperationnote', 'model' => 'Element_OphTrOperationnote_Anaesthetic', 'event_id' => $element->event_id,
                        'episode_id' => $event->episode_id,
                        'patient_id' => $episode->patient_id)
                    );

                       //When option GA is selected, set delivery method to (only) Other, set given by to Anaesthetist
                } elseif ($element->anaesthetic_type_id == $anaesthetic_GA_id) {
                    $original_anaesthetist_name = $this->dbConnection->createCommand()->select('name')->from('anaesthetist')->where('id=:id', array(':id' => $element->anaesthetist_id))->queryScalar();
                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                        'et_ophtroperationnote_anaesthetic_id' => $element->id,
                        'anaesthetic_type_id' => $anaesthetic_GA_id
                    ));

                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticDelivery', array(
                        'et_ophtroperationnote_anaesthetic_id' => $element->id,
                        'anaesthetic_delivery_id' => $delivery_other_id
                    ));

                    $anaesthetist_id = $this->dbConnection->createCommand()->select('id')->from('anaesthetist')->where('name=:name', array(':name' => 'Anaesthetist'))->queryScalar();
                    $element->anaesthetist_id = $anaesthetist_id;

                    if ( !$element->save(false) ) {
                        throw new Exception('Unable to save anaesthetic agent assignment: '.print_r($element->getErrors(), true));
                    }
                    $anaesthetic_delivery_name = $this->dbConnection->createCommand()->select('name')->from('anaesthetic_delivery')->where('id=:id', array(':id' => $element->anaesthetic_delivery_id))->queryScalar();
                    $data = array(
                        'original_attributes' => array(
                            'Element_OphTrOperationnote_Anaesthetic' => $element->attributes,
                        ),
                        'new_attributes' => array(
                            'Element_OphTrOperationnote_Anaesthetic' => $element->attributes,
                            'OphTrOperationnote_OperationAnaestheticType' => array(
                                array(
                                    'et_ophtroperationnote_anaesthetic_id' => $element->id,
                                    'anaesthetic_type_id' => $anaesthetic_GA_id
                                ),
                            ),
                            'OphTrOperationnote_OperationAnaestheticDelivery' => array(
                                array(
                                    'et_ophtroperationnote_anaesthetic_id' => $element->id,
                                    'anaesthetic_delivery_id' => $delivery_other_id
                                ),
                            ),
                        ),
                        'text' => "Delivery type: " . $anaesthetic_delivery_name .
                            ( $element->anaesthetic_delivery_id != $delivery_other_id ? ', added extra Delivery type Other' : '' ) .
                            ", Anaesthetist became 'Anaesthetist' from {$original_anaesthetist_name}",
                    );

                    Audit::add(
                        'admin',
                        'update',
                        serialize($data),
                        'Remove redundant Anaesthetic options',
                        array('module' => 'OphTrOperationnote', 'model' => 'Element_OphTrOperationnote_Anaesthetic', 'event_id' => $element->event_id,
                            'episode_id' => $event->episode_id, 'patient_id' => $episode->patient_id)
                    );
                } else {
                    $data = array(
                        'original_attributes' => array(
                            'Element_OphTrOperationnote_Anaesthetic' => $element->attributes,
                        ),
                        'new_attributes' => array(),
                    );

                    // Adding type
                    switch ($element->anaesthetic_type_id) {
                        // migrate existing Op Note with "LAC" to "LA"
                        case $anaesthetic_LAC_id:
                            $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $anaesthetic_LA_id));
                            $data['new_attributes']['OphTrOperationnote_OperationAnaestheticType'][] = array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $anaesthetic_LA_id,
                            );
                            break;

                        // migrate OpNote with "LAS" to "LA" + "Sedation"
                        case $anaesthetic_LAS_id:
                            $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $anaesthetic_LA_id));
                            $data['new_attributes']['OphTrOperationnote_OperationAnaestheticType'][] = array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $anaesthetic_LA_id,
                            );

                            $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $anaesthetic_sedation_id));
                            $data['new_attributes']['OphTrOperationnote_OperationAnaestheticType'][] = array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $anaesthetic_sedation_id,
                            );

                            break;

                        // just add the Type and Delivery
                        default:
                            $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticType', array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $element->anaesthetic_type_id));

                            $data['new_attributes']['OphTrOperationnote_OperationAnaestheticType'][] = array(
                                'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_type_id' => $element->anaesthetic_type_id,
                            );
                            break;
                    }

                    $this->createOrUpdate('OphTrOperationnote_OperationAnaestheticDelivery', array('et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_delivery_id' => $element->anaesthetic_delivery_id));
                    $data['new_attributes']['OphTrOperationnote_OperationAnaestheticDelivery'][] = array(
                        'et_ophtroperationnote_anaesthetic_id' => $element->id, 'anaesthetic_delivery_id' => $element->anaesthetic_delivery_id,
                    );

                    Audit::add(
                        'admin',
                        'update',
                        serialize($data),
                        'Remove redundant Anaesthetic options',
                        array('module' => 'OphTrOperationnote', 'model' => 'Element_OphTrOperationnote_Anaesthetic', 'event_id' => $element->event_id,
                        'episode_id' => $event->episode_id,
                        'patient_id' => $episode->patient_id)
                    );
                }
            }

            $dataProvider = new CActiveDataProvider('Element_OphTrIntravitrealinjection_Anaesthetic');
            $iterator = new CDataProviderIterator($dataProvider);
            foreach ($iterator as $element) {
                // if event was deleted
                if (!$event = $element->event) {
                    $event = Event::model()->disableDefaultScope()->findByPk($element->event_id);
                } else {
                    $event = $element->event;
                }

                if (!$episode = $event->episode) {
                    $episode = Episode::model()->disableDefaultScope()->findByPk($event->episode_id);
                } else {
                    $episode = $event->episode;
                }

                $data = array(
                    'original_attributes' => array(
                        'Element_OphTrOperationnote_Anaesthetic' => $element->attributes,
                    ),
                    'new_attributes' => array(),
                );
                if ($element->left_anaesthetictype_id == $anaesthetic_topical_id) {
                    $element->left_anaesthetictype_id = $anaesthetic_LA_id;
                    $element->left_anaestheticdelivery_id = $delivery_topical_id;
                }

                if ($element->right_anaesthetictype_id == $anaesthetic_topical_id) {
                    $element->right_anaesthetictype_id = $anaesthetic_LA_id;
                    $element->right_anaestheticdelivery_id = $delivery_topical_id;
                }

                if (!$element->save()) {
                    throw new Exception("Unable to save Element_OphTrIntravitrealinjection_Anaesthetic: " . print_r($element->getErrors(), true));
                }

                $data['new_attributes'] = $element->attributes;

                Audit::add(
                    'admin',
                    'update',
                    serialize($data),
                    'Remove redundant Anaesthetic options',
                    array('module' => 'OphTrIntravitrealinjection', 'model' => 'Element_OphTrIntravitrealinjection_Anaesthetic', 'event_id' => $element->event_id,
                        'episode_id' => $event->episode_id, 'patient_id' => $episode->patient_id)
                );
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            \OELog::log($e->getMessage());
            throw $e;
        }

        $this->update('et_ophtrconsent_procedure', array('anaesthetic_type_id' => $anaesthetic_LA_id), 'anaesthetic_type_id = ' . $anaesthetic_topical_id);
        $this->update('et_ophtrconsent_procedure', array('anaesthetic_type_id' => $anaesthetic_LA_id), 'anaesthetic_type_id = ' . $anaesthetic_LAS_id);
        $this->update('et_ophtrconsent_procedure', array('anaesthetic_type_id' => $anaesthetic_LA_id), 'anaesthetic_type_id = ' . $anaesthetic_LAC_id);

        $this->update('ophtrintravitinjection_anaesthetictype', array('anaesthetic_type_id' => $anaesthetic_LA_id), 'anaesthetic_type_id = ' . $anaesthetic_topical_id);

        /**
         * OE-6557
         * to resolve the the question of the et_ophtroperationbooking_operation FK
         */

          // after that we can drop the FKs and delete anaesthetic_type_id, anaesthetic_delivery_id

        $this->dropForeignKey('et_ophtroperationnote_ana_anaesthetic_delivery_id_fk', 'et_ophtroperationnote_anaesthetic');
        $this->dropForeignKey('et_ophtroperationnote_ana_anaesthetic_type_id_fk', 'et_ophtroperationnote_anaesthetic');

        $this->dropColumn('et_ophtroperationnote_anaesthetic', 'anaesthetic_type_id');
        $this->dropColumn('et_ophtroperationnote_anaesthetic_version', 'anaesthetic_type_id');

        $this->dropColumn('et_ophtroperationnote_anaesthetic', 'anaesthetic_delivery_id');
        $this->dropColumn('et_ophtroperationnote_anaesthetic_version', 'anaesthetic_delivery_id');

        // ok, so I leave these here for references,
        // I do not remove these values here as we need to migrate the Op Booking as well
        // and here we need these values

//        $this->delete("anaesthetic_type", "name = 'Topical'");
//        $this->delete("anaesthetic_type", "name = 'LAC'");
//        $this->delete("anaesthetic_type", "name = 'LAS'");
    }

    private function createOrUpdate($model_name, $attributes)
    {
        $model = $this->dbConnection->createCommand("SELECT * FROM {$model_name::model()->tableName()}")
            ->where($attributes)
            ->queryRow();
        if (!$model) {
            $this->insert($model_name::model()->tableName(), $attributes);
        } else {
            $this->update($model_name::model()->tableName(), $attributes, 'id = :id', array(':id' => $model['id']));
        }
    }

    public function down()
    {
          /** We will NOT support the migration down() here  */

        echo "m170711_151955_anaesthetic_types_multiselect does not support migration down.\n";
        return false;
    }
}
