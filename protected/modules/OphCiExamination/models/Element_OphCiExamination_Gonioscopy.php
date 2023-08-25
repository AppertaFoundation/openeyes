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
 * This is the model class for table "et_ophciexamination_gonioscopy".
 *
 * The followings are the available columns in table 'et_ophciexamination_gonioscopy':
 *
 * @property int $id
 * @property int $eye_id
 * @property int $event_id
 * @property string $left_description
 * @property string $right_description
 * @property OphCiExamination_Gonioscopy_Description $left_gonio_sup
 * @property OphCiExamination_Gonioscopy_Description $left_gonio_tem
 * @property OphCiExamination_Gonioscopy_Description $left_gonio_nas
 * @property OphCiExamination_Gonioscopy_Description $left_gonio_inf
 * @property OphCiExamination_Gonioscopy_Description $right_gonio_sup
 * @property OphCiExamination_Gonioscopy_Description $right_gonio_tem
 * @property OphCiExamination_Gonioscopy_Description $right_gonio_nas
 * @property OphCiExamination_Gonioscopy_Description $right_gonio_inf
 * @property string $left_eyedraw
 * @property string $right_eyedraw
 * @property string $left_ed_report
 * @property string $right_ed_report
 * @property int $right_iris_id
 * @property int $left_iris_id
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property OphCiExamination_Gonioscopy_Iris $right_iris
 * @property OphCiExamination_Gonioscopy_Iris $left_iris
 */
class Element_OphCiExamination_Gonioscopy extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
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
    public $letter_string_prefix = "Gonioscopy:\n";

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
        return 'et_ophciexamination_gonioscopy';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('eye_id, left_gonio_sup_id, left_gonio_tem_id, left_gonio_nas_id, left_gonio_inf_id,
						right_gonio_sup_id, right_gonio_tem_id, right_gonio_nas_id, right_gonio_inf_id,
						left_description, right_description, left_eyedraw, right_eyedraw,
						left_ed_report, right_ed_report, right_iris_id, left_iris_id', 'safe'),
                array('left_eyedraw, left_ed_report', 'requiredIfSide', 'side' => 'left'),
                array('right_eyedraw, right_ed_report', 'requiredIfSide', 'side' => 'right'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('eye_id, event_id, left_description, right_description, left_eyedraw, right_eyedraw',
                        'safe', 'on' => 'search', ),
        );
    }

    public function sidedFields()
    {
        return array('gonio_sup_id', 'gonio_tem_id', 'gonio_nas_id', 'gonio_inf_id', 'description', 'eyedraw');
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
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'left_gonio_sup' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Gonioscopy_Description', 'left_gonio_sup_id'),
                'left_gonio_tem' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Gonioscopy_Description', 'left_gonio_tem_id'),
                'left_gonio_nas' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Gonioscopy_Description', 'left_gonio_nas_id'),
                'left_gonio_inf' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Gonioscopy_Description', 'left_gonio_inf_id'),
                'right_gonio_sup' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Gonioscopy_Description', 'right_gonio_sup_id'),
                'right_gonio_tem' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Gonioscopy_Description', 'right_gonio_tem_id'),
                'right_gonio_nas' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Gonioscopy_Description', 'right_gonio_nas_id'),
                'right_gonio_inf' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Gonioscopy_Description', 'right_gonio_inf_id'),
                'left_iris' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Gonioscopy_Iris', 'left_iris_id'),
                'right_iris' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Gonioscopy_Iris', 'right_iris_id'),
                'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
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
            'left_gonio_sup_id' => 'Gonioscopy',
            'left_gonio_tem_id' => 'Gonioscopy',
            'left_gonio_nas_id' => 'Gonioscopy',
            'left_gonio_inf_id' => 'Gonioscopy',
            'right_gonio_sup_id' => 'Gonioscopy',
            'right_gonio_tem_id' => 'Gonioscopy',
            'right_gonio_nas_id' => 'Gonioscopy',
            'right_gonio_inf_id' => 'Gonioscopy',
            'left_description' => 'Comments',
            'right_description' => 'Comments',
            'left_eyedraw' => 'EyeDraw',
            'right_eyedraw' => 'EyeDraw',
            'left_ed_report' => 'Report',
            'right_ed_report' => 'Report',
            'left_iris_id' => 'Iris',
            'right_iris_id' => 'Iris',
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
        $criteria->compare('left_gonio_sup_id', $this->left_gonio_sup_id, true);
        $criteria->compare('left_gonio_tem_id', $this->left_gonio_tem_id, true);
        $criteria->compare('left_gonio_nas_id', $this->left_gonio_nas_id, true);
        $criteria->compare('left_gonio_inf_id', $this->left_gonio_inf_id, true);
        $criteria->compare('right_gonio_sup_id', $this->right_gonio_sup_id, true);
        $criteria->compare('right_gonio_tem_id', $this->right_gonio_tem_id, true);
        $criteria->compare('right_gonio_nas_id', $this->right_gonio_nas_id, true);
        $criteria->compare('right_gonio_inf_id', $this->right_gonio_inf_id, true);
        $criteria->compare('left_description', $this->left_description, true);
        $criteria->compare('right_description', $this->right_description, true);
        $criteria->compare('left_eyedraw', $this->left_eyedraw, true);
        $criteria->compare('right_eyedraw', $this->right_eyedraw, true);
        $criteria->compare('left_ed_report', $this->left_ed_report, true);
        $criteria->compare('right_ed_report', $this->right_ed_report, true);
        $criteria->compare('left_iris', $this->left_iris, true);
        $criteria->compare('right_iris', $this->right_iris, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * @return array
     */
    public function getGonioscopyOptions()
    {
        return \CHtml::listData(OphCiExamination_Gonioscopy_Description::model()
                ->findAll(array('order' => 'display_order')), 'id', 'name');
    }

    /**
     * Load in the correction values for the eyedraw fields
     *
     * @param Patient|null $patient
     * @throws \CException
     */
    public function setDefaultOptions(\Patient $patient = null)
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
     */
    public function afterSave()
    {
        $processor = new \EDProcessor();
        $processor->shredElementEyedraws($this, [
        'left_eyedraw' => \Eye::LEFT,
        'right_eyedraw' => \Eye::RIGHT,
        ]);
        parent::afterSave();
    }

    /**
     * @return array
     */
    public function getIrisOptions()
    {
        return OphCiExamination_Gonioscopy_Iris::model()->findAll(['order' => 'display_order']);
    }

    /**
     * @return array
     */
    public function sidedDefaults()
    {
        $defaults = array();
        foreach (array('sup', 'tem', 'nas', 'inf') as $position) {
            $defaults['gonio_'.$position.'_id'] = 2;
        }

        return $defaults;
    }
}
