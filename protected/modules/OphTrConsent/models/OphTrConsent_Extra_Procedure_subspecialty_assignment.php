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
 * This is the model class for table "proc_subspecialty_assignment".
 *
 * The followings are the available columns in table 'proc_subspecialty_assignment':
 *
 * @property int $id
 * @property int $proc_id
 * @property int $subspecialty_id
 * @property int $institution_id
 * @property int $display_order
 *
 * The followings are the available model relations:
 * @property Subspecialty $subspecialty
 * @property Procedure $procedure
 * @property Institution $institution
 */
class OphTrConsent_Extra_Procedure_subspecialty_assignment extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphTrConsent_Extra_Procedure_subspecialty_assignment|BaseActiveRecord the static model class
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
        return 'ophtrconsent_extra_proc_subspecialty_assignment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('extra_proc_id, subspecialty_id', 'required'),
            array(
                'extra_proc_id', 'exist',
                'attributeName' => 'id',
                'className' => 'OphTrConsent_Extra_Procedure',
                'message' => 'The specified extra procedure does not exist.',
            ),
            array(
                'subspecialty_id', 'exist',
                'attributeName' => 'id',
                'className' => 'Subspecialty',
                'message' => 'The specified subspecialty does not exist.',
            ),
            array('extra_proc_id, subspecialty_id', 'length', 'max' => 10),
            array('extra_proc_id, subspecialty_id, institution_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, extra_proc_id, subspecialty_id,', 'safe', 'on' => 'search'),
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
            'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
            'extra_procedure' => array(self::BELONGS_TO, 'OphTrConsent_Extra_Procedure', 'extra_proc_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'extra_proc_id' => 'Extra procedure',
            'subspecialty_id' => 'Subspecialty',
        );
    }

    /**
     * Retrieves a list of procedures associated with the subspecialty with the given id.
     *
     * @param int $id
     *
     * @return array of procedures (proc_id=>term)
     */
    public function getExtraProcedureListFromSubspecialty($id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('institution_id IS NULL OR institution_id = :institution_id');
        $criteria->addCondition('subspecialty_id IS NULL OR subspecialty_id = :subspecialty_id');
        $criteria->params = array(
            ':institution_id' => Yii::app()->session['selected_institution_id'],
            ':subspecialty_id' => $id,
        );
        $list = self::model()->findAll($criteria);
        $result = array();

        foreach ($list as $subspecialty) {
            $result[$subspecialty->extra_procedure->id] = $subspecialty->extra_procedure->term;
        }

        return $result;
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
        $criteria->compare('proc_id', $this->proc_id, true);
        $criteria->compare('subspecialty_id', $this->subspecialty_id, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
