<?php

namespace OEModule\OphCoCvi\models;

use OEModule\OphCoCvi\widgets\PatientSignatureCaptureElement;

/**
 * This is the model class for table "et_ophcocvi_patient_signature"
 *
 * The followings are the available columns in table 'et_ophcocvi_patient_signature':
 * @property string $relationship_status
 * @property int $consented_to_gp
 * @property int $consented_to_la
 * @property int $consented_to_rcop
 * @property string $consented_for
 *
 * The followings are the available model relations:
 * @property \OEModule\OphCoCvi\models\OphCoCvi_ConsentConsignee[] $consentConsignees
 */
class Element_OphCoCvi_Esign extends \BaseEsignElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return static the static model class
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
        return 'et_ophcocvi_esign';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id', 'safe'),
            array('id, event_id', 'safe', 'on' => 'search'),
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
            'event' => array(self::BELONGS_TO, Event::class, 'event_id'),
            'user' => array(self::BELONGS_TO, User::class, 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
            'signatures' => array(self::HAS_MANY, OphCoCorrespondence_Signature::class, 'element_id'),
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
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return OphCoCorrespondence_Signature[]
     */
    public function getSignatures(): array
    {

        $consultant = new OphCoCvi_Signature_Entry();
        $consultant->signatory_role = "Consultant";
        $consultant->type = \BaseSignature::TYPE_OTHER_USER;

        $secretary = new OphCoCvi_Signature_Entry();
        $secretary->signatory_role = "Secretary";
        $secretary->type = \BaseSignature::TYPE_SECRETARY;

        return !empty($this->signatures) ? $this->signatures : [$consultant, $secretary];
    }

    /**
     * A correspondence is signed if at least one of the signatures
     * (consultant or secretary) is done
     *
     * @return bool
     */
    public function isSigned() : bool
    {
        return !empty(
        array_filter(
            $this->signatures,
            function($signature) {
                return $signature->isSigned();
            }
        )
        );
    }

    /**
     * @inheritDoc
     */
    public function getUnsignedMessage(): string
    {
        return "This CVI must be signed by either a Consultant or a Secretary before it can be sent.";
    }

    /**
     * @param array $elements
     */
    public function eventScopeValidation(array $elements)
    {
        $elements = array_filter(
            $elements,
            function ($element) {
                return $element instanceof ElementLetter;
            }
        );
        if(!empty($elements)) {
            $element_letter = $elements[0];
            /** @var ElementLetter $element_letter */
            if(!$this->isSigned() && !$element_letter->draft) {
                $this->addError(
                    "id",
                    "At least one signature must be provided to finalize this CVI."
                );
            }
        }
    }
}
