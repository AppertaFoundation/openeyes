<?php
/**
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

class OphCiExamination_ClinicProcedure extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return BaseActiveRecord the static model class
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
        return 'ophciexamination_clinic_procedure';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id, proc_id, institution_id, firm_id, subspecialty_id', 'safe'),
            array('id, proc_id, institution_id, firm_id, subspecialty_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'procedure' => [self::BELONGS_TO, 'Procedure', 'proc_id'],
            'institution' => [self::BELONGS_TO, 'Institution', 'institution_id'],
            'firm' => [self::BELONGS_TO, 'Firm', 'firm_id'],
            'subspecialty' => [self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'],
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('proc_id', $this->proc_id, true);
        $criteria->compare('institution_id', $this->institution_id, true);
        $criteria->compare('firm_id', $this->firm_id, true);
        $criteria->compare('subspecialty_id', $this->subspecialty_id, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getClinicProceduresItemSet()
    {
        $institution_id = \Yii::app()->session['selected_institution_id'];
        $firm_id = \Yii::app()->session['selected_firm_id'];
        $subspecialty_id = \Firm::model()->findByPk($firm_id)->serviceSubspecialtyAssignment->subspecialty_id;

        $criteria = new \CDbCriteria();
        $criteria->select = 't.id, term';
        $criteria->with = 'clinic_procedure';
        $criteria->together = true;
        $criteria->addCondition('t.active = 1 AND t.is_clinic_proc = 1');
        $criteria->addCondition('clinic_procedure.institution_id IS NULL OR clinic_procedure.institution_id = :institution_id');
        $criteria->addCondition('clinic_procedure.firm_id IS NULL OR clinic_procedure.firm_id = :firm_id');
        $criteria->addCondition('clinic_procedure.subspecialty_id IS NULL OR clinic_procedure.subspecialty_id = :subspecialty_id');
        $criteria->params = [':institution_id' => $institution_id, ':firm_id' => $firm_id, ':subspecialty_id' => $subspecialty_id];

        $clinic_procedures = \Procedure::model()->findAll($criteria);

        $itemSets = [];
        foreach ($clinic_procedures as $procedure) {
            $itemSets[] = [
                'items' => $procedure->term,
                'id' => $procedure->id,
            ];
        }

        return $itemSets;
    }
}
