<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;


use OE\factories\models\traits\HasFactory;

/**
 * Class HistoryRisksEntry
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $element_id
 * @property int $risk_id
 * @property int $has_risk
 * @property string $other
 * @property string $comments
 *
 * @property OphCiExaminationRisk $risk
 * @property HistoryRisks $element
 */
class HistoryRisksEntry extends \BaseElement
{
    use HasFactory;

    public static $PRESENT = 1;
    public static $NOT_PRESENT = 0;
    public static $NOT_CHECKED = -9;
    /**
     * Returns the static model of the specified AR class.
     *
     * @return static
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
        return 'ophciexamination_history_risks_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, risk_id, other, has_risk, comments', 'safe'),
            array('risk_id', 'required'),
            array('has_risk', 'required', 'message'=>'Status cannot be blank'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, element_id, risk_id, other, has_risk, comments', 'safe', 'on' => 'search'),
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
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\HistoryRisks', 'element_id'),
            'risk' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExaminationRisk', 'risk_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'risk_id' => 'Risk',
        );
    }

    public function afterValidate()
    {
        if ($this->risk && $this->risk->isOther() && !$this->other) {
            $this->addError('other', 'Other description is required');
        }
        parent::afterValidate();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $criteria = new \CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('risk_id', $this->allergy_id, true);
        $criteria->compare('other', $this->other, true);
        $criteria->compare('has_risk', $this->has_risk, true);
        $criteria->compare('comments', $this->comments, true);
        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    protected function beforeSave()
    {
        if ($this->isModelDirty()) {
            $this->element->addAudit('edited-risks');
        }
        return parent::beforeSave();
    }

    /**
     * @return string
     */
    public function getDisplayRisk()
    {
        if ($this->other) {
            return $this->other;
        }
        return $this->risk ? $this->risk->name : '';
    }

    /**
     * @return string
     */
    public function getDisplayHasRisk()
    {
        if ($this->has_risk === (string) static::$PRESENT) {
            return 'Present';
        } elseif ($this->has_risk === (string) static::$NOT_PRESENT) {
            return 'Not present';
        }
        return 'Not checked';
    }

    /**
     * @param bool $show_status
     * @return string
     */
    public function getDisplay($show_status = false)
    {
        if ($show_status) {
            $res = $this->getDisplayRisk();
            $res .= ' - ' . $this->getDisplayHasRisk();

            if ($this->comments) {
                $res .= ' (' . $this->comments . ')';
            }
        } else {
            $res = [
                'risk' => $this->getDisplayRisk(),
                'comments' => $this->comments
            ];
        }
        return $res;

    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDisplay(true);
    }
}
