<?php

/**
 * Class Element_ConsultantSignature
 *
 * @property int id
 * @property int $event_id
 * @property int $protected_file_id
 * @property int $signed_by_user_id
 * @property string $signature_date
 *
 * @property Event $event
 * @property ProtectedFile $signature_file
 * @property User $signed_by
 */

abstract class Element_ConsultantSignature extends BaseEventTypeElement implements WidgetizedElement
{
    public $can_be_signed_in_view_mode = true;
    public $signature_date_readonly = true;

    /** @var BaseEventElementWidget */
    public $widget = null;

    public $pin = null;

    public function getWidgetClass()
    {
        return ConsultantSignatureElementWidget::class;
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
        return "et_consultant_signature";
    }

    public function init()
    {
        return parent::init();
    }

    public function rules()
    {
        return array(
            array('event_id, protected_file_id, signed_by_user_id, signature_date, pin', 'safe'),
            array(
                'event_id, protected_file_id, signed_by_user_id, signature_date',
                'safe',
                'on' => 'search'
            ),
        );
    }

    public function relations()
    {
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'signature_file' => array(self::BELONGS_TO, 'ProtectedFile', 'protected_file_id'),
            'signed_by' => array(self::BELONGS_TO, User::class, 'signed_by_user_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'protected_file_id' => 'Signature image',
            'signature_date' => 'Signature date',
            'signed_by_user_id' => 'Signed by'
        );
    }

    public function beforeValidate()
    {
        if ($this->pin != "") {
            if (strlen(filter_var($this->pin, FILTER_SANITIZE_NUMBER_INT)) != 4) {
                $this->addError("pin", "PIN must be a four-digit number.");
            } else {
                $user = $this->signed_by;
                if (!$user->getDecryptedSignature($this->pin)) {
                    $this->addError("pin", "The entered PIN is invalid.");
                }
            }
        }

        if (
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
        if ($this->protected_file_id) {
            if ($this->signed_by_user_id == 0 || $this->signed_by_user_id == "") {
                $this->signed_by_user_id = Yii::app()->user->id;
            }
        }

        return parent::beforeSave();
    }

    public function isAtTip()
    {
        return false;
    }

    public function isSigned()
    {
        return $this->protected_file_id != "";
    }
}
