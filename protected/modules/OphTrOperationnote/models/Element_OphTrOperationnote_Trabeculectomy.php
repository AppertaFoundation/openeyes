<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class Element_OphTrOperationnote_Trabeculectomy extends Element_OnDemand
{
    public function tableName()
    {
        return 'et_ophtroperationnote_trabeculectomy';
    }

    public function rules()
    {
        return array(
            array('eyedraw, conjunctival_flap_type_id, stay_suture, site_id, size_id, sclerostomy_type_id, viscoelastic_type_id, viscoelastic_removed, viscoelastic_flow_id, report, difficulty_other, complication_other, comments', 'safe'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'conjunctival_flap_type_id' => 'Conjunctival flap',
            'stay_suture' => 'Stay suture',
            'site_id' => 'Site',
            'size_id' => 'Size',
            'sclerostomy_type_id' => 'Sclerostomy',
            'viscoelastic_type_id' => 'Viscoelastic',
            'viscoelastic_removed' => 'Removed',
            'viscoelastic_flow_id' => 'Flow',
            'complication_other' => 'Other complication',
            'difficulty_other' => 'Other difficulty',
            'report' => 'Description',
            'MultiSelect_Complications' => 'Complications',
            'MultiSelect_Difficulties' => 'Operative Difficulties',
            'comments' => 'Comments'
        );
    }

    public function relations()
    {
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'conjunctival_flap_type' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Conjunctival_Flap_Type', 'conjunctival_flap_type_id'),
            'site' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Site', 'site_id'),
            'size' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Size', 'size_id'),
            'sclerostomy_type' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Sclerostomy_Type', 'sclerostomy_type_id'),
            'viscoelastic_type' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Viscoelastic_Type', 'viscoelastic_type_id'),
            'viscoelastic_flow' => array(self::BELONGS_TO, 'OphTrOperationnote_Trabeculectomy_Viscoelastic_Flow', 'viscoelastic_flow_id'),
            'difficulties' => array(self::MANY_MANY, 'OphTrOperationnote_Trabeculectomy_Difficulty', 'ophtroperationnote_trabeculectomy_difficulties(element_id, difficulty_id)'),
            'difficulty_assignments' => array(self::HAS_MANY, 'OphTrOperationnote_Trabeculectomy_Difficulties', 'element_id'),
            'complications' => array(self::MANY_MANY, 'OphTrOperationnote_Trabeculectomy_Complication', 'ophtroperationnote_trabeculectomy_complications(element_id, complication_id)'),
            'complication_assignments' => array(self::HAS_MANY, 'OphTrOperationnote_Trabeculectomy_Complications', 'element_id'),
        );
    }

    public function getEye()
    {
        return Element_OphTrOperationnote_ProcedureList::model()->find('event_id=?', array($this->event_id))->eye;
    }

    public function afterValidate()
    {
        if ($this->hasMultiSelectValue('difficulties', 'Other')) {
            if (!$this->difficulty_other) {
                $this->addError('difficulty_other', $this->getAttributeLabel('difficulty_other').' cannot be blank.');
            }
        }

        if ($this->hasMultiSelectValue('complications', 'Other')) {
            if (!$this->complication_other) {
                $this->addError('complication_other', $this->getAttributeLabel('complication_other').' cannot be blank.');
            }
        }

        return parent::afterValidate();
    }

    public function getPrefillableAttributeSet()
    {
        $attributes = [
            'eyedraw',
            'conjunctival_flap_type_id',
            'stay_suture',
            'site_id',
            'size_id',
            'sclerostomy_type_id',
            'viscoelastic_type_id',
            'viscoelastic_removed',
            'viscoelastic_flow_id',
            'report',
            'difficulties' => 'id',
            'comments'
        ];

        if (SettingMetadata::model()->checkSetting('allow_complications_in_pre_fill_templates', 'on')) {
            $attributes['complications'] = 'id';
        }

        return $attributes;
    }


    /**
     * Returns comma separated list of complications on this procedure note.
     *
     * @param $default
     *
     * @return string
     */
    public function getComplicationsString($default = 'None')
    {
        $res = array();
        foreach ($this->complications as $comp) {
                $res[] = $comp->name;
        }
        if ($res) {
            return implode(', ', $res);
        } else {
            return $default;
        }
    }

    protected function applyComplexData($data, $index): void
    {
        $difficulties = array();

        if (!empty($data['MultiSelect_Difficulties'])) {
            foreach ($data['MultiSelect_Difficulties'] as $difficulty_id) {
                $assignment = new OphTrOperationnote_Trabeculectomy_Difficulties();
                $assignment->difficulty_id = $difficulty_id;

                $difficulties[] = $assignment;
            }
        } elseif (!empty($data[$this->elementType->class_name]['difficulties'])) {
            foreach ($data[$this->elementType->class_name]['difficulties'] as $difficulty_id) {
                $assignment = new OphTrOperationnote_Trabeculectomy_Difficulties();
                $assignment->difficulty_id = $difficulty_id;

                $difficulties[] = $assignment;
            }
        }

        $this->difficulties = $difficulties;

        $complications = array();

        if (!empty($data['MultiSelect_Complications'])) {
            foreach ($data['MultiSelect_Complications'] as $complication_id) {
                $assignment = new OphTrOperationnote_Trabeculectomy_Complications();
                $assignment->complication_id = $complication_id;

                $complications[] = $assignment;
            }
        } elseif (!empty($data[$this->elementType->class_name]['complications'])) {
            foreach ($data[$this->elementType->class_name]['complications'] as $complication_id) {
                $assignment = new OphTrOperationnote_Trabeculectomy_Complications();
                $assignment->complication_id = $complication_id;

                $complications[] = $assignment;
            }
        }

        $this->complications = $complications;
    }
}
