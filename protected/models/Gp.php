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
 * This is the model class for table "Gp".
 *
 * The followings are the available columns in table 'Gp':
 *
 * @property int $id
 * @property string $obj_prof
 * @property string $nat_id
 *
 * The followings are the available model relations:
 * @property Contact $contact
 */
class Gp extends BaseActiveRecordVersioned
{
    const UNKNOWN_SALUTATION = 'Doctor';
    const UNKNOWN_NAME = 'The General Practitioner';

    public $use_pas = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Gp the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Suppress PAS integration.
     *
     * @return Gp
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
        return 'gp';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('obj_prof, nat_id', 'required'),
            array('obj_prof, nat_id', 'length', 'max' => 20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, obj_prof, nat_id', 'safe', 'on' => 'search'),
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
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'obj_prof' => 'Obj Prof',
            'nat_id' => 'Nat',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('obj_prof', $this->obj_prof, true);
        $criteria->compare('nat_id', $this->nat_id, true);

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
     * Raise event to allow external data sources to update gp.
     *
     * @see CActiveRecord::afterFind()
     */
    protected function afterFind()
    {
        parent::afterFind();
        Yii::app()->event->dispatch('gp_after_find', array('gp' => $this));
    }

    /**
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function getLetterAddress($params = array())
    {
        if (!isset($params['patient'])) {
            throw new Exception('Patient must be passed for GP contacts.');
        }

        $contact = $address = null;

        if ($params['patient']->practice) {
            if (@$params['contact']) {
                $contactRelation = $params['contact'];
                $contact = $params['patient']->practice->$contactRelation;
            } else {
                $contact = $params['patient']->practice->contact;
            }

            $address = $contact->address;
        }

        return $this->formatLetterAddress($contact, $address, $params);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return 'GP';
    }

    /**
     * @return mixed|string
     */
    public function getCorrespondenceName()
    {
        return $this->contact->fullName;
    }

    /**
     * @return array|CDbDataReader
     */
    public function gpCorrespondences()
    {
        $sql = 'SELECT gp.id, CONCAT_WS(" ", title, first_name, last_name) as correspondenceName
                FROM gp 
                JOIN contact on gp.contact_id = contact.id';

        $query = $this->getDbConnection()->createCommand($sql);

        return $query->queryAll();
    }
}
