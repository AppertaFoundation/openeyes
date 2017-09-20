<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class Element_OphInVisualfields_Result extends BaseEventTypeElement
{
    public function tableName()
    {
        return 'et_ophinvisualfields_result';
    }

    public function rules()
    {
        return array(
            array(' other', 'safe'),
        );
    }

    public function getophinvisualfields_result_assessment_defaults()
    {
        $ids = array();
        foreach (OphInVisualfields_Result_Assessment::model()->findAll('`default` = ?', array(1)) as $item) {
            $ids[] = $item->id;
        }

        return $ids;
    }

    public function relations()
    {
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'assessment' => array(self::HAS_MANY, 'Element_OphInVisualfields_Result_Assessment_Assignment', 'element_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'assessment_id' => 'Assessment Result',
        );
    }

    public function afterValidate()
    {
        if ($this->hasMultiSelectValue('assessment', 'Other')) {
            if (empty($this->other)) {
                $this->addError('other', 'Please enter details');
            }
        }

        parent::afterValidate();
    }

    public function beforeSave()
    {
        if (!$this->hasMultiSelectValue('assessment', 'Other')) {
            $this->other = null;
        }

        return parent::beforeSave();
    }
}
