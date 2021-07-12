<?php

/**
 * Class Element_PatientSignature
 *
 * @property int id
 * @property int $signatory_person
 * @property int $event_id
 * @property int $protected_file_id
 * @property int $signatory_required
 * @property string $signature_date
 * @property string $signatory_name
 * @property string $signatory_person_label
 *
 * @property Event $event
 * @property ProtectedFile $signature_file
 */

abstract class Element_PatientSignature extends BaseEventTypeElement implements WidgetizedElement
{
    public $can_be_signed_in_view_mode = true;
    public $signature_date_readonly = true;
    public $required_checkbox_visible = false;

    const SIGNATORY_PERSON_PATIENT = 1;
    const SIGNATORY_PERSON_REPRESENTATIVE = 2;
    const SIGNATORY_PERSON_INTERPRETER = 3;
    const SIGNATORY_PERSON_WITNESS = 4;
    const SIGNATORY_PERSON_PARENT = 5;

    /** @var BaseEventElementWidget */
    public $widget = null;

    public function getWidgetClass()
    {
        return PatientSignatureCaptureElement::class;
    }

    public function getWidget()
    {
        return $this->widget;
    }

    public function setWidget(BaseEventElementWidget $widget)
    {
        $this->widget = $widget;
    }

    public function tableName()
    {
        return "et_patient_signature";
    }

    public function rules()
    {
        $rules = array(
            array('event_id, signatory_person, signature_date, signatory_name, protected_file_id, signatory_required', 'safe'),
            array('signatory_name', 'validateSignatoryName', 'except' => 'draft'),
            array(
                'event_id, signatory_person, signature_date, signatory_name, protected_file_id, signatory_required',
                'safe',
                'on' => 'search'
            ),
        );

        if(!$this->can_be_signed_in_view_mode) {
            $rules = array_merge($rules, array(array('protected_file_id', 'required')));
        }

        return $rules;
    }

    public function validateSignatoryName($attribute, $params)
    {
        if($this->signatory_required) {
            if($this->signatory_person != self::SIGNATORY_PERSON_PATIENT && $this->signatory_name == "") {
                $this->addError("signatory_name", "Signatory name must not be empty.");
            }
        }
    }
	
	/**
	 * Validate function of Freehand drawing elements
	 * @param type $object
	 * @param type $attribute
	 */
	public function validateFreehandDrawing( $object, $attribute )
	{
		if(((int)$this->protected_file_id === 0) && (trim($this->comments) === "")){
			$this->addError('protected_file_id','The image or the comments field cannot be empty!');
		}
	}

    public function relations()
    {
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'signature_file' => array(self::BELONGS_TO, 'ProtectedFile', 'protected_file_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'signatory_person' => 'Signed by',
            'signature_date' => 'Signature date',
            'signatory_name' => 'Signatory name',
            'protected_file_id' => 'Signature image',
        );
    }

    public function isAtTip()
    {
        return false;
    }

    protected function getSignatoryPersonLabels()
    {
        return array(
            self::SIGNATORY_PERSON_PATIENT => 'Patient',
            self::SIGNATORY_PERSON_REPRESENTATIVE => 'Patient\'s representative',
            self::SIGNATORY_PERSON_INTERPRETER => 'Interpreter',
            self::SIGNATORY_PERSON_WITNESS => 'Withess',
            self::SIGNATORY_PERSON_PARENT => 'Parent/Guardian',
        );
    }

    protected function getSignatoryPersonLabel($key)
    {
        $labels = $this->getSignatoryPersonLabels();
        return array_key_exists($key, $labels) ? $labels[$key] : "??";
    }

    public function getSignatoryPersonOptions()
    {
        return $this->getSignatoryPersonLabels();
    }

    public function setDefaultOptions(?Patient $patient = NULL)
    {
        if($this->isNewRecord && !Yii::app()->request->isPostRequest) {
            $this->signatory_required = (int)!$this->required_checkbox_visible;
        }
    }

    public function getSignedBy()
    {
        return $this->getSignatoryPersonLabel($this->signatory_person);
    }


    public function beforeValidate()
    {
        if(
            (!$this->signature_date && $this->getIsNewRecord() && $this->protected_file_id)
            ||
            (!$this->getIsNewRecord() && is_null($this->originalAttributes['protected_file_id']) && $this->protected_file_id)
        ) {
            $this->signature_date = $this->signature_file->created_date;
        }

        return parent::beforeValidate();
    }

    public function beforeSave()
    {
        if($this->signatory_required == 0) {
            $this->protected_file_id = null;
        }

        if($this->signatory_name == "" && $this->signatory_person == self::SIGNATORY_PERSON_PATIENT) {
            $this->signatory_name = $this->event->episode->patient->getFullName();
        }

        return parent::beforeSave();
    }

    public function isSigned()
    {
        return $this->protected_file_id != "";
    }

    public function getAdditionalFields()
    {
        return array();
    }

    public function afterFind()
    {
        if($this->signature_date === "0000-00-00") {
            $this->signature_date = null;
        }

        return parent::afterFind();
    }
}