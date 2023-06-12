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

namespace OEModule\OphCiExamination\models;

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "ophciexamination_postop_complications".
 *
 * @property int $id
 * @property string $name
 * @property int $display_order
 */
class OphCiExamination_PostOpComplications extends \BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphCiExamination_PostOpComplications the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected $auto_update_relations = true;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_postop_complications';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false) . '.display_order');
    }

    /**
     * @return array validation rules for model OphCiExamination_Comorbidities_Item.
     */
    public function rules()
    {
        return array(
                array('name, display_order', 'required'),
                array('id, name, display_order', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'complication' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_Et_PostOpComplications', 'complication_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
                'id' => 'Common Complications',
                'name' => 'Common Complications',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('display_order', $this->display_order, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function getPostOpComplicationsList($element_id, $operation_note_id, $subspecialty_id, $eye_id, $term = null)
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('active = 1');

        $criteria->addCondition('t.id NOT IN (SELECT DISTINCT complication_id '
                                                . 'FROM ophciexamination_postop_et_complications '
                                                . 'WHERE element_id = :element_id AND operation_note_id = :operation_note_id AND eye_id = :eye_id) ');

        $criteria->params['element_id'] = $element_id;
        $criteria->params['operation_note_id'] = $operation_note_id;
        $criteria->params['eye_id'] = $eye_id;

        if ($term && strlen($term) > 0) {
            $term = addcslashes($term, '%_');
            $criteria->addSearchCondition('t.name', $term);
        } else {
            $criteria->join = 'JOIN ophciexamination_postop_complications_subspecialty ON t.id = ophciexamination_postop_complications_subspecialty.complication_id';
            $criteria->addCondition('institution_id = :institution_id AND subspecialty_id = :subspecialty_id');
            $criteria->params['subspecialty_id'] = $subspecialty_id;
            $criteria->params['institution_id'] = \Yii::app()->session['selected_institution_id'];
            $criteria->order = 'ophciexamination_postop_complications_subspecialty.display_order ASC';
        }

        return $this->findAll($criteria);
    }

    /**
     * Named scope to fetch enabled items for the given subspecialty.
     *
     * @param int $institution_id
     * @param int|null $subspecialty_id Null for default episode summary
     *
     * @return EpisodeSummaryItem
     */
    public function enabled($institution_id, $subspecialty_id = null)
    {
        $criteria = array(
            'join' => 'LEFT JOIN ophciexamination_postop_complications_subspecialty AS cs ON t.id = cs.complication_id',
            'order' => 'cs.display_order',
        );

        $criteria['condition'] = '(cs.institution_id IS NULL OR cs.institution_id = :institution_id) AND cs.subspecialty_id = :subspecialty_id';
        $criteria['params'] = array('institution_id' => $institution_id, 'subspecialty_id' => $subspecialty_id);

        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }

    /**
     * Named scope to fetch available (non-enabled) items for the given subspecialty.
     *
     * @return EpisodeSummaryItem
     */
    public function available()
    {
        $criteria['join'] = 'LEFT JOIN ophciexamination_postop_complications_subspecialty AS cs ON t.id = cs.complication_id';
        $criteria['order'] = 't.name ASC, t.id ASC';

        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }

    /**
     * Assign the given items to the given episode summary.
     *
     * @param array    $item_ids
     * @param int|null $institution_id
     * @param int|null $subspecialty_id
     */
    public function assign($item_ids, $institution_id = null, $subspecialty_id = null)
    {
        if (is_null($institution_id)) {
            $institution_id = \Yii::app()->session['selected_institution_id'];
        }
        $this->dbConnection->createCommand()->delete(
            'ophciexamination_postop_complications_subspecialty',
            'institution_id = :institution_id AND ' . ($subspecialty_id ? 'subspecialty_id = :subspecialty_id' : 'subspecialty_id is null'),
            array('institution_id' => $institution_id, 'subspecialty_id' => $subspecialty_id)
        );

        if ($item_ids) {
            $rows = array();
            foreach ($item_ids as $display_order => $complication_id) {
                $rows[] = array(
                    'complication_id' => $complication_id,
                    'institution_id' => $institution_id,
                    'subspecialty_id' => $subspecialty_id,
                    'display_order' => $display_order,
                );
            }

            $this->dbConnection->getCommandBuilder()->createMultipleInsertCommand('ophciexamination_postop_complications_subspecialty', $rows)->execute();
        }
    }
}
