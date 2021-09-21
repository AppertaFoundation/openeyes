<?php

/**
 * This is the model class for table "et_ophtrconsent_others_involved_decision_making_process".
 *
 * The followings are the available columns in table 'et_ophtrconsent_others_involved_decision_making_process':
 * @property integer $id
 * @property string $event_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Event $event
 */

use OEModule\OphTrConsent\models\RequiresSignature;

class Element_OphTrConsent_OthersInvolvedDecisionMakingProcess extends BaseEventTypeElement implements RequiresSignature
{
    public const TYPE_PATIENT_AGREEMENT_ID = 4;

    private const PATIENT_CONTACTS_TYPE = 1;
    private const OPENEYES_USERS_TYPE = 2;

    private const SIGNATURE_TYPES = [
        self::PATIENT_CONTACTS_TYPE => BaseSignature::TYPE_PATIENT,
        self::OPENEYES_USERS_TYPE => BaseSignature::TYPE_OTHER_USER
    ];

    public $jsonData;

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_OphTrConsent_OthersInvolvedDecisionMakingProcess the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophtrconsent_others_involved_decision_making_process';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'consentContact' => array(self::HAS_MANY, 'Ophtrconsent_OthersInvolvedDecisionMakingProcessContact', 'element_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Retrieves a list of Relationships.
     *
     * @return array
     * @throws Exception
     */
    public function getRelationshipItemSet()
    {
        $relationships = \OphTrConsent_PatientRelationship::model()->findAll();
        foreach ($relationships as $i => $relationship) {
            $relationship_items[$i] = [
                'label' => $relationship->name,
                'item_id' => $relationship->id
            ];
            if (strtolower($relationship->name) === 'other') {
                $relationship_items[$i]['action'] = 'setOtherRelationsip';
            }
        }
        return $relationship_items;
    }

    /**
     * Retrieves a list of Relationships.
     *
     * @return array
     * @throws Exception
     */
    public function getContactMethodItemSet()
    {
        $contact_methods = \OphTrConsent_PatientContactMethod::model()->findAll();
        foreach ($contact_methods as $i => $method) {
            $signature_require_string = '';

            $contact_method_items[$i] = [
                'label' => $method->name,
                'item_id' => $method->id,
                'signature_require' => $method->need_signature,
                'signature_require_string' => $method->getDefaultSignatureRequiredString(),
            ];
            if (strtolower($method->name) === 'other') {
                $contact_method_items[$i]['action'] = 'setOtherContactMethod';
            }
        }
        return $contact_method_items;
    }

    /**
     * Retrieves an item sets.
     *
     * @return array
     */
    public function getContactTypeItemSet()
    {
        $contact_type_items = [
            [
                'search_url' => Yii::app()->createUrl('/OphTrConsent/contact') . '/AllPatientContacts',
                'label' => 'Patient contacts',
                'contact_type_id' => self::PATIENT_CONTACTS_TYPE,
                'id' => 'adder_dialog_patient_contact_button',
            ],
            [
                'search_url' => Yii::app()->createUrl('/OphTrConsent/contact') . '/OpeneyesContactsWithUser',
                'label' => 'Openeyes users',
                'contact_type_id' => self::OPENEYES_USERS_TYPE,
                'id' => 'adder_dialog_openeyes_users_contact_button'
            ]
        ];

        $relationship_items = $this->getRelationshipItemSet();

        $contact_method_items = $this->getContactMethodItemSet();

        $itemSets = array(
            [
                'items' => $contact_type_items,
                'header' => 'Contact type',
                'multiSelect' => false
            ],
            [
                'items' => $relationship_items,
                'header' => 'Relationship',
                'multiSelect' => false
            ],
            [
                'items' => $contact_method_items,
                'header' => 'Contact method',
                'multiSelect' => false
            ]
        );
        return $itemSets;
    }

    public function afterSave()
    {
        $existing_ids = [];
        $data_all = [];
        $request = \Yii::app()->request;
        $model_name = \CHtml::modelName($this);
        $post_data = $request->getPost($model_name);
        $new_ids = [];

        if (isset($post_data['jsonData'])) {
            foreach ($post_data['jsonData'] as $idx => $json) {
                if (empty($json)) {
                    continue;
                }
                $data = json_decode(htmlspecialchars_decode($json), true);
                $data_all[] = $data;
                $existing_id = isset($data['existing_id']) ? (int)$data['existing_id'] : null;

                if ($existing_id && $model = Ophtrconsent_OthersInvolvedDecisionMakingProcessContact::model()->find(
                        'element_id=? AND id=?',
                        array($this->id, $data['existing_id'])
                    )
                ) {
                    $model->comment = $post_data['comment'][$idx];
                    $model->signature_required = $post_data['signature_required'][$idx];
                } else {
                    $model = new Ophtrconsent_OthersInvolvedDecisionMakingProcessContact();
                    $model->setAttributes($data);
                    $model->element_id = $this->id;
                    $model->signature_required = $post_data['signature_required'][$idx];
                }
                if (!$model->save()) {
                    throw new Exception('Unable to save procedure item: ' . print_r($model->getErrors(), true));
                } else {
                    $new_ids[] = $model->id;
                }
            }
        }

        $existing_ids = array_merge(array_column($data_all, 'existing_id'), $new_ids);
        $criteria = new \CDbCriteria();
        $criteria->addCondition('element_id = :element_id');
        $criteria->params = array(':element_id' => $this->id);
        $criteria->addNotInCondition('id', $existing_ids);
        $esign_element = new \Ophtrconsent_OthersInvolvedDecisionMakingProcessContact;
        $esign_element->deleteAll($criteria);

        return parent::afterSave();
    }

    public function getRequiredSignatures($only_required = true) : array
    {
        $result = [];
        $contacts = $this->consentContact;

        foreach ($contacts as $contact) {
            $user = null;
            if($contact->signature_required === '0' && $only_required ){
                continue;
            }
            $signature_id = $contact->contact_signature_id;
            if ($signature_id) {
                $result[] = OphTrConsent_Signature::model()->findByPk($signature_id);
            } else {
                $signature_type_id = self::SIGNATURE_TYPES[$contact->contact_type_id];
                if ( (int)$contact->contact_type_id === self::OPENEYES_USERS_TYPE ) {
                    $user = User::model()->findByPk($contact->contact_user_id);
                    $user_name = $user->getFullNameAndTitleAndQualifications();
                } else {
                    $user_name = $contact->getFullName();
                }

                $sig = new OphTrConsent_Signature();
                $sig->setAttributes([
                    "type" => $signature_type_id,
                    "signatory_role" => $contact->getRelationshipName(),
                    "signatory_name" => $user_name,
                    "initiator_row_id" => $contact->id,
                ]);

                $sig->user_id = ($user ? $user->id : null);
                $result[] = $sig;
            }
        }

        return $result;
    }

    public function afterSignedCallback(int $row_id, int $signature_id) : void
    {
        $contact = \Ophtrconsent_OthersInvolvedDecisionMakingProcessContact::model()->findByPk($row_id);
        $contact->contact_signature_id = $signature_id;
        $contact->save(false, ["contact_signature_id"]);
    }
}
