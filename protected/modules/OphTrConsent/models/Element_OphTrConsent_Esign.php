<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class Element_OphTrConsent_Esign
 *
 * @property int $id
 * @property int $event_id
 * @property int $healthprof_signature_id
 *
 * @property Event $event
 * @property OphTrConsent_Signature[] $signatures
 */
use OEModule\OphTrConsent\models\RequiresSignature;
use OEModule\OphTrConsent\widgets\EsignElementWidget;

class Element_OphTrConsent_Esign extends BaseEsignElement implements RequiresSignature
{
    protected $widgetClass = EsignElementWidget::class;

    /** @var array Signature items cached so that they're not collected with every function call */
    private array $cached_signatures = [];

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
        return 'et_ophtrconsent_esign';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id, healthprof_signature_id', 'safe'),
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
            'signatures' => array(self::HAS_MANY, OphTrConsent_Signature::class, 'element_id'),
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
     * @return OphTrConsent_Signature[]
     */
    public function getSignatures(): array
    {
        if (!empty($this->cached_signatures)) {
            return $this->cached_signatures;
        }
        $default_signatures = [];
        foreach ($this->event->getElements() as $element) {
            if ($element instanceof RequiresSignature) {
                $signatures = $element->getRequiredSignatures();
                foreach ($signatures as $signature) {
                    if (!$signature instanceof OphTrConsent_Signature) {
                        throw new Exception(get_class($element).
                            "::getRequiredSignatures must return an array of OphTrConsent_Signature");
                    }
                    $signature->initiator_element_type_id = $element->getElementType()->id;
                }
                $default_signatures = array_merge($default_signatures, $signatures);
            }
        }
        return $default_signatures;
    }

    private function countRemainingSignatures() : int
    {
        return count(array_filter($this->getSignatures(), function ($signature) {
            return $signature->is_mandatory && !$signature->isSigned();
        }));
    }

    /**
     * @inheritDoc
     */
    public function isSigned(): bool
    {
        return $this->countRemainingSignatures() === 0;
    }

    /**
     * @inheritDoc
     */
    public function getUnsignedMessage(): string
    {
        $remaining_count = $this->countRemainingSignatures();
        return "$remaining_count signature".($remaining_count > 1 ? "s" : "")." still required to complete consent form.";
    }

    /**
     * @inheritDoc
     */
    public function getRequiredSignatures(): array
    {
        if($this->healthprof_signature_id) {
            return [OphTrConsent_Signature::model()->findByPk($this->healthprof_signature_id)];
        } else {
            $user = User::model()->findByPk(Yii::app()->session['user']->id);
            $sig = new OphTrConsent_Signature();
            $sig->setAttributes([
                "type" => BaseSignature::TYPE_OTHER_USER,
                "signatory_role" => "Health professional",
                "signatory_name" => $user->getFullNameAndTitleAndQualifications(),
                "initiator_row_id" => 0,
            ]);
            $sig->user_id = $user->id;
            return [$sig];
        }
    }

    /**
     * @inheritDoc
     */
    public function afterSignedCallback(int $row_id, int $signature_id): void
    {
        $this->healthprof_signature_id = $signature_id;
        $this->save(false, ["healthprof_signature_id"]);
    }

    /**
     * @return bool True if the signature is the one being signed in print mode
     */
    public function isBeingSigned(BaseSignature $signature) : bool
    {
        $req = Yii::app()->request;
        return (int)$req->getParam("sign") > 0
            && (int)$signature->initiator_element_type_id === (int)$req->getParam("initiator_element_type_id")
            && (int)$signature->initiator_row_id === (int)$req->getParam("initiator_row_id");
    }

    /**
     * @return object filtered signature object.
     */
    public function getSignatureByAttributes($element, $custom_key) {
        if (!is_null($element->{$custom_key})) {
            $filtered_signature = array_filter($element->getSignatures(), function ($signature) use ($element, $custom_key) {
                return $signature->id === $element->{$custom_key};
            });
        } else {
            $req = Yii::app()->request;
            $filtered_signature = array_filter($element->getSignatures(), function ($signature) use ($req) {
                return
                    (int)$signature->initiator_element_type_id === (int)$req->getParam("initiator_element_type_id")
                    && (int)$signature->initiator_row_id === (int)$req->getParam("initiator_row_id");

            });
        }
        return isset($filtered_signature[0]) ? $filtered_signature[0] : false;
    }
}
