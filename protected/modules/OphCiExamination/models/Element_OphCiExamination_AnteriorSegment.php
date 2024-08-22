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

use Patient;

/**
 * This is the model class for table "et_ophciexamination_anteriorsegment".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $event_id
 * @property int $eye_id
 * @property string $left_eyedraw
 * @property string $left_eyedraw2
 * @property string $left_ed_report
 * @property int $left_pupil_id
 * @property int $left_nuclear_id
 * @property int $left_cortical_id
 * @property bool $left_pxe
 * @property bool $left_phako
 * @property string $left_description
 * @property string $right_eyedraw
 * @property string $right_eyedraw2
 * @property string $right_ed_report
 * @property int $right_pupil_id
 * @property int $right_nuclear_id
 * @property int $right_cortical_id
 * @property bool $right_pxe
 * @property bool $right_phako
 * @property string $right_description
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_AnteriorSegment extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    protected static $ed_persistence_attributes = array(
        'left_eyedraw' => \Eye::LEFT,
        'right_eyedraw' => \Eye::RIGHT,
    );

    public $service;

    /** Side view was introduced and the earlier data is not using it
    * This variable is used to identify if the side view should be rendered
    */
    public $has_side_view = false;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    // used for the letter string method in the eyedraw element behavior
    public $letter_string_prefix = "Anterior Segment:\n";

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return array(
            'EyedrawElementBehavior' => array(
                'class' => 'application.behaviors.EyedrawElementBehavior',
            ),
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_anteriorsegment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('eye_id, left_eyedraw, left_eyedraw2, left_ed_report, left_pupil_id, left_nuclear_id, left_cortical_id, left_pxe, left_phako, left_description,
						right_eyedraw, right_eyedraw2, right_ed_report, right_pupil_id, right_nuclear_id, right_cortical_id, right_pxe, right_phako, right_description', 'safe'),
                array('left_eyedraw, left_eyedraw2, left_ed_report', 'requiredIfSide', 'side' => 'left'),
                array('right_eyedraw, right_eyedraw2, right_ed_report', 'requiredIfSide', 'side' => 'right'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id, left_eyedraw, left_pupil_id, left_nuclear_id, left_cortical_id, left_pxe, left_phako, left_description,
						right_eyedraw, right_pupil_id, right_nuclear_id, right_cortical_id, right_pxe, right_phako, right_description', 'safe', 'on' => 'search'),
        );
    }

    public function sidedFields()
    {
        return array('pupil_id', 'cortical_id', 'pxe', 'eyedraw', 'eyedraw2', 'ed_report', 'phako', 'description', 'nuclear_id');
    }

    public function sidedDefaults()
    {
        return array();
    }

    public function canCopy()
    {
        return true;
    }

    /**
     * @inheritdoc
     * @return bool
     */
    public function isDirtyWhenNewRecord(): bool
    {
        return true;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
                'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'right_pupil' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_Pupil', 'right_pupil_id'),
                'left_pupil' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_Pupil', 'left_pupil_id'),
                'right_nuclear' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_Nuclear', 'right_nuclear_id'),
                'left_nuclear' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_Nuclear', 'left_nuclear_id'),
                'right_cortical' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_Cortical', 'right_cortical_id'),
                'left_cortical' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_Cortical', 'left_cortical_id'),
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
                'left_eyedraw' => 'Eyedraw',
                'left_ed_report' => 'Report',
                'left_pupil_id' => 'Pupil Size',
                'left_nuclear_id' => 'Nuclear',
                'left_cortical_id' => 'Cortical',
                'left_pxe' => 'PXF',
                'left_phako' => 'Phacodonesis',
                'left_description' => 'Comments',
                'right_eyedraw' => 'Eyedraw',
                'right_ed_report' => 'Report',
                'right_pupil_id' => 'Pupil Size',
                'right_nuclear_id' => 'Nuclear',
                'right_cortical_id' => 'Cortical',
                'right_pxe' => 'PXF',
                'right_phako' => 'Phacodonesis',
                'right_description' => 'Comments',
        );
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
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('left_eyedraw', $this->left_eyedraw);
        $criteria->compare('left_pupil_id', $this->left_pupil_id);
        $criteria->compare('left_nuclear_id', $this->left_nuclear_id);
        $criteria->compare('left_cortical_id', $this->left_cortical_id);
        $criteria->compare('left_pxe', $this->left_pxe);
        $criteria->compare('left_phako', $this->left_phako);
        $criteria->compare('left_description', $this->left_description);
        $criteria->compare('right_eyedraw', $this->right_eyedraw);
        $criteria->compare('right_pupil_id', $this->right_pupil_id);
        $criteria->compare('right_nuclear_id', $this->right_nuclear_id);
        $criteria->compare('right_cortical_id', $this->right_cortical_id);
        $criteria->compare('right_pxe', $this->right_pxe);
        $criteria->compare('right_phako', $this->right_phako);
        $criteria->compare('right_description', $this->right_description);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * Load in the correction values for the eyedraw fields
     *
     * @param Patient|null $patient
     * @throws \CException
     */
    public function setDefaultOptions(Patient $patient = null)
    {
        parent::setDefaultOptions($patient);
        if ($patient === null) {
            throw new \CException('patient object required for setting ' . get_class($this) . ' default options');
        }
        $processor = new \EDProcessor();
        $processor->loadElementEyedrawDoodles($patient, $this, \Eye::LEFT, 'left_eyedraw');
        $processor->loadElementEyedrawDoodles($patient, $this, \Eye::RIGHT, 'right_eyedraw');
    }

    /**
     * Ensure we remove any doodles shredded out of this element for object persistence
     *
     * @return bool
     * @inheritdoc
     */
    public function beforeDelete()
    {
        $processor = new \EDProcessor();
        $processor->removeElementEyedraws($this);
        return parent::beforeDelete();
    }

    /**
     * Performs the shredding of Eyedraw data for the patient record
     *
     * @inheritdoc
     */
    public function afterSave()
    {
        $processor = new \EDProcessor();
        $processor->shredElementEyedraws($this, static::$ed_persistence_attributes);
        parent::afterSave();
    }

    public function afterFind()
    {
        if (!$this->isNewRecord && $this->eye_id != \Eye::BOTH) {
            $eye_id = $this->eye_id == \Eye::LEFT ? \Eye::RIGHT : \Eye::LEFT;
            $patient = $this->event->episode->patient;
            $processor = new \EDProcessor();
            $processor->loadElementEyedrawDoodles($patient, $this, $eye_id, strtolower(\Eye::methodPostFix($eye_id)) . '_eyedraw');
        }

        if ($this->right_eyedraw2 || $this->left_eyedraw2) {
            $this->has_side_view = true;
        }
        parent::afterFind();
    }

    public function getPrint_view()
    {
        return 'print_'.$this->getDefaultView();
    }
}
