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

/**
 * This is the model class for table "et_ophciexamination_oct".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $event_id
 * @property int $eye_id
 * @property string $left_crt
 * @property string $right_crt
 * @property string $left_sft
 * @property string $right_sft
 * @property bool $left_thickness_increase
 * @property bool $right_thickness_increase
 * @property bool $left_dry
 * @property bool $right_dry
 * @property int $left_fluidstatus_id
 * @property int $right_fluidstatus_id
 * @property string $left_comments
 * @property string $right_comments
 * @property OphCiExamination_OCT_Method $left_method
 * @property OphCiExamination_OCT_Method $right_method
 * @property OphCiExamination_OCT_FluidType[] $left_fluidtypes
 * @property OphCiExamination_OCT_FluidType[] $right_fluidtypes
 * @property OphCiExamination_OCT_FluidStatus $left_fluidstatus
 * @property OphCiExamination_OCT_FluidStatus $right_fluidstatus
 */
class Element_OphCiExamination_OCT extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    protected $auto_update_relations = true;
    protected $relation_defaults = array(
            'left_fluidtypes' => array(
                'eye_id' => \Eye::LEFT,
            ),
            'right_fluidtypes' => array(
                    'eye_id' => \Eye::RIGHT,
            ),
    );

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_AnteriorSegment_CCT
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
        return 'et_ophciexamination_oct';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('eye_id, left_method_id, left_crt, left_sft, left_thickness_increase, left_dry,
					left_fluidstatus_id, left_comments, right_method_id, right_crt, right_sft, right_thickness_increase,
					right_dry, right_fluidstatus_id, right_comments', 'safe'),
                array('left_method_id, left_sft, left_dry', 'requiredIfSide', 'side' => 'left'),
                array('right_method_id, right_sft, right_dry', 'requiredIfSide', 'side' => 'right'),
                array('left_crt', 'numerical', 'allowEmpty' => true, 'integerOnly' => true, 'max' => 850, 'min' => 250,
                        'tooBig' => 'Left {attribute} must be between 250 and 850',
                        'tooSmall' => 'Left {attribute} must be between 250 and 850', ),
                array('right_crt', 'numerical', 'allowEmpty' => true, 'integerOnly' => true, 'max' => 850, 'min' => 250,
                        'tooBig' => 'Right {attribute} must be between 250 and 850',
                        'tooSmall' => 'Right {attribute} must be between 250 and 850', ),
                array('left_crt, left_thickness_increase, left_comments, right_crt, right_thickness_increase,
					right_comments', 'default', 'setOnEmpty' => true, 'value' => null),
                array('left_sft', 'numerical', 'integerOnly' => true, 'max' => 1500, 'min' => 50,
                        'tooBig' => 'Left {attribute} must be between 50 and 1500',
                        'tooSmall' => 'Left {attribute} must be between 50 and 1500', ),
                array('right_sft', 'numerical', 'integerOnly' => true, 'max' => 1500, 'min' => 50,
                        'tooBig' => 'Right {attribute} must be between 50 and 1500',
                        'tooSmall' => 'Right {attribute} must be between 50 and 1500', ),
                array('left_fluidstatus_id',
                    'notAllowedIfTrue', 'side' => 'left', 'dependency' => 'left_dry', ),
                array('right_fluidstatus_id',
                    'notAllowedIfTrue', 'side' => 'right', 'dependency' => 'right_dry', ),
                array('left_fluidstatus_id, left_fluidtypes', 'requiredIfFalse', 'side' => 'left', 'dependency' => 'left_dry'),
                array('right_fluidstatus_id, right_fluidtypes', 'requiredIfFalse', 'side' => 'right', 'dependency' => 'right_dry'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id, left_method_id, left_crt, left_sft, left_thickness_increase, left_dry,
					left_fluidstatus_id, left_comments, right_method_id, right_crt, right_sft, right_thickness_increase,
					right_dry, right_fluidstatus_id, right_comments', 'safe', 'on' => 'search'),
        );
    }

    public function sidedFields()
    {
        return array('method_id', 'crt', 'sft', 'dry', 'fluidstatus_id', 'comments', 'fluidtypes');
    }

    public function sidedDefaults()
    {
        return array();
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'left_method' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OCT_Method', 'left_method_id'),
                'right_method' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OCT_Method', 'right_method_id'),
                'fluidtype_assignments' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_OCT_FluidTypeAssignment', 'element_id'),
                'left_fluidtypes' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_OCT_FluidType', 'fluidtype_id', 'through' => 'fluidtype_assignments', 'on' => 'fluidtype_assignments.eye_id = '.\Eye::LEFT),
                'right_fluidtypes' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_OCT_FluidType', 'fluidtype_id', 'through' => 'fluidtype_assignments', 'on' => 'fluidtype_assignments.eye_id = '.\Eye::RIGHT),
                'left_fluidstatus' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OCT_FluidStatus', 'left_fluidstatus_id'),
                'right_fluidstatus' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_OCT_FluidStatus', 'right_fluidstatus_id'),
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
            'left_method_id' => 'Image Type',
            'right_method_id' => 'Image Type',
            'left_crt' => 'Maximum CRT',
            'right_crt' => 'Maximum CRT',
            'left_sft' => 'Central Macular Thickness (CMT)',
            'right_sft' => 'Central Macular Thickness (CMT)',
            'left_thickness_increase' => 'Thickness increase of 100µm',
            'right_thickness_increase' => 'Thickness increase of 100µm',
            'left_dry' => 'Dry',
            'right_dry' => 'Dry',
            'left_fluidtypes' => 'Findings',
            'right_fluidtypes' => 'Findings',
            'left_fluidstatus_id' => 'Findings Type',
            'right_fluidstatus_id' => 'Findings Type',
            'left_comments' => 'Comments',
            'right_comments' => 'Comments',
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

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('left_method_id', $this->left_method_id);
        $criteria->compare('right_method_id', $this->right_method_id);
        $criteria->compare('left_crt', $this->left_crt);
        $criteria->compare('right_crt', $this->right_crt);
        $criteria->compare('left_sft', $this->left_sft);
        $criteria->compare('right_sft', $this->right_sft);
        $criteria->compare('left_thickness_increase', $this->left_thickness_increase);
        $criteria->compare('right_thickness_increase', $this->right_thickness_increase);
        $criteria->compare('left_dry', $this->left_dry);
        $criteria->compare('right_dry', $this->right_dry);
        $criteria->compare('left_fluidtypes', $this->left_fluidtypes);
        $criteria->compare('right_fluidtypes', $this->right_fluidtypes);
        $criteria->compare('left_fluidstatus_id', $this->left_fluidstatus_id);
        $criteria->compare('right_fluidstatus_id', $this->right_fluidstatus_id);
        $criteria->compare('left_comments', $this->left_comments);
        $criteria->compare('right_comments', $this->right_comments);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * returns the appropriate string for displaying the fluid finding value(s) for the given side.
     *
     * @param string $side        left or right
     * @param bool   $notrecorded - flag to indicate whether we want a string for it not being recorded
     *
     * @return string
     */
    protected function getFluidString($side, $notrecorded = true)
    {
        // we check that dry is not null here, because if it is then it indicates the OCT
        // was recorded prior to the introduction of the fluid fields
        if ($this->{'has'.ucfirst($side)}() && $this->{$side.'_dry'} !== null) {
            if ($this->{$side.'_dry'}) {
                return 'Dry';
            } else {
                $fts = array();
                foreach ($this->{$side.'_fluidtypes'} as $ft) {
                    $fts[] = $ft->name;
                }

                return $this->{$side.'_fluidstatus'}->name.' '.implode(', ', $fts);
            }
        } else {
            return 'Not recorded';
        }
    }

    /**
     * get the fluid findings string for the left.
     *
     * @return string
     */
    public function getLeftFluidString()
    {
        return $this->getFluidString('left');
    }

    /**
     * get the fluid findings string for the right.
     *
     * @return string
     */
    public function getRightFluidString()
    {
        return $this->getFluidString('right');
    }

    /**
     * validate that attribute is set if dependency is false
     * requires side param.
     *
     * @param $attribute
     * @param $params
     */
    public function requiredIfFalse($attribute, $params)
    {
        $dependency = $params['dependency'];
        $side = $params['side'];
        $checker = 'has'.ucfirst($side);
        if ($this->$checker() && $this->$dependency !== null && $this->$dependency != '' && !$this->$dependency &&
            !$this->$attribute) {
            $this->addError($attribute, ucfirst($side).' '.$this->getAttributeLabel($attribute).' is required when '.
                ucfirst($side).' '.$this->getAttributeLabel($dependency).' is no');
        }
    }

    /**
     * validate that attribute is not set if dependency is true - should never arise through the forms
     * requires side param.
     *
     * @param $attribute
     * @param $params
     */
    public function notAllowedIfTrue($attribute, $params)
    {
        $dependency = $params['dependency'];
        $side = $params['side'];
        $checker = 'has'.ucfirst($side);
        if ($this->$checker() && $this->$dependency && $this->$attribute) {
            $this->addError($attribute, ucfirst($side).' '.$this->getAttributeLabel($attribute).' cannot be set when '.$side.' '.$this->getAttributeLabel($dependency).' is set');
        }
    }

    /**
     * get the letter string for the given side.
     *
     * @param $side
     *
     * @return string
     */
    protected function getLetterStringForSide($side)
    {
        $res = ucfirst($side)." Eye:\n";
        $res .= $this->getAttributeLabel($side.'_method_id').': '.$this->{$side.'_method'}->name."\n";
        if ($this->{$side.'_crt'}) {
            $res .= $this->getAttributeLabel($side.'_crt').': '.$this->{$side.'_crt'}." microns\n";
        }
        $res .= $this->getAttributeLabel($side.'_sft').': '.$this->{$side.'_sft'}." microns\n";
        if ($this->{$side.'_thickness_increase'} !== null) {
            $res .= 'Thickness increase over 100 microns: '.($this->{$side.'_thickness_increase'} ? 'Yes' : 'No');
            $res .= "\n";
        }

        if ($fluid = $this->getFluidString($side, false)) {
            $res .= 'Finding: '.$fluid."\n";
        }

        if ($this->{$side.'_comments'}) {
            $res .= $this->{$side.'_comments'}."\n";
        }

        return $res;
    }

    /**
     * get the letter string for the element
     * used by correspondence if installed.
     *
     * @return string
     */
    public function getLetter_string()
    {
        $res = "OCT:\n";
        if ($this->hasRight()) {
            $res .= $this->getLetterStringForSide('right');
        }
        if ($this->hasLeft()) {
            $res .= $this->getLetterStringForSide('left');
        }

        return $res;
    }

    public function getFluidTypeValues()
    {
        $fluidtype_values = array();

        foreach ($this->fluidtype_assignments as $fluidtype_assignment) {
            $fluidtype_values[] = $fluidtype_assignment->fluidtype_id;
        }

        return $fluidtype_values;
    }

    /**
     * Set FindingsType to empty string if Dry is set
     *
     * @return bool
     */
    protected function beforeValidate()
    {
        foreach (['left', 'right'] as $eye_side) {
            if ($this->{$eye_side.'_dry'}) {
                $this->{$eye_side.'_fluidstatus_id'} = null;
            }
        }

        return parent::beforeValidate();
    }

    /**
     * Remove the Findings if Dry is set
     *
     * @throws \Exception
     */
    public function afterSave()
    {
        foreach (['left', 'right'] as $eye_side) {
            if ($this->{$eye_side.'_dry'}) {
                foreach ($this->fluidtype_assignments as $fluidtype_assignment) {
                    if (strtolower(\Eye::methodPostFix($fluidtype_assignment->eye_id)) == $eye_side) {
                        $fluidtype_assignment->delete();
                    }
                }
            }
        }

        parent::afterSave();
    }

    public function getViewTitle()
    {
        return 'OCT (manual)';
    }

    public function getFormTitle()
    {
        return 'OCT (manual)';
    }
}
