<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "practice".
 *
 * The followings are the available columns in table 'practice':
 *
 * @property int $id
 * @property string $code
 * @property string $phone
 *
 * The followings are the available model relations:
 * @property Contact $contact
 * @property CommissioningBody[] $commissioningbodies
 */
class Practice extends BaseActiveRecordVersioned
{
    public $use_pas = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Practice the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Suppress PAS integration.
     *
     * @return Practice
     */
    public function noPas()
    {
        // Clone to avoid singleton problems with use_pas flag
        $model = clone $this;
        $model->use_pas = false;

        return $model;
    }

    public function behaviors()
    {
        return array(
            'ContactBehavior' => array(
                'class' => 'application.behaviors.ContactBehavior',
            ),
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'practice';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('phone, contact_id, code', 'safe'),
            array('id, code', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'commissioningbodyassigments' => array(self::HAS_MANY, 'CommissioningBodyPracticeAssignment', 'practice_id'),
            'commissioningbodies' => array(self::MANY_MANY, 'CommissioningBody', 'commissioning_body_practice_assignment(practice_id, commissioning_body_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'code' => 'Code',
            'phone' => 'Phone',
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
        $criteria->compare('code', $this->code, true);
        $criteria->compare('phone', $this->phone, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Pass through use_pas flag to allow pas supression.
     *
     * @see CActiveRecord::instantiate()
     */
    protected function instantiate($attributes)
    {
        $model = parent::instantiate($attributes);
        $model->use_pas = $this->use_pas;

        return $model;
    }

    /**
     * Raise event to allow external data sources to update practice.
     *
     * @see CActiveRecord::afterFind()
     */
    protected function afterFind()
    {
        parent::afterFind();
        Yii::app()->event->dispatch('practice_after_find', array('practice' => $this));
    }

    /**
     * get the CommissioningBody of the CommissioningBodyType $type
     * currently assumes there would only ever be one commissioning body of a given type.
     *
     * @param CommissioningBodyType $type
     *
     * @return CommissioningBody
     */
    public function getCommissioningBodyOfType($type)
    {
        foreach ($this->commissioningbodies as $body) {
            if ($body->type->id == $type->id) {
                return $body;
            }
        }
    }

    public function getCorrespondenceName()
    {
        return Gp::UNKNOWN_NAME;
    }

    public function getSalutationName()
    {
        return Gp::UNKNOWN_SALUTATION;
    }
    
    public function getAddressLines()
    {
        if( isset($this->contact->address) ){
            return ($this->contact->address->address1 ? $this->contact->address->address1.", " : ' ')
            .($this->contact->address->address2 ? $this->contact->address->address2.", " : ' ')
            .($this->contact->address->city ? $this->contact->address->city.", " : ' ')
            .($this->contact->address->county ? $this->contact->address->county.", " : ' ')
            .($this->contact->address->postcode ? $this->contact->address->postcode.", " : ' ');
        } else {
            return '';
        }
    }

    public function getPracticeNames()
    {
        $name = $this->contact->getCorrespondenceName() ? $this->contact->getCorrespondenceName()." - " : '';
        $address1 = $this->contact->address->address1 ? $this->contact->address->address1 . ", " : '';
        $address2 = $this->contact->address->address2 ? $this->contact->address->address2 . ", " : '';
        $city = $this->contact->address->city ? $this->contact->address->city . ", " : '';
        $county = $this->contact->address->county ? $this->contact->address->county . ", " : '';
        $postcode = $this->contact->address->postcode ? $this->contact->address->postcode . ", " : '';
        $country = $this->contact->address->country->name ? $this->contact->address->country->name : '';
        return $name . $address1 . $address2 . $city . $county . $postcode . $country . '.';
    }

    /**
     * Delete commissioning body assignments for referential integrity
     * Note if patients are assigned to the practice, there will still be
     * a referential integrity error and the delete will fail.
     *
     * @return bool
     */
    protected function beforeDelete()
    {
        if (parent::beforeDelete()) {
            foreach ($this->commissioningbodyassigments as $cba) {
                $cba->delete();
            }

            return true;
        }
    }

    /**
     * Extend parent behaviour to enforce a transaction so that we don't lose commissioning
     * body assignments if the delete fails part way through.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete()
    {
        // perform this process in a transaction if one has not been created
        $transaction = Yii::app()->db->getCurrentTransaction() === null
            ? Yii::app()->db->beginTransaction()
            : false;

        try {
            if (parent::delete()) {
                if ($transaction) {
                    $transaction->commit();
                }

                return true;
            } else {
                if ($transaction) {
                    $transaction->rollback();
                }

                return false;
            }
        } catch (Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }
            throw $e;
        }
    }

    /**
     * @return array|CDbDataReader
     */
    public function practiceAddresses()
    {
        $sql = 'SELECT practice.id, CONCAT_WS(", ", address1, address2, city, county, postcode) as letterLine
                FROM practice 
                JOIN contact on practice.contact_id = contact.id
                JOIN address on contact.id = address.contact_id 
                WHERE ( (date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW()))';

        $query = $this->getDbConnection()->createCommand($sql);

        return $query->queryAll();
    }
}
