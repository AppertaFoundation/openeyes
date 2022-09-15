<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;
use OE\factories\models\traits\HasFactory;


/**
 * This is the model class for table "ophciexamination_allergy".
 *
 * Could not be named Allergy due to conflicts with core class (that is left in place as a view on this model)
 *
 * @property int $id
 * @property string $name
 * @property int $display_order
 * @property boolean $is_other
 * @property int $medication_set_id
 *
 * @property \MedicationSet $medicationSet
 */
class OphCiExaminationAllergy extends \BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Allergy the static model class
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
        return 'ophciexamination_allergy';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order');
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, display_order, medication_set_id, active', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            "medicationSet" => array(self::HAS_ONE, \MedicationSet::class, "medication_set_id"),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @TODO: replace with DB property
     * @return bool
     */
    public function isOther()
    {
        return $this->name === 'Other';
    }

    function unchecked($element_id){
        return self::model()->findAll('id NOT IN (SELECT allergy_id FROM ophciexamination_allergy_entry WHERE element_id = ?) AND id != 17', array($element_id));
    }

    public function __toString()
    {
        return $this->name;
    }
}
