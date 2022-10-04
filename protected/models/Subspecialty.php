<?php
use OE\factories\models\traits\HasFactory;
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
 * This is the model class for table "subspecialty".
 *
 * The followings are the available columns in table 'subspecialty':
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string $ref_spec
 *
 * The followings are the available model relations:
 * @property ServiceSubspecialtyAssignment $serviceSubspecialtyAssignment
 * @property Specialty $specialty
 */
class Subspecialty extends BaseActiveRecordVersioned
{
    use HasFactory;

    const SELECTION_ORDER = 'name';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'subspecialty';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false) . '.name');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, ref_spec', 'required'),
            array('name, ref_spec', 'length', 'max' => 40),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, short_name, ref_spec', 'safe', 'on' => 'search'),
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
            'specialty' => array(self::BELONGS_TO, 'Specialty', 'specialty_id'),
            'serviceSubspecialtyAssignment' => array(self::HAS_ONE, 'ServiceSubspecialtyAssignment', 'subspecialty_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Subspecialty',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('ref_spec', $this->ref_spec, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Fetch an array of subspecialty IDs and names, by default does not return non medical subspecialties (as defined by parent specialty).
     *
     * @param bool $nonmedical
     *
     * @return array
     */
    public function getList($nonmedical = false)
    {
        if (!$nonmedical) {
            $list = self::model()->with('specialty')->findAll('specialty.specialty_type_id = :surgical or specialty.specialty_type_id = :medical', array(':surgical' => 1, ':medical' => 2));
        } else {
            $list = self::model()->findAll();
        }
        $result = array();

        foreach ($list as $subspecialty) {
            $result[$subspecialty->id] = $subspecialty->name;
        }

        asort($result);

        return $result;
    }

    public function findAllByCurrentSpecialty()
    {
        if (!isset(Yii::app()->params['institution_specialty'])) {
            throw new Exception('institution_specialty code is not set in params');
        }

        if (!$specialty = Specialty::model()->find('code=?', array(Yii::app()->params['institution_specialty']))) {
            throw new Exception('Specialty not found: '.Yii::app()->params['institution_specialty']);
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('specialty_id = :specialty_id');
        $criteria->params[':specialty_id'] = $specialty->id;
        $criteria->order = 'name asc';

        return self::model()->findAll($criteria);
    }

    public function getTreeName()
    {
        return $this->ref_spec;
    }

    public function getSubspecialtyEmail()
    {
        $firm = $this->serviceSubspecialtyAssignment->firms;

        // Checking only the first element of the array for the email, because there is a validation i.e. at most
        // only one subspecialty can have the email address set.
        $email = $firm[0]['service_email'];

        return $email ?? null;
    }
}
