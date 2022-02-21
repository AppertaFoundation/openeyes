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
 * This is the model class for table "et_ophtrconsent_patient_attorney_deputy".
 *
 * The followings are the available columns in table 'et_ophtrconsent_patient_attorney_deputy':
 * @property integer $id
 * @property string $event_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property User $createdUser
 * @property User $lastModifiedUser
 */

namespace OEModule\OphTrConsent\models;

use OEModule\OphTrConsent\widgets\Contacts as ContactsWidget;
use OEModule\OphTrConsent\models\RequiresSignature;

class Element_OphTrConsent_PatientAttorneyDeputy extends \BaseEventTypeElement implements RequiresSignature
{
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    protected $default_from_previous = true;
    protected $widgetClass = ContactsWidget::class;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophtrconsent_patient_attorney_deputy';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('comments, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array('id, event_id, comments, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
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
            'comments' => 'Any other comments (including the circumstances considered in assessing the patient’s best interests)',
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
        $criteria=new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new \CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function getRequiredSignatures(): array
    {
        $result = [];

        $criteria = new \CDbCriteria();
        $criteria->addCondition('event_id = :event_id');
        $criteria->params = [
            ':event_id' => $this->event_id
        ];
        $contacts = \PatientAttorneyDeputyContact::model()->findAll($criteria);

        foreach ($contacts as $contact) {
            if ($signature_id = $contact->signature_id) {
                $result[] = \OphTrConsent_Signature::model()->findByPk($signature_id);
            } else {
                $sig = new \OphTrConsent_Signature();
                $sig->setAttributes([
                    "type" => \BaseSignature::TYPE_PATIENT,
                    "signatory_role" => $contact->contact->label->name,
                    "signatory_name" => $contact->contact->getFullName(),
                    "initiator_row_id" => $contact->id,
                ]);
                $result[] = $sig;
            }
        }

        return $result;
    }

    public function afterSignedCallback(int $row_id, int $signature_id): void
    {
        $contact = \PatientAttorneyDeputyContact::model()->findByPk($row_id);
        $contact->signature_id = $signature_id;
        if (!$contact->save(false, ["signature_id"])) {
            throw new \Exception('Unable to save patient deputy: ' . print_r($contact->getErrors(), true));
        };
    }
}
