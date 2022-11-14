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
 * This is the model class for table "et_ophtrconsent_confirm".
 *
 * @property int $confirmed
 * @property int $event_id
 * @property int $signature_id
 *
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Event $event
 */

use OEModule\OphTrConsent\models\RequiresSignature;

class Element_OphTrConsent_Confirm extends BaseEventTypeElement implements RequiresSignature
{

    public function getElementTypeName()
    {
        return "Confirm consent";
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophtrconsent_confirmation';
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, confirmed', 'safe'),
            array('id, event_id, confirmed', 'safe', 'on' => 'search'),
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
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
            'confirmed' =>  'Patient has confirmed consent',
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

        $criteria = new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('signature_id', $this->signature_id, true);
        $criteria->compare('confirmed', $this->confirmed, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_OphTrConsent_Confirm the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getRequiredSignatures(): array
    {
        $result = [];
        if ($this->signature_id) {
            $result[] = OphTrConsent_Signature::model()->findByPk($this->signature_id);
        } else {
            $user = User::model()->findByPk(Yii::app()->session['user']->id);

            $sig = new OphTrConsent_Signature();
            $sig->setAttributes([
                "type" => BaseSignature::TYPE_OTHER_USER,
                "signatory_role" => "Confirmed by",
                "signatory_name" => $user->getFullName(),
                "initiator_row_id" => 6,
            ]);
            $sig->user_id = $user->id;
            $sig->signed_user_id = $user->id;

            if (SettingMetadata::model()->checkSetting('require_pin_for_consent', 'no')) {
                $sig->proof = \SignatureHelper::getSignatureProof($user->signature->id, new \DateTime(), $user->id);
                $sig->setDataFromProof();
            }

            $result[] = $sig;
        }
        return $result;
    }

    public function afterSignedCallback(int $row_id, int $signature_id): void
    {
        $this->signature_id = $signature_id;
        if(!$this->save(false, ["signature_id"])) {
            throw new Exception('Unable to save Confirm: ' . print_r($this->getErrors(), true));
        };
    }
}
